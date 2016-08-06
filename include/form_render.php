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
 * @copyright       {@see http://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             http://xoops.org XOOPS
 * @since           1.30
 */

use Xmf\Module\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (empty($form) || (!$form instanceof XformsForms)) {
    header('Location: index.php');
    exit();
}

include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

$moduleDirName = basename(dirname(__DIR__));
$xformsHelper  = Helper::getHelper($moduleDirName);

$xformsEleHandler = $xformsHelper->getHandler('element');
require_once $xformsHelper->path('/class/elementrenderer.php');
//include_once XFORMS_ROOT_PATH . '/class/elementrenderer.php';

if (!interface_exists('XformsConstants')) {
    require_once $xformsHelper->path('class/constants.php');
}

if (XformsConstants::FORM_DISPLAY_STYLE_FORM == $form->getVar('form_display_style')) {
    $xoopsOption['template_main'] = 'xforms_form.tpl';
} else {
    $xoopsOption['template_main'] = 'xforms_form_poll.tpl';
}
include_once $GLOBALS['xoops']->path('/header.php');
$GLOBALS['xoTheme']->addStylesheet("browse.php?modules/{$moduleDirName}/assets/css/style.css");

/*
 * Read form elements
 */
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('form_id', $form->getVar('form_id')));
$criteria->add(new Criteria('ele_display', XformsConstants::ELEMENT_DISPLAY));
$criteria->setSort('ele_order');
$criteria->setOrder('ASC');
$elements = $xformsEleHandler->getObjects($criteria, true);

$xformsHelper->loadLanguage('admin');
$xformsHelper->loadLanguage('main');

if (empty($elements)) { // this form doesn't have any elements
    xoops_header();
    echo sprintf(_MD_XFORMS_ELE_ERR, $form->getVar('form_title'), 's') . "<br><br>\n";
    $GLOBALS['xoopsTpl']->display($GLOBALS['xoopsOption']['template_main']);
    require $GLOBALS['xoops']->path('/footer.php');
    xoops_footer();
    exit();
}
$formOutput   = new XoopsThemeForm($form->getVar('form_title'), 'xforms_' . $form->getVar('form_id'), $xformsHelper->url('index.php'), 'post', true);
$firstElement = true;
$count        = 1;
$multipart    = false;
foreach ($elements as $i) {
    $renderer = new XformsElementRenderer($i);
    $formEle  = $renderer->constructElement(false, $form->getVar('form_delimiter'));
    $req      = (XformsConstants::ELEMENT_REQD == $i->getVar('ele_req')) ? true : false;
    if (true === $firstElement) {
        $formEle->setExtra('autofocus');  //give the 1st element focus on form load
        $firstElement = false;
    }
    $formEle->setExtra('tabindex="' . $count++ . '"'); // allow tabbing through fields on form

    if (in_array($i->getVar('ele_type'), array('upload', 'uploadimg'))) {
        $multipart = true; // will be a multipart form
    }

    $formOutput->addElement($formEle, $req);
    unset($formEle);
}

if ($multipart) { // set multipart attribute for form
    $formOutput->setExtra('enctype="multipart/form-data"');
}
$formOutput->addElement(new XoopsFormHidden('form_id', $form->getVar('form_id')));

// load captcha
xoops_load('formCaptcha', XFORMS_DIRNAME);
$xfFormCaptcha = new XformsFormCaptcha();
$formOutput->addElement($xfFormCaptcha);

$subButton = new XoopsFormButton('', 'submit', $form->getVar('form_submit_text'), 'submit');
$subButton->setExtra('tabindex="' . $count++ . '"'); // allow tabbing to the Submit button too
$formOutput->addElement($subButton, 1);

$eles = array();
foreach ($formOutput->getElements() as $e) {
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
        $display_row = (int)$elements[$id]->getVar('ele_display_row');
    }
    $eles[] = array(
        'caption'     => $caption,
        'name'        => $name,
        'body'        => $e->render(),
        'hidden'      => $e->isHidden(),
        'required'    => $req,
        'display_row' => $display_row,
        'ele_type'    => $ele_type
    );
}
$js = $formOutput->renderValidationJS();
$GLOBALS['xoopsTpl']->assign('form_output', array(
    'title'      => $formOutput->getTitle(),
    'name'       => $formOutput->getName(),
    'action'     => $formOutput->getAction(),
    'method'     => $formOutput->getMethod(),
    'extra'      => 'onsubmit="return xoopsFormValidate_' . $formOutput->getName() . '();"' . $formOutput->getExtra(),
    'javascript' => $js,
    'elements'   => $eles
));

$GLOBALS['xoopsTpl']->assign('form_req_prefix', $xformsHelper->getConfig('prefix'));
$GLOBALS['xoopsTpl']->assign('form_req_suffix', $xformsHelper->getConfig('suffix'));
$GLOBALS['xoopsTpl']->assign('form_intro', $form->getVar('form_intro'));
$GLOBALS['xoopsTpl']->assign('form_text_global', $myts->displayTarea($xformsHelper->getConfig('global')));
if (XformsConstants::FORM_HIDDEN == $form->getVar('form_order')) {
    if (!$xformsHelper->isUserAdmin()) {
        header('Location: ' . $xformsHelper->url('index.php'));
        exit();
    }
    $GLOBALS['xoopsTpl']->assign('form_is_hidden', _MD_XFORMS_FORM_IS_HIDDEN);
}

$GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $form->getVar('form_title'));
