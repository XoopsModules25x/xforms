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
 * @package   \XoopsModules\Xforms\blocks
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\Helper;

include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

// Instantiate module helper
/* @var \XoopsModules\Xforms\Helper $helper */
$helper = Helper::getInstance();
require_once $helper->path('include/common.php');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');

/**
 * Display the list of forms block
 *
 * @param array $options
 *
 * @return array $block[] = array('title'=> item title, 'desc' => description), etc.
 */
function b_xforms_list_show($options)
{
    // Instantiate module helper
    /* @var \XoopsModules\Xforms\Helper $helper */
    $helper = Helper::getInstance();

    $block = [];
    /* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
    $formsHandler = $helper::getInstance()->getHandler('Forms');
    //$formsHandler = $helper->getHandler('forms');
    $forms = $formsHandler->getPermittedForms();
    if (!empty($forms)) {
        foreach ($forms as $form) {
            $block[$form->getVar('form_id')] = ['title' => $form->getVar('form_title', 's'),
                                                'desc'  => $form->getVar('form_desc', 's')];
        }
    }

    return $block;
}

/**
 *Create HTML for list block editing functionality
 *
 * @param array $options [0] = sort, [1] = number to show
 *
 * @return string HTML to display
 */
function b_xforms_list_edit($options)
{
    $optVals = explode(',', _MB_XFORMS_LIST_BLOCK_SORT_OPTS);
    $optKeys = explode(',', Constants::LIST_BLOCK_SORT_KEYS);
    $optArray = array_combine($optKeys, $optVals);
    $radioInput = '';
    $sortBy = in_array($options[0], $optKeys) ? $options[0] : $optKeys[0];
    foreach ($optArray as $key => $val) {
        $checked = ($sortBy == $key) ? ' checked' : '';
        $radioInput .= '<input type="radio" name="options[0]" value="' . $key . '" id="' . $key . $checked . ' style="margin-right: 1em;">'
                     . '<label for="' . $key . '" style="margin-right: 1em;">' . $val . '</label>';
    }
    $options[1] = (int)$options[1];

    $form = '<strong>' . _MB_XFORMS_SORTBY . '</strong>&nbsp;' . $radioInput . '<br>'
          . '<label for="num_forms">' . _MB_XFORMS_NUM_FORMS . '</label>'
          . '<input class="right" type="number" name="options[1]" id="num_forms" value="' . $options[1] . '" size="5" width="5em;"><br>';

    return $form;
}
