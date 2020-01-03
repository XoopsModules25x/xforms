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
 * Module: Xforms
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
    xoops_load('FormRaw', basename(dirname(dirname(__DIR__))));
}

/**
 * Select element
 *
 * ele_value array [0] => size,
 *                 [1] => allow_multi,
 *                 [2] => array (caption => selected)
 */
if (!empty($eleId)) { // not a new element
    $ele_value = $element->getVar('ele_value');
}
$eleSize    = !empty($ele_value[0]) ? $ele_value[0] : Constants::DEFAULT_ELEMENT_SIZE;
$size       = new FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 3, $eleSize, null, 'number');
$size->setAttribute('min', 1);
$size->setExtra('style="width: 3em;"');
$allowMulti = empty($ele_value[1]) ? Constants::DISALLOW_MULTI : Constants::ALLOW_MULTI;
$multiple   = new \XoopsFormRadioYN(_AM_XFORMS_ELE_MULTIPLE, 'ele_value[1]', $allowMulti);

$optTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br>');
$optTray->setDescription(_AM_XFORMS_ELE_OPT_DESC1 . '<br><br>' . _AM_XFORMS_ELE_OTHER);
$optTray->addElement(new FormRaw('<div id="checked_selecttray">'));
//create 2 empty "options" if none exist
$keys = (!empty($value[2]) && is_array($value[2])) ? array_keys($value[2]) : array('', '');
$keyArray     = (!empty($value[2]) && is_array($value[2])) ? array_keys($value[2]) : array('', '');
$checkedArray = (!empty($value[2]) && is_array($value[2])) ? array_values($value[2]) : array('', '');
foreach ($keyArray as $k => $v) {
    $eleTray = new \XoopsFormElementTray('');
    $checkVal = (!empty($checkedArray[$k])) ? $k : null;
    $checkEle = new \XoopsFormCheckBox('', 'checked[' . $k . ']', $checkVal);
    $checkEle->addOption($k, ' ');
    $eleTray->addElement($checkEle);
    $optVal = $myts->htmlSpecialChars($keyArray[$k]);
    $formEleObj = new \XoopsFormText('', 'ele_value[2][' . $k . ']', 40, 255, $optVal);
    $formEleObj->setExtra('placeholder = "' . _AM_XFORMS_ELE_OPT_PLACEHOLDER . '"');
    $eleTray->addElement($formEleObj);
    $optTray->addElement($eleTray);
}

$optTray->addElement(new FormRaw('</div>'));
$moreOptsButton = new \XoopsFormButton('', 'moreoptions', _ADD, 'button');
$moreOptsButton->setExtra('onclick="addToTray' . $element->getVar('ele_id') . '()"');
$optTray->addElement($moreOptsButton);
$output->addElement($size, 1);
$output->addElement($multiple);
$output->addElement($optTray);

//@TODO - this code should be made more generic so it can be used in more places than just here. It could
//        then be loaded using 'standard' .js include methods for a cleaner implementation
$funcScript = new FormRaw("<script>function addToTray" . $element->getVar('ele_id') . "() {
//first time through set id (counter)
if (typeof addToTray" . $element->getVar('ele_id') . ".counter == \"undefined\") {
  addToTray" . $element->getVar('ele_id') . ".counter = $('[id^=\"ele_value[\"]').length;
}

//setup the checkbox
var radioTray = document.getElementById(\"checked_selecttray\");
var rb = document.createElement(\"input\");
rb.setAttribute(\"type\", \"checkbox\");
rb.setAttribute(\"name\", \"checked[\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]\");
rb.setAttribute(\"id\", \"checked[\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]1\");
rb.value = addToTray" . $element->getVar('ele_id') . ".counter;

// setup the label
var lbl = document.createElement(\"label\");
lbl.setAttribute(\"name\", \"xolb_checked[\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]\");
lbl.setAttribute(\"for\", \"checked[\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]1\");
lbl.innerHTML = \" \";

// now create input box
var ib = document.createElement(\"input\");
ib.setAttribute(\"type\", \"text\");
ib.setAttribute(\"name\", \"ele_value[2][\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]\");
ib.setAttribute(\"id\", \"ele_value[2][\" + addToTray" . $element->getVar('ele_id') . ".counter + \"]\");
ib.setAttribute(\"size\", 40);
ib.setAttribute(\"maxwidth\", 255);
ib.setAttribute(\"placeholder\", \"" . _AM_XFORMS_ELE_OPT_PLACEHOLDER . "\");
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
addToTray" . $element->getVar('ele_id') . ".counter = addToTray" . $element->getVar('ele_id') . ".counter + 1;
}</script>");
$output->addElement($funcScript);
