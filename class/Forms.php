<?php

namespace XoopsModules\Xforms;

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
 * Module: Xforms
 *
 * @package   \XoopsModules\Xforms\class
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @since     1.30
 * @link      https://github.com/XoopsModules25x/xforms
 */
use XoopsModules\Xforms\Constants;
use XoopsModules\Xforms\Helper;
use XoopsModules\Xforms\FormCaptcha;
use XoopsModules\Xforms\ElementRenderer;

/**
 * Class \XoopsForms\Xforms\Forms
 */
class Forms extends \XoopsObject
{
    /**
     * @var string this module's directory
     */
    protected $dirname;

    /**
     * XformsForms constructor
     */
    public function __construct()
    {
        /**@todo set var options for form_save_db, form_send_method, form_delimiter, form_display_style, form_active
         * for example
         * $this->initVar('form_save_db', XOBJ_DTYPE_INT, Constants::SAVE_IN_DB, true, 1, Constants::SAVE_IN_DB|Constants::DO_NOT_SAVE_IN_DB);
         * $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, Constants::SEND_METHOD_MAIL, true, 1, Constants::SEND_METHOD_MAIL|Constants::SEND_METHOD_PM|Constants::SEND_METHOD_NONE);
         * $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, Constants::DELIMITER_SPACE, true, 1, Constants::DELIMITER_SPACE|Constants::DELIMITER_BR);
         * $this->initVar('form_display_style', XOBJ_DTYPE_TXTBOX, Constants::FORM_DISPLAY_STYLE_FORM, true, 1, Constants::FORM_DISPLAY_STYLE_FORM|Constants::FORM_DISPLAY_STYLE_POLL);
         * $this->initVar('form_active', XOBJ_DTYPE_INT, Constants::FORM_ACTIVE, true, Constants::FORM_ACTIVE|Constants::FORM_INACTIVE);
         */
        parent::__construct();
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('form_save_db', XOBJ_DTYPE_INT, Constants::SAVE_IN_DB, true, 1);
        $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, Constants::SEND_METHOD_MAIL, true, 1);
        $this->initVar('form_send_to_group', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('form_send_to_other', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('form_send_copy', XOBJ_DTYPE_INT);
        $this->initVar('form_order', XOBJ_DTYPE_INT, 1, false, 3);
        $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, Constants::DELIMITER_SPACE, true, 1);
        $this->initVar('form_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('form_submit_text', XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
        $this->initVar('form_desc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_intro', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_header', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_footer', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_uheader', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_ufooter', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_whereto', XOBJ_DTYPE_TXTBOX);
        $this->initVar('form_display_style', XOBJ_DTYPE_TXTBOX, Constants::FORM_DISPLAY_STYLE_FORM, true, 1);
        $this->initVar('form_begin', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('form_end', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('form_active', XOBJ_DTYPE_INT, Constants::FORM_ACTIVE, true);

        $this->dirname = basename(dirname(__DIR__));
    }

    /**
     * Check to see if form is active
     *
     * @return bool
     */
    public function isActive()
    {
        $now     = time();
        $fbegin  = (int)$this->getVar('form_begin');
        $fend    = (int)$this->getVar('form_end');
        $factive = (int)$this->getVar('form_active');
        $retVal  = true;
        // Check if form is inactive, hasn't started yet, or ended already
        if ((Constants::FORM_INACTIVE === $factive)
            || ((0 !== $fbegin && $fbegin > $now) || (0 !== $fend && $fend < $now))) {
            $retVal = false;
        }

        return $retVal;
    }

    /**
     * get info to create edit links for Admin
     *
     * @return string|array empty string if not an admin, else return edit link array
     */
    public function getEditLinkInfo()
    {
        $editLink = '';
        // Instantiate
        $helper = Helper::getInstance();     // module helper
        if (isset($GLOBALS['xoopsUser']) && $helper->isUserAdmin()) {
            $editLink = [
                'location'      => $helper->url('admin/main.php') . '?op=edit&form_id=' . $this->getVar('form_id'),
                              'target'        => '_self',
                              'icon_location' => \Xmf\Module\Admin::iconUrl('edit.png', '16'),
                              'icon_title'    => _AM_XFORMS_ACTION_EDITFORM,
                'icon_alt'      => _AM_XFORMS_ACTION_EDITFORM,
            ];
        }

        return $editLink;
    }

    /**
     * Render the Form
     *
     * @since v2.00 ALPHA 2
     * @return bool|array false on error|array containing variables for template
     */
    public function render()
    {
        // Instantiate
        $helper = Helper::getInstance();     // module helper
        $myts = \MyTextSanitizer::getInstance();

        if ((Constants::FORM_HIDDEN == $this->getVar('form_order'))
            && (!(isset($GLOBALS['xoopsUser']) || !$helper->isUserAdmin()))) {
            $this->setErrors(_NOPERM);

            return false;
        }

        xoops_load('xoopsformloader');
        //require_once $helper->path('class/ElementRenderer.php');
        $xformsEleHandler = $helper::getInstance()->getHandler('Element');
        //$xformsEleHandler = $helper->getHandler('Element');

        $helper->loadLanguage('admin');
        $helper->loadLanguage('main');

        // Read form elements
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('form_id', $this->getVar('form_id')));
        $criteria->add(new \Criteria('ele_display', Constants::ELEMENT_DISPLAY));
        $criteria->setSort('ele_order');
        $criteria->order = 'ASC';
        $eleObjects = $xformsEleHandler->getObjects($criteria, true);

        if (empty($eleObjects)) { // this form doesn't have any elements
            $this->setErrors(sprintf(_MD_XFORMS_ELE_ERR, $this->getVar('form_title'), 's'));

            return false;
        }

        $formOutput = new \XoopsThemeForm($this->getVar('form_title', 's'), 'xforms_' . $this->getVar('form_id'), $helper->url('index.php'), 'post', true);
        $eleCount = 1;
        $multipart = false;
        foreach ($eleObjects as $elementObj) {
            $eleRenderer = new ElementRenderer($elementObj);
            $formEle     = $eleRenderer->constructElement(false, $this->getVar('form_delimiter'));
            $req         = (Constants::ELEMENT_REQD !== (int)$elementObj->getVar('ele_req')) ? false : true;
            if (1 === $eleCount) {
                $formEle->setExtra('autofocus');  //give the 1st element focus on form load
            }
            $formEle->setExtra('tabindex="' . $eleCount++ . '"'); // allow tabbing through fields on form
            if (in_array($elementObj->getVar('ele_type'), ['upload', 'uploadimg'])) {
                $multipart = true; // will be a multipart form
            }
            $formOutput->addElement($formEle, $req);
            unset($formEle);
        }

        if ($multipart) { // set multipart attribute for form
            $formOutput->setExtra('enctype="multipart/form-data"');
        }
        $formOutput->addElement(new \XoopsFormHidden('form_id', $this->getVar('form_id')));
        $formOutput->addElement(new \XoopsFormCaptcha());

        $subButton = new \XoopsFormButton('', 'submit', $this->getVar('form_submit_text'), 'submit');
        $subButton->setExtra('tabindex="' . $eleCount++ . '"'); // allow tabbing to the Submit button too
        $formOutput->addElement($subButton, 1);

        $eles = [];
        foreach ($formOutput->getElements() as $currElement) {
            $id = $req = $name = $ele_type = false;
            $name    = $currElement->getName();
            $caption = $currElement->getCaption();
            if (!empty($name)) {
                $id = str_replace('ele_', '', $currElement->getName());
            } elseif (method_exists($currElement, 'getElements')) {
//            } elseif (method_exists($currElement, 'getElements') && is_callable('getElements')) {
                $obj = $currElement->getElements();
                if (count($obj) > 0) {
                    $id  = str_replace('ele_', '', $obj[0]->getName());
                    $id  = str_replace('[]', '', $id);
                }
            }
            $req         = false;
            $display_row = 1;
            if (isset($eleObjects[$id])) {
                $req         = $eleObjects[$id]->getVar('ele_req') ? true : false;
                $ele_type    = $eleObjects[$id]->getVar('ele_type');
                $display_row = (int)$eleObjects[$id]->getVar('ele_display_row');
            }

            $eles[] = [
                'caption'     => $caption,
                               'name' => $name,
                               'body' => $currElement->render(),
                             'hidden' => $currElement->isHidden(),
                           'required' => $req,
                        'display_row' => $display_row,
                'ele_type'    => $ele_type,
            ];
        }

        $js          = $formOutput->renderValidationJS();
        $isHiddenTxt = (Constants::FORM_HIDDEN == $this->getVar('form_order')) ? _MD_XFORMS_FORM_IS_HIDDEN : '';

        $assignArray = [
            'form_output'      => [
                'title'      => $formOutput->getTitle(),
                                                     'name' => $formOutput->getName(),
                                                   'action' => $formOutput->getAction(),
                                                   'method' => $formOutput->getMethod(),
                                                    'extra' => 'onsubmit="return xoopsFormValidate_' . $formOutput->getName() . '();"' . $formOutput->getExtra(),
                                               'javascript' => $js,
                'elements'   => $eles,
            ],
                                          'form_req_prefix' => $helper->getConfig('prefix'),
                                          'form_req_suffix' => $helper->getConfig('suffix'),
                                               'form_intro' => $this->getVar('form_intro'),
                                         'form_text_global' => $myts->displayTarea($helper->getConfig('global')),
                                           'form_is_hidden' => $isHiddenTxt,
                                          'xoops_pagetitle' => $this->getVar('form_title'),
            'form_edit_link'   => $this->getEditLinkInfo(),
        ];

        return $assignArray;
    }
}
