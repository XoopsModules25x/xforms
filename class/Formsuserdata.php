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
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @copyright       Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           1.30
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class Formsuserdata
 *
 * @see XoopsObject
 */
class Formsuserdata extends \XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('udata_id', XOBJ_DTYPE_INT);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('ele_id', XOBJ_DTYPE_INT);
        $this->initVar('udata_time', XOBJ_DTYPE_INT);
        $this->initVar('udata_ip', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('udata_agent', XOBJ_DTYPE_TXTBOX, '', true, 500);
        $this->initVar('udata_value', XOBJ_DTYPE_ARRAY, '');
    }
}
