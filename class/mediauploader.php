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
 * @copyright       {@see http://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             http://xoops.org XOOPS
 * @since           1.30
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!class_exists('XoopsMediaUploader')) {
    xoops_load('xoopsmediauploader');
}

/**
 * Class XformsMediaUploader
 *
 * @see XoopsMediaUploader
 */
class XformsMediaUploader extends XoopsMediaUploader
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

    public function __construct(
        $uploadDir = null,
        $maxFileSize = 0,
        $allowedExtensions = null,
        $allowedMimeTypes = null,
        $maxWidth = null,
        $maxHeight = null,
        $randomFilename = false
    ) {
        parent::__construct($uploadDir, $allowedMimeTypes, $maxFileSize, $maxWidth, $maxHeight, $randomFilename);
        if (!empty($allowedExtensions)) {
            $this->allowedExtensions = $allowedExtensions;
        } else {
            $mimeArray               = include $GLOBALS['xoops']->path('include/mimetypes.inc.php');
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
     * @return bool
     **/
    public function checkExtension()
    {
        $ext = substr(strrchr($this->mediaName, '.'), 1);
        if (!empty($this->allowedExtensions) && !in_array(strtolower($ext), $this->allowedExtensions)) {
            return false;
        } else {
            $this->ext = $ext;

            return true;
        }
    }
}
