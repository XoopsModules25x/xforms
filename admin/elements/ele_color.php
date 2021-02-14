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
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\FormInput;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Color element
 *
 * value [0] = default value
 *       [1] = input box size
 */
$defVal   = !empty($value[0]) ? $value[0] : 0; // default
$size     = !empty($value[1]) ? (int)$value[1] : 10; // input box size
$lblSuff  = rand();
$defInput = new FormInput('', 'ele_value[0]', 7, 255, $defVal, null, 'color');
$defInput->setExtra('onchange="document.getElementById(\'default_label_' . $lblSuff . '\').innerHTML = this.value;"');
$defLbl = new \XoopsFormLabel('', '<label class="middle" id="default_label_' . $lblSuff . '" for="ele_value[0]">' . $defVal . '</label>');

$sizeInput = new FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[1]', 7, 255, $size, null, 'number');
$sizeInput->setAttribute('min', 0);
$sizeInput->setExtra('style="width: 7em;"');
$colorTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT);

$colorTray->addElement($defInput);
$colorTray->addElement($defLbl);

$output->addElement($sizeInput, 1);
$output->addElement($colorTray, 1);
