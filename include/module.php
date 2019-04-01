<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once(XOOPS_ROOT_PATH . "/modules/xforms/include/migrate.php");

/**
 * @param $xoopsModule
 *
 * @return bool
 */
function xoops_module_pre_install_xforms(&$xoopsModule)
{
    // NOP
    return true;
}

/**
 * @param $module
 *
 * @return bool
 */
function xoops_module_install_xforms(&$module)
{
    global $moduleperm_handler;

    for ($i = 1; $i < 4; ++$i) {
        //$migrate->copyTable('demomvc_log', 'demomvc_log2', true);
        //$migrate->addColumn('demomvc_log2', 'log_xint', 'log_start_time','int(10) not null default \'0\'' );
        //$migrate->update('demomvc_log2', array('log_xint' => time()), '');
        //$migrate->queueExecute(true);

        $perm = $moduleperm_handler->create();
        $perm->setVar('gperm_name', 'xforms_form_access');
        $perm->setVar('gperm_itemid', 1);
        $perm->setVar('gperm_groupid', $i);
        $perm->setVar('gperm_modid', $module->getVar('mid'));
        $moduleperm_handler->insert($perm);
    }

    return true;
}

/**
 * @param $xoopsModule
 * @param $prev_version
 *
 * @return bool
 */
function xoops_module_update_xforms(&$xoopsModule, $prev_version)
{
    /** @todo
    //---------------------------------------------------------------
    // Remove ./admin/ele_*.php files since they're being replaced by
    // ./admin/elements/ele_*.php files
    //---------------------------------------------------------------
    */
    ob_start();
    update_tables_to_130($xoopsModule);
    $feedback = ob_get_clean();
    if (method_exists($xoopsModule, "setMessage")) {
        $xoopsModule->setMessage($feedback);
    } else {
        echo $feedback;
        //$migrate->copyTable('demomvc_log', 'demomvc_log2', true);
        //$migrate->addColumn('demomvc_log2', 'log_xint', 'log_start_time','int(10) not null default \'0\'' );
        //$migrate->update('demomvc_log2', array('log_xint' => time()), '');
        //$migrate->queueExecute(true);
    }

    return true;
}

/**
 * @param $xoopsModule
 *
 * @return bool
 */
function xoops_module_pre_uninstall_xforms(&$xoopsModule)
{
    // NOP
    return true;
}

/**
 * @param $xoopsModule
 */
function xoops_module_uninstall_xforms(&$xoopsModule)
{
    // NOP
    return true;
}

// =========================================================================================
// This function updates any existing table of a 1.2x version to the format used
// in the release of xForms 1.30
// =========================================================================================
/**
 * @param $module
 */
function update_tables_to_130($module)
{

    $migrate = new Migrate;
//    $migrate->copyTable('demomvc_log', 'demomvc_log2', true);
//    $migrate->addColumn('demomvc_log2', 'log_xint', 'log_start_time','int(10) not null default \'0\'' );
//    $migrate->update('demomvc_log2', array('log_xint' => time()), '');
//    $migrate->queueExecute(true);

    $migrate->addTable('zzztest');
    $migrate->addColumn('zzztest', 'log_xint', 'int(10) not null default \'0\'');
//    $migrate->update('zzztest', array('log_xint' => time()), '');
//    $migrate->addColumn('zzztest', 'log_xint2', '','int(10) not null default \'0\'' );
//    $migrate->update('zzztest', array('log_xint2' => time()), '');
    $migrate->queueExecute(true);

}
