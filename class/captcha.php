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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           2.00
 *
 */
use Xmf\Module\Helper;

xoops_load('xoopscaptcha');

/**
 * Class to manipulate captcha
 *
 */
class XformsCaptcha extends XoopsCaptcha
{
    protected $dirname;

    /**
     * class constructor
     *
     * @uses \Xmf\Module\Helper
     */
    public function __construct()
    {
        parent::__construct();
        // overwrite config setting for name
        $this->name           = strtolower(get_called_class());
        $this->config['name'] = $this->name;
        $this->dirname        = basename(dirname(__DIR__));

        // get this module's Preferences for captcha
        $xformsHelper        = Helper::getHelper($this->dirname);
        $xformsCaptchaConfig = $xformsHelper->getConfig('captcha');
        unset($xformsHelper);

        if (!interface_exists('XformsConstants')) {
            xoops_load('constants', $this->dirname);
        }

        switch ($xformsCaptchaConfig) {
            case XformsConstants::CAPTCHA_INHERIT:
            default:
                //don't need to do anything, will use settings from XOOPS
                break;
            case XformsConstants::CAPTCHA_ANON_ONLY:
                $this->active = (isset($GLOBALS['xoopsUser'])
                                 && ($GLOBALS['xoopsUser'] instanceof XoopsUser)) ? false : true;
                $this->setConfigs(array('skipmember' => true, 'disabled' => false));
                break;
            case XformsConstants::CAPTCHA_EVERYONE:
                $this->active = true;
                $this->setConfigs(array('skipmember' => false, 'disabled' => false));
                break;
            case XformsConstants::CAPTCHA_NONE:
                $this->active = false;
                $this->setConfigs(array('skipmember' => true, 'disabled' => true));
                break;
        }
        $this->loadHandler();
        $this->config = $this->loadConfig($this->config['mode']);
    }

    /**
     * Get Instance
     *
     * Temp patch method because core uses __CLASS__ instead of get_called_class()
     *
     * @return XformsCaptcha
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class    = get_called_class();
            $instance = new $class();
        }

        return $instance;
    }

    /**
     * XformsCaptcha::loadConfig()
     *
     * varies from {@see XoopsCaptcha} in that it keeps the base config located in
     * ./config.php even if there's a config.{$filename}.php file in
     * either the basic path ($this->path_basic) or plugin path ($this->path_plugin)
     *
     * config setting priorities: plugin (highest) -> basic -> core (lowest)
     *
     * @see loadConfig()
     *
     * @param mixed $filename
     *
     * @return array An array of captcha configs
     */
    public function loadConfig($filename = null)
    {
        $coreCfg   = array();
        $basicCfg  = array();
        $pluginCfg = array();

        if (file_exists($file = $this->path_basic . '/config.php')) {
            $coreCfg = include $file;
        }
        $filename = (isset($filename) && ('' !== trim($filename))) ? $filename : false;
        if (false === $filename) {
            if (file_exists($file = $this->path_basic . '/config.' . $filename . '.php')) {
                $basicCfg = include $file;
            }
            if (file_exists($file = $this->path_plugin . '/config.' . $filename . '.php')) {
                $pluginCfg = include $file;
            }
        }
        $fileConfigs = array_merge($coreCfg, $basicCfg, $pluginCfg);
        $config      = array();
        foreach ($fileConfigs as $key => $val) {
            $config[$key] = $val;
        }

        return $config;
    }
}
