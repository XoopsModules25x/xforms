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
 * @package   \XoopsModules\Xforms\admin\elements
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * HTML element
 *
 * value array [0] = text value
 */
$defaultEditorConfigs = [
    'editor' => $sysHelper->getConfig('general_editor'),
    'rows'   => 8,
    'cols'   => 90,
    'width'  => '100%',
    'height' => '260px',
    'name'   => 'ele_value[0]',
    'value'  => isset($value[0]) ? htmlspecialchars($value[0]) : '',
];
$default              = new \XoopsFormEditor(_AM_XFORMS_ELE_HTML_DEFAULT_DISP, 'ele_value[0]', $defaultEditorConfigs);
$renderer             = $default->editor->renderer;
if (property_exists($renderer, 'skipPreview')) {
    $default->editor->renderer->skipPreview = true;
}

$output->addElement($default);
