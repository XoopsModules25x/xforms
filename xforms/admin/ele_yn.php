<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
if( !defined('xforms_ROOT_PATH') ){ exit(); }

if( !empty($ele_id) ){
	if( $value['_YES'] == 1 ){
		$selected = '_YES';
	}else{
		$selected = '_NO';
	}
}else{
	$selected = '_YES';
}
$options = new XoopsFormRadio(_AM_ELE_DEFAULT, 'ele_value', $selected);
$options->addOption('_YES', _YES);
$options->addOption('_NO', _NO);
$output->addElement($options);


?>