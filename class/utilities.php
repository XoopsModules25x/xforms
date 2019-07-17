<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module: xForms
 *
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @author          Mamba, ZySpec
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 */

require_once __DIR__ . '/../include/common.php';

/**
 * XformsUtilities
 *
 * Static utilities class to provide common functionality
 *
 */
class XformsUtilities
{
    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkXoopsVer(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        //check for minimum XOOPS version
        $currentVer  = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray   = explode('.', $currentVer);
        $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        $reqArray    = explode('.', $requiredVer);
        $success     = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int)$v > 0) { // handles versions like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }
    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule
     *
     * @return bool true if meets requirements, false if not
     */
    /*
        public static function checkPHPVer(&$module)
        {
            xoops_loadLanguage('admin', $module->dirname());
            // check for minimum PHP version
            $phpLen   = strlen(PHP_VERSION);
            $extraLen = strlen(PHP_EXTRA_VERSION);
            $verNum   = trim(substr(PHP_VERSION, 0, ($phpLen-$extraLen)));
            $reqVer   = trim($module->getInfo('min_php') . ""); //make sure it's a string and then trim it

            $success  = true;
            if ($verNum >= $reqVer) {
                $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }

            return $success;
        }
    */
    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkPHPVer(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum  = phpversion();
        $reqVer  =& $module->getInfo('min_php');
        if ((false !== $reqVer) && ('' !== $reqVer)) {
            if (version_compare($verNum, (string)$reqVer, '<')) {
                $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Callback function to convert item to integer
     *
     * @param mixed $item
     *
     * @return int
     */
    public static function intArray(&$item)
    {
        $item = (int)$item;
    }

    /**
     * @param $var
     * @return mixed
     */
    public static function quoteThisString($var)
    {
        return $GLOBALS['xoopsDB']->escape($var);
    }

    /**
     * @param        $key
     * @param int    $id
     * @param string $caption
     *
     * @todo refactor code to eliminate use of 'global $err' to track errors
     *
     * @global       array err - used to keep error messages
     * @global array $_POST
     *
     * @return bool|string false on error | string for 'other' element
     */
    public static function checkOther($key, $id, $caption)
    {
        $myts = MyTextSanitizer::getInstance();
        $id   = (int)$id;
        global $err;
        if (!preg_match('/\{OTHER\|+\d+\}/', $key)) {
            return false;
        } else {
            if (!empty($_POST['other']["ele_{$id}"])) {
                return _MD_XFORMS_OPT_OTHER . $_POST['other']["ele_{$id}"];
            } else {
                $err[] = sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption));
            }
        }

        return false;
    }

    /**
     *
     * Remove old files and (sub)directories
     *
     * @param string $directory
     *
     * @return bool $success
     */
    public static function deleteDirectory($directory)
    {
        $success      = true;
        $xformsHelper = Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));

        if (!$xformsIsAdmin = $xformsHelper->isUserAdmin()) {
            $success = false;
        } else {
            // remove old files
            $dirInfo = new SplFileInfo($directory);
            // validate is a directory
            if ($dirInfo->isDir()) {
                $fileList = array_diff(scandir($directory), array('..', '.'));
                foreach ($fileList as $k => $v) {
                    $fileInfo = new SplFileInfo("{$directory}/{$v}");
                    if ($fileInfo->isDir()) {
                        // recursively handle subdirectories
                        if (!$success = self::deleteDirectory($fileInfo->getRealPath())) {
                            break;
                        }
                    } else {
                        // delete the file
                        if (!($success = unlink($fileInfo->getRealPath()))) {
                            break;
                        }
                    }
                }
                // now delete this (sub)directory if all the files are gone
                if ($success) {
                    $success = rmdir($dirInfo->getRealPath());
                }
            } else {
                // input is not a valid directory
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param       $fromDir
     * @param       $toDir
     * @param array $exceptions
     * @param bool  $okNotExist
     * @return bool
     */

    public static function copyFiles($fromDir, $toDir, array $exceptions = array(), $okNotExist = false)
    {
        $xformsHelper = Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));

        $toUploadDir   = ('/' === substr($toDir, -1, 1)) ? substr($toDir, 0, -1) : $toDir;
        $toDirInfo     = new SplFileInfo($toDir);
        $fromUploadDir = ('/' === substr($fromDir, -1, 1)) ? substr($fromDir, 0, -1) : $fromDir;
        $fromDirInfo   = new SplFileInfo($fromUploadDir);

        $success = true;
        // validate they are valid directories
        if ($toDirInfo->isDir() && $fromDirInfo->isDir()) {
            $exceptions  = (array)$exceptions;
            $exceptArray = array_merge(array('..', '.'), $exceptions);
            $fileList    = array_diff(scandir($fromUploadDir), $exceptArray);

            //now copy the file(s) to the (to) directory
            foreach ($fileList as $fileName) {
                if (($fileInfo = new SplFileInfo("{$eformsUploadDir}{$fileName}"))
                    && ($currFileInfo = new SplFileinf("{$eformsUploadDir}{$fileName}"))
                ) {
                    $fileSuccess = copy("{$eformsUploadDir}{$fileName}", "{$eformsUploadDir}{$fileName}");
                    $success     = $success && $fileSuccess;
                }
            }
        } else {
            // input directory(ies) are not valid
            $success = $okNotExist ? true : false;
        }

        return $success;
    }

    /**
     * function used for smarty output of csv files
     * @param unknown $tpl_output
     * @return string
     */
    public function undoHtmlEntities($tpl_output)
    {
        return html_entity_decode($tpl_output);
    }

    /**
     * Function responsible for checking if a directory exists, we can also write in and create an index.html file
     *
     * @param string $folder The full path of the directory to check
     *
     * @throws Exception
     */
    public static function createFolder($folder)
    {
        try {
            if (!@mkdir($folder) && !is_dir($folder)) {
                throw new \Exception(sprintf('Unable to create the %s directory', $folder));
            } else {
                file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
            }
        } catch (RuntimeException $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n", '<br>';
        }
    }
}
