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
 * @copyright       {@see https://xoops.org 2001-2016 XOOPS Project}
 * @license         {@see http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @see             https://xoops.org XOOPS
 * @since           1.30
 */

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Interface XformsConstants
 */
interface XformsConstants
{
    /**#@+
     * Constant definition
     */

    const DISALLOW = 0;

    // CONFIG displayicons
    const DISPLAYICONS_ICON = 1;
    const DISPLAYICONS_TEXT = 2;
    const DISPLAYICONS_NO = 3;

    // CONFIG submissions
    const SUBMISSIONS_NONE = 1;
    const SUBMISSIONS_DOWNLOAD = 2;
    const SUBMISSIONS_MIRROR = 3;
    const SUBMISSIONS_BOTH = 4;

    // CONFIG anonpost
    const ANONPOST_NONE = 1;
    const ANONPOST_DOWNLOAD = 2;
    const ANONPOST_MIRROR = 3;
    const ANONPOST_BOTH = 4;

    // CONFIG autoapprove
    const AUTOAPPROVE_NONE = 1;
    const AUTOAPPROVE_DOWNLOAD = 2;
    const AUTOAPPROVE_MIRROR = 3;
    const AUTOAPPROVE_BOTH = 4;

    const DEFAULT_ELEMENT_SIZE = 1;

    /**
     * List Block Sort Keys - must be columns in the dB
     * must match the number of entities in define('_MB_XFORMS_LIST_BLOCK_SORT_OPTS
     */
    const LIST_BLOCK_SORT_KEYS = 'form_order,form_title';
    /**
     * default forms to show per page in lists
     */
    const FORMS_PER_PAGE_DEFAULT = 10;
    /**
     * form Active
     */
    const FORM_ACTIVE = 1;
    /**
     * form Inactive
     */
    const FORM_INACTIVE = 0;
    /**
     * Allow Multi select
     */
    const ALLOW_MULTI = 1;
    /**
     * Disallow Multi select
     */
    const DISALLOW_MULTI = 0;
    /**
     * Allow HTML
     */
    const ALLOW_HTML = 1;
    /**
     * Disallow HTML
     */
    const DISALLOW_HTML = 0;
    /**
     * Element - no
     */
    const ELE_NO = 0;
    /**
     * Element - yes
     */
    const ELE_YES = 1;
    /**
     * element - use current date
     */
    const ELE_CURR = 1;
    /**
     * element - use other date
     */
    const ELE_OTHER = 2;
    /**
     * range default step size
     */
    const ELE_DEFAULT_STEP = 1;
    /**
     * form save in database
     */
    const SAVE_IN_DB = 1;
    /**
     * form do not save in database
     */
    const DO_NOT_SAVE_IN_DB = 0;
    /**
     * hidden form
     */
    const FORM_HIDDEN = 0;
    /**
     * show the form list
     */
    const FORM_LIST_SHOW = 1;
    /**
     * hide the form list
     */
    const FORM_LIST_NO_SHOW = 0;
    /**
     * form display style - form (f)
     */
    const FORM_DISPLAY_STYLE_FORM = 'f';
    /**
     * form display style - poll (p)
     */
    const FORM_DISPLAY_STYLE_POLL = 'p';
    /**
     * Required (form element setting)
     */
    const REQUIRED = 1;
    /**
     * indicates the form element is NOT required
     */
    const ELEMENT_NOT_REQD = 0;
    /**
     * indicates the form element is required
     */
    const ELEMENT_REQD = 1;
    /**
     * display element
     */
    const ELEMENT_DISPLAY = 1;
    /**
     * do not display element
     */
    const ELEMENT_NOT_DISPLAY = 0;
    /**
     * display element on two rows in form
     */
    const DISPLAY_DOUBLE_ROW = 2;
    /**
     * display element on one row in form
     */
    const DISPLAY_SINGLE_ROW = 1;
    /**
     * indicates form results sent via email
     */
    const SEND_METHOD_MAIL = 'e';
    /**
     * indicates form results sent via private message
     */
    const SEND_METHOD_PM = 'p';
    /**
     * indicates form results will not be sent (none)
     */
    const SEND_METHOD_NONE = 'n';
    /**
     * indicates a copy of form results will not be sent
     */
    const SEND_NO_COPY = 0;
    /**
     * indicates a copy of form results will be sent
     */
    const SEND_COPY = 1;
    /**
     * send copy to others
     */
    const SEND_TO_OTHER = -1;
    /**
     * don't send copy
     */
    const SEND_TO_NONE = 0;
    /**
     * save uploaded file as mail
     */
    const UPLOAD_SAVEAS_ATTACHMENT = 0;
    /**
     * save uploaded file as attachment
     */
    const UPLOAD_SAVEAS_FILE = 1;
    /**
     * no delay XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_NONE = 0;
    /**
     * short XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_SHORT = 1;
    /**
     * medium XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_MEDIUM = 3;
    /**
     * long XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_LONG = 7;
    /**
     * use captcha settings inherited from XOOPS
     */
    const CAPTCHA_INHERIT = 0;
    /**
     * use captcha for ANON users only
     */
    const CAPTCHA_ANON_ONLY = 1;
    /**
     * use captcha for everyone
     */
    const CAPTCHA_EVERYONE = 2;
    /**
     * don't use captcha
     */
    const CAPTCHA_NONE = 3;
    /**
     * linefeed delimiter
     */
    const DELIMITER_BR = 'b';
    /**
     * white space delimiter
     */
    const DELIMITER_SPACE = 's';
    /**
     * confirm not ok to take action
     */
    const CONFIRM_NOT_OK = 0;
    /**
     * confirm ok to take action
     */
    const CONFIRM_OK = 1;
    /**#@-*/
}
