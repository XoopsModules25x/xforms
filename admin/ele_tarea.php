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

if (!defined('XFORMS_ROOT_PATH')) {
    exit();
}

$rows    = !empty($value[1]) ? $value[1] : $xoopsModuleConfig['ta_rows'];
$cols    = !empty($value[2]) ? $value[2] : $xoopsModuleConfig['ta_cols'];
$rows    = new XoopsFormText(_AM_XFORMS_ELE_ROWS, 'ele_value[1]', 3, 3, $rows);
$cols    = new XoopsFormText(_AM_XFORMS_ELE_COLS, 'ele_value[2]', 3, 3, $cols);
$default = new XoopsFormTextArea(_AM_XFORMS_ELE_DEFAULT, 'ele_value[0]', isset($value[0]) ? $myts->htmlspecialchars($myts->stripSlashesGPC($value[0])) : '', 5, 50);
$output->addElement($rows, 1);
$output->addElement($cols, 1);
$output->addElement($default);
