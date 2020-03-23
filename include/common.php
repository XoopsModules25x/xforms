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
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 * @link      https://github.com/XoopsModules25x/xforms
 */

use XoopsModules\Xforms\Helper;
use Xmf\Module\Helper\Session;

require dirname(__DIR__) . '/preloads/autoloader.php';

// defined('XOOPS_ROOT_PATH') || exit('Restricted Access');

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName); //$capsDirName

// Instantiate Module Helper
$helper = Helper::getInstance();

if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_DIRNAME', basename(dirname(__DIR__)));
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_IMAGE_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/');
    define($moduleDirNameUpper . '_IMAGE_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/assets/images');
    define($moduleDirNameUpper . '_ADMIN_URL', constant($moduleDirNameUpper . '_URL') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/admin/');
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($moduleDirNameUpper . '_DIRNAME'));
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
}

//define('XFORMS_ROOT_PATH', $helper->path());

$mypathIcon16 = $helper->url('assets/images/icons/16');
$pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32 = Xmf\Module\Admin::iconUrl('', 32);

//$uploadDir = $helper->getConfig('uploaddir');
//$uploadDir = ('/' === substr($uploadDir, -1, 1)) ? $uploadDir : $uploadDir . '/';
//define('XFORMS_UPLOAD_PATH', $uploadDir);

// use Session to reduce disk access while checking directory/file existance
$sessionHelper = new Session();
$uploadChecked = $sessionHelper->get('uploadChecked', false);
if (!$uploadChecked) {
    $prevUploadPath = $sessionHelper->get('uploadPath', '');
    $currUploadPath = base64_encode(XFORMS_UPLOAD_PATH);
    if ($prevUploadPath !== $currUploadPath) {
        $sessionHelper->set('uploadPath', $currUploadPath);
        //create Upload directory, if it does not exist
        if (!is_dir(XFORMS_UPLOAD_PATH)) {
            $oldumask = umask(0);
            if (!mkdir($concurrentDirectory = XFORMS_UPLOAD_PATH) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
            umask($oldumask);
        }
        if (is_dir(XFORMS_UPLOAD_PATH) && !is_writable(XFORMS_UPLOAD_PATH)) {
            chmod(XFORMS_UPLOAD_PATH, 0777);
        }
        // make sure there's a index.html file to "prevent" browsing
        $fileInfo = new \SplFileInfo(XFORMS_UPLOAD_PATH . '/index.html');
        if (!$fileInfo->isFile()) {
            // index file doesn't exist so create it
            $fhandle = fopen(XFORMS_UPLOAD_PATH . '/index.html', 'wb');
            if (false !== $fhandle) {
                // write out file
                $string = '<script>history.go(-1);</script>';
                fwrite($fhandle, $string);
                fclose($fhandle);
            }
        }
    }
    $sessionHelper->set('uploadChecked', true);
}
