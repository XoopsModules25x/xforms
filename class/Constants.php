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
 */

/**
 * Interface \XoopsModules\Xforms\Constants
 */
interface Constants
{
    /**#@+
     * Constant definition
     */

    public const DISALLOW = 0;
// CONFIG displayicons
    public const DISPLAYICONS_ICON = 1;
    public const DISPLAYICONS_TEXT = 2;
    public const DISPLAYICONS_NO = 3;
// CONFIG submissions
    public const SUBMISSIONS_NONE     = 1;
    public const SUBMISSIONS_DOWNLOAD = 2;
    public const SUBMISSIONS_MIRROR = 3;
    public const SUBMISSIONS_BOTH = 4;
// CONFIG anonpost
    public const ANONPOST_NONE     = 1;
    public const ANONPOST_DOWNLOAD = 2;
    public const ANONPOST_MIRROR = 3;
    public const ANONPOST_BOTH = 4;
// CONFIG autoapprove
    public const AUTOAPPROVE_NONE     = 1;
    public const AUTOAPPROVE_DOWNLOAD = 2;
    public const AUTOAPPROVE_MIRROR = 3;
    public const AUTOAPPROVE_BOTH = 4;
    public const DEFAULT_ELEMENT_SIZE = 1;
    /**
     * List Block Sort Keys - must be columns in the dB
     * must match the number of entities in define('_MB_XFORMS_LIST_BLOCK_SORT_OPTS
     */
    public const LIST_BLOCK_SORT_KEYS = 'form_order,form_title';
    /**
     * default forms to show per page in lists
     */
    public const FORMS_PER_PAGE_DEFAULT = 10;
    /**
     * form - invalid ID
     */
    public const FORM_NOT_VALID = 0;
    /**
     * form Active
     */
    public const FORM_ACTIVE = 1;
    /**
     * form Inactive
     */
    public const FORM_INACTIVE = 0;
    /**
     * Allow Multi select
     */
    public const ALLOW_MULTI = 1;
    /**
     * Disallow Multi select
     */
    public const DISALLOW_MULTI = 0;
    /**
     * Allow HTML
     */
    public const ALLOW_HTML = 1;
    /**
     * Disallow HTML
     */
    public const DISALLOW_HTML = 0;
    /**
     * Element - invalid ID
     */
    public const ELE_NOT_VALID = 0;
    /**
     * Element - no
     */
    public const ELE_NO = 0;
    /**
     * Element - yes
     */
    public const ELE_YES = 1;
    /**
     * element - scheme both http & ftp
     */
    public const SCHEME_BOTH = 0;
    /**
     * element - scheme both http & ftp
     */
    public const SCHEME_HTTP = 1;
    /**
     * element - scheme both http & ftp
     */
    public const SCHEME_FTP = 2;
    /**
     * element - use current date
     */
    public const ELE_CURR = 1;
    /**
     * element - use other date
     */
    public const ELE_OTHER = 2;
    /**
     * element - set date
     */
    public const DATE_NONE = 0;
    /**
     * element - set to current date
     */
    public const DATE_CURRENT = 1;
    /**
     * element - set specific date
     */
    public const DATE_SPECIFIC = 2;
    /**
     * range default step size
     */
    public const ELE_DEFAULT_STEP = 1;
    /**
     * Element not checked
     */
    public const ELE_NOT_CHECKED = 0;
    /**
     * Element checked
     */
    public const ELE_CHECKED = 1;
    /**
     * form save in database
     */
    public const SAVE_IN_DB = 1;
    /**
     * form do not save in database
     */
    public const DO_NOT_SAVE_IN_DB = 0;
    /**
     * hidden form
     */
    public const FORM_HIDDEN = 0;
    /**
     * show the form list
     */
    public const FORM_LIST_SHOW = 1;
    /**
     * hide the form list
     */
    public const FORM_LIST_NO_SHOW = 0;
    /**
     * form display style - form (f)
     */
    public const FORM_DISPLAY_STYLE_FORM = 'f';
    /**
     * form display style - poll (p)
     */
    public const FORM_DISPLAY_STYLE_POLL = 'p';
    /**
     * Required (form element setting)
     */
    public const REQUIRED = 1;
    /**
     * indicates the form element is NOT required
     */
    public const ELEMENT_NOT_REQD = 0;
    /**
     * indicates the form element is required
     */
    public const ELEMENT_REQD = 1;
    /**
     * display element
     */
    public const ELEMENT_DISPLAY = 1;
    /**
     * do not display element
     */
    public const ELEMENT_NOT_DISPLAY = 0;
    /**
     * display element on two rows in form
     */
    public const DISPLAY_DOUBLE_ROW = 2;
    /**
     * display element on one row in form
     */
    public const DISPLAY_SINGLE_ROW = 1;
    /**
     * save uploaded file to mail
     */
    public const FILE_AS_MAIL = 0;
    /**
     * save uploaded file to file
     */
    public const FILE_AS_FILE = 1;
    /**
     * form text field is not an email
     */
    public const FIELD_IS_NOT_EMAIL = 0;
    /**
     * form text field is an email addr
     */
    public const FIELD_IS_EMAIL = 1;
    /**
     * indicates form results sent via email
     */
    public const SEND_METHOD_MAIL = 'e';
    /**
     * indicates form results sent via private message
     */
    public const SEND_METHOD_PM = 'p';
    /**
     * indicates form results will not be sent (none)
     */
    public const SEND_METHOD_NONE = 'n';
    /**
     * indicates a copy of form results will not be sent
     */
    public const SEND_NO_COPY = 0;
    /**
     * indicates a copy of form results will be sent
     */
    public const SEND_COPY = 1;
    /**
     * send copy to others
     */
    public const SEND_TO_OTHER = -1;
    /**
     * don't send copy
     */
    public const SEND_TO_NONE = 0;
    /**
     * save uploaded file as mail
     */
    public const UPLOAD_SAVEAS_ATTACHMENT = 0;
    /**
     * save uploaded file as attachment
     */
    public const UPLOAD_SAVEAS_FILE = 1;
    /**
     * Form is not a clone
     */
    public const FORM_NOT_CLONED = 0;
    /**
     * Form is a clone
     */
    public const FORM_CLONED = 1;
    /**
     * no delay XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_NONE = 0;
    /**
     * short XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_SHORT = 1;
    /**
     * medium XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_MEDIUM = 3;
    /**
     * long XOOPS redirect delay (in seconds)
     */
    public const REDIRECT_DELAY_LONG = 7;
    /**
     * linefeed delimiter
     */
    public const DELIMITER_BR = 'b';
    /**
     * white space delimiter
     */
    public const DELIMITER_SPACE = 's';
    /**
     * confirm not ok to take action
     */
    public const CONFIRM_NOT_OK = 0;
    /**
     * confirm ok to take action
     */
    public const CONFIRM_OK = 1;
    /**#@-*/
}
