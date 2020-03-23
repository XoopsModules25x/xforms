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
 * @package   \XoopsModules\Xforms\include
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link http://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 * @link      https://github.com/XoopsModules25x/xforms
 */

/**
 * @internal {Make sure you PROTECT THIS FILE}
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\Utility;

if ((!defined('XOOPS_ROOT_PATH'))
    || !($GLOBALS['xoopsUser'] instanceof XoopsUser)
    || !($GLOBALS['xoopsUser']->isAdmin()))
{
    exit('Restricted access' . PHP_EOL);
}

/**
 * Prepare to uninstall module
 *
 * @param XoopsModule $module
 *
 * @return bool success
 */
function xoops_module_pre_uninstall_xforms(\XoopsModule $module)
{
    // NOP
    return true;
}

/**
 * Uninstall module
 *
 * @param XoopsModule $module
 *
 * @return bool success
 */
function xoops_module_uninstall_xforms(\XoopsModule $module)
{
    /* @var \XoopsModules\Xforms\Helper $helper */
    $helper = Helper::getInstance();
    $helper->loadLanguage('modinfo');

    // Remove uploads directory (and all files in the directory)

    /* @var \XoopsModules\Xforms\Utility $utility */
    $utility = new Utility();

    // Get uploads directory name from Preferences setting
    $uploadDir = $helper->getConfig('uploaddir');
    $uploadDir = ('/' === mb_substr($uploadDir, -1, 1)) ? mb_substr($uploadDir, 0, -1) : $uploadDir;
    $dirInfo   = new \SplFileInfo($uploadDir);
    $success   = true;
    if ($dirInfo->isDir()) {
        // Directory exists so try and delete it
        $success = $utility::deleteDirectory($uploadDir);
    } else {
        // Try and delete uploads/xforms directory (default)
        $moduleUploadPath = XOOPS_UPLOAD_PATH . '/' . $module->dirname();
        $uploadPathObj = new \SplFileInfo($moduleUploadPath);
        if (false !== $uploadPathObj->isDir()) {
            // directory exists so try and delete it
            $success = $utility::deleteDirectory($moduleUploadPath);
        }
    }
    if (!$success) {
        $module->setErrors(sprintf(_MI_XFORMS_INST_NO_DEL_UPLOAD, $uploadDir));
    }

    return $success;
}
