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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */

use Xmf\Module\Helper;

/**
 * Prepare to uninstall module
 *
 * @param XoopsModule $xoopsModule
 *
 * @return bool success
 */
function xoops_module_pre_uninstall_xforms(XoopsModule $xoopsModule)
{
    /*********************************
     * Remove uploads directory
     * (and all files in the directory)
     *********************************/
    $moduleDirName = basename(dirname(__DIR__));
    $xformsHelper  = Helper::getHelper($moduleDirName);
    $xformsHelper->loadLanguage('modinfo');
    //    $modulePrefix = $xformsHelper->getModule()->getVar('dirname');
    if (!class_exists('XformsUtilities')) {
        xoops_load('utilities', $moduleDirName);
    }

    if (!$xformsHelper->isUserAdmin()) {
        $xoopsModule->setErrors(_NOPERM);

        return false;
    }
    // get uploads directory name from Preferences setting
    $uploadDir = $xformsHelper->getConfig('uploaddir');
    $uploadDir = ('/' === substr($uploadDir, -1, 1)) ? substr($uploadDir, 0, -1) : $uploadDir;
    $dirInfo   = new SplFileInfo($uploadDir);
    $success   = true;
    if ($dirInfo->isDir()) {
        // directory exists so try and delete it
        $success = XformsUtilities::deleteDirectory($uploadDir);
    }
    if (!$success) {
        $xoopsModule->setErrors(sprintf(_MI_XFORMS_INST_NO_DEL_UPLOAD, $uploadDir));
    }

    return $success;
}

/**
 * Uninstall module
 *
 * @param XoopsModule $xoopsModule
 *
 * @return bool success
 */
function xoops_module_uninstall_xforms(XoopsModule $xoopsModule)
{
    // NOP
    return true;
}
