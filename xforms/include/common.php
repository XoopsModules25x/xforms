<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xForms
 * @since           1.30
 * @author          Xoops Development Team (see credits.txt)
 */

// defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

//$module_handler =& xoops_gethandler('module');
//$xoopsModule = & $module_handler->getByDirname(basename(dirname(__DIR__)));

// This must contain the name of the folder in which reside xForms
define("XFORMS_DIRNAME", basename(dirname(__DIR__)));
define("XFORMS_URL", XOOPS_URL . '/modules/' . XFORMS_DIRNAME);
define("XFORMS_IMAGES_URL", XFORMS_URL . '/assets/images');
//define("XFORMS_ADMIN_URL", XFORMS_URL . '/admin');
//define('XFORMS_ADMIN_URL', XFORMS_URL . 'admin/main.php');
define("XFORMS_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . XFORMS_DIRNAME);

//define("XFORMS_UPLOAD_PATH", $xoopsModuleConfig['uploaddir'] . '/');
define('XFORMS_UPLOAD_PATH', XOOPS_ROOT_PATH . '/uploads/' . XFORMS_DIRNAME);

xoops_loadLanguage('common', XFORMS_DIRNAME);

include_once XFORMS_ROOT_PATH . '/include/functions.php';
include_once XFORMS_ROOT_PATH . '/class/constants.php';
include_once XFORMS_ROOT_PATH . '/class/session.php';
include_once XFORMS_ROOT_PATH . '/class/xforms.php';
//include_once XFORMS_ROOT_PATH . '/class/request.php';
//include_once XFORMS_ROOT_PATH . '/class/breadcrumb.php';

$debug  = false;
$xforms = XformsXforms::getInstance($debug);

//This is needed or it will not work in blocks.
global $xforms_isAdmin;

// Load only if module is installed
if (is_object($xforms->getModule())) {
    // Find if the user is admin of the module
    $xforms_isAdmin = xforms_userIsAdmin();
}

//if (!defined("XFORMS_CONSTANTS_DEFINED")) {
//    define("XFORMS_URL", XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/');
//    define("XFORMS_ROOT_PATH", XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/');
//    define("XFORMS_UPLOAD_PATH", $xoopsModuleConfig['uploaddir'] . '/');
//    define("XFORMS_CONSTANTS_DEFINED", true);
//}

$xforms_form_mgr = xoops_getmodulehandler('forms', XFORMS_DIRNAME);

//set Upload directory, if doe
if (false != XFORMS_UPLOAD_PATH) {
    if (!is_dir(XFORMS_UPLOAD_PATH)) {
        $oldumask = umask(0);
        mkdir(XFORMS_UPLOAD_PATH, 0777);
        umask($oldumask);
    }
    if (is_dir(XFORMS_UPLOAD_PATH) && !is_writable(XFORMS_UPLOAD_PATH)) {
        chmod(XFORMS_UPLOAD_PATH, 0777);
    }
}
