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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License

 * @since           1.30
 */

use XoopsModules\Xforms;

include dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

/** @var \XoopsModules\Xforms\Helper $helper */
$helper = \XoopsModules\Xforms\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
if (is_object($helper->getModule())) {
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
}

$adminmenu = [
    [
        'title' => _MI_XFORMS_ADMENU0,
        'link'  => 'admin/index.php',
        'icon'  => "{$pathIcon32}/home.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU1,
        'link'  => 'admin/main.php',
        'icon'  => "{$pathIcon32}/manage.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU2,
        'link'  => 'admin/main.php?op=edit',
        'icon'  => "{$pathIcon32}/add.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU3,
        'link'  => 'admin/editelement.php',
        'icon'  => "{$pathIcon32}/insert_table_row.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU4,
        'link'  => 'admin/report.php',
        'icon'  => "{$pathIcon32}/content.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU6,
        'link'  => 'admin/import.php',
        'icon'  => "{$pathIcon32}/exec.png",
    ],
    [
        'title' => _MI_XFORMS_ADMENU5,
        'link'  => 'admin/about.php',
        'icon'  => "{$pathIcon32}/about.png",
    ],
];
