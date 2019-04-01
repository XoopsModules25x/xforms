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

defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!isset($form) || empty($form) || !is_object($form)) {
    header("Location: index.php");
    exit();
}

$xformsEleMgr = xoops_getmodulehandler('elements');
$criteria     = new CriteriaCompo();
$criteria->add(new Criteria('form_id', $form->getVar('form_id')), 'AND');
$criteria->add(new Criteria('ele_display', 1), 'AND');
$criteria->setSort('ele_order');
$criteria->setOrder('ASC');
$elements = $xformsEleMgr->getObjects($criteria, true);

$msg = $err = $attachments = array();
foreach ($_POST as $k => $v) {
    if (preg_match('/^ele_[0-9]+$/', $k)) {
        $n          = explode("_", $k);
        $ele[$n[1]] = $v;
    }
}
if (isset($_POST['xoops_upload_file']) && is_array($_POST['xoops_upload_file'])) {
    foreach ($_POST['xoops_upload_file'] as $k => $v) {
        $n          = explode("_", $v);
        $ele[$n[1]] = $v;
    }
}

if ($xoopsModuleConfig['captcha']) {
    // Verify entered code
    xoops_load('XoopsCaptcha');
    if (class_exists('XoopsFormCaptcha')) {
        $xoopsCaptcha = XoopsCaptcha::getInstance();
        if (!$xoopsCaptcha->verify()) {
            $err[] = $xoopsCaptcha->getMessage();
        }
    }
}

/*
 * Generate the extra info
 */
$genInfo = array(
    "UID"   => "0",
    "UNAME" => "",
    "IP"    => "",
    "AGENT" => ""
);
if (count($err) == 0) {
    if (is_object($xoopsUser)) {
        $genInfo["UID"]   = $xoopsUser->getVar("uid"); /*Set the user id*/
        $genInfo["UNAME"] = $xoopsUser->getVar("uname"); /*Set the user name*/
    }

    $proxy = $_SERVER['REMOTE_ADDR'];
    $ip    = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_PROXY_CONNECTION'])) {
        $ip = $_SERVER['HTTP_PROXY_CONNECTION'];
    } elseif (isset($_SERVER['HTTP_VIA'])) {
        $ip = $_SERVER['HTTP_VIA'];
    }
    $ip = empty($ip) ? $_SERVER['REMOTE_ADDR'] : $ip;
    if ($proxy != $ip) {
        $ip = $ip . sprintf(_MD_XFORMS_PROXY, $proxy);
    }
    $genInfo["IP"]    = $ip; /*Set the IP*/
    $genInfo["AGENT"] = $_SERVER['HTTP_USER_AGENT']; /*Set the Agent*/
}

/*
 * Se recorren los elementos del formulario para guardar o enviar e-mail
 */
$udataMgr = xoops_getmodulehandler('userdata');
$udatas   = array();

$userMailText = ""; /* Capturing email for user if have textbox in the form */

$saveToDB = false;
if ($form->getVar('form_save_db') != 0) {
    $saveToDB = true;
}

if (count($err) == 0) {
    $timedata = time();
    foreach ($elements as $i) {
        $eleType  = $i->getVar('ele_type');
        $eleValue = $i->getVar('ele_value');
        if ($eleType == 'html') {
            $msg[$eleId] .= "<br /><br />" . $myts->displayTarea($myts->stripSlashesGPC($eleValue[0]), 1);
            continue; // The html element does not enter data
        }

        $eleId      = $i->getVar('ele_id');
        $eleOrder     = $i->getVar('ele_req');
        $eleCaption = $i->getVar('ele_caption');

        $udata = null;
        if ($saveToDB) {
            $udata = $udataMgr->create();
            $udata->setVar('uid', $genInfo["UID"]);
            $udata->setVar('form_id', $form->getVar('form_id'));
            $udata->setVar('udata_time', $timedata);
            $udata->setVar('udata_ip', $genInfo["IP"]);
            $udata->setVar('udata_agent', $genInfo["AGENT"]);
            $udata->setVar('ele_id', $eleId);
        }
        $uDataValue = array();

        $ufid = -1;
        if (isset($ele[$eleId]) && !empty($ele[$eleId])) {
            if ($eleCaption != '') {
                $msg[$eleId] = "<br />- " . $myts->displayTarea($myts->stripSlashesGPC($eleCaption), 1) . "<br />";
            }
        include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $countries = XoopsLists::getCountryList();
            switch ($eleType) {
                case 'upload':
                case 'uploadimg':
                    if (isset($_FILES['ele_' . $eleId])) {
                        require_once XFORMS_ROOT_PATH . 'class/uploader.php';
                        $ext  = empty($eleValue[1]) ? 0 : explode('|', $eleValue[1]);
                        $mime = empty($eleValue[2]) ? 0 : explode('|', $eleValue[2]);

                        if ($eleType == 'uploadimg') {
                            $uploader[$eleId] = new XFormsMediaUploader(XFORMS_UPLOAD_PATH, $eleValue[0], $ext, $mime, $eleValue[4], $eleValue[5]);
                        } else {
                            $uploader[$eleId] = new XFormsMediaUploader(XFORMS_UPLOAD_PATH, $eleValue[0], $ext, $mime);
                        }
                        if ($eleValue[0] == 0) {
                            $uploader[$eleId]->noAdminSizeCheck(true);
                        }
                        if ($uploader[$eleId]->fetchMedia('ele_' . $eleId, null, $i)) {
                            $uploader[$eleId]->prefix = $form->getVar('form_id') . '_';
                            if (false == $uploader[$eleId]->upload()) {
                                $err[] = $uploader[$eleId]->getErrors();
                            } else {
                                $saved       = $uploader[$eleId]->savedFileName;
                                $uploaded[]  = array(
                                    'id'     => $eleId,
                                    'file'   => $saved,
                                    'name'   => $_FILES['ele_' . $eleId]['name'],
                                    'saveto' => $eleValue[3]
                                );
                                $uDataValue = array(
                                    'file' => $saved,
                                    'name' => $_FILES['ele_' . $eleId]['name']
                                );
                                $ufid        = count($uploaded) - 1;
                            }
                        } else {
                            if (count($uploader[$eleId]->errors) > 0) {
                                $err[] = $uploader[$eleId]->getErrors();
                            }
                        }
                    }
                    break;

                case 'text':
                    $ele[$eleId] = trim($ele[$eleId]);
                    if (preg_match('/\{EMAIL\}/', $eleValue[2])) {
                        if (!checkEmail($ele[$eleId])) {
                            $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                        } else {
                            $replyMail = $ele[$eleId];
                        }
                    }
                    if (preg_match('/\{UNAME\}/', $eleValue[2])) {
                        $replyName = $ele[$eleId];
                    }
                    $msg[$eleId] .= $myts->stripSlashesGPC($ele[$eleId]);
                    $uDataValue[0] = $myts->stripSlashesGPC($ele[$eleId]);
                    if (0 !== (int)$eleValue[3]) && empty($userMailText) && !empty($ele[$eleId])) {
                        /* Obtain the user email from the form */
                        $userMailText = $myts->stripSlashesGPC($ele[$eleId]);
                    }

                    break;

                case 'textarea':
                    $msg[$eleId]  .= $myts->stripSlashesGPC($ele[$eleId]);
                    $uDataValue[0] = $myts->stripSlashesGPC($ele[$eleId]);
                    break;

                case 'radio':
                    $optCount = 1;
                    while ($v = each($eleValue)) {
                        if ($optCount == $ele[$eleId]) {
                            $other = checkOther($v['key'], $eleId, $eleCaption);
                            if ($other != false) {
                                $msg[$eleId] .= $other;
                                $uDataValue[] = $other;
                            } else {
                                $msg[$eleId] .= $myts->stripSlashesGPC($v['key']);
                                $uDataValue[] = $myts->stripSlashesGPC($v['key']);
                            }
                        }
                        ++$optCount;
                    }
                    break;
            case 'select2':
                $ele[$eleId] = trim($ele[$eleId]);
                if( preg_match('/\{EMAIL\}/', $eleValue[2]) ){
                    if( !checkEmail($ele[$eleId]) ){
                        $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                    }else{
                        $replyMail = $ele[$eleId];
                    }
                }
                if( preg_match('/\{UNAME\}/', $eleValue[2]) ){
                    $replyName = $ele[$eleId];
                }
        //		$msg[$eleId] .= $myts->stripSlashesGPC($ele[$eleId]);

                $msg[$eleId] .= $countries[$myts->stripSlashesGPC($ele[$eleId])];
                $uDataValue[0] = $countries[$myts->stripSlashesGPC($ele[$eleId])];

            break;
            case 'date':
                $ele[$eleId] = trim($ele[$eleId]);
                if( preg_match('/\{EMAIL\}/', $eleValue) ){
                    if( !checkEmail($ele[$eleId]) ){
                        $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                    }else{
                        $replyMail = $ele[$eleId];
                    }
                }
                if( preg_match('/\{UNAME\}/', $eleValue) ){
                    $replyName = $ele[$eleId];
                }
                $msg[$eleId] .= $myts->stripSlashesGPC($ele[$eleId]);
                $uDataValue[0] = $myts->stripSlashesGPC($ele[$eleId]);
            break;
                case 'yn':
                    $v = ($ele[$eleId] == 2) ? _NO : _YES;
                    $msg[$eleId] .= $myts->stripSlashesGPC($v);
                    $uDataValue[0] = $myts->stripSlashesGPC($v);
                    break;

                case 'checkbox':
                    $optCount = 1;
                    $ch        = array();
                    while ($v = each($eleValue)) {
                        if (is_array($ele[$eleId])) {
                            if (in_array($optCount, $ele[$eleId])) {
                                $other = checkOther($v['key'], $eleId, $eleCaption);
                                if ($other != false) {
                                    $ch[] = $other;
                                } else {
                                    $ch[] = $myts->stripSlashesGPC($v['key']);
                                }
                            }
                            ++$optCount;
                        } else {
                            if (!empty($ele[$eleId])) {
                                $ch[] = $myts->stripSlashesGPC($v['key']);
                            }
                        }
                    }
                    $msg[$eleId] .= !empty($ch) ? implode("<br />", $ch) : '';
                    $uDataValue = $ch;
                    break;

                case 'select':
                    $optCount = 1;
                    $ch        = array();
                    if (is_array($ele[$eleId])) {
                        while ($v = each($eleValue[2])) {
                            if (in_array($optCount, $ele[$eleId])) {
                                $ch[] = $myts->stripSlashesGPC($v['key']);
                            }
                            ++$optCount;
                        }
                    } else {
                        while ($j = each($eleValue[2])) {
                            if ($optCount == $ele[$eleId]) {
                                $ch[] = $myts->stripSlashesGPC($j['key']);
                            }
                            ++$optCount;
                        }
                    }
                    $msg[$eleId] .= !empty($ch) ? implode("<br />", $ch) : '';
                    $uDataValue = $ch;
                    break;

                default:
                    break;
            }
        } elseif ($eleOrder == 1) {
            $err[] = sprintf(_MD_XFORMS_ERR_REQ, $eleCaption);
        }
        if ($saveToDB) {
            $udata->setVar('udata_value', $uDataValue);
            $udatas[] = $udata;
            if (!$udataMgr->insert($udata)) {
                $err[] = $udata->getHtmlErrors();
            }
            if ($ufid >= 0) {
                $uploaded[$ufid]['udata_id'] = $udata->getVar('udata_id');
            }
        }
    }
}

/*
 * Send forms if "form_send_method" is "e" or "p", "n" for not send
 */
if ((count($err) == 0) && ($form->getVar('form_send_method') != 'n')) {
    /*
     * Include message template
     */
    if (is_dir(XFORMS_ROOT_PATH . "/language/" . $xoopsConfig['language'] . "/mail_template")) {
        $templateDir = XFORMS_ROOT_PATH . "/language/" . $xoopsConfig['language'] . "/mail_template";
    } else {
        $templateDir = XFORMS_ROOT_PATH . "/language/english/mail_template";
    }

    /* Mail to sending internal */
    $interMail = xoops_getMailer();
    $interMail->setHTML();
    $interMail->setTemplateDir($templateDir);
    $interMail->setTemplate('xforms.tpl');
    $interMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT, $myts->stripSlashesGPC($form->getVar('form_title'))));

    /*
     * Add extra general info
     */
    $interMail->assign("UNAME", '');
    $interMail->assign("ULINK", '');
    $interMail->assign("IP", '');
    $interMail->assign("AGENT", '');
    $interMail->assign("FORMURL", '');
    if (in_array('user', $xoopsModuleConfig['moreinfo'])) {
        if (is_object($xoopsUser)) {
            $interMail->assign("UNAME", sprintf(_MD_XFORMS_MSG_UNAME, $xoopsUser->getVar("uname")));
            $interMail->assign("ULINK", sprintf(_MD_XFORMS_MSG_UINFO, XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar("uid")));
        } else {
            $interMail->assign("UNAME", sprintf(_MD_XFORMS_MSG_UNAME, $xoopsConfig['anonymous']));
            $interMail->assign("ULINK", '');
        }
    }
    if (in_array('ip', $xoopsModuleConfig['moreinfo'])) {
        $proxy = $_SERVER['REMOTE_ADDR'];
        $ip    = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_PROXY_CONNECTION'])) {
            $ip = $_SERVER['HTTP_PROXY_CONNECTION'];
        } elseif (isset($_SERVER['HTTP_VIA'])) {
            $ip = $_SERVER['HTTP_VIA'];
        }
        $ip = empty($ip) ? $_SERVER['REMOTE_ADDR'] : $ip;
        if ($proxy != $ip) {
            $ip = $ip . sprintf(_MD_XFORMS_PROXY, $proxy);
        }
        $interMail->assign("IP", sprintf(_MD_XFORMS_MSG_IP, $ip));
    }
    if (in_array('agent', $xoopsModuleConfig['moreinfo'])) {
        $interMail->assign("AGENT", sprintf(_MD_XFORMS_MSG_AGENT, $_SERVER['HTTP_USER_AGENT']));
    }
    if (in_array('form', $xoopsModuleConfig['moreinfo'])) {
        $interMail->assign("FORMURL", sprintf(_MD_XFORMS_MSG_FORMURL, XFORMS_URL . '/index.php?form_id=' . $formId));
    }

    /*
     * Set header and footer of email
     */
    $eheader = $form->getVar('form_email_header');
    if (empty($eheader)) {
        $interMail->assign("EHEADER", '');
    } else {
        $interMail->assign("EHEADER", $eheader);
    }
    $efooter = $form->getVar('form_email_footer');
    if (empty($efooter)) {
        $interMail->assign("EFOOTER", '');
    } else {
        $interMail->assign("EFOOTER", $efooter);
    }

    /*
     * Prepare mail copy for user
     */
    $copyMail = null;
    $copyMsg  = null;
    $sendCopy = false;
    if (0 !== (int)$form->getVar('form_send_copy')) {
        $sendCopy = true;
    }
    if ($sendCopy) {
        $copyMail = xoops_getMailer();
        $copyMail->setHTML();
        $copyMail->setTemplateDir($templateDir);
        $copyMail->setTemplate('xforms_copy.tpl');
        $copyMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT_COPY, $myts->stripSlashesGPC($form->getVar('form_title'))));
        $charset           = !empty($xoopsModuleConfig['mail_charset']) ? $xoopsModuleConfig['mail_charset'] : _CHARSET;
        $copyMail->charSet = $charset;

        /* Set header and footer of email */
        $euheader = $form->getVar('form_email_uheader');
        if (empty($euheader)) {
            $copyMail->assign("EHEADER", '');
        } else {
            $copyMail->assign("EHEADER", $euheader);
        }
        $eufooter = $form->getVar('form_email_ufooter');
        if (empty($eufooter)) {
            $copyMail->assign("EFOOTER", '');
        } else {
            $copyMail->assign("EFOOTER", $eufooter);
        }
        $copyMsg = $msg;
    }

    /*
     * Send form by selected method
     */
    $sendGroup = (int)$form->getVar('form_send_to_group');
    $group      = false;
    if ($sendGroup != -1) {
        $group = $member_handler->getGroup($sendGroup);
    }
    if ($form->getVar('form_send_method') == 'p' && is_object($xoopsUser) && ($group != false)) {
        /* Send by private message */
        $interMail->usePM();
        $interMail->setToGroups($group);
    } else {
        /* Send by e-mail */
        $interMail->useMail();
        $interMail->setFromName($xoopsConfig['sitename']);
        $interMail->setFromEmail($xoopsConfig['adminmail']);
        if (isset($replyMail)) {
            $interMail->multimailer->AddReplyTo($replyMail, isset($replyName) ? '"' . $replyName . '"' : null);
        }
        $charset            = !empty($xoopsModuleConfig['mail_charset']) ? $xoopsModuleConfig['mail_charset'] : _CHARSET;
        $interMail->charSet = $charset;
        if ($sendGroup > 0) {
            /* Setting the selected groups */
            $interMail->setToGroups($group);
        } else {
            if ($sendGroup == -1) {
                /* Setting the emails specifics */
                $emailsto = explode(';', $form->getVar('form_send_to_other'));
                if (!empty($emailsto)) {
                    $interMail->setToEmails($emailsto);
                } else {
                    $interMail->setToEmails($xoopsConfig['adminmail']);
                }
            } else {
                /* Setting the admin e-mail */
                $interMail->setToEmails($xoopsConfig['adminmail']);
            }
        }
    }

    /*
     * Attaching the uploaded files (images and files)
     */
    if (count($uploaded) > 0) {
        foreach ($uploaded as $a) {
            if (false == $interMail->isMail || $a['saveto']) {
                if ($saveToDB) {
                    $msg[$a['id']] .= sprintf(
                        _MD_XFORMS_UPLOADED_FILE,
                        '<a href="' . XFORMS_URL . '/file.php?ui=' . $a['udata_id'] . '&fm=' . $formId . '&el=' . $a['id'] . '">' . $a['name'] . '</a>'
                    );
                } else {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, '<a href="' . XFORMS_URL . '/file.php?f=' . $a['file'] . '&fn=' . $a['name'] . '">' . $a['name'] . '</a>');
                }
            } else {
                if ($interMail->multimailer->AddAttachment(XFORMS_UPLOAD_PATH . $a['file'], $a['name'])) {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_ATTACHED_FILE, $a['name']);
                } else {
                    $err[] = $interMail->multimailer->ErrorInfo;
                }
            }
            if ($sendCopy) {
                $copyMsg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, $a['name']);
            }
        }
    }

    /*
     * Send the message, email or private message and send the copy if the option is selected.
     */
    $interMail->assign("MSG", implode("<br /><br />", $msg));
    if (count($err) < 1) {
        if (!$interMail->send(true)) {
            $err[] = $interMail->getErrors();
        }
    }
    if ($sendCopy && (count($err) == 0)) {
        $emailstoCopy = array();
        if (is_object($xoopsUser)) {
            $emailstoCopy[] = $xoopsUser->getVar('email');
        } elseif (!empty($userMailText)) {
            $emailstoCopy[] = trim($userMailText);
        }
        $copyMail->setToEmails($emailstoCopy);
        if (!empty($copyMail->toEmails)) {
            $copyMail->assign("MSG", implode("<br /><br />", $copyMsg));
            $copyMail->send(true); /* Don't control errors for send copy */
        }
    }
}

/*
 * Redirect user to error page on error in the process
 */
if (count($err) > 0) {
    if (count($uploaded) > 0) {
        foreach ($uploaded as $u) {
            @unlink(XFORMS_UPLOAD_PATH . $u['file']);
        }
    }
    if ($saveToDB) {
        foreach ($udatas as $ud) {
            $udataMgr->delete($ud);
        }
    }
    $xoopsOption['template_main'] = 'xforms_error.tpl';
    include_once XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('error_heading', _MD_XFORMS_ERR_HEADING);
    $xoopsTpl->assign('errors', $err);
    $xoopsTpl->assign('go_back', _BACK);
    $xoopsTpl->assign('XFORMS_URL', XFORMS_URL . '/index.php?form_id=' . $formId);
    $xoopsTpl->assign('xoops_pagetitle', _MD_XFORMS_ERR_HEADING);
    include XOOPS_ROOT_PATH . '/footer.php';
    exit();
}

/*
 * Redirect the user to the success page on send the form
 */
$whereto = $form->getVar('form_whereto');
$whereto = (!empty($whereto)) ? str_replace('{SITE_URL}', XOOPS_URL, $whereto) : XOOPS_URL . '/index.php';
redirect_header($whereto, 0, _MD_XFORMS_MSG_SENT);

/**
 * @param $key
 * @param $id
 * @param $caption
 *
 * @return bool|string
 */
function checkOther($key, $id, $caption)
{
    global $err, $myts;
    if (!preg_match('/\{OTHER\|+[0-9]+\}/', $key)) {
        return false;
    } else {
        if (!empty($_POST['other']['ele_' . $id])) {
            return _MD_XFORMS_OPT_OTHER . $myts->stripSlashesGPC($_POST['other']['ele_' . $id]);
        } else {
            $err[] = sprintf(_MD_XFORMS_ERR_REQ, $caption);
        }
    }

    return false;
}
