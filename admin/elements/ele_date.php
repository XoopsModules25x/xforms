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
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\FormInput;
use XoopsModules\Xforms\FormRaw;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!class_exists('\XoopsModules\Xforms\FormRaw')) {
    xoops_load('formraw', basename(dirname(dirname(__DIR__))));
}
/**
 * Date element
 *
 * value [0] = default date
 *       [1] = default date option (1 = current, 2 = default date)
 *       [2] = min date
 *       [3] = min date option (0 = none, 1 = current, 2 = min date)
 *       [4] = max date
 *       [5] = max date option (0 = none, 1 = current, 2 = max date)
 */

$dateValue = !empty($value[0]) ? $value[0] : date('Y-m-d');
$minDate   = !empty($value[2]) ? $value[2] : date('Y-m-d');
$maxDate   = !empty($value[4]) ? $value[4] : date('Y-m-d');
$setTheDef = !empty($value[1]) ? (int)$value[1] : Constants::ELE_CURR;
$setTheMin = !empty($value[3]) ? (int)$value[3] : Constants::ELE_NO;
$setTheMax = !empty($value[5]) ? (int)$value[5] : Constants::ELE_NO;

$minTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DATE_MIN);
$setMin  = new \XoopsFormRadio('', 'ele_value[3]', $setTheMin);
$setMin->addOptionArray(array(Constants::ELE_NO => _NO,
                            Constants::ELE_CURR => _AM_XFORMS_ELE_DATE_CUR,
                           Constants::ELE_OTHER => _AM_XFORMS_ELE_OPT_OTHER)
);
$minEle = new FormInput('', 'ele_value[2]', 15, 15, $minDate, null, 'date');
$minTray->addElement($setMin);
$minTray->addElement($minEle);

$maxTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DATE_MAX);
$setMax  = new \XoopsFormRadio('', 'ele_value[5]', $setTheMax);
$setMax->addOptionArray(array(Constants::ELE_NO => _NO,
                            Constants::ELE_CURR => _AM_XFORMS_ELE_DATE_CUR,
                           Constants::ELE_OTHER => _AM_XFORMS_ELE_OPT_OTHER)
);
$maxEle = new FormInput('', 'ele_value[4]', 15, 15, $maxDate, null, 'date');
$maxTray->addElement($setMax);
$maxTray->addElement($maxEle);

$date   = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT);
$setDef = new \XoopsFormRadio('', 'ele_value[1]', $setTheDef);
$setDef->addOptionArray(array(Constants::ELE_CURR => _AM_XFORMS_ELE_DATE_CUR,
                             Constants::ELE_OTHER => _AM_XFORMS_ELE_OPT_OTHER)
);

$inpEle = new FormInput('', 'ele_value[0]', 15, 15, $dateValue, null, 'date');
$date->addElement($setDef);
$date->addElement($inpEle);
$date->addElement(new FormRaw(
    "<script>\n"
    .    "if (!Modernizr.inputtypes.date) {\n"
//    .    "alert(\"Browser doesn't support date\");\n"
    .    "  $('input[type=date]')\n"
    .    "  .attr('type', 'text')\n"
    .    "  .datepicker({\n"
    .    "  // Consistent format with the HTML5 picker\n"
    .    "  dateFormat: 'yy-mm-dd'\n"
    .    "  });\n"
    .    "}\n"
    . "</script>\n"
));

$output->addElement($minTray);
$output->addElement($maxTray);
$output->addElement($date);
