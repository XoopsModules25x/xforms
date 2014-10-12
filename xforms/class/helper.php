<?php

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/**
 * xoalbum module for xoops
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GPL 2.0 or later
 * @package         xoalbum
 * @since           2.0.0
 * @author          XOOPS Development Team <name@site.com> - <http://xoops.org>
 * @version         $Id: helper.php 12813 2014-10-08 14:58:09Z beckmi $
 */

//defined('XOOPS_ROOT_PATH') or die('Restricted access');

/**
 * Class XformHelper
 */
class XformHelper   /*extends Xform_Module_Helper_Abstract*/
{
    /**
     * Init vars
     * @initialize variables
     */
    private $config;
    private $dirname;
    private $handler;
    private $module;

    /**
     * Constructor
     *
     * @param $dirname
     */
    public function __construct($dirname = '')
    {
        $this->dirname = $dirname;
    }

    /**
     * Get instance
     * @return object
     */
    public function &getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Init config
     * @initialize object
     */
    public function initConfig()
    {
        $modConfigHandler = xoops_gethandler('config');
        $this->config = $modConfigHandler->getConfigsByCat(0, $this->getModule()->getVar('mid'));
    }

    /**
     * Init module
     * @initialize object
     */
    public function initModule()
    {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $this->dirname) {
            $this->module = $xoopsModule;
        } else {
            $module_handler = xoops_gethandler('module');
            $this->module = $module_handler->getByDirname($this->dirname);
        }
    }

    /**
     * Init handler
     *
     * @initialize object
     *
     * @param $name
     */
    public function initHandler($name)
    {
        $this->handler[$name . '_handler'] = xoops_getmodulehandler($name, $this->_dirname);
    }

    /**
     * Get module
     * @return object
     */
    public function &getModule()
    {
        if ($this->module == null) {
            $this->initModule();
        }

        return $this->module;
    }

    /**
     * Get modules
     *
     * @param array $dirnames
     * @param null  $otherCriteria
     * @param bool  $asObj
     *
     * @return array objects
     */
    public function &getModules($dirnames = array(), $otherCriteria = null, $asObj = false)
    {
        // get all dirnames
        $module_handler = xoops_gethandler('module');
        $criteria = new CriteriaCompo();
        if (count($dirnames) > 0) {
            foreach ($dirnames as $mDir) {
                $criteria->add(new Criteria('dirname', $mDir), 'OR');
            }
        }
        if (!empty($otherCriteria)) {
            $criteria->add($otherCriteria);
        }
        $criteria->add(new Criteria('isactive', 1), 'AND');
        $modules = $module_handler->getObjects($criteria, true);
        if($asObj) return $modules;
        $dirs['system-root'] = _YOURHOME;
        foreach ($modules as $module) {
            $dirs[$module->dirname()] = $module->name();
        }

        return $dirs;
    }

    /**
     * Get handler
     *
     * @param $name
     *
     * @return object
     */
    public function &getHandler($name)
    {
        if (!isset($this->handler[$name . '_handler'])) {
            $this->initHandler($name);
        }

        return $this->handler[$name . '_handler'];
    }
}
