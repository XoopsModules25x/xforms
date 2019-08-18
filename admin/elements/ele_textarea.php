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
 * @package   \XoopsModules\Xforms\admin\elements
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */
use \XoopsModules\Xforms;
use \XoopsModules\Xforms\Helper as xHelper;
use \XoopsModules\Xforms\FormInput;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/* @var \XoopsModules\Xforms\Helper $helper */
$helper = xHelper::getInstance();

/**
 * Textarea element
 *
 * value [0] = default value
 *       [1] = number of rows
 *       [2] = number of columns
 *       [3] = placeholder (HTML5)
 */
$rowAttrib = !empty($value[1]) ? $value[1] : $helper->getConfig('ta_rows');
$colAttrib = !empty($value[2]) ? $value[2] : $helper->getConfig('ta_cols');
$rows      = new FormInput(_AM_XFORMS_ELE_ROWS, 'ele_value[1]', 3, 3, (string)((int)$rowAttrib), null, 'number');
$rows->setAttribute('min', 0);
$rows->setExtra('style="width: 5em;"');

$cols    = new FormInput(_AM_XFORMS_ELE_COLS, 'ele_value[2]', 3, 3, (int)$colAttrib, null, 'number');
$cols->setAttribute('min', 0);
$cols->setExtra('style="width: 5em;"');

$default = new \XoopsFormTextArea(_AM_XFORMS_ELE_DEFAULT, 'ele_value[0]', isset($value[0]) ? $myts->htmlSpecialChars($value[0]) : '', 5, 35);

//placeholder
$plAttrib = isset($value[3]) ? $myts->htmlSpecialChars($value[3]) : '';
$placeholder = new \XoopsFormText(_AM_XFORMS_ELE_PLACEHOLDER, 'ele_value[3]', 35, 255, $plAttrib);

$output->addElement($rows, 1);
$output->addElement($cols, 1);
$output->addElement($placeholder);
$output->addElement($default);
