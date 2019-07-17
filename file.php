<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Module: xForms
 *
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */

use Xmf\Request;
use XoopsModules\Xforms\Constants;

ob_start(); /*To prevent problems in file send*/
require_once __DIR__ . '/header.php';

$udid  = Request::getInt('ui', 0, 'GET');
$form  = Request::getInt('fm', 0, 'GET');
$elem  = Request::getInt('el', 0, 'GET');
$file  = Request::getString('f', '', 'GET');
$fname = Request::getString('fn', '', 'GET');

if (empty($file)) {
    if ((0 == $udid) || (0 == $form) || (0 == $elem)) {
        //Error in params to read file
        //@todo - test ob_end_clean here - added in v2.00 ALPHA 2
        ob_end_clean();
        exit();
    }
    $uDataHandler = $helper->getHandler('Userdata', basename(__DIR__));
    if (!($uData = $uDataHandler->get($udid)) || ($uData->getVar('form_id') != $form)
        || ($uData->getVar('ele_id') != $elem)) {
        //@todo - test ob_end_clean here - added in v2.00 ALPHA 2
        ob_end_clean();
        exit();
    }
    $uDataValue = $uData->getVar('udata_value');
    if (empty($uDataValue) || empty($uDataValue['file']) || empty($uDataValue['name'])) {
        //@todo - test ob_end_clean here - added in v2.00 ALPHA 2
        ob_end_clean();
        exit();
    }
    $file  = $uDataValue['file'];
    $fname = $uDataValue['name'];
}

if (empty($file)) {
    //@TODO - shouldn't this be an ob_end_clean here instead of ob_end_flush?
    ob_end_flush();
    redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}
if (empty($fname)) {
    $fname = $file;
}

$path = XFORMS_UPLOAD_PATH . $file;
if (!preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path)) {
    //@TODO - shouldn't this be an ob_end_clean here instead of ob_end_flush?
    ob_end_flush();
    redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}

ob_end_clean(); /*Clear all contents sending to browser to prevent corruptions in file send*/

/**
 * @param $filename
 *
 * @return mixed|string
 */
/***************************************************
 * function getMimeType($filename)
 * {
 * //    $mimeTypes = require_once $GLOBALS['xoops']->path('www/include/mimetypes.inc.php');
 * $mimeTypes = array(
 * "txt"  => "text/plain",
 * "htm"  => "text/html",
 * "html" => "text/html",
 * "php"  => "text/html",
 * "css"  => "text/css",
 * "js"   => "application/javascript",
 * "json" => "application/json",
 * "xml"  => "application/xml",
 * "swf"  => "application/x-shockwave-flash",
 * "flv"  => "video/x-flv",
 * // images
 * "png"  => "image/png",
 * "jpe"  => "image/jpeg",
 * "jpeg" => "image/jpeg",
 * "jpg"  => "image/jpeg",
 * "gif"  => "image/gif",
 * "bmp"  => "image/bmp",
 * "ico"  => "image/vnd.microsoft.icon",
 * "tiff" => "image/tiff",
 * "tif"  => "image/tiff",
 * "svg"  => "image/svg+xml",
 * "svgz" => "image/svg+xml",
 * // archives
 * "zip"  => "application/zip",
 * "rar"  => "application/x-rar-compressed",
 * "exe"  => "application/x-msdownload",
 * "msi"  => "application/x-msdownload",
 * "cab"  => "application/vnd.ms-cab-compressed",
 * // audio/video
 * "mp3"  => "audio/mpeg",
 * "qt"   => "video/quicktime",
 * "mov"  => "video/quicktime",
 * // adobe
 * "pdf"  => "application/pdf",
 * "psd"  => "image/vnd.adobe.photoshop",
 * "ai"   => "application/postscript",
 * "eps"  => "application/postscript",
 * "ps"   => "application/postscript",
 * // ms office
 * "doc"  => "application/msword",
 * "rtf"  => "application/rtf",
 * "xls"  => "application/vnd.ms-excel",
 * "ppt"  => "application/vnd.ms-powerpoint",
 * // open office
 * "odt"  => "application/vnd.oasis.opendocument.text",
 * "ods"  => "application/vnd.oasis.opendocument.spreadsheet"
 * );
 *
 * if (function_exists("finfo_open")) {
 * $finfo    = finfo_open(FILEINFO_MIME);
 * $mimeType = finfo_file($finfo, $filename);
 * finfo_close($finfo);
 * return $mimeType;
 * }
 *
 * if (class_exists('finfo')) {
 * $finfo = new finfo(FILEINFO_MIME);
 * return $finfo->file($filename);
 * }
 *
 * $extArray = explode(".", $filename);
 * $ext      = strtolower(array_pop($extArray));
 * if (array_key_exists($ext, $mimeTypes)) {
 * return $mimeTypes[$ext];
 * }
 *
 * return "application/octet-stream";
 * }
 *
 * $mimeFile = getMimeType($path);
 *************************************************************/

$mimeFile = 'application/octet-stream';

if (class_exists('finfo')) {  //this should exist for >= PHP 5.3
    $finfo    = new finfo(FILEINFO_MIME);
    $mimeFile = $finfo->file($filename);
} else {
    $mimeTypes = require_once $GLOBALS['xoops']->path('www/include/mimetypes.inc.php');
    $extArray  = explode('.', $filename);
    $ext       = mb_strtolower(array_pop($extArray));
    if (array_key_exists($ext, $mimeTypes)) {
        $mimeFile = $mimeTypes[$ext];
    }
}

header('Content-Description: File Transfer');
//@TODO should this be replaced by:
//header('Content-Type: ' . $mimeFile);
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
