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
 * @package   \XoopsModules\Xforms\include
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    trabis <lusopoemas@gmail.com>
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */

use Xmf\Database\Tables;
use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\Utility;

/**
 * @internal {Make sure you PROTECT THIS FILE}
 */
if ((!defined('XOOPS_ROOT_PATH'))
    || !($GLOBALS['xoopsUser'] instanceof \XoopsUser)
    || !($GLOBALS['xoopsUser']->isAdmin())) {
    exit('Restricted access' . PHP_EOL);
}

/**
 * Upgrade works to update Xforms from previous versions
 *
 * @param \XoopsModule $module
 * @param string       $prev_version version * 100
 *
 * @return bool
 * @uses \XoopsModules\Xforms\Utility
 *
 * @uses \Xmf\Module\Admin
 */
function xoops_module_update_xforms(\XoopsModule $module, $prev_version)
{
    $utility = new Utility();

    $success = true;
    $success = $utility::checkVerXoops($module);
    $success = $utility::checkVerPhp($module);
    if (!$success) {
        return false;
    }
    /*
     =============================================================
     Upgrade for Xforms < 2.0
     =============================================================
     =====================================
     - rename xforms_forms to xforms_form
     - init following columns in xforms_form:
     =====================================
     form_save_db       tinyint(1)
     form_send_to_other varchar(255)
     form_send_copy     tinyint(1)
     form_email_header  text
     form_email_footer  text
     form_email_uheader text
     form_email_ufooter text
     form_display_style varchar(1)
     form_begin         int(10)
     form_end           int(10)
     form_active        tinyint(1)
     =====================================
     - rename xforms_formelements to xforms_element
     - add index disp_ele_by_form
     - change all ele_type 'select2' column data to 'country'
     =====================================
     - create the xforms_userdata table
     =====================================
     - remove old .css, .js, and .image
       and (sub)directories if they exist
     - remove old element files (./admin/ele_*.php)
     =====================================
     =============================================================
    */

    $success = true;
    /* @var \XoopsModules\Xforms\Helper $helper */
    $helper = Helper::getInstance();
    $helper->loadLanguage('modinfo');

    require_once $helper->path('include/common.php');

    if ($prev_version < 200) {
        $migrate = new Tables();

        //-------------------------------
        //   Forms table modifications
        //-------------------------------
        $oldTableName    = $modulePrefix . '_forms';
        $mainTableName   = $modulePrefix . '_form';
        $oldTableExists  = $migrate->useTable($oldTableName);
        $mainTableExists = $migrate->useTable($mainTableName);

        if (!$oldTableExists) {
            $module->setErrors(sprintf(_MI_XFORMS_INST_NO_TABLE, $oldTableName));

            return false;
        } elseif ($mainTableExists) {
            $module->setErrors(sprintf(_MI_XFORMS_INST_TABLE_EXISTS, $mainTableName));

            return false;
        }
        // Rename table to new table name
        $success = $migrate->renameTable($oldTableName, $mainTableName);
        $success &= $migrate->executeQueue();
        if (false === $success) {
            $module->setErrors($migrate->getLastError());

            return false;
        }

        // Modify Form table - add columns
        $columnArray = [
            ['form_save_db', "tinyint(1) NOT NULL default '1'"],
            ['form_send_to_other', "varchar(255) NOT NULL default ''"],
            ['form_send_copy', "tinyint(1) NOT NULL default '1'"],
            ['form_email_header', 'text NOT NULL'],
            ['form_email_footer', 'text NOT NULL'],
            ['form_email_uheader', 'text NOT NULL'],
            ['form_email_ufooter', 'text NOT NULL'],
            ['form_display_style', "varchar(1) NOT NULL default 'f'"],
            ['form_begin', "int(10) unsigned NOT NULL default '0'"],
            ['form_end', "int(10) unsigned NOT NULL default '0'"],
            ['form_active', "tinyint(1) NOT NULL default '1'"],
        ];

        $migrate->resetQueue();
        $migrate->useTable($mainTableName);
        foreach ($columnArray as $column) {
            if (false === $migrate->addColumn($mainTableName, $column[0], $column[1])) {
                $module->setErrors($migrate->getLastError());

                return false;
            }
        }

        if (false === $migrate->executeQueue()) {
            $module->setErrors($migrate->getLastError());

            return false;
        }

        //-------------------------------
        // Elements table modifications
        //-------------------------------
        // rename the old element table
        $migrate->resetQueue();
        $oldTableName    = $modulePrefix . '_formelements';
        $oldTableExists  = $migrate->useTable($oldTableName);
        $mainTableName   = $modulePrefix . '_element';
        $mainTableExists = $migrate->useTable($mainTableName);

        if (!$oldTableExists) {
            $module->setErrors(sprintf(_MI_XFORMS_INST_NO_TABLE, $oldTableName));

            return false;
        } elseif ($mainTableExists) {
            $module->setErrors(sprintf(_MI_XFORMS_INST_TABLE_EXISTS, $mainTableName));

            return false;
        }
        // rename table to new table name
        $success = $migrate->renameTable($oldTableName, $mainTableName);
        $success &= $migrate->executeQueue();
        if (false === $success) {
            $module->setErrors($migrate->getLastError());

            return false;
        }

        // Add index to improve performance
        $migrate->resetQueue();
        $migrate->useTable($mainTableName);
        $success &= $migrate->addIndex('disp_ele_by_form', $mainTableName, 'form_id, ele_display');
        $success &= $migrate->executeQueue();
        if (false === $success) {
            $module->setErrors($migrate->getLastError());
        }

        // Change all 'select2' elements to 'country'
        $migrate->resetQueue();
        $success = $migrate->useTable($mainTableName);
        $success &= $migrate->update($mainTableName, ['ele_type' => 'country'], new \Criteria('ele_type', 'select2'));
        // Change ele_id from smallint(5) to mediumint(8)
        $success &= $migrate->alterColumn($mainTableName, 'ele_id', 'mediumint(8) NOT NULL auto_increment');
        // Change ele_caption from varchar(255) to text
        $success &= $migrate->alterColumn($mainTableName, 'ele_caption', 'text NOT NULL');
        $success &= $migrate->executeQueue();
        if (false === $success) {
            $module->setErrors($migrate->getLastError());
        }

        //-------------------------------
        //  Create the UserData table
        //-------------------------------
        $migrate->resetQueue();
        $success = $mainTableName = $modulePrefix . '_userdata';
        $success &= $migrate->addTable($mainTableName);

        // Add UserData table columns
        $columnArray = [
            ['udata_id', 'int(11) unsigned NOT NULL auto_increment'],
            ['uid', "mediumint(8) unsigned NOT NULL default '0'"],
            ['form_id', 'smallint(5) NOT NULL'],
            ['ele_id', 'mediumint(8) NOT NULL'],
            ['udata_time', "int(10) unsigned NOT NULL default '0'"],
            ['udata_ip', "varchar(100) NOT NULL default '0.0.0.0'"],
            ['udata_agent', "varchar(500) NOT NULL default ''"],
            ['udata_value', 'text NOT NULL'],
        ];
        foreach ($columnArray as $column) {
            if (false === $migrate->addColumn($mainTableName, $column[0], $column[1])) {
                $module->setErrors($migrate->getLastError());

                return false;
            }
        }

        // Add primary key to table
        $success = $migrate->addPrimaryKey($mainTableName, 'udata_id');
        $success &= $migrate->executeQueue();
        if (!$success) {
            $module->setErrors($migrate->getLastError());

            return false;
        }

        unset($migrate);

        //----------------------------------------------------------------
        // Remove previous .css, .js and .images directories since they're
        // being moved to ./assets
        //----------------------------------------------------------------
        $old_directories = [
            $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/css/'),
            $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/js/'),
            $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/images/'),
        ];
        foreach ($old_directories as $old_dir) {
            $dirInfo = new \SplFileInfo($old_dir);
            if ($dirInfo->isDir()) {
                // Directory exists so try and delete it
                $success = $utility::deleteDirectory($old_dir);
            }
        }
        if (!$success) {
            $module->setErrors(_MI_XFORMS_INST_NO_DEL_DIRS);

            return false;
        }
    }
    //-----------------------------------------------------------------------
    // Remove ./template/*.html (except index.html) files since they're being
    // replaced by *.tpl files
    //-----------------------------------------------------------------------
    // Remove old files
    $directory = $helper->path('templates/');
    //    $directory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/templates/');
    $dirInfo = new \SplFileInfo($directory);
    // Validate is a directory
    if ($dirInfo->isDir()) {
        $fileList = array_diff(scandir($directory, SCANDIR_SORT_NONE), ['..', '.', 'index.html']);
        foreach ($fileList as $k => $v) {
            if (!preg_match('/.tpl+$/i', $v)) {
                $fileInfo = new \SplFileInfo($directory . $v);
                if ($fileInfo->isDir()) {
                    // Recursively handle subdirectories
                    if (!($success = $utility::deleteDirectory($directory . $v))) {
                        break;
                    }
                } elseif ($fileInfo->isFile()) {
                    if (!($success = unlink($fileInfo->getRealPath()))) {
                        break;
                    }
                }
            }
        }
    } else {
        // Couldn't find template directory - that's bad
        $module->setErrors(sprintf(_MI_XFORMS_INST_DIR_NOT_FOUND, htmlspecialchars($directory, ENT_QUOTES | ENT_HTML5)));
        $success = false;
    }

    if ($success) { // ok, continue
        //---------------------------------------------------------------
        // Remove ./admin/ele_*.php files since they're being replaced by
        // ./admin/elements/ele_*.php files
        //---------------------------------------------------------------
        $directory = $GLOBALS['xoops']->path('modules/' . $module->getVar('dirname', 'n') . '/admin/');
        $dirInfo   = new \SplFileInfo($directory);
        // Validate directory exists
        if ($dirInfo->isDir()) {
            $fileList = array_diff(scandir($directory, SCANDIR_SORT_NONE), ['..', '.', 'index.html']);
            foreach ($fileList as $k => $v) {
                if (preg_match('/^(ele_).*(\.php)$/i', $v)) {
                    $fileInfo = new \SplFileInfo($directory . $v);
                    if ($fileInfo->isFile()) {
                        if (!($success = unlink($fileInfo->getRealPath()))) {
                            break;
                        }
                    }
                }
            }
        } else {
            // Couldn't find ./admin directory - that's bad
            $module->setErrors(sprintf(_MI_XFORMS_INST_DIR_NOT_FOUND, htmlspecialchars($directory, ENT_QUOTES | ENT_HTML5)));
            $success = false;
        }
    }

    return $success;
}
