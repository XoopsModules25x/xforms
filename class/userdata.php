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
 * @since           1.30
 */

use Xmf\Module\Helper;

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XformsUserdata
 *
 * @see XoopsObject
 */
class XformsUserdata extends XoopsObject
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('udata_id', XOBJ_DTYPE_INT);
        $this->initVar('uid', XOBJ_DTYPE_INT);
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('ele_id', XOBJ_DTYPE_INT);
        $this->initVar('udata_time', XOBJ_DTYPE_INT);
        $this->initVar('udata_ip', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('udata_agent', XOBJ_DTYPE_TXTBOX, '', true, 500);
        $this->initVar('udata_value', XOBJ_DTYPE_OTHER, '');
    }

    /**
     *
     * {@inheritDoc}
     * @see XoopsObject::getVar()
     */
    public function getVar($key, $format = 's')
    {
        $myVar = parent::getVar($key, $format);
        if (('udata_value' === $key) && !empty($myVar) && is_scalar($myVar)) {
            $myVar = unserialize($myVar);
            if (!empty($myVar) && is_array($myVar)) {
                $keys  = array_keys($myVar);
                $vals  = array_values($myVar);
                $vals  = array_map('base64_decode', $vals);
                $myVar = array_combine($keys, $vals);
            } else {
                $myVar = '';
            }
        }

        return $myVar;
    }
    /*
        public function getVars()
        {
            $theVars = array();
            foreach (array_keys($this->vars) as $key) {
                $theVars[$key] = $this->getVar($key);
            }
            return $theVars;
        }
    */
    /**
     *
     * {@inheritDoc}
     * @see XoopsObject::setVar()
     */
    public function setVar($key, $val, $not_gpc = false)
    {
        if (('udata_value' === $key) && !is_scalar($val)) {
            $keys = array_keys($val);
            $vals = array_values($val);
            $vals = array_map('base64_encode', $vals);
            $val  = array_combine($keys, $vals);
            $val  = serialize($val);
        }
        parent::setVar($key, $val, $not_gpc);
    }
    /*
        public function assignVar($key, $val)
        {
            if (('udata_value' == $key) && !is_scalar($val)) {
                $keys = array_keys($val);
                $vals = array_values($val);
                $vals = array_map('base64_encode', $keys);
                $val  = array_combine($keys, $vals);
                $val = serialize($val);
            }
            parent::assignVar($key, $val);
        }
    */
}

/**
 * Class xFormsUserdataHandler
 *
 * @see XoopsPersistableObjectHandler
 */
class XformsUserdataHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $obj_class = 'XformsUserdata';

    /**
     * @param $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_userdata');
        parent::__construct($db, 'xforms_userdata', 'XformsUserdata', 'udata_id');
    }

    /**
     * @param int $formId
     *
     * @return array
     */
    public function getReport($formId)
    {
        $ret    = [];
        $formId = (int)$formId;
        if ($formId > 0) {
            /**
             * @todo - test refactor using classes
             */
            /* Fields needed:
             * XOOPS User: uid, name, uname
             * UserData: uid, form_id, ele_id, udata_time, udata_ip, udata_value
             * Elements: ele_id, ele_type, ele_caption, ele_order
             *
             * Table relationship:
             *  User (uid) <=> Userdata (uid)
             *  Userdata(ele_id) <=> Elements(ele_id)
             *
             *  Sort order:
             *  uid ASC, udata_time ASC, udata_ip ASC, ele_order ASC"
             *
             *  pseudo code:
             *  - get all info for this form from Userdata table into 'array',
             *    sorted by uid ASC, udata_time ASC, udata_ip ASC
             *  - add User info (name, uname) based on uname to 'array'
             *  - get all elements for this form from elements table sorted by weight
             *  - reorder elements in 'array' by weight
             */
            $uFields  = ['uid', 'form_id', 'ele_id', 'udata_time', 'udata_ip', 'udata_value'];
            $criteria = new CriteriaCompo(new Criteria('form_id', (int)$formId));
            $criteria->setSort('uid ASC, udata_time ASC, udata_ip');
            $criteria->setOrder('ASC');
            $userDataArray = $this->getAll($criteria, $uFields, true, false);

            $ret = [];
            if (!empty($userDataArray)) {
                $uIdArray   = [];
                $eleIdArray = [];
                foreach ($userDataArray as $userDataObj) {
                    $uIdArray[]   = $userDataObj->getVar('uid');    // get all the user IDs
                    $eleIdArray[] = $userDataObj->getVar('ele_id'); // get all the element IDs
                }
                // get user info from dB
                $uIdArray        = array_unique($uIdArray);
                $userHandler     = xoops_getHandler('user');
                $uDataUsersArray = $userHandler->getAll(new Criteria('uid', '(' . implode(',', $uIdArray) . ')', 'IN'), ['uname', 'name'], false);
                if (array_key_exists(0, $uDataUsersArray)) { // means anon voter - getAll should have created new object for Anon user
                    //update anon user name (uname) in array
                    $systemHelper                = Helper::getHelper('system');
                    $uDataUsersArray[0]['uname'] = $systemHelper->getConfig('anonymous');
                }

                // get element info from dB
                $helper     = Helper::getHelper('xforms');
                $xformsEleHandler = $helper->getHandler('element');
                $eleIdArray       = array_unique($eleIdArray);
                $criteria         = new CriteriaCompo();
                $criteria->add(new Criteria('ele_id', '(' . implode(',', $eleIdArray) . ')', 'IN'));
                $criteria->setSort('ele_order');
                $criteria->setOrder('ASC');
                $uDataEleArray = $xformsEleHandler->getAll($criteria, ['ele_type', 'ele_caption', 'ele_order'], false);

                foreach ($userDataArray as $thisDataObj) {
                    $thisEleId         = $thisDataObj->getVar('ele_id');
                    $thisUid           = $thisDataObj->getVar('uid');
                    $thisUdataId       = $thisDataObj->getVar('udata_id');
                    $ret[$thisUdataId] = [
                        'form_id'     => $formId,
                        'uid'         => $thisUid,
                        'name'        => $uDataUsersArray[$thisUid]['name'],
                        'uname'       => $uDataUsersArray[$thisUid]['uname'],
                        'udata_time'  => $thisDataObj->getVar('udata_time'),
                        'udata_ip'    => $thisDataObj->getVar('udata_ip'),
                        'udata_value' => $thisDataObj->getVar('udata_value'),
                        'ele_id'      => $thisEleId,
                        'ele_type'    => $uDataEleArray[$thisEleId]['ele_type'],
                        'ele_caption' => $uDataEleArray[$thisEleId]['ele_caption']
                    ];
                }
            }
            ksort($ret);
            $ret = array_values($ret);
            /* Save the following code as a "model" to use for when module is converted to XOOPS 2.6+ **/
            /*
                        $sql = "SELECT D.uid, D.form_id, D.ele_id, D.udata_time, D.udata_ip, D.udata_value
                                , U.name, U.uname
                                , E.ele_type, E.ele_caption
                                FROM {$this->db_table} D
                                LEFT JOIN " . $this->db->prefix('users') . " U ON (D.uid=U.uid)
                                INNER JOIN " . $this->db->prefix('xforms_element') . " E ON (D.ele_id=E.ele_id)
                                WHERE D.form_id={$formId}
                                ORDER BY D.uid ASC, D.udata_time ASC, D.udata_ip ASC, E.ele_order ASC";
                        $result = $this->db->query($sql);
                        if ($result) {
                            while ($myrow = $this->db->fetchArray($result)) {
                                $ret[] = $myrow;
                            }
                        }
            */
        }

        return $ret;
    }
}
