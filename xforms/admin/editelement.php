<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
include("admin_header.php");
$xforms_ele_mgr =& xoops_getmodulehandler('elements');
include_once xforms_ROOT_PATH.'class/elementrenderer.php';
define('_THIS_PAGE', xforms_URL.'admin/editelement.php');
$myts =& MyTextSanitizer::getInstance();
if( $xforms_form_mgr->getCount() < 1 ){
	redirect_header(xforms_ADMIN_URL, 0, _AM_GO_CREATE_FORM);
}

if( count($_POST) > 0 ){
	extract($_POST);
}else{
	extract($_GET);
}

$op = isset($_GET['op']) ? trim($_GET['op']) : '';
$op = isset($_POST['op']) ? trim($_POST['op']) : $op;
$clone = isset($_GET['clone']) ? intval($_GET['clone']) : 0;
$clone = isset($_POST['clone']) ? trim($_POST['clone']) : $clone;
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
$form_id = isset($_POST['form_id']) ? trim($_POST['form_id']) : $form_id;

if( isset($_POST['submit']) && $_POST['submit'] == _AM_ELE_ADD_OPT_SUBMIT && intval($_POST['addopt']) > 0 ){
	$op = 'edit';
}

switch($op){
	case 'edit':
		adminHtmlHeader();
		if( !empty($ele_id) ){
			$element =& $xforms_ele_mgr->get($ele_id);
			$ele_type = $element->getVar('ele_type');
			$output_title = $clone ? _AM_ELE_CREATE : sprintf(_AM_ELE_EDIT, $element->getVar('ele_caption'));
		}else{
			$element =& $xforms_ele_mgr->create();
			$output_title = _AM_ELE_CREATE;
		}
		$output = new XoopsThemeForm($output_title, 'form_ele', _THIS_PAGE);
		if( empty($addopt) ){
			$ele_caption = $clone ? sprintf(_AM_COPIED, $element->getVar('ele_caption', 'f')) : $element->getVar('ele_caption', 'f');
			$text_ele_caption = new XoopsFormText(_AM_ELE_CAPTION, 'ele_caption', 50, 255, $ele_caption);
			$value = $element->getVar('ele_value', 'f');
			$req = $element->getVar('ele_req');
			$display = $element->getVar('ele_display');
			$order = $element->getVar('ele_order');
		}else{
			$ele_caption = $myts->makeTboxData4PreviewInForm($ele_caption);
			$text_ele_caption = new XoopsFormText(_AM_ELE_CAPTION, 'ele_caption', 50, 255, $ele_caption);
			$req = isset($_POST['ele_req']) ? 1 : 0;
			$display = isset($_POST['ele_display']) ? 1 : 0;
			$order = isset($_POST['ele_order']) ? intval($_POST['ele_order']) : 0;
		}
		$output->addElement($text_ele_caption);

		$check_ele_req = new XoopsFormCheckBox(_AM_ELE_REQ, 'ele_req', $req);
		$check_ele_req->addOption(1, ' ');
		$output->addElement($check_ele_req);
		
		$check_ele_display = new XoopsFormCheckBox(_AM_ELE_DISPLAY, 'ele_display', $display);
		$check_ele_display->addOption(1, ' ');
		$output->addElement($check_ele_display);
		
		$text_ele_order = new XoopsFormText(_AM_ELE_ORDER, 'ele_order', 3, 2, $order);
		$output->addElement($text_ele_order);
		
		switch($ele_type){
			case 'text':
			default:
				include 'ele_text.php';
			break;
			case 'textarea':
				include 'ele_tarea.php';
			break;
			case 'select':
				include 'ele_select.php';
			break;
			case 'checkbox':
				include 'ele_check.php';
			break;
			case 'radio':
				include 'ele_radio.php';
			break;
			case 'yn':
				include 'ele_yn.php';
			break;
			case 'html':
				$check_ele_req->setExtra('disabled="disabled"');
				include 'ele_html.php';
			break;
			case 'uploadimg':
				include 'ele_uploadimg.php';
			break;
			case 'upload':
				include 'ele_upload.php';
			break;
		}

		$hidden_op = new XoopsFormHidden('op', 'save');
		$hidden_type = new XoopsFormHidden('ele_type', $ele_type);
		$output->addElement($hidden_op);
		$output->addElement($hidden_type);
		
		if( $clone == true || empty($form_id) ){
			$select_apply_form = new XoopsFormSelect(_AM_ELE_APPLY_TO_FORM, 'form_id', $form_id);
			$forms =& $xforms_form_mgr->getObjects(null, 'form_id, form_title');
			foreach( $forms as $f ){
				$select_apply_form->addOption($f->getVar('form_id'), $f->getVar('form_title'));
			}
			$output->addElement($select_apply_form);
			$hidden_clone = new XoopsFormHidden('clone', 1);
			$output->addElement($hidden_clone);
		}else{
			$hidden_form_id = new XoopsFormHidden('form_id', $form_id);
			$output->addElement($hidden_form_id);
		}
		
		if( !empty($ele_id) && !$clone ){
			$hidden_id = new XoopsFormHidden('ele_id', $ele_id);
			$output->addElement($hidden_id);
		}
		$submit = new XoopsFormButton('', 'submit', _AM_SAVE, 'submit');
		$cancel = new XoopsFormButton('', 'cancel', _CANCEL, 'button');
		$cancel->setExtra('onclick="javascript:history.go(-1);"');
		$tray = new XoopsFormElementTray('');
		$tray->addElement($submit);
		$tray->addElement($cancel);
		$output->addElement($tray);
		$output->display();
	break;
	case 'delete':
		if( empty($ele_id) ){
			redirect_header(xforms_ADMIN_URL, 0, _AM_NOTHING_SELECTED);
		}
		if( empty($_POST['ok']) ){
			adminHtmlHeader();
			xoops_confirm(array('op' => 'delete', 'ele_id' => $ele_id, 'form_id' => $form_id, 'ok' => 1), _THIS_PAGE, _AM_ELE_CONFIRM_DELETE);
		}else{
			$element =& $xforms_ele_mgr->get($ele_id);
			$xforms_ele_mgr->delete($element);
			redirect_header(xforms_URL.'admin/elements.php?form_id='.$form_id, 0, _AM_DBUPDATED);
		}
	break;
	case 'save':
		if( !empty($ele_id) ){
			$element =& $xforms_ele_mgr->get($ele_id);
		}else{
			$element =& $xforms_ele_mgr->create();
		}
		$element->setVar('form_id', $form_id);
		$element->setVar('ele_caption', $ele_caption);
		$req = !empty($ele_req) ? 1 : 0;
		$element->setVar('ele_req', $req);
		$order = empty($ele_order) ? 0 : intval($ele_order);
		$element->setVar('ele_order', $order);
		$display = !empty($ele_display) ? 1 : 0;
		$element->setVar('ele_display', $display);
		$element->setVar('ele_type', $ele_type);
		$value = array();
		switch($ele_type){
			case 'text':
				$value[] = !empty($ele_value[0]) ? intval($ele_value[0]) : $xoopsModuleConfig['t_width'];
				$value[] = !empty($ele_value[1]) ? intval($ele_value[1]) : $xoopsModuleConfig['t_max'];
				$value[] = $ele_value[2];
			break;
			case 'textarea':
			case 'html':
				$value[] = $ele_value[0];
				if( intval($ele_value[1]) != 0 ){
					$value[] = intval($ele_value[1]);
				}else{
					$value[] = $xoopsModuleConfig['ta_rows'];
				}
				if( intval($ele_value[2]) != 0 ){
					$value[] = intval($ele_value[2]);
				}else{
					$value[] = $xoopsModuleConfig['ta_cols'];
				}
			break;
			case 'select':
				$value[0] = $ele_value[0]>1 ? intval($ele_value[0]) : 1;
				$value[1] = !empty($ele_value[1]) ? 1 : 0;
				$v2 = array();
				$multi_flag = 1;
				while( $v = each($ele_value[2]) ){
					if( !empty($v['value']) ){
						if( $value[1] == 1 || $multi_flag ){
							if( $checked[$v['key']] == 1 ){
								$check = 1;
								$multi_flag = 0;
							}else{
								$check = 0;
							}
						}else{
							$check = 0;
						}
						$v2[$v['value']] = $check;
					}
				}
				$value[2] = $v2;
			break;
			case 'checkbox':
				while( $v = each($ele_value) ){
					if( !empty($v['value']) ){
						if( $checked[$v['key']] == 1 ){
							$check = 1;
						}else{
							$check = 0;
						}
						$value[$v['value']] = $check;
					}
				}
			break;
			case 'radio':
				while( $v = each($ele_value) ){
					if( !empty($v['value']) ){
						if( $checked == $v['key'] ){
							$value[$v['value']] = 1;
						}else{
							$value[$v['value']] = 0;
						}
					}
				}
			break;
			case 'yn':
				if( $ele_value == '_NO' ){
					$value = array('_YES'=>0,'_NO'=>1);
				}else{
					$value = array('_YES'=>1,'_NO'=>0);
				}
			break;
			case 'uploadimg':
				$value[] = intval($ele_value[0]);
				$value[] = trim($ele_value[1]);
				$value[] = trim($ele_value[2]);
				$value[] = $ele_value[3] != 1 ? 0 : 1;
				$value[] = intval($ele_value[4]);
				$value[] = intval($ele_value[5]);
			break;
			case 'upload':
				$value[] = intval($ele_value[0]);
				$value[] = trim($ele_value[1]);
				$value[] = trim($ele_value[2]);
				$value[] = $ele_value[3] != 1 ? 0 : 1;
			break;
		}
		$element->setVar('ele_value', $value);
		if( !$xforms_ele_mgr->insert($element) ){
			adminHtmlHeader();
			echo $element->getHtmlErrors();
		}else{
			redirect_header(xforms_URL.'admin/elements.php?form_id='.$form_id, 0, _AM_DBUPDATED);
		}
	break;
	default:
		adminHtmlHeader();
		echo "<h4>"._AM_ELE_CREATE."</h4>
		<ul>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=text'>"._AM_ELE_TEXT."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=textarea'>"._AM_ELE_TAREA."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=select'>"._AM_ELE_SELECT."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=checkbox'>"._AM_ELE_CHECK."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=radio'>"._AM_ELE_RADIO."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=yn'>"._AM_ELE_YN."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=html'>"._AM_ELE_HTML."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=uploadimg'>"._AM_ELE_UPLOADIMG."</a></li>
		<li><a href='"._THIS_PAGE."?op=edit&amp;ele_type=upload'>"._AM_ELE_UPLOADFILE."</a></li>
		</ul>"
		;
	break;
}
include 'footer.php';
xoops_cp_footer();


function addOption($id1, $id2, $text='', $type='check', $checked=null){
	$d = new XoopsFormText('', $id1, 40, 255, $text);
	if( $type == 'check' ){
		$c = new XoopsFormCheckBox('', $id2, $checked);
		$c->addOption(1, ' ');
	}else{
		$c = new XoopsFormRadio('', 'checked', $checked);
		$c->addOption($id2, ' ');
	}
	$t = new XoopsFormElementTray('');
	$t->addElement($c);
	$t->addElement($d);
	return $t;
}

function addOptionsTray(){
	$t = new XoopsFormText('', 'addopt', 3, 2);
	$l = new XoopsFormLabel('', sprintf(_AM_ELE_ADD_OPT, $t->render()));
	$b = new XoopsFormButton('', 'submit', _AM_ELE_ADD_OPT_SUBMIT, 'submit');
	$r = new XoopsFormElementTray('');
	$r->addElement($l);
	$r->addElement($b);
	return $r;
}
?>