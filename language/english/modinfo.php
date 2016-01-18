<?php
// The name of this module
define("_MI_XFORMS_NAME", "xForms");

// A brief description of this module
define("_MI_XFORMS_DESC", "Contact forms generator");

// admin/menu.php
define("_MI_XFORMS_ADMENU0", "Home");
define("_MI_XFORMS_ADMENU1", "Manage forms");
define("_MI_XFORMS_ADMENU2", "Create a new form");
define("_MI_XFORMS_ADMENU3", "Create form elements");
define("_MI_XFORMS_ADMENU4", "Form Report");
define("_MI_XFORMS_ADMENU5", "About");

//	template descriptions
define("_MI_XFORMS_TMPL_FORM_DESC", "Template for forms");
define("_MI_XFORMS_TMPL_ERROR_DESC", "Page to show when error occurs");

//	preferences
define("_MI_XFORMS_TEXT_WIDTH", "Default width of text boxes");
define("_MI_XFORMS_TEXT_MAX", "Default maximum length of text boxes");
define("_MI_XFORMS_TAREA_ROWS", "Default rows of text areas");
define("_MI_XFORMS_TAREA_COLS", "Default columns of text areas");
define("_MI_XFORMS_MAIL_CHARSET", "Text encoding for sending emails");
define("_MI_XFORMS_TMPL_MAIN_DESC", "Main page of xForms");
define("_MI_XFORMS_MOREINFO", "Send additional information along with the submitted data");
define("_MI_XFORMS_MOREINFO_USER", "User name and url to user info page");
define("_MI_XFORMS_MOREINFO_IP", "Submitter's IP address");
define("_MI_XFORMS_MOREINFO_AGENT", "Submitter's user agent (browser info)");
define("_MI_XFORMS_MOREINFO_FORM", "URL of the submitted form");
define("_MI_XFORMS_PREFIX", "Text prefix for required fields");
define("_MI_XFORMS_SUFFIX", "Text suffix for required fields");
define("_MI_XFORMS_INTRO", "Introduction text in main page");
define("_MI_XFORMS_GLOBAL", "Text to be displayed in every form page");
define("_MI_XFORMS_DEFAULT_TITLE", "Default Title Principal Page");

// preferences default values
define("_MI_XFORMS_MAIL_CHARSET_DESC", "Leave blank for " . _CHARSET);
define("_MI_XFORMS_INTRO_DEFAULT", "Feel free to contact us via the following means:");
define("_MI_XFORMS_GLOBAL_DEFAULT", "[b]* Required[/b]");
define("_MI_XFORMS_UPLOADDIR", "Physical path for storing uploaded files WITHOUT trailing slash");
define("_MI_XFORMS_UPLOADDIR_DESC", "All upload files will be stored here when a form is sent via private message");
define("_MI_XFORMS_CAPTCHA", "Use captcha in submit form?");
define("_MI_XFORMS_CAPTCHADSC", "Select <em>Yes</em> to use captcha in the submit form.<br />Default: <em>Yes</em>");
define("_MI_XFORMS_NOFORM", "Text showed when there are no forms visible to the current user");
define("_MI_XFORMS_NOFORM_DEFAULT", "Sorry, there are currently no forms (visible for you).");
define("_MI_XFORMS_SHOWFORMS", "Forms available on Home?");
define("_MI_XFORMS_SHOWFORMS_DESC", "Indicate whether you want to see the forms available to the user when no one indicated parameter. If you select No, the user will be sent to the home page of the site when not indicate a form parameter.");
define("_MI_XFORMS_DEFAULT_TITLE_DESC", "Forms Page");

define("_MI_xforms_ADMENU0","Home");
//1.22
define("_MI_XFORMS_ELE_SELECT_CTRY_DEFAULT","Select Default Country");
