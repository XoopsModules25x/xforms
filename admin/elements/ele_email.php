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
 * Module: Xforms
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

/** @var \XoopsModules\Xforms\Helper $helper */
$helper = Helper::getInstance();

/**
 * Email element
 *
 * value
 *      [0] = element rendered box size
 *      [1] = max length
 *      [2] = default value
 */
$size     = !empty($value[0]) ? (int)$value[0] : $helper->getConfig('t_width');
$maxAttr  = !empty($value[1]) ? (int)$value[1] : 254;
$defVal   = isset($value[2]) ? $myts->htmlSpecialChars($value[2]) : '';

$sizeInput = new FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 5, 5, $size, null, 'number');
$sizeInput->setAttribute('min', 1);
$sizeInput->setExtra('style="width: 5em;"');

$max       = new FormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 5, 5, $maxAttr, null, 'number');
$max->setAttribute('min', 1);
$max->setExtra('style="width: 5em;"');

$default   = new \XoopsFormText(_AM_XFORMS_ELE_EMAIL_ADD_DEFAULT, 'ele_value[2]', $size, $maxAttr, $defVal);

$output->addElement($sizeInput,1);
$output->addElement($max,1);
$output->addElement($default);
