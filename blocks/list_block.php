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
require_once __DIR__ . '/../../../mainfile.php';

// instantiate module helper
//$helper = Helper::getHelper($moduleDirName);
//require_once $helper->path('include/common.php');
require_once $GLOBALS['xoops']->path("/modules/{$moduleDirName}/include/common.php");

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

function b_xforms_list_show($options)
{

    // instantiate module helper
    $moduleDirName = basename(dirname(__DIR__));
    $helper  = Helper::getHelper($moduleDirName);

    $block              = [];
    $xformsFormsHandler = $helper->getHandler('forms');
    $forms              = $xformsFormsHandler->getPermittedForms();
    if (!empty($forms)) {
        foreach ($forms as $form) {
            $block[$form->getVar('form_id')] = [
                'title' => $form->getVar('form_title', 's'),
                'desc'  => $form->getVar('form_desc', 's')
            ];
        }
    }

    return $block;
}

/**
 *
 * @param array $options [0] = sort, [1] = number to show
 *
 * @return string
 */
function b_xforms_list_edit($options)
{
    $optVals    = explode(',', _MB_XFORMS_LIST_BLOCK_SORT_OPTS);
    $optKeys    = explode(',', XformsConstants::LIST_BLOCK_SORT_KEYS);
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
