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
		$r = $value[$keys[$i]] ? $opt_count : null;
		$v = $myts->makeTboxData4PreviewInForm($keys[$i]);
		$options[] = addOption('ele_value['.$opt_count.']', $opt_count, $v, 'radio', $r);
		$opt_count++;
	}
}else{
	if( isset($ele_value) && count($ele_value) > 0 ){
		while( $v = each($ele_value) ){
			$v['value'] = $myts->makeTboxData4PreviewInForm($v['value']);
			if( !empty($v['value']) ){
				$r = ($checked == $opt_count) ? $opt_count : null;
				$options[] = addOption('ele_value['.$opt_count.']', $opt_count, $v['value'], 'radio', $r);
				$opt_count++;
			}
		}
	}
	$addopt = empty($addopt) ? 2 : $addopt;
	for( $i=0; $i<$addopt; $i++ ){
		$options[] = addOption('ele_value['.$opt_count.']', $opt_count, '', 'radio');
		$opt_count++;
	}
}
$options[] = addOptionsTray();
$opt_tray = new XoopsFormElementTray(_AM_ELE_OPT, '<br />');
$opt_tray->setDescription(_AM_ELE_OPT_DESC2.'<br /><br />'._AM_ELE_OTHER);
for( $i=0; $i<count($options); $i++ ){
	$opt_tray->addElement($options[$i]);
}
$output->addElement($opt_tray);
?>