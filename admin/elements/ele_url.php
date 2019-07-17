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
 * @since           2.00
 */

use XoopsModules\Xforms;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

/**
 * Url element
 *
 * value  [0] = input box size
 *        [1] = maximum input size
 *        [2] = placeholder
 *        [3] = url type: 0 = http[s]|ftp[s], 1 = http[s] only, 2 = ftp[s] only
 */
$size      = !empty($value[0]) ? (int)$value[0] : $helper->getConfig('t_width');
$max       = !empty($value[1]) ? (int)$value[1] : $helper->getConfig('t_max');
$max       = ($size > $max) ? $size : $max;  // won't let max be smaller than size
$allowed   = isset($value[3]) ? (int)$value[3] < 3 ? (int)$value[3] : 0 : 0;
$sizeInput = new Xforms\FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 5, 5, (string)$size, null, 'number');
$sizeInput->setAttribute('min', 0);
$sizeInput->setExtra('style="width: 5em;"');

$maxInput = new Xforms\FormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 5, 5, (string)$max, null, 'number');
$maxInput->setAttribute('min', 1);
$maxInput->setExtra('style="width: 5em;"');
$maxInput->setExtra('onchange="document.getElementById(\'ele_value[2]\').setAttribute(\'maxlength\', this.value);"');

$allowUrls = new \XoopsFormElementTray(_AM_XFORMS_ELE_URL_TYPES);
$allowUrls->setDescription(_AM_XFORMS_ELE_URL_TYPES_DESC);
$radio = new \XoopsFormRadio('', 'ele_value[3]', $allowed);
$radio->addOptionArray([0 => 'both', 1 => 'http[s]', 2 => 'ftp[s]']);
$allowUrls->addElement($radio);

$phText      = isset($value[2]) ? $myts->htmlSpecialChars($value[2]) : '';
$placeholder = new \XoopsFormText(_AM_XFORMS_ELE_PLACEHOLDER, 'ele_value[2]', (string)$size, (string)$max, $phText);

//$defVal     = isset($value[3]) ? $myts->htmlSpecialChars($value[3]) : '';
//$default    = new Xforms\FormInput(_AM_XFORMS_ELE_DEFAULT, 'ele_value[3]', $size, $max, $defVal);
//$default->setDescription("You must enter http[s]://");
//$default->setExtra('pattern="https?://.+"');
//$output->addElement($default);

$output->addElement($sizeInput);
$output->addElement($maxInput);
$output->addElement($allowUrls);
$output->addElement($placeholder);
