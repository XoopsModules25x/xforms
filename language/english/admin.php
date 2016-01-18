<?php
define("_AM_XFORMS_SAVE", "Save");
define("_AM_XFORMS_COPIED", "%s copy");
define("_AM_XFORMS_DBUPDATED", "Database Updated Successfully!");
define("_AM_XFORMS_ELE_CREATE", "Create a form element");
define("_AM_XFORMS_ELE_EDIT", "Edit form element: %s");

define("_AM_XFORMS_ELE_CAPTION", "Caption");
define("_AM_XFORMS_ELE_DEFAULT", "Default value");
define("_AM_XFORMS_ELE_DETAIL", "Detail");
define("_AM_XFORMS_ELE_REQ", "Required");
define("_AM_XFORMS_ELE_ORDER", "Order");
define("_AM_XFORMS_ELE_DISPLAY_ROW", "2 Rows");
define("_AM_XFORMS_ELE_DISPLAY_ROW_DESC", "Indicate whether to display the item in the same row of the title or in the next row.");
define("_AM_XFORMS_ELE_DISPLAY", "Display");
define("_AM_XFORMS_ELE_CONTAINS_EMAIL", "Contains email?");
define("_AM_XFORMS_ELE_CONTAINS_EMAIL_DESC", "Indicate whether this text box enter the e-mail user, this may send a copy of the form if you select this option in the configuration of the form.");

define("_AM_XFORMS_ELE_TEXT", "Text box");
define("_AM_XFORMS_ELE_TEXT_DESC", "Enter a default value, you can add other system values by selecting the drop down list");
define("_AM_XFORMS_ELE_TEXT_ADD_DEFAULT", "Add Value");
define("_AM_XFORMS_ELE_TEXT_ADD_DEFAULT_SEL", "-- Select to add --");
define("_AM_XFORMS_ELE_TAREA", "Text area");
define("_AM_XFORMS_ELE_SELECT", "Selections");
define("_AM_XFORMS_ELE_CHECK", "Check boxes");
define("_AM_XFORMS_ELE_RADIO", "Radio buttons");
define("_AM_XFORMS_ELE_YN", "Simple yes/no radio buttons");

define("_AM_XFORMS_ELE_SIZE", "Size");
define("_AM_XFORMS_ELE_MAX_LENGTH", "Maximum length");
define("_AM_XFORMS_ELE_ROWS", "Rows");
define("_AM_XFORMS_ELE_COLS", "Columns");
define("_AM_XFORMS_ELE_OPT", "Options");
define("_AM_XFORMS_ELE_OPT_DESC", "Tick the check boxes for selecting default values");
define("_AM_XFORMS_ELE_OPT_DESC1", "<br />Only the first checked is used if multiple selection is not allowed");
define("_AM_XFORMS_ELE_OPT_DESC2", "Select the default value by checking the radio buttons");
define("_AM_XFORMS_ELE_ADD_OPT", "Add %s options");
define("_AM_XFORMS_ELE_ADD_OPT_SUBMIT", "Add");
define("_AM_XFORMS_ELE_SELECTED", "Selected");
define("_AM_XFORMS_ELE_CHECKED", "Checked");
define("_AM_XFORMS_ELE_MULTIPLE", "Allow multiple selections");

define("_AM_XFORMS_ELE_CONFIRM_DELETE", "Are you sure you want to delete this form element?");
define("_AM_XFORMS_ELE_OTHER", 'For an option of "Other", put {OTHER|*number*} in one of the text boxes. e.g. {OTHER|30} generates a text box with 30 chars width.');

define("_AM_XFORMS_BYID", "Form by ID");
define("_AM_XFORMS_ENTER_ID", "Enter form ID");
define("_AM_XFORMS_SHOW_REPORT", "Show Report");
define("_AM_XFORMS_FORM_NOTSAVE", "Data is not saved. Check for configuration.");
define("_AM_XFORMS_FORM_NOTEXISTS", "No se encuentra el formulario indicado");

define("_AM_XFORMS_RPT_USER", "User");
define("_AM_XFORMS_RPT_DATETIME", "Date/Time");
define("_AM_XFORMS_RPT_IP", "IP");
define("_AM_XFORMS_RPT_QUESTION", "Question");
define("_AM_XFORMS_RPT_ANSWER", "Answer");
define("_AM_XFORMS_RPT_NODATA", "No saved data for the form");
define("_AM_XFORMS_RPT_EXPORT_V", "Vertical Export");
define("_AM_XFORMS_RPT_EXPORT_H", "Horizontal Export");

define("_AM_XFORMS_LISTING", "Contact Form Listing");
define("_AM_XFORMS_ORDER", "Display Order");
define("_AM_XFORMS_ORDER_DESC", "0 = hide this form");
define("_AM_XFORMS_STATUS", "Status");
define("_AM_XFORMS_STATUS_ACTIVE", "Active");
define("_AM_XFORMS_STATUS_INACTIVE", "Inactive");
define("_AM_XFORMS_STATUS_EXPIRED", "Expired");
define("_AM_XFORMS_TITLE", "Form Title");
define("_AM_XFORMS_PERM", "Groups allowed to use this form");
define("_AM_XFORMS_SAVE_DB", "Save on Database");
define("_AM_XFORMS_SAVE_DB_YES", "Save");
define("_AM_XFORMS_SAVE_DB_NO", "Don't save");
define("_AM_XFORMS_SAVE_DB_DESC", "Indicate whether to store the form data sent by users in the database");
define("_AM_XFORMS_SENDTO", "Send to");
define("_AM_XFORMS_SENDTO_ADMIN", "Site Admin email");
define("_AM_XFORMS_SENDTO_OTHER", "Defined e-Mails");
define("_AM_XFORMS_SENDTO_OTHER_EMAILS", "Send to defined e-mails");
define("_AM_XFORMS_SENDTO_OTHER_DESC", "Define the e-mails to send the form, separating each one by ; (semicolon)");
define("_AM_XFORMS_SEND_METHOD", "Send method");
define("_AM_XFORMS_SEND_METHOD_DESC", "Information cannot be sent via private message when the form is sent to " . _AM_XFORMS_SENDTO_ADMIN . " or sent by anonymous users");
define("_AM_XFORMS_SEND_METHOD_MAIL", "Email");
define("_AM_XFORMS_SEND_METHOD_PM", "Private message");
define("_AM_XFORMS_SEND_METHOD_NO", "Don't send");
define("_AM_XFORMS_SEND_COPY", "Send copy to user?");
define("_AM_XFORMS_SEND_COPY_DESC", "Indicate whether to send copy of form to the user. If the user is registered, will be sent to your personal email, if not, you must add an element in the form to request e-mail.");
define("_AM_XFORMS_EMAIL_HEADER", "Emails header text");
define("_AM_XFORMS_EMAIL_HEADER_DESC", "Text to be inserted at the beginning of the emails prior to the data entered by users.");
define("_AM_XFORMS_EMAIL_FOOTER", "Emails footer text");
define("_AM_XFORMS_EMAIL_FOOTER_DESC", "Text to be inserted at the end of emails sent.");
define("_AM_XFORMS_EMAIL_UHEADER", "Emails header text copy");
define("_AM_XFORMS_EMAIL_UHEADER_DESC", "Text to be inserted at the beginning of the emails, copy for user, prior to the data entered by users. (Only if option 'Send copy to user' is selected)");
define("_AM_XFORMS_EMAIL_UFOOTER", "Emails footer text copy");
define("_AM_XFORMS_EMAIL_UFOOTER_DESC", "Text to be inserted at the end of emails sent, copy for user. (Only if option 'Send copy to user' is selected)");
define("_AM_XFORMS_DELIMETER", "Delimeter for check boxes and radio buttons");
define("_AM_XFORMS_DELIMETER_SPACE", "White space");
define("_AM_XFORMS_DELIMETER_BR", "Line break");
define("_AM_XFORMS_SUBMIT_TEXT", "Text for submit button");
define("_AM_XFORMS_DESC", "Form description");
define("_AM_XFORMS_DESC_DESC", "Text to be displayed in the main page if more then one form is listed");
define("_AM_XFORMS_INTRO", "Form introduction");
define("_AM_XFORMS_INTRO_DESC", "Text to be displayed in form page itself");
define("_AM_XFORMS_WHERETO", "URL to go after the form is submitted");
define("_AM_XFORMS_WHERETO_DESC", "Leave blank for the home page of this site; {SITE_URL} will print " . XOOPS_URL);
define("_AM_XFORMS_DISPLAY_STYLE", "Show as");
define("_AM_XFORMS_DISPLAY_STYLE_FORM", "Form");
define("_AM_XFORMS_DISPLAY_STYLE_POLL", "Poll");
define("_AM_XFORMS_DISPLAY_STYLE_DESC", "Type of display for form and options.");
define("_AM_XFORMS_BEGIN", "Date and Time to begin");
define("_AM_XFORMS_END", "Date and Time to end");
define("_AM_XFORMS_ACTIVE", "Active Form?");
define("_AM_XFORMS_ACTIVE_DESC", "It was also verified that the form is active on the dates indicated");
define("_AM_XFORMS_DEFINE_BEGIN", "Define begin date");
define("_AM_XFORMS_DEFINE_BEGIN_DESC", "Select Yes to define begin date in the next control, No to ingnore.");
define("_AM_XFORMS_DEFINE_END", "Define expire date");
define("_AM_XFORMS_DEFINE_END_DESC", "Select Yes to define expire date in the next control, No to ingnore.");

define("_AM_XFORMS_REPORT_FORM", "Form Report");

define("_AM_XFORMS_DEFAULT_ELE_YOURNAME", "Your name");
define("_AM_XFORMS_DEFAULT_ELE_YOUREMAIL", "Your e-mail");
define("_AM_XFORMS_DEFAULT_ELE_COMMENTS", "Your comments");

define("_AM_XFORMS_ACTION_EDITFORM", "Edit form settings");
define("_AM_XFORMS_ACTION_EDITELEMENT", "Edit form elements");
define("_AM_XFORMS_ACTION_CLONE", "Clone this form");
define("_AM_XFORMS_ACTION_REPORT", "Generate report of form");
define("_AM_XFORMS_ACTION_INACTIVE", "Deactivate form");

define("_AM_XFORMS_NEW", "Create a new form");
define("_AM_XFORMS_EDIT", "Edit form: %s");
define("_AM_XFORMS_CONFIRM_DELETE", "Are you sure you want to delete this form and all its form elements?");
define("_AM_XFORMS_CONFIRM_INACTIVE", "Are you sure you want to deactivate this form?");

define("_AM_XFORMS_ID", "ID");
define("_AM_XFORMS_ACTION", "Action");
define("_AM_XFORMS_RESET_ORDER", "Update Order");
define("_AM_XFORMS_SHOW_ALL_FORMS", "Show Inactive Forms");
define("_AM_XFORMS_SHOW_NORMAL_FORMS", "Hide Inactive Forms");
define("_AM_XFORMS_SAVE_THEN_ELEMENTS", "Save then edit elements");
define("_AM_XFORMS_SAVE_THEN_FORM", "Save then edit form settings");
define("_AM_XFORMS_NOTHING_SELECTED", "Nothing selected.");
define("_AM_XFORMS_GO_CREATE_FORM", "You have to create a form first.");
define("_AM_XFORMS_NOTHING_SAVESENT", "You must indicate whether the form is saved or sent.");

define("_AM_XFORMS_ELEMENTS_OF_FORM", "Form elements of %s");
define("_AM_XFORMS_ELE_APPLY_TO_FORM", "Apply to form");
define("_AM_XFORMS_ELE_HTML", "Plain text / HTML");

define("_AM_XFORMS_XOOPS_VERSION_WRONG", "Version of XOOPS does not meet the system requirement. xForms may not work properly.");
define("_AM_XFORMS_ELE_UPLOADFILE", "File upload");
define("_AM_XFORMS_ELE_UPLOADIMG", "Image upload");
define("_AM_XFORMS_ELE_UPLOADIMG_MAXWIDTH", "Maximum width (pixels)");
define("_AM_XFORMS_ELE_UPLOADIMG_MAXHEIGHT", "Maximum height (pixels)");
define("_AM_XFORMS_ELE_UPLOAD_MAXSIZE", "Maximum file size (bytes)");
define("_AM_XFORMS_ELE_UPLOAD_MAXSIZE_DESC", "1k = 1024 bytes");
define("_AM_XFORMS_ELE_UPLOAD_DESC_SIZE_NOLIMIT", "0 = no limit");
define("_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT", "Allowed filename extensions");
define("_AM_XFORMS_ELE_UPLOAD_ALLOWED_EXT_DESC", "Separate filename extensions with a |, case insensitive. e.g. 'jpg|jpeg|gif|png|tif|tiff'");
define("_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME", "Allowed MIME types");
define("_AM_XFORMS_ELE_UPLOAD_ALLOWED_MIME_DESC", "Separate MIME types with a |, case insensitive. e.g. 'image/jpeg|image/pjpeg|image/png|image/x-png|image/gif|image/tiff'");
define("_AM_XFORMS_ELE_UPLOAD_DESC_NOLIMIT", "Leave blank for no limit (not recommended for security reasons)");
define("_AM_XFORMS_ELE_UPLOAD_SAVEAS", "Save uploaded file to");
define("_AM_XFORMS_ELE_UPLOAD_SAVEAS_MAIL", "Mail attachment");
define("_AM_XFORMS_ELE_UPLOAD_SAVEAS_FILE", "Upload directory");

//ModuleAdmin
//define('_AM_MODULEADMIN_MISSING','Error: The ModuleAdmin class is missing. Please install the ModuleAdmin Class into /Frameworks (see /docs/readme.txt)');
//define("_AM_MARQUEE_BGCOLOR_SHORT","Background color");
define("_AM_XFORMS_NO_FORMS", "No forms registered. Press on menu - Create a new form -");
define("_AM_XFORMS_NO_FORMS_TOREPORT", "There are no forms to generate report. (Check inactive forms)");
define("_AM_XFORMS_STATUS_INFORMATION", "Information about form status:");
//xForms 1.21 Defaults
define("_AM_XFORMS_ELE_YOUR_NAME",'Your Name');
define("_AM_XFORMS_ELE_YOUR_EMAIL",'Email address');
define("_AM_XFORMS_ELE_YOUR_COMMENTS",'Your comments');

//2.00

define('_AM_XFORMS_ELE_DATE',"Date Selection");
define('_AM_XFORMS_ELE_SELECT_CTRY',"Country Selection");
