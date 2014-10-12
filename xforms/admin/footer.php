<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
$version = number_format($xoopsModule->getVar('version')/100, 2);
$version = !substr($version, -1, 1) ? substr($version, 0, 3) : $version;

$credits = "<div style='text-align: right; font-size: x-small; margin-top: 15px;'>Powered by <a href='about.php'>xForms ".$version."</a>";
echo $credits;

if(version_compare(XOOPS_VERSION, '2.3.0', '>=')){
	echo '<br /><span style="color: #F00;"><b>'._AM_XOOPS_VERSION_WRONG.'</b></span>';
}

echo '</div>';
?>