<?php
###################################
##  See license.txt for license  ##
###################################

require 'header.php';
$myts = MyTextSanitizer::getInstance();
if( empty($_POST['submit']) ){
global $xoopsCaptcha, $xoopsModuleConfig;
    if ( $xoopsModuleConfig['captcha'] ) {
        // Verify entered code 
        if ( class_exists( 'XoopsFormCaptcha' ) ) { 
            if ( @include_once XOOPS_ROOT_PATH . '/class/captcha/captcha.php' ) {
                $xoopsCaptcha = XoopsCaptcha::instance(); 
                if ( ! $xoopsCaptcha -> verify( true ) ) { 
                    redirect_header( 'index.php', 2, $xoopsCaptcha -> getMessage() ); 
                } 
            } 
        }
    }
	$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
	if( empty($form_id) ){
		$forms = $xforms_form_mgr->getPermittedForms();
		if( false != $forms && count($forms) === 1 ){
			$form = $xforms_form_mgr->get($forms[0]->getVar('form_id'));
			require 'include/form_render.php';
		}else{
			$xoopsOption['template_main'] = 'xforms_index.html';
			require_once XOOPS_ROOT_PATH.'/header.php';
			if( count($forms) > 0 ){
				foreach( $forms as $form ){
					$xoopsTpl->append('forms',
								array('title' => $form->getVar('form_title'),
									'desc' => $form->getVar('form_desc'),
									'id' => $form->getVar('form_id')
									)
								);
				}
				$xoopsTpl->assign('forms_intro', $myts->displayTarea($xoopsModuleConfig['intro']));
			}else{
				$xoopsTpl->assign('noform', $myts->displayTarea($xoopsModuleConfig['noform']));
			}	
		}
	}else{
		if( !$form = $xforms_form_mgr->get($form_id) ){
			header("Location: ".xforms_URL);
			exit();
		}else{
			if( false != $xforms_form_mgr->getSingleFormPermission($form_id) ){
				require 'include/form_render.php';
			}else{
				header("Location: ".xforms_URL);
				exit();
			}
		}
	}
	require XOOPS_ROOT_PATH.'/footer.php';
}else{
	$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
	if( empty($form_id) || !$form =& $xforms_form_mgr->get($form_id) || $xforms_form_mgr->getSingleFormPermission($form_id) == false ){
		header("Location: ".xforms_URL);
		exit();
	}
	require 'include/form_execute.php';
}
?>