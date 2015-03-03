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

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Interface XformsConstants
 */
interface XformsConstants
{
    /**#@+
     * Constant definition
     */

    const DISALLOW = 0;

// CONFIG displayicons
    const _XFORMS_DISPLAYICONS_ICON = 1;
    const _XFORMS_DISPLAYICONS_TEXT = 2;
    const _XFORMS_DISPLAYICONS_NO = 3;

// CONFIG submissions
    const _XFORMS_SUBMISSIONS_NONE = 1;
    const _XFORMS_SUBMISSIONS_DOWNLOAD = 2;
    const _XFORMS_SUBMISSIONS_MIRROR = 3;
    const _XFORMS_SUBMISSIONS_BOTH = 4;

// CONFIG anonpost
    const _XFORMS_ANONPOST_NONE = 1;
    const _XFORMS_ANONPOST_DOWNLOAD = 2;
    const _XFORMS_ANONPOST_MIRROR = 3;
    const _XFORMS_ANONPOST_BOTH = 4;

// CONFIG autoapprove
    const _XFORMS_AUTOAPPROVE_NONE = 1;
    const _XFORMS_AUTOAPPROVE_DOWNLOAD = 2;
    const _XFORMS_AUTOAPPROVE_MIRROR = 3;
    const _XFORMS_AUTOAPPROVE_BOTH = 4;

    /**#@-*/
}

/*

// CONFIG displayicons
define("_XFORMS_DISPLAYICONS_ICON", 1);
define("_XFORMS_DISPLAYICONS_TEXT", 2);
define("_XFORMS_DISPLAYICONS_NO", 3);

// CONFIG submissions
define("_XFORMS_SUBMISSIONS_NONE", 1);
define("_XFORMS_SUBMISSIONS_DOWNLOAD", 2);
define("_XFORMS_SUBMISSIONS_MIRROR", 3);
define("_XFORMS_SUBMISSIONS_BOTH", 4);

// CONFIG anonpost
define("_XFORMS_ANONPOST_NONE", 1);
define("_XFORMS_ANONPOST_DOWNLOAD", 2);
define("_XFORMS_ANONPOST_MIRROR", 3);
define("_XFORMS_ANONPOST_BOTH", 4);

// CONFIG autoapprove
define("_XFORMS_AUTOAPPROVE_NONE", 1);
define("_XFORMS_AUTOAPPROVE_DOWNLOAD", 2);
define("_XFORMS_AUTOAPPROVE_MIRROR", 3);
define("_XFORMS_AUTOAPPROVE_BOTH", 4);
*/
