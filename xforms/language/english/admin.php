<?php
######### (liaise) version 1.0  additions #########
define("_AM_SAVE","Save");
define("_AM_COPIED","%s copy");
define("_AM_DBUPDATED","Database Updated Successfully!");
define("_AM_ELE_CREATE","Create a form element");
define("_AM_ELE_EDIT","Edit form element: %s");

define("_AM_ELE_CAPTION","Caption");
define("_AM_ELE_DEFAULT","Default value");
define("_AM_ELE_DETAIL","Detail");
define("_AM_ELE_REQ","Required");
define("_AM_ELE_ORDER","Order");
define("_AM_ELE_DISPLAY","Display");

define("_AM_ELE_TEXT","Text box");
define("_AM_ELE_TEXT_DESC","{UNAME} will print user name;<br />{EMAIL} will print user email");
define("_AM_ELE_TAREA","Text area");
define("_AM_ELE_SELECT","Selections");
define("_AM_ELE_CHECK","Check boxes");
define("_AM_ELE_RADIO","Radio buttons");
define("_AM_ELE_YN","Simple yes/no radio buttons");

define("_AM_ELE_SIZE","Size");
define("_AM_ELE_MAX_LENGTH","Maximum length");
define("_AM_ELE_ROWS","Rows");
define("_AM_ELE_COLS","Columns");
define("_AM_ELE_OPT","Options");
define("_AM_ELE_OPT_DESC","Tick the check boxes for selecting default values");
define("_AM_ELE_OPT_DESC1","<br />Only the first checked is used if multiple selection is not allowed");
define("_AM_ELE_OPT_DESC2","Select the default value by checking the radio buttons");
define("_AM_ELE_ADD_OPT","Add %s options");
define("_AM_ELE_ADD_OPT_SUBMIT","Add");
define("_AM_ELE_SELECTED","Selected");
define("_AM_ELE_CHECKED","Checked");
define("_AM_ELE_MULTIPLE","Allow multiple selections");

define("_AM_ELE_CONFIRM_DELETE","Are you sure you want to delete this form element?");

######### (liaise) version 1.1 #########
define("_AM_ELE_OTHER", 'For an option of "Other", put {OTHER|*number*} in one of the text boxes. e.g. {OTHER|30} generates a text box with 30 chars width.');

######### (liaise) version 1.2 additions #########
define("_AM_FORM_LISTING","Contact Form Listing");
define("_AM_FORM_ORDER","Display Order");
define("_AM_FORM_ORDER_DESC","0 = hide this form");
define("_AM_FORM_TITLE","Form Title");
define("_AM_FORM_PERM","Groups allowed to use this form");
define("_AM_FORM_SENDTO","Send to");
define("_AM_FORM_SENDTO_ADMIN","Site Admin email");
define("_AM_FORM_SEND_METHOD","Send method");
define("_AM_FORM_SEND_METHOD_DESC","Information cannot be sent via private message when the form is sent to "._AM_FORM_SENDTO_ADMIN." or sent by anonymous users");
define("_AM_FORM_SEND_METHOD_MAIL","Email");
define("_AM_FORM_SEND_METHOD_PM","Private message");
define("_AM_FORM_DELIMETER","Delimeter for check boxes and radio buttons");
define("_AM_FORM_DELIMETER_SPACE","White space");
define("_AM_FORM_DELIMETER_BR","Line break");
define("_AM_FORM_SUBMIT_TEXT","Text for submit button");
define("_AM_FORM_DESC","Form description");
define("_AM_FORM_DESC_DESC","Text to be displayed in the main page if more then one form is listed");
define("_AM_FORM_INTRO","Form introduction");
define("_AM_FORM_INTRO_DESC","Text to be displayed in form page itself");
define("_AM_FORM_WHERETO","URL to go after the form is submitted");
define("_AM_FORM_WHERETO_DESC","Leave blank for the home page of this site; {SITE_URL} will print ".XOOPS_URL);

define("_AM_FORM_ACTION_EDITFORM","Edit form settings");
define("_AM_FORM_ACTION_EDITELEMENT","Edit form elements");
define("_AM_FORM_ACTION_CLONE","Clone this form");

define("_AM_FORM_NEW","Create a new form");
define("_AM_FORM_EDIT","Edit form: %s");
define("_AM_FORM_CONFIRM_DELETE","Are you sure you want to delete this form and all its form elements?");

define("_AM_ID","ID");
define("_AM_ACTION","Action");
define("_AM_RESET_ORDER","Update Order");
define("_AM_SAVE_THEN_ELEMENTS","Save then edit elements");
define("_AM_SAVE_THEN_FORM","Save then edit form settings");
define("_AM_NOTHING_SELECTED","Nothing selected.");
define("_AM_GO_CREATE_FORM","You have to create a form first.");

define("_AM_ELEMENTS_OF_FORM","Form elements of %s");
define("_AM_ELE_APPLY_TO_FORM","Apply to form");
define("_AM_ELE_HTML","Plain text / HTML");

######### (liaise) version 1.23 additions #########
define("_AM_XOOPS_VERSION_WRONG","Version of XOOPS does not meet the system requirement. xforms may not work properly.");
define("_AM_ELE_UPLOADFILE","File upload");
define("_AM_ELE_UPLOADIMG","Image upload");
define("_AM_ELE_UPLOADIMG_MAXWIDTH","Maximum width (pixels)");
define("_AM_ELE_UPLOADIMG_MAXHEIGHT","Maximum height (pixels)");
define("_AM_ELE_UPLOAD_MAXSIZE","Maximum file size (bytes)");
define("_AM_ELE_UPLOAD_MAXSIZE_DESC","1k = 1024 bytes");
define("_AM_ELE_UPLOAD_DESC_SIZE_NOLIMIT","0 = no limit");
define("_AM_ELE_UPLOAD_ALLOWED_EXT","Allowed filename extensions");
define("_AM_ELE_UPLOAD_ALLOWED_EXT_DESC","Separate filename extensions with a |, case insensitive. e.g. 'jpg|jpeg|gif|png|tif|tiff'");
define("_AM_ELE_UPLOAD_ALLOWED_MIME","Allowed MIME types");
define("_AM_ELE_UPLOAD_ALLOWED_MIME_DESC","Separate MIME types with a |, case insensitive. e.g. 'image/jpeg|image/pjpeg|image/png|image/x-png|image/gif|image/tiff'");
define("_AM_ELE_UPLOAD_DESC_NOLIMIT","Leave blank for no limit (not recommended for security reasons)");
define("_AM_ELE_UPLOAD_SAVEAS","Save uploaded file to");
define("_AM_ELE_UPLOAD_SAVEAS_MAIL","Mail attachment");
define("_AM_ELE_UPLOAD_SAVEAS_FILE","Upload directory");

######### (xforms) version 1.0 additions ##########
define("_AM_IMPORT_SUCCES","xForms has succesfully imported all liaise forms.");
define("_AM_IMPORT_FAILED","xForms has failed importing all liaise forms.");
define("_AM_IMPORT_NFND","Liaise is not found on your system!");

//ModuleAdmin
define('_AM_XFORMS_MODULEADMIN_MISSING','Error: The ModuleAdmin class is missing. Please install the ModuleAdmin Class into /Frameworks (see /docs/readme.txt)');

// Text for Admin footer
//define("_AM_XFORMS_FOOTER","<div class='center smallsmall italic pad5'>xForms is maintained by the <a class='tooltip' rel='external' href='http://xoops.org/' title='Visit XOOPS Community'>XOOPS Community</a></div>");

//xForms 1.21 Defaults
define("_AM_ELE_YOUR_NAME",'Your Name');
define("_AM_ELE_YOUR_EMAIL",'Email address');
define("_AM_ELE_YOUR_COMMENTS",'Your comments');
