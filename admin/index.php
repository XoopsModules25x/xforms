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
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @link      https://github.com/XoopsModules25x/xforms
 * @since     1.30
 *
 */

use Xmf\Module\Admin;
use Xmf\Request;
use Xmf\Yaml;
use XoopsModules\Xforms\{Common,
    Common\TestdataButtons,
    Constants,
    Forms,
    FormsHandler,
    Helper,
    Utility
};

/** @var Admin $adminObject */
/** @var Helper $helper */
/** @var Utility $utility */
/** @var FormsHandler $formsHandler */

require_once __DIR__ . '/admin_header.php';

/**
 * @var \Xmf\Module\Admin                 $adminObject
 * @var \XoopsModules\Xforms\Helper       $helper
 * @var \XoopsModules\Xforms\FormsHandler $formsHandler
 *
 * @var string                            $moduleDirName
 */

xoops_cp_header();
$criteria           = new \CriteriaCompo();
$criteria->setGroupBy('form_active');
$criteria->add(new \Criteria('form_active', Constants::FORM_ACTIVE, '='));
$totalForms         = (int)$formsHandler->getCount();
$totalActiveForms   = (int)$formsHandler->getCount($criteria);
$totalInactiveForms = $totalForms - $totalActiveForms;

$adminObject->addInfoBox(_MD_XFORMS_DASHBOARD);
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_ACTIVE . '</span>', '<span class="infotext green bold">' . $totalActiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_INACTIVE . '</span>', '<span class="infotext red bold">' . $totalInactiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_FORMS . '</span>', '<span class="infotext bold">' . $totalForms . '</span>'));

// Check for profile module
/* @var XoopsModules\Profile\Helper $profileHelper */
$profileHelper = \Xmf\Module\Helper::getHelper('profile');
if (false === $profileHelper) {
    $adminObject->addConfigWarning(sprintf(_MD_XFORMS_PROFILE_NOT_FOUND, $moduleDirName));
} else {
    $adminObject->addConfigAccept(sprintf(_MD_XFORMS_PROFILE_FOUND, $moduleDirName));
}

$utility = new Utility();

//check for latest release
//$newRelease = $utility::checkVerModule($helper);
//if (null !== $newRelease) {
//    $adminObject->addItemButton($newRelease[0], $newRelease[1], 'download', 'style="color : Red"');
//}

$adminObject->displayNavigation(basename(__FILE__));

//------------- Test Data Buttons ----------------------------
if ($helper->getConfig('displaySampleButton')) {
    TestdataButtons::loadButtonConfig($adminObject);
    $adminObject->displayButton('left', '');
}
$op = Request::getString('op', 0, 'GET');
switch ($op) {
    case 'hide_buttons':
        TestdataButtons::hideButtons();
        break;
    case 'show_buttons':
        TestdataButtons::showButtons();
        break;
}
//------------- End Test Data Buttons ----------------------------

$adminObject->displayIndex();
echo $utility::getServerStats();

//codeDump(__FILE__);
require_once __DIR__ . '/admin_footer.php';
