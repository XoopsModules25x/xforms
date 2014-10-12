<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################

class xformsElementRenderer{
	var $_ele;

	function xformsElementRenderer(&$element){
		$this->_ele =& $element;
	}

	function &constructElement($admin=false){
		global $xoopsUser, $form;
		$myts = MyTextSanitizer::getInstance();
		$ele_caption = $this->_ele->getVar('ele_caption');
		$ele_value = $this->_ele->getVar('ele_value');
		$e = $this->_ele->getVar('ele_type');
		$delimiter = $form->getVar('form_delimiter');
		$form_ele_id = $admin ? 'ele_value['.$this->_ele->getVar('ele_id').']' : 'ele_'.$this->_ele->getVar('ele_id');
		switch($e){
			case 'text':
				if( !is_object($xoopsUser) ){
					$ele_value[2] = str_replace('{UNAME}', '', $ele_value[2]);
					$ele_value[2] = str_replace('{EMAIL}', '', $ele_value[2]);
				}elseif( !$admin ){
					$ele_value[2] = str_replace('{UNAME}', $xoopsUser->getVar('uname', 'e'), $ele_value[2]);
					$ele_value[2] = str_replace('{EMAIL}', $xoopsUser->getVar('email', 'e'), $ele_value[2]);
				}
				$form_ele = new XoopsFormText(
					$ele_caption,
					$form_ele_id,
					$ele_value[0],	//	box width
					$ele_value[1],	//	max width
					$myts->htmlspecialchars($myts->stripSlashesGPC($ele_value[2]))	//	default value
				);
			break;

			case 'textarea':
				$form_ele = new XoopsFormTextArea(
					$ele_caption,
					$form_ele_id,
					$myts->htmlspecialchars($myts->stripSlashesGPC($ele_value[0])),	//	default value
					$ele_value[1],	//	rows
					$ele_value[2]	//	cols
				);
			break;
			
			case 'html':
				global $check_req;
				if( !$admin ){
					$form_ele = new XoopsFormLabel(
						$ele_caption,
						$myts->displayTarea($myts->stripSlashesGPC($ele_value[0]), 1)
					);
				}else{
					$form_ele = new XoopsFormDhtmlTextArea(
						$ele_caption,
						$form_ele_id,
						$myts->htmlspecialchars($myts->stripSlashesGPC($ele_value[0])),	//	default value
						$ele_value[1],	//	rows
						$ele_value[2]	//	cols
					);
					$check_req->setExtra('disabled="disabled"');
				}
			break;

			case 'select':
				$selected = array();
				$options = array();
				$opt_count = 1;
				while( $i = each($ele_value[2]) ){
					$options[$opt_count] = $myts->stripSlashesGPC($i['key']);
					if( $i['value'] > 0 ){
						$selected[] = $opt_count;
					}
				$opt_count++;
				}
				$form_ele = new XoopsFormSelect(
					$ele_caption,
					$form_ele_id,
					$selected,
					$ele_value[0],	//	size
					$ele_value[1]	//	multiple
				);
				if( $ele_value[1] ){
					$this->_ele->setVar('ele_req', 0);
				}
				$form_ele->addOptionArray($options);
			break;
			
			case 'checkbox':
				$selected = array();
				$options = array();
				$opt_count = 1;
				while( $i = each($ele_value) ){
					$options[$opt_count] = $i['key'];
					if( $i['value'] > 0 ){
						$selected[] = $opt_count;
					}
					$opt_count++;
				}
				
				$form_ele = new XoopsFormElementTray($ele_caption, $delimiter == 'b' ? '<br />' : ' ');
				while( $o = each($options) ){
					$t = new XoopsFormCheckBox(
						'',
						$form_ele_id.'[]',
						$selected
					);
					$other = $this->optOther($o['value'], $form_ele_id);
					if( $other != false && !$admin ){
						$t->addOption($o['key'], _xforms_OPT_OTHER.$other);
					}else{
						$t->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
					}
					$form_ele->addElement($t);
				}
			break;
			
			case 'radio':
			case 'yn':
				$selected = '';
				$options = array();
				$opt_count = 1;
				while( $i = each($ele_value) ){
					switch($e){
						case 'radio':
							$options[$opt_count] = $i['key'];
						break;
						case 'yn':
							$options[$opt_count] = constant($i['key']);
						break;
					}
					if( $i['value'] > 0 ){
						$selected = $opt_count;
					}
					$opt_count++;
				}
				switch($delimiter){
					case 'b':
						$form_ele = new XoopsFormElementTray($ele_caption, '<br />');
						while( $o = each($options) ){
							$t = new XoopsFormRadio(
								'',
								$form_ele_id,
								$selected
							);
							$other = $this->optOther($o['value'], $form_ele_id);
							if( $other != false && !$admin ){
								$t->addOption($o['key'], _xforms_OPT_OTHER.$other);
							}else{
								$t->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
							}
							$form_ele->addElement($t);
						}
					break;
					case 's':
					default:
						$form_ele = new XoopsFormRadio(
							$ele_caption,
							$form_ele_id,
							$selected
						);
						while( $o = each($options) ){
							$other = $this->optOther($o['value'], $form_ele_id);
							if( $other != false && !$admin ){
								$form_ele->addOption($o['key'], _xforms_OPT_OTHER.$other);
							}else{
								$form_ele->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
							}
						}
					break;
				}
			break;
			
			case 'upload':
			case 'uploadimg':
				if( $admin ){
					$form_ele = new XoopsFormElementTray('', '<br />');
					$form_ele->addElement(new XoopsFormText(_AM_ELE_UPLOAD_MAXSIZE, $form_ele_id.'[0]', 10, 20, $ele_value[0]));
					if( $e == 'uploadimg' ){
						$form_ele->addElement(new XoopsFormText(_AM_ELE_UPLOADIMG_MAXWIDTH, $form_ele_id.'[4]', 10, 20, $ele_value[4]));
						$form_ele->addElement(new XoopsFormText(_AM_ELE_UPLOADIMG_MAXHEIGHT, $form_ele_id.'[5]', 10, 20, $ele_value[5]));
					}
				}else{
					global $form_output;
					$form_output->setExtra('enctype="multipart/form-data"');
					$form_ele = new XoopsFormFile($ele_caption, $form_ele_id, $ele_value[0]);
				}
			break;
			
			default:
				$form_ele = false;
			break;
		}
		return $form_ele;
	}
	
	function optOther($s='', $id){
		global $xoopsModuleConfig;
		if( !preg_match('/\{OTHER\|+[0-9]+\}/', $s) ){
			return false;
		}
		$s = explode('|', preg_replace('/[\{\}]/', '', $s));
		$len = !empty($s[1]) ? $s[1] : $xoopsModuleConfig['t_width'];
		$box = new XoopsFormText('', 'other['.$id.']', $len, 255);
		return $box->render();
	}

}
?>