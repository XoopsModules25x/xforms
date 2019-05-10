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
 * Module: XForms
 *
 * @package   \XoopsModules\Xforms\frontside
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
use \XoopsModules\Xforms;
use \XoopsModules\Xforms\Constants;
use \XoopsModules\Xforms\Helper as xHelper;
use \Xmf\Request;

ob_start(); //To prevent problems in file send
require __DIR__ . '/header.php';

$udid  = Request::getInt('ui', 0, 'GET');
$form  = Request::getInt('fm', 0, 'GET');
$elem  = Request::getInt('el', 0, 'GET');
$file  = Request::getString('f', '', 'GET');
$fname = Request::getString('fn', '', 'GET');

if (empty($file)) {
    if ((0 === $udid) || (0 === $form) || (0 === $elem)) {
        //Error in params to read file
        //@todo - test ob_end_clean here - added in v2.00 ALPHA 2
        ob_end_clean();
        exit();
    }
    /* @var \XoopsModules\Xforms\Helper $helper */
    $helper       = xHelper::getInstance();
    $uDataHandler = $helper::getInstance()->getHandler('UserData');
    //$uDataHandler = $helper->getHandler('userdata');
    if (!($uData  = $uDataHandler->get($udid))
        || ((int)$uData->getVar('form_id') !== $form)
        || ((int)$uData->getVar('ele_id') !== $elem))
    {
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
//}

//if (empty($file)) {
    //@todo - shouldn't this be an ob_end_clean here instead of ob_end_flush?
    ob_end_flush();
    redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}
if (empty($fname)) {
    $fname = $file;
}

$path = XFORMS_UPLOAD_PATH . $file;
if (!preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path)) {
    //@todo - shouldn't this be an ob_end_clean here instead of ob_end_flush?
    ob_end_flush();
    redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}

ob_end_clean(); /*Clear all contents sending to browser to prevent corruptions in file send*/

$mimeFile = 'application/octet-stream';

if (class_exists('finfo')) { // should exist for >= PHP 5.3
    $finfo    = new \finfo(FILEINFO_MIME);
    //$mimeFile = $finfo->file($filename);
    $mimeFile = $finfo->file($fname);
} else {
    $mimeTypes = include_once $GLOBALS['xoops']->path('www/include/mimetypes.inc.php');
    //$extArray  = explode('.', $filename);
    $extArray  = explode('.', $fname);
    $ext       = strtolower(array_pop($extArray));
    if (array_key_exists($ext, $mimeTypes)) {
        $mimeFile = $mimeTypes[$ext];
    }
}

header('Content-Description: File Transfer');
/**@todo check to see if Content-Type should be replaced by:
header('Content-Type: ' . $mimeFile); */
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
