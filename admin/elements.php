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
 * @link      https://github.com/XoopsModules/xforms
 */

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\ElementRenderer;
use XoopsModules\Xforms\FormInput;

require __DIR__ . '/admin_header.php';

/**
 * @var string                              $moduleDirName
 * @var \XoopsModules\Xforms\Helper         $helper
 * @var \XoopsModules\Xforms\FormsHandler   $formsHandler
 * @var \XoopsModules\Xforms\ElementHandler $xformsEleHandler
 * @var \Xmf\Module\Admin                   $adminObject
 */
$xformsEleHandler = $helper->getHandler('Element');

$op = Request::getCmd('op', '', 'POST');

switch ($op) {
    default: // list
        $formId = Request::getInt('form_id', Constants::FORM_NOT_VALID, 'GET');
        $formId = (int)$formId; // to fix Request bug in XOOPS < 2.5.9
        if (empty($formId)) {
            $helper->redirect('admin/main.php', Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        /* @var \XoopsModules\Xforms\Forms $form */
        $form = $formsHandler->get($formId);

        xoops_cp_header();
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url('browse.php?modules/' . $moduleDirName . '/assets/css/style.css'));
        /* @var \Xmf\Module\Admin $adminObj */
        $adminObject->displayNavigation('editelement.php');
        $formSelect = new \XoopsFormSelect('', 'ele_type');
        $formSelect->addOptionArray($xformsEleHandler->getValidElements());
        $hiddenOp   = new \XoopsFormHidden('op', 'edit');
        $hiddenId   = new \XoopsFormHidden('form_id', $formId);
        $formButton = new \XoopsFormButton('', 'submit', _ADD, 'submit');

        echo '<div class="center">'
             . '  <form action="'
             . $helper->url('admin/editelement.php')
             . '" method="post">'
             . '    <b>'
             . _AM_XFORMS_ELE_CREATE
             . '</b>:'
             . $formSelect->render()
             . $hiddenOp->render()
             . $hiddenId->render()
             . $formButton->render()
             . '  </form>'
             . '</div>'
             . '<form action="'
             . $_SERVER['SCRIPT_NAME']
             . '" method="post">'
             /** var XoopsSecurity $GLOBALS['xoopsSecurity'] */
             . $GLOBALS['xoopsSecurity']->getTokenHTML()
             . '<table class="outer width100 bspacing1">'
             . '  <thead>'
             . '  <tr><th colspan="7">'             . sprintf(_AM_XFORMS_ELEMENTS_OF_FORM, $form->getVar('form_title'))             . '</th></tr>'
             . '  <tr>'
             . '    <th class="center">' . _AM_XFORMS_ELE_CAPTION  . ' / ' . _AM_XFORMS_ELE_DEFAULT . '</th>'
             . '    <th class="center">' . _AM_XFORMS_ELE_TYPE . '</th>'
             . '    <th class="center width10">' . _AM_XFORMS_ELE_REQ . '</th>'
             . '    <th class="center">'  . _AM_XFORMS_ELE_ORDER . '</th>'
             . '    <th class="center">'  . _AM_XFORMS_ELE_DISPLAY_ROW . '</th>'
             . '    <th class="center width10">' . _AM_XFORMS_ELE_DISPLAY . '</th>'
             . '    <th class="center">' . _AM_XFORMS_ACTION . '</th>'
             . '  </tr>'
             . '  </thead>'
             . '  <tbody>';

        $criteria = new \Criteria('form_id', $formId);
        $criteria->setSort('ele_order ASC, ele_caption');  // trick criteria to allow 2 sort criteria
        $criteria->order = 'ASC';

        /* @var \XoopsModules\Xforms\ElementHandler $xformsEleHandler */
        if ($elements = $xformsEleHandler->getObjects($criteria)) {
            foreach ($elements as $eleObj) {
                $renderer = new ElementRenderer($eleObj);
                $eleValue = $renderer->constructElement(true, $form->getVar('form_delimiter'));
                unset($renderer);

                $id       = $eleObj->getVar('ele_id');
                $dispType = new \XoopsFormLabel('', ucwords($eleObj->getVar('ele_type')));
                $checkReq = new \XoopsFormRadioYN('', 'ele_req[' . $id . ']', $eleObj->getVar('ele_req'));
                $txtOrder = new FormInput('', 'ele_order[' . $id . ']', 5, 5, $eleObj->getVar('ele_order'), null, 'number');
                $txtOrder->setAttribute('min', 0);
                $txtOrder->setExtra('style="width: 5em;"');
                $checkDisp    = new \XoopsFormRadioYN('', 'ele_display[' . $id . ']', $eleObj->getVar('ele_display'));
                $checkDispRow = new \XoopsFormCheckBox('', 'ele_display_row[' . $id . ']', $eleObj->getVar('ele_display_row'));
                $checkDispRow->addOption(2, ' ');
                //                $hidden_id = new \XoopsFormHidden('ele_id[]', $id);
                $myts = \MyTextSanitizer::getInstance();
                echo '  <tr>'
                     . '    <td class="odd">'
                     . $myts->displayTarea($eleObj->getVar('ele_caption'), Constants::ALLOW_HTML)
                     . '</td>'
                     . '    <td class="even center middle" rowspan="2">'
                     . $dispType->render()
                     . '</td>'
                     . '    <td class="even center middle" rowspan="2">'
                     . $checkReq->render()
                     . '</td>'
                     . '    <td class="even center middle" rowspan="2">'
                     . $txtOrder->render()
                     . '</td>'
                     . '    <td class="even center middle" rowspan="2">'
                     . $checkDispRow->render()
                     . '</td>'
                     . '    <td class="even center middle" rowspan="2">'
                     . $checkDisp->render()
                     //replaced $hidden_id->render() so that id's will be unique not true with XoopsFormHidden for arrays
                     . '      <input type="hidden" name="ele_id[]" id="ele_id_'
                     . $id
                     . '" value='
                     . $id
                     . '>'
                     . '    </td>'
                     . '    <td class="even center middle" nowrap="nowrap" rowspan="2">'
                     . '      <a href="'
                     . $helper->url('admin/editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId)
                     . '">'
                     . '<img src="'
                     . Admin::iconUrl('edit.png', '16')
                     . '" class="tooltip floatcenter1" title="'
                     . _EDIT
                     . '"></a>'
                     . '      <a href="'
                     . $helper->url('admin/editelement.php?op=edit&amp;ele_id=' . $id . '&amp;form_id=' . $formId . '&amp;clone=1')
                     . '">'
                     . '<img src="'
                     . Admin::iconUrl('editcopy.png', '16')
                     . '" class="tooltip floatcenter1" title="'
                     . _CLONE
                     . '"></a>'
                     . '      <a href="'
                     . $helper->url('admin/editelement.php?op=delete&amp;ele_id=' . $id . '&amp;form_id=' . $formId)
                     . '">'
                     . '<img src="'
                     . Admin::iconUrl('delete.png', '16')
                     . '" class="tooltip floatcenter1" title="'
                     . _DELETE
                     . '"></a>'
                     . '    </td>'
                     . '  </tr>';

                switch ($eleObj->getVar('ele_type')) {
                    case 'html':
                        echo '  <tr><td class="odd" id="html_' . $id . '">' . $myts->displayTarea($eleValue->render(), Constants::ALLOW_HTML) . '</td></tr>';
                        break;
                    /*
                                        case 'label':
                                            echo '  <tr><td class="odd">&nbsp;</td></tr>';
                                            break;
                    */
                    default:
                        echo '  <tr><td class="odd">' . $eleValue->render() . '</td></tr>';
                        break;
                }
                /*
                                if ('html' !== $eleObj->getVar('ele_type')) {
                                    echo '  <tr><td class="odd">' . $eleValue->render() . '</td></tr>';
                                } else {
                                    echo '  <tr><td class="odd" id="html_' . $id . '">' . $myts->displayTarea($eleValue->render(), Constants::ALLOW_HTML) . '</td></tr>';
                //                    echo '  <tr><td class="odd">' . $myts->displayTarea($eleObj->getVar('ele_value'), Constants::ALLOW_HTML) . '</td></tr>';
                                }
                */
            }
        }

        $submit  = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $submit1 = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_FORM, 'submit');
        $submit2 = new \XoopsFormButton('', 'gotoform', _AM_XFORMS_GOTO_FORM);
        $submit2->setExtra("onclick=\"window.location.href='" . $helper->url('index.php?form_id=' . $formId) . "'\"");
        $submit3 = new \XoopsFormButton('', 'gotoform', _CANCEL);
        $submit3->setExtra("onclick=\"window.location.href='" . $helper->url('admin/main.php') . "'\"");
        $tray = new \XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);
        $tray->addElement($submit2);
        $tray->addElement($submit3);
        echo '  </tbody>' . '  <tfoot>' . '  <tr>' . '    <td class="foot center" colspan="7">' . $tray->render() . '</td>' . '  </tr>' . '  </tfoot>' . '</table>';
        $hiddenOp     = new \XoopsFormHidden('op', 'save');
        $hiddenFormId = new \XoopsFormHidden('form_id', $formId);
        echo $hiddenOp->render() . $hiddenFormId->render() . '</form>';
        break;

    case 'save': // Save element(s)
        // Check to make sure this is from known location
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $formId = Request::getInt('form_id', 0, 'POST');
        $formId = (int)$formId;  // to fix Xmf\Request bug in XOOPS < 2.5.9
        if (empty($formId)) {
            $helper->redirect('admin/main.php', Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }

        $error = '';

        $eleId = Request::getArray('ele_id', [], 'POST');
        $eleId = array_map('\intval', $eleId);

        $eleReq = Request::getArray('ele_req', [], 'POST');
        array_walk($eleReq, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

        $eleOrder = Request::getArray('ele_order', [], 'POST');
        array_walk($eleOrder, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

        $eleDisplay = Request::getArray('ele_display', [], 'POST');
        array_walk($eleDisplay, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

        $eleDisplayRow = Request::getArray('ele_display_row', [], 'POST');
        array_walk($eleDisplayRow, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys

        $eleValue = Request::getArray('ele_value', [], 'POST');

        foreach ($eleId as $id) {
            $element    = $xformsEleHandler->get($id);
            $req        = (array_key_exists($id, $eleReq) && (Constants::ELEMENT_REQD == $eleReq[$id])) ? Constants::ELEMENT_REQD : Constants::ELEMENT_NOT_REQD;
            $order      = (array_key_exists($id, $eleOrder)) ? $eleOrder[$id] : 0;
            $displayRow = (array_key_exists($id, $eleDisplayRow) && (Constants::DISPLAY_DOUBLE_ROW == $eleDisplayRow[$id])) ? Constants::DISPLAY_DOUBLE_ROW : Constants::DISPLAY_SINGLE_ROW;
            $display    = (array_key_exists($id, $eleDisplay) && (Constants::ELEMENT_DISPLAY == $eleDisplay[$id])) ? Constants::ELEMENT_DISPLAY : Constants::ELEMENT_NOT_DISPLAY;
            $type       = $element->getVar('ele_type');
            $value      = $element->getVar('ele_value');
            $element->setVars(
                [
                    'ele_req'         => $req,
                    'ele_order'       => $order,
                    'ele_display_row' => $displayRow,
                    'ele_display'     => $display,
                ]
            );

            switch ($type) {
                case 'checkbox':
                    $newVars  = [];
                    $optCount = 1;
                    if (isset($eleValue[$id]) && is_array($eleValue[$id])) {
                        foreach ($value as $key => $j) {
                            //while ($j = each($value)) {
                            $newVars[$key] = in_array($optCount, $eleValue[$id]) ? 1 : 0;
                            ++$optCount;
                        }
                    } else {
                        if (count($value) > 1) {
                            foreach ($value as $key => $j) {
                                //while ($j = each($value)) {
                                $newVars[$key] = 0;
                            }
                        } else {
                            foreach ($value as $key => $j) {
                                //while ($j = each($value)) {
                                $newVars = !empty($eleValue[$id]) ? [$key => 1] : [$key => 0];
                            }
                        }
                    }
                    $value = $newVars;
                    break;
                case 'color':
                    $value[0] = $eleValue[$id];
                    break;
                case 'country':
                case 'select2':
                    $value[2] = !empty($eleValue[$id]) ? $eleValue[$id] : 'LB';
                    break;
                case 'date':
                    $value[0] = $eleValue[$id];
                    break;
                case 'email':
                    $value[2] = $eleValue[$id];
                    break;
                case 'html':
                    $value[0] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                    $value[0] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
                    $element->setVar('ele_display_row', 0);
                    break;
                case 'number':
                    $value[2] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                    $value[2] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
                    break;
                case 'obfuscated':
                    $value[] = $eleValue[$id];
                    break;
                case 'pattern':
                    $value[] = $eleValue[$id];
                    break;
                case 'radio':
                    $newVars = [];
                    $i       = 1;
                    foreach ($value as $key => $j) {
                        //while ($j = each($value)) {
                        if (null !== $j) {
                            $newVars[$key] = ($eleValue[$id] == $i) ? '1' : '0';
                        }
                        ++$i;
                    }
                    $value = $newVars;
                    break;
                case 'select':
                    $newVars  = [];
                    $optCount = 1;
                    if (isset($eleValue[$id])) {
                        if (is_array($eleValue[$id])) {
                            foreach ($value[2] as $key => $j) {
                                //while ($j = each($value[2])) {
                                $newVars[$key] = in_array($optCount, $eleValue[$id]) ? 1 : 0;
                                ++$optCount;
                            }
                        } else {
                            if (count($value[2]) > 1) {
                                foreach ($value[2] as $key => $j) {
                                    //while ($j = each($value[2])) {
                                    $newVars[$key] = ($optCount == $eleValue[$id]) ? 1 : 0;
                                    ++$optCount;
                                }
                            } else {
                                foreach ($value[2] as $key => $j) {
                                    //while ($j = each($value[2])) {
                                    $newVars = empty($eleValue[$id]) ? [$key => 0] : [$key => 1];
                                }
                            }
                        }
                        $value[2] = $newVars;
                    } else {
                        foreach ($value[2] as $k => $v) {
                            $value[2][$k] = 0;
                        }
                    }
                    break;
                case 'text':
                    $value[2] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                     $value[2] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
                    break;
                case 'textarea':
                    $value[0] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                $value[0] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
                    break;
                case 'time':
                    $value[2] = $eleValue[$id];
                    break;
                case 'upload':
                    $value[0] = (int)$eleValue[$id][0];
                    break;
                case 'uploadimg':
                    $value[0] = (int)$eleValue[$id][0];
                    $value[4] = (int)$eleValue[$id][4];
                    $value[5] = (int)$eleValue[$id][5];
                    break;
                case 'url':
                    $value[] = $eleValue[$id];
                    break;
                case 'yn':
                    $newVars = [];
                    $i       = 1;
                    foreach ($value as $key => $j) {
                        //while ($j = each($value)) {
                        if (null !== $j) {
                            $newVars[$key] = ($eleValue[$id] == $i) ? '1' : '0';
                        }
                        ++$i;
                    }
                    $value = $newVars;
                    break;
                default:
                    break;
            }
            $element->setVar('ele_value', $value, true);
            if (!$xformsEleHandler->insert($element)) {
                $error .= $element->getHtmlErrors();
            }
        }
        if (empty($error)) {
            if (_AM_XFORMS_SAVE_THEN_FORM == $_POST['submit']) {
                $helper->redirect('admin/main.php?op=edit&form_id=' . $formId, Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                //redirect_header($GLOBALS['xoops']->buildUrl('/modules/' . $moduleDirName . '/admin/main.php', array('op' => 'edit', 'form_id' => $formId)), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'] . '?form_id=' . $formId, Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            }
        } else {
            xoops_cp_header();
            echo $error;
        }
}

require __DIR__ . '/admin_footer.php';
