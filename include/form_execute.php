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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

use XoopsModules\Xforms;

if (!defined('XFORMS_ROOT_PATH')) {
    exit();
}

if (!isset($form) || empty($form) || !is_object($form)) {
    header('Location: index.php');
    exit();
}

/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

$xforms_ele_mgr = xoops_getModuleHandler('elements');
$criteria       = new \CriteriaCompo();
$criteria->add(new \Criteria('form_id', $form->getVar('form_id')), 'AND');
$criteria->add(new \Criteria('ele_display', 1), 'AND');
$criteria->setSort('ele_order');
$criteria->setOrder('ASC');
$elements = $xforms_ele_mgr->getObjects($criteria, true);

$msg = $err = $attachments = [];
foreach ($_POST as $k => $v) {
    if (preg_match('/^ele_[0-9]+$/', $k)) {
        $n          = explode('_', $k);
        $ele[$n[1]] = $v;
    }
}
if (isset($_POST['xoops_upload_file']) && is_array($_POST['xoops_upload_file'])) {
    foreach ($_POST['xoops_upload_file'] as $k => $v) {
        $n          = explode('_', $v);
        $ele[$n[1]] = $v;
    }
}

if ($helper->getConfig('captcha')) {
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
$genInfo = [
    'UID'   => '0',
    'UNAME' => '',
    'IP'    => '',
    'AGENT' => ''
];
if (0 == count($err)) {
    if (is_object($xoopsUser)) {
        $genInfo['UID']   = $xoopsUser->getVar('uid'); /*Set the user id*/
        $genInfo['UNAME'] = $xoopsUser->getVar('uname'); /*Set the user name*/
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
        $ip .= sprintf(_MD_XFORMS_PROXY, $proxy);
    }
    $genInfo['IP']    = $ip; /*Set the IP*/
    $genInfo['AGENT'] = $_SERVER['HTTP_USER_AGENT']; /*Set the Agent*/
}

/*
 * Se recorren los elementos del formulario para guardar o enviar e-mail
 */
$udata_mgr = xoops_getModuleHandler('userdata');
$udatas    = [];

$userMailText = ''; /* Capturing email for user if have textbox in the form */

$saveToDB = false;
if (0 != $form->getVar('form_save_db')) {
    $saveToDB = true;
}

if (0 == count($err)) {
    $timedata = time();
    foreach ($elements as $i) {
        $ele_type  = $i->getVar('ele_type');
        $ele_value = $i->getVar('ele_value');
        if ('html' === $ele_type) {
            $msg[$ele_id] .= '<br><br>' . $myts->displayTarea($myts->stripSlashesGPC($ele_value[0]), 1);
            continue; // The html element does not enter data
        }

        $ele_id      = $i->getVar('ele_id');
        $ele_req     = $i->getVar('ele_req');
        $ele_caption = $i->getVar('ele_caption');

        $udata = null;
        if ($saveToDB) {
            $udata = $udata_mgr->create();
            $udata->setVar('uid', $genInfo['UID']);
            $udata->setVar('form_id', $form->getVar('form_id'));
            $udata->setVar('udata_time', $timedata);
            $udata->setVar('udata_ip', $genInfo['IP']);
            $udata->setVar('udata_agent', $genInfo['AGENT']);
            $udata->setVar('ele_id', $ele_id);
        }
        $udata_value = [];

        $ufid = -1;
        if (isset($ele[$ele_id]) && !empty($ele[$ele_id])) {
            if ('' != $ele_caption) {
                $msg[$ele_id] = '<br>- ' . $myts->displayTarea($myts->stripSlashesGPC($ele_caption), 1) . '<br>';
            }
            require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
            $countries = \XoopsLists::getCountryList();
            switch ($ele_type) {
                case 'upload':
                case 'uploadimg':
                    if (isset($_FILES['ele_' . $ele_id])) {
                        require_once XFORMS_ROOT_PATH . '/class/uploader.php';
                        $ext  = empty($ele_value[1]) ? 0 : explode('|', $ele_value[1]);
                        $mime = empty($ele_value[2]) ? 0 : explode('|', $ele_value[2]);

                        if ('uploadimg' === $ele_type) {
                            $uploader[$ele_id] = new MediaUploader(XFORMS_UPLOAD_PATH, $ele_value[0], $ext, $mime, $ele_value[4], $ele_value[5]);
                        } else {
                            $uploader[$ele_id] = new MediaUploader(XFORMS_UPLOAD_PATH, $ele_value[0], $ext, $mime);
                        }
                        if (0 == $ele_value[0]) {
                            $uploader[$ele_id]->noAdminSizeCheck(true);
                        }
                        if ($uploader[$ele_id]->fetchMedia('ele_' . $ele_id, null, $i)) {
                            $uploader[$ele_id]->prefix = $form->getVar('form_id') . '_';
                            if (false === $uploader[$ele_id]->upload()) {
                                $err[] = $uploader[$ele_id]->getErrors();
                            } else {
                                $saved       = $uploader[$ele_id]->savedFileName;
                                $uploaded[]  = [
                                    'id'     => $ele_id,
                                    'file'   => $saved,
                                    'name'   => $_FILES['ele_' . $ele_id]['name'],
                                    'saveto' => $ele_value[3]
                                ];
                                $udata_value = [
                                    'file' => $saved,
                                    'name' => $_FILES['ele_' . $ele_id]['name']
                                ];
                                $ufid        = count($uploaded) - 1;
                            }
                        } else {
                            if (count($uploader[$ele_id]->errors) > 0) {
                                $err[] = $uploader[$ele_id]->getErrors();
                            }
                        }
                    }
                    break;

                case 'text':
                    $ele[$ele_id] = trim($ele[$ele_id]);
                    if (preg_match('/\{EMAIL\}/', $ele_value[2])) {
                        if (!checkEmail($ele[$ele_id])) {
                            $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                        } else {
                            $reply_mail = $ele[$ele_id];
                        }
                    }
                    if (preg_match('/\{UNAME\}/', $ele_value[2])) {
                        $reply_name = $ele[$ele_id];
                    }
                    $msg[$ele_id]   .= $myts->stripSlashesGPC($ele[$ele_id]);
                    $udata_value[0] = $myts->stripSlashesGPC($ele[$ele_id]);
                    if ((0 != intval($ele_value[3], 10)) && empty($userMailText) && !empty($ele[$ele_id])) {
                        /* Obtain the user email from the form */
                        $userMailText = $myts->stripSlashesGPC($ele[$ele_id]);
                    }

                    break;

                case 'textarea':
                    $msg[$ele_id]   .= $myts->stripSlashesGPC($ele[$ele_id]);
                    $udata_value[0] = $myts->stripSlashesGPC($ele[$ele_id]);
                    break;

                case 'radio':
                    $opt_count = 1;
                    foreach ($ele_value as $v) {
                        if ($opt_count == $ele[$ele_id]) {
                            $other = checkOther($v['key'], $ele_id, $ele_caption);
                            if (false !== $other) {
                                $msg[$ele_id]  .= $other;
                                $udata_value[] = $other;
                            } else {
                                $msg[$ele_id]  .= $myts->stripSlashesGPC($v['key']);
                                $udata_value[] = $myts->stripSlashesGPC($v['key']);
                            }
                        }
                        ++$opt_count;
                    }
                    break;
                case 'select2':
                    $ele[$ele_id] = trim($ele[$ele_id]);
                    if (preg_match('/\{EMAIL\}/', $ele_value[2])) {
                        if (!checkEmail($ele[$ele_id])) {
                            $err[] = _LIAISE_ERR_INVALIDMAIL;
                        } else {
                            $reply_mail = $ele[$ele_id];
                        }
                    }
                    if (preg_match('/\{UNAME\}/', $ele_value[2])) {
                        $reply_name = $ele[$ele_id];
                    }
                    //      $msg[$ele_id] .= $myts->stripSlashesGPC($ele[$ele_id]);

                    $msg[$ele_id]   .= $countries[$myts->stripSlashesGPC($ele[$ele_id])];
                    $udata_value[0] = $countries[$myts->stripSlashesGPC($ele[$ele_id])];

                    break;
                case 'date':
                    $ele[$ele_id] = trim($ele[$ele_id]);
                    if (preg_match('/\{EMAIL\}/', $ele_value)) {
                        if (!checkEmail($ele[$ele_id])) {
                            $err[] = _LIAISE_ERR_INVALIDMAIL;
                        } else {
                            $reply_mail = $ele[$ele_id];
                        }
                    }
                    if (preg_match('/\{UNAME\}/', $ele_value)) {
                        $reply_name = $ele[$ele_id];
                    }
                    $msg[$ele_id]   .= $myts->stripSlashesGPC($ele[$ele_id]);
                    $udata_value[0] = $myts->stripSlashesGPC($ele[$ele_id]);
                    break;
                case 'yn':
                    $v              = (2 == $ele[$ele_id]) ? _NO : _YES;
                    $msg[$ele_id]   .= $myts->stripSlashesGPC($v);
                    $udata_value[0] = $myts->stripSlashesGPC($v);
                    break;

                case 'checkbox':
                    $opt_count = 1;
                    $ch        = [];
                    foreach ($ele_value as $v) {
                        if (is_array($ele[$ele_id])) {
                            if (in_array($opt_count, $ele[$ele_id])) {
                                $other = checkOther($v['key'], $ele_id, $ele_caption);
                                if (false !== $other) {
                                    $ch[] = $other;
                                } else {
                                    $ch[] = $myts->stripSlashesGPC($v['key']);
                                }
                            }
                            ++$opt_count;
                        } else {
                            if (!empty($ele[$ele_id])) {
                                $ch[] = $myts->stripSlashesGPC($v['key']);
                            }
                        }
                    }
                    $msg[$ele_id] .= !empty($ch) ? implode('<br>', $ch) : '';
                    $udata_value  = $ch;
                    break;

                case 'select':
                    $opt_count = 1;
                    $ch        = [];
                    if (is_array($ele[$ele_id])) {
//                        while ($v = each($ele_value[2])) {
                    foreach($ele_Value[2] as $v) {
                            if (in_array($opt_count, $ele[$ele_id])) {
                                $ch[] = $myts->stripSlashesGPC($v['key']);
                            }
                            ++$opt_count;
                        }
                    } else {
//                        while ($j = each($ele_value[2])) {
                            foreach($ele_value[2] as $j) {
                            if ($opt_count == $ele[$ele_id]) {
                                $ch[] = $myts->stripSlashesGPC($j['key']);
                            }
                            ++$opt_count;
                        }
                    }
                    $msg[$ele_id] .= !empty($ch) ? implode('<br>', $ch) : '';
                    $udata_value  = $ch;
                    break;

                default:
                    break;
            }
        } elseif (1 == $ele_req) {
            $err[] = sprintf(_MD_XFORMS_ERR_REQ, $ele_caption);
        }
        if ($saveToDB) {
            $udata->setVar('udata_value', $udata_value);
            $udatas[] = $udata;
            if (!$udata_mgr->insert($udata)) {
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
if ((0 == count($err)) && ('n' !== $form->getVar('form_send_method'))) {
    /*
     * Include message template
     */
    if (is_dir(XFORMS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/mail_template')) {
        $template_dir = XFORMS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/mail_template';
    } else {
        $template_dir = XFORMS_ROOT_PATH . '/language/english/mail_template';
    }

    /* Mail to sending internal */
    $interMail = xoops_getMailer();
    $interMail->setHTML();
    $interMail->setTemplateDir($template_dir);
    $interMail->setTemplate('xforms.tpl');
    $interMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT, $myts->stripSlashesGPC($form->getVar('form_title'))));

    /*
     * Add extra general info
     */
    $interMail->assign('UNAME', '');
    $interMail->assign('ULINK', '');
    $interMail->assign('IP', '');
    $interMail->assign('AGENT', '');
    $interMail->assign('FORMURL', '');
    if (in_array('user', $helper->getConfig('moreinfo'))) {
        if (is_object($xoopsUser)) {
            $interMail->assign('UNAME', sprintf(_MD_XFORMS_MSG_UNAME, $xoopsUser->getVar('uname')));
            $interMail->assign('ULINK', sprintf(_MD_XFORMS_MSG_UINFO, XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->getVar('uid')));
        } else {
            $interMail->assign('UNAME', sprintf(_MD_XFORMS_MSG_UNAME, $xoopsConfig['anonymous']));
            $interMail->assign('ULINK', '');
        }
    }
    if (in_array('ip', $helper->getConfig('moreinfo'))) {
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
            $ip .= sprintf(_MD_XFORMS_PROXY, $proxy);
        }
        $interMail->assign('IP', sprintf(_MD_XFORMS_MSG_IP, $ip));
    }
    if (in_array('agent', $helper->getConfig('moreinfo'))) {
        $interMail->assign('AGENT', sprintf(_MD_XFORMS_MSG_AGENT, $_SERVER['HTTP_USER_AGENT']));
    }
    if (in_array('form', $helper->getConfig('moreinfo'))) {
        $interMail->assign('FORMURL', sprintf(_MD_XFORMS_MSG_FORMURL, XFORMS_URL . '/index.php?form_id=' . $form_id));
    }

    /*
     * Set header and footer of email
     */
    $eheader = $form->getVar('form_email_header');
    if (empty($eheader)) {
        $interMail->assign('EHEADER', '');
    } else {
        $interMail->assign('EHEADER', $eheader);
    }
    $efooter = $form->getVar('form_email_footer');
    if (empty($efooter)) {
        $interMail->assign('EFOOTER', '');
    } else {
        $interMail->assign('EFOOTER', $efooter);
    }

    /*
     * Prepare mail copy for user
     */
    $copyMail = null;
    $copyMsg  = null;
    $sendCopy = false;
    if (0 != intval($form->getVar('form_send_copy'), 10)) {
        $sendCopy = true;
    }
    if ($sendCopy) {
        $copyMail = xoops_getMailer();
        $copyMail->setHTML();
        $copyMail->setTemplateDir($template_dir);
        $copyMail->setTemplate('xforms_copy.tpl');
        $copyMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT_COPY, $myts->stripSlashesGPC($form->getVar('form_title'))));
        $charset           = !empty($helper->getConfig('mail_charset')) ? $helper->getConfig('mail_charset') : _CHARSET;
        $copyMail->charSet = $charset;

        /* Set header and footer of email */
        $euheader = $form->getVar('form_email_uheader');
        if (empty($euheader)) {
            $copyMail->assign('EHEADER', '');
        } else {
            $copyMail->assign('EHEADER', $euheader);
        }
        $eufooter = $form->getVar('form_email_ufooter');
        if (empty($eufooter)) {
            $copyMail->assign('EFOOTER', '');
        } else {
            $copyMail->assign('EFOOTER', $eufooter);
        }
        $copyMsg = $msg;
    }

    /*
     * Send form by selected method
     */
    $send_group = intval($form->getVar('form_send_to_group'), 10);
    $group      = false;
    if (-1 != $send_group) {
        $group = $memberHandler->getGroup($send_group);
    }
    if ('p' === $form->getVar('form_send_method') && is_object($xoopsUser) && (false !== $group)) {
        /* Send by private message */
        $interMail->usePM();
        $interMail->setToGroups($group);
    } else {
        /* Send by e-mail */
        $interMail->useMail();
        $interMail->setFromName($xoopsConfig['sitename']);
        $interMail->setFromEmail($xoopsConfig['adminmail']);
        if (isset($reply_mail)) {
            $interMail->multimailer->addReplyTo($reply_mail, isset($reply_name) ? '"' . $reply_name . '"' : null);
        }
        $charset            = !empty($helper->getConfig('mail_charset')) ? $helper->getConfig('mail_charset') : _CHARSET;
        $interMail->charSet = $charset;
        if ($send_group > 0) {
            /* Setting the selected groups */
            $interMail->setToGroups($group);
        } else {
            if (-1 == $send_group) {
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
            if (false === $interMail->isMail || $a['saveto']) {
                if ($saveToDB) {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, '<a href="' . XFORMS_URL . '/file.php?ui=' . $a['udata_id'] . '&fm=' . $form_id . '&el=' . $a['id'] . '">' . $a['name'] . '</a>');
                } else {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, '<a href="' . XFORMS_URL . '/file.php?f=' . $a['file'] . '&fn=' . $a['name'] . '">' . $a['name'] . '</a>');
                }
            } else {
                if ($interMail->multimailer->addAttachment(XFORMS_UPLOAD_PATH . $a['file'], $a['name'])) {
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
    $interMail->assign('MSG', implode('<br><br>', $msg));
    if (count($err) < 1) {
        if (!$interMail->send(true)) {
            $err[] = $interMail->getErrors();
        }
    }
    if ($sendCopy && (0 == count($err))) {
        $emailstoCopy = [];
        if (is_object($xoopsUser)) {
            $emailstoCopy[] = $xoopsUser->getVar('email');
        } elseif (!empty($userMailText)) {
            $emailstoCopy[] = trim($userMailText);
        }
        $copyMail->setToEmails($emailstoCopy);
        if (!empty($copyMail->toEmails)) {
            $copyMail->assign('MSG', implode('<br><br>', $copyMsg));
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
            $udata_mgr->delete($ud);
        }
    }
    $GLOBALS['xoopsOption']['template_main'] = 'xforms_error.tpl';
    require_once XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('error_heading', _MD_XFORMS_ERR_HEADING);
    $xoopsTpl->assign('errors', $err);
    $xoopsTpl->assign('go_back', _BACK);
    $xoopsTpl->assign('XFORMS_URL', XFORMS_URL . '/index.php?form_id=' . $form_id);
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
