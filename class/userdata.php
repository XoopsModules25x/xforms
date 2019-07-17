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
 * Class Userdata
 *
 * @see XoopsObject
 */
class Userdata extends \XoopsObject
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
        $this->initVar('udata_value', XOBJ_DTYPE_OTHER, '');
    }

    /**
     * {@inheritDoc}
     * @see XoopsObject::getVar()
     */
    public function getVar($key, $format = 's')
    {
        $myVar = parent::getVar($key, $format);
        if (('udata_value' === $key) && !empty($myVar) && is_scalar($myVar)) {
            $myVar = unserialize($myVar);
            if (!empty($myVar) && is_array($myVar)) {
                $keys  = array_keys($myVar);
                $vals  = array_values($myVar);
                $vals  = array_map('base64_decode', $vals);
                $myVar = array_combine($keys, $vals);
            } else {
                $myVar = '';
            }
        }

        return $myVar;
    }

    /*
        public function getVars()
        {
            $theVars = array();
            foreach (array_keys($this->vars) as $key) {
                $theVars[$key] = $this->getVar($key);
            }
            return $theVars;
        }
    */

    /**
     * {@inheritDoc}
     * @see XoopsObject::setVar()
     */
    public function setVar($key, $val, $not_gpc = false)
    {
        if (('udata_value' === $key) && !is_scalar($val)) {
            $keys = array_keys($val);
            $vals = array_values($val);
            $vals = array_map('base64_encode', $vals);
            $val  = array_combine($keys, $vals);
            $val  = serialize($val);
        }
        parent::setVar($key, $val, $not_gpc);
    }

    /*
        public function assignVar($key, $val)
        {
            if (('udata_value' == $key) && !is_scalar($val)) {
                $keys = array_keys($val);
                $vals = array_values($val);
                $vals = array_map('base64_encode', $keys);
                $val  = array_combine($keys, $vals);
                $val = serialize($val);
            }
            parent::assignVar($key, $val);
        }
    */
}
