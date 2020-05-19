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
 * Admin header file
 *
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 *
 * @see       \Xmf\Module\Admin
 */

include dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);
$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');

/** @var \XoopsModules\Xforms\Helper $helper */
$helper = \XoopsModules\Xforms\Helper::getInstance();
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');

$adminmenu = [
    [
        'title' => _MI_XFORMS_ADMENU0,
        'link'  => 'admin/index.php',
        'desc'  => _MI_XFORMS_ADMENU0_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('home.png'),
    ],
    [
        'title' => _MI_XFORMS_ADMENU1,
        'link'  => 'admin/main.php',
        'desc'  => _MI_XFORMS_ADMENU1_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('manage.png'),
    ],
    [
        'title' => _MI_XFORMS_ADMENU2,
        'link'  => 'admin/main.php?op=edit',
        'desc'  => _MI_XFORMS_ADMENU2_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('add.png'),
    ],
    [
        'title' => _MI_XFORMS_ADMENU3,
        'link'  => 'admin/editelement.php',
        'desc'  => _MI_XFORMS_ADMENU3_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('insert_table_row.png'),
    ],
    [
        'title' => _MI_XFORMS_ADMENU4,
        'link'  => 'admin/report.php',
        'desc'  => _MI_XFORMS_ADMENU4_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('content.png'),
    ],
    [
        'title' => _MI_XFORMS_ADMENU6,
        'link'  => 'admin/import.php',
        'desc'  => _MI_XFORMS_ADMENU6_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('exec.png'),
    ],


// Blocks Admin
[
        'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS'),
        'link' => 'admin/blocksadmin.php',
        'icon' => \Xmf\Module\Admin::menuIconPath('block.png'),
    ],
];

if (is_object($helper->getModule()) && $helper->getConfig('displayDeveloperTools')) {
    $adminmenu[] = [
        'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_MIGRATE'),
        'link' => 'admin/migrate.php',
        'icon' => $pathIcon32 . '/database_go.png',
    ];
}

$adminmenu[] = [
        'title' => _MI_XFORMS_ADMENU5,
        'link'  => 'admin/about.php',
        'desc'  => _MI_XFORMS_ADMENU5_DESC,
        'icon'  => \Xmf\Module\Admin::menuIconPath('about.png'),
    ];

