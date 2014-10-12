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

require __DIR__ . '/header.php';
$myts = MyTextSanitizer::getInstance();

if (empty($_POST['submit'])) {
    global $xoopsModuleConfig;

    $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
    if (empty($form_id)) {
        if (intval($xoopsModuleConfig['showforms'], 10) == 0) {
            /*Not shown the forms available if no one parameter*/
            redirect_header(XOOPS_URL, 2, _MD_XFORMS_MSG_NOFORM_SELECTED);
        }

        $forms = $xforms_form_mgr->getPermittedForms();
        if (($forms != false) && (count($forms) === 1)) {
            $form = $xforms_form_mgr->get($forms[0]->getVar('form_id'));
            require __DIR__ . '/include/form_render.php';
        } else {
            $xoopsOption['template_main'] = 'xforms_index.tpl';
            require_once XOOPS_ROOT_PATH . '/header.php';
            if (($forms != false) && (count($forms) > 0)) {
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
        if (!($form = $xforms_form_mgr->get($form_id))) {
            header("Location: " . XOOPS_URL);
            exit();
        } else {
            if (false != $xforms_form_mgr->getSingleFormPermission($form_id)) {
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
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    if (empty($form_id) || !($form = $xforms_form_mgr->get($form_id)) || ($xforms_form_mgr->getSingleFormPermission($form_id) == false)) {
        header("Location: " . XOOPS_URL);
        exit();
    }
    if (!$form->isActive()) {
        redirect_header('index.php', 2, _MD_XFORMS_MSG_INACTIVE);
    }
    require __DIR__ . '/include/form_execute.php';
}
