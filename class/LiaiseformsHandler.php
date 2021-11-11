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

use XoopsDatabase;
use XoopsPersistableObjectHandler;



/**
 * Module: xForms
 *
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @copyright       Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           2.00
 */
\defined('XFORMS_ROOT_PATH') || exit('Restricted access');

//if (!interface_exists('Xforms\Constants')) {
//    require_once __DIR__ . '/constants.php';
//    //    xoops_load('constants', 'xforms');
//}

/**
 * Class LiaiseformsHandler
 */
class LiaiseformsHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'liaise_form_access';
    public $obj_class = Liaiseforms::class;

    /**
     * LiaiseformsHandler constructor.
     * @param \XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('liaise_forms');
        parent::__construct($db, 'liaise_forms', Liaiseforms::class, 'form_id', 'form_title');
    }
}
