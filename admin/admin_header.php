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

require_once __DIR__ . '/../../../include/cp_header.php';
require_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');
require_once $GLOBALS['xoops']->path('www/class/pagenav.php');
// require_once __DIR__ . '/../class/Utility.php';
require_once __DIR__ . '/../include/common.php';

$moduleDirName = basename(dirname(__DIR__));
/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

/** @var Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();


require_once $helper->path('include/functions.php');
//require $helper->path('include/common.php');

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

//
$xformsFormsHandler = $helper->getHandler('Forms');

//xoops_cp_header();
