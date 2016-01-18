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

$options   = array();
$opt_count = 0;
if (empty($addopt) && !empty($ele_id)) {
    $keys = array_keys($value);
    for ($i = 0; $i < count($keys); ++$i) {
        $v         = $myts->makeTboxData4PreviewInForm($keys[$i]);
        $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']', $v, 'check', $value[$keys[$i]]);
        ++$opt_count;
    }
} else {
    if (isset($ele_value) && count($ele_value) > 0) {
        while ($v = each($ele_value)) {
            $v['value'] = $myts->makeTboxData4PreviewInForm($v['value']);
            if (!empty($v['value'])) {
                $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']', $v['value'], 'check', $checked[$v['key']]);
                ++$opt_count;
            }
        }
    }
    $addopt = empty($addopt) ? 2 : $addopt;
    for ($i = 0; $i < $addopt; ++$i) {
        $options[] = addOption('ele_value[' . $opt_count . ']', 'checked[' . $opt_count . ']');
        ++$opt_count;
    }
}
$add_opt   = addOptionsTray();
$options[] = $add_opt;
$opt_tray  = new XoopsFormElementTray(_AM_XFORMS_ELE_OPT, '<br />');
$opt_tray->setDescription(_AM_XFORMS_ELE_OPT_DESC . '<br /><br />' . _AM_XFORMS_ELE_OTHER);
for ($i = 0; $i < count($options); ++$i) {
    $opt_tray->addElement($options[$i]);
}
$output->addElement($opt_tray);
