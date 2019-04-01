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
class XFormsElementRenderer
{
    private $ele;

    /**
     * @param $element
     */
    public function __construct($element)
    {
        $this->ele = $element;
    }

    /**
     * @param bool $admin
     *
     * @return XoopsFormDhtmlTextArea|XoopsFormElementTray|XoopsFormLabel|XoopsFormSelect|XoopsFormText|XoopsFormTextArea
     */
    public function constructElement($admin = false)
    {
        global $xoopsUser, $form;
        $myts        = MyTextSanitizer::getInstance();
        $eleCaption = $myts->displayTarea($myts->stripSlashesGPC($this->ele->getVar('ele_caption')), 1);
        $eleValue   = $this->ele->getVar('ele_value');
        $e           = $this->ele->getVar('ele_type');
        $delimiter   = $form->getVar('form_delimiter');
        $form_ele_id = $admin ? 'ele_value[' . $this->ele->getVar('ele_id') . ']' : 'ele_' . $this->ele->getVar('ele_id');
        switch ($e) {
            case 'text':
                $profile_handler = xoops_getmodulehandler('profile', 'profile');
                $member_handler  = xoops_gethandler('member');
                $xur             = null;
                $xpr             = null;
                if (is_object($xoopsUser)) {
                    $xur = $xoopsUser;
                    $xpr = $profile_handler->get($xoopsUser->getVar('uid'));
                } else {
                    $xur = $member_handler->createUser();
                    $xpr = $profile_handler->create();
                }
                if (!$admin) {
                    foreach ($xur->vars as $k => $v) {
                        $eleValue[2] = str_replace('{U_' . $k . '}', $xur->getVar($k, 'e'), $eleValue[2]);
                    }
                    foreach ($xpr->vars as $k => $v) {
                        $eleValue[2] = str_replace('{P_' . $k . '}', $xpr->getVar($k, 'e'), $eleValue[2]);
                    }
                }
                $form_ele = new XoopsFormText
                (
                    $eleCaption, $form_ele_id, $eleValue[0], //	box width
                    $eleValue[1], //	maxlenght
                    $myts->htmlSpecialChars($myts->stripSlashesGPC($eleValue[2])) //	default value
                );
                break;

            case 'textarea':
                $form_ele = new XoopsFormTextArea(
                    $eleCaption, $form_ele_id, $myts->htmlSpecialChars($myts->stripSlashesGPC($eleValue[0])), //	default value
                    $eleValue[1], //	rows
                    $eleValue[2] //	cols
                );
                break;

            case 'html':
                global $check_req;
                if (!$admin) {
                    $form_ele = new XoopsFormLabel(
                        $eleCaption, $myts->displayTarea($myts->stripSlashesGPC($eleValue[0]), 1), $form_ele_id
                    );
                } else {
                    $form_ele              = new XoopsFormDhtmlTextArea (
                        $eleCaption, $form_ele_id, $myts->htmlSpecialChars($myts->stripSlashesGPC($eleValue[0])) //	default value
                    );
                    $form_ele->skipPreview = true;
                    $check_req->setExtra('disabled="disabled"');
                }
                break;
            case 'date':
                if (isset($post_val)) {
                    $eleValue = $post_val;
                }
                $form_ele = new XoopsFormTextDateSelect(
                    $eleCaption, $form_ele_id, 15, strtotime($eleValue[0])
                );

                break;

            case 'select2':
                if (isset($post_val)) {
                    $eleValue = $post_val;
                }
                $form_ele = new XoopsFormSelectCountry(
                    $eleCaption, $form_ele_id, $myts->htmlSpecialChars($myts->stripSlashesGPC($eleValue[2]))    //	default value
                );
                break;

            case 'select':
                $selected  = array();
                $options   = array();
                $opt_count = 1;
                while ($i = each($eleValue[2])) {
                    $options[$opt_count] = $myts->stripSlashesGPC($i['key']);
                    if ($i['value'] > 0) {
                        $selected[] = $opt_count;
                    }
                    ++$opt_count;
                }
                $form_ele = new XoopsFormSelect (
                    $eleCaption, $form_ele_id, $selected, $eleValue[0], //	size
                    $eleValue[1] //	multiple
                );
                if ($eleValue[1]) {
                    $this->ele->setVar('ele_req', 0);
                }
                $form_ele->addOptionArray($options);
                break;

            case 'checkbox':
                $selected  = array();
                $options   = array();
                $opt_count = 1;
                while ($i = each($eleValue)) {
                    $options[$opt_count] = $i['key'];
                    if ($i['value'] > 0) {
                        $selected[] = $opt_count;
                    }
                    ++$opt_count;
                }

                $form_ele = new XoopsFormElementTray($eleCaption, $delimiter == 'b' ? '<br />' : ' ');
                while ($o = each($options)) {
                    $t     = new XoopsFormCheckBox('', $form_ele_id . '[]', $selected);
                    $other = $this->optOther($o['value'], $form_ele_id);
                    if ($other != false && !$admin) {
                        $t->addOption($o['key'], _MD_XFORMS_OPT_OTHER . $other);
                    } else {
                        $t->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
                    }
                    $form_ele->addElement($t);
                }
                break;

            case 'radio':
            case 'yn':
                $selected  = '';
                $options   = array();
                $opt_count = 1;
                while ($i = each($eleValue)) {
                    switch ($e) {
                        case 'radio':
                            $options[$opt_count] = $i['key'];
                            break;
                        case 'yn':
                            $options[$opt_count] = constant($i['key']);
                            break;
                    }
                    if ($i['value'] > 0) {
                        $selected = $opt_count;
                    }
                    ++$opt_count;
                }
                switch ($delimiter) {
                    case 'b':
                        $form_ele = new XoopsFormElementTray($eleCaption, '<br />');
                        while ($o = each($options)) {
                            $t     = new XoopsFormRadio('', $form_ele_id, $selected);
                            $other = $this->optOther($o['value'], $form_ele_id);
                            if ($other != false && !$admin) {
                                $t->addOption($o['key'], _MD_XFORMS_OPT_OTHER . $other);
                            } else {
                                $t->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
                            }
                            $form_ele->addElement($t);
                        }
                        break;
                    case 's':
                    default:
                        $form_ele = new XoopsFormRadio($eleCaption, $form_ele_id, $selected);
                        while ($o = each($options)) {
                            $other = $this->optOther($o['value'], $form_ele_id);
                            if ($other != false && !$admin) {
                                $form_ele->addOption($o['key'], _MD_XFORMS_OPT_OTHER . $other);
                            } else {
                                $form_ele->addOption($o['key'], $myts->stripSlashesGPC($o['value']));
                            }
                        }
                        break;
                }
                break;

            case 'upload':
            case 'uploadimg':
                if ($admin) {
                    $form_ele = new XoopsFormElementTray('', '<br />');
                    $form_ele->addElement(new XoopsFormText(_AM_XFORMS_ELE_UPLOAD_MAXSIZE, $form_ele_id . '[0]', 10, 20, $eleValue[0]));
                    if ($e == 'uploadimg') {
                        $form_ele->addElement(new XoopsFormText(_AM_XFORMS_ELE_UPLOADIMG_MAXWIDTH, $form_ele_id . '[4]', 10, 20, $eleValue[4]));
                        $form_ele->addElement(new XoopsFormText(_AM_XFORMS_ELE_UPLOADIMG_MAXHEIGHT, $form_ele_id . '[5]', 10, 20, $eleValue[5]));
                    }
                } else {
                    global $form_output;
                    $form_output->setExtra('enctype="multipart/form-data"');
                    $form_ele = new XoopsFormFile($eleCaption, $form_ele_id, $eleValue[0]);
                }
                break;

            default:
                $form_ele = false;
                break;
        }

        return $form_ele;
    }

    /**
     * @param string $s
     * @param        $id
     *
     * @return string
     */
    public function optOther($s = '', $id)
    {
        global $xoopsModuleConfig;
        if (!preg_match('/\{OTHER\|+[0-9]+\}/', $s)) {
            return false;
        }
        $s   = explode('|', preg_replace('/[\{\}]/', '', $s));
        $len = !empty($s[1]) ? $s[1] : $xoopsModuleConfig['t_width'];
        $box = new XoopsFormText('', 'other[' . $id . ']', $len, 255);
        $box->setExtra('onclick="var self=this; window.setTimeout(function () { self.focus(); }, 100);"');

        return $box->render();
    }
}
