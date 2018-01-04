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

$helper = Helper::getHelper(basename(dirname(dirname(__DIR__))));

/**
 * Country element
 *
 * eleValue [0] = size
 *          [1] = allow multiple
 *          [2] = selected value(s)
 */

if (!empty($eleId)) {
    //if (empty($addOpt) && !empty($eleId)) {
    $eleValue = $element->getVar('ele_value');
}
$eleSize   = !empty($eleValue[0]) ? (int)$eleValue[0] : 1;
$eleMulti  = !empty($eleValue[1]) ? XformsConstants::ALLOW_MULTI : XformsConstants::DISALLOW_MULTI;
$countries = !empty($eleValue[2]) ? $eleValue[2] : $helper->getConfig('mycountry');

$size = new XformsFormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 5, 5, $eleSize, null, 'number');
$size->setAttribute('min', 0);
$size->setExtra('style="width: 5em;"');

$multInput = new XoopsFormRadioYN(_AM_XFORMS_ELE_MULTIPLE, 'ele_value[1]', $eleMulti);
$defInput  = new XoopsFormSelectCountry(_AM_XFORMS_ELE_DEFAULT, 'ele_value[2]', $countries);
//
$output->addElement($size, 1);
$output->addElement($multInput);
$output->addElement($defInput);
