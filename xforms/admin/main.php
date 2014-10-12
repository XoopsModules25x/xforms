<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
include 'admin_header.php';
$myts = MyTextSanitizer::getInstance();
$op = isset($_GET['op']) ? trim($_GET['op']) : 'list';
$op = isset($_POST['op']) ? trim($_POST['op']) : $op;

switch($op){
	case 'list':
	default:
		xoops_cp_header();
		$indexAdmin = new ModuleAdmin();	
		echo $indexAdmin->addNavigation('main.php') ;

        $indexAdmin->addItemButton(_MI_xforms_ADMENU2, 'main.php?op=edit', 'add', '');
        $indexAdmin->addItemButton(_MI_xforms_ADMENU3, 'editelement.php', 'add', '');
        echo $indexAdmin->renderButton('right', '');


		$criteria = new Criteria(1, 1);
		$criteria->setSort('form_order');
		$criteria->setOrder('ASC');
		if( $forms = $xforms_form_mgr->getObjects($criteria, 'admin_list') ){
			echo '<form action="'.xforms_ADMIN_URL.'" method="post">
				<table class="outer" cellspacing="1" width="100%">
					<tr><th colspan="5">'._AM_FORM_LISTING.'</th></tr>
					<tr>
						<td class="head" align="center">'._AM_ID.'</td>
						<td class="head" align="center">'._AM_FORM_ORDER.'<br />'._AM_FORM_ORDER_DESC.'</td>
						<td class="head" align="center">'._AM_FORM_TITLE.'</td>
						<td class="head" align="center">'._AM_FORM_SENDTO.'</td>
						<td class="head" align="center">'._AM_ACTION.'</td>
					</tr>';
			foreach( $forms as $f ){
				$id = $f->getVar('form_id');
				$order = new XoopsFormText('', 'order['.$id.']', 3, 2, $f->getVar('form_order'));
				$group_mgr = xoops_gethandler('group');
				$sendto = $f->getVar('form_send_to_group');
				if( false != $sendto && $group = $group_mgr->get($sendto) ){
					$sendto = $group->getVar('name');
				}else{
					$sendto = _AM_FORM_SENDTO_ADMIN;
				}
				$ids = new XoopsFormHidden('ids[]', $id);
				echo '
					<tr>
						<td class="odd" align="center">'.$id.'</td>
						<td class="even" align="center">'.$order->render().'</td>
						<td class="odd"><a target="_blank" href="'.xforms_URL.'?form_id='.$id.'">'.$f->getVar('form_title').'</a></td>
						<td class="odd" align="center">'.$sendto.'</td>
						<td class="odd" align="center">
                    	<a href="'.xforms_ADMIN_URL.'?op=edit&amp;form_id='.$id.'"><img src='.$pathIcon16.'/edit.png title='._AM_FORM_ACTION_EDITFORM.'alt='._AM_FORM_ACTION_EDITFORM.'></a>
						<a href="'.'elements.php?form_id='.$id.'"><img src='.$pathIcon16.'/1day.png title='._AM_FORM_ACTION_EDITELEMENT.'alt='._AM_FORM_ACTION_EDITELEMENT.'></a>
						<a href="'.xforms_ADMIN_URL.'?op=edit&amp;clone=1&amp;form_id='.$id.'"><img src='.$pathIcon16.'/editcopy.png title='._AM_FORM_ACTION_CLONE.'alt='._AM_FORM_ACTION_CLONE.'></a>

           	           <a href="'.xforms_ADMIN_URL.'?op=delete&amp;form_id='.$id.'"><img src='.$pathIcon16.'/delete.png title='._DELETE.' alt='._DELETE.'></a>'.$ids->render()
                    .'</td>
					</tr>';
			}
			$submit = new XoopsFormButton('', 'submit', _AM_RESET_ORDER, 'submit');
			echo '
					<tr>
						<td class="foot">&nbsp;</td>
						<td class="foot" align="center">'.$submit->render().'</td>
						<td class="foot" colspan="3">&nbsp;</td>
					</tr>	
					</table>';
			$hidden = new XoopsFormHidden('op', 'saveorder');
			echo $hidden->render()."\n</form>\n";
		}
	break;

	case 'edit':
		$clone = isset($_GET['clone']) ? intval($_GET['clone']) : false;
		$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
		xoops_cp_header();
		$indexAdmin = new ModuleAdmin();	
		echo $indexAdmin->addNavigation('main.php?op=edit') ;				
		if( !empty($form_id) ){
			$form = $xforms_form_mgr->get($form_id);
		}else{
			$form = $xforms_form_mgr->create();
		}

		$text_form_title = new XoopsFormText(_AM_FORM_TITLE, 'form_title', 50, 255, $form->getVar('form_title', 'e'));

		$group_ids = $moduleperm_handler->getGroupIds($xforms_form_mgr->perm_name, $form_id, $xoopsModule->getVar('mid'));
		$select_form_group_perm = new XoopsFormSelectGroup(_AM_FORM_PERM, 'form_group_perm', true, $group_ids, 5, true);
		
		$select_form_send_method = new XoopsFormSelect(_AM_FORM_SEND_METHOD, 'form_send_method', $form->getVar('form_send_method'));
		$select_form_send_method->addOption('e', _AM_FORM_SEND_METHOD_MAIL);
		$select_form_send_method->addOption('p', _AM_FORM_SEND_METHOD_PM);
		$select_form_send_method->setDescription(_AM_FORM_SEND_METHOD_DESC);
		
		$select_form_send_to_group = new XoopsFormSelectGroup(_AM_FORM_SENDTO, 'form_send_to_group', false, $form->getVar('form_send_to_group'));
		$select_form_send_to_group->addOption('0', _AM_FORM_SENDTO_ADMIN);
		
		$select_form_delimiter = new XoopsFormSelect(_AM_FORM_DELIMETER, 'form_delimiter', $form->getVar('form_delimiter'));
		$select_form_delimiter->addOption('s', _AM_FORM_DELIMETER_SPACE);
		$select_form_delimiter->addOption('b', _AM_FORM_DELIMETER_BR);
		
		$text_form_order = new XoopsFormText(_AM_FORM_ORDER, 'form_order', 3, 2, $form->getVar('form_order'));
		$text_form_order->setDescription(_AM_FORM_ORDER_DESC);
		
		$submit_text = $form->getVar('form_submit_text');
		$text_form_submit_text = new XoopsFormText(_AM_FORM_SUBMIT_TEXT, 'form_submit_text', 50, 50, empty($submit_text) ? _SUBMIT : $submit_text);

		$tarea_form_desc = new XoopsFormDhtmlTextArea(_AM_FORM_DESC, 'form_desc', $form->getVar('form_desc', 'e'), 5);
		$tarea_form_desc->setDescription(_AM_FORM_DESC_DESC);
		
		$tarea_form_intro = new XoopsFormDhtmlTextArea(_AM_FORM_INTRO, 'form_intro', $form->getVar('form_intro', 'e'), 10);
		$tarea_form_intro->setDescription(_AM_FORM_INTRO_DESC);

		$text_form_whereto = new XoopsFormText(_AM_FORM_WHERETO, 'form_whereto', 50, 255, $form->getVar('form_whereto'));
		$text_form_whereto->setDescription(_AM_FORM_WHERETO_DESC);

		$hidden_op = new XoopsFormHidden('op', 'saveform');
		$submit = new XoopsFormButton('', 'submit', _AM_SAVE, 'submit');
		$submit1 = new XoopsFormButton('', 'submit', _AM_SAVE_THEN_ELEMENTS, 'submit');
		$tray = new XoopsFormElementTray('');
		$tray->addElement($submit);
		$tray->addElement($submit1);
		
		if( empty($form_id) ){
			$caption = _AM_FORM_NEW;
		}else{
			if( $clone ){
				$caption = sprintf(_AM_COPIED, $form->getVar('form_title'));
				$clone_form_id = new XoopsFormHidden('clone_form_id', $form_id);
				$text_form_title = new XoopsFormText(_AM_FORM_TITLE, 'form_title', 50, 255, sprintf(_AM_COPIED, $form->getVar('form_title', 'e')));
			}else{
				$caption = sprintf(_AM_FORM_EDIT, $form->getVar('form_title'));
				$hidden_form_id = new XoopsFormHidden('form_id', $form_id);
			}
		}
		$output = new XoopsThemeForm($caption, 'editform', xforms_ADMIN_URL);
		$output->addElement($text_form_title, true);
		$output->addElement($select_form_group_perm);
		$output->addElement($select_form_send_method);
		$output->addElement($select_form_send_to_group);
		$output->addElement($select_form_delimiter);
		$output->addElement($text_form_order);
		$output->addElement($text_form_submit_text, true);
		$output->addElement($tarea_form_desc);
		$output->addElement($tarea_form_intro);
		$output->addElement($text_form_whereto);
		$output->addElement($hidden_op);
		if( isset($hidden_form_id) && is_object($hidden_form_id) ){
			$output->addElement($hidden_form_id);
		}
		if( isset($clone_form_id) && is_object($clone_form_id) ){
			$output->addElement($clone_form_id);
		}
		$output->addElement($tray);
		$output->display();
	break;

	case 'delete':
		if( empty($_POST['ok']) ){
			//adminHtmlHeader();
	xoops_cp_header();			
			xoops_confirm(array('op' => 'delete', 'form_id' => $_GET['form_id'], 'ok' => 1), xforms_ADMIN_URL, _AM_FORM_CONFIRM_DELETE);
		}else{
			$form_id = intval($_POST['form_id']);
			if( empty($form_id) ){
				redirect_header(xforms_ADMIN_URL, 0, _AM_NOTHING_SELECTED);
			}
			$form = $xforms_form_mgr->get($form_id);
			$xforms_form_mgr->delete($form);
			$xforms_ele_mgr = xoops_getmodulehandler('elements');
			$criteria = new Criteria('form_id', $form_id);
			$xforms_ele_mgr->deleteAll($criteria);
			$xforms_form_mgr->deleteFormPermissions($form_id);
			redirect_header(xforms_ADMIN_URL, 0, _AM_DBUPDATED);
		}
	break;

	case 'saveorder':
		if( !isset($_POST['ids']) || count($_POST['ids']) < 1 ){
			redirect_header(xforms_ADMIN_URL, 0, _AM_NOTHING_SELECTED);
		}
		extract($_POST);
		foreach( $ids as $id ){
			$form = $xforms_form_mgr->get($id);
			$form->setVar('form_order', $order[$id]);
			$xforms_form_mgr->insert($form);
		}
		redirect_header(xforms_ADMIN_URL, 0, _AM_DBUPDATED);
	break;

	case 'saveform':
		if( !isset($_POST['submit']) ){
			redirect_header(xforms_ADMIN_URL, 0, _AM_NOTHING_SELECTED);
		}
		$error = '';
		extract($_POST);
		if( !empty($form_id) ){
			$form = $xforms_form_mgr->get($form_id);
		}else{
			$form = $xforms_form_mgr->create();
		}
		$form->setVar('form_send_method', $form_send_method);
		$form->setVar('form_send_to_group', $form_send_to_group);
		$form->setVar('form_order', $form_order);
		$form->setVar('form_delimiter', $form_delimiter);
		$form->setVar('form_title', $form_title);
		$form->setVar('form_submit_text', $form_submit_text);
		$form->setVar('form_desc', $form_desc);
		$form->setVar('form_intro', $form_intro);
		$form->setVar('form_whereto', $form_whereto);
		if( !$ret = $xforms_form_mgr->insert($form) ){
			$error = $form->getHtmlErrors();
		}else{
			$xforms_form_mgr->deleteFormPermissions($ret);
			if( count($form_group_perm) > 0 ){
				$xforms_form_mgr->insertFormPermissions($ret, $form_group_perm);
			}
			if( !empty($clone_form_id) ){
				$xforms_ele_mgr = xoops_getmodulehandler('elements');
				$criteria = new Criteria('form_id', $clone_form_id);
				$count = $xforms_ele_mgr->getCount($criteria);
				if( $count > 0 ){
					$elements = $xforms_ele_mgr->getObjects($criteria);
					foreach( $elements as $e ){
						$cloned = $e->xoopsClone();
						$cloned->setVar('form_id', $ret);
						if( !$xforms_ele_mgr->insert($cloned) ){
							$error .= $cloned->getHtmlErrors();
						}
					}
				}
			}elseif( empty($form_id) ){
				$xforms_ele_mgr = xoops_getmodulehandler('elements');
				$error = $xforms_ele_mgr->insertDefaults($ret);
			}
		}
		if( !empty($error) ){
			//adminHtmlHeader();
				xoops_cp_header();
			echo $error;
		}else{
			if( $_POST['submit'] == _AM_SAVE_THEN_ELEMENTS ){
				redirect_header(xforms_URL.'admin/elements.php?form_id='.$ret, 0, _AM_DBUPDATED);
			}else{
				redirect_header(xforms_ADMIN_URL.'?op=edit&amp;form_id='.$ret, 0, _AM_DBUPDATED);
			}
		}
	break;	
	
	case 'liaise_import':
	xoops_cp_header();
	$indexAdmin = new ModuleAdmin();
	echo $indexAdmin->addNavigation('main.php?op=liaise_import');	
	
		//include(XOOPS_ROOT_PATH."/modules/xforms/admin/liaise_import.php");
		if(file_exists(XOOPS_ROOT_PATH."/modules/liaise/xoops_version.php")){
			$sql = array();
			$sql[0] = "DROP TABLE `".$xoopsDB -> prefix( 'xforms_formelements' )."`;";
			$sql[1] = "CREATE TABLE ".$xoopsDB -> prefix( 'xforms_formelements' )." LIKE ".$xoopsDB -> prefix( 'liaise_formelements' ).";";
			$sql[2] = "INSERT ".$xoopsDB -> prefix( 'xforms_formelements' )." SELECT * FROM ".$xoopsDB -> prefix( 'liaise_formelements' ).";"; 
			$sql[3] = "DROP TABLE `".$xoopsDB -> prefix( 'xforms_forms' )."`;";
			$sql[4] = "CREATE TABLE ".$xoopsDB -> prefix( 'xforms_forms' )." LIKE ".$xoopsDB -> prefix( 'liaise_forms' ).";";
			$sql[5] = "INSERT ".$xoopsDB -> prefix( 'xforms_forms' )." SELECT * FROM ".$xoopsDB -> prefix( 'liaise_forms' ).";";
		
			$number = count($sql);
			for( $i=0; $i<count($sql); $i++ ){
				if(false == $xoopsDB->queryF($sql[$i])) {
					echo 'ERROR: &nbsp;'.$xoopsDB->error().' ('.$xoopsDB->errno().')<br />';
					$number--;
				/*}else{
					echo 'The query "'.$sql[$i].'" is executed...<br />';*/
				}
			}

			If ($number == count($sql)) {
				echo '<br /><strong>'._AM_IMPORT_SUCCES.'</strong>';
			}else{
				echo '<br /><strong>'._AM_IMPORT_FAILED.'</strong>';
			}
		}else{
			echo _AM_IMPORT_NFND;
		}
	break;
}

include 'admin_footer.php';
xoops_cp_footer();
?>