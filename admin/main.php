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

use Xmf\Module\Admin;
use Xmf\Request;
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';
$myts = \MyTextSanitizer::getInstance();

$op        = Request::getCmd('op', 'list');
$showAll   = Request::getBool('showall', false, 'POST');
$saveOrder = Request::getBool('saveorder', false, 'POST');

if ($showAll) {
    $op = 'list';
} elseif ($saveOrder) {
    $op = 'saveorder';
}

switch ($op) {
    case 'list':
    default:
        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->addItemButton(_AM_XFORMS_NEW, 'main.php' . '?op=edit', 'add');
        $adminObject->displayButton('left', '');

        $perpage = (int)$helper->getConfig('perpage'); // get # of forms to show per page

        $xformsDisplay          = new \stdClass(); // group all the page items together
        $xformsDisplay->start   = Request::getInt('start', 0);
        $xformsDisplay->perpage = ($perpage > 0) ? $perpage : Constants::FORMS_PER_PAGE_DEFAULT;
        $xformsDisplay->order   = 'ASC';
        $xformsDisplay->sort    = 'form_order';

        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url("browse.php?modules/{$moduleDirName}/assets/css/style.css"));

        echo "<form action='" . basename(__FILE__) . "' method='post'>\n";
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<table class='outer width100 bspacing1'>\n"
             . "  <thead>\n"
             . "  <tr><th colspan='6'>"
             . _AM_XFORMS_LISTING
             . "</th></tr>\n"
             . "  <tr>\n"
             . "    <td class='head center bottom width5'>"
             . _AM_XFORMS_NO
             . "</td>\n"
             . "    <td class='head center bottom'>"
             . _AM_XFORMS_TITLE
             . "</td>\n"
             . "    <td class='head center bottom width10'>"
             . _AM_XFORMS_ORDER
             . '<br>'
             . _AM_XFORMS_ORDER_DESC
             . "</td>\n"
             . "    <td class='head center bottom width5'>"
             . _AM_XFORMS_STATUS
             . "</td>\n"
             . "    <td class='head center bottom width15'>"
             . _AM_XFORMS_SENDTO
             . "</td>\n"
             . "    <td class='head center bottom width10'>"
             . _AM_XFORMS_ACTION
             . "</td>\n"
             . "  </tr>\n"
             . "  </thead>\n"
             . "  <tbody>\n";

        //Read list of forms
        if ($showAll) {
            $criteria = new \Criteria('');
        } else {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('form_active', Constants::FORM_INACTIVE, '<>'));
        }
        $ttlFormCount = $xformsFormsHandler->getCount(); // count of all forms we want
        // now get the forms
        $criteria->setStart($xformsDisplay->start);
        $criteria->setLimit($xformsDisplay->perpage);
        $criteria->setSort($xformsDisplay->sort);
        $criteria->setOrder($xformsDisplay->order);
        $totalList    = 0;
        $forms        = $xformsFormsHandler->getAll($criteria, null, true, false);
        $ttlFormCount = $xformsFormsHandler->getCount(); // count of all forms

        if ($forms) {
            // get the UserData to see if there's any reports
            $criteria = new \CriteriaCompo();
            $criteria->setGroupBy('form_id');
            $uDataHandler  = $helper->getHandler('Userdata');
            $rptCountArray = $uDataHandler->getCounts($criteria);

            if (!class_exists('Xforms\FormInput')) {
                xoops_load('formInput', $moduleDirName);
            }

            foreach ($forms as $f) {
                if ($showAll || $f->isActive()) {
                    $id    = $f->getVar('form_id');
                    $order = new Xforms\FormInput('', "order[{$id}]", 5, 5, $f->getVar('form_order'), null, 'number');
                    $order->setAttribute('min', 0);
                    $order->setExtra('style="width: 5em;"');
                    $sendTo    = (int)$f->getVar('form_send_to_group');
                    $group_mgr = xoops_getHandler('group');
                    if (Constants::SEND_TO_OTHER == $sendTo) {
                        $sendToTxt = '<b>' . _AM_XFORMS_SENDTO_OTHER . ': </b>' . $f->getVar('form_send_to_other');
                    } elseif ((Constants::SEND_TO_NONE != $sendTo) && ($group = $group_mgr->get($sendTo))) {
                        $sendToTxt = $group->getVar('name');
                    } else {
                        $sendToTxt = _AM_XFORMS_SENDTO_ADMIN;
                    }

                    if (!$f->isActive()) {
                        if (Constants::FORM_INACTIVE == $f->getVar('form_active')) {
                            $fStatus = "<img src='{$pathModIcon16}/inactive.gif' title='" . _AM_XFORMS_STATUS_INACTIVE . "' alt='" . _AM_XFORMS_STATUS_INACTIVE . "'>";
                            $fAction = " <a href='" . $_SERVER['PHP_SELF'] . "?op=active&form_id={$id}'><img src='{$pathModIcon16}/active.gif' class='tooltip floatcenter1' title='" . _AM_XFORMS_ACTION_ACTIVE . "' alt='" . _AM_XFORMS_ACTION_ACTIVE . "'></a>";
                        } else {
                            $fStatus = "<img src='{$pathModIcon16}/expired.gif' title='" . _AM_XFORMS_STATUS_EXPIRED . "' alt='" . _AM_XFORMS_STATUS_EXPIRED . "'>";
                            $fAction = '';
                        }
                    } else {
                        $fStatus = "<img src='{$pathModIcon16}/active.gif' title='" . _AM_XFORMS_STATUS_ACTIVE . "' alt='" . _AM_XFORMS_STATUS_ACTIVE . "'>";
                        $fAction = "<a href='" . $_SERVER['PHP_SELF'] . "?op=inactive&form_id={$id}'><img src='{$pathModIcon16}/inactive.gif' class='tooltip floatcenter1' title='" . _AM_XFORMS_ACTION_INACTIVE . "' alt='" . _AM_XFORMS_ACTION_INACTIVE . "'></a>";
                    }
                    $ids = new \XoopsFormHidden('ids[]', $id);
                    echo "  <tr>\n"
                         . "    <td class='odd middle center'>{$id}</td>\n"
                         . "    <td class='even middle'><a href='"
                         . $helper->url("index.php?form_id={$id}")
                         . "' title='"
                         . _AM_XFORMS_ACTION_VIEWFORM
                         . "'>"
                         . $f->getVar('form_title', 's')
                         . "</a><br>\n"
                         . '      '
                         . $f->getVar('form_desc', 's')
                         . "\n"
                         . "    </td>\n"
                         . "    <td class='odd middle center'>"
                         . $order->render()
                         . "</td>\n"
                         . "    <td class='even middle center'>{$fStatus}</td>\n"
                         . "    <td class='odd middle center'>{$sendToTxt}</td>\n"
                         . "    <td class='even middle center' nowrap='nowrap'>\n"
                         . "      <a href='"
                         . $_SERVER['PHP_SELF']
                         . "?op=edit&form_id={$id}'><img src='"
                         . Admin::iconUrl('edit.png', '16')
                         . "' class='tooltip floatcenter1' title='"
                         . _AM_XFORMS_ACTION_EDITFORM
                         . "' alt='"
                         . _AM_XFORMS_ACTION_EDITFORM
                         . "'></a>\n"
                         . "      {$fAction}\n"
                         . "      <a href='"
                         . $_SERVER['PHP_SELF']
                         . "?op=edit&clone=1&form_id={$id}'><img src='"
                         . Admin::iconUrl('editcopy.png', '16')
                         . "' class='tooltip floatcenter1' title='"
                         . _AM_XFORMS_ACTION_CLONE
                         . "' alt='"
                         . _AM_XFORMS_ACTION_CLONE
                         . "'></a>\n"
                         . "      <a href='"
                         . $_SERVER['PHP_SELF']
                         . "?op=delete&form_id={$id}'><img src='"
                         . Admin::iconUrl('delete.png', '16')
                         . "' class='tooltip floatcenter1' title='"
                         . _DELETE
                         . "' alt='"
                         . _DELETE
                         . "'></a>\n"
                         . '      '
                         . $ids->render()
                         . "\n"
                         . "      <a target='_blank' href='"
                         . $helper->url("index.php?form_id={$id}")
                         . "'><img src='"
                         . Admin::iconUrl('view.png', '16')
                         . "' class='tooltip floatcenter1' title='"
                         . _AM_XFORMS_ACTION_VIEWFORM
                         . "' alt='"
                         . _AM_XFORMS_ACTION_VIEWFORM
                         . "'></a>\n";

                    if (Constants::SAVE_IN_DB == $f->getVar('form_save_db') && isset($rptCountArray[$id])) {
                        echo "      <a href='report.php?op=show&form_id={$id}'><img src='{$pathModIcon16}/content.png' class='tooltip floatcenter1' title='" . _AM_XFORMS_ACTION_REPORT . "' alt='" . _AM_XFORMS_ACTION_REPORT . "'></a>\n";
                    }
                    echo "      <a href='elements.php?form_id={$id}'><img src='" . Admin::iconUrl('1day.png', '16') . "' class='tooltip floatcenter1' title='" . _AM_XFORMS_ACTION_EDITELEMENT . "' alt='" . _AM_XFORMS_ACTION_EDITELEMENT . "'></a>\n" . "    </td>\n" . "  </tr>\n";

                    ++$totalList;
                }
            }
            if ($totalList > 0) {
                $submit = new \XoopsFormButton('', 'saveorder', _AM_XFORMS_RESET_ORDER, 'submit');
                $bshow  = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
                echo "  </tbody>\n"
                     . "  <tfoot>\n"
                     . "  <tr>\n"
                     . "    <td class='foot' colspan='2'>"
                     . $bshow->render()
                     . "</td>\n"
                     . "    <td class='foot center'>"
                     . $submit->render()
                     . $xoopsSecurity->getTokenHTML()
                     . "</td>\n"
                     . "    <td class='foot' colspan='3'>&nbsp;</td>\n"
                     . "  </tr>\n"
                     . "  </tfoot>\n"
                     . "</table><br><br>\n";

                if ($ttlFormCount > $xformsDisplay->perpage) {
                    xoops_load('pagenav');
                    $xformsPagenav = new \XoopsPageNav($ttlFormCount, $xformsDisplay->perpage, $xformsDisplay->start, 'start', 'perpage=' . $xformsDisplay->perpage);
                    echo "<div class='center middle larger width100 line160'>" . $xformsPagenav->renderNav() . "</div>\n";
                }

                echo "<fieldset><legend class='bold' style='color: #900;'>"
                     . _AM_XFORMS_STATUS_INFORMATION
                     . "</legend>\n"
                     . "<div class='pad7'>\n"
                     . "  <div class='center'>\n"
                     . "    <img src='{$pathModIcon16}/active.gif' style='margin-right: .5em;'><span style='padding-right: 3em;'>"
                     . _AM_XFORMS_STATUS_ACTIVE
                     . '</span>'
                     . "    <img src='{$pathModIcon16}/inactive.gif' style='margin-right: .5em;'><span style='padding-right: 3em;'>"
                     . _AM_XFORMS_STATUS_INACTIVE
                     . '</span>'
                     . "    <img src='{$pathModIcon16}/expired.gif' style='margin-right: .5em;'>"
                     . _AM_XFORMS_STATUS_EXPIRED
                     . "\n"
                     . "  </div>\n"
                     . "</div>\n"
                     . "</fieldset>\n";
            }
        }

        /*Show message no forms*/
        if (0 == $totalList) {
            $bshow = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo "  <tr>\n"
                 . "    <td class='odd center' colspan='6'>"
                 . _AM_XFORMS_NO_FORMS
                 . "</td>\n"
                 . "  </tr>\n"
                 . "  </tbody>\n"
                 . "  <tfoot>\n"
                 . "  <tr>\n"
                 . "    <td class='foot'>&nbsp;</td>\n"
                 . "    <td class='foot center'>&nbsp;</td>\n"
                 . "    <td class='foot' colspan='4'>"
                 . $bshow->render()
                 . "</td>\n"
                 . "  </tr>\n"
                 . "  </tfoot>\n"
                 . "</table>\n";
        }
        echo "\n</form>\n";
        break;
    case 'edit':
        $clone  = Request::getInt('clone', 0, 'GET');
        $formId = Request::getInt('form_id', 0, 'GET');
        xoops_cp_header();
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url("browse.php?modules/{$moduleDirName}/assets/css/style.css"));

        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('main.php?op=edit');

        $form          = $xformsFormsHandler->get($formId); // will auto-create if form_id == 0
        $textFormTitle = new \XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, $form->getVar('form_title', 'e'));

        $permHelper = new \Xmf\Module\Helper\Permission($moduleDirName);
        if (0 == $formId) {
            // new form so preselect Administrator group
            $groupIds = [XOOPS_GROUP_ADMIN];
        } else {
            $groupIds = $permHelper->getGroupsForItem($xformsFormsHandler->perm_name, $formId);
            //            $groupIds      = $GLOBALS['grouppermHandler']->getGroupIds($xformsFormsHandler->perm_name, $formId, $helper->getModule()->getVar('mid'));
        }
        $selectFormGroupPerm = new \XoopsFormSelectGroup(_AM_XFORMS_PERM, 'form_group_perm', true, $groupIds, 5, true);

        $selectFormSaveDb = new \XoopsFormRadioYN(_AM_XFORMS_SAVE_DB, 'form_save_db', ((((int)$form->getVar('form_save_db')) > 0) ? 1 : 0), _AM_XFORMS_SAVE_DB_YES, _AM_XFORMS_SAVE_DB_NO);
        $selectFormSaveDb->setDescription(_AM_XFORMS_SAVE_DB_DESC);

        $selectFormSendMethod = new \XoopsFormSelect(_AM_XFORMS_SEND_METHOD, 'form_send_method', $form->getVar('form_send_method'));
        $selectFormSendMethod->addOption(Constants::SEND_METHOD_MAIL, _AM_XFORMS_SEND_METHOD_MAIL);
        $selectFormSendMethod->addOption(Constants::SEND_METHOD_PM, _AM_XFORMS_SEND_METHOD_PM);
        $selectFormSendMethod->addOption(Constants::SEND_METHOD_NONE, _AM_XFORMS_SEND_METHOD_NONE);
        $selectFormSendMethod->setDescription(_AM_XFORMS_SEND_METHOD_DESC);

        $selectFormSendToGroup = new \XoopsFormSelectGroup(_AM_XFORMS_SENDTO, 'form_send_to_group', false, $form->getVar('form_send_to_group'));
        $selectFormSendToGroup->addOption('0', _AM_XFORMS_SENDTO_ADMIN);
        $selectFormSendToGroup->addOption('-1', _AM_XFORMS_SENDTO_OTHER);

        $sendToOther         = $form->getVar('form_send_to_other');
        $textFormSendToOther = new \XoopsFormText(_AM_XFORMS_SENDTO_OTHER_EMAILS, 'form_send_to_other', 50, 255, empty($sendToOther) ? '' : $sendToOther);
        $textFormSendToOther->setDescription(_AM_XFORMS_SENDTO_OTHER_DESC);

        $selectFormSendCopy = new \XoopsFormRadioYN(_AM_XFORMS_SEND_COPY, 'form_send_copy', ((((int)$form->getVar('form_send_copy')) > 0) ? 1 : 0), _YES, _NO);
        $selectFormSendCopy->setDescription(_AM_XFORMS_SEND_COPY_DESC);

        // set same configs for all editors on this page
        $sysHelper     = \Xmf\Module\Helper::getHelper('system');
        $editorConfigs = [
            'editor' => $sysHelper->getConfig('general_editor'),
            'rows'   => 5,
            'cols'   => 90,
            'width'  => '100%',
            'height' => '200px',
        ];

        $editorConfigs        = array_merge($editorConfigs, [
            'name'  => 'form_email_header',
            'value' => $form->getVar('form_email_header', 'e'),
        ]);
        $tareaFormEmailHeader = new \XoopsFormEditor(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $editorConfigs);
        $tareaFormEmailHeader->setDescription(_AM_XFORMS_EMAIL_HEADER_DESC);
        //        $tareaFormEmailHeader = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $form->getVar('form_email_header', 'e'), 5, 90);
        //        $tareaFormEmailHeader->skipPreview = true;
        $renderer = $tareaFormEmailHeader->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailHeader->editor->renderer->skipPreview = true;
        }

        $editorConfigs        = array_merge($editorConfigs, [
            'name'  => 'form_email_footer',
            'value' => $form->getVar('form_email_footer', 'e'),
        ]);
        $tareaFormEmailFooter = new \XoopsFormEditor(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $editorConfigs);
        $tareaFormEmailFooter->setDescription(_AM_XFORMS_EMAIL_FOOTER_DESC);
        //        $tareaFormEmailFooter = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $form->getVar('form_email_footer', 'e'), 5, 90);
        //        $tareaFormEmailFooter->skipPreview = true;
        $renderer = $tareaFormEmailFooter->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailFooter->editor->renderer->skipPreview = true;
        }

        $editorConfigs         = array_merge($editorConfigs, [
            'name'  => 'form_email_uheader',
            'value' => $form->getVar('form_email_uheader', 'e'),
        ]);
        $tareaFormEmailUheader = new \XoopsFormEditor(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $editorConfigs);
        $tareaFormEmailUheader->setDescription(_AM_XFORMS_EMAIL_UHEADER_DESC);
        //        $tareaFormEmailUheader = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $form->getVar('form_email_uheader', 'e'), 5, 90);
        //        $tareaFormEmailUheader->skipPreview = true;
        $renderer = $tareaFormEmailUheader->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailUheader->editor->renderer->skipPreview = true;
        }

        $editorConfigs         = array_merge($editorConfigs, [
            'name'  => 'form_email_ufooter',
            'value' => $form->getVar('form_email_ufooter', 'e'),
        ]);
        $tareaFormEmailUfooter = new \XoopsFormEditor(_AM_XFORMS_EMAIL_UFOOTER, 'form_email_ufooter', $editorConfigs);
        $tareaFormEmailUfooter->setDescription(_AM_XFORMS_EMAIL_UFOOTER_DESC);
        //        $tareaFormEmailUfooter = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UFOOTER, 'form_email_ufooter', $form->getVar('form_email_ufooter', 'e'), 5, 90);
        //        $tareaFormEmailUfooter->skipPreview = true;
        $renderer = $tareaFormEmailUfooter->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailUfooter->editor->renderer->skipPreview = true;
        }

        $selectFormDelimiter = new \XoopsFormSelect(_AM_XFORMS_DELIMETER, 'form_delimiter', $form->getVar('form_delimiter'));
        $selectFormDelimiter->addOption(Constants::DELIMITER_SPACE, _AM_XFORMS_DELIMETER_SPACE);
        $selectFormDelimiter->addOption(Constants::DELIMITER_BR, _AM_XFORMS_DELIMETER_BR);

        $textFormOrder = new Xforms\FormInput(_AM_XFORMS_ORDER, 'form_order', 4, 5, $form->getVar('form_order'), null, 'number');
        $textFormOrder->setAttribute('min', 0);
        $textFormOrder->setExtra('style="width: 4em;"');
        $textFormOrder->setDescription(_AM_XFORMS_ORDER_DESC);

        $submitText           = $form->getVar('form_submit_text');
        $submitFormSubmitText = new \XoopsFormText(_AM_XFORMS_SUBMIT_TEXT, 'form_submit_text', 50, 50, empty($submitText) ? _SUBMIT : $submitText);

        $editorConfigs = array_merge($editorConfigs, ['name' => 'form_desc', 'value' => $form->getVar('form_desc', 'e')]);
        $tareaFormDesc = new \XoopsFormEditor(_AM_XFORMS_DESC, 'form_desc', $editorConfigs);
        $tareaFormDesc->setDescription(_AM_XFORMS_DESC_DESC);
        //        $tareaFormDesc = new \XoopsFormDhtmlTextArea(_AM_XFORMS_DESC, 'form_desc', $form->getVar('form_desc', 'e'), 5, 90);
        //        $tareaFormDesc->skipPreview = true;
        $renderer = $tareaFormDesc->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormDesc->editor->renderer->skipPreview = true;
        }

        $editorConfigs  = array_merge($editorConfigs, ['name' => 'form_intro', 'value' => $form->getVar('form_intro', 'e')]);
        $tareaFormIntro = new \XoopsFormEditor(_AM_XFORMS_INTRO, 'form_intro', $editorConfigs);
        $tareaFormIntro->setDescription(_AM_XFORMS_INTRO_DESC);
        //        $tareaFormIntro = new \XoopsFormDhtmlTextArea(_AM_XFORMS_INTRO, 'form_intro', $form->getVar('form_intro', 'e'), 5, 90);
        //        $tareaFormIntro->skipPreview = true;
        $renderer = $tareaFormIntro->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormIntro->editor->renderer->skipPreview = true;
        }

        $textFormContactLabel = new \XoopsFormLabel("<span style='font-weight: bold; font-size: larger;'>" . _AM_FORMS_CONTACT_INFO . '</span>', '', 'contact_label');

        $textFormWhereTo = new \XoopsFormText(_AM_XFORMS_WHERETO, 'form_whereto', 50, 255, $form->getVar('form_whereto'));
        $textFormWhereTo->setDescription(_AM_XFORMS_WHERETO_DESC);

        $selectFormDisplayStyle = new \XoopsFormSelect(_AM_XFORMS_DISPLAY_STYLE, 'form_display_style', $form->getVar('form_display_style'));
        $selectFormDisplayStyle->addOption(Constants::FORM_DISPLAY_STYLE_FORM, _AM_XFORMS_DISPLAY_STYLE_FORM);
        $selectFormDisplayStyle->addOption(Constants::FORM_DISPLAY_STYLE_POLL, _AM_XFORMS_DISPLAY_STYLE_POLL);
        $selectFormDisplayStyle->setDescription(_AM_XFORMS_DISPLAY_STYLE_DESC);

        $radioFormDefineBegin = new \XoopsFormRadioYN(_AM_XFORMS_DEFINE_BEGIN, 'define_form_begin', (((int)$form->getVar('form_begin') > 0) ? 1 : 0), _YES, _NO);
        $textFormBegin        = new \XoopsFormDateTime(_AM_XFORMS_BEGIN, 'form_begin', 15, $form->getVar('form_begin'));
        $beginTray            = new \XoopsFormElementTray(_AM_XFORMS_BEGIN, '<br>');
        $beginTray->addElement($radioFormDefineBegin);
        $beginTray->addElement($textFormBegin);
        $beginTray->setDescription(_AM_XFORMS_DEFINE_BEGIN_DESC);

        $radioFormDefineEnd = new \XoopsFormRadioYN(_AM_XFORMS_DEFINE_END, 'define_form_end', (((int)$form->getVar('form_end') > 0) ? 1 : 0), _YES, _NO);
        $textFormEnd        = new \XoopsFormDateTime(_AM_XFORMS_END, 'form_end', 15, $form->getVar('form_end'));
        $endTray            = new \XoopsFormElementTray(_AM_XFORMS_END, '<br>');
        $endTray->addElement($radioFormDefineEnd);
        $endTray->addElement($textFormEnd);
        $endTray->setDescription(_AM_XFORMS_DEFINE_END_DESC);

        $selectFormActive = new \XoopsFormRadioYN(_AM_XFORMS_ACTIVE, 'form_active', (((int)$form->getVar('form_active') > 0) ? 1 : 0), _YES, _NO);
        $selectFormActive->setDescription(_AM_XFORMS_ACTIVE_DESC);

        $hiddenOp = new \XoopsFormHidden('op', 'saveform');
        $submit   = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE, 'submit');
        $submit1  = new \XoopsFormButton('', 'submit', _AM_XFORMS_SAVE_THEN_ELEMENTS, 'submit');
        $tray     = new \XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);

        $hiddenFormId = $cloneFormId = '';

        if (empty($formId)) {
            $caption = _AM_XFORMS_NEW;
        } else {
            if ($clone) {
                $caption       = sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title'));
                $cloneFormId   = new \XoopsFormHidden('clone_form_id', $formId);
                $textFormTitle = new \XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title', 'e')));
            } else {
                $caption      = sprintf(_AM_XFORMS_EDIT, $form->getVar('form_title'));
                $hiddenFormId = new \XoopsFormHidden('form_id', $formId);
            }
        }
        $output = new \XoopsThemeForm($caption, 'editform', $_SERVER['PHP_SELF'], 'post', true);
        $output->addElement($textFormTitle, true);
        $output->addElement($tareaFormDesc);
        $output->addElement($selectFormActive);
        $output->addElement($textFormOrder);
        $output->addElement($selectFormDisplayStyle);
        $output->addElement($beginTray);
        $output->addElement($endTray);
        $output->addElement($tareaFormIntro);
        $output->addElement($selectFormDelimiter);
        $output->addElement($submitFormSubmitText, true);
        $output->addElement($textFormWhereTo);
        $output->addElement($selectFormGroupPerm);
        $output->addElement($selectFormSaveDb);
        $output->addElement($textFormContactLabel);
        $output->addElement($selectFormSendMethod);
        $output->addElement($selectFormSendToGroup);
        $output->addElement($textFormSendToOther);
        $output->addElement($selectFormSendCopy);
        $output->addElement($tareaFormEmailHeader);
        $output->addElement($tareaFormEmailFooter);
        $output->addElement($tareaFormEmailUheader);
        $output->addElement($tareaFormEmailUfooter);
        $output->addElement($hiddenOp);
        if ($hiddenFormId instanceof XoopsFormHidden) {
            $output->addElement($hiddenFormId);
        }
        if ($cloneFormId instanceof XoopsFormHidden) {
            $output->addElement($cloneFormId);
        }
        $output->addElement($tray);
        $output->display();
        break;
    case 'delete':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = Request::getInt('form_id', 0, 'GET');
            if ($formId) {
                $xformsFormsHandler = $helper->getHandler('Forms');
                $formObj            = $xformsFormsHandler->get($formId);
                $formTitle          = $formObj->getVar('form_title');
                xoops_confirm(['op' => 'delete', 'form_id' => $formId, 'ok' => 1], $_SERVER['PHP_SELF'], sprintf(_AM_XFORMS_CONFIRM_DELETE, $formTitle));
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }

            $formId = Request::getInt('form_id', 0, 'POST');
            if (!empty($formId) && ($formObj = $xformsFormsHandler->get($formId)) && !$formObj->isNew()) {
                if ($xformsFormsHandler->delete($formObj)) {
                    //form deleted so now delete the elements
                    $xformsEleHandler = $helper->getHandler('Element');
                    $criteria         = new \Criteria('form_id', $formId);
                    $xformsEleHandler->deleteAll($criteria);

                    //delete the userdata (report info) for this form
                    $uDataHandler = $helper->getHandler('Userdata');
                    $uDataHandler->deleteAll($criteria);

                    //and now delete the form's permissions too
                    $xformsFormsHandler->deleteFormPermissions($formId);
                    redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $formObj->getHtmlErrors();
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;
    case 'active':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = Request::getInt('form_id', 0, 'GET');
            if ($formId) {
                $xformsFormsHandler = $helper->getHandler('Forms');
                $formObj            = $xformsFormsHandler->get($formId);
                $formTitle          = $formObj->getVar('form_title');
                xoops_confirm(['op' => 'active', 'form_id' => $formId, 'ok' => 1], $_SERVER['PHP_SELF'], sprintf(_AM_XFORMS_CONFIRM_ACTIVE, $formTitle));
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            $formId             = Request::getInt('form_id', 0, 'POST');
            $xformsFormsHandler = $helper->getHandler('Forms');
            if (!empty($formId) && ($formObj = $xformsFormsHandler->get($formId)) && !$formObj->isNew()) {
                if ($xformsFormsHandler->setActive($formObj)) {
                    redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $formObj->getHtmlErrors();
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;
    case 'inactive':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = Request::getInt('form_id', 0, 'GET');
            if ($formId) {
                $xformsFormsHandler = $helper->getHandler('Forms');
                $formObj            = $xformsFormsHandler->get($formId);
                $formTitle          = $formObj->getVar('form_title');
                xoops_confirm(['op' => 'inactive', 'form_id' => $formId, 'ok' => 1], $_SERVER['PHP_SELF'], sprintf(_AM_XFORMS_CONFIRM_INACTIVE, $formTitle));
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            $formId             = Request::getInt('form_id', 0, 'POST');
            $xformsFormsHandler = $helper->getHandler('Forms');
            if (!empty($formId) && ($formObj = $xformsFormsHandler->get($formId)) && !$formObj->isNew()) {
                if ($xformsFormsHandler->setInactive($formObj)) {
                    redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $form->getHtmlErrors();
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;
    case 'saveorder':
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }

        $ids = Request::getArray('ids', [], 'POST');
        if (empty($ids)) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        $ids = array_map('intval', $ids); //sanitize the array
        // now get and filter the order too
        $order = Request::getArray('order', [], 'POST');
        array_walk($order, 'xformsIntArray'); // can't use array_map since must preserve keys
        foreach ($ids as $id) {
            $form = $xformsFormsHandler->get($id);
            $form->setVar('form_order', $order[$id]);
            $xformsFormsHandler->insert($form);
        }
        redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        break;
    case 'saveform':
        if (!isset($_POST['submit'])) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        // check security
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }

        $formSaveDb     = Request::getInt('form_save_db', 0, 'POST');
        $formSendMethod = Request::getCmd('form_save_method', '', 'POST');
        $formId         = Request::getInt('form_id', 0, 'POST');

        if ((0 == $formSaveDb) && (Constants::SEND_METHOD_NONE == $formSendMethod)) {
            redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SAVESENT);
        }

        $error = '';
        $form  = $xformsFormsHandler->get($formId);

        $formSendToGroup  = Request::getInt('form_send_to_group', 0, 'POST');
        $formSendToOther  = Request::getString('form_send_to_other', '', 'POST');
        $formSendCopy     = Request::getInt('form_send_copy', '', 'POST');
        $formSendMethod   = Request::getWord('form_send_method', 'POST');
        $formEmailHeader  = Request::getText('form_email_header', 'POST');
        $formEmailFooter  = Request::getText('form_email_footer', '', 'POST');
        $formEmailUheader = Request::getText('form_email_uheader', '', 'POST');
        $formEmailUfooter = Request::getText('form_email_ufooter', '', 'POST');
        $formType         = Request::getWord('form_type', 'XoopsThemeForm', 'POST');
        $formOrder        = Request::getInt('form_order', 0, 'POST');
        $formDelimiter    = Request::getString('form_delimiter', '', 'POST');
        $formTitle        = Request::getString('form_title', '', 'POST');
        $formSubmitText   = Request::getText('form_submit_text', '', 'POST');
        $formDesc         = Request::getText('form_desc', '', 'POST');
        $formIntro        = Request::getText('form_intro', '', 'POST');
        $formWhereTo      = Request::getString('form_whereto', '', 'POST');
        $formDisplayStyle = Request::getCmd('form_display_style', '', 'POST');
        $defineFormBegin  = Request::getInt('define_form_begin', 0, 'POST');
        $defineFormEnd    = Request::getInt('define_form_end', 0, 'POST');
        $formActive       = Request::getInt('form_active', 0, 'POST');

        //validate list of other email addresses
        $sToO     = !empty($FormSendToOther) ? explode(';', $formSendToOther) : [];
        $valArray = [];
        foreach ($sToO as $oEmail) {
            if ($valEmail = filter_var($oEmail, FILTER_VALIDATE_EMAIL)) {
                $valArray[] = $valEmail;
            }
        }
        $formSendToOther = !empty($valArray) ? implode(';', $valArray) : '';

        $form->setVars([
                           'form_send_to_group' => $formSendToGroup,
                           'form_send_to_other' => $formSendToOther,
                           'form_send_copy'     => $formSendCopy,
                           'form_send_method'   => $formSendMethod,
                           'form_email_header'  => $formEmailHeader,
                           'form_email_footer'  => $formEmailFooter,
                           'form_email_uheader' => $formEmailUheader,
                           'form_email_ufooter' => $formEmailUfooter,
                           'form_type'          => $formType,
                           'form_order'         => $formOrder,
                           'form_delimiter'     => $formDelimiter,
                           'form_title'         => $formTitle,
                           'form_submit_text'   => $formSubmitText,
                           'form_desc'          => $formDesc,
                           'form_intro'         => $formIntro,
                           'form_whereto'       => $formWhereTo,
                           'form_display_style' => $formDisplayStyle,
                           'form_begin'         => 0,
                           'form_active'        => $formActive,
                       ]);

        if (0 != $defineFormBegin) {
            $formBegin = Request::getArray('form_begin', ['date' => getdate(), 'time' => 0], 'POST');
            $formBegin = strtotime($formBegin['date']) + $formBegin['time'];
            $form->setVar('form_begin', (int)$formBegin);
        }

        if (0 != $defineFormEnd) {
            $formEnd = Request::getArray('form_end', ['date' => getdate(), 'time' => 0], 'POST');
            $formEnd = strtotime($formEnd['date']) + $formEnd['time'];
        } else {
            $formEnd = 0;
        }
        $form->setVar('form_end', (int)$formEnd);

        // now update the form
        if (!$ret = $xformsFormsHandler->insert($form)) {
            $error = $form->getHtmlErrors();
        } else {
            $xformsFormsHandler->deleteFormPermissions($ret);

            $formGroupPerm = Request::getArray('form_group_perm', [], 'POST');
            if (count($formGroupPerm > 0)) {
                $xformsFormsHandler->insertFormPermissions($ret, $formGroupPerm);
            }
            if (!empty($cloneFormId)) {
                $xformsEleHandler = $helper->getHandler('Element');
                $criteria         = new \Criteria('form_id', $cloneFormId);
                $count            = $xformsEleHandler->getCount($criteria);
                if ($count > 0) {
                    $elements = $xformsEleHandler->getObjects($criteria);
                    foreach ($elements as $e) {
                        $cloned = $e->xoopsClone();
                        $cloned->setVar('form_id', $ret);
                        if (!$xformsEleHandler->insert($cloned)) {
                            $error .= $cloned->getHtmlErrors();
                        }
                    }
                }
            } elseif (empty($formId)) {
                $xformsEleHandler = $helper->getHandler('Element');
                $error            = $xformsEleHandler->insertDefaults($ret);
            }
        }
        if (!empty($error)) {
            xoops_cp_header();
            echo $error;
        } else {
            if (_AM_XFORMS_SAVE_THEN_ELEMENTS == $_POST['submit']) {
                redirect_header($helper->url("admin/elements.php?form_id={$ret}"), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            } else {
                redirect_header($_SERVER['PHP_SELF'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            }
        }
        break;
}

require_once __DIR__ . '/admin_footer.php';
xoops_cp_footer();
