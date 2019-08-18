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
 * @package   \XoopsModules\Xforms\frontside
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */

use \XoopsModules\Xforms\Helper as xHelper;

$moduleDirName = basename(__DIR__);

require dirname(dirname(__DIR__)) . '/mainfile.php';
require __DIR__ . '/include/common.php';

$xformsFormsHandler = xHelper::getInstance()->getHandler('Forms');

include_once $GLOBALS['xoops']->path('www/class/xoopsform/tableform.php');
