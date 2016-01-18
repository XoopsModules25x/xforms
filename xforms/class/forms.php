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
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

if (!defined('XFORMS_ROOT_PATH')) {
    exit();
}

/**
 * Class xFormsForms
 */
class xFormsForms extends XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar("form_id", XOBJ_DTYPE_INT);
        $this->initVar("form_save_db", XOBJ_DTYPE_INT, 1, true, 1);
        $this->initVar("form_send_method", XOBJ_DTYPE_TXTBOX, 'e', true, 1);
        $this->initVar("form_send_to_group", XOBJ_DTYPE_INT);
        $this->initVar("form_send_to_other", XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar("form_send_copy", XOBJ_DTYPE_INT);
        $this->initVar("form_order", XOBJ_DTYPE_INT, 1, false, 3);
        $this->initVar("form_delimiter", XOBJ_DTYPE_TXTBOX, 's', true, 1);
        $this->initVar("form_title", XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar("form_submit_text", XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
        $this->initVar("form_desc", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_intro", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_email_header", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_email_footer", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_email_uheader", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_email_ufooter", XOBJ_DTYPE_TXTAREA);
        $this->initVar("form_whereto", XOBJ_DTYPE_TXTBOX);
        $this->initVar("form_display_style", XOBJ_DTYPE_TXTBOX, 'f', true, 1);
        $this->initVar("form_begin", XOBJ_DTYPE_INT, 0, true);
        $this->initVar("form_end", XOBJ_DTYPE_INT, 0, false);
        $this->initVar("form_active", XOBJ_DTYPE_INT, 1, true);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $now    = time();
        $fbegin = intval($this->getVar('form_begin'), 10);
        $fend   = intval($this->getVar('form_end'), 10);
        if ($this->getVar('form_active') == 0) {
            return false;
        }
        if (($fbegin != 0 && $fbegin > $now) || ($fend != 0 && $fend < $now)) {
            return false;
        }

        return true;
    }
}

/**
 * Class xFormsFormsHandler
 */
class xFormsFormsHandler extends XoopsObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xforms_form_access';
    public $obj_class = 'xFormsForms';

    /**
     * @param $db
     */
    public function xFormsFormsHandler($db)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_form');
    }

    /**
     * @param $db
     *
     * @return xFormsFormsHandler
     */
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xFormsFormsHandler($db);
        }

        return $instance;
    }

    public function create()
    {
        $ret = new $this->obj_class();

        return $ret;
    }

    /**
     * @param int    $id
     * @param string $fields
     *
     * @return bool
     */
    public function get($id, $fields = '*')
    {
        $id = intval($id);
        if ($id > 0) {
            $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table . ' WHERE form_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $form = new $this->obj_class();
                $form->assignVars($this->db->fetchArray($result));

                return $form;
            }
        }

        return false;
    }

    /**
     * @param object $form
     * @param bool   $force
     *
     * @return bool
     */
    public function insert($form, $force = false)
    {
        if (strtolower(get_class($form)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$form->isDirty()) {
            return true;
        }
        if (!$form->cleanVars()) {
            return false;
        }
        foreach ($form->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($form->isNew() || empty($form_id)) {
            $form_id = $this->db->genId($this->db_table . "_form_id_seq");
            $sql     = sprintf(
                "INSERT INTO %s (
                                form_id, form_save_db, form_send_method, form_send_to_group, form_send_to_other, form_send_copy, form_order, form_delimiter, form_title, form_submit_text, form_desc, form_intro, form_email_header, form_email_footer, form_email_uheader, form_email_ufooter, form_whereto, form_display_style, form_begin, form_end, form_active
                                ) VALUES (
                                %u, %u, %s, %s, %s, %u, %u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %u, %u, %u
                                )",
                $this->db_table,
                intval($form_id, 10),
                intval($form_save_db, 10),
                $this->db->quoteString($form_send_method),
                $this->db->quoteString($form_send_to_group),
                $this->db->quoteString($form_send_to_other),
                intval($form_send_copy, 10),
                intval($form_order, 10),
                $this->db->quoteString($form_delimiter),
                $this->db->quoteString($form_title),
                $this->db->quoteString($form_submit_text),
                $this->db->quoteString($form_desc),
                $this->db->quoteString($form_intro),
                $this->db->quoteString($form_email_header),
                $this->db->quoteString($form_email_footer),
                $this->db->quoteString($form_email_uheader),
                $this->db->quoteString($form_email_ufooter),
                $this->db->quoteString($form_whereto),
                $this->db->quoteString($form_display_style),
                intval($form_begin, 10),
                intval($form_end, 10),
                intval($form_active, 10)
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET
                                form_save_db = %u,
                                form_send_method = %s,
                                form_send_to_group = %s,
                                form_send_to_other = %s,
                                form_send_copy = %u,
                                form_order = %u,
                                form_delimiter = %s,
                                form_title = %s,
                                form_submit_text = %s,
                                form_desc = %s,
                                form_intro = %s,
                                form_email_header = %s,
                                form_email_footer = %s,
                                form_email_uheader = %s,
                                form_email_ufooter = %s,
                                form_whereto = %s,
                                form_display_style = %s,
                                form_begin = %u,
                                form_end = %u,
                                form_active = %u
                                WHERE form_id = %u",
                $this->db_table,
                $form_save_db,
                $this->db->quoteString($form_send_method),
                $this->db->quoteString($form_send_to_group),
                $this->db->quoteString($form_send_to_other),
                intval($form_send_copy, 10),
                $form_order,
                $this->db->quoteString($form_delimiter),
                $this->db->quoteString($form_title),
                $this->db->quoteString($form_submit_text),
                $this->db->quoteString($form_desc),
                $this->db->quoteString($form_intro),
                $this->db->quoteString($form_email_header),
                $this->db->quoteString($form_email_footer),
                $this->db->quoteString($form_email_uheader),
                $this->db->quoteString($form_email_ufooter),
                $this->db->quoteString($form_whereto),
                $this->db->quoteString($form_display_style),
                intval($form_begin, 10),
                intval($form_end, 10),
                intval($form_active, 10),
                $form_id
            );
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $form->setErrors("Could not store data in the database.<br />" . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);

            return false;
        }
        if (empty($form_id)) {
            $form_id = $this->db->getInsertId();
        }
        $form->assignVar('form_id', $form_id);

        return $form_id;
    }

    /**
     * @param      $form
     * @param bool $force
     *
     * @return bool
     */
    public function inactive($form, $force = false)
    {
        if (strtolower(get_class($form)) != strtolower($this->obj_class)) {
            return false;
        }
        $sql = "UPDATE " . $this->db_table . " SET form_active=0 WHERE form_id=" . $form->getVar("form_id");
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $form->setErrors("Could not update data in the database.<br />" . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);

            return false;
        }

        return true;
    }

    /**
     * @param object $form
     * @param bool   $force
     *
     * @return bool
     */
    public function delete($form, $force = false)
    {
        if (strtolower(get_class($form)) != strtolower($this->obj_class)) {
            return false;
        }
        $sql = "DELETE FROM " . $this->db_table . " WHERE form_id=" . $form->getVar("form_id");
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $form->setErrors("Could not delete data in the database.<br />" . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);

            return false;
        }

        return true;
    }

    /**
     * @param null   $criteria
     * @param string $fields
     * @param bool   $id_as_key
     *
     * @return array|bool
     */
    public function &getObjects($criteria = null, $fields = '*', $id_as_key = false)
    {
        $ret   = false;
        $limit = $start = 0;
        if (strtolower($fields) == "home") {
            $fields = 'form_id, form_title, form_desc, form_begin, form_end, form_active';
        }
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $forms = new $this->obj_class();
            $forms->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $forms;
            } else {
                $ret[$myrow['form_id']] = $forms;
            }
            unset($forms);
        }

        return $ret;
    }

    /**
     * @param null $criteria
     *
     * @return int
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * @param null $criteria
     *
     * @return bool
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db_table;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!($result = $this->db->query($sql))) {
            return false;
        }

        return true;
    }

    /**
     * @param $form_id
     *
     * @return bool
     */
    public function deleteFormPermissions($form_id)
    {
        $GLOBALS['moduleperm_handler']->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), $this->perm_name, $form_id);

        return true;
    }

    /**
     * @param $form_id
     * @param $group_ids
     *
     * @return bool
     */
    public function insertFormPermissions($form_id, $group_ids)
    {
        foreach ($group_ids as $id) {
            $GLOBALS['moduleperm_handler']->addRight($this->perm_name, $form_id, $id, $GLOBALS['xoopsModule']->getVar('mid'));
        }

        return true;
    }

    /**
     * @return array|bool
     */
    public function getPermittedForms()
    {
        global $xoopsUser, $xoopsModule, $moduleperm_handler;
        $groups   = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        $criteria = new CriteriaCompo();
        $now      = time();
        $criteria->add(new Criteria('form_active', 0, '<>'));
        $criteria->add(new Criteria('form_order', 1, '>='));
        $criteria->setSort('form_order');
        $criteria->setOrder('ASC');
        if ($forms = $this->getObjects($criteria, 'home')) {
            $ret = array();
            foreach ($forms as $f) {
                if ($f->isActive()) {
                    if (false != $moduleperm_handler->checkRight($this->perm_name, $f->getVar('form_id'), $groups, $xoopsModule->getVar('mid'))) {
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
     * @param $form_id
     *
     * @return bool
     */
    public function getSingleFormPermission($form_id)
    {
        global $xoopsUser, $xoopsModule, $moduleperm_handler;
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
        if (false != $moduleperm_handler->checkRight($this->perm_name, $form_id, $groups, $xoopsModule->getVar('mid'))) {
            return true;
        }

        return false;
    }
}
