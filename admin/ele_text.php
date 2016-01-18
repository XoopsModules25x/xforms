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

$profile_handler = xoops_getmodulehandler('profile', 'profile');
$member_handler  = xoops_gethandler('member');

$size           = !empty($value[0]) ? intval($value[0], 10) : $xoopsModuleConfig['t_width'];
$max            = !empty($value[1]) ? intval($value[1], 10) : $xoopsModuleConfig['t_max'];
$size           = new XoopsFormText(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 3, $size);
$max            = new XoopsFormText(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 3, 3, $max);
$default        = new XoopsFormText('', 'ele_value[2]', 50, 255, $myts->htmlspecialchars($myts->stripSlashesGPC($value[2])));
$select_default = new XoopsFormSelect(_AM_XFORMS_ELE_TEXT_ADD_DEFAULT, 'ele_value_2_add');
$select_default->addOption('', _AM_XFORMS_ELE_TEXT_ADD_DEFAULT_SEL);
$oprofile = $profile_handler->create();
$ouser    = $member_handler->createUser();
$uvars    = $ouser->vars;
foreach ($uvars as $uk => $uv) {
    if ($uk != 'pass' && ($uv['data_type'] == XOBJ_DTYPE_TXTBOX || $uv['data_type'] == XOBJ_DTYPE_UNICODE_TXTBOX)) {
        $select_default->addOption('{U_' . $uk . '}', 'User: ' . $uk);
    }
}
$pvars = $oprofile->vars;
foreach ($pvars as $pk => $pv) {
    if (!isset($uvars[$pk]) && ($pv['data_type'] == XOBJ_DTYPE_TXTBOX || $pv['data_type'] == XOBJ_DTYPE_UNICODE_TXTBOX)) {
        $select_default->addOption('{P_' . $pk . '}', 'Profile: ' . $pk);
    }
}
unset($uvars);
unset($pvars);
unset($ouser);
unset($oprofile);
$select_default->setExtra(' onchange="document.getElementById(\'ele_value[2]\').value += this.value;"');
$default_tray = new XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT, '<br />');
$default_tray->addElement($default);
$default_tray->addElement($select_default);
$default_tray->setDescription(_AM_XFORMS_ELE_TEXT_DESC);

$emailindicator = new XoopsFormRadioYN(_AM_XFORMS_ELE_CONTAINS_EMAIL, 'ele_value[3]', ((intval($value[3], 10) > 0) ? 1 : 0), _YES, _NO);
$emailindicator->setDescription(_AM_XFORMS_ELE_CONTAINS_EMAIL_DESC);

$output->addElement($size, 1);
$output->addElement($max, 1);
$output->addElement($default_tray);
$output->addElement($emailindicator);
