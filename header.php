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

$moduleDirName = basename(__DIR__);

include dirname(dirname(__DIR__)) . '/mainfile.php';
include __DIR__ . '/include/common.php';
//xoops_load('XoopsRequest');
include_once $GLOBALS['xoops']->path('www/class/xoopsform/tableform.php');

if (!class_exists('XformsUtilities')) {
    xoops_load('utilities', $moduleDirName);
}
