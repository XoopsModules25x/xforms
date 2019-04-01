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

require __DIR__ . '/header.php';
$myts = MyTextSanitizer::getInstance();

if (empty($_POST['submit'])) {
    global $xoopsModuleConfig;

    $formId = Request::getInt('form_id', 0, 'GET');
    $formId = (int)$formId; // fix for XOOPS < 2.5.9 not returning integer
    //$formId = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
    //if (empty($formId)) {
    if (0 === $formId) {
        if (0 === (int)$xoopsModuleConfig['showforms']) {
            /*Not shown the forms available if no one parameter*/
            redirect_header(XOOPS_URL, 2, _MD_XFORMS_MSG_NOFORM_SELECTED);
        }

        $forms = $xformsFormMgr->getPermittedForms();
        if (($forms != false) && (count($forms) === 1)) {
            $form = $xformsFormMgr->get($forms[0]->getVar('form_id'));
            require __DIR__ . '/include/form_render.php';
        } else {
            $xoopsOption['template_main'] = 'xforms_index.tpl';
            require_once XOOPS_ROOT_PATH . '/header.php';
            $formCount = count($forms);
            if (($forms != false) && $formCount > 0)) {
                foreach ($forms as $form) {
                    $xoopsTpl->append('forms',array(
                            'title' => $form->getVar('form_title'),
                            'desc'  => $form->getVar('form_desc'),
                            'id'    => $form->getVar('form_id')
                        )
                    );
                }
                $xoopsTpl->assign('forms_intro', $myts->displayTarea($xoopsModuleConfig['intro'], 1));
            } else {
                $xoopsTpl->assign('noform', $myts->displayTarea($xoopsModuleConfig['noform'], 1));
            }
        }
    } else {
        if (!($form = $xformsFormMgr->get($formId))) {
            header("Location: " . XOOPS_URL);
            exit();
        } else {
            if (false != $xformsFormMgr->getSingleFormPermission($formId)) {
                if (!$form->isActive()) {
                    redirect_header(XOOPS_URL, 2, _MD_XFORMS_MSG_INACTIVE);
                }
                require __DIR__ . '/include/form_render.php';
            } else {
                header("Location: " . XOOPS_URL);
                exit();
            }
        }
    }

    $xoopsTpl->assign('default_title', $xoopsModuleConfig['dtitle']);

    require XOOPS_ROOT_PATH . '/footer.php';
} else {
    $formId = isset($_POST['form_id']) ? (int)$_POST['form_id'] : 0;
    if (empty($formId) || !($form = $xformsFormMgr->get($formId)) || ($xformsFormMgr->getSingleFormPermission($formId) == false)) {
        header("Location: " . XOOPS_URL);
        exit();
    }
    if (!$form->isActive()) {
        redirect_header('index.php', 2, _MD_XFORMS_MSG_INACTIVE);
    }
    require __DIR__ . '/include/form_execute.php';
}
