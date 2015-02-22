<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
if( !defined('xforms_ROOT_PATH') ){ exit(); }

$options = array();
$opt_count = 0;
if( empty($addopt) && !empty($ele_id) ){
	$keys = array_keys($value);
	for( $i=0; $i<count($keys); $i++ ){
		$v = $myts->makeTboxData4PreviewInForm($keys[$i]);
		$options[] = addOption('ele_value['.$opt_count.']', 'checked['.$opt_count.']', $v, 'check', $value[$keys[$i]]);
		$opt_count++;
	}
}else{
	if( isset($ele_value) && count($ele_value) > 0 ){
		while( $v = each($ele_value) ){
			$v['value'] = $myts->makeTboxData4PreviewInForm($v['value']);
			if( !empty($v['value']) ){
				$options[] = addOption('ele_value['.$opt_count.']', 'checked['.$opt_count.']', $v['value'], 'check', $checked[$v['key']]);
				$opt_count++;
			}
		}
	}
	$addopt = empty($addopt) ? 2 : $addopt;
	for( $i=0; $i<$addopt; $i++ ){
		$options[] = addOption('ele_value['.$opt_count.']', 'checked['.$opt_count.']');
		$opt_count++;
	}
}
$add_opt = addOptionsTray();
$options[] = $add_opt;
$opt_tray = new XoopsFormElementTray(_AM_ELE_OPT, '<br />');
$opt_tray->setDescription(_AM_ELE_OPT_DESC.'<br /><br />'._AM_ELE_OTHER);
for( $i=0; $i<count($options); $i++ ){
	$opt_tray->addElement($options[$i]);
}
$output->addElement($opt_tray);
?>