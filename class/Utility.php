<?php

namespace XoopsModules\Xforms;

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
 * Module: Xforms
 *
 * @package   \XoopsModules\Xforms\class
 * @author    XOOPS Module Development Team
 * @author    Mamba <mambax7@gmail.com>
 * @author    ZySpec <zyspec@yahoo.com>
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */
use XoopsModules\Xforms;

 /**
  * \XoopsModules\Xforms\Utility
  *
  * Static utility class to provide common functionality
  *
  */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    /** @var array errs list of errors */
    public static $errs = [];

    /**
     * Copies files from one directory to another, does not alter source directory files
     *
     * @deprecated
     * @param string $fromDir copy from directory
     * @param string $toDir copy to directory
     * @param array $exceptions don't copy these files
     * @param bool $okNotExist true if source (from) directory doesn't exist | false if source must exist
     *
     * @return bool
     */
/**
    function copyFiles($fromDir, $toDir, $exceptions = array(), $okNotExist = false) {
        $xformsHelper = \Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));

        $toUploadDir = ('/' === substr($toDir, -1, 1)) ? substr($toDir, 0, -1) : $toDir;
        $toDirInfo = new \SplFileInfo($toDir);
        $fromUploadDir = ('/' === substr($fromDir, -1, 1)) ? substr($fromDir, 0, -1) : $fromDir;
        $fromDirInfo = new \SplFileInfo($fromUploadDir);

        $success = true;
        // validate they are valid directories
        if ($toDirInfo->isDir() && $fromDirInfo->isDir()) {
            $exceptions = (array) $exceptions;
            $exceptArray = array_merge(array('..', '.'), $exceptions);
            $fileList = array_diff(scandir($fromUploadDir), $exceptArray);

            //now copy the file(s) to the (to) directory
            foreach ($fileList as $fileName) {
                if (($fileInfo = new \SplFileInfo($eformsUploadDir . $fileName))
                    && ($currFileInfo = new \SplFileinfo($eformsUploadDir . $fileName)))
                {
                    $fileSuccess = copy($eformsUploadDir . $fileName, $eformsUploadDir . $fileName);
                    $success &= $fileSuccess;
                }
            }
        } else {
            // Input directory(ies) not valid
            $success = $okNotExist ? true : false;
        }
        return $success;
    }
*/
    /**
     * Check Other element setting
     *
     * Checks to see if there's anything in the 'Other' setting
     *
     * @param string $key
     * @param int $id
     * @param string|bool returns 'Other' string or false if nothing set or on error
     *
     * @return bool|string false on error | string for 'other' element
     * @global array $_POST
     *
     */
    public static function checkOther($key, $id, $caption)
    {
        $id = (int)$id;
        if (!preg_match('/\{OTHER\|+[0-9]+\}/', $key)) {
            return false;
        }
            /* @var \MyTextSanitizer $myts */
            $myts = \MyTextSanitizer::getInstance();
            if (!empty($_POST['other']['ele_' . $id])) {
                return _MD_XFORMS_OPT_OTHER . $myts->htmlSpecialChars($_POST['other']['ele_' . $id]);
            } else {
                static::setErrors(sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption)), true);
                //global $err;
                //$err[] = sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption));
            }
        return false;
    }

    /**
     * Decode HTML entities
     *
     * function used for smarty output filter of csv files
     *
     * @param string $tpl_output
     *
     * @return string filtered to decode HTML entities
     */
    public static function undoHtmlEntities($tpl_output) {
        return html_entity_decode($tpl_output);
    }

        /**
         * Callback function to convert item to integer
         *
         * Allows use of PHP array_walk to also preserve keys
         *
         * @param string|int $item
         *
         * @return void
         */
    public static function intArray(&$item)
    {
        $item = (int)$item;
    }

    /**
     * Set errors for the Utility class
     *
     * @param string|array item
     * @param bool replace true to replace errors, false to add item to list of errors
     *
     * @return int
     */
    public static function setErrors($item, $replace = true) {
        if (!empty($item)) {
            $item = (array)$item;
            if ($replace) {
                static::$errs = $item;
            } else {
                static::$errs = array_merge(static::$errs, $item);
                static::$errs = array_unique(static::$errs);
            }
        } else {
            static::$errs = []; // clears the array if $item is empty
        }
        return static::errs;
    }

    /**
     * Get Utility class errors
     *
     * @return array
     */
    public static function getErrors() {

        return static::$errs;
    }
}
