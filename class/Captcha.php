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
 * @package   \XoopsModules\Xforms\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     2.00
 * @link      https://github.com/XoopsModules25x/xforms
 */
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\Helper as Helper;

xoops_load('xoopscaptcha');

/**
 * Class to manipulate captcha
 *
 */
class Captcha extends \XoopsCaptcha
{
    protected $dirname;

    /**
     * class constructor
     *
     * @uses \Xmf\Module\Helper
     */
    protected function __construct()
    {
        parent::__construct();
        // overwrite config setting for name
        $this->name           = strtolower(get_called_class());
        $this->config['name'] = $this->name;
        $this->dirname        = basename(dirname(__DIR__));

        // instantiate module helper
        /* @var \XoopsModules\Xforms\Helper $helper */
        $helper = Helper::getInstance();

        // get this module's Preferences for captcha
        $xformsCaptchaConfig = $helper->getConfig('captcha');
        unset($helper);
/*
        if (!interface_exists('\XoopsModules\Xforms\Constants')) {
            require_once __DIR__ . '/constants.php';
//            xoops_load('constants', $this->dirname);
        }
*/
        switch($xformsCaptchaConfig) {
            case Constants::CAPTCHA_INHERIT:
            default:
                //don't need to do anything, will use settings from XOOPS
                break;
            case Constants::CAPTCHA_ANON_ONLY:
                $this->active = (isset($GLOBALS['xoopsUser']) && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? false: true;
                $this->setConfigs(array('skipmember' => true, 'disabled' => false));
                break;
            case Constants::CAPTCHA_EVERYONE:
                $this->active = true;
                $this->setConfigs(array('skipmember' => false, 'disabled' => false));
                break;
            case Constants::CAPTCHA_NONE:
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
     * in XOOPS < 2.5.9
     *
     * @return Captcha
     */
/*
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class    = get_called_class();
            $instance = new $class();
        }
        return $instance;
    }
*/
    /**
     * \XoopsModules\Xforms\Captcha::loadConfig()
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
        //init config arrays
        $coreCfg = $basicCfg = $pluginCfg = array();

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
         $config = array();
         foreach ($fileConfigs as $key => $val) {
             $config[$key] = $val;
         }
         return $config;
     }
 }
