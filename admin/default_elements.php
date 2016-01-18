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

$defaults                       = array();
$defaults[0]['caption']         = _AM_XFORMS_DEFAULT_ELE_YOURNAME;
$defaults[0]['req']             = true;
$defaults[0]['ele_display_row'] = 1;
$defaults[0]['order']           = 1;
$defaults[0]['display']         = 1;
$defaults[0]['type']            = 'text';
$defaults[0]['value']           = array(
    0 => $xoopsModuleConfig['t_width'],
    1 => $xoopsModuleConfig['t_max'],
    2 => '{U_uname}'
);

$defaults[1]['caption']         = _AM_XFORMS_DEFAULT_ELE_YOUREMAIL;
$defaults[1]['req']             = true;
$defaults[1]['ele_display_row'] = 1;
$defaults[1]['order']           = 2;
$defaults[1]['display']         = 1;
$defaults[1]['type']            = 'text';
$defaults[1]['value']           = array(
    0 => $xoopsModuleConfig['t_width'],
    1 => $xoopsModuleConfig['t_max'],
    2 => '{U_email}'
);

$defaults[2]['caption']         = _AM_XFORMS_DEFAULT_ELE_COMMENTS;
$defaults[2]['req']             = true;
$defaults[2]['ele_display_row'] = 1;
$defaults[2]['order']           = 3;
$defaults[2]['display']         = 1;
$defaults[2]['type']            = 'textarea';
$defaults[2]['value']           = array(
    0 => '',
    1 => $xoopsModuleConfig['ta_rows'],
    2 => $xoopsModuleConfig['ta_cols']
);
