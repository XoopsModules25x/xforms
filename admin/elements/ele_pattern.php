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
 * @since           2.00
 */

defined('XFORMS_ROOT_PATH') || die('Restricted access');

$helper = Xforms\Helper::getInstance();

/**
 * Pattern element
 *
 *  value [0] = input box size
 *        [1] = maximum input size
 *        [2] = placeholder
 *        [3] = pattern: use HTML5 pattern to validate input
 *        [4] = pattern description
 */

$size      = !empty($value[0]) ? (int)$value[0] : $helper->getConfig('t_width');
$max       = !empty($value[1]) ? (int)$value[1] : $helper->getConfig('t_max');
$max       = ($size > $max) ? $size : $max;  // won't let max be smaller than size
$pattern   = isset($value[3]) ? $value[3] : '';
$sizeInput = new Xforms\FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 5, 5, (string)$size, null, 'number');
$sizeInput->setAttribute('min', 0);
$sizeInput->setExtra('style="width: 5em;"');

$maxInput = new Xforms\FormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 5, 5, (string)$max, null, 'number');
$maxInput->setAttribute('min', 1);
$maxInput->setExtra('style="width: 5em;"');
$maxInput->setExtra('onchange="document.getElementById(\'ele_value[2]\').setAttribute(\'maxlength\', this.value);"');

$phText      = isset($value[2]) ? $myts->htmlSpecialChars($value[2]) : '';
$placeholder = new \XoopsFormText(_AM_XFORMS_ELE_PLACEHOLDER, 'ele_value[2]', (string)$size, (string)$max, $phText);

$ptnInput = new Xforms\FormInput(_AM_XFORMS_ELE_PATTERN_INP, 'ele_value[3]', 25, 50, $pattern, null, 'text');
$ptnInput->setDescription(_AM_XFORMS_ELE_PATTERN_INP_DESC);

$ptnDesc = new Xforms\FormInput(_AM_XFORMS_ELE_PATTERN_DESC, 'ele_value[4]', 25, 50, $pattern, null, 'text');
$ptnDesc->setDescription(_AM_XFORMS_ELE_PATTERN_DESC_DESC);

$output->addElement($sizeInput);
$output->addElement($maxInput);
$output->addElement($ptnInput);
$output->addElement($ptnDesc);
$output->addElement($placeholder);