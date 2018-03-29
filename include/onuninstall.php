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

use XoopsModules\Xforms;

/**
 * Prepare to uninstall module
 *
 * @param XoopsModule $xoopsModule
 *
 * @return bool success
 */
function xoops_module_pre_uninstall_xforms(\XoopsModule $xoopsModule)
{
    /*********************************
     * Remove uploads directory
     * (and all files in the directory)
     *********************************/
    $moduleDirName = basename(dirname(__DIR__));
    $helper  = Xforms\Helper::getHelper($moduleDirName);
    $helper->loadLanguage('modinfo');
    //    $modulePrefix = $helper->getModule()->getVar('dirname');

    if (!$helper->isUserAdmin()) {
        $xoopsModule->setErrors(_NOPERM);

        return false;
    }
    // get uploads directory name from Preferences setting
    $uploadDir = $helper->getConfig('uploaddir');
    $uploadDir = ('/' === substr($uploadDir, -1, 1)) ? substr($uploadDir, 0, -1) : $uploadDir;
    $dirInfo   = new \SplFileInfo($uploadDir);
    $success   = true;
    require_once __DIR__ . '/functions.php';
    if ($dirInfo->isDir()) {
        // directory exists so try and delete it
        $success = xformsDeleteDirectory($uploadDir);
    }
    if (false === $success) {
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
function xoops_module_uninstall_xforms(\XoopsModule $xoopsModule)
{
    // NOP
    return true;
}
