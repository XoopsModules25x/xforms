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
 * @category        Module
 * @package         xforms
 * @author          XOOPS Module Development Team
 * @copyright       {@see http://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             http://xoops.org XOOPS
 * @since           1.30
 */
use Xmf\Module\Admin;
use Xmf\Module\Helper;
use Xmf\Module\Helper\Permission;

//defined('XFORMS_ROOT_PATH') || exit('Restricted access');

if (!interface_exists('XformsConstants')) {
    $xformsHelper = Helper::getHelper(basename(dirname(__DIR__)));
    require_once $xformsHelper->path('/class/constants.php');
}

/**
 * Class XformsForms
 */
class XformsForms extends XoopsObject
{
    /**
     * this module's directory
     */
    protected $dirname;

    /**
     * XformsForms constructor.
     */
    public function __construct()
    {
        /**@todo set var options for form_save_db, form_send_method, form_delimiter, form_display_style, form_active
         * for example
         * $this->initVar('form_save_db', XOBJ_DTYPE_INT, XformsConstants::SAVE_IN_DB, true, 1, XformsConstants::SAVE_IN_DB|XformsConstants::DO_NOT_SAVE_IN_DB);
         * $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, XformsConstants::SEND_METHOD_MAIL, true, 1, XformsConstants::SEND_METHOD_MAIL|XformsConstants::SEND_METHOD_PM|XformsConstants::SEND_METHOD_NONE);
         * $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, XformsConstants::DELIMITER_SPACE, true, 1, XformsConstants::DELIMITER_SPACE|XformsConstants::DELIMITER_BR);
         * $this->initVar('form_display_style', XOBJ_DTYPE_TXTBOX, XformsConstants::FORM_DISPLAY_STYLE_FORM, true, 1, XformsConstants::FORM_DISPLAY_STYLE_FORM|XformsConstants::FORM_DISPLAY_STYLE_POLL);
         * $this->initVar('form_active', XOBJ_DTYPE_INT, XformsConstants::FORM_ACTIVE, true, XformsConstants::FORM_ACTIVE|XformsConstants::FORM_INACTIVE);
         */
        parent::__construct();
        $this->initVar('form_id', XOBJ_DTYPE_INT);
        $this->initVar('form_save_db', XOBJ_DTYPE_INT, XformsConstants::SAVE_IN_DB, true, 1);
        $this->initVar('form_send_method', XOBJ_DTYPE_TXTBOX, XformsConstants::SEND_METHOD_MAIL, true, 1);
        $this->initVar('form_send_to_group', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('form_send_to_other', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('form_send_copy', XOBJ_DTYPE_INT);
        $this->initVar('form_order', XOBJ_DTYPE_INT, 1, false, 3);
        $this->initVar('form_delimiter', XOBJ_DTYPE_TXTBOX, XformsConstants::DELIMITER_SPACE, true, 1);
        $this->initVar('form_title', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('form_submit_text', XOBJ_DTYPE_TXTBOX, _SUBMIT, true, 50);
        $this->initVar('form_desc', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_intro', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_header', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_footer', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_uheader', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_email_ufooter', XOBJ_DTYPE_TXTAREA);
        $this->initVar('form_whereto', XOBJ_DTYPE_TXTBOX);
        $this->initVar('form_display_style', XOBJ_DTYPE_TXTBOX, XformsConstants::FORM_DISPLAY_STYLE_FORM, true, 1);
        $this->initVar('form_begin', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('form_end', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('form_active', XOBJ_DTYPE_INT, XformsConstants::FORM_ACTIVE, true);

        $this->dirname = basename(dirname(__DIR__));
    }

    /**
     * Check to see if form is active
     *
     * @return bool
     */
    public function isActive()
    {
        $now    = time();
        $fbegin = (int)$this->getVar('form_begin');
        $fend   = (int)$this->getVar('form_end');
        $retVal = true;
        if (XformsConstants::FORM_INACTIVE == $this->getVar('form_active')) {
            $retVal = false;
        }
        if ((0 != $fbegin && $fbegin > $now) || (0 != $fend && $fend < $now)) {
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
        $xformsHelper = Helper::getHelper($this->dirname);
        if ($xformsHelper->isUserAdmin()) {
            $editLink = array(
                'location'      => $xformsHelper->url('admin/main.php') . '?op=edit&form_id=' . $this->getVar('form_id'),
                'target'        => '_self',
                'icon_location' => Admin::iconUrl('edit.png', 16),
                'icon_title'    => _AM_XFORMS_ACTION_EDITFORM,
                'icon_alt'      => _AM_XFORMS_ACTION_EDITFORM
            );
        } else {
            $editLink = '';
        }

        return $editLink;
    }

    /**
     * Render the Form
     *
     * @since v2.00 ALPHA 2
     * @return boolean|array false on error|array containing variables for template
     */
    public function render()
    {
        $xformsHelper = Helper::getHelper($this->dirname);
        $myts         = MyTextSanitizer::getInstance();

        if ((XformsConstants::FORM_HIDDEN == $this->getVar('form_order')) && (!$xformsHelper->isUserAdmin())) {
            $this->setErrors(_NOPERM);

            return false;
        }

        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        include_once $xformsHelper->path('class/elementrenderer.php');
        $xformsEleHandler = $xformsHelper->getHandler('element');

        $xformsHelper->loadLanguage('admin');
        $xformsHelper->loadLanguage('main');

        // Read form elements
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('form_id', $this->getVar('form_id')));
        $criteria->add(new Criteria('ele_display', XformsConstants::ELEMENT_DISPLAY));
        $criteria->setSort('ele_order');
        $criteria->setOrder('ASC');
        $eleObjects = $xformsEleHandler->getObjects($criteria, true);

        if (empty($eleObjects)) { // this form doesn't have any elements
            $this->setErrors(sprintf(_MD_XFORMS_ELE_ERR, $this->getVar('form_title'), 's'));

            return false;
        }

        $formOutput = new XoopsThemeForm($this->getVar('form_title'), 'xforms_' . $this->getVar('form_id'), $xformsHelper->url('index.php'), 'post', true);
        $eleCount   = 1;
        $multipart  = false;
        foreach ($eleObjects as $elementObj) {
            $eleRenderer = new XformsElementRenderer($elementObj);
            $formEle     = $eleRenderer->constructElement(false, $this->getVar('form_delimiter'));
            $req         = (XformsConstants::ELEMENT_REQD != $elementObj->getVar('ele_req')) ? false : true;
            if (1 === $eleCount) {
                $formEle->setExtra('autofocus');  //give the 1st element focus on form load
            }
            $formEle->setExtra('tabindex="' . $eleCount++ . '"'); // allow tabbing through fields on form
            if (in_array($elementObj->getVar('ele_type'), array('upload', 'uploadimg'))) {
                $multipart = true; // will be a multipart form
            }
            $formOutput->addElement($formEle, $req);
            unset($formEle);
        }

        if ($multipart) { // set multipart attribute for form
            $formOutput->setExtra('enctype="multipart/form-data"');
        }
        $formOutput->addElement(new XoopsFormHidden('form_id', $this->getVar('form_id')));

        // load captcha
        xoops_load('formCaptcha', XFORMS_DIRNAME);
        $xfFormCaptcha = new XformsFormCaptcha();
        $formOutput->addElement($xfFormCaptcha);

        $subButton = new XoopsFormButton('', 'submit', $this->getVar('form_submit_text'), 'submit');
        $subButton->setExtra('tabindex="' . $eleCount++ . '"'); // allow tabbing to the Submit button too
        $formOutput->addElement($subButton, 1);

        $eles = array();
        foreach ($formOutput->getElements() as $currElement) {
            $id      = $req = $name = $ele_type = false;
            $name    = $currElement->getName();
            $caption = $currElement->getCaption();
            if (!empty($name)) {
                $id = str_replace('ele_', '', $currElement->getName());
            } elseif (method_exists($currElement, 'getElements')) {
                //            } elseif (method_exists($currElement, 'getElements') && is_callable('getElements')) {
                $obj = $currElement->getElements();
                if (count($obj) > 0) {
                    $id = str_replace('ele_', '', $obj[0]->getName());
                    $id = str_replace('[]', '', $id);
                }
            }
            $req         = false;
            $display_row = 1;
            if (isset($eleObjects[$id])) {
                $req         = $eleObjects[$id]->getVar('ele_req') ? true : false;
                $ele_type    = $eleObjects[$id]->getVar('ele_type');
                $display_row = (int)$eleObjects[$id]->getVar('ele_display_row');
            }

            $eles[] = array(
                'caption'     => $caption,
                'name'        => $name,
                'body'        => $currElement->render(),
                'hidden'      => $currElement->isHidden(),
                'required'    => $req,
                'display_row' => $display_row,
                'ele_type'    => $ele_type
            );
        }

        $js          = $formOutput->renderValidationJS();
        $isHiddenTxt = (XformsConstants::FORM_HIDDEN == $this->getVar('form_order')) ? _MD_XFORMS_FORM_IS_HIDDEN : '';

        $assignArray = array(
            'form_output'      => array(
                'title'      => $formOutput->getTitle(),
                'name'       => $formOutput->getName(),
                'action'     => $formOutput->getAction(),
                'method'     => $formOutput->getMethod(),
                'extra'      => 'onsubmit="return xoopsFormValidate_' . $formOutput->getName() . '();"' . $formOutput->getExtra(),
                'javascript' => $js,
                'elements'   => $eles
            ),
            'form_req_prefix'  => $xformsHelper->getConfig('prefix'),
            'form_req_suffix'  => $xformsHelper->getConfig('suffix'),
            'form_intro'       => $this->getVar('form_intro'),
            'form_text_global' => $myts->displayTarea($xformsHelper->getConfig('global')),
            'form_is_hidden'   => $isHiddenTxt,
            'xoops_pagetitle'  => $this->getVar('form_title'),
            'form_edit_link'   => $this->getEditLinkInfo()

        );

        return $assignArray;
    }
}

/**
 * Class XformsFormsHandler
 */
class XformsFormsHandler extends XoopsPersistableObjectHandler
{
    public $db;
    public $db_table;
    public $perm_name = 'xforms_form_access';
    public $obj_class = 'XformsForms';
    protected $dirname;

    /**
     * @param $db XoopsDatabase to use for the form
     */
    public function __construct(XoopsDatabase $db = null)
    {
        $this->db       = $db;
        $this->db_table = $this->db->prefix('xforms_form');
        $this->dirname  = basename(dirname(__DIR__));
        parent::__construct($db, 'xforms_form', 'XformsForms', 'form_id', 'form_title');
    }

    /**
     * Set the form inactive and update it in the database
     * @param XformsForms $form
     * @param bool        $force
     *
     * @return bool true on success
     */
    public function setInactive(XformsForms $form, $force = true)
    {
        $ret = true;
        if (XformsConstants::FORM_INACTIVE != $form->getVar('form_active')) {
            $form->setVar('form_active', XformsConstants::FORM_INACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(sprintf(_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno(), $sql));
                $ret = false;
            }
        }

        return $ret ? true : false;
    }

    /**
     * Set the form active and update it in the database
     * @param XformsForms $form
     * @param bool        $force
     *
     * @return bool true on success
     */
    public function setActive(XformsForms $form, $force = true)
    {
        $ret = true;
        if (XformsConstants::FORM_ACTIVE != $form->getVar('form_active')) {
            $form->setVar('form_active', XformsConstants::FORM_ACTIVE);
            $result = $this->insert($form, (bool)$force);
            if (!$result) {
                $form->setErrors(sprintf(_MD_XFORMS_ERR_DB_INSERT, $this->db->error(), $this->db->errno(), $sql));
                $ret = false;
            }
        }

        return $ret ? true : false;
    }

    /**
     * @param int $formId
     *
     * @return bool
     */
    public function deleteFormPermissions($formId)
    {
        $permHelper = new Permission($this->dirname);
        $ret        = $permHelper->deletePermissionForItem($this->perm_name, (int)$formId);

        //        $ret = $GLOBALS['modulepermHandler']->deleteByModule($GLOBALS['xoopsModule']->getVar('mid'), $this->perm_name, (int)$formId);
        return $ret;
    }

    /**
     * @param int   $formId
     * @param array $groupIds an array of integer group ids to insert
     *
     * @return bool true if success | false if setting any group perm fails
     */
    public function insertFormPermissions($formId, $groupIds)
    {
        $permHelper = new Permission($this->dirname);

        $groupIds = (array)$groupIds; //make sure it's an array
        $groupIds = array_map('intval', $groupIds); //make sure all array elements are integers
        $ret      = $permHelper->savePermissionForItem($this->perm_name, (int)$formId, $groupIds);

        /*
                $ret = true;
                foreach ($groupIds as $id) {
                    $status = $GLOBALS['modulepermHandler']->addRight($this->perm_name, (int)$formId, $id, $GLOBALS['xoopsModule']->getVar('mid'));
                    $ret = $ret & ($status) ? true : false;
                }
        */

        return $ret;
    }

    /**
     * Get the forms for this user (permissions aware)
     *
     * @return array|bool
     */
    public function getPermittedForms()
    {
        $groups   = (isset($GLOBALS['xoopsUser'])
                     && $GLOBALS['xoopsUser'] instanceof XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $criteria = new CriteriaCompo();
        $now      = time();
        $criteria->add(new Criteria('form_active', XformsConstants::FORM_INACTIVE, '<>'));
        $criteria->add(new Criteria('form_order', XformsConstants::FORM_HIDDEN, '>'));
        $criteria->setSort('form_order');
        $criteria->setOrder('ASC');
        if ($forms =& $this->getAll($criteria)) {
            $ret = array();
            foreach ($forms as $f) {
                if ($f->isActive()) {
                    $permHelper = new Permission($this->dirname);
                    if ($permHelper->checkPermission($this->perm_name, $f->getVar('form_id'))) {
                        //                    if (false !== $GLOBALS['modulepermHandler']->checkRight($this->perm_name, $f->getVar('form_id'), $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
                        $ret[] = $f;
                    }
                }
                unset($f);
            }

            return $ret;
        }

        return false;
    }

    /**
     * @param $formId
     *
     * @return bool
     */
    public function getSingleFormPermission($formId)
    {
        $permHelper = new Permission($this->dirname);

        return $permHelper->checkPermission($this->perm_name, (int)$formId);
        /*
                $groups = (isset($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser'] instanceof XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : XOOPS_GROUP_ANONYMOUS;
                if (false != $GLOBALS['modulepermHandler']->checkRight($this->perm_name, (int)$formId, $groups, $GLOBALS['xoopsModule']->getVar('mid'))) {
                    return true;
                }
                return false;
        */
    }
}
