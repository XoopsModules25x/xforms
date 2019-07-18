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

use XoopsModules\Xforms;

$moduleDirName = basename(dirname(__DIR__));

// instantiate module helper
$helper = \XoopsModules\Xforms\Helper::getInstance();
require_once $helper->path('include/common.php');

function b_xforms_form_show($options)
{
    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    /** @var \XoopsModules\Xforms\Helper $helper */
    $helper = \XoopsModules\Xforms\Helper::getInstance();
    $helper->loadLanguage('admin');

    $block = [];

    $formsHandler = $helper->getHandler('Forms');
    $formOk             = $formsHandler->getSingleFormPermission((int)$options[0]);
    $formObj            = $formsHandler->get((int)$options[0]); // get the form object we want
    if ($formObj instanceof Xforms\Forms) {
        $block = $formObj->render();
    }

    return $block;
}

/**
 * @param array $options [0] = form to show
 *
 * @return string html for edit form
 */
function b_xforms_form_edit($options)
{
    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    /** @var \XoopsModules\Xforms\Helper $helper */
    $helper = \XoopsModules\Xforms\Helper::getInstance();

    $formsHandler = $helper->getHandler('Forms');
    $forms              = $formsHandler->getAll();
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
