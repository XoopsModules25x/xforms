<?php

namespace XoopsModules\Xforms;

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
 * @package   \XoopsModules\Xforms\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
use \XoopsModules\Xforms;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!class_exists('\XoopsMediaUploader')) {
    xoops_load('xoopsmediauploader');
}

/**
 * Class \XoopsModules\Xforms\MediaUploader
 *
 * @see \XoopsMediaUploader
*/
class MediaUploader extends \XoopsMediaUploader
{
    /**
     * Don't check size for admin (default: true)
     */
    protected $noadmin_sizecheck = true;

    /**
     * Constructor
     *
     * @param string $uploadDir
     * @param int    $maxFileSize
     * @param array  $allowedExtensions
     * @param array  $allowedMimeTypes
     * @param int    $maxWidth
     * @param int    $maxHeight
     * @param bool   $randomFilename
     */
    public function __construct($uploadDir = null, $maxFileSize = 0, $allowedExtensions = null, $allowedMimeTypes = null, $maxWidth = null, $maxHeight = null, $randomFilename = false)
    {
        parent::__construct ($uploadDir, $allowedMimeTypes, $maxFileSize, $maxWidth, $maxHeight, $randomFilename);
        if (!empty($allowedExtensions)) {
            $this->allowedExtensions = $allowedExtensions;
        } else {
            $mimeArray = include $GLOBALS['xoops']->path('include/mimetypes.inc.php');
            $this->allowedExtensions = array_keys($mimeArray);
        }
    }

    /**
     * set value to determine if should check size if admin
     *
     * @param bool $value
     *
     * @return void
     */
    public function setNoAdminSizeCheck($value)
    {
        $this->noadmin_sizecheck = (bool)$value;
    }

    /**
     * Is the file the right size?
     *
     * @deprecated v2.00 ALPHA 2
     * @return bool
     */
    public function checkMaxFileSize()
    {
        if ($this->noadmin_sizecheck) {
            return true;
        }
        return parent::checkMaxFileSize;
    }
    /**
     * Is the file the extension type allowed
     *
     * @deprecated v2.00 ALPHA 2
     * @return bool
     **/
    public function checkExtension()
    {
        $ext = substr(strrchr($this->mediaName, '.'), 1);
        $retVal = false;
        if (!empty($this->allowedExtensions) && in_array(strtolower($ext), $this->allowedExtensions)) {
            $this->ext = $ext;
            $retVal    = true;
        }
        return $retVal;
    }
}
