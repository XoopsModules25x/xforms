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

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

include_once __DIR__ . '/common.php';

/**
 *
 * Module functions
 *
 */

/**
 * Checks if a user is admin of Wfdownloads
 *
 * @return boolean
 */
function xforms_userIsAdmin()
{
    global $xoopsUser;
    $xforms = XformsXforms::getInstance();

    static $xformsIsAdmin;

    if (isset($xformsIsAdmin)) {
        return $xformsIsAdmin;
    }

    if (!$xoopsUser) {
        $xformsIsAdmin = false;
    } else {
        $xformsIsAdmin = $xoopsUser->isAdmin($xforms->getModule()->getVar('mid'));
    }

    return $xformsIsAdmin;
}
