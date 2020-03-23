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
 * @package         \XoopsModules\Xforms\admin
 * @author          XOOPS Module Development Team
 * @copyright       Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           1.30
 *
 * @see             \Xmf\Module\Helper
 * @see             \Xmf\Module\Admin
 */
require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once dirname(__DIR__) . '/include/common.php';

/* @var \Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();
/* @var \XoopsModules\Xforms\Helper $helper */
$helper = \XoopsModules\Xforms\Helper::getInstance();
/* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
$formsHandler = $helper->getHandler('Forms');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
$helper->loadLanguage('common');

// Load/Instantiate form classes
xoops_load('xoopsformloader');
