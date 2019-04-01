<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */
use \Xmf\Request;

include __DIR__ . '/admin_header.php';
$xformsEleMgr = xoops_getmodulehandler('elements');
include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';
define('_THIS_PAGE', XFORMS_URL . '/admin/editelement.php');
$myts = MyTextSanitizer::getInstance();

if ($xformsFormMgr->getCount() < 1) {
    redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_GO_CREATE_FORM);
}

$op         = Request::getCmd('op', '');
$clone      = Request::getInt('clone', 0);
$formId     = Request::getInt('form_id', 0);
$eleId      = Request::getInt('ele_id', 0);
$eleValue   = Request::getArray('ele_value', '');
$eleCaption = Request::getText('ele_caption', '', 'POST');
$eleOrder   = Request::getInt('ele_order', 0, 'POST');
$eleOrder   = Request::getInt('ele_req', 0, 'POST');
$submit     = Request::getCmd('submit', '', 'POST');
/*
if (count($_POST) > 0) {
    extract($_POST);
} else {
    extract($_GET);
}

$op     = isset($_GET['op']) ? trim($_GET['op']) : '';
$op     = isset($_POST['op']) ? trim($_POST['op']) : $op;
$clone  = isset($_GET['clone']) ? (int)$_GET['clone'] : 0;
$clone  = isset($_POST['clone']) ? trim($_POST['clone']) : $clone;
$formId = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$formId = isset($_POST['form_id']) ? trim($_POST['form_id']) : $formId;
*/
if (isset($_POST['submit']) && $_POST['submit'] == _AM_XFORMS_ELE_ADD_OPT_SUBMIT && (int)$_POST['addopt'] > 0) {
    $op = 'edit';
}

switch ($op) {
    case 'edit':
        xoops_cp_header();

        if (!empty($eleId)) {
            $element     = $xformsEleMgr->get($eleId);
            $eleType     = $element->getVar('ele_type');
            $outputTitle = $clone ? _AM_XFORMS_ELE_CREATE : sprintf(_AM_XFORMS_ELE_EDIT, $element->getVar('ele_caption'));
        } else {
            $element     = $xformsEleMgr->create();
            $eleType     = mb_strtolower(Request::getCmd('ele_type', 'text'));
            $outputTitle = _AM_XFORMS_ELE_CREATE;
        }
        $output = new XoopsThemeForm($outputTitle, 'form_ele', _THIS_PAGE);
        if (empty($addOpt)) {
            $eleCaption                  = $clone ? sprintf(_AM_XFORMS_COPIED, $element->getVar('ele_caption', 'e')) : $element->getVar('ele_caption', 'e');
            $textEleCaption              = new XoopsFormDhtmlTextArea(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $myts->htmlSpecialChars($myts->stripSlashesGPC($eleCaption)), 5, 90);
            $textEleCaption->skipPreview = true;
            $value                       = $element->getVar('ele_value', 'f');
            $req                         = $element->getVar('ele_req');
            $displayRow                  = $element->getVar('ele_display_row');
            $display                     = $element->getVar('ele_display');
            $order                       = $element->getVar('ele_order');
        } else {
            $textEleCaption              = new XoopsFormDhtmlTextArea(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $myts->htmlSpecialChars($myts->stripSlashesGPC($eleCaption)), 5, 90);
            $textEleCaption->skipPreview = true;
            $req                         = isset($_POST['ele_req']) ? 1 : 0;
            $displayRow                  = isset($_POST['ele_display_row']) ? 2 : 1;
            $display                     = isset($_POST['ele_display']) ? 1 : 0;
            $order                       = isset($_POST['ele_order']) ? (int)$_POST['ele_order'] : 0;
        }

        if ($eleType != 'html') {
            $output->addElement($textEleCaption);

            $checkEleReq = new XoopsFormCheckBox(_AM_XFORMS_ELE_REQ, 'ele_req', $req);
            $checkEleReq->addOption(1, ' ');
            $output->addElement($checkEleReq);

            $checkEleDisplayRow = new XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY_ROW, 'ele_display_row', $displayRow);
            $checkEleDisplayRow->setDescription(_AM_XFORMS_ELE_DISPLAY_ROW_DESC);
            $checkEleDisplayRow->addOption(2, ' ');
            $output->addElement($checkEleDisplayRow);
        }

        $checkEleDisplay = new XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY, 'ele_display', $display);
        $checkEleDisplay->addOption(1, ' ');
        $output->addElement($checkEleDisplay);

        $textEleOrder = new XoopsFormText(_AM_XFORMS_ELE_ORDER, 'ele_order', 3, 2, $order);
        $output->addElement($textEleOrder);

        $elementName = "";
        switch ($eleType) {
            case 'text':
            default:
                $elementName = _AM_XFORMS_ELE_TEXT;
                include './elements/ele_text.php';
                break;

            case 'textarea':
                $elementName = _AM_XFORMS_ELE_TAREA;
                include './elements/ele_tarea.php';
                break;

            case 'select':
                $elementName = _AM_XFORMS_ELE_SELECT;
                include './elements/ele_select.php';
                break;
            case 'select2':
                $elementName = _AM_XFORMS_ELE_COUNTRY;
                include './elements/ele_select_ctry.php';
            break;
            case 'date':
                $elementName = _AM_XFORMS_ELE_DATE;
                include './elements/ele_date.php';
                break;
            case 'checkbox':
                $elementName = _AM_XFORMS_ELE_CHECK;
                include './elements/ele_check.php';
                break;

            case 'radio':
                $elementName = _AM_XFORMS_ELE_RADIO;
                include './elements/ele_radio.php';
                break;

            case 'yn':
                $elementName = _AM_XFORMS_ELE_YN;
                include './elements/ele_yn.php';
                break;

            case 'html':
                $elementName = _AM_XFORMS_ELE_HTML;
                include './elementsele_html.php';
                break;

            case 'uploadimg':
                $elementName = _AM_XFORMS_ELE_UPLOADIMG;
                include './elements/ele_uploadimg.php';
                break;

            case 'upload':
                $elementName = _AM_XFORMS_ELE_UPLOADFILE;
                include './elements/ele_upload.php';
                break;
        }

        $hiddenOp   = new XoopsFormHidden('op', 'save');
        $hiddenType = new XoopsFormHidden('ele_type', $eleType);
        $output->addElement($hiddenOp);
        $output->addElement($hiddenType);

        if ($clone == true || empty($formId)) {
            $selectApplyForm = new XoopsFormSelect(_AM_XFORMS_ELE_APPLY_TO_FORM, 'form_id', $formId);
            $forms           = $xformsFormMgr->getObjects(null, 'form_id, form_title');
            foreach ($forms as $f) {
                $selectApplyForm->addOption($f->getVar('form_id'), $f->getVar('form_title'));
            }
            $output->addElement($selectApplyForm);
            $hiddenClone = new XoopsFormHidden('clone', 1);
            $output->addElement($hiddenClone);
        } else {
            $hiddenFormId = new XoopsFormHidden('form_id', $formId);
            $output->addElement($hiddenFormId);
        }

        if (!empty($eleId) && !$clone) {
            $hiddenId = new XoopsFormHidden('ele_id', $eleId);
            $output->addElement($hiddenId);
        }
        $submit = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $cancel = new XoopsFormButton('', 'cancel', _CANCEL, 'button');
        $cancel->setExtra('onclick="javascript:history.go(-1);"');
        $tray = new XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($cancel);
        $output->addElement($tray);
        echo '<h4 style="text-align: center;">' . $elementName . '</h4>';
        $output->display();
        break;
    case 'delete':
        $eleId = (int)$eleId; // fix for Xmf\Request bug in XOOPS < 2.5.9 FINAL

        if (0 === $eleId) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
        }
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            xoops_confirm(array('op' => 'delete', 'ele_id' => $eleId, 'form_id' => $formId, 'ok' => 1), _THIS_PAGE, _AM_XFORMS_ELE_CONFIRM_DELETE);
        } else {
            $element = $xformsEleMgr->get($eleId);
            $xformsEleMgr->delete($element);
            redirect_header(XFORMS_URL . '/admin/elements.php?form_id=' . $formId, 0, _AM_XFORMS_DBUPDATED);
        }
        break;

    case 'save':
        //check to make sure this is from known location
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], 3, implode('<br>', $xoopsSecurity->getErrors()));
        }
        $element = $xformsEleHandler->get($eleId);
        if ($element->isNew()) {
            $eleType = mb_strtolower(Request::getWord('ele_type', 'text', 'POST'));
        } else {
            $eleType = $element->getVar('ele_type');
        }
        /*
        if (!empty($eleId)) {
            $element = $xformsEleMgr->get($eleId);
        } else {
            $element = $xformsEleMgr->create();
        }
        */
        $element->setVar('form_id', $formId);
        $element->setVar('ele_caption', $eleCaption);
        $element->setVar('ele_req', (int)$eleOrder);
        if ($eleType != 'html') {
            $displayRow = (isset($_POST['ele_display_row'])) ? 2 : 1;
            $element->setVar('ele_display_row', $displayRow);
        } else {
            $element->setVar('ele_display_row', 0);
        }
        $order = empty($eleOrder) ? 0 : (int)$eleOrder;
        $element->setVar('ele_order', $order);
        $display = Request::getInt('ele_display', 0, 'POST');
        //$display = (isset($ele_display)) ? 1 : 0;
        $element->setVar('ele_display', $display);
        $element->setVar('ele_type', $eleType);
        $value = array();

        switch ($eleType) {
            case 'text':
                $value[] = !empty($eleValue[0]) ? (int)$eleValue[0] : $xoopsModuleConfig['t_width'];
                $value[] = !empty($eleValue[1]) ? (int)$eleValue[1] : $xoopsModuleConfig['t_max'];
                $value[] = $eleValue[2];
                $value[] = $eleValue[3];

                break;

            case 'textarea':
                $value[] = $eleValue[0];
                if (0 !== (int)$eleValue[1]) {
                    $value[] = (int)$eleValue[1];
                } else {
                    $value[] = $xoopsModuleConfig['ta_rows'];
                }
                if (0 !== (int)$eleValue[2]) {
                    $value[] = (int)$eleValue[2];
                } else {
                    $value[] = $xoopsModuleConfig['ta_cols'];
                }
                break;

            case 'html':
                $value[] = $eleValue[0];
                break;

            case 'select':
                $value[0]   = $eleValue[0] > 1 ? (int)$eleValue[0] : 1;
                $value[1]   = !empty($eleValue[1]) ? 1 : 0;
                $v2         = array();
                $multi_flag = 1;
                while ($v = each($eleValue[2])) {
                    if (!empty($v['value'])) {
                        if ($value[1] == 1 || $multi_flag) {
                            if ($checked[$v['key']] == 1) {
                                $check      = 1;
                                $multi_flag = 0;
                            } else {
                                $check = 0;
                            }
                        } else {
                            $check = 0;
                        }
                        $v2[$v['value']] = $check;
                    }
                }
                $value[2] = $v2;
                break;
            case 'select2':
                $value[0] = $eleValue[0]>1 ? (int)$eleValue[0] : 1;
                $value[1] = !empty($eleValue[1]) ? 1 : 0;
                $value[2] = !empty($eleValue[2]) ? $eleValue[2] : 'LB';
                /*
                $v2 = array();
                $multi_flag = 1;
                while( $v = each($eleValue[2]) ){
                    if( !empty($v['value']) ){
                        if( $value[1] == 1 || $multi_flag ){
                            if( $checked[$v['key']] == 1 ){
                                $check = 1;
                                $multi_flag = 0;
                            }else{
                                $check = 0;
                            }
                        }else{
                            $check = 0;
                        }
                        $v2[$v['value']] = $check;
                    }
                }
                $value[2] = $v2;
                */
            break;
            case 'date':
            $value   = array();
            $value[] = $eleValue;
      break;

            case 'checkbox':
                while ($v = each($eleValue)) {
                    if (!empty($v['value'])) {
                        if ($checked[$v['key']] == 1) {
                            $check = 1;
                        } else {
                            $check = 0;
                        }
                        $value[$v['value']] = $check;
                    }
                }
                break;

            case 'radio':
                while ($v = each($eleValue)) {
                    if (!empty($v['value'])) {
                        if ($checked == $v['key']) {
                            $value[$v['value']] = 1;
                        } else {
                            $value[$v['value']] = 0;
                        }
                    }
                }
                break;

            case 'yn':
                if ($eleValue == '_NO') {
                    $value = array('_YES' => 0, '_NO' => 1);
                } else {
                    $value = array('_YES' => 1, '_NO' => 0);
                }
                break;

            case 'uploadimg':
                $value[] = (int)$eleValue[0];
                $value[] = trim($eleValue[1]);
                $value[] = trim($eleValue[2]);
                $value[] = $eleValue[3] != 1 ? 0 : 1;
                $value[] = (int)$eleValue[4];
                $value[] = (int)$eleValue[5];
                break;

            case 'upload':
                $value[] = (int)$eleValue[0];
                $value[] = trim($eleValue[1]);
                $value[] = trim($eleValue[2]);
                $value[] = $eleValue[3] != 1 ? 0 : 1;
                break;
        }
        $element->setVar('ele_value', $value);
        if (!$xformsEleMgr->insert($element)) {
            xoops_cp_header();
            echo $element->getHtmlErrors();
        } else {
            redirect_header(XFORMS_URL . '/admin/elements.php?form_id=' . $formId, 0, _AM_XFORMS_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('editelement.php');
        echo '<table class="outer" cellspacing="1" width="100%">
                <tr><th>' . _AM_XFORMS_ELE_CREATE . '</th></tr>
                <tr>
                    <td class="odd center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=text">' . _AM_XFORMS_ELE_TEXT . '</a></td>
                </tr>
                <tr>
                    <td class="even center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=textarea">' . _AM_XFORMS_ELE_TAREA . '</a></td>
                </tr>
                <tr>
                    <td class="odd center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=select">' . _AM_XFORMS_ELE_SELECT . '</a></td>
                </tr>
                <tr>
		           <td class="even center"><a href="'._THIS_PAGE.'?op=edit&amp;ele_type=select2">'. _AM_XFORMS_ELE_SELECT_CTRY .'</a></td>
                </tr>
                <tr>
		          <td class="odd center"><a href="'._THIS_PAGE.'?op=edit&amp;ele_type=date">'._AM_XFORMS_ELE_DATE.'</a></td>
                </tr>
                <tr>
                    <td class="even center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=checkbox">' . _AM_XFORMS_ELE_CHECK . '</a></td>
                </tr>
                <tr>
                    <td class="odd center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=radio">' . _AM_XFORMS_ELE_RADIO . '</a></td>
                </tr>
                <tr>
                    <td class="even center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=yn">' . _AM_XFORMS_ELE_YN . '</a></td>
                </tr>
                <tr>
                    <td class="odd center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=html">' . _AM_XFORMS_ELE_HTML . '</a></td>
                </tr>
                <tr>
                    <td class="even center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=uploadimg">' . _AM_XFORMS_ELE_UPLOADIMG . '</a></td>
                </tr>
                <tr>
                    <td class="odd center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=upload">' . _AM_XFORMS_ELE_UPLOADFILE . '</a></td>
                </tr>
            </table>';
        break;
}
include __DIR__ . '/admin_footer.php';
xoops_cp_footer();

/**
 * @param        $id1
 * @param        $id2
 * @param string $text
 * @param string $type
 * @param null   $checked
 *
 * @return XoopsFormElementTray
 */
function addOption($id1, $id2, $text = '', $type = 'check', $checked = null)
{
    $d = new XoopsFormText('', $id1, 40, 255, $text);
    if ('check' == $type) {
        $c = new XoopsFormCheckBox('', $id2, $checked);
        $c->addOption(1, ' ');
    } else {
        $c = new XoopsFormRadio('', 'checked', $checked);
        $c->addOption($id2, ' ');
    }
    $t = new XoopsFormElementTray('');
    $t->addElement($c);
    $t->addElement($d);

    return $t;
}

/**
 * @return XoopsFormElementTray
 */
function addOptionsTray()
{
    $t = new XoopsFormText('', 'addopt', 3, 2);
    $l = new XoopsFormLabel('', sprintf(_AM_XFORMS_ELE_ADD_OPT, $t->render()));
    $b = new XoopsFormButton('', 'submit', _AM_XFORMS_ELE_ADD_OPT_SUBMIT, 'submit');
    $r = new XoopsFormElementTray('');
    $r->addElement($l);
    $r->addElement($b);

    return $r;
}
