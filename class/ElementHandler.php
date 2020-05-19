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
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 */

//use XoopsModules\Xforms;
use XoopsModules\Xforms\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class \XoopsModules\Xforms\ElementHandler
 *
 * @param \XoopsDatabase $db the database object
 */
class ElementHandler extends \XoopsPersistableObjectHandler
{
    /** {@internal HTML password (obfuscated) is stored in dB in 'plain text' as
     * there's no way to see/use them otherwise - thus making this pretty useless
     * for passwords}}
     */
    protected $_validElementTypes = [
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
        'yn'         => _AM_XFORMS_ELE_YN,
    ];

    /**
     * Element class constructor
     *
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        //        $this->db       = $db;
        //        $this->db_table = $this->db->prefix('xforms_element');
        parent::__construct($db, 'xforms_element', Element::class, 'ele_id', 'ele_type');
        natcasesort($this->_validElementTypes); // put items in a logical order for display
    }

    /**
     * Get the valid HTML input types supported by this class
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
        $dFile = Helper::getInstance()->path('admin/default_elements.php');
        require $dFile;
        /** @var array $defaults */
        if (count($defaults) > 0) {
            $error = '';
            foreach ($defaults as $d) {
                $ele = $this->create();
                $ele->setVars(
                    [
                        'form_id'         => (int)$form_id,
                        'ele_caption'     => $d['caption'],
                        'ele_req'         => $d['req'],
                        'ele_display_row' => $d['ele_display_row'],
                        'ele_order'       => $d['order'],
                        'ele_display'     => $d['display'],
                        'ele_type'        => $d['type'],
                        'ele_value'       => $d['value'],
                    ]
                );
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
