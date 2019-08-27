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
 * Main administration page
 *
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
use \XoopsModules\Xforms\Constants;
use \XoopsModules\Xforms\FormInput;

require_once __DIR__ . '/admin_header.php';
$myts = \MyTextSanitizer::getInstance();

$op        = Xmf\Request::getCmd('op', 'list');
$showAll   = \Xmf\Request::getBool('showall', false, 'POST');
$saveOrder = \Xmf\Request::getBool('saveorder', false, 'POST');

if ($showAll) {
    $op = 'list';
} elseif ($saveOrder) {
    $op = 'saveorder';
}

switch ($op) {
    case 'list':
    default:
        xoops_cp_header();
        /* @var \Xmf\Module\Admin $adminObject */
        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->addItemButton(_AM_XFORMS_NEW, basename(__FILE__) . '?op=edit', 'add');
        $adminObject->displayButton('left');

        /* @var \XoopsModules\Xforms\Helper $helper */
        $perpage = (int)$helper->getConfig('perpage'); // Get # of forms to show per page

        // Group all the page items together
        $xformsDisplay = new \stdClass;
        $xformsDisplay->start   = \Xmf\Request::getInt('start', 0);
        $xformsDisplay->perpage = ($perpage > 0) ? $perpage: Constants::FORMS_PER_PAGE_DEFAULT;
        $xformsDisplay->order   = 'ASC';
        $xformsDisplay->sort    = 'form_order';

        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url('browse.php?modules/' . $moduleDirName . '/assets/css/style.css'));

        echo '<form action="' . basename(__FILE__) . '" method="post">'
           . '<table class="outer width100 bspacing1">'
           . '  <thead>'
           . '  <tr><th colspan="6">' . _AM_XFORMS_LISTING . '</th></tr>'
           . '  <tr>'
           . '    <td class="head center bottom width5">' . _AM_XFORMS_NO . '</td>'
           . '    <td class="head center bottom">' . _AM_XFORMS_TITLE . '</td>'
           . '    <td class="head center bottom width10">' . _AM_XFORMS_ORDER . '<br>' . _AM_XFORMS_ORDER_DESC . '</td>'
           . '    <td class="head center bottom width5">' . _AM_XFORMS_STATUS . '</td>'
           . '    <td class="head center bottom width15">' . _AM_XFORMS_SENDTO . '</td>'
           . '    <td class="head center bottom width10">' . _AM_XFORMS_ACTION . '</td>'
           . '  </tr>'
           . '  </thead>'
           . '  <tbody>';

        $totalList    = 0;
         /* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
        $ttlFormCount = $formsHandler->getCount(); // count of all forms

        // Get the forms we're interested in
        $criteria = ($showAll) ? new \Criteria(1, 1) : new \Criteria('form_active', Constants::FORM_INACTIVE, '<>');
        $criteria->setStart($xformsDisplay->start);
        $criteria->setLimit($xformsDisplay->perpage);
        $criteria->setSort($xformsDisplay->sort);
        $criteria->order = $xformsDisplay->order;
        $forms = $formsHandler->getAll($criteria, null, true, false);

        if (!empty($forms)) {
            // get the UserData to see if there's any reports
            $criteria = new \CriteriaCompo();
            $criteria->setGroupBy('form_id');
            $uDataHandler  = $helper->getHandler('UserData');
            $rptCountArray = $uDataHandler->getCounts($criteria);
            $groupHandler  = xoops_getHandler('group');

            foreach ($forms as $f) {
                /* @var \XoopsModules\Xforms\Forms $f */
                if ($showAll || $f->isActive()) {
                    $id    = (int)$f->getVar('form_id');
                    $order = new FormInput('', 'order[' . $id . ']', 5, 5, $f->getVar('form_order'), null, 'number');
                    $order->setAttribute('min', 0);
                    $order->setExtra('style="width: 5em;"');
                    $sendTo = (int)$f->getVar('form_send_to_group');
                    switch ($sendTo) {
                        case Constants::SEND_TO_OTHER:
                            $sendToTxt = '<b>' . _AM_XFORMS_SENDTO_OTHER . ': </b>' . $f->getVar('form_send_to_other');
                            break;
                        case Constants::SEND_TO_NONE:
                            $sendToTxt = _AM_XFORMS_SENDTO_ADMIN;
                            break;
                        default:
                            $sendToTxt = _AM_XFORMS_SENDTO_ADMIN;
                            if ($group = $groupHandler->get($sendTo)) {
                                $sendToTxt = $group->getVar('name');
                            }
                            break;
                    }

                    if (!$f->isActive()) {
                        // Form is either inactive or expired
                        if (Constants::FORM_INACTIVE === (int)$f->getVar('form_active')) {
                            // Form is inactive
                            $fStatus = '<img src="' . $mypathIcon16 . '/inactive.gif" title="' . _AM_XFORMS_STATUS_INACTIVE
                                     . '" alt="' . _AM_XFORMS_STATUS_INACTIVE . '">';
                            $fAction = ' <a href="' . $_SERVER['SCRIPT_NAME'] . '?op=active&form_id=' . $id . '">'
                                     . '<img src="' . $mypathIcon16 . '/active.gif" class="tooltip floatcenter1" title="'
                                     . _AM_XFORMS_ACTION_ACTIVE . '" alt="' . _AM_XFORMS_ACTION_ACTIVE . '"></a>';
                        } else {
                            // Form has expired
                            $fStatus = '<img src="' . $mypathIcon16 . '/expired.gif" title="' . _AM_XFORMS_STATUS_EXPIRED
                                     . '" alt="' . _AM_XFORMS_STATUS_EXPIRED . '">';
                            $fAction = '';
                        }
                    } else {
                        // Form is active
                        $fStatus = '<img src="' . $mypathIcon16 . '/active.gif" title="' . _AM_XFORMS_STATUS_ACTIVE
                                 . '" alt="' . _AM_XFORMS_STATUS_ACTIVE . '">';
                        $fAction = '<a href="' . $_SERVER['SCRIPT_NAME'] . '?op=inactive&form_id=' . $id . '">'
                                 . '<img src="' . $mypathIcon16 . '/inactive.gif" class="tooltip floatcenter1" title="'
                                 . _AM_XFORMS_ACTION_INACTIVE . '" alt="' . _AM_XFORMS_ACTION_INACTIVE . '"></a>';
                    }
                    $ids = new \XoopsFormHidden('ids[]', $id);
                    echo '  <tr>'
                       . '    <td class="odd middle center">' . $id . '</td>'
                       . '    <td class="even middle"><a href="' . $helper->url('index.php?form_id=' . $id) . '" title="' . _AM_XFORMS_ACTION_VIEWFORM . '">' . $f->getVar('form_title', 's') . '</a><br>'
                       . '      ' . $f->getVar('form_desc', 's')
                       . '    </td>'
                       . '    <td class="odd middle center">' . $order->render() . '</td>'
                       . '    <td class="even middle center">' . $fStatus . '</td>'
                       . '    <td class="odd middle center">' . $sendToTxt . '</td>'
                       . '    <td class="even middle center" nowrap="nowrap">'
                           . '      <a href="' . $_SERVER['SCRIPT_NAME'] . '?op=edit&form_id=' . $id . '"><img src="' . \Xmf\Module\Admin::iconUrl('edit.png', '16') . '" class="tooltip floatcenter1" title="' . _AM_XFORMS_ACTION_EDITFORM . '" alt="'
                       .        _AM_XFORMS_ACTION_EDITFORM . '"></a>'
                           . '      <a href="elements.php?form_id=' . $id . '"><img src="' . \Xmf\Module\Admin::iconUrl('inserttable.png', '16') . '" class="tooltip floatcenter1" title="' . _AM_XFORMS_ACTION_EDITELEMENT . '" alt="'
                       .        _AM_XFORMS_ACTION_EDITELEMENT . '"></a>'
                       .        $fAction
                       . '      <a href="' . $_SERVER['SCRIPT_NAME'] . '?op=edit&clone=1&form_id=' . $id . '"><img src="' . \Xmf\Module\Admin::iconUrl('editcopy.png', '16') . '" class="tooltip floatcenter1" title="' . _AM_XFORMS_ACTION_CLONE . '" alt="'
                       .        _AM_XFORMS_ACTION_CLONE . '"></a>'
                           . '      <a href="' . $_SERVER['SCRIPT_NAME'] . '?op=delete&form_id=' . $id . '"><img src="' . \Xmf\Module\Admin::iconUrl('delete.png', '16') . '" class="tooltip floatcenter1" title="' . _DELETE . '" alt="' . _DELETE . '"></a>'
                       .        $ids->render()
                       . '      <a target="_blank" href="' . $helper->url('index.php?form_id=' . $id) . '"><img src="' . \Xmf\Module\Admin::iconUrl('view.png', '16') . '" class="tooltip floatcenter1" title="' . _AM_XFORMS_ACTION_VIEWFORM . '" alt="'
                       .        _AM_XFORMS_ACTION_VIEWFORM . '"></a>';

                    if (Constants::SAVE_IN_DB === (int)$f->getVar('form_save_db') && isset($rptCountArray[$id])) {
                        echo '      <a href="report.php?op=show&form_id=' . $id . '"><img src="' . $mypathIcon16 . '/content.png" class="tooltip floatcenter1" title="' . _AM_XFORMS_ACTION_REPORT . '" alt="'
                             .      _AM_XFORMS_ACTION_REPORT . '"></a>';
                    }
                    echo '    </td>'
                       . '  </tr>';

                    ++$totalList;
                }
            }
            if ($totalList > 0) {
                $submit = new \XoopsFormButton('', 'saveorder', _AM_XFORMS_RESET_ORDER, 'submit');
                $bshow  = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
                echo '  </tbody>'
                   . '  <tfoot>'
                   . '  <tr>'
                   . '    <td class="foot" colspan="2">' . $bshow->render() . '</td>'
                   . '    <td class="foot center">' . $submit->render() . $xoopsSecurity->getTokenHTML() . '</td>'
                   . '    <td class="foot" colspan="3">&nbsp;</td>'
                   . '  </tr>'
                   . '  </tfoot>'
                   . '</table><br><br>';

                if ($ttlFormCount > $xformsDisplay->perpage) {
                    xoops_load('pagenav');
                    $xformsPagenav = new \XoopsPageNav($ttlFormCount, $xformsDisplay->perpage, $xformsDisplay->start, 'start', 'perpage=' . $xformsDisplay->perpage);
                    echo '<div class="center middle larger width100 line160">' . $xformsPagenav->renderNav() . '</div>';
                }

                echo '<fieldset><legend class="bold" style="color: #900;">' . _AM_XFORMS_STATUS_INFORMATION . '</legend>'
                   . '<div class="pad7">'
                   . '  <div class="center">'
                   . '    <img src="' . $mypathIcon16 . '/active.gif" style="margin-right: .5em;"><span style="padding-right: 3em;">' . _AM_XFORMS_STATUS_ACTIVE . '</span>'
                   . '    <img src="' . $mypathIcon16 . '/inactive.gif" style="margin-right: .5em;"><span style="padding-right: 3em;">' . _AM_XFORMS_STATUS_INACTIVE . '</span>'
                   . '    <img src="' . $mypathIcon16 . '/expired.gif" style="margin-right: .5em;">' . _AM_XFORMS_STATUS_EXPIRED
                   . '  </div>'
                   . '</div>'
                   . '</fieldset>';
            }
        }

        /*Show message no forms*/
        if (0 === $totalList) {
            $bshow = new \XoopsFormButton('', ($showAll ? 'shownormal' : 'showall'), ($showAll ? _AM_XFORMS_SHOW_NORMAL_FORMS : _AM_XFORMS_SHOW_ALL_FORMS), 'submit');
            echo '  <tr>'
               . '    <td class="odd center" colspan="6"><a href="' . basename(__FILE__) . '?op=edit" target="_self">' . _AM_XFORMS_NO_FORMS . '</a></td>'
               . '  </tr>'
               . '  </tbody>'
               . '  <tfoot>'
               . '  <tr>'
               . '    <td class="foot">&nbsp;</td>'
               . '    <td class="foot center">&nbsp;</td>'
               . '    <td class="foot" colspan="4">' . $bshow->render() . '</td>'
               . '  </tr>'
               . '  </tfoot>'
               . '</table>';
        }
        echo '</form>';
        break;

    case 'edit':
        $clone  = \Xmf\Request::getInt('clone', 0, 'GET');
        $formId = \Xmf\Request::getInt('form_id', 0, 'GET');
        xoops_cp_header();
        $GLOBALS['xoTheme']->addStylesheet($GLOBALS['xoops']->url('browse.php?modules/' . $moduleDirName . '/assets/css/style.css'));

        $adminObject->displayNavigation(basename(__FILE__) . '?op=edit');

        $form          = $formsHandler->get($formId); // will auto-create if form_id == 0
        $textFormTitle = new \XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, $form->getVar('form_title', 'e'));

        $permHelper = new \Xmf\Module\Helper\Permission($moduleDirName);
        if (0 === (int)$formId) {
            // new form so preselect Administrator group
            $groupIds = array(XOOPS_GROUP_ADMIN);
        } else {
            $groupIds = $permHelper->getGroupsForItem($formsHandler->perm_name, $formId);
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
        $editorConfigs = array('editor' => $sysHelper->getConfig('general_editor'),
                                 'rows' => 5,
                                 'cols' => 90,
                                'width' => '100%',
                               'height' => '200px'
        );

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_email_header', 'value' => $form->getVar('form_email_header', 'e')));
        $tareaFormEmailHeader = new \XoopsFormEditor(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $editorConfigs);
        $tareaFormEmailHeader->setDescription(_AM_XFORMS_EMAIL_HEADER_DESC);
//        $tareaFormEmailHeader = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_HEADER, 'form_email_header', $form->getVar('form_email_header', 'e'), 5, 90);
//        $tareaFormEmailHeader->skipPreview = true;
        $renderer = $tareaFormEmailHeader->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailHeader->editor->renderer->skipPreview = true;
        }

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_email_footer', 'value' => $form->getVar('form_email_footer', 'e')));
        $tareaFormEmailFooter = new \XoopsFormEditor(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $editorConfigs);
        $tareaFormEmailFooter->setDescription(_AM_XFORMS_EMAIL_FOOTER_DESC);
//        $tareaFormEmailFooter = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_FOOTER, 'form_email_footer', $form->getVar('form_email_footer', 'e'), 5, 90);
//        $tareaFormEmailFooter->skipPreview = true;
        $renderer = $tareaFormEmailFooter->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailFooter->editor->renderer->skipPreview = true;
        }

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_email_uheader', 'value' => $form->getVar('form_email_uheader', 'e')));
        $tareaFormEmailUheader = new \XoopsFormEditor(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $editorConfigs);
        $tareaFormEmailUheader->setDescription(_AM_XFORMS_EMAIL_UHEADER_DESC);
//        $tareaFormEmailUheader = new \XoopsFormDhtmlTextArea(_AM_XFORMS_EMAIL_UHEADER, 'form_email_uheader', $form->getVar('form_email_uheader', 'e'), 5, 90);
//        $tareaFormEmailUheader->skipPreview = true;
        $renderer = $tareaFormEmailUheader->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormEmailUheader->editor->renderer->skipPreview = true;
        }

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_email_ufooter', 'value' => $form->getVar('form_email_ufooter', 'e')));
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

        $textFormOrder = new FormInput(_AM_XFORMS_ORDER, 'form_order', 4, 5, $form->getVar('form_order'), null, 'number');
        $textFormOrder->setAttribute('min', 0);
        $textFormOrder->setExtra('style="width: 4em;"');
        $textFormOrder->setDescription(_AM_XFORMS_ORDER_DESC);

        $submitText           = $form->getVar('form_submit_text');
        $submitFormSubmitText = new \XoopsFormText(_AM_XFORMS_SUBMIT_TEXT, 'form_submit_text', 50, 50, empty($submitText) ? _SUBMIT : $submitText);

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_desc', 'value' => $form->getVar('form_desc', 'e')));
        $tareaFormDesc = new \XoopsFormEditor(_AM_XFORMS_DESC, 'form_desc', $editorConfigs);
        $tareaFormDesc->setDescription(_AM_XFORMS_DESC_DESC);
//        $tareaFormDesc = new \XoopsFormDhtmlTextArea(_AM_XFORMS_DESC, 'form_desc', $form->getVar('form_desc', 'e'), 5, 90);
//        $tareaFormDesc->skipPreview = true;
        $renderer = $tareaFormDesc->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormDesc->editor->renderer->skipPreview = true;
        }

        $editorConfigs = array_merge($editorConfigs, array('name' => 'form_intro', 'value' => $form->getVar('form_intro', 'e')));
        $tareaFormIntro = new \XoopsFormEditor(_AM_XFORMS_INTRO, 'form_intro', $editorConfigs);
        $tareaFormIntro->setDescription(_AM_XFORMS_INTRO_DESC);
//        $tareaFormIntro = new \XoopsFormDhtmlTextArea(_AM_XFORMS_INTRO, 'form_intro', $form->getVar('form_intro', 'e'), 5, 90);
//        $tareaFormIntro->skipPreview = true;
        $renderer = $tareaFormIntro->editor->renderer;
        if (property_exists($renderer, 'skipPreview')) {
            $tareaFormIntro->editor->renderer->skipPreview = true;
        }

        $textFormContactLabel = new \XoopsFormLabel('<span style="font-weight: bold; font-size: larger;">' . _AM_FORMS_CONTACT_INFO . '</span>', '', 'contact_label');

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
        $submit2  = new \XoopsFormButton('', 'gotoform', _CANCEL);
        $submit2->setExtra("onclick=\"window.location.href='" . $helper->url('admin/main.php') . "'\"");
        $tray     = new \XoopsFormElementTray('');
        $tray->addElement($submit);
        $tray->addElement($submit1);
        $tray->addElement($submit2);

        $hiddenFormId = $cloneFormId = '';

        if (empty($formId)) {
            $caption = _AM_XFORMS_NEW;
        } else {
            if ($clone) {
                $caption       = sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title'));
                $cloneFormId   = new \XoopsFormHidden('clone_form_id', $formId);
                $textFormTitle = new \XoopsFormText(_AM_XFORMS_TITLE, 'form_title', 50, 255, sprintf(_AM_XFORMS_COPIED, $form->getVar('form_title', 'e')));
            } else {
                $caption       = sprintf(_AM_XFORMS_EDIT, $form->getVar('form_title'));
                $hiddenFormId = new \XoopsFormHidden('form_id', $formId);
            }
        }
        $output = new \XoopsThemeForm($caption, 'editform', $_SERVER['SCRIPT_NAME'], 'post', true);
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
        if ($hiddenFormId instanceof \XoopsFormHidden) {
            $output->addElement($hiddenFormId);
        }
        if ($cloneFormId instanceof \XoopsFormHidden) {
            $output->addElement($cloneFormId);
        }
        $output->addElement($tray);
        $output->display();
        break;

    case 'delete':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = \Xmf\Request::getInt('form_id', 0, 'GET');
            if ($formId) {
                //$formsHandler = $helper->getHandler('Forms');
                $formObj            = $formsHandler->get($formId);
                $formTitle          = $formObj->getVar('form_title');
                xoops_confirm(array('op' => 'delete', 'form_id' => $formId, 'ok' => 1), $_SERVER['SCRIPT_NAME'], sprintf(_AM_XFORMS_CONFIRM_DELETE, $formTitle));
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }

        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }

            $formId = \Xmf\Request::getInt('form_id', 0, 'POST');
            if (!empty($formId) && ($formObj = $formsHandler->get($formId)) && !$formObj->isNew()) {
                if ($formsHandler->delete($formObj)) {
                    //form deleted so now delete the elements
                    $xformsEleHandler = $helper->getHandler('Element');
                    $criteria         = new \Criteria('form_id', $formId);
                    $xformsEleHandler->deleteAll($criteria);

                    //delete the userdata (report info) for this form
                    $uDataHandler = $helper->getHandler('UserData');
                    $uDataHandler->deleteAll($criteria);

                    //and now delete the form's permissions too
                    $formsHandler->deleteFormPermissions($formId);
                    redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $formObj->getHtmlErrors();
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;

    case 'active':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = \Xmf\Request::getInt('form_id', 0,  'GET');
            if ($formId) {
                //$formsHandler = $helper->getHandler('Forms');
                $formObj            = $formsHandler->get($formId);
                $formTitle          = $formObj->getVar('form_title');
                xoops_confirm(array('op' => 'active', 'form_id' => $formId, 'ok' => Constants::CONFIRM_OK), $_SERVER['SCRIPT_NAME'], sprintf(_AM_XFORMS_CONFIRM_ACTIVE, $formTitle));
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            $formId = \Xmf\Request::getInt('form_id', 0, 'POST');
            //$formsHandler = $helper->getHandler('Forms');
            if (!empty($formId) && ($formObj = $formsHandler->get($formId)) && !$formObj->isNew()) {
                if ($formsHandler->setActive($formObj)) {
                    redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $formObj->getHtmlErrors();
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;

    case 'inactive':
        if (empty($_POST['ok'])) {
            xoops_cp_header();
            $formId = \Xmf\Request::getInt('form_id', 0,  'GET');
            if ($formId) {
                //$formsHandler = $helper->getHandler('Forms');
                $formObj   = $formsHandler->get($formId);
                $formTitle = $formObj->getVar('form_title');
                xoops_confirm(array('op' => 'inactive', 'form_id' => $formId, 'ok' => 1), $_SERVER['SCRIPT_NAME'], sprintf(_AM_XFORMS_CONFIRM_INACTIVE, $formTitle));
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_FORM_NOTEXISTS);
            }
        } else {
            if (!$xoopsSecurity->check()) {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
            }
            $formId = \Xmf\Request::getInt('form_id', 0, 'POST');
            //$formsHandler = $helper->getHandler('Forms');
            if (!empty($formId) && ($formObj = $formsHandler->get($formId)) && !$formObj->isNew()) {
                if ($formsHandler->setInactive($formObj)) {
                    redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
                }
                xoops_cp_header();
                echo $form->getHtmlErrors();
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
            }
        }
        break;

    case 'saveorder':
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }

        $ids = \Xmf\Request::getArray('ids', array(), 'POST');
        if (empty($ids)) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        $ids = array_map('intval', $ids); //sanitize the array
        // now get and filter the order too
        $order = \Xmf\Request::getArray('order', array(), 'POST');
        array_walk($order, '\XoopsModules\Xforms\Utility::intArray'); // can't use array_map since must preserve keys
        foreach ($ids as $id) {
            $form = $formsHandler->get($id);
            $form->setVar('form_order', $order[$id]);
            $formsHandler->insert($form);
        }
        redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
        break;

    case 'saveform':
        if (!isset($_POST['submit'])) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
        }
        // check security
        if (!$xoopsSecurity->check()) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $xoopsSecurity->getErrors()));
        }

        $formSaveDb     = \Xmf\Request::getInt('form_save_db', 0, 'POST');
        $formSendMethod = \Xmf\Request::getCmd('form_save_method', '', 'POST');
        $formId         = \Xmf\Request::getInt('form_id', 0, 'POST');
        $cloneFormId    = \Xmf\Request::getInt('clone_form_id', 0, 'POST');

        if ((0 === (int)$formSaveDb) && (Constants::SEND_METHOD_NONE === $formSendMethod)) {
            redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SAVESENT);
        }

        $error = '';
        $form = $formsHandler->get((int)$formId);

        $formSendToGroup  = \Xmf\Request::getInt('form_send_to_group', 0, 'POST');
        $formSendToOther  = \Xmf\Request::getString('form_send_to_other', '', 'POST');
        $formSendCopy     = \Xmf\Request::getInt('form_send_copy', '', 'POST');
        $formSendMethod   = \Xmf\Request::getWord('form_send_method', 'POST');
        $formEmailHeader  = \Xmf\Request::getText('form_email_header', 'POST');
        $formEmailFooter  = \Xmf\Request::getText('form_email_footer', '', 'POST');
        $formEmailUheader = \Xmf\Request::getText('form_email_uheader', '', 'POST');
        $formEmailUfooter = \Xmf\Request::getText('form_email_ufooter', '', 'POST');
        $formType         = \Xmf\Request::getWord('form_type', 'XoopsThemeForm', 'POST');
        $formOrder        = \Xmf\Request::getInt('form_order', 0, 'POST');
        $formDelimiter    = \Xmf\Request::getString('form_delimiter', '', 'POST');
        $formTitle        = \Xmf\Request::getString('form_title', '', 'POST');
        $formSubmitText   = \Xmf\Request::getText('form_submit_text', '', 'POST');
        $formDesc         = \Xmf\Request::getText('form_desc', '', 'POST');
        $formIntro        = \Xmf\Request::getText('form_intro', '', 'POST');
        $formWhereTo      = \Xmf\Request::getString('form_whereto', '', 'POST');
        $formDisplayStyle = \Xmf\Request::getCmd('form_display_style', '', 'POST');
        $defineFormBegin  = \Xmf\Request::getInt('define_form_begin', 0, 'POST');
        $defineFormEnd    = \Xmf\Request::getInt('define_form_end', 0, 'POST');
        $formActive       = \Xmf\Request::getInt('form_active', 0, 'POST');

        //validate list of other email addresses
        $sToO = (!empty($FormSendToOther)) ? explode(';', $formSendToOther) : array();
        $valArray = array();
        foreach ($sToO as $oEmail) {
            if ($valEmail = filter_var($oEmail, FILTER_VALIDATE_EMAIL)) {
                $valArray[] = $valEmail;
            }
        }
        $formSendToOther = (!empty($valArray)) ? implode(';', $valArray) : '';

        $form->setVars(array('form_send_to_group' => $formSendToGroup,
                             'form_send_to_other' => $formSendToOther,
                                 'form_send_copy' => $formSendCopy,
                               'form_send_method' => $formSendMethod,
                              'form_email_header' => $formEmailHeader,
                              'form_email_footer' => $formEmailFooter,
                             'form_email_uheader' => $formEmailUheader,
                             'form_email_ufooter' => $formEmailUfooter,
                                      'form_type' => $formType,
                                     'form_order' => $formOrder,
                                 'form_delimiter' => $formDelimiter,
                                     'form_title' => $formTitle,
                               'form_submit_text' => $formSubmitText,
                                      'form_desc' => $formDesc,
                                     'form_intro' => $formIntro,
                                   'form_whereto' => $formWhereTo,
                             'form_display_style' => $formDisplayStyle,
                                     'form_begin' => 0,
                                    'form_active' => $formActive)
        );

        if (0 !== (int)$defineFormBegin) {
            $formBegin = \Xmf\Request::getArray('form_begin', array('date' => getdate(), 'time' => 0), 'POST');
            $formBegin = strtotime($formBegin['date']) + $formBegin['time'];
            $form->setVar('form_begin', (int)$formBegin);
        }

        if (0 !== (int)$defineFormEnd) {
            $formEnd = \Xmf\Request::getArray('form_end', array('date' => getdate(), 'time' => 0), 'POST');
            $formEnd = strtotime($formEnd['date']) + $formEnd['time'];
        } else {
            $formEnd = 0;
        }
        $form->setVar('form_end', (int)$formEnd);

        // now update the form
        if (!$ret = $formsHandler->insert($form)) {
            $error = $form->getHtmlErrors();
        } else {
            $formsHandler->deleteFormPermissions($ret);

            $formGroupPerm = \Xmf\Request::getArray('form_group_perm', array(), 'POST');
            if (count($formGroupPerm > 0)) {
                $formsHandler->insertFormPermissions($ret, $formGroupPerm);
            }
            if (!empty($cloneFormId)) {
                //$xformsEleHandler = $helper->getHandler('Element');
                $criteria         = new \Criteria('form_id', $cloneFormId);
                $count            = $xformsEleHandler->getCount($criteria);
                if ($count > 0) {
                    $elements = $xformsEleHandler->getObjects($criteria);
                    foreach ($elements as $e) {
                        $values = $e->getValues();
                        unset($values['ele_id']);
                        $values['form_id'] = $ret;
                        $cloned = $xformsEleHandler->create();
                        $cloned->setVars($values);
                        if (!$xformsEleHandler->insert($cloned)) {
                            $error .= $cloned->getHtmlErrors();
                        }
                        unset($values, $cloned);
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
            if (_AM_XFORMS_SAVE_THEN_ELEMENTS === $_POST['submit']) {
                redirect_header($helper->url('admin/elements.php?form_id=' . $ret), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            } else {
                redirect_header($_SERVER['SCRIPT_NAME'], Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_DBUPDATED);
            }
        }
        break;
}

include __DIR__ . '/admin_footer.php';
xoops_cp_footer();
