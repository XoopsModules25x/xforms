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

// defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$module_handler = xoops_gethandler('module');
$module         = $module_handler->getByDirname(basename(dirname(__DIR__)));
$pathIcon32     = $module->getInfo('icons32');
xoops_loadLanguage('admin', $module->dirname());

$adminmenu              = array();
$i                      = 1;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU0;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/home.png';
++$i;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU1;
$adminmenu[$i]['link']  = 'admin/main.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/manage.png';
++$i;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU2;
$adminmenu[$i]['link']  = 'admin/main.php?op=edit';
$adminmenu[$i]['icon']  = $pathIcon32 . '/add.png';
++$i;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU3;
$adminmenu[$i]['link']  = 'admin/editelement.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/insert_table_row.png';
++$i;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU4;
$adminmenu[$i]['link']  = 'admin/report.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/content.png';
++$i;
$adminmenu[$i]['title'] = _MI_XFORMS_ADMENU5;
$adminmenu[$i]['link']  = "admin/about.php";
$adminmenu[$i]['icon']  = $pathIcon32 . '/about.png';
