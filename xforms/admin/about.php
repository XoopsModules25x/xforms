<?php
###############################################################################
##                           See license.txt                                 ##
###############################################################################
include 'admin_header.php';
adminHtmlHeader();
?>
<img src="../images/xforms.png" alt="xforms" style="float: left; margin: 0 10px 5px 0;" />
<h4 style="margin: 0;">xForms</h4>
<p style="margin-top: 0;">
Version <?=number_format($xoopsModule->getVar('version')/100, 2);?><br />
Presented by <a href="http://www.dylian.melgert.net/software" target="_blank">FliX Software</a> <br />
Copyright &copy; 2009 Dylian Melgert
<br clear="all" />
</p>

<h4 style="margin: 0;">License</h4>
<p style="margin-top: 0;">
This software is licensed under the CC-GNU GPL.<br />
<a href="http://creativecommons.org/licenses/GPL/2.0/" target="_blank">Commons Deed</a> |
<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">Legal Code</a>
</p>

<h4 style="margin: 0;">Who to Contact</h4>
<p style="margin-top: 0;">
Whe have not yet created a support page.
</p>

<h4 style="margin: 0;">Special thanks to</h4>
<p style="margin: 0;">
<a href="http://www.brandycoke.com/" target="_blank">Brandycoke Productions</a> for creating liaise, the module xforms is based on.
</p>

<?php
xoops_cp_footer();
?>