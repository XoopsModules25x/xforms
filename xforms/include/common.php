<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################

if( !defined("xforms_CONSTANTS_DEFINED") ){
	define("xforms_URL", XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("xforms_ROOT_PATH", XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("xforms_UPLOAD_PATH", $xoopsModuleConfig['uploaddir'].'/');

	define("xforms_CONSTANTS_DEFINED", true);
}

$xforms_form_mgr = xoops_getmodulehandler('forms');

if( false != xforms_UPLOAD_PATH ){
	if( !is_dir(xforms_UPLOAD_PATH) ){
		$oldumask = umask(0);
		mkdir(xforms_UPLOAD_PATH, 0777);
		umask($oldumask);
	}
	if( is_dir(xforms_UPLOAD_PATH) && !is_writable(xforms_UPLOAD_PATH) ){
		chmod(xforms_UPLOAD_PATH, 0777);
	}
}

?>