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
 * @since           2.00
 */

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!interface_exists('XformsConstants')) {
    require_once __DIR__ . '/constants.php';
    //    xoops_load('constants', 'xforms');
}

/**
 * Class XformsLiaiseForms
 */
class XformsLiaiseforms extends XoopsObject
{
    /**
     * this module's directory
     */
    protected $dirname;

    /**
     * XformsLiaiseforms constructor.
     */
    public function __construct()
    {
        parent::__construct();
        //    key, data_type, value, req, max, opt
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, XformsConstants::SEND_METHOD_MAIL, true, 1);
        $this->initVar('form_send_to_group', XOBJ_DTYPE_TXTBOX, '', false, 3);
        $this->initVar('form_order', XOBJ_DTYPE_INT, 1, false, 3);
        $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, XformsConstants::DELIMITER_SPACE, true, 1);
        $this->initVar('form_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('form_submit_text', XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
        $this->initVar('form_desc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_intro', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_whereto', XOBJ_DTYPE_TXTBOX);

        $this->dirname = basename(dirname(__DIR__));
    }
}

/**
 * Class XformsLiaiseFormsHandler
 */
class XformsLiaiseformsHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'liaise_form_access';
    public $obj_class = 'XformsLiaiseforms';

    /**
     * @param $db
     */
    public function __construct(XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('liaise_forms');
        parent::__construct($db, 'liaise_forms', 'XformsLiaiseforms', 'form_id', 'form_title');
    }
}
