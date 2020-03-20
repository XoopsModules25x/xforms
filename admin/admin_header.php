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
 * @copyright Copyright (c) 2001-2019 {@link http://xoops.org XOOPS Project}
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @link      https://github.com/XoopsModules25x/xforms
 *
 * @see \Xmf\Module\Helper
 * @see \Xmf\Module\Admin
 */

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once dirname(__DIR__) . '/include/common.php';

$moduleDirName = basename(dirname(__DIR__));

/**
 * @var \Xmf\Module\Admin $adminObject
 * @var \XoopsModules\Xforms\Helper $helper
 * @var \XoopsModules\Xforms\FormsHandler $formsHandler
 */
$adminObject = \Xmf\Module\Admin::getInstance();
$helper = \XoopsModules\Xforms\Helper::getInstance();
$formsHandler = $helper->getHandler('Forms');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
$helper->loadLanguage('common');

// Load/Instantiate form classes
xoops_load('xoopsformloader');
