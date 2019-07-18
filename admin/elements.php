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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License

 * @since           1.30
 */

use Xmf\Request;
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';
$xformsEleHandler = $helper->getHandler('Element');
//require_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';

$op = Request::getString('op', '', 'POST');

switch ($op) {
    default: // list
        $formId = Request::getInt('form_id', 0, 'GET');
        $formId = $formId;
        if (empty($formId)) {
            $helper->redirect('admin/main.php', Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        $form = $xformsFormsHandler->get($formId);

        xoops_cp_header();
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url("browse.php?modules/{$moduleDirName}/assets/css/style.css"));
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('editelement.php');

        $jump    = [];
        $jump[0] = new \XoopsFormSelect('', 'ele_type');
        $jump[0]->addOptionArray($xformsEleHandler->getValidElements());
        $jump[1] = new \XoopsFormHidden('op', 'edit');
        $jump[2] = new \XoopsFormHidden('form_id', $formId);
        $jump[3] = new \XoopsFormButton('', 'submit', _ADD, 'submit');
        echo "<div class='center'>\n" . "  <form action='" . $helper->url('admin/editelement.php') . "' method='post'>\n" . '    <b>' . _AM_XFORMS_ELE_CREATE . "</b>:\n";
        foreach ($jump as $j) {
            echo "\n    " . $j->render();
        }
        echo "  </form>\n"
             . "</div>\n"
             . "<form action='"
             . $_SERVER['PHP_SELF']
             . "' method='post'>\n"
             . $xoopsSecurity->getTokenHTML()
             . "\n"
             . "<table class='outer width100 bspacing1'>\n"
             . "  <thead>\n"
             . "  <tr><th colspan='7'>"
             . sprintf(_AM_XFORMS_ELEMENTS_OF_FORM, $form->getVar('form_title'))
             . "</th></tr>\n"
             . "  <tr>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_CAPTION
             . ' / '
             . _AM_XFORMS_ELE_DEFAULT
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_TYPE
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_REQ
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_ORDER
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_DISPLAY_ROW
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ELE_DISPLAY
             . "</th>\n"
             . "    <th class='head center'>"
             . _AM_XFORMS_ACTION
             . "</th>\n"
             . "  </tr>\n"
             . "  </thead>\n"
             . "  <tbody>\n";

        $criteria = new \Criteria('form_id', $formId);
        $criteria->setSort('ele_order ASC, ele_caption');  // trick criteria to allow 2 sort criteria
        $criteria->setOrder('ASC');

        if ($elements = $xformsEleHandler->getObjects($criteria)) {
            foreach ($elements as $eleObj) {
                $renderer = new Xforms\ElementRenderer($eleObj);
                $eleValue = $renderer->constructElement(true, $form->getVar('form_delimiter'));
                //                unset($renderer);

                $id       = $eleObj->getVar('ele_id');
                $dispType = new \XoopsFormLabel('', ucwords($eleObj->getVar('ele_type')));
                $checkReq = new \XoopsFormRadioYN('', "ele_req[{$id}]", $eleObj->getVar('ele_req'));
                $txtOrder = new Xforms\FormInput('', "ele_order[{$id}]", 5, 5, $eleObj->getVar('ele_order'), null, 'number');
                $txtOrder->setAttribute('min', 0);
                $txtOrder->setExtra('style="width: 5em;"');
                $checkDisp    = new \XoopsFormRadioYN('', "ele_display[{$id}]", $eleObj->getVar('ele_display'));
                $checkDispRow = new \XoopsFormCheckBox('', "ele_display_row[{$id}]", $eleObj->getVar('ele_display_row'));
                $checkDispRow->addOption(2, ' ');
                //                $hidden_id = new \XoopsFormHidden('ele_id[]', $id);
                $myts = \MyTextSanitizer::getInstance();
                echo "  <tr>\n"
                     . "    <td class='odd'>"
                     . $myts->displayTarea($eleObj->getVar('ele_caption'), Constants::ALLOW_HTML)
                     . "</td>\n"
                     . "    <td class='even center middle' rowspan='2'>"
                     . $dispType->render()
                     . "</td>\n"
                     . "    <td class='even center middle' rowspan='2'>"
                     . $checkReq->render()
                     . "</td>\n"
                     . "    <td class='even center middle' rowspan='2'>"
                     . $txtOrder->render()
                     . "</td>\n"
                     . "    <td class='even center middle' rowspan='2'>"
                     . $checkDispRow->render()
                     . "</td>\n"
                     . "    <td class='even center middle' rowspan='2'>"
                     . $checkDisp->render()
                     . "\n"
                     //replaced $hidden_id->render() so that id's will be unique not true with XoopsFormHidden for arrays
                     . "      <input type='hidden' name='ele_id[]' id='ele_id_{$id}' value='{$id}'>\n"
                     . "    </td>\n"
                     . "    <td class='even center middle' nowrap='nowrap' rowspan='2'>\n"
                     . "      <a href='"
                     . $helper->url("admin/editelement.php?op=delete&amp;ele_id={$id}&amp;form_id={$formId}")
                     . "'><img src='{$pathIcon16}/delete.png' class='tooltip floatcenter1' title='"
                     . _DELETE
                     . "'></a>\n"
                     . "      <a href='"
                     . $helper->url("admin/editelement.php?op=edit&amp;ele_id={$id}&amp;form_id={$formId}&amp;clone=1")
                     . "'><img src='{$pathIcon16}/editcopy.png' class='tooltip floatcenter1' title='"
                     . _CLONE
                     . "'></a>\n"
                     . "      <a href='"
                     . $helper->url("admin/editelement.php?op=edit&amp;ele_id={$id}&amp;form_id={$formId}")
                     . "'><img src='{$pathIcon16}/edit.png' class='tooltip floatcenter1' title='"
                     . _EDIT
                     . "'></a>\n"
                     . "    </td>\n"
                     . "  </tr>\n";

                switch ($eleObj->getVar('ele_type')) {
                    case 'html':
                        echo "  <tr><td class='odd' id='html_{$id}'>" . $myts->displayTarea($eleValue->render(), Constants::ALLOW_HTML) . "</td></tr>\n";
                        break;
                    /*
                                        case 'label':
                                            echo "  <tr><td class='odd'>&nbsp;</td></tr>\n";
                                            break;
                    */
                    default:
                        echo "  <tr><td class='odd'>" . $eleValue->render() . "</td></tr>\n";
                        break;
                }
                /*
                                if ("html" != $eleObj->getVar('ele_type')) {
                                    echo "  <tr><td class='odd'>" . $eleValue->render() . "</td></tr>\n";
                                } else {
                                    echo "  <tr><td class='odd' id='html_{$id}'>" . $myts->displayTarea($eleValue->render(), Constants::ALLOW_HTML) . "</td></tr>\n";
                //                    echo "  <tr><td class='odd'>" . $myts->displayTarea($eleObj->getVar('ele_value'), Constants::ALLOW_HTML) . "</td></tr>\n";
                                }
                */
            }
        }

        $submit  = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $submit1 = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_FORM, 'submit');
        $submit2 = new \XoopsFormButton('', 'gotoform', _AM_XFORMS_GOTO_FORM);
        $submit2->setExtra("onclick=\"window.location.href='" . $helper->url("index.php?form_id={$formId}") . "'\"");
        $tray = new \XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);
        $tray->addElement($submit2);
        echo "  </tbody>\n" . "  <tfoot>\n" . "  <tr>\n" . "    <td class='foot center' colspan='7'>" . $tray->render() . "</td>\n" . "  </tr>\n" . "  </tfoot>\n" . "</table>\n";
        $hiddenOp     = new \XoopsFormHidden('op', 'save');
        $hiddenFormId = new \XoopsFormHidden('form_id', $formId);
        echo $hiddenOp->render() . "\n" . $hiddenFormId->render() . "\n" . "</form>\n";
        break;
    case 'save': // Save element(s)
        //check to make sure this is from known location
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }

        $formId = Request::getInt('form_id', 0, 'POST');
        $formId = $formId;
        if (empty($formId)) {
            $helper->redirect('admin/main.php', Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }

        $error = '';

        $eleId = Request::getArray('ele_id', [], 'POST');
        $eleId = array_map('intval', $eleId);

        $eleReq = Request::getArray('ele_req', [], 'POST');
        array_walk($eleReq, 'xformsIntArray'); // can't use array_map since must preserve keys

        $eleOrder = Request::getArray('ele_order', [], 'POST');
        array_walk($eleOrder, 'xformsIntArray'); // can't use array_map since must preserve keys

        $eleDisplay = Request::getArray('ele_display', [], 'POST');
        array_walk($eleDisplay, 'xformsIntArray'); // can't use array_map since must preserve keys

        $eleDisplayRow = Request::getArray('ele_display_row', [], 'POST');
        array_walk($eleDisplayRow, 'xformsIntArray'); // can't use array_map since must preserve keys

        $eleValue = Request::getArray('ele_value', [], 'POST');

        foreach ($eleId as $id) {
            $element    = $xformsEleHandler->get($id);
            $req        = (array_key_exists($id, $eleReq)
                           && (Constants::ELEMENT_REQD == $eleReq[$id])) ? Constants::ELEMENT_REQD : Constants::ELEMENT_NOT_REQD;
            $order      = array_key_exists($id, $eleOrder) ? $eleOrder[$id] : 0;
            $displayRow = (array_key_exists($id, $eleDisplayRow)
                           && (Constants::DISPLAY_DOUBLE_ROW == $eleDisplayRow[$id])) ? Constants::DISPLAY_DOUBLE_ROW : Constants::DISPLAY_SINGLE_ROW;
            $display    = (array_key_exists($id, $eleDisplay)
                           && (Constants::ELEMENT_DISPLAY == $eleDisplay[$id])) ? Constants::ELEMENT_DISPLAY : Constants::ELEMENT_NOT_DISPLAY;
            $type       = $element->getVar('ele_type');
            $value      = $element->getVar('ele_value');
            $element->setVars([
                                  'ele_req'         => $req,
                                  'ele_order'       => $order,
                                  'ele_display_row' => $displayRow,
                                  'ele_display'     => $display,
                              ]);

            switch ($type) {
                case 'checkbox':
                    $newVars  = [];
                    $optCount = 1;
                    if (isset($eleValue[$id]) && is_array($eleValue[$id])) {
                        //                        while ($j = each($value)) {
                        foreach ($value as $j) {
                            $newVars[$j['key']] = in_array($optCount, $eleValue[$id]) ? 1 : 0;
                            ++$optCount;
                        }
                    } else {
                        if (count($value) > 1) {
                            //                            while ($j = each($value)) {
                            foreach ($value as $j) {
                                $newVars[$j['key']] = 0;
                            }
                        } else {
                            //                            while ($j = each($value)) {
                            foreach ($value as $j) {
                                $newVars = !empty($eleValue[$id]) ? [$j['key'] => 1] : [$j['key'] => 0];
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
                    $value = [$eleValue[$id]];
                    break;
                case 'email':
                    $value[] = $eleValue[$id];
                    break;
                case 'html':
                    $value[0] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                $value[0] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
                    $element->setVar('ele_display_row', 0);
                    break;
                case 'number':
                    //                $value[2] = $eleValue[$id];
                    $value[] = $eleValue[$id];
                    // removed in v2.00 ALPHA 2 - as of PHP5.4 get_magic_quotes_gpc() always returns FALSE
                    //                 $value[2] = ($magicQuotes) ? stripslashes($eleValue[$id]) : $eleValue[$id];
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
                    //                    while ($j = each($value)) {
                    foreach ($value as $j) {
                        if (null !== $j['value']) {
                            $newVars[$j['key']] = ($eleValue[$id] == $i) ? '1' : '0';
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
                            // while ($j = each($value[2])) {
                            foreach ($value[2] as $j) {
                                $newVars[$j['key']] = in_array($optCount, $eleValue[$id]) ? 1 : 0;
                                ++$optCount;
                            }
                        } else {
                            if (count($value[2]) > 1) {
                                //                            while ($j = each($value[2])) {
                                foreach ($value[2] as $j) {
                                    $newVars[$j['key']] = ($optCount == $eleValue[$id]) ? 1 : 0;
                                    ++$optCount;
                                }
                            } else {
                                // while ($j = each($value[2])) {
                                foreach ($value[2] as $j) {
                                    $newVars = !empty($eleValue[$id]) ? [$j['key'] => 1] : [$j['key'] => 0];
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
                    // while ($j = each($value)) {
                    foreach ($value as $j) {
                        if (null !== $j['value']) {
                            $newVars[$j['key']] = ($eleValue[$id] == $i) ? '1' : '0';
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
                redirect_header($GLOBALS['xoops']->buildUrl("/modules/{$moduleDirName}/admin/main.php", ['op' => 'edit', 'form_id' => $formId]), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            } else {
                redirect_header($_SERVER['PHP_SELF'] . "?form_id={$formId}", Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            }
        } else {
            xoops_cp_header();
            echo $error;
        }
}

require_once __DIR__ . '/admin_footer.php';
xoops_cp_footer();
