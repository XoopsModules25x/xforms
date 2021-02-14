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
 * @package   \XoopsModules\Xforms\frontside
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @link      https://github.com/XoopsModules25x/xforms
 * @since     1.30
 */

use Xmf\FilterInput;
use Xmf\Request;
use XoopsModules\Xforms;
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\MediaUploader;
use XoopsModules\Xforms\Utility;

require __DIR__ . '/header.php';
$myts   = \MyTextSanitizer::getInstance();

/**
 * @var string $moduleDirName
 * @var \XoopsModules\Xforms\Helper $helper
 * @var \XoopsModules\Xforms\FormsHandler $formsHandler
 */
$helper->loadLanguage('admin');

$submit = Request::getCmd('submit', '', 'POST');
if (empty($submit)) {
    $formId = Request::getInt('form_id', 0, 'GET');
    if (empty($formId)) {
        if (Constants::FORM_LIST_NO_SHOW === (int)$helper->getConfig('showforms')) {
            // Don't show the forms available if no parameter set
            redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_MEDIUM, _MD_XFORMS_MSG_NOFORM_SELECTED);
        }
        $forms = $formsHandler->getPermittedForms();
        if ((false !== $forms) && (1 == count($forms))) {
            /** @var \XoopsModules\Xforms\Forms $form */
            $form = $formsHandler->get($forms[0]->getVar('form_id'));
            if (!$assignedArray = $form->render()) {
                redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_LONG, $form->getHtmlErrors());
            }
            if (Constants::FORM_DISPLAY_STYLE_FORM == $form->getVar('form_display_style')) {
                $GLOBALS['xoopsOption']['template_main'] = 'xforms_form.tpl';
            } else {
                $GLOBALS['xoopsOption']['template_main'] = 'xforms_form_poll.tpl';
            }
            require_once $GLOBALS['xoops']->path('/header.php');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/style.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.structure.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.theme.min.css');
            $GLOBALS['xoTheme']->addScript('browse.php?modules/' . $moduleDirName . '/assets/js/modernizr-custom.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
            $GLOBALS['xoopsTpl']->assign($assignedArray);
        } else {
            $GLOBALS['xoopsOption']['template_main'] = 'xforms_index.tpl';
            require_once $GLOBALS['xoops']->path('/header.php');
            if ((false !== $forms) && (count($forms) > 0)) {
                foreach ($forms as $form) {
                    $GLOBALS['xoopsTpl']->append(
                        'forms',
                        [
                            'title'          => $form->getVar('form_title'),
                            'desc'           => $form->getVar('form_desc'),
                                                                   'id' => $form->getVar('form_id'),
                            'form_edit_link' => $form->getEditLinkInfo(),
                        ]
                    );
                }
                $GLOBALS['xoopsTpl']->assign('forms_intro', $myts->displayTarea($helper->getConfig('intro'), 1));
            } else {
                $GLOBALS['xoopsTpl']->assign('noform', $myts->displayTarea($helper->getConfig('noform'), 1));
            }
        }
    } else {
        /**
         * @var \XoopsModules\Xforms\FormsHandler $formsHandler
         * @var \XoopsModules\Xforms\Forms $form
         */
        if (($form = $formsHandler->get($formId))
            && (false !== $formsHandler->getSingleFormPermission($formId))) {
            if (!$form->isActive()) {
                redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_MEDIUM, _MD_XFORMS_MSG_INACTIVE);
            }
            if (!$assignedArray = $form->render()) {
                 redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_LONG, $form->getHtmlErrors());
            }
            if (Constants::FORM_DISPLAY_STYLE_FORM == $form->getVar('form_display_style')) {
                $GLOBALS['xoopsOption']['template_main'] = 'xforms_form.tpl';
            } else {
                $GLOBALS['xoopsOption']['template_main'] = 'xforms_form_poll.tpl';
            }
            require_once $GLOBALS['xoops']->path('/header.php');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/style.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.structure.min.css');
            $GLOBALS['xoTheme']->addStylesheet('browse.php?modules/' . $moduleDirName . '/assets/css/jquery-ui.theme.min.css');
            $GLOBALS['xoTheme']->addScript('browse.php?modules/' . $moduleDirName . '/assets/js/modernizr-custom.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
            $GLOBALS['xoopsTpl']->assign($assignedArray);
        } else {
            redirect_header($GLOBALS['xoops']->url('www'), Constants::REDIRECT_DELAY_MEDIUM, _NOPERM);
        }
    }

    $GLOBALS['xoopsTpl']->assign('default_title', $helper->getConfig('dtitle'));
    require $GLOBALS['xoops']->path('/footer.php');
    exit();
}

//-------------------------------
// Now execute the form
//-------------------------------
/** @var \XoopsSecurity $GLOBALS['xoopsSecurity'] */
if (!$GLOBALS['xoopsSecurity']->check()) {
    $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
}

$formId = Request::getInt('form_id', 0, 'POST');
if (empty($formId)
    || !($form = $formsHandler->get($formId))
    || (false === $formsHandler->getSingleFormPermission($formId))) {
    redirect_header(XOOPS_URL, Constants::REDIRECT_DELAY_MEDIUM, _AM_XFORMS_NO_PERM);
}
if (!$form->isActive()) {
    $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _MD_XFORMS_MSG_INACTIVE);
}

$msg = $err = [];

include (dirname(__DIR__, 2)) . '/class/captcha/xoopscaptcha.php';
$xfCaptchaObj = \XoopsCaptcha::getInstance();
if (!$xfCaptchaObj->verify()) {
    $err[] = $xfCaptchaObj->getMessage();
}

require_once $helper->path('include/common.php');

$xformsEleHandler = $helper->getHandler('Element');
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('form_id', $form->getVar('form_id')), 'AND');
$criteria->add(new \Criteria('ele_display', Constants::ELEMENT_DISPLAY), 'AND');
$criteria->setSort('ele_order');
$criteria->order = 'ASC';
$eleObjArray = $xformsEleHandler->getObjects($criteria, true);

/** @var array $ele */
foreach ($_POST as $k => $v) {
    if (preg_match('/^ele_\d+$/', $k)) {
        $n          = explode('_', $k);
        $ele[$n[1]] = $v;
    }
}

$xoopsUploadFile = Request::getArray('xoops_upload_file', [], 'POST');
if (!empty($xoopsUploadFile)) {
    foreach ($xoopsUploadFile as $k => $v) {
        $n          = explode('_', $v);
        $ele[$n[1]] = $v;
    }
}

/** {@internal $ele values are not sanitized here}} */

// Generate the extra info
$genInfo = [
    'UID'   => '0',
               'UNAME' => '',
                  'IP' => '',
    'AGENT' => '',
];

/*
 * Loops through the elements of the form to save or send e-mail
 */
$uDataHandler = $helper->getHandler('UserData');
$udatas       = [];
$userMailText = ''; // Capturing email for user if have textbox in the form
$saveToDB     = (Constants::SAVE_IN_DB == $form->getVar('form_save_db')) ? true : false;

if (0 == count($err)) {
    if (isset($GLOBALS['xoopsUser']) && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
        $genInfo['UID']   = $GLOBALS['xoopsUser']->getVar('uid'); /*Set the user id*/
        $genInfo['UNAME'] = $GLOBALS['xoopsUser']->getVar('uname'); /*Set the user name*/
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
    if ($proxy !== $ip) {
        $ip .= sprintf(_MD_XFORMS_PROXY, $proxy);
    }
    $genInfo['IP']    = $ip; // Set the IP
    $genInfo['AGENT'] = $_SERVER['HTTP_USER_AGENT']; // Set the Agent

    $timeData = time();
    foreach ($eleObjArray as $eleObj) {
        $eleArray   = $eleObj->getValues(['ele_id', 'form_id', 'ele_type', 'ele_value', 'ele_req', 'ele_caption']);
        $eleId      = (int)$eleArray['ele_id'];
        $eleType    = FilterInput::clean($eleArray['ele_type'], 'ALPHANUM');
        $eleValue   = $eleArray['ele_value'];
        $eleReq     = (int)$eleArray['ele_req'];
        $eleCaption = $eleArray['ele_caption'];

        if ('html' === $eleType) {
            $msg[$eleId] = '<br><br>' . $myts->displayTarea($eleValue[0], Constants::ALLOW_HTML);
            continue; // html element does not have data
        }

        $udata      = null;
        $uDataValue = [];
        $ufid       = -1;

        if ($saveToDB) {
            $udata = $uDataHandler->create();
            $udata->setVars(
                [
                    'uid'         => (int)$genInfo['UID'],
                              'form_id' => (int)$formId,
                           'udata_time' => (int)$timeData,
                             'udata_ip' => $genInfo['IP'],
                          'udata_agent' => $genInfo['AGENT'],
                    'ele_id'      => $eleId,
                ]
            );
        }

        $ele[$eleId] = is_scalar($ele[$eleId]) ? trim($ele[$eleId]): $ele[$eleId];
        if (!empty($ele[$eleId])) {
            if ('' !== $eleCaption) {
                $msg[$eleId] = '<br>- ' . $myts->displayTarea($eleCaption, Constants::ALLOW_HTML) . '<br>';
            }
            xoops_load('xoopslists');
            switch ($eleType) {
                case 'pattern':
                case 'textarea':
                    $msg[$eleId]  .= FilterInput::clean($ele[$eleId], 'STRING'); // @test this value filter
                    $uDataValue[0] = FilterInput::clean($ele[$eleId], 'STRING');
                    break;
                case 'color':
                    $msg[$eleId]  .= preg_replace('/[^0-9a-f#]/', '', $ele[$eleId]);
                    $uDataValue[0] = preg_replace('/[^0-9a-f#]/', '', $ele[$eleId]);
                    break;
                case 'date':
                    $msg[$eleId]  .= preg_replace('/[^0-9\-]/', '', $ele[$eleId]);
                    $uDataValue[0] = preg_replace('/[^0-9\-]/', '', $ele[$eleId]);
                    break;
                case 'email':
                    $msg[$eleId]  .= FilterInput::clean($ele[$eleId], 'EMAIL'); // @test this value filter
                    $uDataValue[0] = FilterInput::clean($ele[$eleId], 'EMAIL');
                    break;
                case 'number':
                    $msg[$eleId]  .= FilterInput::clean($ele[$eleId], 'INT'); // @test this value filter
                    $uDataValue[0] = FilterInput::clean($ele[$eleId], 'INT');
                    break;
                case 'range':
                    $msg[$eleId]  .= FilterInput::clean($ele[$eleId], 'FLOAT'); // @test this value filter
                    $uDataValue[0] = FilterInput::clean($ele[$eleId], 'FLOAT');
                    break;
                case 'time':
                    $msg[$eleId]  .= preg_replace('/[^0-9:]/', '', $ele[$eleId]);
                    $msg[$eleId]  .= preg_replace('/[^0-9:]/', '', $ele[$eleId]);
                    break;
                case 'url':
                    $msg[$eleId]  .= FilterInput::clean($ele[$eleId], 'WEBURL'); // @test this value filter
                    $uDataValue[0] = FilterInput::clean($ele[$eleId], 'WEBURL');
                    break;
                case 'checkbox':
                    $opt_count = 1;
                    $ch        = [];
                    foreach ($eleValue as $key=>$v) {
                    //while ($v = each($eleValue)) {
                        if (is_array($ele[$eleId])) {
                            if (in_array($opt_count, $ele[$eleId])) {
                                $other = Utility::checkOther($key, $eleId, $eleCaption);
                                if (false !== $other) {
                                    $ch[] = $other;
                                } else {
                                    $ch[] = $key;
                                }
                            }
                            ++$opt_count;
                        } elseif (!empty($ele[$eleId])) {
                                $ch[] = $v['key'];
                            }
                        }
                    $msg[$eleId] .= !empty($ch) ? implode('<br>', $ch) : '';
                    $uDataValue   = $ch;
                    break;
                case 'obfuscated':
                    /** {@internal set msg to '***** - not transmitted in email'}}} */
                    $s             = '';
                    $data          = FilterInput::clean($ele[$eleId], 'STRING'); // @test this value filter
                    $msg[$eleId]   .= str_pad($s, mb_strlen($data), '*');
                    $uDataValue[0] = $data;
                    break;
                case 'radio':
                    $opt_count = 1;
                    foreach ($eleValue as $key=>$v) {
                    //while ($v = each($eleValue)) {
                        if ($opt_count == $ele[$eleId]) {
                            $other = Utility::checkOther($key, $eleId, $eleCaption);
                            if (false !== $other) {
                                $msg[$eleId] .= FilterInput::clean($other, 'STRING');
                                $uDataValue[] = FilterInput::clean($other, 'STRING');
                                //$msg[$eleId] .= $other;
                                //$uDataValue[] = $other;
                            } else {
                                $msg[$eleId] .= $key;
                                $uDataValue[] = $key;
                            }
                        }
                        ++$opt_count;
                    }
                    break;
                case 'select':
                    $opt_count = 1;
                    $ch        = [];
                    if (is_array($ele[$eleId])) {
                        foreach ($eleValue[2] as $key=>$v) {
                        //while ($v = each($eleValue[2])) {
                            if (in_array($opt_count, $ele[$eleId])) {
                                $ch[] = $key;
                            }
                            ++$opt_count;
                        }
                    } else {
                        foreach ($eleValue[2] as $key=>$j) {
                        //while ($j = each($eleValue[2])) {
                            if ($opt_count == $ele[$eleId]) {
                                $ch[] = $key;
                            }
                            ++$opt_count;
                        }
                    }
                    $msg[$eleId] .= !empty($ch) ? implode('<br>', $ch) : '';
                    $uDataValue   = $ch;
                    break;
                case 'select2': //left for backward compatibility w/ v2.00 ALPHA 1
                case 'country':
                    $countries = \XoopsLists::getCountryList();
                    if (is_array($ele[$eleId])) {
                        $cntryList = '';
                        foreach ($ele[$eleId] as $thisVal) { // @FIXME: does this $ele[] needs to be filtered?
                            if (!empty($cntryList)) {
                                $cntryList .= ',';
                            }
                            $cntryList .= array_key_exists($thisVal, $countries) ? $countries[$thisVal] : '';
                        }
                        $msg[$eleId]  .= $cntryList;
                        $uDataValue[0] = $cntryList;
                    } else {
                        $msg[$eleId]  .= $countries[$ele[$eleId]];
                        $uDataValue[0] = $countries[$ele[$eleId]];
                    }
                    break;
                case 'text':
                    if (preg_match('/\{EMAIL\}/', $eleValue[2])) {
                        if (!checkEmail($ele[$eleId])) {
                            $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                        } else {
                            $reply_mail = $ele[$eleId];
                        }
                    }
                    if (preg_match('/\{UNAME\}/', $eleValue[2])) {
                        $reply_name = $ele[$eleId];  // @FIXME: this $ele[] needs to be filtered
                    }
                    $msg[$eleId]  .= $ele[$eleId]; // @FIXME: this $ele[] needs to be filtered
                    $uDataValue[0] = $ele[$eleId];

                    /* Obtain the user email from the form */
                    if ((!empty($eleValue[3])) && empty($userMailText) && !empty($ele[$eleId])) {
                        if (checkEmail($ele[$eleId])) {
                            $userMailText = $ele[$eleId];
                        } else {
                            $err[] = _MD_XFORMS_ERR_INVALIDMAIL;
                        }
                    }
                    break;
                case 'yn':
                    $v = (2 == $ele[$eleId]) ? _NO : _YES;
                    $msg[$eleId]  .= $v;
                    $uDataValue[0] = $v;
                    break;
                case 'upload':
                case 'uploadimg':
                    if (isset($_FILES['ele_' . $eleId]) && !empty($_FILES['ele_' . $eleId]['name'])) {
                        /*
                        if (!class_exists('\XoopsModules\Xforms\MediaUploader')) {
                            xoops_load('MediaUploader', $moduleDirName);
                        }
                        */
                        $maxSize   = empty($eleValue[0]) ?    0 : (int)$eleValue[0];
                        $ext       = empty($eleValue[1]) ? null : explode('|', $eleValue[1]);
                        $mime      = empty($eleValue[2]) ? null : explode('|', $eleValue[2]);
                        $maxWidth  = empty($eleValue[4]) ? null : (int)$eleValue[4];
                        $maxHeight = empty($eleValue[5]) ? null : (int)$eleValue[5];

//                        if ('uploadimg' === $eleType) {
                            $uploader[$eleId] = new MediaUploader(XFORMS_UPLOAD_PATH, $maxSize, $ext, $mime, $maxWidth, $maxHeight);
//                        } else {
//                            $uploader[$eleId] = new \MediaUploader(XFORMS_UPLOAD_PATH, $maxSize, $ext, $mime);
//                        }
                        if (0 == $eleValue[0]) {
                            $uploader[$eleId]->setNoAdminSizeCheck(true);
                        }
                        if ($uploader[$eleId]->fetchMedia('ele_' . $eleId, null, $eleObj)) {
                            $uploader[$eleId]->prefix = $formId . '_';
                            if (false === $uploader[$eleId]->upload()) {
                                $err = array_merge($err, $uploader[$eleId]->getErrors(false));
                            } else {
                                $saved      = $uploader[$eleId]->savedFileName;
                                $uploaded[] = [
                                    'id'     => $eleId,
                                                  'file' => $saved,
                                                  'name' => $_FILES['ele_' . $eleId]['name'],
                                    'saveto' => $eleValue[3],
                                ];
                                $uDataValue = [
                                    'file' => $saved,
                                    'name' => $_FILES['ele_' . $eleId]['name'],
                                ];
                                $ufid       = count($uploaded) - 1;
                            }
                        } elseif (count($uploader[$eleId]->errors) > 0) {
                                $err = array_merge($err, $uploader[$eleId]->getErrors(false));
                            }
                        }
                    break;
                default:
                    break;
            }
        } elseif (Constants::ELEMENT_REQD == $eleReq) {
            $err[] = sprintf(_MD_XFORMS_ERR_REQ, $eleCaption);
        }
        if ($saveToDB) {
            $udata->setVar('udata_value', $uDataValue);
            $udatas[] = $udata;
            $newKey = $uDataHandler->insert($udata);
            if (false === $newKey) {
                $err = array_merge($err, $udata->getErrors());
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
if ((0 == count($err)) && (Constants::SEND_METHOD_NONE !== $form->getVar('form_send_method'))) {
    /*
     * Include message template
     */
    if (is_dir(XFORMS_ROOT_PATH . 'language/' . $GLOBALS['xoopsConfig']['language'] . '/mail_template')) {
        $templateDir = XFORMS_ROOT_PATH . 'language/' . $GLOBALS['xoopsConfig']['language'] . '/mail_template/';
    } else {
        $templateDir = XFORMS_ROOT_PATH . 'language/english/mail_template/';
    }

    /* Mail to sending internal */
    $interMail = xoops_getMailer();
    $interMail->setHTML();
    $interMail->setTemplateDir($templateDir);
    $interMail->setTemplate('xforms.tpl');
    $interMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT, $GLOBALS['xoopsConfig']['sitename'], $form->getVar('form_title')));

    /*
     * Add extra general info
    */
    $interMail->assign(
        [
            'UNAME'   => '',
                             'ULINK' => '',
                                'IP' => '',
                             'AGENT' => '',
            'FORMURL' => '',
        ]
    );

    $xformsMoreInfoConfig = $helper->getConfig('moreinfo');
    if (in_array('user', $xformsMoreInfoConfig)) {
        if (isset($GLOBALS['xoopsUser']) && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
            $interMail->assign(
                'UNAME',
                sprintf(
                _MD_XFORMS_MSG_UNAME,
                    $GLOBALS['xoopsUser']->getVar('uname')
                )
            );
            $interMail->assign(
                'ULINK',
                sprintf(
                _MD_XFORMS_MSG_UINFO,
                    $GLOBALS['xoops']->url('userinfo.php?uid=' . $GLOBALS['xoopsUser']->getVar('uid'))
                )
            );
        } else {
            $interMail->assign(
                'UNAME',
                sprintf(
                _MD_XFORMS_MSG_UNAME,
                    $GLOBALS['xoopsConfig']['anonymous']
                )
            );
            $interMail->assign('ULINK', '');
        }
    }
    if (in_array('ip', $xformsMoreInfoConfig)) {
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
        if ($proxy !== $ip) {
            $ip = $ip . sprintf(_MD_XFORMS_PROXY, $proxy);
        }
        $interMail->assign('IP', sprintf(_MD_XFORMS_MSG_IP, $ip));
    }
    if (in_array('agent', $xformsMoreInfoConfig)) {
        $interMail->assign('AGENT', sprintf(_MD_XFORMS_MSG_AGENT, $_SERVER['HTTP_USER_AGENT']));
    }
    if (in_array('form', $xformsMoreInfoConfig)) {
        $interMail->assign(
            'FORMURL',
            sprintf(
            _MD_XFORMS_MSG_FORMURL,
                $helper->url('index.php?form_id=' . $formId)
            )
        );
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
    $copyMail = $copyMsg = null;
    $sendCopy = (0 !== ((int)$form->getVar('form_send_copy'))) ? true : false;
    if ($sendCopy) {
        $copyMail = xoops_getMailer();
        $copyMail->setHTML();
        $copyMail->setTemplateDir($templateDir);
        $copyMail->setTemplate('xforms_copy.tpl');
        $copyMail->setSubject(sprintf(_MD_XFORMS_MSG_SUBJECT_COPY, $form->getVar('form_title')));
        $mailCharset = $helper->getConfig('mail_charset');
        $charset = !empty($mailCharset) ? $mailCharset : _CHARSET;
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
    $send_group = (int)$form->getVar('form_send_to_group');
    $group      = false;
    if (-1 !== $send_group) {
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        $group = $memberHandler->getGroup($send_group);
    }
    if (Constants::SEND_METHOD_PM == $form->getVar('form_send_method') && (isset($GLOBALS['xoopsUser']) && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) && (false !== $group)) {
        /* Send by private message */
        $interMail->setTemplate('xforms_pm.tpl');
        $interMail->usePM();
        $interMail->setToGroups($group);
    } else {
        /* Send by e-mail */
        $interMail->useMail();
        $interMail->setFromName($GLOBALS['xoopsConfig']['sitename']);
        $interMail->setFromEmail($GLOBALS['xoopsConfig']['adminmail']);
        if (isset($reply_mail)) {
            $interMail->multimailer->addReplyTo($reply_mail, isset($reply_name) ? '"' . $reply_name . '"' : null);
        }
        $mailCharset        = $helper->getConfig('mail_charset');
        $charset            = !empty($mailCharset) ? $mailCharset : _CHARSET;
        $interMail->charSet = $charset;
        if ($send_group > 0) {
            /* Setting the selected groups */
            $interMail->setToGroups($group);
        } elseif (-1 == $send_group) {
                /* Setting the emails specifics */
                $emailsto = explode(';', $form->getVar('form_send_to_other'));
                if (!empty($emailsto)) {
                    $interMail->setToEmails($emailsto);
                } else {
                    $interMail->setToEmails($GLOBALS['xoopsConfig']['adminmail']);
                }
            } else {
                /* Setting the admin e-mail */
                $interMail->setToEmails($GLOBALS['xoopsConfig']['adminmail']);
            }
        }

    /*
     * Attaching the uploaded files (images and files)
     */
    if (isset($uploaded) && (count($uploaded) > 0)) {
        foreach ($uploaded as $a) {
            if (false === $interMail->isMail || $a['saveto']) {
                if ($saveToDB) {
                    $msg[$a['id']] .= sprintf(
                        _MD_XFORMS_UPLOADED_FILE,
                        '<a href="' . $helper->url('file.php?ui=' . $a['udata_id'] . '&fm=' . $formId . '&el=' . $a['id']) . '">' . $a['name'] . '</a>'
                    );
                }
                else {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, '<a href="' . $helper->url('file.php?f=' . $a['file'] . '&fn=' . $a['name']) . '">' . $a['name'] . '</a>');
                }
            }
            elseif ($interMail->multimailer->addAttachment(XFORMS_UPLOAD_PATH . "/{$a['file']}", $a['name'])) {
                    $msg[$a['id']] .= sprintf(_MD_XFORMS_ATTACHED_FILE, $a['name']);
                }
            else {
                $err[] = $interMail->multimailer->ErrorInfo;
            }
            if ($sendCopy) {
                $copyMsg[$a['id']] .= sprintf(_MD_XFORMS_UPLOADED_FILE, $a['name']);
            }
        }
    }
    /*
     * Send the message, email or private message and send the copy if the option is selected.
     */
    if (Constants::SEND_METHOD_PM == $form->getVar('form_send_method')) {
        $msg = implode("\n\n", $msg);
        $msg = preg_replace('/<br>/', "\n", $msg);
//        $msg = strip_tags(htmlspecialchars_decode($msg), '<href>');
        $interMail->assign('MSG', $msg);
    } else {
        $interMail->assign('MSG', implode('<br><br>', $msg));
    }
    if (!$interMail->send(true)) {
        $err = array_merge($err, $interMail->getErrors(false)); // get mail errors as an array
    }
    if ($sendCopy && (0 == count($err))) {
        $emailstoCopy = [];
        if (isset($GLOBALS['xoopsUser']) && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
            $emailstoCopy[] = $GLOBALS['xoopsUser']->getVar('email');
        } elseif ($uMailText = checkEmail(trim($userMailText))) {
            $emailstoCopy[] = $uMailText;
        }
        $copyMail->setToEmails($emailstoCopy);
        if (!empty($copyMail->toEmails)) {
            $copyMail->assign('MSG', implode('<br><br>', $copyMsg));
            $copyMail->send(true); /* Don't control errors for send copy */
        }
    }
}

// Redirect user to error page on error in the process
if (0 < count($err)) {
    if (isset($uploaded) && (count($uploaded) > 0)) {
        foreach ($uploaded as $u) {
            @unlink(XFORMS_UPLOAD_PATH . "/{$u['file']}");
        }
    }
    if ($saveToDB) {
        if (!empty($udatas) && (count($udatas) > 0)) {
            foreach ($udatas as $ud) {
                $uDataHandler->delete($ud);
            }
        }
    }
    $GLOBALS['xoopsOption']['template_main'] = 'xforms_error.tpl';
    require_once $GLOBALS['xoops']->path('header.php');
    $GLOBALS['xoopsTpl']->assign(
        [
            'error_heading'   => _MD_XFORMS_ERR_HEADING,
                                              'errors' => $err,
                                             'go_back' => _BACK,
                                          'xforms_url' => $helper->url('index.php?form_id=' . $formId),
            'xoops_pagetitle' => _MD_XFORMS_ERR_HEADING,
        ]
    );
    require $GLOBALS['xoops']->path('/footer.php');
    exit();
}

/*
 * Redirect the user to the success page on send the form
 */
$whereto = $form->getVar('form_whereto');
$whereto = (!empty($whereto)) ? str_replace('{SITE_URL}', $GLOBALS['xoops']->url('www'), $whereto) : $GLOBALS['xoops']->url('www/index.php');
redirect_header($whereto, Constants::REDIRECT_DELAY_NONE, _MD_XFORMS_MSG_SENT);
