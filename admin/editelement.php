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

use Xmf\Module\Admin;
use Xmf\Module\Helper;
use Xmf\Request;

require_once __DIR__ . '/admin_header.php';
$xformsEleHandler = $helper->getHandler('element');
require_once $helper->path('class/elementrenderer.php');
//require_once __DIR__ . '/../class/elementrenderer.php';
//define('_THIS_PAGE', $helper->url('admin/editelement.php');
$myts = \MyTextSanitizer::getInstance();
if ($xformsFormsHandler->getCount() < 1) {
    redirect_header($GLOBALS['xoops']->url($helper->url('admin/main.php?op=edit')), XformsConstants::REDIRECT_DELAY_NONE, _AM_XFORMS_GO_CREATE_FORM);
}

$submit = Request::getCmd('submit', '', 'POST');

$op = Request::getCmd('op', '');
//$addOpt  = Request::getInt('addopt', 0, 'POST');
//$op      = ((_ADD == $submit)  && ((isset($addOpt)) && ($addOpt > 0))) ? 'edit' : $op;

$clone  = Request::getInt('clone', 0);
$formId = Request::getInt('form_id', 0);
$eleId  = Request::getInt('ele_id', 0);
//$eleType = mb_strtolower(Request::getCmd('ele_type', ''));

$submit     = Request::getString('submit', '', 'POST');
$eleCaption = Request::getText('ele_caption', '', 'POST');
$eleOrder   = Request::getInt('ele_order', 0, 'POST');
$eleValue   = Request::getArray('ele_value', '');
$eleReq     = Request::getInt('ele_req', 0, 'POST');
//$eleDisplay =  Request::getInt('ele_display', 0, 'POST');
//$displayRow = Request::getInt('ele_display_row', 1, 'POST');

switch ($op) {
    case 'edit':
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url("browse.php?modules/{$moduleDirName}/assets/css/style.css"));

        if (!class_exists('XformsFormInput')) {
            require_once $GLOBALS['xoops']->path($xformsHandler->path('class/forminput.php'));
        }

        if (!empty($eleId)) {
            $element     = $xformsEleHandler->get($eleId);
            $eleType     = $element->getVar('ele_type');
            $outputTitle = $clone ? _AM_XFORMS_ELE_CREATE : sprintf(_AM_XFORMS_ELE_EDIT, $element->getVar('ele_caption'));
        } else {
            $element     = $xformsEleHandler->create();
            $eleType     = mb_strtolower(Request::getCmd('ele_type', 'text'));
            $outputTitle = _AM_XFORMS_ELE_CREATE;
        }

        if ('date' === $eleType) { // only load jquery & modernizr if needed
            $GLOBALS['xoTheme']->addStylesheet("browse.php?modules/{$moduleDirName}/assets/css/jquery-ui.min.css");
            $GLOBALS['xoTheme']->addStylesheet("browse.php?modules/{$moduleDirName}/assets/css/jquery-ui.structure.min.css");
            $GLOBALS['xoTheme']->addStylesheet("browse.php?modules/{$moduleDirName}/assets/css/jquery-ui.theme.min.css");
            $GLOBALS['xoTheme']->addScript("browse.php?modules/{$moduleDirName}/assets/js/modernizr-custom.js");
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
        }
        // editor settings
        $sysHelper     = Helper::getHelper('system');
        $editorConfigs = [
            'editor' => $sysHelper->getConfig('general_editor'),
            'rows'   => 10,
            'cols'   => 60,
            'width'  => '100%',
            'height' => '350px',
            'name'   => 'ele_caption',
            'value'  => $element->getVar('ele_caption', 'e')
        ];
        // end editor settings

        $output = new XoopsThemeForm($outputTitle, 'form_ele', $_SERVER['PHP_SELF'], 'post', true);

        $editorConfigs['value'] = $clone ? sprintf(_AM_XFORMS_COPIED, $element->getVar('ele_caption', 'e')) : $element->getVar('ele_caption', 'e');
        $textEleCaption         = new XoopsFormEditor(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $editorConfigs);
        $captionRenderer        = $textEleCaption->editor->renderer;
        if (property_exists($captionRenderer, 'skipPreview')) {
            $textEleCaption->editor->renderer->skipPreview = true;
        }
        $value      = $element->getVar('ele_value', 'f');
        $eleReq     = $element->getVar('ele_req');
        $displayRow = $element->getVar('ele_display_row');
        $eleDisplay = $element->getVar('ele_display');
        $eleOrder   = $element->getVar('ele_order');

        if ('html' !== $eleType) {
            $output->addElement($textEleCaption);

            $checkEleReq = new XoopsFormRadioYN(_AM_XFORMS_ELE_REQ, 'ele_req', $eleReq);
            $output->addElement($checkEleReq);

            $checkEleDisplayRow = new XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY_ROW, 'ele_display_row', $displayRow);
            $checkEleDisplayRow->setDescription(_AM_XFORMS_ELE_DISPLAY_ROW_DESC);
            $checkEleDisplayRow->addOption(2, ' ');
            $output->addElement($checkEleDisplayRow);
        } else {
            $textEleCaption->setDescription(_AM_XFORMS_ELE_HTML_CAPTION_DESC);
            $output->addElement($textEleCaption);
        }

        $checkEleDisplay = new XoopsFormRadioYN(_AM_XFORMS_ELE_DISPLAY, 'ele_display', $eleDisplay);
        $output->addElement($checkEleDisplay);
        $orderEleDisp = new XformsFormInput(_AM_XFORMS_ELE_ORDER, 'ele_order', 5, 5, $eleOrder, null, 'number');
        $orderEleDisp->setAttribute('min', 0);
        $orderEleDisp->setExtra('style="width: 5em;"');
        $output->addElement($orderEleDisp);

        $elementName   = '';
        $validElements = $xformsEleHandler->getValidElements();
        $validKeys     = array_keys($validElements);
        if (in_array($eleType, $validKeys)) {
            $elementName = constant('_AM_XFORMS_ELE_' . strtoupper($eleType));
            include $helper->path("admin/elements/ele_{$eleType}.php");
        } else {
            $helper->redirect('admin/index.php', XformsConstants::REDIRECT_DELAY_MEDIUM, sprintf(_AM_XFORMS_ERR_BAD_ELEMENT, htmlspecialchars($eleType)));
        }

        $output->addElement(new XoopsFormHidden('op', 'save'));
        $output->addElement(new XoopsFormHidden('ele_type', $eleType));

        if (empty($formId) || (true == $clone)) {
            $selectApplyForm = new XoopsFormSelect(_AM_XFORMS_ELE_APPLY_TO_FORM, 'form_id', $formId);
            $forms           = $xformsFormsHandler->getAll(null, null, true, false);
            foreach ($forms as $fObj) {
                $selectApplyForm->addOption($fObj->getVar('form_id'), $fObj->getVar('form_title'));
            }
            $output->addElement($selectApplyForm);
            $output->addElement(new XoopsFormHidden('clone', 1));
        } else {
            $output->addElement(new XoopsFormHidden('form_id', $formId));
        }

        if (!empty($eleId) && !$clone) {
            $output->addElement(new XoopsFormHidden('ele_id', $eleId));
        }
        $tray = new XoopsFormButtonTray('submit', _SUBMIT, 'submit', null);
        $output->addElement($tray);
        echo "<h4 class='center'>{$elementName}</h4>";
        $output->display();
        break;

    case 'delete':
        if (empty($eleId)) {
            $xformsHandler->redirect('admin/main.php', XformsConstants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        if (empty($_POST['ok'])) {
            $element = $xformsEleHandler->get($eleId);
            xoops_cp_header();
            xoops_confirm(['op' => 'delete', 'ele_id' => $eleId, 'form_id' => $formId, 'ok' => 1], $_SERVER['PHP_SELF'], sprintf(_AM_XFORMS_ELE_CONFIRM_DELETE, $element->getVar('ele_caption')), _YES);
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['PHP_SELF'], XformsConstants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            //delete the element
            $eleObj = $xformsEleHandler->get($eleId);
            $xformsEleHandler->delete($eleObj);
            //delete the userdata for this element too
            $uDataHandler = $helper->getHandler('userdata');
            $uDataHandler->deleteAll(new Criteria('ele_id', $eleId));
            redirect_header($helper->url("admin/elements.php?form_id={$formId}"), XformsConstants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        }
        break;

    case 'save':
        //check to make sure this is from known location
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], XformsConstants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }
        $element = $xformsEleHandler->get($eleId);
        if ($element->isNew()) {
            $eleType = mb_strtolower(Request::getWord('ele_type', 'text', 'POST'));
        } else {
            $eleType = $element->getVar('ele_type');
        }

        $element->setVar('form_id', $formId);
        $element->setVar('ele_caption', $eleCaption);
        $eleReq = (!empty($eleReq)) ? XformsConstants::ELEMENT_REQD : XformsConstants::ELEMENT_NOT_REQD;
        $element->setVar('ele_req', $eleReq);
        if ('html' !== $eleType) {
            $displayRow = isset($_POST['ele_display_row']) ? XformsConstants::DISPLAY_DOUBLE_ROW : XformsConstants::DISPLAY_SINGLE_ROW;
            $element->setVar('ele_display_row', $displayRow);
        } else {
            $element->setVar('ele_display_row', 0);
        }
        //        $order   = empty($ele_order) ? 0 : (int)$eleOrder;
        //        $display = (isset($ele_display)) ? 1 : 0;
        //        $element->setVar('ele_order', $order);
        //        $element->setVar('ele_display', $display);
        $eleDisplay = Request::getInt('ele_display', XformsConstants::ELEMENT_NOT_DISPLAY, 'POST');
        $element->setVar('ele_order', $eleOrder);
        $element->setVar('ele_display', $eleDisplay);
        $element->setVar('ele_type', $eleType);
        /* as of PHP 5.4 get_magic_quotes_gpc always returns false so $magicQuotes always eq false
                $magicQuotes = false; // Flag to fix problem with slashes
                if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
                    $magicQuotes = true;
                }
        */
        $value = [];

        switch ($eleType) {
            case 'checkbox':
                $checked = Request::getArray('checked', 0, 'POST');
                $checked = array_map('intval', $checked);
//                while ($v = each($eleValue)) {
                    foreach ($eleValue as $v) {
                        if ('' == $v['value']) { // remove 'empty' options
                            unset($eleValue[$v['key']]);
                        } else {
                            $check              = (isset($checked[$v['key']]) && (1 == $checked[$v['key']])) ? 1 : 0;
                            $value[$v['value']] = $check;
                        }
                    }
                break;

            case 'color':
                $currEleValues = $element->getVar('ele_value'); //get current values
                $value[]       = !empty($eleValue[0]) ? $myts->htmlSpecialChars($eleValue[0]) : $currEleValues[0]; // default
                $value[]       = !empty($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // input box size
                break;

            case 'date':
                $value[] = $eleValue[0]; // default date
                $value[] = $eleValue[1]; // default date option (0 = none, 1 = current, 2 = min date)
                $value[] = $eleValue[2]; // min date
                $value[] = $eleValue[3]; // min date option (0 = none, 1 = current, 2 = min date)
                $value[] = $eleValue[4]; // max date
                $value[] = $eleValue[5]; // max date option (0 = none, 1 = current, 2 = max date)
                break;

            case 'email':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                break;

            case 'html':
                $value[] = $eleValue[0];
                //                $value[] = ($magicQuotes) ? stripslashes($eleValue[0]) : $eleValue[0];
                break;

            case 'number':
                $currEleValues = $element->getVar('ele_value'); //get current values
                $value[]       = isset($eleValue[0]) ? (int)$eleValue[0] : $currEleValues[0];  // min value
                $value[]       = !empty($eleValue[1]) ? (int)$eleValue[1] : $currEleValues[1]; // max value
                $value[]       = !empty($eleValue[2]) ? (int)$eleValue[2] : $currEleValues[2]; // default value
                $value[]       = !empty($eleValue[3]) ? (int)$eleValue[3] : $currEleValues[3]; // input box size
                $value[]       = !empty($eleValue[4]) ? (int)$eleValue[4] : $currEleValues[4]; // set min value
                $value[]       = !empty($eleValue[5]) ? (int)$eleValue[5] : $currEleValues[5]; // set max value
                $value[]       = !empty($eleValue[6]) ? (int)$eleValue[6] : $currEleValues[6]; // set default value
                $value[]       = !empty($eleValue[7]) ? (int)$eleValue[7] : $currEleValues[7]; // step size
                break;

            case 'obfuscated':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                break;

            case 'pattern':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                $value[] = isset($eleValue[2]) ? $myts->htmlSpecialChars($eleValue[2]) : '';
                $value[] = isset($eleValue[3]) ? $eleValue[3] : '';
                $value[] = isset($eleValue[4]) ? $myts->htmlSpecialChars($eleValue[4]) : '';
                break;

            case 'radio':
                $checked = Request::getCmd('checked', 0, 'POST');
//                while ($v = each($eleValue)) {
                foreach ($eleValue as $v) {
                    if ('' == $v['value']) { // remove 'empty' options
                        unset($eleValue[$v['key']]);
                    } else {
                        $newVal         = $myts->htmlSpecialChars($myts->addSlashes($v['value']));
                        $value[$newVal] = ($checked == $v['key']) ? 1 : 0;
                    }
                }
                break;

            case 'range':
                $value[] = $eleValue[0]; // default
                $value[] = $eleValue[1]; // default option (0 = no, 1 = yes)
                $value[] = $eleValue[2]; // min num
                $value[] = $eleValue[3]; // max num
                $value[] = $eleValue[4]; // step
                break;

            case 'select':
                // value [0] = size; [1] = multi-select; [2] = options
                // checked = selected array (0 = not checked, 1 = checked)
                $value[] = ($eleValue[0] > 0) ? (int)$eleValue[0] : 1; // size
                $value[] = empty($ele_value[1]) ? XformsConstants::DISALLOW_MULTI : XformsConstants::ALLOW_MULTI; // multi-select

                $checked     = Request::getArray('checked', []);
                $tempValue   = [];
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

            case 'select2':
            case 'country':
                $value[] = (!empty($eleValue[0]) && ((int)$eleValue[0] > 1)) ? (int)$eleValue[0] : 1;
                $value[] = !empty($eleValue[1]) ? XformsConstants::ALLOW_MULTI : XformsConstants::DISALLOW_MULTI;
                $value[] = !empty($eleValue[2]) ? $eleValue[2] : $helper->getConfig('mycountry');
                break;

            case 'text':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                //                $value[] = ($magicQuotes) ? stripslashes($eleValue[2]) : $eleValue[2];
                $value[] = $eleValue[2];
                $value[] = $eleValue[3];
                $value[] = isset($eleValue[4]) ? strip_tags($myts->htmlSpecialChars($eleValue[4])) : '';
                break;

            case 'textarea':
                //                $value[] = ($magicQuotes) ? stripslashes($eleValue[0]) : $eleValue[0];
                $value[] = $eleValue[0];
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('ta_rows');
                $value[] = !empty($eleValue[2]) ? (int)$eleValue[2] : $helper->getConfig('ta_cols');
                $value[] = isset($eleValue[3]) ? strip_tags($myts->htmlSpecialChars($eleValue[3])) : '';
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

            case 'uploadimg':
                $value[4] = (int)$eleValue[4];
                $value[5] = (int)$eleValue[5];
            // intentional fall through (no break)
            // to set other upload values[]
            // no break
            case 'upload':
                $value[0] = (int)$eleValue[0];
                $ele1     = trim($eleValue[1], ' |\t\n\r\0\x0B');// normal trim & pipe '|' too
                // get rid of duplicate extensions
                $ele1Array = explode('|', $ele1);
                $ele1Array = array_unique($ele1Array);
                $value[1]  = implode('|', $ele1Array);

                $ele2 = trim($eleValue[2], ' |\t\n\r\0\x0B');// normal trim & pipe '|' too
                // get rid of duplicate mime types
                $ele2Array = explode('|', $ele2);
                $ele2Array = array_unique($ele2Array);
                $value[2]  = implode('|', $ele2Array);
                $value[3]  = (1 != $eleValue[3]) ? 0 : 1;
                break;

            case 'url':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $helper->getConfig('t_width');
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $helper->getConfig('t_max');
                $value[] = isset($eleValue[2]) ? $myts->htmlSpecialChars($eleValue[2]) : '';
                $value[] = isset($eleValue[3]) ? (int)$eleValue[3] : 0;
                break;

            case 'yn':
                $value = ('_NO' === $eleValue[0]) ? ['_YES' => 0, '_NO' => 1] : ['_YES' => 1, '_NO' => 0];
                break;
        }
        $element->setVar('ele_value', $value);
        if (!$xformsEleHandler->insert($element)) {
            xoops_cp_header();
            echo $element->getHtmlErrors();
        } else {
            redirect_header($helper->url("admin/elements.php?form_id={$formId}"), XformsConstants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));

        //get the valid element types
        $validEleTypes = $xformsEleHandler->getValidElements();

        $counter  = 0;
        $cssClass = '';
        echo "  <table class='outer bspacing1'>\n" . "    <thead>\n" . "    <tr><th colspan= '2'>" . _AM_XFORMS_ELE_CREATE . "</th></tr>\n" . "    </thead>\n" . "    <tbody>\n";
        foreach ($validEleTypes as $thisType => $thisDesc) {
            if (++$counter % 2) {
                //odd
                $cssClass = ('odd' === $cssClass) ? 'even' : 'odd';
                echo "    <tr><td class='{$cssClass} center'><a href='" . $_SERVER['PHP_SELF'] . "?op=edit&amp;ele_type={$thisType}'>{$thisDesc}</a></td>";
            } else {
                //even
                echo "<td class='{$cssClass} center'><a href='" . $_SERVER['PHP_SELF'] . "?op=edit&amp;ele_type={$thisType}'>{$thisDesc}</a></td></tr>\n";
            }
        }
        if ($counter % 2) { //odd so finish out table row
            echo "<td class='{$cssClass} center'>&nbsp;</td></tr>\n";
        }
        echo "  </tbody>\n" . "  </table>\n";
        break;
}
include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
