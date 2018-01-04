<?php namespace XoopsModules\Xforms;

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
 * @author          Mamba, ZySpec
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 */

/**
 * XformsUtility
 *
 * Static utility class to provide common functionality
 *
 */
class Utility
{
    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(XoopsModule $module = null, $requiredVer = null)
    {
        $moduleDirName = basename(dirname(__DIR__));
        if (null === $module) {
            $module = XoopsModule::getByDirname($moduleDirName);
        }
        xoops_loadLanguage('admin', $moduleDirName);

        //check for minimum XOOPS version
        $currentVer = substr(XOOPS_VERSION, 6); // get the numeric part of string
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $success     = true;

        if (version_compare($currentVer, $requiredVer, '<')) {
            $success     = false;
            $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }
    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule
     *
     * @return bool true if meets requirements, false if not
     */
    /*
        public static function checkVerPhp(XoopsModule $module)
        {
            xoops_loadLanguage('admin', $module->dirname());
            // check for minimum PHP version
            $phpLen   = strlen(PHP_VERSION);
            $extraLen = strlen(PHP_EXTRA_VERSION);
            $verNum   = trim(substr(PHP_VERSION, 0, ($phpLen-$extraLen)));
            $reqVer   = trim($module->getInfo('min_php') . ""); //make sure it's a string and then trim it

            $success  = true;
            if ($verNum >= $reqVer) {
                $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }

            return $success;
        }
    */
    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum  = PHP_VERSION;
        $reqVer  = $module->getInfo('min_php');
        if ((false !== $reqVer) && ('' !== $reqVer)) {
            if (version_compare($verNum, (string)$reqVer, '<')) {
                $module->setErrors(sprintf(_AM_XFORMS_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }
}
