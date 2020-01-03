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
 * Class Forms
 */
class Xforms extends \XoopsObject
{
    /**
     * this module's directory
     */
    protected $dirname;

    public function __construct()
    {
        parent::__construct();
        //    key, data_type, value, req, max, opt
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, Constants::SEND_METHOD_MAIL, true, 1);
        $this->initVar('form_send_to_group', XOBJ_DTYPE_TXTBOX, '', false, 3);
        $this->initVar('form_order', XOBJ_DTYPE_INT, 1, false, 3);
        $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, Constants::DELIMITER_SPACE, true, 1);
        $this->initVar('form_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('form_submit_text', XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
        $this->initVar('form_desc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_intro', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_whereto', XOBJ_DTYPE_TXTBOX);

        $this->dirname = basename(dirname(__DIR__));
    }
}
