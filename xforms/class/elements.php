<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################

if( !defined('xforms_ROOT_PATH') ){ exit(); }
class xformsElements extends XoopsObject {
	function xformsElements(){
		$this->XoopsObject();
	//	key, data_type, value, req, max, opt
		$this->initVar("ele_id", XOBJ_DTYPE_INT, NULL, false);
		$this->initVar("form_id", XOBJ_DTYPE_INT);
		$this->initVar("ele_type", XOBJ_DTYPE_TXTBOX, NULL, true, 10);
		$this->initVar("ele_caption", XOBJ_DTYPE_TXTBOX, '', false, 255);
		$this->initVar("ele_order", XOBJ_DTYPE_INT, 0);
		$this->initVar("ele_req", XOBJ_DTYPE_INT);
		$this->initVar("ele_value", XOBJ_DTYPE_ARRAY, '');
		$this->initVar("ele_display", XOBJ_DTYPE_INT);
	}
}

class xformsElementsHandler extends XoopsPersistableObjectHandler{
	var $db;
	var $db_table;
	var $obj_class = 'xformsElements';

	function __construct($db) {
		$this->db = $db;
		$this->db_table = $this->db->prefix('xforms_formelements');
	}
	function &getInstance($db) {
		static $instance;
		if (!isset($instance)) {
			$instance = new xformsElementsHandler($db);
		}
		return $instance;
	}
	function &create() {
		$ret = new $this->obj_class();
		return $ret;
	}

	function &get($id){
		$id = intval($id);
		if ($id > 0) {
			$sql = 'SELECT * FROM '.$this->db_table.' WHERE ele_id='.$id;
			if (!$result = $this->db->query($sql)) {
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$element = new $this->obj_class();
				$element->assignVars($this->db->fetchArray($result));
				return $element;
			}
		}
		return false;
	}

	function insert(&$element, $force = false){
        if( strtolower(get_class($element)) != strtolower($this->obj_class) ){
            return false;
        }
        if( !$element->isDirty() ){
            return true;
        }
        if( !$element->cleanVars() ){
            return false;
        }
		foreach( $element->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		if( $element->isNew() || empty($ele_id) ){
			$ele_id = $this->db->genId($this->db_table."_ele_id_seq");
			$sql = sprintf("INSERT INTO %s (
				ele_id, form_id, ele_type, ele_caption, ele_order, ele_req, ele_value, ele_display
				) VALUES (
				%u, %u, %s, %s, %u, %u, %s, %u
				)",
				$this->db_table,
				$ele_id,
				$form_id,
				$this->db->quoteString($ele_type),
				$this->db->quoteString($ele_caption),
				$ele_order,
				$ele_req,
				$this->db->quoteString($ele_value),
				$ele_display
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				form_id = %u,
				ele_type = %s,
				ele_caption = %s,
				ele_order = %u,
				ele_req = %u,
				ele_value = %s,
				ele_display = %u
				WHERE ele_id = %u",
				$this->db_table,
				$form_id,
				$this->db->quoteString($ele_type),
				$this->db->quoteString($ele_caption),
				$ele_order,
				$ele_req,
				$this->db->quoteString($ele_value),
				$ele_display,
				$ele_id
			);
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$element->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($ele_id) ){
			$ele_id = $this->db->getInsertId();
		}
        $element->assignVar('ele_id', $ele_id);
		return $ele_id;
	}
	
	function delete(&$element, $force = false){
		if( strtolower(get_class($element)) != strtolower($this->obj_class) ) {
			return false;
		}
		$sql = "DELETE FROM ".$this->db_table." WHERE ele_id=".$element->getVar("ele_id")."";
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		return true;
	}

	function &getObjects($criteria = null, $id_as_key = false){
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db_table;
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
			$elements = new $this->obj_class();
			$elements->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] =& $elements;
			}else{
				$ret[$myrow['ele_id']] =& $elements;
			}
			unset($elements);
		}
		if(count($ret) > 0){return $ret;}else{return false;}
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
	
	function insertDefaults($form_id){
		global $xoopsModuleConfig;
		include xforms_ROOT_PATH.'admin/default_elements.php';
		if( count($defaults) > 0 ){
			$error = '';
			foreach( $defaults as $d ){
				$ele = $this->create();
				$ele->setVar('form_id', $form_id);
				$ele->setVar('ele_caption', $d['caption']);
				$ele->setVar('ele_req', $d['req']);
				$ele->setVar('ele_order', $d['order']);
				$ele->setVar('ele_display', $d['display']);
				$ele->setVar('ele_type', $d['type']);
				$ele->setVar('ele_value', $d['value']);
				if( !$this->insert($ele) ){
					$error .= $ele->getHtmlErrors();
				}
			}
			if( !empty($error) ){
				return $error;
			}
		}
		return false;
	}
	
}
?>