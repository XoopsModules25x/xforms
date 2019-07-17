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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License

 * @since           1.30
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');


/**
 * Class FormsHandler
 */
class XformsHandler extends \XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'eforms_form_access';
    public $obj_class = Xforms::class;

    /**
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('eforms_form');
        parent::__construct($db, 'eforms_form', Xforms::class, 'form_id', 'form_title');
    }
}
