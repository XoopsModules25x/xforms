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

use Xmf\Module\Admin;
use Xmf\Module\Helper;

require_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$xformsFormsHandler = $xformsHelper->getHandler('forms');
$totalForms         = $xformsFormsHandler->getCount();
$criteria           = new CriteriaCompo();
$criteria->add(new Criteria('form_active', XformsConstants::FORM_ACTIVE, '='));
$totalActiveForms   = $xformsFormsHandler->getCount($criteria);
$totalInactiveForms = $totalForms - $totalActiveForms;

$moduleAdmin = Admin::getInstance();
$moduleAdmin->addInfoBox(_MD_XFORMS_DASHBOARD);
$moduleAdmin->AddInfoBoxLine(sprintf("<span class='infolabel'>" . _MD_XFORMS_TOTAL_ACTIVE . '</span>', "<span class='infotext green bold'>{$totalActiveForms}</span>"));
$moduleAdmin->addInfoBoxLine(sprintf("<span class='infolabel'>" . _MD_XFORMS_TOTAL_INACTIVE . '</span>', "<span class='infotext red bold'>{$totalInactiveForms}</span>"));
$moduleAdmin->addInfoBoxLine(sprintf("<span class='infolabel'>" . _MD_XFORMS_TOTAL_FORMS . '</span>', "<span class='infotext bold'>{$totalForms}</span>"));

// check for profile module
$profileHelper = Helper::getHelper('profile');
if (false === $profileHelper) {
    $moduleAdmin->addConfigWarning(sprintf(_MD_XFORMS_PROFILE_NOT_FOUND, $moduleDirName));
} else {
    $moduleAdmin->addConfigAccept(sprintf(_MD_XFORMS_PROFILE_FOUND, $moduleDirName));
}



foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
    XformsUtilities::createFolder($uploadFolders[$i]);
    $moduleAdmin->addConfigBoxLine($uploadFolders[$i], 'folder');
    //    $indexAdmin->addConfigBoxLine(array($folder[$i], '777'), 'chmod');
}

$moduleAdmin->displayNavigation(basename(__FILE__));
$moduleAdmin->displayIndex();

include __DIR__ . '/admin_footer.php';
