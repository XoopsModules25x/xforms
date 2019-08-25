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
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 *
 * @see \Xmf\Request
 * @see \Xmf\Module\Helper
 * @see \Xmf\Module\Admin
 * @see \XoopsModules\Xforms\Helper
 */

use \XoopsModules\Xforms;
use \XoopsModules\Xforms\Constants;
use \XoopsModules\Xforms\FormInput;
use \Xmf\Module\Helper;

require_once __DIR__ . '/admin_header.php';

/* @var \XoopsModules\Xforms\Helper $helper */
/* @var \XoopsModules\Xforms\ElementHandler $xformsEleHandler */
$xformsEleHandler = $helper->getHandler('Element');

$myts = \MyTextSanitizer::getInstance();

if ($formsHandler->getCount() < 1) {
    $helper->redirect('admin/main.php?op=edit', Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_GO_CREATE_FORM);
}

$op         = \Xmf\Request::getCmd('op', '');
$clone      = \Xmf\Request::getInt('clone', Constants::FORM_NOT_CLONED);
$formId     = \Xmf\Request::getInt('form_id', Constants::FORM_NOT_VALID);
$eleId      = \Xmf\Request::getInt('ele_id', Constants::ELE_NOT_VALID);
$eleValue   = \Xmf\Request::getArray('ele_value', '');
$eleCaption = \Xmf\Request::getText('ele_caption', '', 'POST');
$eleOrder   = \Xmf\Request::getInt('ele_order', 0, 'POST');
$eleReq     = \Xmf\Request::getInt('ele_req', Constants::ELEMENT_NOT_REQD, 'POST');
$submit     = \Xmf\Request::getCmd('submit', '', 'POST');

switch ($op) {
    case 'edit':
        xoops_cp_header();
        /* @var \Xmf\Module\Admin $adminObject */
        $adminObject->displayNavigation(basename(__FILE__));
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url('browse.php?modules/' . $moduleDirName . '/assets/css/style.css'));
/*
        if (!class_exists('XformsFormInput')) {
            include_once $helper->path('class/FormInput.php');
        }
*/
        if (Constants::ELE_NOT_VALID !== (int)$eleId) {
            $element     = $xformsEleHandler->get($eleId);
            $eleType     = $element->getVar('ele_type');
            $outputTitle = (Constants::FORM_CLONED === $clone) ? _AM_XFORMS_ELE_CREATE : sprintf(_AM_XFORMS_ELE_EDIT, $element->getVar('ele_caption'));
        } else {
            $element     = $xformsEleHandler->create();
            $eleType     = mb_strtolower(\Xmf\Request::getCmd('ele_type', 'text'));
            $outputTitle = _AM_XFORMS_ELE_CREATE;
        }

        if ('date' === $eleType) { // only load jquery & modernizr if needed
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.structure.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.theme.min.css');
            $GLOBALS['xoTheme']->addScript('browse.php?modules/' . $moduleDirName . '/assets/js/modernizr-custom.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        }

        $sysHelper  = Helper::getHelper('system');
        $output     = new \XoopsThemeForm($outputTitle, 'form_ele', $_SERVER['PHP_SELF'], 'post', true);

        $value      = $element->getVar('ele_value', 'f');
        $eleReq     = $element->getVar('ele_req');
        $displayRow = $element->getVar('ele_display_row');
        $eleDisplay = $element->getVar('ele_display');
        $eleOrder   = $element->getVar('ele_order');

        if ('html' !== $eleType) {
            // editor settings
            $editorConfigs = array('editor' => $sysHelper->getConfig('general_editor'),
                                     'rows' => 10,
                                     'cols' => 60,
                                    'width' => '100%',
                                   'height' => '350px',
                                     'name' => 'ele_caption',
                                    'value' => (Constants::FORM_CLONED === $clone)
                                               ? sprintf(_AM_XFORMS_COPIED, $element->getVar('ele_caption', 'e'))
                                               : $element->getVar('ele_caption', 'e')
            );
            // end editor settings
            $textEleCaption  = new \XoopsFormEditor(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $editorConfigs);
            $captionRenderer = $textEleCaption->editor->renderer;
            if (property_exists($captionRenderer, 'skipPreview')) {
                $textEleCaption->editor->renderer->skipPreview = true;
            }
            $output->addElement($textEleCaption);

            if ('pattern' === $eleType) {
                $checkEleReq = new \XoopsFormHidden('ele_req', Constants::REQUIRED);
            } else {
                $checkEleReq = new \XoopsFormRadioYN(_AM_XFORMS_ELE_REQ, 'ele_req', $eleReq);
            }
            $output->addElement($checkEleReq);

            $checkEleDisplayRow = new \XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY_ROW, 'ele_display_row', $displayRow);
            $checkEleDisplayRow->setDescription(_AM_XFORMS_ELE_DISPLAY_ROW_DESC);
            $checkEleDisplayRow->addOption(2, ' ');
            $output->addElement($checkEleDisplayRow);
        } else {
            $textEleCaption = new \XoopsFormText(_AM_XFORMS_ELE_CAPTION, 'ele_caption',50, 255, $element->getVar('ele_caption', 'e'));
            $textEleCaption->setDescription(_AM_XFORMS_ELE_HTML_CAPTION_DESC);
            $output->addElement($textEleCaption);
        }

        $checkEleDisplay = new \XoopsFormRadioYN(_AM_XFORMS_ELE_DISPLAY, 'ele_display', $eleDisplay);
        $output->addElement($checkEleDisplay);
        $orderEleDisp = new FormInput(_AM_XFORMS_ELE_ORDER, 'ele_order', 5, 5, $eleOrder, null, 'number');
        $orderEleDisp->setAttribute('min', 0);
        $orderEleDisp->setExtra('style="width: 5em;"');
        $output->addElement($orderEleDisp);

        $elementName = '';
        $validElements = $xformsEleHandler->getValidElements();
        $validKeys = array_keys($validElements);
        if (in_array($eleType, $validKeys)) {
            $elementName = constant('_AM_XFORMS_ELE_' . strtoupper($eleType));
            include $helper->path('admin/elements/ele_' . $eleType . '.php');
        } else {
            $helper->redirect('admin/index.php',
                                    Constants::REDIRECT_DELAY_MEDIUM,
                                    sprintf(_AM_XFORMS_ERR_BAD_ELEMENT, htmlspecialchars($eleType))
            );
        }

        $output->addElement(new \XoopsFormHidden('op', 'save'));
        $output->addElement(new \XoopsFormHidden('ele_type', $eleType));

        if ((0 === (int)$formId) || (Constants::FORM_CLONED === $clone)) {
            $selectApplyForm = new \XoopsFormSelect(_AM_XFORMS_ELE_APPLY_TO_FORM, 'form_id', $formId);
            $forms           = $formsHandler->getAll(null, null, true, false);
            foreach ($forms as $fObj) {
                $selectApplyForm->addOption($fObj->getVar('form_id'), $fObj->getVar('form_title'));
            }
            $output->addElement($selectApplyForm);
            $output->addElement(new \XoopsFormHidden('clone', Constants::FORM_CLONED));
        } else {
            $output->addElement(new \XoopsFormHidden('form_id', $formId));
        }

        if ((0 !== $eleId) && (Constants::FORM_NOT_CLONED === $clone)) {
            $output->addElement(new \XoopsFormHidden('ele_id', $eleId));
        }
        $tray = new \XoopsFormButtonTray('submit', _SUBMIT, 'submit', null);
        $output->addElement($tray);
        echo '<h4 class="center">' . $elementName . '</h4>';
        $output->display();
        break;

    case 'delete':
        $eleId = (int)$eleId; // fix for Xmf\Request bug in XOOPS < 2.5.9 FINAL
        if (0 === (int)$eleId) {
            $xformsHandler->redirect('admin/main.php',
                                     Constants::REDIRECT_DELAY_NONE,
                                     _AM_XFORMS_NOTHING_SELECTED
            );
        }
        if (empty($_POST['ok'])) {
            $element = $xformsEleHandler->get($eleId);
            xoops_cp_header();
            xoops_confirm(array('op' => 'delete', 'ele_id' => $eleId, 'form_id' => $formId, 'ok' => Constants::CONFIRM_OK), $_SERVER['PHP_SELF'], sprintf(_AM_XFORMS_ELE_CONFIRM_DELETE, $element->getVar('ele_caption')), _YES);
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            //delete the element
            $eleObj = $xformsEleHandler->get($eleId);
            $xformsEleHandler->delete($eleObj);
            //delete the userdata for this element too
            $uDataHandler = $helper::getInstance()->getHandler('UserData');
            //$uDataHandler = $helper->getHandler('UserData');
            $uDataHandler->deleteAll(new \Criteria('ele_id', $eleId));
            redirect_header($helper->url('admin/elements.php?form_id=' . $formId), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        }
        break;

    case 'save':
        //check to make sure this is from known location
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }
        $element = $xformsEleHandler->get($eleId);
        if ($element->isNew()) {
            $eleType = mb_strtolower(\Xmf\Request::getWord('ele_type', 'text', 'POST'));
        } else {
            $eleType = $element->getVar('ele_type');
        }

        $element->setVar('form_id', $formId);
        $element->setVar('ele_caption', $eleCaption);
        $eleReq = (Constants::ELEMENT_NOT_REQD !== $eleReq) ? Constants::ELEMENT_REQD : Constants::ELEMENT_NOT_REQD;
        $element->setVar('ele_req', $eleReq);
        if ('html' !== $eleType) {
            $displayRow = isset($_POST['ele_display_row']) ? Constants::DISPLAY_DOUBLE_ROW : Constants::DISPLAY_SINGLE_ROW;
            $element->setVar('ele_display_row', $displayRow);
        } else {
            // Force text box to be 2 rows
            $element->setVar('ele_display_row', Constants::DISPLAY_DOUBLE_ROW);
        }
//        $order   = empty($ele_order) ? 0 : (int)$eleOrder;
//        $display = (isset($ele_display)) ? 1 : 0;
//        $element->setVar('ele_order', $order);
//        $element->setVar('ele_display', $display);
        $eleDisplay = \Xmf\Request::getInt('ele_display', Constants::ELEMENT_NOT_DISPLAY, 'POST');
        $element->setVar('ele_order', $eleOrder);
        $element->setVar('ele_display', $eleDisplay);
        $element->setVar('ele_type', $eleType);
/* as of PHP 5.4 get_magic_quotes_gpc always returns false so $magicQuotes always eq false
        $magicQuotes = false; // Flag to fix problem with slashes
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            $magicQuotes = true;
        }
*/
        $value = array();

        switch ($eleType) {
            case 'checkbox':
                $checked  = \Xmf\Request::getArray('checked', Constants::ELE_NOT_CHECKED, 'POST');
                $checked  = array_map('intval', $checked);
                foreach($eleValue as $key=>$v) {
                //while ($v = each($eleValue)) {
                    if ('' == $v) { // remove 'empty' options
                        unset($eleValue[$key]);
                    } else {
                        $check = (isset($checked[$key]) && (Constants::ELE_CHECKED == $checked[$key])) ? Constants::ELE_CHECKED : Constants::ELE_NOT_CHECKED;
                        $value[$v] = $check;
                    }
                }
                break;

            /**
             * Color element
             *
             * value [0] = default value
             *       [1] = input box size
             */
            case 'color':
                $currEleValues = $element->getVar('ele_value'); // get current values
                $value[0] = !empty($eleValue[0]) ? $myts->htmlSpecialChars($eleValue[0]) : $currEleValues[0]; // default
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // input box size
                break;

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
            case 'date':
                $currEleValues = $element->getVar('ele_value'); // get current values
                $value[0] = isset($eleValue[0]) ? $eleValue[0] : $currEleValues[0]; // default date
                $value[1] = isset($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // default date option (0 = none, 1 = current, 2 = min date)
                $value[2] = isset($eleValue[2]) ? $eleValue[2] : $currEleValues[2]; // min date
                $value[3] = isset($eleValue[3]) ? (int)$eleValue[3] : $currEleValues[3]; // min date option (0 = none, 1 = current, 2 = min date)
                $value[4] = isset($eleValue[4]) ? $eleValue[4] : $currEleValues[4]; // max date
                $value[5] = isset($eleValue[5]) ? (int)$eleValue[5] : $currEleValues[5]; // max date option (0 = none, 1 = current, 2 = max date)
                break;

            /**
             * Email element
             *
             * value
             *      [0] = element rendered box size
             *      [1] = maximum size length
             *      [2] = default value
             */
            case 'email':
                $value[0] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : 254;
                $value[2] = !empty($eleValue[2]) ? $myts->htmlSpecialChars($eleValue[2]) : '';
                break;

            /**
             * HTML element
             *
             * value array [0] = text value
             */
            case 'html':
                $value[] = $eleValue[0];
                break;

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
            case 'number':
                $currEleValues = $element->getVar('ele_value'); // get current values
                $value[0] = isset($eleValue[0]) ? (int)$eleValue[0] : $currEleValues[0];  // min value
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // max value
                $value[2] = !empty($eleValue[2]) ? (int)$eleValue[2] : $currEleValues[2]; // default value
                $value[3] = !empty($eleValue[3]) ? (int)$eleValue[3] : $currEleValues[3]; // input box size
                $value[4] = !empty($eleValue[4]) ? (int)$eleValue[4] : $currEleValues[4]; // set min value
                $value[5] = !empty($eleValue[5]) ? (int)$eleValue[5] : $currEleValues[5]; // set max value
                $value[6] = !empty($eleValue[6]) ? (int)$eleValue[6] : $currEleValues[6]; // set default value
                $value[7] = !empty($eleValue[7]) ? (int)$eleValue[7] : $currEleValues[7]; // step size
                break;

            /**
             * Obfuscated element
             *
             * value
             *      [0] = element rendered box size
             *      [1] = maximum size length
             */
            case 'obfuscated':
                $value[0] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                break;

            /**
             * Pattern element
             *
             *  value [0] = input box size
             *        [1] = maximum input size
             *        [2] = placeholder
             *        [3] = pattern: use HTML5 pattern to validate input
             *        [4] = pattern description
             */
            case 'pattern':
                $value[0] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                $value[2] = isset($eleValue[2]) ? $myts->htmlSpecialChars($eleValue[2]) : '';
                $value[3] = isset($eleValue[3]) ? $eleValue[3] : '';
                $value[4] = isset($eleValue[4]) ? $myts->htmlSpecialChars($eleValue[4]) : '';
                break;

            case 'radio':
                $checked = \Xmf\Request::getCmd('checked', 0, 'POST');
                foreach ($eleValue as $key=>$v) {
                //while ($v = each($eleValue)) {
                    if ('' == $v) { // remove 'empty' options
                        unset($eleValue[$key]);
                    } else {
                        $newVal = $myts->htmlSpecialChars($myts->addSlashes($v));
                        $value[$newVal] = ($checked == $key) ? Constants::ELE_CHECKED : Constants::ELE_NOT_CHECKED;
                    }
                }
                break;

            /**
             * Range element
             *
             * value [0] = default
             *       [1] = default option (0 = no, 1 = yes)
             *       [2] = min num
             *       [3] = max num
             *       [4] = step
             */
            case 'range':
                $currEleValues = $element->getVar('ele_value'); //get current values
                $value[0] = isset($eleValue[0]) ? (int)$eleValue[0] : $currEleValues[0]; // default
                $value[1] = isset($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // default option (0 = no, 1 = yes)
                $value[2] = isset($eleValue[2]) ? (int)$eleValue[2] : $currEleValues[2]; // min num
                $value[3] = isset($eleValue[3]) ? (int)$eleValue[3] : $currEleValues[3]; // max num
                $value[4] = isset($eleValue[4]) ? (int)$eleValue[4] : $currEleValues[4]; // step
                break;

            /**
             * Select element
             *
             * eleValue array [0] => size,
             *                [1] => allow_multi,
             *                [2] => array (caption => selected)
             */
            case 'select':
                $value[0]    = ($eleValue[0] > 0) ? (int)$eleValue[0] : 1; // size
                $value[1]    = empty($ele_value[1]) ? Constants::DISALLOW_MULTI : Constants::ALLOW_MULTI; // multi-select

                $checked     = \Xmf\Request::getArray('checked', array());
                $tempValue   = array();
                $noneChecked = true;
                foreach ($eleValue[2] as $key => $option) {
                    if (!empty($option)) { // throw out any blank options
                        if (array_key_exists($key, $checked) && $checked[$key] && ($noneChecked || $value[1])) {
                            $noneChecked        = false;
                            $tempValue[$option] = 1;
                        } else {
                            $tempValue[$option] = 0;
                        }
                    }
                }
                $value[2] = $tempValue;
                break;

            /**
             * Country element
             *
             * eleValue [0] = size
             *          [1] = allow multiple
             *          [2] = selected value(s)
             */
            case 'select2':
            case 'country':
                $value[0] = (!empty($eleValue[0]) && ((int)$eleValue[0] > 1)) ? (int)$eleValue[0] : 1;
                $value[1] = !empty($eleValue[1]) ? Constants::ALLOW_MULTI : Constants::DISALLOW_MULTI;
                $value[2] = !empty($eleValue[2]) ? $eleValue[2] : $helper->getConfig('mycountry');
                break;

            /**
             * Text element
             *
             * value [0] = width of text box
             *       [1] = max input size
             *       [2] = default value
             *       [3] = isEmail (0 = no, else = yes)
             *       [4] = placeholder
             */
            case 'text':
                $value[0] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                $value[2] = !empty($eleValue[2]) ? $eleValue[2] : '';
                $value[3] = !empty($eleValue[3]) ? (int)$eleValue[3] : Constants::FIELD_IS_NOT_EMAIL;
                $value[4] = isset($eleValue[4]) ? strip_tags($myts->htmlSpecialChars($eleValue[4])) : '';
                break;

            /**
             * Textarea element
             *
             * value [0] = default value
             *       [1] = number of rows
             *       [2] = number of columns
             *       [3] = placeholder (HTML5)
             */
            case 'textarea':
                $value[0] = $eleValue[0];
                $value[1] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('ta_rows');
                $value[2] = !empty($eleValue[2]) ? (int)$eleValue[2] : $helper->getConfig('ta_cols');
                $value[3] = isset($eleValue[3]) ? strip_tags($myts->htmlSpecialChars($eleValue[3])) : '';
                break;

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
            case 'time':
                $value[] = $eleValue[0]; // min value allowed
                $value[] = $eleValue[1]; // max value allowed
                $value[] = $eleValue[2]; // def value
                $value[] = $eleValue[3]; // step size (60 = 1 min)
                $value[] = $eleValue[4]; // set min value 0|false = no, else = yes
                $value[] = $eleValue[5]; // set max value 0|false = no, else = yes
                $value[] = $eleValue[6]; // set def value 0|false = no, else = yes
                break;

            /**
             * Uploadimg element
             *
             * value [0] = input size
             *       [1] = mime file extensions
             *       [2] = mime types
             *       [3] = save to (mail or directory)
             *       [4] = image width
             *       [5] = image height
             */
            case 'uploadimg':
                $value[4] = (int)$eleValue[4];
                $value[5] = (int)$eleValue[5];
                // intentional fall through (no break) - to set other upload values[]
            /**
             * Upload element
             * value [0] = input size
             *       [1] = mime file extensions
             *       [2] = mime types
             *       [3] = save to (mail or directory)
             */
            case 'upload':
                $value[0] = (int)$eleValue[0];
                $ele1    = trim($eleValue[1], ' |\t\n\r\0\x0B');// normal trim & pipe '|' too
                // get rid of duplicate extensions
                $ele1Array = explode('|', $ele1);
                $ele1Array = array_unique($ele1Array);
                $value[1] = implode('|', $ele1Array);

                $ele2    = trim($eleValue[2], ' |\t\n\r\0\x0B');// normal trim & pipe '|' too
                // get rid of duplicate mime types
                $ele2Array = explode('|', $ele2);
                $ele2Array = array_unique($ele2Array);
                $value[2] = implode('|', $ele2Array);
                $value[3] = (Constants::UPLOAD_SAVEAS_FILE !== (int)$eleValue[3])
                          ? Constants::UPLOAD_SAVEAS_ATTACHMENT
                          : Constants::UPLOAD_SAVEAS_FILE;
                break;

            /**
             * Url element
             *
             * value  [0] = input box size
             *        [1] = maximum input size
             *        [2] = placeholder
             *        [3] = url type: 0 = http[s]|ftp[s], 1 = http[s] only, 2 = ftp[s] only
             */
            case 'url':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                $value[] = isset($eleValue[2]) ? $myts->htmlSpecialChars($eleValue[2]) : '';
                $value[] = isset($eleValue[3]) ? (int)$eleValue[3] : 0;
                break;

            /**
             * RadioYN element
             *
             * value ['_YES'] = 1 is yes, else is no
             */
            case 'yn':
                $value = ('_NO' === $eleValue[0]) ? array('_YES' => 0, '_NO' => 1) : array('_YES' => 1, '_NO' => 0);
                break;
        }
        $element->setVar('ele_value', $value);
        if (!$xformsEleHandler->insert($element)) {
            xoops_cp_header();
            echo $element->getHtmlErrors();
        } else {
            redirect_header($helper->url('admin/elements.php?form_id=' . $formId), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));

        //get the valid element types
        $validEleTypes = $xformsEleHandler->getValidElements();

        $counter = 0;
        $cssClass = '';
        echo '  <table class="outer bspacing1">'
           . '    <thead>'
           . '    <tr><th colspan="2">' . _AM_XFORMS_ELE_CREATE . '</th></tr>'
           . '    </thead>'
           . '    <tbody>';
        foreach ($validEleTypes as $thisType => $thisDesc) {
            if (++$counter % 2) {
                //odd
                $cssClass = ('odd' === $cssClass) ? 'even' : 'odd';
                echo '    <tr><td class="' . $cssClass . ' center"><a href="' . $_SERVER['PHP_SELF'] . '?op=edit&amp;ele_type=' . $thisType . '">' . $thisDesc . '</a></td>';
            } else {
                //even
                echo '<td class="' . $cssClass . ' center"><a href="' . $_SERVER['PHP_SELF'] . '?op=edit&amp;ele_type=' . $thisType . '">' . $thisDesc . '</a></td></tr>';
            }
        }
        if ($counter % 2) { //odd so finish out table row
            echo '<td class="' . $cssClass . ' center">&nbsp;</td></tr>';
        }
        echo '  </tbody>'
           . '  </table>';
        break;
}
include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
