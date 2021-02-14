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

use Xmf\Module\Helper\Permission;

/**
 * Class \XoopsModules\Xforms\FormsHandler
 */
class FormsHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var \XoopsDatabase
     */
    public $db;
    /**
     * @var string name of table in database
     */
    public $db_table;
    /**
     * @var string permission name
     */
    public $perm_name = 'xforms_form_access';
    /**
     * @var string name of the module's root directory
     */
    protected $dirname;

    /**
     * @param \XoopsDatabase|null $db to use for the form
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_form');
        $this->dirname  = \basename(\dirname(__DIR__));
        parent::__construct($db, 'xforms_form', Forms::class, 'form_id', 'form_title');
    }

    /**
     * Set the form inactive and update it in the database
     * @param \XoopsModules\Xforms\Forms $form
     * @param bool                       $force true to force write to database independent of security settings
     *
     * @return bool true on success
     */
    public function setInactive(\XoopsModules\Xforms\Forms $form, $force = true)
    {
        $ret = true;
        if (Constants::FORM_INACTIVE !== (int)$form->getVar('form_active')) {
            $form->setVar('form_active', Constants::FORM_INACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(\sprintf(\_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno()));
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * Set the form active and update it in the database
     * @param \XoopsModules\Xforms\Forms $form
     * @param bool                       $force true to force write to database independent of security settings
     *
     * @return bool true on success
     */
    public function setActive(\XoopsModules\Xforms\Forms $form, $force = true)
    {
        $ret = true;
        if (Constants::FORM_ACTIVE !== (int)$form->getVar('form_active')) {
            $form->setVar('form_active', Constants::FORM_ACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(\sprintf(\_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno()));
                $ret = false;
            }
        }

        return $ret;
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
        //        $ret = $GLOBALS['moduleperm_handler']->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), $this->perm_name, (int)$formId);
        return $ret;
    }

    /**
     * @param int   $formId
     * @param array $groupIds an array of integer group ids to insert
     *
     * @return bool true if success | false if setting any group perm fails
     */
    public function insertFormPermissions($formId, $groupIds)
    {
        $permHelper = new Permission($this->dirname);

        $groupIds = (array)$groupIds; //make sure it's an array
        $groupIds = \array_map('\intval', $groupIds); //make sure all array elements are integers
        $ret      = $permHelper->savePermissionForItem($this->perm_name, (int)$formId, $groupIds);
        /*
                $ret = true;
                foreach ($groupIds as $id) {
                    $status = $GLOBALS['moduleperm_handler']->addRight($this->perm_name, (int)$formId, $id, $GLOBALS['xoopsModule']->getVar('mid'));
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
        //$groups   = (isset($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
        //$now      = time();
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('form_active', Constants::FORM_INACTIVE, '<>'));
        $criteria->add(new \Criteria('form_order', Constants::FORM_HIDDEN, '>'));
        $criteria->setSort('form_order');
        $criteria->order = 'ASC';
        if ($forms = &$this->getAll($criteria)) {
            $ret = [];
            foreach ($forms as $f) {
                if ($f->isActive()) {
                    $permHelper = new Permission($this->dirname);
                    if ($permHelper->checkPermission($this->perm_name, $f->getVar('form_id'))) {
                        //                    if (false !== $GLOBALS['moduleperm_handler']->checkRight($this->perm_name, $f->getVar('form_id'), $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
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
     * @param int $formId
     *
     * @return bool
     */
    public function getSingleFormPermission($formId)
    {
        $permHelper = new Permission($this->dirname);

        return $permHelper->checkPermission($this->perm_name, (int)$formId);
        /*
                $groups = (isset($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
                if (false !== $GLOBALS['moduleperm_handler']->checkRight($this->perm_name, (int)$formId, $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
                    return true;
                }
                return false;
        */
    }
}
