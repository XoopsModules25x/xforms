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

include 'admin_header.php';
$file = isset($_GET['f']) ? trim($_GET['f']) : '';
$path = XFORMS_UPLOAD_PATH.$file;
if ( !$file || !preg_match('/^[0-9]+_{1}[0-9a-z]+\.[0-9a-z]+$/', $file) || !file_exists($path) ) {
    redirect_header(XOOPS_URL, 0, _AM_XFORMS_NOTHING_SELECTED);
}

header("Content-Type: application/octet-stream");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Cache-Control: private, no-cache');
header("Pragma: no-cache");
header('Content-Disposition: attachment; filename="'.$file.'"');
header("Content-Length: ".filesize($path));

readfile($path);
