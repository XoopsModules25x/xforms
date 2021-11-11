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
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

use Xmf\Request;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';
$file = Request::getString('f', '', 'GET');
$path = XFORMS_UPLOAD_PATH . "/{$file}";
if (!$file || !preg_match('/^\d+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path)) {
    redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_NONE, _AM_XFORMS_NOTHING_SELECTED);
}

header('Content-Type: application/octet-stream');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: private, no-cache');
header('Pragma: no-cache');
header("Content-Disposition: attachment; filename='{$file}'");
header("Content-Length: {filesize($path)}");

readfile($path);
