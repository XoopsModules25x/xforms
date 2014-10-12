<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################

if( !defined('xforms_ROOT_PATH') ){ exit(); }
class xformsForms extends XoopsObject {
	function xformsForms(){
		$this->XoopsObject();
	//	key, data_type, value, req, max, opt
		$this->initVar("form_id", XOBJ_DTYPE_INT);
		$this->initVar("form_send_method", XOBJ_DTYPE_TXTBOX, 'e', true, 1);
		$this->initVar("form_send_to_group", XOBJ_DTYPE_TXTBOX, 1, false, 3);
		$this->initVar("form_order", XOBJ_DTYPE_INT, 1, false, 3);
		$this->initVar("form_delimiter", XOBJ_DTYPE_TXTBOX, 's', true, 1);
		$this->initVar("form_title", XOBJ_DTYPE_TXTBOX, '', true, 255);
		$this->initVar("form_submit_text", XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
		$this->initVar("form_desc", XOBJ_DTYPE_TXTAREA);
		$this->initVar("form_intro", XOBJ_DTYPE_TXTAREA);
		$this->initVar("form_whereto", XOBJ_DTYPE_TXTBOX);
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 1); //allow html
	}
}

class xformsFormsHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'xforms_form_access';
	var $obj_class = 'xformsForms';

	function xformsFormsHandler(&$db){
		$this->db = $db;
		$this->db_table = $this->db->prefix('xforms_forms');
	}
	function &getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new xformsFormsHandler($db);
		}
		return $instance;
	}
	function &create(){
		$ret = new $this->obj_class();
		return $ret;
	}

	function &get($id, $fields='*'){
		$id = intval($id);
		if( $id > 0 ){
			$sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE form_id='.$id;
			if( !$result = $this->db->query($sql) ){
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if( $numrows == 1 ){
				$form = new $this->obj_class();
				$form->assignVars($this->db->fetchArray($result));
				return $form;
			}
			return false;
		}
		return false;
	}

	function insert(&$form, $force = false){
        if( strtolower(get_class($form)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$form->isDirty() ){
            return true;
        }
        if( !$form->cleanVars() ){
            return false;
        }
		foreach( $form->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		if( $form->isNew() || empty($form_id) ){
			$form_id = $this->db->genId($this->db_table."_form_id_seq");
			$sql = sprintf("INSERT INTO %s (
				form_id, form_send_method, form_send_to_group, form_order, form_delimiter, form_title, form_submit_text, form_desc, form_intro, form_whereto
				) VALUES (
				%u, %s, %s, %u, %s, %s, %s, %s, %s, %s
				)",
				$this->db_table,
				$form_id,
				$this->db->quoteString($form_send_method),
				$this->db->quoteString($form_send_to_group),
				$form_order,
				$this->db->quoteString($form_delimiter),
				$this->db->quoteString($form_title),
				$this->db->quoteString($form_submit_text),
				$this->db->quoteString($form_desc),
				$this->db->quoteString($form_intro),
				$this->db->quoteString($form_whereto)
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				form_send_method = %s,
				form_send_to_group = %s,
				form_order = %u,
				form_delimiter = %s,
				form_title = %s,
				form_submit_text = %s,
				form_desc = %s,
				form_intro = %s,
				form_whereto = %s
				WHERE form_id = %u",
				$this->db_table,
				$this->db->quoteString($form_send_method),
				$this->db->quoteString($form_send_to_group),
				$form_order,
				$this->db->quoteString($form_delimiter),
				$this->db->quoteString($form_title),
				$this->db->quoteString($form_submit_text),
				$this->db->quoteString($form_desc),
				$this->db->quoteString($form_intro),
				$this->db->quoteString($form_whereto),
				$form_id
			);
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$form->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($form_id) ){
			$form_id = $this->db->getInsertId();
		}
        $form->assignVar('form_id', $form_id);
		return $form_id;
	}
	
	function delete(&$form, $force = false){
		if( strtolower(get_class($form)) != strtolower($this->obj_class) ){
			return false;
		}
		$sql = "DELETE FROM ".$this->db_table." WHERE form_id=".$form->getVar("form_id")."";
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		return true;
	}

	function &getObjects($criteria = null, $fields='*', $id_as_key = false){
		$ret = false;
		$limit = $start = 0;
		switch($fields){
			case 'home_list':
				$fields = 'form_id, form_title, form_desc';
			break;
			case 'admin_list':
				$fields = 'form_id, form_title, form_order, form_send_to_group';
			break;
		}
		$sql = 'SELECT '.$fields.' FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
			if( $criteria->getSort() != '' ){
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if( !$result ){
			return false;
		}
		while( $myrow = $this->db->fetchArray($result) ){
			$forms = new $this->obj_class();
			$forms->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] = $forms;
			}else{
				$ret[$myrow['form_id']] = $forms;
			}
			unset($forms);
		}
		return $ret;
	}
	
    function getCount($criteria = null){
		$sql = 'SELECT COUNT(*) FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if( !$result ){
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}
    
    function deleteAll($criteria = null){
		$sql = 'DELETE FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		if( !$result = $this->db->query($sql) ){
			return false;
		}
		return true;
	}
	
	function deleteFormPermissions($form_id){
		$GLOBALS['moduleperm_handler']->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), $this->perm_name, $form_id);
		return true;
	}
	
	function insertFormPermissions($form_id, $group_ids){
		foreach( $group_ids as $id ){
			$GLOBALS['moduleperm_handler']->addRight($this->perm_name, $form_id, $id, $GLOBALS['xoopsModule']->getVar('mid'));
		}
		return true;
	}
	
	function &getPermittedForms(){
		global $xoopsUser, $xoopsModule, $moduleperm_handler;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('form_order', 1, '>='), 'OR');
		$criteria->setSort('form_order');
		$criteria->setOrder('ASC');
		if( $forms = $this->getObjects($criteria, 'home_list') ){
			$ret = array();
			foreach( $forms as $f ){
				if( false != $moduleperm_handler->checkRight($this->perm_name, $f->getVar('form_id'), $groups, $xoopsModule->getVar('mid')) ){
					$ret[] = $f;
					unset($f);
				}
			}
			return $ret;
		}
		return false;
	}
	
	function getSingleFormPermission($form_id){
		global $xoopsUser, $xoopsModule, $moduleperm_handler;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $moduleperm_handler->checkRight($this->perm_name, $form_id, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
?>