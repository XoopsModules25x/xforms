<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
if( !defined('xforms_ROOT_PATH') ){ exit(); }

$rows = !empty($value[1]) ? $value[1] : $xoopsModuleConfig['ta_rows'];
$cols = !empty($value[2]) ? $value[2] : $xoopsModuleConfig['ta_cols'];
$rows = new XoopsFormText(_AM_ELE_ROWS, 'ele_value[1]', 3, 3, $rows);
$cols = new XoopsFormText(_AM_ELE_COLS, 'ele_value[2]', 3, 3, $cols);
$default = new XoopsFormTextArea(_AM_ELE_DEFAULT, 'ele_value[0]', isset($value[0]) ? $myts->htmlspecialchars($myts->stripSlashesGPC($value[0])) : '', 5, 50);
$output->addElement($rows, 1);
$output->addElement($cols, 1);
$output->addElement($default);

?>