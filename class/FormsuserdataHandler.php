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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */
defined('XFORMS_ROOT_PATH') || die('Restricted access');

/**
 * Class FormsuserdataHandler
 *
 * @see XoopsPersistableObjectHandler
 */
class FormsuserdataHandler extends \XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $obj_class = 'Formsuserdata';

    /**
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('eforms_userdata');
        parent::__construct($db, 'eforms_userdata', 'Formsuserdata', 'udata_id');
    }
}