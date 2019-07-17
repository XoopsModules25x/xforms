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
 * @since           2.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted Access');

/**
 * @param XoopsModule $xoopsModule
 *
 * @return bool success
 */
function xoops_module_pre_install_xforms(XoopsModule $xoopsModule)
{
    class_exists('Xmf\Module\Admin') || exit('XMF is required.');

    if (!class_exists('XformsUtilities')) {
        xoops_load('utilities', 'xforms');
    }
    //check for minimum XOOPS version
    if (!XformsUtilities::checkXoopsVer($xoopsModule)) {
        return false;
    }
    // check for minimum PHP version
    if (!XformsUtilities::checkPHPVer($xoopsModule)) {
        return false;
    }

    return true;
}

/**
 * @param XoopsModule $xoopsModule
 *
 * @return bool success
 */
function xoops_module_install_xforms(XoopsModule $xoopsModule)
{
    //@TODO check to see if uploads/xforms exists and create if not
    /*
        // no pre-installed forms so don't need to set permissions yet
        for ($i = 1; $i < 4; ++$i) {
            $perm = $GLOBALS['modulepermHandler']->create();
            $perm->setVars(array('gperm_name' => 'xforms_form_access',
                               'gperm_itemid' => 1,
                              'gperm_groupid' => $i,
                                'gperm_modid' => $xoopsModule->getVar('mid'))
            );
            $GLOBALS['modulepermHandler']->insert($perm);
        }
    */
    /*********************************
     * Add uploads directory to store files
     *********************************/
    $success = true;

    /*
        $dir = $GLOBALS['xoops']->path(XOOPS_UPLOAD_PATH . '/' . $xoopsModule->getVar('dirname', 'n'));
        $dirInfo = new SplFileInfo($dir);
        if (!$dirInfo->isDir()) {
            // create directory if it doesn't exist
            $success = mkdir($dir, 0777);
        }
        if (false !== $success) {
            // directory exists so create index.html file
            // make sure there's an index.html file
            $fileInfo = new SplFileInfo("{$dir}/index.html");
            if (!$fileInfo->isFile()) {
                // index file doesn't exist so create it
                $fhandle = fopen("{dir}/index.html", 'w');
                if (false === $fhandle) {
                    // couldn't create file
                    $success = false;
                } else {
                    // write out file
                    $string = '<script>history.go(-1);</script>';
                    $success = fwrite($fhandle, $string);
                    $success = $success & fclose($fhandle);
                }
            }
        }
    */

    return $success;
}
