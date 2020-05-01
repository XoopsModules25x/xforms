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
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\Utility;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once dirname(__DIR__) . '/preloads/autoloader.php';

/**
 * @internal {Make sure you PROTECT THIS FILE}
 */
if ((!defined('XOOPS_ROOT_PATH'))
   || !($GLOBALS['xoopsUser'] instanceof \XoopsUser)
   || !($GLOBALS['xoopsUser']->isAdmin()))
{
     exit("Restricted access" . PHP_EOL);
}

 /**
 * Prepares system prior to attempting to install module
 * @param \XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if ready to install, false if not
*/
function xoops_module_pre_install_xforms(\XoopsModule $module)
{
    /* @var \XoopsModules\Xforms\Utility $utility */
    $utility      = new Utility();
    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPHP($module);

    return $xoopsSuccess && $phpSuccess;
}

/**
 * Performs tasks required during installation of the module
 * @param \XoopsModule $module {@link XoopsModule}
 *
 * @return bool true if installation successful, false if not
 */
function xoops_module_install_xforms(\XoopsModule $module)
{
    $success = true;

    /* @var \XoopsModules\Xforms\Helper $helper */
    $helper       = Helper::getInstance();
    $utility      = new Utility();
    $configurator = new \XoopsModules\Xforms\Common\Configurator();

    $helper->loadLanguage('admin');

    // Create the xforms upload directory
    // - defaults to XOOPS_UPLOAD_PATH/xforms directory
    ///$moduleUploadPath = XOOPS_UPLOAD_PATH . '/' . $module->dirname();
    //$uploadPathObj = new \SplFileInfo($moduleUploadPath);
    //if ((false === $uploadPathObj->isDir()) && (false === mkdir($moduleUploadPath, 0755, true))) {
    $uploadPathObj = new \SplFileInfo($configurator->paths['uploadPath']);
    if ((false === $uploadPathObj->isDir()) && (false === mkdir($configurator->paths['uploadPath'], 0755, true))) {
        $success = false;
        $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_UPLOAD_PATH, $configurator->paths['uploadPath']));
    } else {
        // Create index file in new directories
        $newFile = $configurator->paths['uploadPath']. '/index.html';
        $fileInfo = new \SplFileInfo($newFile);
        $fileObj = $fileInfo->openFile('w');
        $success = $fileObj->fwrite('<script>history.go(-1);</script>');
        $fileObj = null; // destroy SplFileObject so it closes file
        if (null === $success) {
            $success = false;
            $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_INDEX, $newFile));
            //break;
        }
        $fileInfo = null; // destroy this splFileInfo object
    }
    return $success;
}
