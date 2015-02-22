<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
if( !defined('xforms_ROOT_PATH') ){ exit(); }

$size = !empty($value[0]) ? intval($value[0]) : 0;
//$saveas = $value[3] != 1 ? 0 : 1;
if (isset($value[3]) && $value[3] == 1) {
    $saveas = $value[3];
} else {
    $saveas = 0;
}

$size = new XoopsFormText(_AM_ELE_UPLOAD_MAXSIZE, 'ele_value[0]', 10, 20, $size);
$size->setDescription(_AM_ELE_UPLOAD_MAXSIZE_DESC.'<br />'._AM_ELE_UPLOAD_DESC_SIZE_NOLIMIT);

$ext = new XoopsFormText(_AM_ELE_UPLOAD_ALLOWED_EXT, 'ele_value[1]', 50, 255, isset($value[1]) ? $myts->htmlspecialchars($myts->stripSlashesGPC($value[1])) : '');
$ext->setDescription(_AM_ELE_UPLOAD_ALLOWED_EXT_DESC.'<br /><br />'._AM_ELE_UPLOAD_DESC_NOLIMIT);

$mime = new XoopsFormTextArea(_AM_ELE_UPLOAD_ALLOWED_MIME, 'ele_value[2]', isset($value[2]) ? $myts->htmlspecialchars($myts->stripSlashesGPC($value[2])) : '', 5, 50);
$mime->setDescription(_AM_ELE_UPLOAD_ALLOWED_MIME_DESC.'<br /><br />'._AM_ELE_UPLOAD_DESC_NOLIMIT);

$saveas = new XoopsFormSelect(_AM_ELE_UPLOAD_SAVEAS, 'ele_value[3]', $saveas);
$saveas->addOptionArray(array(0=>_AM_ELE_UPLOAD_SAVEAS_MAIL, 1=>_AM_ELE_UPLOAD_SAVEAS_FILE));

$output->addElement($size, 1);
$output->addElement($ext);
$output->addElement($mime);
$output->addElement($saveas, 1);

?>