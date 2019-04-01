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
$myts = MyTextSanitizer::getInstance();

$op        = Request::getCmd('op', 'list');
$showAll   = Request::getBool('showall', false, 'POST');
$saveOrder = Request::getBool('saveorder', false, 'POST');
/*
$op   = isset($_GET['op']) ? trim($_GET['op']) : 'list';
$op   = isset($_POST['op']) ? trim($_POST['op']) : $op;

$showAll   = isset($_POST['showall']) ? true : false;
$saveOrder = isset($_POST['saveorder']) ? true : false;
*/
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
            <table class="outer width100" cellspacing="1">
                <tr><th colspan="6">' . _AM_XFORMS_LISTING . '</th></tr>
                <tr>
                    <td class="head center">' . _AM_XFORMS_ID . '</td>
                    <td class="head center">' . _AM_XFORMS_ORDER . '<br />' . _AM_XFORMS_ORDER_DESC . '</td>
                    <td class="head center">' . _AM_XFORMS_STATUS . '</td>
                    <td class="head center">' . _AM_XFORMS_TITLE . '</td>
                    <td class="head center">' . _AM_XFORMS_SENDTO . '</td>
                    <td class="head center">' . _AM_XFORMS_ACTION . '</td>
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
        if ($forms = $xformsFormMgr->getObjects($criteria)) {
            foreach ($forms as $f) {
                if ($showAll || (!$showAll && $f->isActive())) {
                    $id    = $f->getVar('form_id');
                    $order = new XoopsFormText('', 'order[' . $id . ']', 3, 2, $f->getVar('form_order'));
                    $order->setExtra('style="text-align: right;"');
                    $sendto = $f->getVar('form_send_to_group');
                    if (-1 === (int)$sendto == -1) {
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
                            <td class="odd center">' . $id . '</td>
                            <td class="even center">' . $order->render() . '</td>
                            <td class="odd center">' . $fstatus . '</td>
                            <td class="odd"><a target="_blank" href="' . XFORMS_URL . '/?form_id=' . $id . '">' . $f->getVar('form_title') . '</a></td>
                            <td class="odd center">' . $sendto . '</td>
                            <td class="odd center" nowrap="nowrap">
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
                            <td class="foot center">' . $submit->render() . '</td>
                            <td class="foot" colspan="4">' . $bshow->render() . '</td>
                        </tr>
                        </table><br /><br />';
                echo '<fieldset><legend style="font-weight: bold; color: #900;">' . _AM_XFORMS_STATUS_INFORMATION . '</legend>
                        <div style="padding: 8px;">
                            <div class="center">
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
                        <td class="foot center">&nbsp;</td>
                        <td class="foot" colspan="4">' . $bshow->render() . '</td>
                    </tr>
                  </table>';
        }
        echo "\n</form>\n";
        break;

    case 'edit':
        $clone  = isset($_GET['clone']) ? (int)$_GET['clone'] : false;
        $formId = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
        xoops_cp_header();
        $indexAdmin = new ModuleAdmin();
        echo $indexAdmin->addNavigation('main.php?op=edit');
        if (!empty($formId)) {
            $form = $xformsFormMgr->get($formId);
        } else {
            $form = $xformsFormMgr->create();
        }

        $textFormTitle = new XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 90, 255, $form->getVar('form_title', 'e'));

        $groupIds            = $moduleperm_handler->getGroupIds($xformsFormMgr->perm_name, $formId, $xoopsModule->getVar('mid'));
        $selectFormGroupPerm = new XoopsFormSelectGroup(_AM_XFORMS_PERM, 'form_group_perm', true, $groupIds, 5, true);

        $selectFormSaveDb = new XoopsFormSelect(_AM_XFORMS_SAVE_DB, 'form_save_db', $form->getVar('form_save_db'));
        $selectFormSaveDb->addOption('1', _AM_XFORMS_SAVE_DB_YES);
        $selectFormSaveDb->addOption('0', _AM_XFORMS_SAVE_DB_NO);
        $selectFormSaveDb->setDescription(_AM_XFORMS_SAVE_DB_DESC);

        $selectFormSendMethod = new XoopsFormSelect(_AM_XFORMS_SEND_METHOD, 'form_send_method', $form->getVar('form_send_method'));
        $selectFormSendMethod->addOption('e', _AM_XFORMS_SEND_METHOD_MAIL);
        $selectFormSendMethod->addOption('p', _AM_XFORMS_SEND_METHOD_PM);
        $selectFormSendMethod->addOption('n', _AM_XFORMS_SEND_METHOD_NO);
        $selectFormSendMethod->setDescription(_AM_XFORMS_SEND_METHOD_DESC);

        $selectFormSendToGroup = new XoopsFormSelectGroup(_AM_XFORMS_SENDTO, 'form_send_to_group', false, $form->getVar('form_send_to_group'));
        $selectFormSendToGroup->addOption('0', _AM_XFORMS_SENDTO_ADMIN);
        $selectFormSendToGroup->addOption('-1', _AM_XFORMS_SENDTO_OTHER);

        $sendToOther         = $form->getVar('form_send_to_other');
        $textFormSendToOther = new XoopsFormText(_AM_XFORMS_SENDTO_OTHER_EMAILS, 'form_send_to_other', 90, 50, empty($sendToOther) ? "" : $sendToOther);
        $textFormSendToOther->setDescription(_AM_XFORMS_SENDTO_OTHER_DESC);

        $selectFormSendCopy = new XoopsFormRadioYN(_AM_XFORMS_SEND_COPY, 'form_send_copy', (((int)$form->getVar('form_send_copy') > 0) ? 1 : 0), _YES, _NO);
        $selectFormSendCopy->setDescription(_AM_XFORMS_SEND_COPY_DESC);

        $tareaFormEmailHeader = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $form->getVar('form_email_header', 'e'), 5, 90);
        $tareaFormEmailHeader->setDescription(_AM_XFORMS_EMAIL_HEADER_DESC);
        $tareaFormEmailHeader->skipPreview = true;

        $tareaFormEmailFooter = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $form->getVar('form_email_footer', 'e'), 5, 90);
        $tareaFormEmailFooter->setDescription(_AM_XFORMS_EMAIL_FOOTER_DESC);
        $tareaFormEmailFooter->skipPreview = true;

        $tareaFormEmailUheader = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $form->getVar('form_email_uheader', 'e'), 5, 90);
        $tareaFormEmailUheader->setDescription(_AM_XFORMS_EMAIL_UHEADER_DESC);
        $tareaFormEmailUheader->skipPreview = true;

        $tareaFormEmailUfooter = new XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UFOOTER, 'form_email_ufooter', $form->getVar('form_email_ufooter', 'e'), 5, 90);
        $tareaFormEmailUfooter->setDescription(_AM_XFORMS_EMAIL_UFOOTER_DESC);
        $tareaFormEmailUfooter->skipPreview = true;

        $selectFormDelimiter = new XoopsFormSelect(_AM_XFORMS_DELIMETER, 'form_delimiter', $form->getVar('form_delimiter'));
        $selectFormDelimiter->addOption('s', _AM_XFORMS_DELIMETER_SPACE);
        $selectFormDelimiter->addOption('b', _AM_XFORMS_DELIMETER_BR);

        $textFormOrder = new XoopsFormText(_AM_XFORMS_ORDER, 'form_order', 3, 2, $form->getVar('form_order'));
        $textFormOrder->setDescription(_AM_XFORMS_ORDER_DESC);

        $submitText         = $form->getVar('form_submitText');
        $TextFormSubmitText = new XoopsFormText(_AM_XFORMS_SUBMIT_TEXT, 'form_submit_text', 50, 50, empty($submitText) ? _SUBMIT : $submit_text);

        $tareaFormDesc = new XoopsFormDhtmlTextArea(_AM_XFORMS_DESC, 'form_desc', $form->getVar('form_desc', 'e'), 5, 90);
        $tareaFormDesc->setDescription(_AM_XFORMS_DESC_DESC);
        $tareaFormDesc->skipPreview = true;

        $tareaFormIntro = new XoopsFormDhtmlTextArea(_AM_XFORMS_INTRO, 'form_intro', $form->getVar('form_intro', 'e'), 5, 90);
        $tareaFormIntro->setDescription(_AM_XFORMS_INTRO_DESC);
        $tareaFormIntro->skipPreview = true;

        $textFormWhereTo = new XoopsFormText(_AM_XFORMS_WHERETO, 'form_whereto', 90, 255, $form->getVar('form_whereto'));
        $textFormWhereTo->setDescription(_AM_XFORMS_WHERETO_DESC);

        $selectFormDisplayStyle = new XoopsFormSelect(_AM_XFORMS_DISPLAY_STYLE, 'form_display_style', $form->getVar('form_display_style'));
        $selectFormDisplayStyle->addOption('f', _AM_XFORMS_DISPLAY_STYLE_FORM);
        $selectFormDisplayStyle->addOption('e', _AM_XFORMS_DISPLAY_STYLE_POLL);
        $selectFormDisplayStyle->setDescription(_AM_XFORMS_DISPLAY_STYLE_DESC);

        $radioFormDefineBegin = new XoopsFormRadioYN(_AM_XFORMS_DEFINE_BEGIN, 'define_form_begin', (((int)$form->getVar('form_begin') > 0) ? 1 : 0), _YES, _NO);
        $textFormBegin        = new XoopsFormDateTime(_AM_XFORMS_BEGIN, 'form_begin', 15, $form->getVar('form_begin'));
        $BeginTray            = new XoopsFormElementTray(_AM_XFORMS_BEGIN, '<br />');
        $BeginTray->addElement($radioFormDefineBegin);
        $BeginTray->addElement($textFormBegin);
        $BeginTray->setDescription(_AM_XFORMS_DEFINE_BEGIN_DESC);

        $radioFormDefineEnd = new XoopsFormRadioYN(_AM_XFORMS_DEFINE_END, 'define_form_end', (((int)$form->getVar('form_end') > 0) ? 1 : 0), _YES, _NO);
        $textFormEnd        = new XoopsFormDateTime(_AM_XFORMS_END, 'form_end', 15, $form->getVar('form_end'));
        $endTray            = new XoopsFormElementTray(_AM_XFORMS_END, '<br />');
        $endTray->addElement($radioFormDefineEnd);
        $endTray->addElement($textFormEnd);
        $endTray->setDescription(_AM_XFORMS_DEFINE_END_DESC);

        $selectFormActive = new XoopsFormRadioYN(_AM_XFORMS_ACTIVE, 'form_active', (((int)$form->getVar('form_active') > 0) ? 1 : 0), _YES, _NO);
        $selectFormActive->setDescription(_AM_XFORMS_ACTIVE_DESC);

        $hiddenOp = new XoopsFormHidden('op', 'saveform');
        $submit   = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $submit1  = new XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_ELEMENTS, 'submit');
        $tray     = new XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);

        if (empty($formId)) {
            $caption = _AM_XFORMS_NEW;
        } else {
            if ($clone) {
                $caption       = sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title'));
                $cloneFormId   = new XoopsFormHidden('cloneFormId', $formId);
                $textFormTitle = new XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title', 'e')));
            } else {
                $caption       = sprintf(_AM_XFORMS_EDIT, $form->getVar('form_title'));
                $$hiddenFormId = new XoopsFormHidden('form_id', $formId);
            }
        }
        $output = new XoopsThemeForm($caption, 'editform', XFORMS_ADMIN_URL);
        $output->addElement($textFormTitle, true);
        $output->addElement($selectFormGroupPerm);
        $output->addElement($selectFormsaveDb);
        $output->addElement($selectFormSendMethod);
        $output->addElement($selectFormSendToGroup);
        $output->addElement($textFormSendToOther);
        $output->addElement($selectFormSendCopy);
        $output->addElement($tareaFormEmailHeader);
        $output->addElement($tareaFormEmailFooter);
        $output->addElement($tareaFormEmailUheader);
        $output->addElement($tareaFormEmailUfooter);
        $output->addElement($selectFormDelimiter);
        $output->addElement($textFormOrder);
        $output->addElement($textFormSubmitText, true);
        $output->addElement($tareaFormDesc);
        $output->addElement($tareaFormIntro);
        $output->addElement($textFormWhereTo);
        $output->addElement($selectFormDisplayStyle);
        $output->addElement($beginTray);
        $output->addElement($endTray);
        $output->addElement($selectFormActive);

        $output->addElement($hiddenOp);
        if (isset($hiddenFormId) && is_object($hiddenFormId)) {
            $output->addElement($hiddenFormId);
        }
        if (isset($cloneFormId) && is_object($cloneFormId)) {
            $output->addElement($cloneFormId);
        }
        $output->addElement($tray);
        $output->display();
        break;

    case 'delete':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            xoops_confirm(array('op' => 'delete', 'form_id' => $_GET['form_id'], 'ok' => 1), XFORMS_ADMIN_URL, _AM_XFORMS_CONFIRM_DELETE);
        } else {
            $formId = (int)$_POST['form_id'];
            if (empty($formId)) {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
            if ($form = $xformsFormMgr->get($formId)) {
                if ($xformsFormMgr->delete($form)) {
                    $xformsEleMgr = xoops_getmodulehandler('elements');
                    $criteria     = new Criteria('form_id', $formId);
                    $xformsEleMgr->deleteAll($criteria);
                    $xformsFormMgr->deleteFormPermissions($formId);
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
            $formId = (int)$_POST['form_id'];
            if (empty($formId)) {
                redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
            }
            if ($form = $xformsFormMgr->get($formId)) {
                if ($xformsFormMgr->inactive($form)) {
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
            $form = $xformsFormMgr->get($id);



            $form->setVar('form_order', $order[$id]);
            $xformsFormMgr->insert($form);
        }
        redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_DBUPDATED);
        break;

    case 'saveform':
        if (!isset($_POST['submit'])) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
        }
        extract($_POST);
        if ((empty($formSaveDb) || ($formSaveDb == "0")) && ($formSendMethod == "n")) {
            redirect_header(XFORMS_ADMIN_URL, 0, _AM_XFORMS_NOTHING_SAVESENT);
        }
        $error = '';
        if (!empty($formId)) {
            $form = $xformsFormMgr->get($formId);
        } else {
            $form = $xformsFormMgr->create();
        }
        $form->setVar('form_save_db', $formSaveDb);
        $form->setVar('form_send_method', $formSendMethod);
        $form->setVar('form_send_to_group', $formSendToGroup);
        $form->setVar('form_send_to_other', $formSendToOther);
        $form->setVar('form_send_copy', $formSendCopy);
        $form->setVar('form_email_header', $formEmailHeader);
        $form->setVar('form_email_footer', $formEmailFooter);
        $form->setVar('form_email_uheader', $formEmailUheader);
        $form->setVar('form_email_ufooter', $formEmailUfooter);
        $form->setVar('form_order', $formOrder);
        $form->setVar('form_delimiter', $formDelimiter);
        $form->setVar('form_title', $formTitle);
        $form->setVar('form_submit_text', $formSubmitText);
        $form->setVar('form_desc', $formDesc);
        $form->setVar('form_intro', $formIntro);
        $form->setVar('form_whereto', $formWhereTo);
        $form->setVar('form_display_style', $formDisplayStyle);
        $form->setVar('form_begin', 0);
        if (0 !== (int)$defineFormBegin) {
            $formBegin = strtotime($formBegin['date']) + $formBegin['time'];
            $form->setVar('form_begin', $formBegin);
        }
        $form->setVar('form_end', 0);
        if (0 !== (int)$defineFormEnd) {
            $formEnd = strtotime($formEnd['date']) + $formEnd['time'];
            $form->setVar('form_end', $formEnd);
        }
        $form->setVar('form_active', $form_active);
        if (!$ret = $xformsFormMgr->insert($form)) {
            $error = $form->getHtmlErrors();
        } else {
            $xformsFormMgr->deleteFormPermissions($ret);
            if (count($form_group_perm) > 0) {
                $xformsFormMgr->insertFormPermissions($ret, $form_group_perm);
            }
            if (!empty($cloneFormId)) {
                $xformsEleMgr = xoops_getmodulehandler('elements');
                $criteria     = new Criteria('form_id', $cloneFormId);
                $count        = $xformsEleMgr->getCount($criteria);
                if ($count > 0) {
                    $elements = $xformsEleMgr->getObjects($criteria);



                    foreach ($elements as $e) {
                        $cloned = $e->xoopsClone();
                        $cloned->setVar('form_id', $ret);
                        if (!$xformsEleMgr->insert($cloned)) {
                            $error .= $cloned->getHtmlErrors();
                        }
                    }
                }
            } elseif (empty($formId)) {
                $xformsEleMgr = xoops_getmodulehandler('elements');
                $error        = $xformsEleMgr->insertDefaults($ret);
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
