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
 * @package   \XoopsModules\Xforms\admin\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */
defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class \XoopsModules\Xforms\Element
 */
class Element extends \XoopsObject
{
    /**
     * \XoopsModules\Xforms\Element class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ele_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('form_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('ele_type', XOBJ_DTYPE_TXTBOX, 'text', true, 10);
        $this->initVar('ele_caption', XOBJ_DTYPE_TXTAREA, '');
        $this->initVar('ele_order', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_req', XOBJ_DTYPE_INT, Constants::ELEMENT_NOT_REQD);
        $this->initVar('ele_display_row', XOBJ_DTYPE_INT, Constants::DISPLAY_SINGLE_ROW);
        $this->initVar('ele_value', XOBJ_DTYPE_OTHER, '');
        $this->initVar('ele_display', XOBJ_DTYPE_INT, Constants::ELEMENT_DISPLAY);
    }

    /**
     * {@inheritDoc}
     * @see \XoopsObject::getVar()
     */
    public function getVar($key, $format = 's')
    {
        $myVar = parent::getVar($key, $format);
        if (('ele_value' === $key) && !empty($myVar) && !is_array($myVar)) {
            $myVar = unserialize($myVar);
            if (!empty($myVar) && is_array($myVar)) {
                $keys  = array_keys($myVar);
                $vals  = array_values($myVar);
                $keys  = array_map('base64_decode', $keys);
                $myVar = array_combine($keys, $vals);
            } else {
                $myVar = '';
            }
        }

        return $myVar;
    }

    /**
     * {@inheritDoc}
     * @see \XoopsObject::getVars()
     */
    public function &getVars()
    {
        foreach (array_keys($this->vars) as $key) {
            $this->getVar($key);
        }

        return $this->vars;
    }

    /**
     * {@inheritDoc}
     * @see \XoopsObject::setVar()
     */
    public function setVar($key, $val, $not_gpc = false)
    {
        if (('ele_value' === $key) && is_array($val)) {
            $keys = array_keys($val);
            $vals = array_values($val);
            $keys = array_map('base64_encode', $keys);
            $val  = array_combine($keys, $vals);
            $val  = serialize($val);
        }
        parent::setVar($key, $val, $not_gpc);
    }

    /**
     * {@inheritDoc}
     * @see \XoopsObject::assignVar()
     */
    public function assignVar($key, $val)
    {
        if (('ele_value' === $key) && is_array($val)) {
            $keys = array_keys($val);
            $vals = array_values($val);
            $keys = array_map('base64_encode', $keys);
            $val  = array_combine($keys, $vals);
            $val  = serialize($val);
        }
        $this->vars[$key]['value'] = $val;
    }
}
