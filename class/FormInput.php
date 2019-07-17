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
 * @author          Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author          zyspec <owners@zyspec.com>
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since           2.00
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * HTML5 Input class for forms
 *
 *  Supports most HTML5 input types
 *
 * @author Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author zyspec <owners@zyspec.com>
 *
 * {@internal this class is a modification of the combination of the
 * \Xoops\HTML\Abstract, \Xoops\Form\Element and the \Xoops\Form\Input
 * classes from XOOPS 2.6}}
 */
class FormInput extends \XoopsFormElement
{
    /**
     * Attributes for this element
     *
     * @var array
     * @access protected
     */
    protected $attributes = [];
    /**
     * Javascript performing additional validation of this element data
     *
     * This property contains a list of Javascript snippets that will be sent to
     * renderValidationJS().
     * NB: All elements are added to the output one after the other, so don't forget
     * to add a ";" after each to ensure no Javascript syntax error is generated.
     *
     * @var array ()
     * @access public
     */
    //    public $customValidationCode = array();

    /**
     * caption of the element
     *
     * @var string
     * @access private
     */
    private $caption = '';

    /**
     * pattern_description for this element
     *
     * @var string
     * @access private
     */
    private $pattern_description;

    /**
     * datalist for this element
     *
     * @var array
     * @access private
     */
    private $datalist;

    /**
     * extra attributes to go in the tag
     *
     * @var array
     * @access private
     */
    private $extra = [];

    /**
     * description of the field
     *
     * @var string
     * @access private
     */
    private $description = '';

    /**
     * value of the field
     *
     * Default  var type is string but extending classes can overide the type
     * Example: protected $value = array();
     *
     * @var string|array
     * @access protected
     */
    protected $value = '';

    /**
     * maximum columns for a field
     *
     * @var int
     * @access private
     */
    private $maxcols = 6;

    /**
     * __construct
     *
     * @param string $caption     Caption
     * @param string $name        name attribute
     * @param int    $size        Size
     * @param int    $maxlength   Maximum length of text
     * @param string $value       Initial text
     * @param string $placeholder placeholder for this element
     * @param string $type        HTML <input> type defaults to 'text'
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '', $placeholder = '', $type = 'text')
    {
        $this->setType($type);
        $this->setCaption($caption);
        $this->setAttribute('name', $name);
        $this->setSize((int)$size);
        $this->setAttribute('maxlength', (int)$maxlength);
        $this->setValue($value);
        if (!empty($placeholder)) {
            $this->setAttribute('placeholder', $placeholder);
        }
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return (int)$this->getAttribute('size');
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    public function getMaxlength()
    {
        return (int)$this->getAttribute('maxlength');
    }

    /**
     * Get placeholder for this element
     *
     * @return string
     */
    public function getPlaceholder()
    {
        return (string)$this->getAttribute('placeholder');
    }

    /**
     * Get type information value
     *
     * @return string
     */
    public function getType()
    {
        return (string)$this->getAttribute('type');
    }

    /**
     * Get HTML types supported
     *
     * @return array of supported HTML <input> types
     */
    public function getHtmlTypes()
    {
        return [
            'color'    => [],
            'date'     => ['min', 'max'],
            //                  'datetime' => array(),
            //            'datetime-local' => array(),
            'email'    => [],
            //                     'month' => array(),
            'number'   => ['min', 'max', 'step'],
            'password' => ['autocomplete'],
            'range'    => ['min', 'max', 'step'],
            //                    'search' => array(),
            //                       'tel' => array(),
            'text'     => [],
            'time'     => [],
            'url'      => [],
            //                      'week' => array()
        ];
    }

    /**
     * @param int $size
     */
    private function setSize($size)
    {
        if (null !== $size) {
            $validAttribute = $this->isAttributeValid('size');
            if ($validAttribute) {
                $this->setAttribute('size', (int)$size);
            } else {
                $this->setExtra('style="width: ' . (int)$size . 'em;"');
            }
        }
    }

    /**
     * Set Type text value
     *
     * @param string|array $value is string, set type; value is array then keys are ('type', 'min', 'max')
     */
    public function setType($value)
    {
        if (isset($value)) {
            $htmlTypes = $this->getHtmlTypes();
            if (is_array($value)) {
                if (isset($value['type'])) {
                    $valueType = mb_strtolower(trim($value['type']));
                    if (isset($valueType) && isset($htmlTypes[$valueType])) {
                        $this->setAttribute('type', $valueType); // set the HTML <input> type
                        foreach ($htmlTypes[$valueType] as $typeAttrib) {
                            if (isset($value[$typeAttrib])) {
                                $this->setAttribute($typeAttrib, $value[$typeAttrib]);
                            }
                        }
                    } else {
                        $this->setAttribute('type', 'text');
                    }
                } else {
                    $this->setAttribute('type', 'text');
                }
            } else {
                $value = isset($htmlTypes[mb_strtolower($value)]) ? mb_strtolower($value) : 'text';
                $this->setAttribute('type', $value);
            }
        } else {
            $this->setAttribute('type', 'text');
        }
    }

    /**
     * Set an attribute
     *
     * @param string $name  name of the attribute
     * @param mixed  $value value for the attribute
     */
    public function setAttribute($name, $value = null)
    {
        // convert boolean to strings, so getAttribute can return boolean
        // false for attributes that are not defined
        $value                                                 = (false === $value) ? '0' : $value;
        $value                                                 = (true === $value) ? '1' : $value;
        $this->attributes[htmlspecialchars($name, ENT_QUOTES)] = $value;
    }

    /**
     * Unset an attribute
     *
     * @param string $name name of the attribute
     */
    public function unsetAttribute($name)
    {
        unset($this->attributes[htmlspecialchars($name, ENT_QUOTES)]);
    }

    /**
     * Set attributes as specified in an array
     *
     * @param array $values an array of name => value pairs of attributes to set
     */
    public function setAttributes($values)
    {
        if (!empty($values)) {
            foreach ($values as $name => $value) {
                $this->setAttribute($name, $value);
            }
        }
    }

    /**
     * get an attribute value
     *
     * @param string $name name of the attribute
     * @param bool   $encode
     *
     * @return string|false value of attribute or false if not a valid attribute
     */
    public function getAttribute($name, $encode = true)
    {
        $value = false;
        $name  = htmlspecialchars($name, ENT_QUOTES);
        if (isset($this->attributes[$name])) {
            $value = (bool)$encode ? htmlspecialchars($this->attributes[$name], ENT_QUOTES) : $this->attributes[$name];
        }

        return $value;
    }

    /**
     * is the attribute set?
     *
     * @param string $name name of the attribute
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        $name = htmlspecialchars($name, ENT_QUOTES);

        return array_key_exists($name, $this->attributes);
    }

    /**
     * add an element attribute value to a multi-value attribute (like class)
     *
     * @param string       $name  name of the attribute
     * @param string|array $value value for the attribute
     */
    public function addAttribute($name, $value)
    {
        if (is_scalar($value)) {
            $value = explode(' ', (string)$value);
        }
        $name = htmlspecialchars($name, ENT_QUOTES);
        if (false === $this->hasAttribute($name)) {
            $this->attributes[$name] = [];
        }
        foreach ($value as $v) {
            if (!in_array($v, $this->attributes[$name])) {
                $this->attributes[$name][] = $v;
            }
        }
    }

    /**
     * get invalid attributes for this element type
     * @param string $type
     *
     * @return array
     */
    public function getInvalidAttributes($type = null)
    {
        $htmlTypes = $this->getHtmlTypes();
        if (null === $type) {
            $type = $this->getType();
        }
        $invalid = [
            'color'    => ['max', 'maxlength', 'min', 'size', 'step'],
            'date'     => ['maxlength', 'placeholder', 'size'],
            //                      'datetime' => array('maxlength', 'placeholder', 'size'),
            //                'datetime-local' => array('maxlength', 'placeholder', 'size'),
            'email'    => ['max', 'min', 'step'],
            //                         'month' => array('maxlength', 'placeholder', 'size'),
            /* 07/2016 - allow 'size' in number for now
             * some browsers don't support number
             * and will render as a text box
             */
            'number'   => ['maxlength'],
            'password' => ['list', 'max', 'min', 'step'],
            'range'    => ['maxlength', 'placeholder', 'size'],
            //                        'search' => array('max', 'min', 'step'),
            //                           'tel' => array('max', 'min', 'step'),
            'text'     => ['max', 'min', 'step'],
            'time'     => ['maxlength', 'placeholder', 'size'],
            'url'      => ['max', 'min', 'step'],
            //                          'week' => array('maxlength', 'size')
        ];
        $ret     = [];
        if (isset($htmlTypes[$type]) && isset($invalid[$type])) {
            $ret = (array)$invalid[$type];
        }

        return $ret;
    }

    /**
     * check to see if attribute is valid for this type
     *
     * @param string $attr attribute to check
     *
     * @return bool
     */
    public function isAttributeValid($attr)
    {
        $type              = $this->getType();
        $invalidAttributes = $this->getInvalidAttributes();

        return in_array($attr, $invalidAttributes);
    }

    /**
     * render attributes as a string to include in HTML output
     *
     * @return string
     */
    public function renderAttributeString()
    {
        // title attribute needs to be generated if not already set
        if (!$this->hasAttribute('title')) {
            $this->setAttribute('title', $this->getTitle());
        }
        // generate id from name if not already set
        if (!$this->hasAttribute('id')) {
            $id = $this->getAttribute('name');
            if ('[]' === mb_substr($id, -2)) {
                $id = mb_substr($id, 0, -2);
            }
            $this->setAttribute('id', $id);
        }
        $type        = $this->getType();
        $invalidAttr = $this->getInvalidAttributes($type);
        $rendered    = '';
        foreach ($this->attributes as $name => $value) {
            if ('name' === $name
                && $this->hasAttribute('multiple')
                && '[]' !== mb_substr($value, -2)) {
                $value .= '[]';
            }
            // check to see if this attribute is valid for this type
            if (!in_array($name, $invalidAttr)) {
                if (is_array($value)) {
                    // arrays can be used for class attributes, space separated
                    $set = '="' . htmlspecialchars(implode(' ', $value), ENT_QUOTES) . '"';
                } elseif (null === $value) {
                    // null indicates name only, like autofocus or readonly
                    $set = '';
                } else {
                    $set = '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
                }
                $rendered .= $name . $set . ' ';
            }
        }

        return $rendered;
    }

    /**
     * getValue - Get an array of pre-selected values
     *
     * @param bool $encode True to encode special characters
     *
     * @return mixed
     */
    public function getValue($encode = false)
    {
        if (is_array($this->value)) {
            $ret = [];
            foreach ($this->value as $value) {
                $ret[] = $encode ? htmlspecialchars($value, ENT_QUOTES) : $value;
            }

            return $ret;
        }

        return $encode ? htmlspecialchars($this->value, ENT_QUOTES) : $this->value;
    }

    /**
     * setValue - Set pre-selected values
     *
     * @param mixed $value value to assign to this element
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            $this->value = (array)$this->value;
            foreach ($value as $v) {
                $this->value[] = $v;
            }
        } elseif (is_array($this->value)) {
            $this->value[] = $value;
        } else {
            $this->value = $value;
        }
    }

    /**
     * setName - set the "name" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    /**
     * getName - get the "name" attribute for the element
     *
     * @param bool $encode
     * @return string
     */
    public function getName($encode = true)
    {
        return (string)$this->getAttribute('name', (bool)$encode);
    }

    /**
     * setAccessKey - set the accesskey attribute for the element
     *
     * @param string $key "accesskey" attribute for the element
     */
    public function setAccessKey($key)
    {
        $this->setAttribute('accesskey', $key);
    }

    /**
     * getAccessKey - get the "accesskey" attribute for the element
     *
     * @return string "accesskey" attribute value
     */
    public function getAccessKey()
    {
        return (string)$this->getAttribute('accesskey');
    }

    /**
     * getAccessString - If the accesskey is found in the specified string, underlines it
     *
     * @param string $str string to add accesskey highlight to
     *
     * @return string Enhanced string with the 1st occurence of accesskey underlined
     */
    public function getAccessString($str)
    {
        $access = $this->getAccessKey();
        if (!empty($access) && (false !== ($pos = mb_strpos($str, $access)))) {
            return htmlspecialchars(mb_substr($str, 0, $pos), ENT_QUOTES) . '<span style="text-decoration: underline;">' . htmlspecialchars(mb_substr($str, $pos, 1), ENT_QUOTES) . '</span>' . htmlspecialchars(mb_substr($str, $pos + 1), ENT_QUOTES);
        }

        return htmlspecialchars($str, ENT_QUOTES);
    }

    /**
     * setClass - set the "class" attribute for the element
     *
     * @param string $class "class" attribute for the element
     */
    public function setClass($class)
    {
        $this->addAttribute('class', (string)$class);
    }

    /**
     * getClass - get the "class" attribute for the element
     *
     * @return string "class" attribute value
     */
    public function getClass()
    {
        $class = $this->getAttribute('class');
        if (false === $class) {
            return false;
        }

        return htmlspecialchars(implode(' ', $class), ENT_QUOTES);
    }

    /**
     * setPattern - set the "pattern" attribute for the element
     *
     * @param string $pattern             pattern attribute for the element
     * @param string $pattern_description pattern description
     */
    public function setPattern($pattern, $pattern_description = '')
    {
        $this->setAttribute('pattern', $pattern);
        $this->pattern_description = trim($pattern_description);
    }

    /**
     * getPattern - get the "pattern" attribute for the element
     *
     * @return string "pattern"
     */
    public function getPattern()
    {
        return (string)$this->getAttribute('pattern');
    }

    /**
     * getPatternDescription - get the "pattern_description"
     *
     * @return string "pattern_description"
     */
    public function getPatternDescription()
    {
        if (empty($this->pattern_description)) {
            return '';
        }

        return $this->pattern_description;
    }

    /**
     * setDatalist - set the datalist attribute for the element
     *
     * @param string|array $datalist datalist attribute for the element
     */
    public function setDatalist($datalist)
    {
        if (is_array($datalist)) {
            if (!empty($datalist)) {
                $this->datalist = $datalist;
            }
        } else {
            $this->datalist[] = trim($datalist);
        }
    }

    /**
     * getDatalist - get the datalist attribute for the element
     *
     * @return string "datalist" attribute value
     */
    public function getDatalist()
    {
        if (empty($this->datalist)) {
            return '';
        }
        $ret = NWLINE . '<datalist id="list_' . $this->getName() . '">' . NWLINE;
        foreach ($this->datalist as $datalist) {
            $ret .= '<option value="' . htmlspecialchars($datalist, ENT_QUOTES) . '">' . NWLINE;
        }
        $ret .= '</datalist>' . NWLINE;

        return $ret;
    }

    /**
     * isDatalist - is there a datalist for the element?
     *
     * @return bool true if element has a non-empty datalist
     */
    public function isDatalist()
    {
        return !empty($this->datalist) ? true : false;
    }

    /**
     * setCaption - set the caption for the element
     *
     * @param string $caption caption for element
     */
    public function setCaption($caption)
    {
        $this->caption = trim($caption);
    }

    /**
     * getCaption - get the caption for the element
     *
     * @param bool $encode
     * @return string
     */
    public function getCaption($encode = true)
    {
        return (bool)$encode ? htmlspecialchars($this->caption, ENT_QUOTES) : $this->caption;
    }

    /**
     * setTitle - set the title for the element
     *
     * @param string $title title for element
     */
    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
    }

    /**
     * getTitle - get the title for the element
     *
     * @param bool $encode
     * @return string
     */
    public function getTitle($encode = true)
    {
        if ($this->hasAttribute('title')) {
            return $this->getAttribute('title', (bool)$encode);
        }
        if ('' !== $this->pattern_description) {
            return htmlspecialchars(strip_tags($this->caption . ' - ' . $this->pattern_description), ENT_QUOTES);
        }

        return htmlspecialchars(strip_tags($this->caption), ENT_QUOTES);
    }

    /**
     * setDescription - set the element's description
     *
     * @param string $description description
     */
    public function setDescription($description)
    {
        $this->description = trim($description);
    }

    /**
     * getDescription - get the element's description
     *
     * @param bool $encode True to encode special characters
     *
     * @return string
     */
    public function getDescription($encode = false)
    {
        return $encode ? htmlspecialchars($this->description, ENT_QUOTES) : $this->description;
    }

    /**
     * setHidden - flag the element as "hidden"
     */
    public function setHidden()
    {
        $this->setAttribute('hidden');
    }

    /**
     * isHidden - is this a hidden element?
     *
     * @return bool true if hidden
     */
    public function isHidden()
    {
        return $this->hasAttribute('hidden');
    }

    /**
     * setRequired - set entry required
     *
     * @param bool $bool true to set required entry for this element
     */
    public function setRequired($bool = true)
    {
        if ($bool) {
            $this->setAttribute('required');
        }
    }

    /**
     * isRequired - is entry required for this element?
     *
     * @return bool true if entry is required
     */
    public function isRequired()
    {
        return $this->hasAttribute('required');
    }

    /**
     * setExtra - Add extra attributes to the element.
     *
     * This string will be inserted verbatim and unvalidated in the
     * element's tag. Know what you are doing!
     *
     * @param string $extra    extra raw text to insert into form
     * @param bool   $replace  If true, passed string will replace current
     *                         content, otherwise it will be appended to it
     *
     * @return string[] New content of the extra string
     */
    public function setExtra($extra, $replace = false)
    {
        if ($replace) {
            $this->extra = [trim($extra)];
        } else {
            $this->extra[] = trim($extra);
        }

        return $this->extra;
    }

    /**
     * getExtra - Get the extra attributes for the element
     *
     * @param bool $encode True to encode special characters
     *
     * @return string
     */
    public function getExtra($encode = false)
    {
        if (!$encode) {
            return implode(' ', $this->extra);
        }
        $value = [];
        foreach ($this->extra as $val) {
            $value[] = str_replace('>', '&gt;', str_replace('<', '&lt;', $val));
        }

        return empty($value) ? '' : ' ' . implode(' ', $value);
    }

    /**
     * renderValidationJS - Render custom javascript validation code
     *
     * @return string|false
     */
    public function renderValidationJS()
    {
        // render custom validation code if any
        if (!empty($this->customValidationCode)) {
            return implode(NWLINE, $this->customValidationCode);
            // generate validation code if required
        }
        if ($this->isRequired() && $eltname = $this->getName()) {
            // $eltname    = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption) ? sprintf(\XoopsLocale::F_ENTER, $eltname) : sprintf(\XoopsLocale::F_ENTER, $eltcaption);
            $eltmsg     = str_replace([':', '?', '%'], '', $eltmsg);
            $eltmsg     = str_replace('"', '\"', stripslashes($eltmsg));
            $eltmsg     = strip_tags($eltmsg);

            return NWLINE . "if ( myform.{$eltname}.value == \"\" ) { window.alert(\"{$eltmsg}\");" . " myform.{$eltname}.focus(); return false; }\n";
        }

        return false;
    }

    /**
     * getMaxcols - get the maximum columns for a field
     *
     * @return int
     */
    public function getMaxcols()
    {
        return $this->maxcols;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        if ($this->getSize() > $this->getMaxcols()) {
            $maxcols = $this->getMaxcols();
        } else {
            $maxcols = $this->getSize();
        }
        $this->addAttribute('class', 'span' . $maxcols);
        $dlist = $this->isDatalist();
        if (!empty($dlist)) {
            $this->addAttribute('list', 'list_' . $this->getName());
        }

        return '<input ' . $this->renderAttributeString() . 'value="' . $this->getValue() . '" ' . $this->getExtra() . ' >';
    }
}
