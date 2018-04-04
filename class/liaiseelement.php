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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 */

use XoopsModules\Xforms\Constants;

defined('XFORMS_ROOT_PATH') || die('Restricted access');

//if (!interface_exists('Xforms\Constants')) {
//    //    xoops_load('constants', 'xforms');
//    require_once __DIR__ . '/constants.php';
//}

/**
 * Class Liaiseelement
 */
class Liaiseelement extends \XoopsObject
{
    /**
     * Liaise Element constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ele_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('form_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('ele_type', XOBJ_DTYPE_TXTBOX, 'text', true, 10);
        $this->initVar('ele_caption', XOBJ_DTYPE_TXTAREA);
        $this->initVar('ele_order', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_req', XOBJ_DTYPE_INT, Constants::ELEMENT_NOT_REQD);
        $this->initVar('ele_value', XOBJ_DTYPE_ARRAY, []);
        $this->initVar('ele_display', XOBJ_DTYPE_INT, Constants::ELEMENT_DISPLAY);
    }
}