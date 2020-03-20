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
 * @package   XoopsModules\Xforms\admin\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link http://xoops.org XOOPS Project}
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class \XoopsModules\Xforms\EfElementHandler
 *
 */
class EfElementHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db, 'eforms_element', EfElement::class, 'ele_id', 'ele_type');
    }
}
