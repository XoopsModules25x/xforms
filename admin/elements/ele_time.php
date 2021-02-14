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
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\FormInput;
use XoopsModules\Xforms\FormRaw;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!class_exists(FormRaw::class)) {
    xoops_load('formraw', basename(dirname(__DIR__, 2)));
}

/**
 * Time element
 *
 * value [0] = minimum value allowed
 *       [1] = maximum value allowed
 *       [2] = default value
 *       [3] = step size
 *       [4] = set minimum value 0|false = no, else = yes
 *       [5] = set maximum value 0|false = no, else = yes
 *       [6] = set default value 0|false = no, else = yes
 */
$minVal    = !empty($value[0]) ? preg_replace('/[^0-9:]/', '', $value[0]) : '12:00';
$maxVal    = !empty($value[1]) ? preg_replace('/[^0-9:]/', '', $value[1]) : '12:00';
$defVal    = !empty($value[2]) ? preg_replace('/[^0-9:]/', '', $value[2]) : '12:00';
$step      = !empty($value[3]) ? abs((float)$value[3]) : (float)60;
$setMinVal = !empty($value[4]) ? Constants::ELE_YES : Constants::ELE_NO;
$setMaxVal = !empty($value[5]) ? Constants::ELE_YES : Constants::ELE_NO;
$setDefVal = !empty($value[6]) ? Constants::ELE_YES : Constants::ELE_NO;

$minTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_NUMBER_MIN, null, 'minTray');
$setMin  = new \XoopsFormRadio(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_MIN), 'ele_value[4]', $setMinVal);
$setMin->addOptionArray([Constants::ELE_NO => _NO, Constants::ELE_YES => _YES]);
$minInput = new FormInput('', 'ele_value[0]', 8, 10, $minVal, null, 'time');
$minInput->setExtra('style="width: 8em;" required');
$minInput->setAttribute('pattern', '[0-9:aAmMpP].');
$minTray->addElement($setMin);
$minTray->addElement($minInput);

$maxTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_NUMBER_MAX, null, 'maxTray');
$setMax  = new \XoopsFormRadio(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_MAX), 'ele_value[5]', $setMaxVal);
$setMax->addOptionArray([Constants::ELE_NO => _NO, Constants::ELE_YES => _YES]);
$maxInput = new FormInput('', 'ele_value[1]', 8, 10, $maxVal, null, 'time');
$maxInput->setExtra('style="width: 8em;" required');
$maxInput->setAttribute('pattern', '[0-9:aAmMpP].');
$maxTray->addElement($setMax);
$maxTray->addElement($maxInput);

$stepInput = new FormInput(_AM_XFORMS_ELE_NUMBER_STEP, 'ele_value[3]', 8, 10, $step, null, 'number');
$stepInput->setDescription(_AM_XFORMS_ELE_NUMBER_STEP_DESC);
$stepInput->setExtra('style="width: 8em;"');
$stepInput->setAttribute('pattern', '\d.');
$stepInput->setAttribute('min', 1);

$defTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT, null, 'defTray');
$setDef  = new \XoopsFormRadio(sprintf(_AM_XFORMS_ELE_NUMBER_SET, _AM_XFORMS_ELE_NUMBER_SET_DEFAULT), 'ele_value[6]', $setDefVal);
$setDef->addOptionArray([Constants::ELE_NO => _NO, Constants::ELE_YES => _YES]);
$defInput = new FormInput(_AM_XFORMS_ELE_DEFAULT, 'ele_value[2]', 8, 10, $defVal, null, 'time');
$defInput->setExtra('style="width: 8em;" required');
$defInput->setAttribute('pattern', '[0-9:aAmMpP].'); //useful if browser doesn't support 'time'
$defTray->addElement($setDef);
$defTray->addElement($defInput);

/** {@internal pseudo code to change default value if:
 * if (setDef = true AND
 *   ((defVal < minVal) && setMin = true)
 *   OR
 *   ((defVal > maxVal) && setMax = true)
 * }
 * if (maxVal < MinVal)
 *    setMax = false;
 * }
 */
$fixerJs = new FormRaw(
    "<div id=\"ele_js\">\n"
    . "  <script>\n"
    //         . "    $('input[id=\"ele_value[0]\"], input[id=\"ele_value[1]\"], input[id=\"ele_value[2]\"], input[name=\"ele_value[4]\"], input[name=\"ele_value[5]\"], input[name=\"ele_value[6]\"]').click(function() {\n"
    . "    $('input[id^=\"ele_value[\"]').click(function() {\n"
    . "      var useMinDate = document.getElementById(\"ele_value[4]2\").checked;\n"
    . "      var useMaxDate = document.getElementById(\"ele_value[5]2\").checked;\n"
    . "      var useDefDate = document.getElementById(\"ele_value[6]2\").checked;\n"
    . "      var minVal     = document.getElementById(\"ele_value[0]\").value;\n"
    . "      var maxVal     = document.getElementById(\"ele_value[1]\").value;\n"
    . "      var defVal     = document.getElementById(\"ele_value[2]\").value;\n"
    //         . "      alert(\"Input Changed \" + useMinDate)\n"
    . "      if (useDefDate === true) {\n"
    //         . "          alert(\"Use default date \" + minVal + \" \" + maxVal + \" \" + defVal);\n"
    . "        if (useMinDate === true) {\n"
    . "          if (minVal > defVal) {\n"
    //         . "          alert(\"Min higher than default\");\n"
    . "            document.getElementById(\"ele_value[2]\").value = minVal;\n"
    . "          }\n"
    . "        }\n"
    . "        if (useMaxDate === true) {\n"
    . "          if (maxVal <= minVal) {\n"
    //         . "          alert(\"Max lower than min date\");\n"
    . '            document.getElementById("ele_value[5]1").checked = true;'
    . "          }\n"
    . "          if (maxVal < defVal) {\n"
    //         . "          alert(\"Max lower than default\");\n"
    . "            document.getElementById(\"ele_value[2]\").value = maxVal;\n"
    . "          }\n"
    . "        }\n"
    . "      }\n"
    . "    });\n"
    . "  </script>\n"
    . "</div>\n"
);
$defTray->addElement($fixerJs);

$output->addElement($minTray);
$output->addElement($maxTray);
$output->addElement($stepInput);
$output->addElement($defTray);
