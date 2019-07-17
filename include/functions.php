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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License

 * @since           1.30
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

require_once __DIR__ . '/common.php';

/**
 * Callback function to convert item to integer
 *
 * @param mixed $item
 */
function xformsIntArray(&$item)
{
    $item = (int)$item;
}

function quoteThisString($var)
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
function xformsCheckOther($key, $id, $caption)
{
    $myts = \MyTextSanitizer::getInstance();
    $id   = (int)$id;
    global $err;
    if (!preg_match('/\{OTHER\|+[0-9]+\}/', $key)) {
        return false;
    }
    if (!empty($_POST['other']["ele_{$id}"])) {
        return _MD_XFORMS_OPT_OTHER . $_POST['other']["ele_{$id}"];
    }
    $err[] = sprintf(_MD_XFORMS_ERR_REQ, $myts->htmlSpecialChars($caption));

    return false;
}

/**
 * Remove old files and (sub)directories
 *
 * @param string $directory
 *
 * @return bool $success
 */
function xformsDeleteDirectory($directory)
{
    $success = true;
    $helper  = Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));

    if (!$xformsIsAdmin = $helper->isUserAdmin()) {
        $success = false;
    } else {
        // remove old files
        $dirInfo = new \SplFileInfo($directory);
        // validate is a directory
        if ($dirInfo->isDir()) {
            $fileList = array_diff(scandir($directory, SCANDIR_SORT_NONE), ['..', '.']);
            foreach ($fileList as $k => $v) {
                $fileInfo = new \SplFileInfo("{$directory}/{$v}");
                if ($fileInfo->isDir()) {
                    // recursively handle subdirectories
                    if (!$success = xformsDeleteDirectory($fileInfo->getRealPath())) {
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

function xformsCopyFiles($fromDir, $toDir, $exceptions = [], $okNotExist = false)
{
    $helper = Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));

    $toUploadDir   = ('/' === mb_substr($toDir, -1, 1)) ? mb_substr($toDir, 0, -1) : $toDir;
    $toDirInfo     = new \SplFileInfo($toDir);
    $fromUploadDir = ('/' === mb_substr($fromDir, -1, 1)) ? mb_substr($fromDir, 0, -1) : $fromDir;
    $fromDirInfo   = new \SplFileInfo($fromUploadDir);

    $success = true;
    // validate they are valid directories
    if ($toDirInfo->isDir() && $fromDirInfo->isDir()) {
        $exceptions  = (array)$exceptions;
        $exceptArray = array_merge(['..', '.'], $exceptions);
        $fileList    = array_diff(scandir($fromUploadDir, SCANDIR_SORT_NONE), $exceptArray);

        //now copy the file(s) to the (to) directory
        foreach ($fileList as $fileName) {
            if (($fileInfo = new \SplFileInfo("{$eformsUploadDir}{$fileName}"))
                && ($currFileInfo = new SplFileinf("{$eformsUploadDir}{$fileName}"))) {
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
function xformsUndoHtmlEntities($tpl_output)
{
    return html_entity_decode($tpl_output);
}
