\<?php
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
 * @package   \XoopsModules\Xforms\admin\elements
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\FormInput;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/* @var \XoopsModules\Xforms\Helper $helper */
$helper = Helper::getInstance();

/**
 * Obfuscated element
 *
 * value
 *      [0] = element rendered box size
 *      [1] = maximum size length
 */
$size      = !empty($value[0]) ? (int)$value[0] : $helper->getConfig('t_width');
$max       = !empty($value[1]) ? (int)$value[1] : $helper->getConfig('t_max');
$sizeInput = new FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 3, $size, null, 'number');
$sizeInput->setAttribute('min', 1);
$sizeInput->setExtra('style="width: 5em;"');

$maxInput = new FormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 3, 3, $max, null, 'number');
$maxInput->setAttribute('min', 1);
$maxInput->setExtra('style="width: 5em;"');

$output->addElement($sizeInput);
$output->addElement($maxInput);
