<?php
######### (liaise) version 1.0  additions #########
// The name of this module
define("_MI_xforms_NAME","xForms");

// A brief description of this module
define("_MI_xforms_DESC","Contact forms generator");

// admin/menu.php
define("_MI_xforms_ADMENU1","Manage forms");
define("_MI_xforms_ADMENU2","Create a new form");

//	preferences
define("_MI_xforms_TEXT_WIDTH","Default width of text boxes");
define("_MI_xforms_TEXT_MAX","Default maximum length of text boxes");
define("_MI_xforms_TAREA_ROWS","Default rows of text areas");
define("_MI_xforms_TAREA_COLS","Default columns of text areas");

######### (liaise) version 1.1  additions #########
//	preferences
define("_MI_xforms_MAIL_CHARSET","Text encoding for sending emails");

//	template descriptions
define("_MI_xforms_TMPL_MAIN_DESC","Main page of xforms");
define("_MI_xforms_TMPL_ERROR_DESC","Page to show when error occurs");

######### (liaise) version 1.2 additions #########
//	template descriptions
define("_MI_xforms_TMPL_FORM_DESC","Template for forms");

//	preferences
define("_MI_xforms_MOREINFO","Send additional information along with the submitted data");
define("_MI_xforms_MOREINFO_USER","User name and url to user info page");
define("_MI_xforms_MOREINFO_IP","Submitter's IP address");
define("_MI_xforms_MOREINFO_AGENT","Submitter's user agent (browser info)");
define("_MI_xforms_MOREINFO_FORM","URL of the submitted form");
define("_MI_xforms_MAIL_CHARSET_DESC","Leave blank for "._CHARSET);
define("_MI_xforms_PREFIX","Text prefix for required fields");
define("_MI_xforms_SUFFIX","Text suffix for required fields");
define("_MI_xforms_INTRO","Introduction text in main page");
define("_MI_xforms_GLOBAL","Text to be displayed in every form page");

// admin/menu.php
define("_MI_xforms_ADMENU3","Create form elements");
define("_MI_xforms_ADMENU5","About");

######### (liaise) version 1.21 additions #########
// preferences default values
define("_MI_xforms_INTRO_DEFAULT","Feel free to contact us via the following means:");
define("_MI_xforms_GLOBAL_DEFAULT","[b]* Required[/b]");

######### (liaise) version 1.23 additions #########
define("_MI_xforms_UPLOADDIR","Physical path for storing uploaded files WITHOUT trailing slash");
define("_MI_xforms_UPLOADDIR_DESC","All upload files will be stored here when a form is sent via private message");

######### (xforms) version 1.0 additions ##########
define("_MI_xforms_CAPTCHA","Use captcha in submit form?");
define("_MI_xforms_CAPTCHADSC","Select <em>Yes</em> to use captcha in the submit form.<br />Default: <em>Yes</em>");
define("_MI_xforms_ADMENU4","Import from Liaise");

######### (xforms) version 1.0.0.1 additions ##########
define("_MI_xforms_NOFORM","Text showed when there are no forms visible to the current user");
define("_MI_xforms_NOFORM_DEFAULT","Sorry, there are currently no forms (visible for you).");

define("_MI_xforms_ADMENU0","Home");
?>