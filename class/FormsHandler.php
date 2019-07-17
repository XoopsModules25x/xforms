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
 * @copyright       Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License

 * @since           1.30
 */

use Xmf\Module\Helper\Permission;
use XoopsModules\Xforms;

//defined('XFORMS_ROOT_PATH') || exit('Restricted access');

//if (!interface_exists('Xforms\Constants')) {
//    /** @var Xforms\Helper $helper */
//    $helper = Xforms\Helper::getInstance();
//    require_once $helper->path('/class/constants.php');
//}

/**
 * Class FormsHandler
 */
class FormsHandler extends \XoopsPersistableObjectHandler
{
    public    $db;
    public    $db_table;
    public    $perm_name = 'xforms_form_access';
    public    $obj_class = 'Forms';
    protected $dirname;

    /**
     * @param \XoopsDatabase|null $db XoopsDatabase to use for the form
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_form');
        $this->dirname  = basename(dirname(__DIR__));
        parent::__construct($db, 'xforms_form', Forms::class, 'form_id', 'form_title');
    }

    /**
     * Set the form inactive and update it in the database
     * @param obj|Forms $form {$Forms}
     * @param bool      $force
     * @return bool true on success
     */
    public function setInactive(Forms $form, $force = true)
    {
        $ret = true;
        if (Constants::FORM_INACTIVE != $form->getVar('form_active')) {
            $form->setVar('form_active', Constants::FORM_INACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(sprintf(_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno(), $sql));
                $ret = false;
            }
        }

        return $ret ? true : false;
    }

    /**
     * Set the form active and update it in the database
     * @param obj|Forms $form {Forms}
     * @param bool      $force
     * @return bool true on success
     */
    public function setActive(Forms $form, $force = true)
    {
        $ret = true;
        if (Constants::FORM_ACTIVE != $form->getVar('form_active')) {
            $form->setVar('form_active', Constants::FORM_ACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(sprintf(_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno(), $sql));
                $ret = false;
            }
        }

        return $ret ? true : false;
    }

    /**
     * @param int $formId
     *
     * @return bool
     */
    public function deleteFormPermissions($formId)
    {
        $permHelper = new Permission($this->dirname);
        $ret        = $permHelper->deletePermissionForItem($this->perm_name, (int)$formId);

        //        $ret = $GLOBALS['grouppermHandler']->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), $this->perm_name, (int)$formId);
        return $ret;
    }

    /**
     * @param int $formId
     * @param     $groupIds
     * @return bool true if success | false if setting any group perm fails
     * @internal param array $group_ids an array of integer group ids to insert
     */
    public function insertFormPermissions($formId, $groupIds)
    {
        $permHelper = new Permission($this->dirname);

        $groupIds = (array)$groupIds; //make sure it's an array
        $groupIds = array_map('intval', $groupIds); //make sure all array elements are integers
        $ret      = $permHelper->savePermissionForItem($this->perm_name, (int)$formId, $groupIds);

        /*
                $ret = true;
                foreach ($groupIds as $id) {
                    $status = $GLOBALS['grouppermHandler']->addRight($this->perm_name, (int)$formId, $id, $GLOBALS['xoopsModule']->getVar('mid'));
                    $ret = $ret & ($status) ? true : false;
                }
        */

        return $ret;
    }

    /**
     * Get the forms for this user (permissions aware)
     *
     * @return array|bool
     */
    public function getPermittedForms()
    {
        $groups   = (isset($GLOBALS['xoopsUser'])
                     && $GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $criteria = new \CriteriaCompo();
        $now      = time();
        $criteria->add(new \Criteria('form_active', Constants::FORM_INACTIVE, '<>'));
        $criteria->add(new \Criteria('form_order', Constants::FORM_HIDDEN, '>'));
        $criteria->setSort('form_order');
        $criteria->setOrder('ASC');
        if ($forms = $this->getAll($criteria)) {
            $ret = [];
            foreach ($forms as $f) {
                if ($f->isActive()) {
                    $permHelper = new Permission($this->dirname);
                    if ($permHelper->checkPermission($this->perm_name, $f->getVar('form_id'))) {
                        //                    if (false !== $GLOBALS['grouppermHandler']->checkRight($this->perm_name, $f->getVar('form_id'), $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
                        $ret[] = $f;
                    }
                }
                unset($f);
            }

            return $ret;
        }

        return false;
    }

    /**
     * @param $formId
     *
     * @return bool
     */
    public function getSingleFormPermission($formId)
    {
        $permHelper = new Permission($this->dirname);

        return $permHelper->checkPermission($this->perm_name, (int)$formId);
        /*
                $groups = (isset($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
                if (false !== $GLOBALS['grouppermHandler']->checkRight($this->perm_name, (int)$formId, $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
                    return true;
                }
                return false;
        */
    }
}
