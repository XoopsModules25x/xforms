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
 * @package   \XoopsModules\Xforms\admin
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 *
 * @see       \Xmf\Module\Helper
 * @see       \Xmf\Module\Admin
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;

require_once __DIR__ . '/admin_header.php';

xoops_cp_header();
/* @var \XoopsModules\Xforms\FormsHandler $formsHandler */
$totalForms = $formsHandler->getCount();
$criteria   = new \CriteriaCompo();
$criteria->add(new \Criteria('form_active', Constants::FORM_ACTIVE, '='));
$totalActiveForms   = $formsHandler->getCount($criteria);
$totalInactiveForms = $totalForms - $totalActiveForms;

/* @var \Xmf\Module\Admin $adminObject */
$adminObject->addInfoBox(_MD_XFORMS_DASHBOARD);
$adminObject->AddInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_ACTIVE . '</span>', '<span class="infotext green bold">' . $totalActiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_INACTIVE . '</span>', '<span class="infotext red bold">' . $totalInactiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_FORMS . '</span>', '<span class="infotext bold">' . $totalForms . '</span>'));

// Check for profile module
/* @var Xmf\Module\Helper $profileHelper */
$profileHelper = \Xmf\Module\Helper::getHelper('profile');
if (false === $profileHelper) {
    $adminObject->addConfigWarning(sprintf(_MD_XFORMS_PROFILE_NOT_FOUND, $moduleDirName));
} else {
    $adminObject->addConfigAccept(sprintf(_MD_XFORMS_PROFILE_FOUND, $moduleDirName));
}

/* @var \XoopsModules\Xforms\Utility $utility */
$utility = new \XoopsModules\Xforms\Utility();

//check for latest release
$newRelease = $utility::checkVerModule($helper);
if (!empty($newRelease)) {
    $adminObject->addItemButton($newRelease[0], $newRelease[1], 'download', 'style="color : Red"');
}

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

include __DIR__ . '/admin_footer.php';
