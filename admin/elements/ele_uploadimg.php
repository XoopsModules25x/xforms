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
 * @copyright Copyright (c) 2001-2019 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 */

use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\FormInput;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Uploadimg element
 *
 * value [0] = input size
 *       [1] = mime file extensions
 *       [2] = mime types
 *       [3] = save to (mail or directory)
 *       [4] = image width
 *       [5] = image height
 */
$mimeArray = include_once $GLOBALS['xoops']->path('www/include/mimetypes.inc.php');
$imgArray  = [];
foreach ($mimeArray as $ext => $type) {
    //get image MIME types as defined by IANA (images/ with no /x-**** which are not official)
    if (preg_match('/^image\/[^x-]/i', $type)) {
        $imgArray[$ext] = $type;
    }
}

$mimeTypes    = implode('|', $imgArray);
$mimeTypesJS  = implode('\|', $imgArray);
$mimeExtArray = array_keys($imgArray);
$mimeExt      = implode('|', $mimeExtArray);
$mimeExtJS    = implode('\|', $mimeExtArray);

$size = !empty($value[0]) ? (int)$value[0] : 0;
//$ext         = empty($eleId) ? 'jpg|jpeg|gif|png|tif|tiff' : $value[1];
//$mime        = empty($eleId) ? 'image/jpeg|image/pjpeg|image/png|image/x-png|image/gif|image/tiff' : $value[2];
$mimeExtIn   = empty($eleId) ? $mimeExt : $value[1];
$mimeTypesIn = empty($eleId) ? $mimeTypes : $value[2];
$saveAs      = (empty($value[3]) || (Constants::UPLOAD_SAVEAS_FILE !== (int)$value[3])) ? Constants::UPLOAD_SAVEAS_ATTACHMENT : Constants::UPLOAD_SAVEAS_FILE;
$width       = !empty($value[4]) ? (int)$value[4] : 0;
$height      = !empty($value[5]) ? (int)$value[5] : 0;

//$size = new \XoopsFormText(_AM_XFORMS_ELE_UPLOAD_MAXSIZE, 'ele_value[0]', 10, 20, $size);
$size = new FormInput(_AM_XFORMS_ELE_UPLOAD_MAXSIZE, 'ele_value[0]', 10, 20, (string)$size, null, 'number');
$size->setAttribute('min', 0);
$size->setAttribute('step', 512);
$size->setDescription(_AM_XFORMS_ELE_UPLOAD_MAXSIZE_DESC . '<br>' . _AM_XFORMS_ELE_UPLOAD_DESC_SIZE_NOLIMIT);

/*
$ext = new \XoopsFormText(_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT, 'ele_value[1]', 50, 255, $myts->htmlSpecialChars($ext));
$ext->setDescription(_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT_DESC . '<br><br>' . _AM_XFORMS_ELE_UPLOAD_DESC_NOLIMIT);
*/
$extTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT, '<br>');
$extTray->setDescription(_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT_DESC . '<br><br>' . _AM_XFORMS_ELE_UPLOAD_DESC_NOLIMIT);
$ext = new \XoopsFormText('', 'ele_value[1]', 50, 255, $myts->htmlSpecialChars($mimeExtIn));

$setExtButton = new \XoopsFormButton('', 'setext', _ADD . ' ' . _AM_XFORMS_ELE_DEFAULT, 'button');
$setExtButton->setExtra('onclick="document.getElementById(\'ele_value[1]\').value += \'\|' . $mimeExtJS . '\';"');
$extTray->addElement($ext);
$extTray->addElement($setExtButton);

/*
$mime = new \XoopsFormTextArea(_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME, 'ele_value[2]', $myts->htmlSpecialChars($mime), 5, 50);
$mime->setDescription(_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME_DESC . '<br><br>' . _AM_XFORMS_ELE_UPLOAD_DESC_NOLIMIT);
*/
$mimeTray = new \XoopsFormElementTray(_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME, '<br>');
$mimeTray->setDescription(_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME_DESC . '<br><br>' . _AM_XFORMS_ELE_UPLOAD_DESC_NOLIMIT);
$mime          = new \XoopsFormTextArea('', 'ele_value[2]', $myts->htmlSpecialChars($mimeTypesIn), 5, 50);
$setMimeButton = new \XoopsFormButton('', 'setmime', _ADD . ' ' . _AM_XFORMS_ELE_DEFAULT, 'button');
$setMimeButton->setExtra('onclick="document.getElementById(\'ele_value[2]\').value += \'\|' . $mimeTypesJS . '\';"');
$mimeTray->addElement($mime);
$mimeTray->addElement($setMimeButton);

$saveAs = new \XoopsFormSelect(_AM_XFORMS_ELE_UPLOAD_SAVEAS, 'ele_value[3]', $saveAs);
$saveAs->addOptionArray(
    [
        Constants::UPLOAD_SAVEAS_ATTACHMENT => _AM_XFORMS_ELE_UPLOAD_SAVEAS_MAIL,
        Constants::UPLOAD_SAVEAS_FILE       => _AM_XFORMS_ELE_UPLOAD_SAVEAS_FILE,
    ]
);

//$width = new \XoopsFormText(_AM_XFORMS_ELE_UPLOADIMG_MAXWIDTH, 'ele_value[4]', 10, 20, $width);
$width = new FormInput(
    _AM_XFORMS_ELE_UPLOADIMG_MAXWIDTH, 'ele_value[4]', 10, 20, (string)$width, null, 'number'
);
$width->setAttribute('min', 0);
$width->setDescription(_AM_XFORMS_ELE_UPLOAD_DESC_SIZE_NOLIMIT);

//$height = new \XoopsFormText(_AM_XFORMS_ELE_UPLOADIMG_MAXHEIGHT, 'ele_value[5]', 10, 20, $height);
$height = new FormInput(
    _AM_XFORMS_ELE_UPLOADIMG_MAXHEIGHT, 'ele_value[5]', 10, 20, (string)$height, null, 'number'
);
$height->setAttribute('min', 0);
$height->setDescription(_AM_XFORMS_ELE_UPLOAD_DESC_SIZE_NOLIMIT);

$output->addElement($size, 1);
//$output->addElement($ext);
//$output->addElement($mime);
$output->addElement($extTray);
$output->addElement($mimeTray);
$output->addElement($saveAs, 1);
$output->addElement($width, 1);
$output->addElement($height, 1);
