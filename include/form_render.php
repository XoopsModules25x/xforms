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

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!isset($form) || empty($form) || !is_object($form)) {
    header("Location: index.php");
    exit();
}

include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
$xformsEleMgr = xoops_getmodulehandler('elements');
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
$elements = $xformsEleMgr->getObjects($criteria, true);

$formOutput = new XoopsThemeForm($form->getVar('form_title'), 'xforms_' . $form->getVar('form_id'), XFORMS_URL . '/index.php');
foreach ($elements as $i) {
    $renderer = new XFormsElementRenderer($i);
    $formEle  = $renderer->constructElement();
    $req      = (int)$i->getVar('ele_req');
    $formOutput->addElement($formEle, $req);
    unset($formEle);
}

$formOutput->addElement(new XoopsFormHidden('form_id', $form->getVar('form_id')));

global $xoopsCaptcha, $xoopsModuleConfig;
if ($xoopsModuleConfig['captcha']) {
    if (class_exists('XoopsFormCaptcha')) {
        $formOutput->addElement(new XoopsFormCaptcha());
    }
}

$formOutput->addElement(new XoopsFormButton('', 'submit', $form->getVar('form_submit_text'), 'submit'));

$c    = 0;
$eles = array();
foreach ($formOutput->getElements() as $e) {
    $id      = $req = $name = $eleType = false;
    $name    = $e->getName();
    $caption = $e->getCaption();
    if (!empty($name)) {
        $id = str_replace('ele_', '', $e->getName());
    } elseif (method_exists($e, 'getElements')) {
        $obj = $e->getElements();
        $id  = str_replace('ele_', '', $obj[0]->getName());
        $id  = str_replace('[]', '', $id);
    }
    $req        = false;
    $displayRow = 1;
    if (isset($elements[$id])) {
        $req        = $elements[$id]->getVar('ele_req') ? true : false;
        $eleType    = $elements[$id]->getVar('ele_type');
        $displayRow = (int)$elements[$id]->getVar('ele_display_row');
    }
    $eles[$c]['caption']     = $caption;
    $eles[$c]['name']        = $name;
    $eles[$c]['body']        = $e->render();
    $eles[$c]['hidden']      = $e->isHidden();
    $eles[$c]['required']    = $req;
    $eles[$c]['display_row'] = $displayRow;
    $eles[$c]['ele_type']    = $eleType;
    ++$c;
}
$js = $formOutput->renderValidationJS();
$xoopsTpl->assign(
    'form_output',
    array(
        'title'      => $formOutput->getTitle(),
        'name'       => $formOutput->getName(),
        'action'     => $formOutput->getAction(),
        'method'     => $formOutput->getMethod(),
        'extra'      => 'onsubmit="return xoopsFormValidate_' . $formOutput->getName() . '();"' . $formOutput->getExtra(),
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
