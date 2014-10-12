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

/**
 * @param $filename
 *
 * @return mixed|string
 */
function getMimeType($filename)
{
    $mime_types = array(
        "txt"  => "text/plain",
        "htm"  => "text/html",
        "html" => "text/html",
        "php"  => "text/html",
        "css"  => "text/css",
        "js"   => "application/javascript",
        "json" => "application/json",
        "xml"  => "application/xml",
        "swf"  => "application/x-shockwave-flash",
        "flv"  => "video/x-flv",
        // images
        "png"  => "image/png",
        "jpe"  => "image/jpeg",
        "jpeg" => "image/jpeg",
        "jpg"  => "image/jpeg",
        "gif"  => "image/gif",
        "bmp"  => "image/bmp",
        "ico"  => "image/vnd.microsoft.icon",
        "tiff" => "image/tiff",
        "tif"  => "image/tiff",
        "svg"  => "image/svg+xml",
        "svgz" => "image/svg+xml",
        // archives
        "zip"  => "application/zip",
        "rar"  => "application/x-rar-compressed",
        "exe"  => "application/x-msdownload",
        "msi"  => "application/x-msdownload",
        "cab"  => "application/vnd.ms-cab-compressed",
        // audio/video
        "mp3"  => "audio/mpeg",
        "qt"   => "video/quicktime",
        "mov"  => "video/quicktime",
        // adobe
        "pdf"  => "application/pdf",
        "psd"  => "image/vnd.adobe.photoshop",
        "ai"   => "application/postscript",
        "eps"  => "application/postscript",
        "ps"   => "application/postscript",
        // ms office
        "doc"  => "application/msword",
        "rtf"  => "application/rtf",
        "xls"  => "application/vnd.ms-excel",
        "ppt"  => "application/vnd.ms-powerpoint",
        // open office
        "odt"  => "application/vnd.oasis.opendocument.text",
        "ods"  => "application/vnd.oasis.opendocument.spreadsheet"
    );
    if (function_exists("finfo_open")) {
        $finfo    = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);

        return $mimetype;
    }
    $extArray = explode(".", $filename);
    $ext      = strtolower(array_pop($extArray));
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }

    return "application/octet-stream";
}

$mimeFile = getMimeType($path);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($fname));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path));
ob_clean();
flush();
readfile($path);
