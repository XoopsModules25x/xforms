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

if (!interface_exists('XformsConstants')) {
    require_once __DIR__ . '/constants.php';
    //    xoops_load('constants', 'xforms');
}

/**
 * Class XformsElement
 */
class XformsElement extends XoopsObject
{

    /**
     * XformsElement class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('ele_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('form_id', XOBJ_DTYPE_INT, 0, true);
        // changed ele_type to default to text in v2.00 ALPHA 2
        $this->initVar('ele_type', XOBJ_DTYPE_TXTBOX, 'text', true, 10);
        $this->initVar('ele_caption', XOBJ_DTYPE_TXTAREA, '');
        $this->initVar('ele_order', XOBJ_DTYPE_INT, 0);
        $this->initVar('ele_req', XOBJ_DTYPE_INT, XformsConstants::ELEMENT_NOT_REQD);
        $this->initVar('ele_display_row', XOBJ_DTYPE_INT, XformsConstants::DISPLAY_SINGLE_ROW);
        // changed ele_value to OTHER from ARRAY
        $this->initVar('ele_value', XOBJ_DTYPE_OTHER, '');
        $this->initVar('ele_display', XOBJ_DTYPE_INT, XformsConstants::ELEMENT_DISPLAY);
    }

    /**
     *
     * {@inheritDoc}
     * @see XoopsObject::getVar()
     */
    public function getVar($key, $format = 's')
    {
        $myVar = parent::getVar($key, $format);
        //        if (('ele_value' == $key) && (in_array($this->vars['ele_type']['value'], array('checkbox', 'select', 'country', 'radio', 'yn')))) {
        //        if (!empty($myVar) && !is_array($myVar)) {
        if (('ele_value' === $key) && !empty($myVar) && !is_array($myVar)) {
            $myVar = unserialize($myVar);
            //            }
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
     *
     * {@inheritDoc}
     * @see XoopsObject::getVars()
     */
    public function &getVars()
    {
        foreach (array_keys($this->vars) as $key) {
            $this->getVar($key);
        }

        return $this->vars;
    }

    /**
     *
     * {@inheritDoc}
     * @see XoopsObject::setVar()
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
     *
     * {@inheritDoc}
     * @see XoopsObject::assignVar()
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

/**
 * Class XformsElementHandler
 *
 * @param XoopsDatabase $db the database object
 */
class XformsElementHandler extends XoopsPersistableObjectHandler
{
    /** {@internal HTML password (obfuscated) is stored in dB in 'plain text' as
     * there's no way to see/use them otherwise - thus making this pretty useless
     * for passwords}}
     */
    protected $_validElementTypes = array(
        'checkbox'   => _AM_XFORMS_ELE_CHECKBOX,
        'color'      => _AM_XFORMS_ELE_COLOR,
        'country'    => _AM_XFORMS_ELE_COUNTRY,
        'date'       => _AM_XFORMS_ELE_DATE,
        'email'      => _AM_XFORMS_ELE_EMAIL,
        'html'       => _AM_XFORMS_ELE_HTML,
        'number'     => _AM_XFORMS_ELE_NUMBER,
        'obfuscated' => _AM_XFORMS_ELE_OBFUSCATED,
        'pattern'    => _AM_XFORMS_ELE_PATTERN,
        'radio'      => _AM_XFORMS_ELE_RADIO,
        'range'      => _AM_XFORMS_ELE_RANGE,
        'select'     => _AM_XFORMS_ELE_SELECT,
        'text'       => _AM_XFORMS_ELE_TEXT,
        'textarea'   => _AM_XFORMS_ELE_TEXTAREA,
        'time'       => _AM_XFORMS_ELE_TIME,
        'url'        => _AM_XFORMS_ELE_URL,
        'upload'     => _AM_XFORMS_ELE_UPLOAD,
        'uploadimg'  => _AM_XFORMS_ELE_UPLOADIMG,
        'yn'         => _AM_XFORMS_ELE_YN
    );
    //    public $db;
    //    public $db_table;
    //    public $obj_class = 'XformsElement';

    /**
     * Element class constructor
     *
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db = null)
    {
        //        $this->db       = $db;
        //        $this->db_table = $this->db->prefix('xforms_element');
        parent::__construct($db, 'xforms_element', 'XformsElement', 'ele_id', 'ele_type');
        natcasesort($this->_validElementTypes); // put items in a logical order for display
    }

    /**
     *
     * @return array list of valid elements (type => title)
     */
    public function getValidElements()
    {
        return $this->_validElementTypes;
    }

    /**
     * @param $form_id
     *
     * @return bool|string (false = completed successfully, else failed)
     */
    public function insertDefaults($form_id)
    {
        include XFORMS_ROOT_PATH . 'admin/default_elements.php';
        if (count($defaults) > 0) {
            $error = '';
            foreach ($defaults as $d) {
                $ele = $this->create();
                $ele->setVars(array(
                                  'form_id'         => $form_id,
                                  'ele_caption'     => $d['caption'],
                                  'ele_req'         => $d['req'],
                                  'ele_display_row' => $d['ele_display_row'],
                                  'ele_order'       => $d['order'],
                                  'ele_display'     => $d['display'],
                                  'ele_type'        => $d['type'],
                                  'ele_value'       => $d['value']
                              ));
                if (!$this->insert($ele)) {
                    $error .= $ele->getHtmlErrors();
                }
            }
            if (!empty($error)) {
                return $error;
            }
        }

        return false;
    }
}
