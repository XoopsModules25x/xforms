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

use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\Common;

require_once __DIR__ . '/admin_header.php';

/**
 * @var \Xmf\Module\Admin $adminObject
 * @var \XoopsModules\Xforms\Helper $helper
 * @var \XoopsModules\Xforms\FormsHandler $formsHandler
 *
 * @var string $moduleDirName
 */

xoops_cp_header();
$criteria = new \Criteria('');
$criteria->setGroupBy('form_active');
$formCount          = $formsHandler->getCount($criteria);
$totalForms         = array_sum($formCount);
$totalActiveForms   = isset($formCount[1]) ? $formCount[1] : 0;
$totalInactiveForms = $totalForms - $totalActiveForms;

$adminObject->addInfoBox(_MD_XFORMS_DASHBOARD);
$adminObject->AddInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_ACTIVE . '</span>', '<span class="infotext green bold">' . $totalActiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_INACTIVE . '</span>', '<span class="infotext red bold">' . $totalInactiveForms . '</span>'));
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_XFORMS_TOTAL_FORMS . '</span>', '<span class="infotext bold">' . $totalForms . '</span>'));

// Check for profile module
$profileHelper = \Xmf\Module\Helper::getHelper('profile');
if (false === $profileHelper) {
    $adminObject->addConfigWarning(sprintf(_MD_XFORMS_PROFILE_NOT_FOUND, $moduleDirName));
} else {
    $adminObject->addConfigAccept(sprintf(_MD_XFORMS_PROFILE_FOUND, $moduleDirName));
}

/** @var \XoopsModules\Xforms\Utility $utility */
$utility = new \XoopsModules\Xforms\Utility();

//check for latest release
//$newRelease = $utility::checkVerModule($helper);
//if (!empty($newRelease)) {
//    $adminObject->addItemButton($newRelease[0], $newRelease[1], 'download', 'style="color : Red"');
//}

$adminObject->displayNavigation(basename(__FILE__));
//------------- Test Data ----------------------------

if ($helper->getConfig('displaySampleButton')) {
    $yamlFile            = dirname(__DIR__) . '/config/admin.yml';
    $config              = loadAdminConfig($yamlFile);
    $displaySampleButton = $config['displaySampleButton'];

    if (1 == $displaySampleButton) {
        xoops_loadLanguage('admin/modulesadmin', 'system');
        require_once dirname(__DIR__) . '/testdata/index.php';

        $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'ADD_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=load', 'add');
        $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'SAVE_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=save', 'add');
        //    $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA'), '__DIR__ . /../../testdata/index.php?op=exportschema', 'add');
        $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'HIDE_SAMPLEDATA_BUTTONS'), '?op=hide_buttons', 'delete');
    } else {
        $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'SHOW_SAMPLEDATA_BUTTONS'), '?op=show_buttons', 'add');
        $displaySampleButton = $config['displaySampleButton'];
    }
    $adminObject->displayButton('left', '');
}

//------------- End Test Data ----------------------------

$adminObject->displayIndex();

/**
 * @param $yamlFile
 * @return array|bool
 */
function loadAdminConfig($yamlFile)
{
    $config = \Xmf\Yaml::readWrapped($yamlFile); // work with phpmyadmin YAML dumps
    return $config;
}

/**
 * @param $yamlFile
 */
function hideButtons($yamlFile)
{
    $app['displaySampleButton'] = 0;
    \Xmf\Yaml::save($app, $yamlFile);
    redirect_header('index.php', 0, '');
}

/**
 * @param $yamlFile
 */
function showButtons($yamlFile)
{
    $app['displaySampleButton'] = 1;
    \Xmf\Yaml::save($app, $yamlFile);
    redirect_header('index.php', 0, '');
}

$op = \Xmf\Request::getString('op', 0, 'GET');

switch ($op) {
    case 'hide_buttons':
        hideButtons($yamlFile);
        break;
    case 'show_buttons':
        showButtons($yamlFile);
        break;
}

echo $utility::getServerStats();

require __DIR__ . '/admin_footer.php';
