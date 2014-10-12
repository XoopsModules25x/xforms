<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
$version = number_format($xoopsModule->getVar('version')/100, 2);
$version = !substr($version, -1, 1) ? substr($version, 0, 3) : $version;

$credits = "<div style='text-align: right; font-size: x-small; margin-top: 15px;'>Powered by <a href='about.php'>xforms ".$version."</a>";
echo $credits;

$version_check = preg_match('/2\.0\./', XOOPS_VERSION) ||  preg_match('/2\.3\./', XOOPS_VERSION) ;
if( !$version_check ){
	echo '<br /><span style="color: #F00;"><b>'._AM_XOOPS_VERSION_WRONG.'</b></span>';
}

echo '</div>';
?>