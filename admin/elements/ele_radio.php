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
use XoopsModules\Xforms\FormRaw;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!class_exists('\XoopsModules\Xforms\FormRaw')) {
    xoops_load('FormRaw', basename(dirname(dirname(__DIR__))));
}

/**
 * Radio element
 *
 * value array([key] => data, [key1] => data1, etc...)
 *       key = item #, data 0|1 = not selected|selected
 * ele_value array([key] => label, [key1] => label1, etc...)
 *       key = item #, label is label for the option
 */
$optTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br>');
$optTray->setDescription(_AM_XFORMS_ELE_OPT_DESC2 . '<br><br>' . _AM_XFORMS_ELE_OTHER);
$optTray->addElement(new FormRaw('<div id="checked_radiotray">'));

//create 2 empty "options" if none exist
$keys     = (!empty($value) && is_array($value)) ? array_keys($value) : ['', ''];
$keyCount = count($keys);
for ($i = 0; $i < $keyCount; ++$i) {
    $eleTray  = new \XoopsFormElementTray('');
    $radioVal = (!empty($value[$keys[$i]])) ? $i : null;
    $radioEle = new \XoopsFormRadio('', 'checked', $radioVal);
    $radioEle->addOption($i, ' ');
    $eleTray->addElement($radioEle);
    $optVal     = $myts->htmlSpecialChars($keys[$i]);
    $formEleObj = new \XoopsFormText('', 'ele_value[' . $i . ']', 40, 255, $optVal);
    $formEleObj->setExtra('placeholder = "' . _AM_XFORMS_ELE_OPT_PLACEHOLDER . '"');
    $eleTray->addElement($formEleObj);
    $optTray->addElement($eleTray);
}
//$optTray->addElement(new FormRaw('<div id="' . $radioEle->getName(true) . '_radiotray"></div>'));
$optTray->addElement(new FormRaw('</div>'));
$moreOptsButton = new \XoopsFormButton('', 'moreoptions', _ADD, 'button');
$moreOptsButton->setExtra('onclick="addToTray' . $element->getVar('ele_id') . '()"');
$optTray->addElement($moreOptsButton);
$output->addElement($optTray);

//@TODO - this code should be made more generic so it can be used in more places than just here. It could
//        then be loaded using 'standard' .js include methods for a cleaner implementation
$funcScript = new FormRaw(
    '<script>function addToTray' . $element->getVar('ele_id') . '() {
//first time through set id (counter)
if (typeof addToTray' . $element->getVar('ele_id') . '.counter == "undefined") {
  addToTray' . $element->getVar('ele_id') . ".counter = $('[id^=\"ele_value[\"]').length;
}

//setup the radio button
var radioTray = document.getElementById(\"checked_radiotray\");
var rb = document.createElement(\"input\");
rb.setAttribute(\"type\", \"radio\");
rb.setAttribute(\"name\", \"checked\");
rb.setAttribute(\"id\", \"checked\" + addToTray" . $element->getVar('ele_id') . '.counter);
rb.value = addToTray' . $element->getVar('ele_id') . '.counter;

// setup the label
var lbl = document.createElement("label");
lbl.setAttribute("name", "xolb_checked");
lbl.setAttribute("for", "checked" + addToTray' . $element->getVar('ele_id') . '.counter);
lbl.innerHTML = " ";

// now create input box
var ib = document.createElement("input");
ib.setAttribute("type", "text");
ib.setAttribute("name", "ele_value[" + addToTray' . $element->getVar('ele_id') . '.counter + "]");
ib.setAttribute("id", "ele_value[" + addToTray' . $element->getVar('ele_id') . '.counter + "]");
ib.setAttribute("size", 40);
ib.setAttribute("maxwidth", 255);
ib.setAttribute("placeholder", "' . _AM_XFORMS_ELE_OPT_PLACEHOLDER . "\");
ib.value = \"\";

radioTray.appendChild(rb);
radioTray.appendChild(lbl);
// the following is to 'match' the code created by XoopsForm elements
//radioTray.innerHTML += \"&nbsp;&nbsp;\";
radioTray.insertAdjacentHTML('beforeend', \"&nbsp;&nbsp;\");
radioTray.appendChild(ib);
// add a line feed to separate the elements
//radioTray.innerHTML += \"<br>\";
radioTray.insertAdjacentHTML('beforeend', \"<br>\");
addToTray" . $element->getVar('ele_id') . '.counter = addToTray' . $element->getVar('ele_id') . '.counter + 1;
}</script>'
);
$output->addElement($funcScript);
