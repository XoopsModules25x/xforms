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

if (empty($addopt) && !empty($ele_id)) {
    $ele_value = $element->getVar('ele_value');
}
$ele_size    = !empty($ele_value[0]) ? $ele_value[0] : 1;
$size        = new XoopsFormText(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 3, 2, $ele_size);
$allow_multi = empty($ele_value[1]) ? 0 : 1;
$multiple    = new XoopsFormRadioYN(_AM_XFORMS_ELE_MULTIPLE, 'ele_value[1]', $allow_multi);

$options   = array();
$opt_count = 0;
if (empty($addopt) && !empty($ele_id)) {
    $keys = array_keys($ele_value[2]);
    for ($i = 0; $i < count($keys); ++$i) {
        $v         = $myts->makeTboxData4PreviewInForm($keys[$i]);
        $options[] = addOption('ele_value[2][' . $opt_count . ']', 'checked[' . $opt_count . ']', $v, 'check', $ele_value[2][$keys[$i]]);
        ++$opt_count;
    }
} else {
    if (!empty($ele_value[2])) {
        while ($v = each($ele_value[2])) {
            $v['value'] = $myts->makeTboxData4PreviewInForm($v['value']);
            if (!empty($v['value'])) {
                $options[] = addOption('ele_value[2][' . $opt_count . ']', 'checked[' . $opt_count . ']', $v['value'], 'check', $checked[$v['key']]);
                ++$opt_count;
            }
        }
    }
    $addopt = empty($addopt) ? 2 : $addopt;
    for ($i = 0; $i < $addopt; ++$i) {
        $options[] = addOption('ele_value[2][' . $opt_count . ']', 'checked[' . $opt_count . ']');
        ++$opt_count;
    }
}

$add_opt   = addOptionsTray();
$options[] = $add_opt;

$opt_tray = new XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br />');
$opt_tray->setDescription(_AM_XFORMS_ELE_OPT_DESC . _AM_XFORMS_ELE_OPT_DESC1);
for ($i = 0; $i < count($options); ++$i) {
    $opt_tray->addElement($options[$i]);
}
$output->addElement($size, 1);
$output->addElement($multiple);
$output->addElement($opt_tray);
