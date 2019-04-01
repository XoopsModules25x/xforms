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
 * Class xFormsUserdata
 */
class xFormsUserdata extends XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar("udata_id", XOBJ_DTYPE_INT);
        $this->initVar("uid", XOBJ_DTYPE_INT);
        $this->initVar("form_id", XOBJ_DTYPE_INT);
        $this->initVar("ele_id", XOBJ_DTYPE_INT);
        $this->initVar("udata_time", XOBJ_DTYPE_INT);
        $this->initVar("udata_ip", XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar("udata_agent", XOBJ_DTYPE_TXTBOX, '', true, 500);
        $this->initVar("udata_value", XOBJ_DTYPE_ARRAY, '');
    }
}

/**
 * Class xFormsUserdataHandler
 */
class xFormsUserdataHandler
{
    public $db;
    public $db_table;
    public $obj_class = 'xFormsUserdata';

    /**
     * @param $db
     */
    public function xFormsUserdataHandler($db)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_userdata');
    }

    /**
     * @param $db
     *
     * @return xFormsUserdataHandler
     */
    public function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new xFormsUserdataHandler($db);
        }

        return $instance;
    }

    public function create()
    {
        $ret = new $this->obj_class();

        return $ret;
    }

    /**
     * @param $uDataId
     *
     * @return bool
     */
    public function get($uDataId)
    {
        $uDataId = (int)$uDataId;
        if ($uDataId > 0) {
            $sql = 'SELECT * FROM ' . $this->db_table . ' WHERE udata_id=' . $uDataId;
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $userdata = new $this->obj_class();
                $userdata->assignVars($this->db->fetchArray($result));

                return $userdata;
            }
        }

        return false;
    }

    /**
     * @param      $userdata
     * @param bool $force
     *
     * @return bool
     */
    public function insert($userdata, $force = false)
    {
        if (strtolower(get_class($userdata)) != strtolower($this->obj_class)) {
            return false;
        }
        if (!$userdata->isDirty()) {
            return true;
        }
        if (!$userdata->cleanVars()) {
            return false;
        }
        foreach ($userdata->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($userdata->isNew() || empty($uDataId)) {
            $sql = sprintf(
                "INSERT INTO %s (uid, form_id, ele_id, udata_time, udata_ip, udata_agent, udata_value) VALUES (%u, %u, %u, %u, %s, %s, %s)",
                $this->db_table,
                $uid,
                $formId,
                $eleId,
                $udata_time,
                $this->db->quoteString($udata_ip),
                $this->db->quoteString($udata_agent),
                $this->db->quoteString($uDataValue)
            );
        } else {
            $sql = sprintf("UPDATE %s SET udata_value = %s WHERE $uDataId = %u", $this->db_table, $this->db->quoteString($uDataValue), $uDataId);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            $userdata->setErrors("Could not store data in the database.<br />" . $this->db->error() . ' (' . $this->db->errno() . ')<br />' . $sql);

            return false;
        }
        if (empty($uDataId)) {
            $uDataId = $this->db->getInsertId();
        }
        $userdata->assignVar('udata_id', $uDataId);

        return $uDataId;
    }

    /**
     * @param      $userdata
     * @param bool $force
     *
     * @return bool
     */
    public function delete($userdata, $force = false)
    {
        if (strtolower(get_class($userdata)) != strtolower($this->obj_class)) {
            return false;
        }
        $sql = "DELETE FROM " . $this->db_table . " WHERE udata_id=" . $userdata->getVar("udata_id");
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        return true;
    }

    /**
     * @param null $criteria
     *
     * @return array|bool
     */
    public function &getObjects($criteria = null)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db_table;
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
            $userdatas = new $this->obj_class();
            $userdatas->assignVars($myrow);
            $ret[] = $userdatas;
            unset($userdatas);
        }
        if (count($ret) > 0) {
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * @param $formId
     *
     * @return array
     */
    public function getReport($formId)
    {
        $ret     = array();
        $formId = (int)$formId;
        if ($formId <= 0) {
            return $ret;
        }
        $sql
                = 'SELECT D.uid, D.form_id, D.ele_id, D.udata_time, D.udata_ip, D.udata_value
                     , U.name, U.uname
                     , E.ele_type, E.ele_caption
                  FROM ' . $this->db_table . ' D
                  LEFT JOIN ' . $this->db->prefix('users') . ' U ON (D.uid=U.uid)
                 INNER JOIN ' . $this->db->prefix('xforms_element') . ' E ON (D.ele_id=E.ele_id)
                 WHERE D.form_id=' . $formId . '
                 ORDER BY D.uid ASC, D.udata_time ASC, D.udata_ip ASC, E.ele_order ASC';
        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] = $myrow;
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
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }
}
