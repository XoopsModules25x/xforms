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

use XoopsModules\Xforms;

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');
//require_once $GLOBALS['xoops']->path('www/class/pagenav.php');
// require_once  dirname(__DIR__) . '/class/Utility.php';
require_once dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));
/** @var \XoopsModules\Xforms\Helper $helper */
$helper = \XoopsModules\Xforms\Helper::getInstance();

/** @var Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();

require_once $helper->path('include/functions.php');
//require_once $helper->path('include/common.php');

//if (!class_exists('Xforms\FormInput')) {
//    xoops_load('FormInput', 'xforms');
//}

//$pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);
//$pathIcon32    = \Xmf\Module\Admin::iconUrl('', 32);
//$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
$helper->loadLanguage('common');

//$myts = \MyTextSanitizer::getInstance();
//
//if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof XoopsTpl)) {
//    require_once $GLOBALS['xoops']->path('class/template.php');
//    $xoopsTpl = new \XoopsTpl();
//}

$formsHandler = $helper->getHandler('Forms');

//xoops_cp_header();
