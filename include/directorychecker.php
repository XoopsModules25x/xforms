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

//defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

require_once __DIR__ . '/../../../mainfile.php';
require_once $GLOBALS['xoops']->path('./modules/xforms/class/constants.php');
//xoops_load('constants', 'xforms');
xoops_load('directorychecker', 'xforms');

$path     = Request::getString('path', null, 'POST');
$redirect = Request::getString('redirect', null, 'POST');
$op       = Request::getCmd('op', '', 'POST');
switch ($op) {
    case 'createdir':
        $msg = XformsDirectoryChecker::createDirectory($path) ? _DC_XFORMS_DIRCREATED : _DC_XFORMS_DIRNOTCREATED;
        break;

    case 'setdirperm':
        $mode = Request::getString('mode', null, 'POST');
        $msg  = XformsDirectoryChecker::setDirectoryPermissions($path, $mode) ? _DC_XFORMS_PERMSET : _DC_XFORMS_PERMNOTSET;
        break;
}
redirect_header($redirect, XformsConstants::REDIRECT_DELAY_MEDIUM, "{$msg}: {$path}");
