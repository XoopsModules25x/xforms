<?php

namespace XoopsModules\Xforms;

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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */
if (!defined('XFORMS_ROOT_PATH')) {
    exit();
}

/**
 * Class Elements
 */
class Elements extends \XoopsObject
{
    /**
     * Xforms\Elements constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ele_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('ele_type', XOBJ_DTYPE_TXTBOX, null, true, 10);
        $this->initVar('ele_caption', XOBJ_DTYPE_TXTAREA);
        $this->initVar('ele_order', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_req', XOBJ_DTYPE_INT);
        $this->initVar('ele_display_row', XOBJ_DTYPE_INT);
        $this->initVar('ele_value', XOBJ_DTYPE_ARRAY, '');
        $this->initVar('ele_display', XOBJ_DTYPE_INT);
    }
}
