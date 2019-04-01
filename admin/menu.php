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

$moduleHandler = xoops_gethandler('module');
$module        = $moduleHandler->getByDirname(basename(dirname(__DIR__)));
$pathIcon32    = $module->getInfo('icons32');
xoops_loadLanguage('admin', $module->dirname());

$adminmenu = array(
                   array('title' => _MI_XFORMS_ADMENU0,
                          'link' => 'admin/index.php',
                          'icon' => $pathIcon32 . '/home.png'),

                   array('title' => _MI_XFORMS_ADMENU1,
                          'link' => 'admin/main.php',
                          'icon' => $pathIcon32 . '/manage.png'),

                   array('title' => _MI_XFORMS_ADMENU2,
                          'link' => 'admin/main.php?op=edit',
                          'icon' => $pathIcon32 . '/add.png'),

                   array('title' => _MI_XFORMS_ADMENU3,
                          'link' => 'admin/editelement.php',
                          'icon' => $pathIcon32 . '/insert_table_row.png'),

                   array('title' => _MI_XFORMS_ADMENU4,
                          'link' => 'admin/report.php',
                          'icon' => $pathIcon32 . '/content.png'),

                   array('title' => _MI_XFORMS_ADMENU5,
                          'link' => "admin/about.php",
                          'icon' => $pathIcon32 . '/about.png')
);