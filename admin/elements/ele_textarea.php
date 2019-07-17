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

use Xmf\Module\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

$xformsHelper = Helper::getHelper(basename(dirname(dirname(__DIR__))));

/**
 * Textarea element
 *
 * value [0] = default value
 *       [1] = number of rows
 *       [2] = number of columns
 *       [3] = placeholder (HTML5)
 */
$rowAttrib = !empty($value[1]) ? $value[1] : $xformsHelper->getConfig('ta_rows');
$colAttrib = !empty($value[2]) ? $value[2] : $xformsHelper->getConfig('ta_cols');
$rows      = new XformsFormInput(_AM_XFORMS_ELE_ROWS, 'ele_value[1]', 3, 3, (string)((int)$rowAttrib), null, 'number');
$rows->setAttribute('min', 0);
$rows->setExtra('style="width: 5em;"');

$cols = new XformsFormInput(_AM_XFORMS_ELE_COLS, 'ele_value[2]', 3, 3, (int)$colAttrib, null, 'number');
$cols->setAttribute('min', 0);
$cols->setExtra('style="width: 5em;"');

$default = new XoopsFormTextArea(_AM_XFORMS_ELE_DEFAULT, 'ele_value[0]', isset($value[0]) ? $myts->htmlSpecialChars($value[0]) : '', 5, 35);

//placeholder
$plAttrib    = isset($value[4]) ? $value[3] : '';
$placeholder = new XoopsFormText(_AM_XFORMS_ELE_PLACEHOLDER, 'ele_value[3]', 35, 255, $plAttrib);

$output->addElement($rows, 1);
$output->addElement($cols, 1);
$output->addElement($placeholder);
$output->addElement($default);
