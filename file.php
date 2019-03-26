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

ob_start(); /*To prevent problems in file send*/

include dirname(dirname(__DIR__)) . '/mainfile.php';
include 'include/common.php';

$udid  = isset($_GET['ui']) ? intval(trim($_GET['ui']), 10) : 0;
$form  = isset($_GET['fm']) ? intval(trim($_GET['fm']), 10) : 0;
$elem  = isset($_GET['el']) ? intval(trim($_GET['el']), 10) : 0;
$file  = isset($_GET['f']) ? trim($_GET['f']) : '';
$fname = isset($_GET['fn']) ? trim($_GET['fn']) : '';
if (empty($file) && ($udid == 0 || $form == 0 || $elem == 0)) {
    /*Error in params to read file*/
    exit();
}
if (empty($file)) {
    $udata_mgr = xoops_getmodulehandler('userdata');
    if (!($udata = $udata_mgr->get($udid)) || ($udata->getVar('form_id') != $form) || ($udata->getVar('ele_id') != $elem)) {
        exit();
    }
    $fdata = $udata->getVar('udata_value');
    if (empty($fdata) || empty($fdata['file']) || empty($fdata['name'])) {
        exit();
    }
    $file  = $fdata['file'];
    $fname = $fdata['name'];
}
if (empty($file)) {
    ob_end_flush();
    redirect_header(XOOPS_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
}
if (empty($fname)) {
    $fname = $file;
}

$path = XFORMS_UPLOAD_PATH . $file;
if (!preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path)) {
    ob_end_flush();
    redirect_header(XOOPS_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
}

ob_end_clean(); /*Clear all contents sending to browser to prevent corruptions in file send*/

$mimeFile = getMimeType($path);
//$mimeFile = 'application/octet-stream';

if (class_exists('finfo')) { // should exist for >= PHP 5.3
    $finfo    = new \finfo(FILEINFO_MIME);
    $mimeFile = $finfo->file($filename);
} else {
    $mimeTypes = include_once $GLOBALS['xoops']->path('www/include/mimetypes.inc.php');
    $extArray  = explode('.', $filename);
    $ext       = strtolower(array_pop($extArray));
    if (array_key_exists($ext, $mimeTypes)) {
        $mimeFile = $mimeTypes[$ext];
    }
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeFile);
//header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($fname));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path));
ob_clean();
flush();
readfile($path);
