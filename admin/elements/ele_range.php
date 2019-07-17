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

use XoopsModules\Xforms\Constants;

defined('XFORMS_ROOT_PATH') || die('Restricted access');

if (!class_exists('Xforms\FormRaw')) {
    XoopsLoad::load('FormRaw', basename(dirname(dirname(__DIR__))));
}
/**
 * Range element
 *
 * value [0] = default
 *       [1] = default option (0 = no, 1 = yes)
 *       [2] = min num
 *       [3] = max num
 *       [4] = step
 */
$default    = !empty($value[0]) ? $value[0] : null;
$minNum     = !empty($value[2]) ? $value[2] : 0;
$maxNum     = !empty($value[3]) ? $value[3] : 0;
$setTheStep = !empty($value[4]) ? $value[4] : Constants::ELE_DEFAULT_STEP;
$setTheDef  = !empty($value[1]) ? Constants::ELE_YES : Constants::ELE_NO;

$minEle = new Xforms\FormInput(_AM_XFORMS_ELE_RANGE_MIN, 'ele_value[2]', 5, 15, $minNum, null, 'number');
$minEle->setAttribute('pattern', '[\d.\+\-]*$');
$minEle->setClass('center');

$maxEle = new Xforms\FormInput(_AM_XFORMS_ELE_RANGE_MAX, 'ele_value[3]', 5, 15, $maxNum, null, 'number');
$maxEle->setAttribute('pattern', '[\d.\+\-]*$');
$maxEle->setClass('center');

$stepEle = new Xforms\FormInput(_AM_XFORMS_ELE_RANGE_STEP, 'ele_value[4]', 5, 10, (float)$setTheStep, null, 'number');
$stepEle->setAttribute('min', 1);
$stepEle->setAttribute('pattern', '^[1-9][\d]*$');
$stepEle->setClass('center');

$defTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT);
// use XoopsFormRadio instead of XoopsFormRadioYN so order or options can be set to No then Yes
$setDef = new \XoopsFormRadio('', 'ele_value[1]', $setTheDef);
$setDef->addOptionArray([0 => _NO, 1 => _YES]);
$defEle = new Xforms\FormInput('', 'ele_value[0]', 15, 15, $default, null, 'number');
$defEle->setAttribute('pattern', '[\d.\+\-]*$');
//@todo need to add javascript to make sure min < default < max
$defTray->addElement($setDef);
$defTray->addElement($defEle);

$output->addElement($minEle, 1);
$output->addElement($maxEle, 1);
$output->addElement($stepEle);
$output->addElement($defTray);
