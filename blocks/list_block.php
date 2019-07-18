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
use XoopsModules\Xforms\Constants;

$moduleDirName = basename(dirname(__DIR__));
require_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

//require_once $helper->path('include/common.php');
require_once $GLOBALS['xoops']->path("/modules/{$moduleDirName}/include/common.php");

/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

function b_xforms_list_show($options)
{
    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    /** @var \XoopsModules\Xforms\Helper $helper */
    $helper = \XoopsModules\Xforms\Helper::getInstance();

    $block              = [];
    $formsHandler = $helper->getHandler('Forms');
    $forms              = $formsHandler->getPermittedForms();
    if (!empty($forms)) {
        foreach ($forms as $form) {
            $block[$form->getVar('form_id')] = [
                'title' => $form->getVar('form_title', 's'),
                'desc'  => $form->getVar('form_desc', 's'),
            ];
        }
    }

    return $block;
}

/**
 * @param array $options [0] = sort, [1] = number to show
 *
 * @return string
 */
function b_xforms_list_edit($options)
{
    $optVals    = explode(',', _MB_XFORMS_LIST_BLOCK_SORT_OPTS);
    $optKeys    = explode(',', Constants::LIST_BLOCK_SORT_KEYS);
    $optArray   = array_combine($optKeys, $optVals);
    $radioInput = '';
    $sortBy     = in_array($options[0], $optKeys) ? $options[0] : $optKeys[0];
    foreach ($optArray as $key => $val) {
        $checked    = ($sortBy == $key) ? ' checked' : '';
        $radioInput .= "<input type='radio' name='options[0]' value='{$key}' id='{key}'{$checked} style='margin-right: 1em;'><label for='{$key}' style='margin-right: 1em;'>{$val}</label>";
    }
    $options[1] = (int)$options[1];

    $form = '<strong>' . _MB_XFORMS_SORTBY . "</strong>&nbsp;{$radioInput}<br>\n" . '<label for="num_forms">' . _MB_XFORMS_NUM_FORMS . "</label>\n" . "<input class=\"right\" type=\"number\" name='options[1]' id=\"num_forms\" value=\"{$options[1]}\" size=\"5\" width=\"5em;\"><br>\n";

    return $form;
}
