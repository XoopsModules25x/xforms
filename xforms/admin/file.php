<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
include 'admin_header.php';
$file = isset($_GET['f']) ? trim($_GET['f']) : '';
$path = xforms_UPLOAD_PATH.$file;
if( !$file || !preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path) ){
	redirect_header(XOOPS_URL, 0, _AM_NOTHING_SELECTED);
}

header("Content-Type: application/octet-stream");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Cache-Control: private, no-cache');
header("Pragma: no-cache");
header('Content-Disposition: attachment; filename="'.$file.'"');
header("Content-Length: ".filesize($path));

readfile($path);
?>