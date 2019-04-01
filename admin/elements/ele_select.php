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

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (empty($addOpt) && !empty($eleId)) {
    $eleValue = $element->getVar('ele_value');
}
$eleSize    = !empty($eleValue[0]) ? $eleValue[0] : 1;
$size       = new XoopsFormText(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 2, $eleSize);
$allowMulti = empty($eleValue[1]) ? 0 : 1;
$multiple   = new XoopsFormRadioYN(_AM_XFORMS_ELE_MULTIPLE, 'ele_value[1]', $allowMulti);

$options  = array();
$optCount = 0;
if (empty($addOpt) && !empty($eleId)) {
    $keys     = array_keys($eleValue[2]);
    $keyCount = count($keys);
    for ($i = 0; $i < $keyCount; ++$i) {
        $v         = $myts->htmlSpecialChars($keys[$i]);
        $options[] = addOption('ele_value[2][' . $optCount . ']', 'checked[' . $optCount . ']', $v, 'check', $eleValue[2][$keys[$i]]);
        ++$optCount;
    }
} else {
    if (!empty($eleValue[2])) {
        while ($v = each($eleValue[2])) {
            $v['value'] = $myts->htmlSpecialChars($v['value']);
            if (!empty($v['value'])) {
                $options[] = addOption('ele_value[2][' . $optCount . ']', 'checked[' . $optCount . ']', $v['value'], 'check', $checked[$v['key']]);
                ++$optCount;
            }
        }
    }
    $addOpt = empty($addOpt) ? 2 : $addOpt;
    for ($i = 0; $i < $addOpt; ++$i) {
        $options[] = addOption('ele_value[2][' . $optCount . ']', 'checked[' . $optCount . ']');
        ++$optCount;
    }
}

$options[] = addOptionsTray();

$optTray = new XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br />');
$optTray->setDescription(_AM_XFORMS_ELE_OPT_DESC . _AM_XFORMS_ELE_OPT_DESC1);
$optCount = count($options);
for ($i = 0; $i < $optCount; ++$i) {
    $optTray->addElement($options[$i]);
}
$output->addElement($size, 1);
$output->addElement($multiple);
$output->addElement($optTray);
