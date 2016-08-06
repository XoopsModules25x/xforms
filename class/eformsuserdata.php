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

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XformsEformsuserdata
 *
 * @see XoopsObject
 */
class XformsEformsuserdata extends XoopsObject
{
    /**
     * XformsEformsuserdata constructor.
     */
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

/**
 * Class xFormsEformsuserdataHandler
 *
 * @see XoopsPersistableObjectHandler
 */
class XformsEformsuserdataHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $obj_class = 'XformsEformsuserdata';

    /**
     * @param $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('eforms_userdata');
        parent::__construct($db, 'eforms_userdata', 'XformsEformsuserdata', 'udata_id');
    }
}
