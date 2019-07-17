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

// defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/*
$moduleHandler = xoops_gethandler('module');
$module         = $moduleHandler->getByDirname(basename(dirname(__DIR__)));
$pathIcon32     = $module->getInfo('icons32');
xoops_loadLanguage('admin', $module->dirname());
*/
if (class_exists('Xmf\Module\Admin', true)) {
    $pathIcon32 = Xmf\Module\Admin::menuIconPath('');
} else {
    $moduleHandler = xoops_getHandler('module');
    $module        = $moduleHandler->getByDirname(basename(dirname(__DIR__)));
    $pathIcon32    = $module->getInfo('icons32');
}

$adminmenu = array(
    array(
        'title' => _MI_XFORMS_ADMENU0,
        'link'  => 'admin/index.php',
        'icon'  => "{$pathIcon32}/home.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU1,
        'link'  => 'admin/main.php',
        'icon'  => "{$pathIcon32}/manage.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU2,
        'link'  => 'admin/main.php?op=edit',
        'icon'  => "{$pathIcon32}/add.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU3,
        'link'  => 'admin/editelement.php',
        'icon'  => "{$pathIcon32}/insert_table_row.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU4,
        'link'  => 'admin/report.php',
        'icon'  => "{$pathIcon32}/content.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU6,
        'link'  => 'admin/import.php',
        'icon'  => "{$pathIcon32}/exec.png"
    ),
    array(
        'title' => _MI_XFORMS_ADMENU5,
        'link'  => 'admin/about.php',
        'icon'  => "{$pathIcon32}/about.png"
    )
);
