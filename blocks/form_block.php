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
 * @package   \XoopsModules\Xforms\admin\blocks
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper as xHelper;

// instantiate module helper
/* @var \XoopsModules\Xforms\Helper $helper */
$helper = xHelper::getInstance();     // module helper
require_once $helper->path('include/common.php');

/**
 * Display the form block
 *
 * @param array $options
 *
 * @return boolean
 */
function b_xforms_form_show($options)
{
    // Instantiate module helper
    $helper = xHelper::getInstance();
    $helper->loadLanguage('admin');

    $block = [];

    /* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
    $formsHandler = $helper::getInstance()->getHandler('Forms');
    //$formsHandler = $helper->getHandler('forms');
    $formOk  = $formsHandler->getSingleFormPermission((int)$options[0]);
    $formObj = $formsHandler->get((int)$options[0]); // get the form object we want
    if ($formObj instanceof XformsForms) {
        $block = $formObj->render();
    }

    return $block;
}

/**
 * Create HTML for block editing functionality
 *
 *
 * @return string html for edit form
 */
function b_xforms_form_edit($options)
{
    // Instantiate module helper
    $helper = xHelper::getInstance();     // module helper

    /* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
    $formsHandler = $helper::getInstance()->getHandler('Forms');
    //$formsHandler = $helper->getHandler('forms');
    $forms   = $formsHandler->getAll();
    $optForm = '<label for="fs1">' . _MB_XFORMS_FORM_DISPLAY . '</label>';
    if (empty($forms)) {
        $optForm .= '<input id="fs1" type="text" placeholder="' . _MB_XFORMS_FORM_NONE . '" size="20" disabled>';
    } else {
        $optForm .= '<select id="fs1"  name="options[0]">';
        foreach ($forms as $formObj) {
            $sel     = ($options[0] == $formObj->getVar('form_id')) ? ' selected' : '';
            $optForm .= '  <option value="' . $formObj->getVar('form_id') . '"' . $sel . '>' . $formObj->getVar('form_title', 's') . '</option>';
        }
        $optForm .= '</select>';
    }

    return $optForm;
}
