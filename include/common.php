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

use XoopsModules\Xforms;
use XoopsModules\Xforms\Common;

include  dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName = basename(dirname(__DIR__));
$moduleDirNameUpper   = strtoupper($moduleDirName); //$capsDirName


/** @var \XoopsDatabase $db */
/** @var Xforms\Helper $helper */
/** @var Xforms\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = Xforms\Helper::getInstance();
$utility = new Xforms\Utility();
//$configurator = new Xforms\Common\Configurator();

$helper->loadLanguage('common');

//handlers
//$categoryHandler     = new Xforms\CategoryHandler($db);
//$downloadHandler     = new Xforms\DownloadHandler($db);

if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_DIRNAME', basename(dirname(__DIR__)));
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_IMAGE_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/');
    define($moduleDirNameUpper . '_IMAGE_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/assets/images');
    define($moduleDirNameUpper . '_ADMIN_URL', constant($moduleDirNameUpper . '_URL') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
}


$uploadDir    = $helper->getConfig('uploaddir');
$uploadDir    = ('/' === substr($uploadDir, -1, 1)) ? $uploadDir : $uploadDir . '/';

//require_once $helper->path('include/functions.php');

if (!interface_exists('Xforms\Constants')) {
    require_once $helper->path('class/constants.php');
    //    xoops_load('constants', XFORMS_DIRNAME);
}


$pathIcon16    = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
//$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$icons = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='" . 0 . "' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='" . 1 . "' align='middle'>",
];

$debug = false;

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);
// Local icons path
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');

    $GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
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
    $fileInfo = new \SplFileInfo(XFORMS_UPLOAD_PATH . 'index.html');
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
