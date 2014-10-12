<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################

function xoops_module_install_xforms(&$module){
	global $moduleperm_handler;
	/*
	$msgs[] = 'Setting up default permissions...';
	$m = '&nbsp;&nbsp;Grant permission of form id %u to group id %u ......%s';
	*/
	for( $i=1; $i<4; $i++ ){
		$perm =& $moduleperm_handler->create();
		$perm->setVar('gperm_name', 'xforms_form_access');
		$perm->setVar('gperm_itemid', 1);
		$perm->setVar('gperm_groupid', $i);
		$perm->setVar('gperm_modid', $module->getVar('mid'));
		$moduleperm_handler->insert($perm);
		/*
		if( !$moduleperm_handler->insert($perm) ){
			$msgs[] = sprintf($m, 1, $i, 'failed');
		}else{
			$msgs[] = sprintf($m, 1, $i, 'done');
		}
		*/
	}
	return true;
}

?>