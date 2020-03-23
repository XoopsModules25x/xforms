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
 * Module: Xforms
 *
 * @package   \XoopsModules\Xforms\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class \XoopsModules\Xforms\EfElement
 */
class EfElement extends \XoopsObject
{
    /**
     * \XoopsModules\Xforms\EfElement contructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ele_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('ele_type', XOBJ_DTYPE_TXTBOX, 'text', true, 10);
        $this->initVar('ele_caption', XOBJ_DTYPE_TXTAREA);
        $this->initVar('ele_order', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_req', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_display_row', XOBJ_DTYPE_INT);
        $this->initVar('ele_value', XOBJ_DTYPE_ARRAY, []);
        $this->initVar('ele_display', XOBJ_DTYPE_INT, 1);
    }
}
