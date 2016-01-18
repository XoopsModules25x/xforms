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

if (!defined('XFORMS_ROOT_PATH')) {
    exit();
}

if (!isset($form) || empty($form) || !is_object($form)) {
    header("Location: index.php");
    exit();
}

include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
$xforms_ele_mgr = xoops_getmodulehandler('elements');
include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';

if ($form->getVar('form_display_style') == 'f') {
    $xoopsOption['template_main'] = 'xforms_form.tpl';
} else {
    $xoopsOption['template_main'] = 'xforms_form_poll.tpl';
}
include_once XOOPS_ROOT_PATH . '/header.php';

/*
 * Read form elements
 */
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('form_id', $form->getVar('form_id')));
$criteria->add(new Criteria('ele_display', 1));
$criteria->setSort('ele_order');
$criteria->setOrder('ASC');
$elements = $xforms_ele_mgr->getObjects($criteria, true);

$form_output = new XoopsThemeForm($form->getVar('form_title'), 'xforms_' . $form->getVar('form_id'), XFORMS_URL . '/index.php');
foreach ($elements as $i) {
    $renderer = new XFormsElementRenderer($i);
    $form_ele = $renderer->constructElement();
    $req      = intval($i->getVar('ele_req'), 10);
    $form_output->addElement($form_ele, $req);
    unset($form_ele);
}

$form_output->addElement(new XoopsFormHidden('form_id', $form->getVar('form_id')));

global $xoopsCaptcha, $xoopsModuleConfig;
if ($xoopsModuleConfig['captcha']) {
    if (class_exists('XoopsFormCaptcha')) {
        $form_output->addElement(new XoopsFormCaptcha());
    }
}

$form_output->addElement(new XoopsFormButton('', 'submit', $form->getVar('form_submit_text'), 'submit'));

$c    = 0;
$eles = array();
foreach ($form_output->getElements() as $e) {
    $id      = $req = $name = $ele_type = false;
    $name    = $e->getName();
    $caption = $e->getCaption();
    if (!empty($name)) {
        $id = str_replace('ele_', '', $e->getName());
    } elseif (method_exists($e, 'getElements')) {
        $obj = $e->getElements();
        $id  = str_replace('ele_', '', $obj[0]->getName());
        $id  = str_replace('[]', '', $id);
    }
    $req         = false;
    $display_row = 1;
    if (isset($elements[$id])) {
        $req         = $elements[$id]->getVar('ele_req') ? true : false;
        $ele_type    = $elements[$id]->getVar('ele_type');
        $display_row = intval($elements[$id]->getVar('ele_display_row'), 10);
    }
    $eles[$c]['caption']     = $caption;
    $eles[$c]['name']        = $name;
    $eles[$c]['body']        = $e->render();
    $eles[$c]['hidden']      = $e->isHidden();
    $eles[$c]['required']    = $req;
    $eles[$c]['display_row'] = $display_row;
    $eles[$c]['ele_type']    = $ele_type;
    ++$c;
}
$js = $form_output->renderValidationJS();
$xoopsTpl->assign(
    'form_output',
    array(
        'title'      => $form_output->getTitle(),
        'name'       => $form_output->getName(),
        'action'     => $form_output->getAction(),
        'method'     => $form_output->getMethod(),
        'extra'      => 'onsubmit="return xoopsFormValidate_' . $form_output->getName() . '();"' . $form_output->getExtra(),
        'javascript' => $js,
        'elements'   => $eles
    )
);

$xoopsTpl->assign('form_req_prefix', $xoopsModuleConfig['prefix']);
$xoopsTpl->assign('form_req_suffix', $xoopsModuleConfig['suffix']);
$xoopsTpl->assign('form_intro', $form->getVar('form_intro'));
$xoopsTpl->assign('form_text_global', $myts->displayTarea($xoopsModuleConfig['global']));
if ($form->getVar('form_order') == 0) {
    if (!isset($xoopsUser) || !is_object($xoopsUser) || !$xoopsUser->isAdmin()) {
        header("Location: " . XFORMS_URL);
        exit();
    }
    $xoopsTpl->assign('form_is_hidden', _MD_XFORMS_FORM_IS_HIDDEN);
}

$xoopsTpl->assign('xoops_pagetitle', $form->getVar('form_title'));
