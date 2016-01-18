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
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
require_once XOOPS_ROOT_PATH . '/include/cp_header.php';

include dirname(__DIR__) .'/include/common.php';
define('XFORMS_ADMIN_URL', XFORMS_URL . '/admin/main.php');
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

global $xoopsModule;

$thisModuleDir = XFORMS_DIRNAME;

//if functions.php file exist
require_once dirname(__DIR__) . '/include/functions.php';

// Load language files
xoops_loadLanguage('admin', XFORMS_DIRNAME);
xoops_loadLanguage('modinfo', XFORMS_DIRNAME);
xoops_loadLanguage('main', XFORMS_DIRNAME);

$pathIcon16      = '../' . $xoopsModule->getInfo('icons16');
$pathIcon32      = '../' . $xoopsModule->getInfo('icons32');
$pathModuleAdmin = $xoopsModule->getInfo('dirmoduleadmin');

$mypathIcon16 = XOOPS_URL . '/modules/' . $thisModuleDir . '/assets/images/icons/16';
//$pathIcon32 = '../'.$xoopsModule->getInfo('icons32');

include_once $GLOBALS['xoops']->path($pathModuleAdmin . '/moduleadmin.php');
