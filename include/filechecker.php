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

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once __DIR__ . '/../../../mainfile.php';

require_once $GLOBALS['xoops']->path('./modules/xforms/class/constants.php');
xoops_load('filechecker', 'xforms');

$op = Request::getCmd('op', '', 'POST');
if ('copyfile' === $op) {
    $originalFilePath = Request::getString('original_file_path', null, 'POST');
    $filePath         = Request::getString('file_path', null, 'POST');
    $redirect         = Request::getString('redirect', null, 'POST');

    $msg = XformsFileChecker::copyFile($originalFilePath, $filePath) ? _FC_XFORMS_FILECOPIED : _FC_XFORMS_FILENOTCOPIED;
    redirect_header($redirect, Constants::REDIRECT_DELAY_MEDIUM, "{$msg}: {$filePath}");
}
