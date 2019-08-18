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
 * @package   \XoopsModules\Xforms\include
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
use \XoopsModules\Xforms;
use \XoopsModules\Xforms\Constants;
use \XoopsModules\Xforms\Helper as xHelper;
use \XoopsModules\Xforms\ElementRenderer;
use \XoopsModules\Xforms\FormCaptcha;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (empty($form) || (!$form instanceof Forms)) {
    header('Location: index.php');
    exit();
}

xoops_load('xoopsformloader');

$moduleDirName = basename(dirname(__DIR__));

// Instantiate
/* @var \XoopsModules\Xforms\Helper $helper */
$helper = xHelper::getInstance();     // module helper

$xformsElesHandler = $helper::getInstance()->getHandler('Element');
//$xformsEleHandler = $helper->getHandler('Element');
require_once $helper->path('class/elementrenderer.php');

if (!interface_exists('\XoopsModules\Xforms\Constants')) {
    require_once $helper->path('class/constants.php');
}

if (Constants::FORM_DISPLAY_STYLE_FORM == $form->getVar('form_display_style')) {
    $GLOBALS['xoopsOption']['template_main'] = 'xforms_form.tpl';
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'xforms_form_poll.tpl';
}
include_once $GLOBALS['xoops']->path('/header.php');
$GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/style.css');

/*
 * Read form elements
 */
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('form_id', $form->getVar('form_id')));
$criteria->add(new \Criteria('ele_display', Constants::ELEMENT_DISPLAY));
$criteria->setSort('ele_order');
$criteria->order = 'ASC';
$elements = $xformsEleHandler->getObjects($criteria, true);

$helper->loadLanguage('admin');
$helper->loadLanguage('main');

if (empty($elements)) { // this form doesn't have any elements
    xoops_header();
    echo sprintf(_MD_XFORMS_ELE_ERR, $form->getVar('form_title'), 's') . '<br><br>';
    $GLOBALS['xoopsTpl']->display($GLOBALS['xoopsOption']['template_main']);
    require $GLOBALS['xoops']->path('/footer.php');
    xoops_footer();
    exit();
}
$formOutput   = new \XoopsThemeForm($form->getVar('form_title'), 'xforms_' . $form->getVar('form_id'), $helper->url('index.php'), 'post', true);
$firstElement = true;
$count        = 1;
$multipart    = false;
foreach ($elements as $i) {
    $renderer = new ElementRenderer($i);
    $formEle  = $renderer->constructElement(false, $form->getVar('form_delimiter'));
    $req      = (Constants::ELEMENT_REQD == $i->getVar('ele_req')) ? true : false;
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
$formOutput->addElement(new \XoopsFormHidden('form_id', $form->getVar('form_id')));

// load captcha
xoops_load('formCaptcha', $moduleDirName);
$xfFormCaptcha = new FormCaptcha();
$formOutput->addElement($xfFormCaptcha);

$subButton = new \XoopsFormButton('', 'submit', $form->getVar('form_submit_text'), 'submit');
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
    $eles[] = array('caption' => $caption,
                       'name' => $name,
                       'body' => $e->render(),
                     'hidden' => $e->isHidden(),
                   'required' => $req,
                'display_row' => $display_row,
                   'ele_type' => $ele_type
    );
}
$js = $formOutput->renderValidationJS();
$GLOBALS['xoopsTpl']->assign('form_output', array('title' => $formOutput->getTitle(),
                                                   'name' => $formOutput->getName(),
                                                 'action' => $formOutput->getAction(),
                                                 'method' => $formOutput->getMethod(),
                                                  'extra' => 'onsubmit="return xoopsFormValidate_' . $formOutput->getName() . '();"' . $formOutput->getExtra(),
                                             'javascript' => $js,
                                               'elements' => $eles,
                                        'form_req_prefix' => $helper->getConfig('prefix'),
                                        'form_req_suffix' => $helper->getConfig('suffix'),
                                             'form_intro' => $form->getVar('form_intro'),
                                       'form_text_global' => $myts->displayTarea($helper->getConfig('global')),
                                        'xoops_pagetitle' => $form->getVar('form_title'))
);
if (Constants::FORM_HIDDEN == $form->getVar('form_order')) {
    if (!$helper->isUserAdmin()) {
        header('Location: ' . $helper->url('index.php'));
        exit();
    }
    $GLOBALS['xoopsTpl']->assign('form_is_hidden', _MD_XFORMS_FORM_IS_HIDDEN);
}
