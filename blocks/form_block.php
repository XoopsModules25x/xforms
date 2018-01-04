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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */

use Xmf\Module\Helper;

$moduleDirName = basename(dirname(__DIR__));

// instantiate module helper
$helper = Xmf\Module\Helper::getHelper($moduleDirName);
require_once $helper->path('include/common.php');

if (!class_exists('XformsFormInput')) {
    xoops_load('FormInput', $moduleDirName);
}

function b_xforms_form_show($options)
{
    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    $helper  = Xmf\Module\Helper::getHelper($moduleDirName);
    $helper->loadLanguage('admin');

    $block = [];

    $xformsFormsHandler = $helper->getHandler('forms');
    $formOk             = $xformsFormsHandler->getSingleFormPermission((int)$options[0]);
    $formObj            = $xformsFormsHandler->get((int)$options[0]); // get the form object we want
    if ($formObj instanceof XformsForms) {
        $block = $formObj->render();
    }

    return $block;
}

/**
 *
 * @param array $options [0] = form to show
 *
 * @return string html for edit form
 *
 */
function b_xforms_form_edit($options)
{
    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    $helper  = Xmf\Module\Helper::getHelper($moduleDirName);

    $xformsFormsHandler = $helper->getHandler('forms');
    $forms              = $xformsFormsHandler->getAll();
    $optForm            = "<label for='fs1'>Form to Display</label>\n";
    if (empty($forms)) {
        $optForm .= "<input id='fs1' type='text' placeholder='No Active Forms' size='20' disabled>\n";
    } else {
        $optForm .= "<select id='fs1'  name='options[0]'>\n";
        foreach ($forms as $formObj) {
            $sel     = ($options[0] == $formObj->getVar('form_id')) ? ' selected' : '';
            $optForm .= "  <option value='" . $formObj->getVar('form_id') . "'{$sel}>" . $formObj->getVar('form_title', 's') . "</option>\n";
        }
        $optForm .= "</select>\n";
    }

    return $optForm;
}
