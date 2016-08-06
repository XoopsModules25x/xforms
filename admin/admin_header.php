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
 * @copyright       {@see http://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             http://xoops.org XOOPS
 * @since           1.30
 */
use Xmf\Module\Helper;

$moduleDirName = basename(dirname(__DIR__));

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
require_once $GLOBALS['xoops']->path('/include/cp_functions.php');
require_once $GLOBALS['xoops']->path('/include/cp_header.php');
include_once $GLOBALS['xoops']->path('/class/xoopsformloader.php');
include_once $GLOBALS['xoops']->path('/class/pagenav.php');

// instantiate module helper
$xformsHelper = Helper::getHelper($moduleDirName);

include_once dirname(__DIR__) . '/include/config.php';

//require_once $xformsHelper->path('include/functions.php');
//require $xformsHelper->path('include/common.php');

if (!class_exists('XformsUtilities')) {
    xoops_load('utilities', $moduleDirName);
}

if (!class_exists('XformsFormInput')) {
    xoops_load('FormInput', 'xforms');
}

// Load language files
$xformsHelper->loadLanguage('admin');
$xformsHelper->loadLanguage('modinfo');
$xformsHelper->loadLanguage('main');

$pathIcon16 = '../' . $xformsHelper->getModule()->getInfo('icons16');
$pathIcon32 = '../' . $xformsHelper->getModule()->getInfo('icons32');
//$pathIcon32 = Admin::menuIconPath('../');

$mypathIcon16 = $GLOBALS['xoops']->url($xformsHelper->url('assets/images/icons/16'));

//xoops_load('XoopsRequest');
$xformsFormsHandler = $xformsHelper->getHandler('forms');
