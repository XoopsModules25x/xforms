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

if (!defined('xforms_ROOT_PATH')) {
    exit();
}

if (empty($addopt) && !empty($ele_id)) {
    $ele_value = $element->getVar('ele_value');
}
$ele_size    = !empty($ele_value[0]) ? $ele_value[0] : 1;
$size        = new XoopsFormText(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 2, $ele_size);
$allow_multi = empty($ele_value[1]) ? 0 : 1;
$multiple    = new XoopsFormRadioYN(_AM_XFORMS_ELE_MULTIPLE, 'ele_value[1]', $allow_multi);
$country     = xoops_getModuleOption('mycountry', 'xforms');
$countries   = !empty($ele_value[2]) ? $ele_value[2] : $country;
$reg_form    = new XoopsFormSelectCountry(_AM_XFORMS_ELE_SELECT_CTRY, 'ele_value[2]', $countries);
//
$output->addElement($size, 1);
$output->addElement($multiple);
//$output->addElement($opt_tray);
$output->addElement($reg_form);
