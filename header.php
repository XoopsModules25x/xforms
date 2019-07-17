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
$moduleDirName = basename(__DIR__);

require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once __DIR__ . '/include/common.php';

require_once $GLOBALS['xoops']->path('www/class/xoopsform/tableform.php');

if (!class_exists('XformsUtility')) {
    xoops_load('utility', $moduleDirName);
}
