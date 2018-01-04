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

include __DIR__ . '/admin_header.php';
$file = Request::getString('f', '', 'GET');
$path = XFORMS_UPLOAD_PATH . $file;
if (!$file || !preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path)) {
    redirect_header($GLOBALS['xoops']->url('www'), XformsConstants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}

header('Content-Type: application/octet-stream');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: private, no-cache');
header('Pragma: no-cache');
header("Content-Disposition: attachment; filename='{$file}'");
header("Content-Length: {filesize($path)}");

readfile($path);
