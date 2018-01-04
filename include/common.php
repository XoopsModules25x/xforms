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

use Xmf\Module\Helper;
use Xmf\Module\Helper\Session;

// defined('XOOPS_ROOT_PATH') || exit('Restricted Access');

define('XFORMS_DIRNAME', basename(dirname(__DIR__)));
define('XFORMS_ROOT_PATH', $GLOBALS['xoops']->path('modules/' . XFORMS_DIRNAME));
define('XFORMS_URL', $GLOBALS['xoops']->url('modules/' . XFORMS_DIRNAME));
define('XFORMS_IMAGES_URL', XFORMS_URL . '/assets/images');

$helper = Helper::getHelper(XFORMS_DIRNAME);
$uploadDir    = $helper->getConfig('uploaddir');
$uploadDir    = ('/' === substr($uploadDir, -1, 1)) ? $uploadDir : $uploadDir . '/';

define('XFORMS_UPLOAD_PATH', $uploadDir);

//require_once $helper->path('include/functions.php');

if (!interface_exists('XformsConstants')) {
    require_once $helper->path('class/constants.php');
    //    xoops_load('constants', XFORMS_DIRNAME);
}

//This is needed or it will not work in blocks.
/**@todo - which blocks? - there are no blocks in xforms that need this. The
 * "offending" block(s) need to be fixed so they DO work without a global here
 */
global $xforms_isAdmin;
$xforms_isAdmin     = $helper->isUserAdmin();
$xformsFormsHandler = $helper->getHandler('forms');
/*
// Load only if module is installed
if (is_object($xforms->getModule())) {
    // Find if the user is admin of the module
    $xforms_isAdmin = xformsUserIsAdmin();
}
$xformsFormsHandler = xoops_getModuleHandler('forms', XFORMS_DIRNAME);
*/
// use session to reduce disk access while checking directory/file existance
$sessionHelper = new Xmf\Module\Helper\Session();
$uploadChecked = $sessionHelper->get('uploadChecked', false);
if (!$uploadChecked) {
    $prevUploadPath = $sessionHelper->get('uploadPath', '');
    $currUploadPath = base64_encode(XFORMS_UPLOAD_PATH);
    if ($prevUploadPath != $currUploadPath) {
        $sessionHelper->set('uploadPath', $currUploadPath);
        //set Upload directory, if it does not exist
        if (!is_dir(XFORMS_UPLOAD_PATH)) {
            $oldumask = umask(0);
            mkdir(XFORMS_UPLOAD_PATH);
            umask($oldumask);
        }
        if (is_dir(XFORMS_UPLOAD_PATH) && !is_writable(XFORMS_UPLOAD_PATH)) {
            chmod(XFORMS_UPLOAD_PATH, 0777);
        }
    }
    // make sure there's a index.html file to "prevent" browsing
    $fileInfo = new SplFileInfo(XFORMS_UPLOAD_PATH . 'index.html');
    if (!$fileInfo->isFile()) {
        // index file doesn't exist so create it
        $fhandle = fopen(XFORMS_UPLOAD_PATH . 'index.html', 'w');
        if (false !== $fhandle) {
            // write out file
            $string = '<script>history.go(-1);</script>';
            fwrite($fhandle, $string);
            fclose($fhandle);
        }
    }
    $sessionHelper->set('uploadChecked', true);
}
