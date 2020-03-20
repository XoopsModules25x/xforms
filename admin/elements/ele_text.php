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
 * @package   \XoopsModules\Xforms\admin\elements
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link http://xoops.org XOOPS Project}
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\FormInput;
use Xmf\Module\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/* @var \XoopsModules\Xforms\Helper $helper */
$helper = Helper::getInstance();

/**
 * Text element
 *
 * value [0] = width of text box
 *       [1] = max input size
 *       [2] = default value
 *       [3] = isEmail (0 = no, else = yes)
 *       [4] = placeholder
 */

$sizeAttr = !empty($value[0]) ? (int)$value[0] : $helper->getConfig('t_width');
$maxAttr  = !empty($value[1]) ? (int)$value[1] : $helper->getConfig('t_max');
$size     = new FormInput(_AM_XFORMS_ELE_SIZE, 'ele_value[0]', 5, 5, (string)$sizeAttr, null, 'number');
$size->setAttribute('min', 0);
$size->setExtra('style="width: 5em;"');

$max        = new FormInput(_AM_XFORMS_ELE_MAX_LENGTH, 'ele_value[1]', 5, 5, (string)$maxAttr, null, 'number');
$max->setAttribute('min', 1);
$max->setExtra('style="width: 5em;"');
$defVal     = isset($value[2]) ?$myts->htmlSpecialChars($value[2]) : '';
$default    = new \XoopsFormText('', 'ele_value[2]', 50, 255, $defVal);
$selDefault = new \XoopsFormSelect(_AM_XFORMS_ELE_TEXT_ADD_DEFAULT, 'ele_value_2_add');
$selDefault->addOption('', _AM_XFORMS_ELE_TEXT_ADD_DEFAULT_SEL);

$memberHelper  = xoops_getHandler('member');
$oUser         = $memberHelper->createUser();
$uVars         = $oUser->vars;
foreach ($uVars as $uk => $uv) {
    if ('pass' !== $uk && (XOBJ_DTYPE_TXTBOX == $uv['data_type']
        || XOBJ_DTYPE_UNICODE_TXTBOX == $uv['data_type']))
    {
        $selDefault->addOption('{U_' . $uk . '}', 'User: ' . $uk);
    }
}

//check to see if profile module is active
$profileHelper = Helper::getHelper('profile');
if (false !== $profileHelper) {
    $profileHandler = $profileHelper->getHandler('profile');
    $oProfile = $profileHandler->create();
    $pVars    = $oProfile->vars;

    foreach ($pVars as $pk => $pv) {
        if (!isset($uVars[$pk]) && (XOBJ_DTYPE_TXTBOX == $pv['data_type']
            || XOBJ_DTYPE_UNICODE_TXTBOX == $pv['data_type']))
        {
            $selDefault->addOption('{P_' . $pk . '}', 'Profile: ' . $pk);
        }
    }
    unset($uVars, $pVars, $oUser, $oProfile, $profileHandler);
}

$selDefault->setExtra('onchange="document.getElementById(\'ele_value[2]\').value += this.value;"');
$defaultTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_DEFAULT, '<br>');
$defaultTray->addElement($default);
$defaultTray->addElement($selDefault);
$defaultTray->setDescription(_AM_XFORMS_ELE_TEXT_DESC);

$contEmail = (isset($value[3]) && ((int)$value[3] > Constants::FIELD_IS_NOT_EMAIL)) ? Constants::FIELD_IS_EMAIL : Constants::FIELD_IS_NOT_EMAIL;
$emailIndicator = new \XoopsFormRadioYN(_AM_XFORMS_ELE_CONTAINS_EMAIL, 'ele_value[3]', $contEmail, _YES, _NO);
$emailIndicator->setDescription(_AM_XFORMS_ELE_CONTAINS_EMAIL_DESC);

$plAttrib = isset($value[4]) ? $myts->htmlSpecialChars($value[4]) : '';
$placeholder = new \XoopsFormText(_AM_XFORMS_ELE_PLACEHOLDER, 'ele_value[4]', $sizeAttr, $maxAttr, $plAttrib);

$output->addElement($size, 1);
$output->addElement($max, 1);
$output->addElement($placeholder);
$output->addElement($defaultTray);
$output->addElement($emailIndicator);
