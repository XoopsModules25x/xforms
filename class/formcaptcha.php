<?php namespace XoopsModules\Xforms;

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
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @author          XOOPS Module Development Team
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');

xoops_load('xoopsformelement');
if (!class_exists('XformsCaptcha')) { // hack for XOOPS < 2.6
    xoops_load('captcha', 'xforms');
}
/**
 * Usage of Xforms\FormCaptcha
 *
 * @see XoopsFormCaptcha
 * For form creation:
 * Add form element where proper: <code>$xform->addElement(new Xforms\FormCaptcha($caption, $name, $skipmember, $configs));</code>
 *
 * For verification:
 * <code>
 *               xoops_load('captcha', 'xforms');
 *               $xformsCaptcha = XformsCaptcha::getInstance();
 *               if (!$xformsCaptcha->verify()) {
 *                   echo $xformsCaptcha->getMessage();
 *                   ...
 *               }
 * </code>
 */

/**
 * Xforms Form Captcha
 *
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @author  XOOPS Module Development Team
 * @package xforms
 */
class FormCaptcha extends \XoopsFormElement
{
    public $captchaHandler;

    /**
     * Class construtor
     *
     * @param string  $caption    Caption of the form element, default value is defined in captcha/language/
     * @param string  $name       Name for the input box
     * @param boolean $skipmember Skip CAPTCHA check for members
     * @param array   $configs
     */
    public function __construct($caption = '', $name = 'xformscaptcha', $skipmember = true, $configs = [])
    {
        //        parent::__construct($caption, $name, $skipmember, $configs);
        $this->captchaHandler  = XformsCaptcha::getInstance();
        $configs['name']       = $name;
        $configs['skipmember'] = $skipmember;
        $this->captchaHandler->setConfigs($configs);
        if (!$this->captchaHandler->isActive()) {
            $this->setHidden();
        } else {
            $caption = !empty($caption) ? $caption : $this->captchaHandler->getCaption();
            $this->setCaption($caption);
            $this->setName($name);
        }
    }

    /**
     * @param $name
     * @param $val
     *
     * @return mixed
     */
    public function setConfig($name, $val)
    {
        return $this->captchaHandler->setConfig($name, $val);
    }

    /**
     * @return mixed
     */
    public function render()
    {
        // if (!$this->isHidden()) {
        return $this->captchaHandler->render();
        // }
    }

    /**
     * @return mixed
     */
    public function renderValidationJS()
    {
        return $this->captchaHandler->renderValidationJS();
    }
}
