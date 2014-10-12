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
$myts = MyTextSanitizer::getInstance();
$op   = isset($_GET['op']) ? trim($_GET['op']) : 'list';
$op   = isset($_POST['op']) ? trim($_POST['op']) : $op;

$showAll   = isset($_POST['showall']) ? true : false;
$saveOrder = isset($_POST['saveorder']) ? true : false;
if ($showAll) {
    $op = 'list';
} elseif ($saveOrder) {
    $op = 'saveorder';
}

switch ($op) {
    case 'list':
    default:
        xoops_cp_header();
        echo '<form action="' . XFORMS_ADMIN_URL . '" method="post">
            <table class="outer" cellspacing="1" width="100%">
                <tr><th colspan="6">' . _AM_XFORMS_LISTING . '</th></tr>
                <tr>
                    <td class="head" align="center">' . _AM_XFORMS_ID . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_ORDER . '<br />' . _AM_XFORMS_ORDER_DESC . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_STATUS . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_TITLE . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_SENDTO . '</td>
                    <td class="head" align="center">' . _AM_XFORMS_ACTION . '</td>
                </tr>';

        /*Read list of forms*/
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php');
        $criteria = null;
        if ($showAll) {
            $criteria = new Criteria(1, 1);
        } else {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('form_active', 0, '<>'));
        }
        $criteria->setSort('form_order');
        $criteria->setOrder('ASC');
        $totalList = 0;
        if ($forms = $xforms_form_mgr->getObjects($criteria)) {
            foreach ($forms as $f) {
                if ($showAll || (!$showAll && $f->isActive())) {
                    $id    = $f->getVar('form_id');
                    $order = new XoopsFormText('', 'order[' . $id . ']', 3, 2, $f->getVar('form_order'));
                    $order->setExtra('style="text-align: right;"');
                    $sendto = $f->getVar('form_send_to_group');
                    if (intval($sendto, 10) == -1) {
                        $sendto = "<b>" . _AM_XFORMS_SENDTO_OTHER . ": </b>" . $f->getVar('form_send_to_other');
                    } else {
                        $group_mgr = xoops_gethandler('group');
                        if (false != $sendto && $group = $group_mgr->get($sendto)) {
                            $sendto = $group->getVar('name');
                        } else {
                            $sendto = _AM_XFORMS_SENDTO_ADMIN;
                        }
                    }
                    $fstatus = '<img src="' . $mypathIcon16 . '/active.gif" title="' . _AM_XFORMS_STATUS_ACTIVE . '" alt="' . _AM_XFORMS_STATUS_ACTIVE . '">';
                    if (!$f->isActive()) {
                        if ($f->getVar('form_active') == 0) {
                            $fstatus = '<img src="' . $mypathIcon16 . '/inactive.gif" title="' . _AM_XFORMS_STATUS_INACTIVE . '" alt="' . _AM_XFORMS_STATUS_INACTIVE . '">';
                        } else {
                            $fstatus = '<img src="' . $mypathIcon16 . '/expired.gif" title="' . _AM_XFORMS_STATUS_EXPIRED . '" alt="' . _AM_XFORMS_STATUS_EXPIRED . '">';
                        }
                    }
                    $ids = new XoopsFormHidden('ids[]', $id);
                    echo '
                        <tr>
                            <td class="odd" align="center">' . $id . '</td>
                            <td class="even" align="center">' . $order->render() . '</td>
                            <td class="odd" align="center">' . $fstatus . '</td>
                            <td class="odd"><a target="_blank" href="' . XFORMS_URL . '/?form_id=' . $id . '">' . $f->getVar('form_title') . '</a></td>
                            <td class="odd" align="center">' . $sendto . '</td>
                            <td class="odd" align="center" nowrap="nowrap">
                                <a href="' . XFORMS_ADMIN_URL . '?op=edit&form_id=' . $id . '"><img src="' . $pathIcon16 . '/edit.png" class="tooltip" title="' . _AM_XFORMS_ACTION_EDITFORM . '" alt="'
                        . _AM_XFORMS_ACTION_EDITFORM . '"></a>&nbsp;&nbsp;
                                <a href="elements.php?form_id=' . $id . '"><img src="' . $pathIcon16 . '/1day.png" class="tooltip" title="' . _AM_XFORMS_ACTION_EDITELEMENT . '" alt="'
                        . _AM_XFORMS_ACTION_EDITELEMENT . '"></a>&nbsp;&nbsp;
                                <a href="' . XFORMS_ADMIN_URL . '?op=edit&clone=1&form_id=' . $id . '"><img src="' . $pathIcon16 . '/editcopy.png" class="tooltip" title="' . _AM_XFORMS_ACTION_CLONE
                        . '" alt="' . _AM_XFORMS_ACTION_CLONE . '"></a>&nbsp;&nbsp;';
                    if ($f->getVar('form_save_db') != 0) {
                        echo '	<a href="report.php?op=show&form_id=' . $id . '"><img src="' . $mypathIcon16 . '/content.png" class="tooltip" title="' . _AM_XFORMS_ACTION_REPORT . '" alt="'
                            . _AM_XFORMS_ACTION_REPORT . '"></a>&nbsp;&nbsp;';
                    }
                    if ($f->getVar('form_active') == 1) {
                        echo '<a href="' . XFORMS_ADMIN_URL . '?op=inactive&form_id=' . $id . '"><img src="' . $mypathIcon16 . '/inactive.gif" class="tooltip" title="' . _AM_XFORMS_ACTION_INACTIVE
                            . '" alt="' . _AM_XFORMS_ACTION_INACTIVE . '"></a>&nbsp;&nbsp;';
                    }
                    echo '<a href="' . XFORMS_ADMIN_URL . '?op=delete&form_id=' . $id . '"><img src="' . $pathIcon16 . '/delete.png" class="tooltip" title="' . _DELETE . '" alt="' . _DELETE . '"></a>
                         ' . $ids->render() . '
                         </td>
                        </tr>';

                    ++$totalList;
                }
            }
            if ($totalList > 0) {
                $submit = new XoopsFormButton('', 'saveorder', _AM_XFORMS_RESET_ORDER, 'submit');
                $bshow  = new XoopsFormButton('', (($showAll) ? 'shownormal' : 'showall'), (($showAll) ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
                echo '	<tr>
                            <td class="foot">&nbsp;</td>
                            <td class="foot" align="center">' . $submit->render() . '</td>
                            <td class="foot" colspan="4">' . $bshow->render() . '</td>
                        </tr>
                        </table><br /><br />';
                echo '<fieldset><legend style="font-weight: bold; color: #900;">' . _AM_XFORMS_STATUS_INFORMATION . '</legend>
                        <div style="padding: 8px;">
                            <div style="text-align: center;">
                                <img src="' . $mypathIcon16 . '/active.gif">&nbsp;' . _AM_XFORMS_STATUS_ACTIVE . '&nbsp; &nbsp; &nbsp;
                                <img src="' . $mypathIcon16 . '/inactive.gif">&nbsp;' . _AM_XFORMS_STATUS_INACTIVE . '&nbsp; &nbsp; &nbsp;
                                <img src="' . $mypathIcon16 . '/expired.gif">&nbsp;' . _AM_XFORMS_STATUS_EXPIRED . '
                            </div>
                        </div>
                    </fieldset>';
            }
        }

        /*Show message no forms*/
        if ($totalList == 0) {
            $bshow = new XoopsFormButton('', (($showAll) ? 'shownormal' : 'showall'), (($showAll) ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo '  <tr>
                        <td class="odd" colspan="6" align="center">
                            ' . _AM_XFORMS_NO_FORMS . '
                        </td>
                    </tr>
                    <tr>
                        <td class="foot">&nbsp;</td>
                        <td class="foot" align="center">&nbsp;</td>
                        <td class="foot" colspan="4">' . $bshow->render() . '</td>
                    </tr>
                  </table>';
        }
        echo "\n</form>\n";
        break;

    case 'edit':
        $clone   = isset($_GET['clone']) ? intval($_GET['clone']) : false;
        $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=edit');
        if (!empty($form_id)) {
            $form = $xforms_form_mgr->get($form_id);
        } else {
            $form = $xforms_form_mgr->create();
        }

        $text_form_title = new XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 90, 255, $form->getVar('form_title', 'e'));

        $group_ids              = $moduleperm_handler->getGroupIds($xforms_form_mgr->perm_name, $form_id, $xoopsModule->getVar('mid'));
        $select_form_group_perm = new XoopsFormSelectGroup(_AM_XFORMS_PERM, 'form_group_perm', true, $group_ids, 5, true);

        $select_form_save_db = new XoopsFormSelect(_AM_XFORMS_SAVE_DB, 'form_save_db', $form->getVar('form_save_db'));
        $select_form_save_db->addOption('1', _AM_XFORMS_SAVE_DB_YES);
        $select_form_save_db->addOption('0', _AM_XFORMS_SAVE_DB_NO);
        $select_form_save_db->setDescription(_AM_XFORMS_SAVE_DB_DESC);

        $select_form_send_method = new XoopsFormSelect(_AM_XFORMS_SEND_METHOD, 'form_send_method', $form->getVar('form_send_method'));
        $select_form_send_method->addOption('e', _AM_XFORMS_SEND_METHOD_MAIL);
        $select_form_send_method->addOption('p', _AM_XFORMS_SEND_METHOD_PM);
        $select_form_send_method->addOption('n', _AM_XFORMS_SEND_METHOD_NO);
        $select_form_send_method->setDescription(_AM_XFORMS_SEND_METHOD_DESC);

        $select_form_send_to_group = new XoopsFormSelectGroup(_AM_XFORMS_SENDTO, 'form_send_to_group', false, $form->getVar('form_send_to_group'));
        $select_form_send_to_group->addOption('0', _AM_XFORMS_SENDTO_ADMIN);
        $select_form_send_to_group->addOption('-1', _AM_XFORMS_SENDTO_OTHER);

        $send_to_other           = $form->getVar('form_send_to_other');
        $text_form_send_to_other = new XoopsFormText(_AM_XFORMS_SENDTO_OTHER_EMAILS, 'form_send_to_other', 90, 50, empty($send_to_other) ? "" : $send_to_other);
        $text_form_send_to_other->setDescription(_AM_XFORMS_SENDTO_OTHER_DESC);

        $select_form_send_copy = new XoopsFormRadioYN(_AM_XFORMS_SEND_COPY, 'form_send_copy', ((intval($form->getVar('form_send_copy'), 10) > 0) ? 1 : 0), _YES, _NO);
        $select_form_send_copy->setDescription(_AM_XFORMS_SEND_COPY_DESC);

        $tarea_form_email_header = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $form->getVar('form_email_header', 'e'), 5, 90);
        $tarea_form_email_header->setDescription(_AM_XFORMS_EMAIL_HEADER_DESC);
        $tarea_form_email_header->skipPreview = true;

        $tarea_form_email_footer = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $form->getVar('form_email_footer', 'e'), 5, 90);
        $tarea_form_email_footer->setDescription(_AM_XFORMS_EMAIL_FOOTER_DESC);
        $tarea_form_email_footer->skipPreview = true;

        $tarea_form_email_uheader = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $form->getVar('form_email_uheader', 'e'), 5, 90);
        $tarea_form_email_uheader->setDescription(_AM_XFORMS_EMAIL_UHEADER_DESC);
        $tarea_form_email_uheader->skipPreview = true;

        $tarea_form_email_ufooter = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UFOOTER, 'form_email_ufooter', $form->getVar('form_email_ufooter', 'e'), 5, 90);
        $tarea_form_email_ufooter->setDescription(_AM_XFORMS_EMAIL_UFOOTER_DESC);
        $tarea_form_email_ufooter->skipPreview = true;

        $select_form_delimiter = new XoopsFormSelect(_AM_XFORMS_DELIMETER, 'form_delimiter', $form->getVar('form_delimiter'));
        $select_form_delimiter->addOption('s', _AM_XFORMS_DELIMETER_SPACE);
        $select_form_delimiter->addOption('b', _AM_XFORMS_DELIMETER_BR);

        $text_form_order = new XoopsFormText(_AM_XFORMS_ORDER, 'form_order', 3, 2, $form->getVar('form_order'));
        $text_form_order->setDescription(_AM_XFORMS_ORDER_DESC);

        $submit_text           = $form->getVar('form_submit_text');
        $text_form_submit_text = new XoopsFormText(_AM_XFORMS_SUBMIT_TEXT, 'form_submit_text', 50, 50, empty($submit_text) ? _SUBMIT : $submit_text);

        $tarea_form_desc = new XoopsFormDhtmlTextArea(_AM_XFORMS_DESC, 'form_desc', $form->getVar('form_desc', 'e'), 5, 90);
        $tarea_form_desc->setDescription(_AM_XFORMS_DESC_DESC);
        $tarea_form_desc->skipPreview = true;

        $tarea_form_intro = new XoopsFormDhtmlTextArea(_AM_XFORMS_INTRO, 'form_intro', $form->getVar('form_intro', 'e'), 5, 90);
        $tarea_form_intro->setDescription(_AM_XFORMS_INTRO_DESC);
        $tarea_form_intro->skipPreview = true;

        $text_form_whereto = new XoopsFormText(_AM_XFORMS_WHERETO, 'form_whereto', 90, 255, $form->getVar('form_whereto'));
        $text_form_whereto->setDescription(_AM_XFORMS_WHERETO_DESC);

        $select_form_display_style = new XoopsFormSelect(_AM_XFORMS_DISPLAY_STYLE, 'form_display_style', $form->getVar('form_display_style'));
        $select_form_display_style->addOption('f', _AM_XFORMS_DISPLAY_STYLE_FORM);
        $select_form_display_style->addOption('e', _AM_XFORMS_DISPLAY_STYLE_POLL);
        $select_form_display_style->setDescription(_AM_XFORMS_DISPLAY_STYLE_DESC);

        $radio_form_define_begin = new XoopsFormRadioYN(_AM_XFORMS_DEFINE_BEGIN, 'define_form_begin', ((intval($form->getVar('form_begin'), 10) > 0) ? 1 : 0), _YES, _NO);
        $text_form_begin         = new XoopsFormDateTime(_AM_XFORMS_BEGIN, 'form_begin', 15, $form->getVar('form_begin'));
        $begin_tray              = new XoopsFormElementTray(_AM_XFORMS_BEGIN, '<br />');
        $begin_tray->addElement($radio_form_define_begin);
        $begin_tray->addElement($text_form_begin);
        $begin_tray->setDescription(_AM_XFORMS_DEFINE_BEGIN_DESC);

        $radio_form_define_end = new XoopsFormRadioYN(_AM_XFORMS_DEFINE_END, 'define_form_end', ((intval($form->getVar('form_end'), 10) > 0) ? 1 : 0), _YES, _NO);
        $text_form_end         = new XoopsFormDateTime(_AM_XFORMS_END, 'form_end', 15, $form->getVar('form_end'));
        $end_tray              = new XoopsFormElementTray(_AM_XFORMS_END, '<br />');
        $end_tray->addElement($radio_form_define_end);
        $end_tray->addElement($text_form_end);
        $end_tray->setDescription(_AM_XFORMS_DEFINE_END_DESC);

        $select_form_active = new XoopsFormRadioYN(_AM_XFORMS_ACTIVE, 'form_active', ((intval($form->getVar('form_active'), 10) > 0) ? 1 : 0), _YES, _NO);
        $select_form_active->setDescription(_AM_XFORMS_ACTIVE_DESC);

        $hidden_op = new XoopsFormHidden('op', 'saveform');
        $submit    = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $submit1   = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_ELEMENTS, 'submit');
        $tray      = new XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);

        if (empty($form_id)) {
            $caption = _AM_XFORMS_NEW;
        } else {
            if ($clone) {
                $caption         = sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title'));
                $clone_form_id   = new XoopsFormHidden('clone_form_id', $form_id);
                $text_form_title = new XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title', 'e')));
            } else {
                $caption        = sprintf(_AM_XFORMS_EDIT, $form->getVar('form_title'));
                $hidden_form_id = new XoopsFormHidden('form_id', $form_id);
            }
        }
        $output = new XoopsThemeForm($caption, 'editform', XFORMS_ADMIN_URL);
        $output->addElement($text_form_title, true);
        $output->addElement($select_form_group_perm);
        $output->addElement($select_form_save_db);
        $output->addElement($select_form_send_method);
        $output->addElement($select_form_send_to_group);
        $output->addElement($text_form_send_to_other);
        $output->addElement($select_form_send_copy);
        $output->addElement($tarea_form_email_header);
        $output->addElement($tarea_form_email_footer);
        $output->addElement($tarea_form_email_uheader);
        $output->addElement($tarea_form_email_ufooter);
        $output->addElement($select_form_delimiter);
        $output->addElement($text_form_order);
        $output->addElement($text_form_submit_text, true);
        $output->addElement($tarea_form_desc);
        $output->addElement($tarea_form_intro);
        $output->addElement($text_form_whereto);
        $output->addElement($select_form_display_style);
        $output->addElement($begin_tray);
        $output->addElement($end_tray);
        $output->addElement($select_form_active);

        $output->addElement($hidden_op);
        if (isset($hidden_form_id) && is_object($hidden_form_id)) {
            $output->addElement($hidden_form_id);
        }
        if (isset($clone_form_id) && is_object($clone_form_id)) {
            $output->addElement($clone_form_id);
        }
        $output->addElement($tray);
        $output->display();
        break;

    case 'delete':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            xoops_confirm(array('op' => 'delete', 'form_id' => $_GET['form_id'], 'ok' => 1), XFORMS_ADMIN_URL, _AM_XFORMS_CONFIRM_DELETE);
        } else {
            $form_id = intval($_POST['form_id']);
            if (empty($form_id)) {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
            if ($form = $xforms_form_mgr->get($form_id)) {
                if ($xforms_form_mgr->delete($form)) {
                    $xforms_ele_mgr = xoops_getmodulehandler('elements');
                    $criteria       = new Criteria('form_id', $form_id);
                    $xforms_ele_mgr->deleteAll($criteria);
                    $xforms_form_mgr->deleteFormPermissions($form_id);
                    redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $form->getHtmlErrors();
            } else {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;

    case 'inactive':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            xoops_confirm(array('op' => 'inactive', 'form_id' => $_GET['form_id'], 'ok' => 1), XFORMS_ADMIN_URL, _AM_XFORMS_CONFIRM_INACTIVE);
        } else {
            $form_id = intval($_POST['form_id']);
            if (empty($form_id)) {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
            if ($form = $xforms_form_mgr->get($form_id)) {
                if ($xforms_form_mgr->inactive($form)) {
                    redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $form->getHtmlErrors();
            } else {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;

    case 'saveorder':
        if (!isset($_POST['ids']) || count($_POST['ids']) < 1) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
        }
        extract($_POST);
        foreach ($ids as $id) {
            $form = $xforms_form_mgr->get($id);
            $form->setVar('form_order', $order[$id]);
            $xforms_form_mgr->insert($form);
        }
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_DBUPDATED);
        break;

    case 'saveform':
        if (!isset($_POST['submit'])) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
        }
        extract($_POST);
        if ((empty($form_save_db) || ($form_save_db == "0")) && ($form_send_method == "n")) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SAVESENT);
        }
        $error = '';
        if (!empty($form_id)) {
            $form = $xforms_form_mgr->get($form_id);
        } else {
            $form = $xforms_form_mgr->create();
        }
        $form->setVar('form_save_db', $form_save_db);
        $form->setVar('form_send_method', $form_send_method);
        $form->setVar('form_send_to_group', $form_send_to_group);
        $form->setVar('form_send_to_other', $form_send_to_other);
        $form->setVar('form_send_copy', $form_send_copy);
        $form->setVar('form_email_header', $form_email_header);
        $form->setVar('form_email_footer', $form_email_footer);
        $form->setVar('form_email_uheader', $form_email_uheader);
        $form->setVar('form_email_ufooter', $form_email_ufooter);
        $form->setVar('form_order', $form_order);
        $form->setVar('form_delimiter', $form_delimiter);
        $form->setVar('form_title', $form_title);
        $form->setVar('form_submit_text', $form_submit_text);
        $form->setVar('form_desc', $form_desc);
        $form->setVar('form_intro', $form_intro);
        $form->setVar('form_whereto', $form_whereto);
        $form->setVar('form_display_style', $form_display_style);
        $form->setVar('form_begin', 0);
        if (intval($define_form_begin, 10) != 0) {
            $form_begin = strtotime($form_begin['date']) + $form_begin['time'];
            $form->setVar('form_begin', $form_begin);
        }
        $form->setVar('form_end', 0);
        if (intval($define_form_end, 10) != 0) {
            $form_end = strtotime($form_end['date']) + $form_end['time'];
            $form->setVar('form_end', $form_end);
        }
        $form->setVar('form_active', $form_active);
        if (!$ret = $xforms_form_mgr->insert($form)) {
            $error = $form->getHtmlErrors();
        } else {
            $xforms_form_mgr->deleteFormPermissions($ret);
            if (count($form_group_perm) > 0) {
                $xforms_form_mgr->insertFormPermissions($ret, $form_group_perm);
            }
            if (!empty($clone_form_id)) {
                $xforms_ele_mgr = xoops_getmodulehandler('elements');
                $criteria       = new Criteria('form_id', $clone_form_id);
                $count          = $xforms_ele_mgr->getCount($criteria);
                if ($count > 0) {
                    $elements = $xforms_ele_mgr->getObjects($criteria);
                    foreach ($elements as $e) {
                        $cloned = $e->xoopsClone();
                        $cloned->setVar('form_id', $ret);
                        if (!$xforms_ele_mgr->insert($cloned)) {
                            $error .= $cloned->getHtmlErrors();
                        }
                    }
                }
            } elseif (empty($form_id)) {
                $xforms_ele_mgr = xoops_getmodulehandler('elements');
                $error          = $xforms_ele_mgr->insertDefaults($ret);
            }
        }
        if (!empty($error)) {
            xoops_cp_header();
            echo $error;
        } else {
            if ($_POST['submit'] == _AM_XFORMS_SAVE_THEN_ELEMENTS) {
                redirect_header(XFORMS_URL . '/admin/elements.php?form_id=' . $ret, 0, _AM_XFORMS_DBUPDATED);
            } else {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_DBUPDATED);
            }
        }
        break;
}

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
