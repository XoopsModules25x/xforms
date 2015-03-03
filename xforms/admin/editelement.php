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

include __DIR__ . '/admin_header.php';
$xforms_ele_mgr = xoops_getmodulehandler('elements');
include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';
define('_THIS_PAGE', XFORMS_URL . '/admin/editelement.php');
$myts = MyTextSanitizer::getInstance();
if ($xforms_form_mgr->getCount() < 1) {
    redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_GO_CREATE_FORM);
}

if (count($_POST) > 0) {
    extract($_POST);
} else {
    extract($_GET);
}

$op      = isset($_GET['op']) ? trim($_GET['op']) : '';
$op      = isset($_POST['op']) ? trim($_POST['op']) : $op;
$clone   = isset($_GET['clone']) ? intval($_GET['clone'], 10) : 0;
$clone   = isset($_POST['clone']) ? trim($_POST['clone']) : $clone;
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id'], 10) : 0;
$form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : $form_id;

if (isset($_POST['submit']) && $_POST['submit'] == _AM_XFORMS_ELE_ADD_OPT_SUBMIT && intval($_POST['addopt'], 10) > 0) {
    $op = 'edit';
}

switch ($op) {
    case 'edit':
        xoops_cp_header();

        if (!empty($ele_id)) {
            $element      = $xforms_ele_mgr->get($ele_id);
            $ele_type     = $element->getVar('ele_type');
            $output_title = $clone ? _AM_XFORMS_ELE_CREATE : sprintf(_AM_XFORMS_ELE_EDIT, $element->getVar('ele_caption'));
        } else {
            $element      = $xforms_ele_mgr->create();
            $output_title = _AM_XFORMS_ELE_CREATE;
        }
        $output = new XoopsThemeForm($output_title, 'form_ele', _THIS_PAGE);
        if (empty($addopt)) {
            $ele_caption                   = $clone ? sprintf(_AM_XFORMS_COPIED, $element->getVar('ele_caption', 'e')) : $element->getVar('ele_caption', 'e');
            $text_ele_caption              = new XoopsFormDhtmlTextArea(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $myts->htmlspecialchars($myts->stripSlashesGPC($ele_caption)), 5, 90);
            $text_ele_caption->skipPreview = true;
            $value                         = $element->getVar('ele_value', 'f');
            $req                           = $element->getVar('ele_req');
            $display_row                   = $element->getVar('ele_display_row');
            $display                       = $element->getVar('ele_display');
            $order                         = $element->getVar('ele_order');
        } else {
            $text_ele_caption              = new XoopsFormDhtmlTextArea(_AM_XFORMS_ELE_CAPTION, 'ele_caption', $myts->htmlspecialchars($myts->stripSlashesGPC($ele_caption)), 5, 90);
            $text_ele_caption->skipPreview = true;
            $req                           = isset($_POST['ele_req']) ? 1 : 0;
            $display_row                   = isset($_POST['ele_display_row']) ? 2 : 1;
            $display                       = isset($_POST['ele_display']) ? 1 : 0;
            $order                         = isset($_POST['ele_order']) ? intval($_POST['ele_order'], 10) : 0;
        }

        if ($ele_type != 'html') {
            $output->addElement($text_ele_caption);

            $check_ele_req = new XoopsFormCheckBox(_AM_XFORMS_ELE_REQ, 'ele_req', $req);
            $check_ele_req->addOption(1, ' ');
            $output->addElement($check_ele_req);

            $check_ele_display_row = new XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY_ROW, 'ele_display_row', $display_row);
            $check_ele_display_row->setDescription(_AM_XFORMS_ELE_DISPLAY_ROW_DESC);
            $check_ele_display_row->addOption(2, ' ');
            $output->addElement($check_ele_display_row);
        }

        $check_ele_display = new XoopsFormCheckBox(_AM_XFORMS_ELE_DISPLAY, 'ele_display', $display);
        $check_ele_display->addOption(1, ' ');
        $output->addElement($check_ele_display);

        $text_ele_order = new XoopsFormText(_AM_XFORMS_ELE_ORDER, 'ele_order', 3, 2, $order);
        $output->addElement($text_ele_order);

        $elementName = "";
        switch ($ele_type) {
            case 'text':
            default:
                $elementName = _AM_XFORMS_ELE_TEXT;
                include 'ele_text.php';
                break;

            case 'textarea':
                $elementName = _AM_XFORMS_ELE_TAREA;
                include 'ele_tarea.php';
                break;

            case 'select':
                $elementName = _AM_XFORMS_ELE_SELECT;
                include 'ele_select.php';
                break;
            case 'select2':
                $elementName = _AM_XFORMS_ELE_COUNTRY;
                include 'ele_select_ctry.php';
            break;
            case 'date':
                $elementName = _AM_XFORMS_ELE_DATE;
                include 'ele_date.php';
            break;
            case 'checkbox':
                $elementName = _AM_XFORMS_ELE_CHECK;
                include 'ele_check.php';
                break;

            case 'radio':
                $elementName = _AM_XFORMS_ELE_RADIO;
                include 'ele_radio.php';
                break;

            case 'yn':
                $elementName = _AM_XFORMS_ELE_YN;
                include 'ele_yn.php';
                break;

            case 'html':
                $elementName = _AM_XFORMS_ELE_HTML;
                include 'ele_html.php';
                break;

            case 'uploadimg':
                $elementName = _AM_XFORMS_ELE_UPLOADIMG;
                include 'ele_uploadimg.php';
                break;

            case 'upload':
                $elementName = _AM_XFORMS_ELE_UPLOADFILE;
                include 'ele_upload.php';
                break;
        }

        $hidden_op   = new XoopsFormHidden('op', 'save');
        $hidden_type = new XoopsFormHidden('ele_type', $ele_type);
        $output->addElement($hidden_op);
        $output->addElement($hidden_type);

        if ($clone == true || empty($form_id)) {
            $select_apply_form = new XoopsFormSelect(_AM_XFORMS_ELE_APPLY_TO_FORM, 'form_id', $form_id);
            $forms             = $xforms_form_mgr->getObjects(null, 'form_id, form_title');
            foreach ($forms as $f) {
                $select_apply_form->addOption($f->getVar('form_id'), $f->getVar('form_title'));
            }
            $output->addElement($select_apply_form);
            $hidden_clone = new XoopsFormHidden('clone', 1);
            $output->addElement($hidden_clone);
        } else {
            $hidden_form_id = new XoopsFormHidden('form_id', $form_id);
            $output->addElement($hidden_form_id);
        }

        if (!empty($ele_id) && !$clone) {
            $hidden_id = new XoopsFormHidden('ele_id', $ele_id);
            $output->addElement($hidden_id);
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
        if (empty($ele_id)) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
        }
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            xoops_confirm(array('op' => 'delete', 'ele_id' => $ele_id, 'form_id' => $form_id, 'ok' => 1), _THIS_PAGE, _AM_XFORMS_ELE_CONFIRM_DELETE);
        } else {
            $element = $xforms_ele_mgr->get($ele_id);
            $xforms_ele_mgr->delete($element);
            redirect_header(XFORMS_URL . '/admin/elements.php?form_id=' . $form_id, 0, _AM_XFORMS_DBUPDATED);
        }
        break;

    case 'save':
        if (!empty($ele_id)) {
            $element = $xforms_ele_mgr->get($ele_id);
        } else {
            $element = $xforms_ele_mgr->create();
        }
        $element->setVar('form_id', $form_id);
        $element->setVar('ele_caption', $ele_caption);
        $req = (isset($ele_req)) ? 1 : 0;
        $element->setVar('ele_req', $req);
        if ($ele_type != 'html') {
            $display_row = (isset($ele_display_row)) ? 2 : 1;
            $element->setVar('ele_display_row', $display_row);
        } else {
            $element->setVar('ele_display_row', 0);
        }
        $order = empty($ele_order) ? 0 : intval($ele_order, 10);
        $element->setVar('ele_order', $order);
        $display = (isset($ele_display)) ? 1 : 0;
        $element->setVar('ele_display', $display);
        $element->setVar('ele_type', $ele_type);
        $value = array();

        $magicQuotes = false; // Flag to fix problem with slashes
        if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
            $magicQuotes = true;
        }
        switch ($ele_type) {
            case 'text':
                $value[] = !empty($ele_value[0]) ? intval($ele_value[0], 10) : $xoopsModuleConfig['t_width'];
                $value[] = !empty($ele_value[1]) ? intval($ele_value[1], 10) : $xoopsModuleConfig['t_max'];
                $value[] = ($magicQuotes) ? stripslashes($ele_value[2]) : $ele_value[2];
                $value[] = $ele_value[3];

                break;

            case 'textarea':
                $value[] = ($magicQuotes) ? stripslashes($ele_value[0]) : $ele_value[0];
                if (intval($ele_value[1], 10) != 0) {
                    $value[] = intval($ele_value[1], 10);
                } else {
                    $value[] = $xoopsModuleConfig['ta_rows'];
                }
                if (intval($ele_value[2], 10) != 0) {
                    $value[] = intval($ele_value[2], 10);
                } else {
                    $value[] = $xoopsModuleConfig['ta_cols'];
                }
                break;

            case 'html':
                $value[] = ($magicQuotes) ? stripslashes($ele_value[0]) : $ele_value[0];
                break;

            case 'select':
                $value[0]   = $ele_value[0] > 1 ? intval($ele_value[0], 10) : 1;
                $value[1]   = !empty($ele_value[1]) ? 1 : 0;
                $v2         = array();
                $multi_flag = 1;
                while ($v = each($ele_value[2])) {
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
                $value[0] = $ele_value[0]>1 ? intval($ele_value[0]) : 1;
                $value[1] = !empty($ele_value[1]) ? 1 : 0;
                $value[2] = !empty($ele_value[2]) ? $ele_value[2] : 'LB';
                /*
                $v2 = array();
                $multi_flag = 1;
                while( $v = each($ele_value[2]) ){
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
            $value = array();
            $value[] = $ele_value;
      break;

            case 'checkbox':
                while ($v = each($ele_value)) {
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
                while ($v = each($ele_value)) {
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
                if ($ele_value == '_NO') {
                    $value = array('_YES' => 0, '_NO' => 1);
                } else {
                    $value = array('_YES' => 1, '_NO' => 0);
                }
                break;

            case 'uploadimg':
                $value[] = intval($ele_value[0], 10);
                $value[] = trim($ele_value[1]);
                $value[] = trim($ele_value[2]);
                $value[] = $ele_value[3] != 1 ? 0 : 1;
                $value[] = intval($ele_value[4], 10);
                $value[] = intval($ele_value[5], 10);
                break;

            case 'upload':
                $value[] = intval($ele_value[0], 10);
                $value[] = trim($ele_value[1]);
                $value[] = trim($ele_value[2]);
                $value[] = $ele_value[3] != 1 ? 0 : 1;
                break;
        }
        $element->setVar('ele_value', $value);
        if (!$xforms_ele_mgr->insert($element)) {
            xoops_cp_header();
            echo $element->getHtmlErrors();
        } else {
            redirect_header(XFORMS_URL . '/admin/elements.php?form_id=' . $form_id, 0, _AM_XFORMS_DBUPDATED);
        }
        break;

    default:
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('editelement.php');
        echo '<table class="outer" cellspacing="1" width="100%">
                <tr><th>' . _AM_XFORMS_ELE_CREATE . '</th></tr>
                <tr>
                    <td class="odd" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=text">' . _AM_XFORMS_ELE_TEXT . '</a></td>
                </tr>
                <tr>
                    <td class="even" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=textarea">' . _AM_XFORMS_ELE_TAREA . '</a></td>
                </tr>
                <tr>
                    <td class="odd" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=select">' . _AM_XFORMS_ELE_SELECT . '</a></td>
                </tr>
                <tr>
		           <td class="even" align="center"><a href="'._THIS_PAGE.'?op=edit&amp;ele_type=select2">'. _AM_XFORMS_ELE_SELECT_CTRY .'</a></td>
                </tr>
                <tr>
		          <td class="odd" align="center"><a href="'._THIS_PAGE.'?op=edit&amp;ele_type=date">'._AM_XFORMS_ELE_DATE.'</a></td>
                </tr>
                <tr>
                    <td class="even" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=checkbox">' . _AM_XFORMS_ELE_CHECK . '</a></td>
                </tr>
                <tr>
                    <td class="odd" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=radio">' . _AM_XFORMS_ELE_RADIO . '</a></td>
                </tr>
                <tr>
                    <td class="even" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=yn">' . _AM_XFORMS_ELE_YN . '</a></td>
                </tr>
                <tr>
                    <td class="odd" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=html">' . _AM_XFORMS_ELE_HTML . '</a></td>
                </tr>
                <tr>
                    <td class="even" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=uploadimg">' . _AM_XFORMS_ELE_UPLOADIMG . '</a></td>
                </tr>
                <tr>
                    <td class="odd" align="center"><a href="' . _THIS_PAGE . '?op=edit&amp;ele_type=upload">' . _AM_XFORMS_ELE_UPLOADFILE . '</a></td>
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
    if ($type == 'check') {
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
