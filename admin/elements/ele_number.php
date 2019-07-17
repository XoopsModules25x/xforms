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
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!class_exists('Xforms\FormInput')) {
    XoopsLoad::load('FormInput', basename(dirname(dirname(__DIR__))));
}

/**
 * Number element
 *
 * value [0] = minimum value allowed
 *       [1] = maximum value allowed
 *       [2] = default value
 *       [3] = element input field size
 *       [4] = set minimum value 0|false = no, else = yes
 *       [5] = set maximum value 0|false = no, else = yes
 *       [6] = set default value 0|false = no, else = yes
 *       [7] = step size
 */
$minVal    = !empty($value[0]) ? (int)$value[0] : 0;
$maxVal    = !empty($value[1]) ? (int)$value[1] : 100;
$defVal    = !empty($value[2]) ? (int)$value[2] : 0;
$size      = !empty($value[3]) ? (int)$value[3] : 10;
$setMinVal = !empty($value[4]) ? 1 : 0;
$setMaxVal = !empty($value[5]) ? 1 : 0;
$setDefVal = !empty($value[6]) ? 1 : 0;
$step      = !empty($value[7]) ? (float)$value[3] : (float)1;

$minTray  = new \XoopsFormElementTray(_AM_XFORMS_ELE_NUMBER_MIN, '<br>', 'minTray');
$setMin   = new \XoopsFormRadioYN(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_MIN), 'ele_value[4]', $setMinVal);
$minInput = new Xforms\FormInput('', 'ele_value[0]', 7, 255, $minVal, null, 'number');
$minInput->setAttribute('size', 7);
$minTray->addElement($setMin);
$minTray->addElement($minInput);

$maxTray  = new \XoopsFormElementTray(_AM_XFORMS_ELE_NUMBER_MAX, '<br>', 'maxTray');
$setMax   = new \XoopsFormRadioYN(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_MAX), 'ele_value[5]', $setMaxVal);
$maxInput = new Xforms\FormInput('', 'ele_value[1]', 7, 255, $maxVal, null, 'number');
$maxInput->setAttribute('size', 7);
$maxTray->addElement($setMax);
$maxTray->addElement($maxInput);

$stepInput = new Xforms\FormInput(_AM_XFORMS_ELE_NUMBER_STEP, 'ele_value[7]', 7, 255, $step, null, 'number');
$stepInput->setAttribute('size', 7);
$stepInput->setAttribute('min', 1);
$stepInput->setAttribute('pattern', '[0-9].');

$defTray  = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT, '<br>', 'defTray');
$setDef   = new \XoopsFormRadioYN(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_DEFAULT), 'ele_value[6]', $setDefVal);
$defInput = new Xforms\FormInput(_AM_XFORMS_ELE_DEFAULT, 'ele_value[2]', 7, 255, $defVal, null, 'number');
$defInput->setAttribute('size', 7);
$defInput->setAttribute('pattern', '[0-9].');
$defTray->addElement($setDef);
$defTray->addElement($defInput);

$sizeInput = new Xforms\FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[3]', 7, 255, $size, null, 'number');
$sizeInput->setAttribute('size', 7);
$sizeInput->setAttribute('min', 0);

$output->addElement($minTray);
$output->addElement($maxTray);
$output->addElement($stepInput);
$output->addElement($defTray);
$output->addElement($sizeInput, 1);
