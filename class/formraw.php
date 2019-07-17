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
 * @author          trabis <trabisdementia@gmail.com>
 * @author          ZySpec <owners@zyspec.com>
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_load('xoopsformelement');

/**
 * Raw - raw form element
 *
 * This class has special treatment by xoopsforms, it will render the raw
 * value provided without wrapping in HTML
 */
class XformsFormRaw extends XoopsFormElement
{
    /**
     * __construct
     *
     * @param string $value value to be rendered
     */
    public function __construct($value = '')
    {
        $this->setValue($value);
    }

    /**
     * Get initial content
     *
     * @param bool $encode to sanitizer the text? Default value should be "true"; however we have to set "false" for backward compatibility
     *
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @param bool $encode to sanitizer the text? Default value should be "true"; however we have to set "false" for backward compatibility
     *
     * @return string HTML
     */
    public function render($encode = false)
    {
        return $this->getValue($encode);
    }
}
