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
use XoopsModules\Xforms\FormRaw;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!class_exists(FormRaw::class)) {
    xoops_load('FormRaw', basename(dirname(__DIR__, 2)));
}

/**
 * Checkbox element
 *
 * value array([key] => data, [key1] => data1, etc...)
 *       key = item #, data 0|1 = unchecked|checked
 * ele_value array([key] => label, [key1] => label1, etc...)
 *       key = item #, label is label for the option
 */
$optTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br>');
$optTray->setDescription('<br>' . _AM_XFORMS_ELE_OTHER);
$optTray->addElement(new FormRaw('<div id="checked_checkboxtray">'));

//create 2 empty "options" if none exist
$keys     = (!empty($value) && is_array($value)) ? array_keys($value) : ['', ''];
$keyCount = count($keys);
for ($i = 0; $i < $keyCount; ++$i) {
    $eleTray     = new \XoopsFormElementTray('');
    $checkboxVal = (isset($value[$keys[$i]]) && !empty($value[$keys[$i]])) ? $i : null;
    $checkboxEle = new \XoopsFormCheckbox('', 'ckbox[' . $i . ']', $checkboxVal);
    $checkboxEle->addOption(1, ' ');
    $eleTray->addElement($checkboxEle);
    $optVal     = htmlspecialchars($keys[$i], ENT_QUOTES | ENT_HTML5);
    $formEleObj = new \XoopsFormText('', 'ele_value[' . $i . ']', 40, 255, $optVal);
    $formEleObj->setExtra('placeholder = "' . _AM_XFORMS_ELE_OPT_PLACEHOLDER . '"');
    $eleTray->addElement($formEleObj);
    $optTray->addElement($eleTray);
}
//$optTray->addElement(new FormRaw('<div id="' . $checkboxEle->getName(true) . '_checkboxtray"></div>'));
$optTray->addElement(new FormRaw('</div>'));
$moreOptsButton = new \XoopsFormButton('', 'moreoptions', _ADD, 'button');
$moreOptsButton->setExtra('onclick="addToCboxTray()"');
$optTray->addElement($moreOptsButton);
$output->addElement($optTray);

/** {@internal @todo this code should be made more generic so it can be used in
 * more places than just here. It could then be loaded using 'standard' .js
 * include methods for a cleaner implementation}}}
 */
$funcScript = new FormRaw(
    "<script>function addToCboxTray() {
//first time through set id (counter)
var counterCB = document.querySelectorAll('[id^=ele_value]').length;
var checkboxTray = document.getElementById(\"checked_checkboxtray\");

// setup the checkbox button
var rb = document.createElement(\"input\");
rb.setAttribute(\"type\", \"checkbox\");
rb.setAttribute(\"name\",\"ckbox[\" + counterCB + \"]\");
rb.setAttribute(\"id\", \"ckbox[\" + counterCB + \"]\");
rb.value = 1;

// setup the label
var lbl = document.createElement(\"label\");
lbl.setAttribute(\"name\", \"xolb_ckbox\");
lbl.setAttribute(\"for\", \"ckbox[\" + counterCB + \"]\");
lbl.innerHTML = \" \";

// now create input box
var ib = document.createElement(\"input\");
ib.setAttribute(\"type\", \"text\");
ib.setAttribute(\"name\", \"ele_value[\" + counterCB + \"]\");
ib.setAttribute(\"id\", \"ele_value[\" + counterCB + \"]\");
ib.setAttribute(\"size\", 40);
ib.setAttribute(\"maxwidth\", 255);
ib.setAttribute(\"placeholder\", \"" . _AM_XFORMS_ELE_OPT_PLACEHOLDER . "\");
ib.value = \"\";

// add them to the HTML input form
checkboxTray.appendChild(rb);
checkboxTray.appendChild(lbl);
// the following is to 'match' the code created by XoopsForm elements
checkboxTray.insertAdjacentHTML('beforeend', \"&nbsp;&nbsp;\");
checkboxTray.appendChild(ib);
// add a line feed to separate the elements
checkboxTray.insertAdjacentHTML('beforeend', \"<br>\");
}</script>"
);
$output->addElement($funcScript);
