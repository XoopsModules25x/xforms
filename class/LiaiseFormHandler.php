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
 * @since     2.00
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class \XoopsModules\Xforms\LiaiseFormHandler
 */
class LiaiseFormHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var \XoopsDatabase
     */
    public $db;
    /**
     * @var string
     */
    public $db_table;
    /**
     * @var string name of permission
     */
    public $perm_name = 'liaise_form_access';

    /**
     * @param $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('liaise_forms');
        parent::__construct($db, 'liaise_forms', LiaiseForm::class, 'form_id', 'form_title');
    }
}
