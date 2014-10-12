<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
include '../../../include/cp_header.php';
include '../include/common.php';
define('xforms_ADMIN_URL', xforms_URL.'admin/index.php');
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
function adminHtmlHeader(){
	global $xoopsModule, $xoopsConfig;
	$langf = xforms_ROOT_PATH.'language/'.$xoopsConfig['language'].'/modinfo.php';
	if( file_exists($langf) ){
		include $langf;
	}else{
		include xforms_ROOT_PATH.'language/english/modinfo.php';
	}
	include 'menu.php';
	for( $i=0; $i<4; $i++ ){
		$links[$i] = array(0 => xforms_URL.$adminmenu[$i]['link'], 1 => $adminmenu[$i]['title']);
	}
	$links[] = array(0 => XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=showmod&mod='.$xoopsModule->getVar('mid'), 1 => _PREFERENCES);
	$links[] = array(0=> xforms_URL.'admin/about.php', 1=> 'About');
	$admin_links = '<table class="outer" width="100%" cellspacing="1"><tr>';
	for( $i=0; $i<count($links); $i++ ){
		$admin_links .= '<td class="even" style="width: 20%; text-align: center;"><a href="'.$links[$i][0].'" accesskey="'.($i+1).'">'.$links[$i][1].'</a></td>';
	}
	$admin_links .= "</tr></table><br clear='all' />\n";
	xoops_cp_header();
	echo $admin_links;
}
?>