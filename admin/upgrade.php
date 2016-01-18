<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * xForms module
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

include 'admin_header.php';
$version = number_format($xoopsModule->getVar('version')/100, 2);
$count = $xforms_form_mgr->getCount();
if ($version >= 1.2 || $count > 0) {
    xoops_cp_header();
    echo 'I guess this module has been upgraded already. Why don\'t you delete this file?';
} elseif ($_POST['goupgrade'] == 1) {
    $sql = $msgs = $ret = array();
    $error = false;

    $msgs[] = 'Rename form elements table...';
    $sql[] = 'ALTER TABLE `'.$xoopsDB->prefix('xforms').'` RENAME `'.$xoopsDB->prefix('xforms_formelements').'`';

    $msgs[] = 'Add form_id to elements table...';
    $sql[] = 'ALTER TABLE `'.$xoopsDB->prefix('xforms_formelements')."` ADD `form_id` SMALLINT( 5 ) DEFAULT '1' NOT NULL AFTER `ele_id`";

    $msgs[] = 'Change default value of form_id in elements table...';
    $sql[] = 'ALTER TABLE `'.$xoopsDB->prefix('xforms_formelements')."` CHANGE `form_id` `form_id` SMALLINT( 5 ) DEFAULT '0' NOT NULL";

    $method = $xoopsModuleConfig['method'];
    $method = $xoopsModuleConfig['method'] == 'pm' ? 'p' : 'e';
    $sendto = !empty($xoopsModuleConfig['admin_only']) ? 0 : $xoopsModuleConfig['group'];
    $delimiter = $xoopsModuleConfig['delimeter'] == 'br' ? 'b' : 's';
    $msgs[] = 'Create forms table...';
    $sql[] =
    "CREATE TABLE `".$xoopsDB->prefix('xforms_forms')."` (
	  `form_id` smallint(5) NOT NULL auto_increment,
	  `form_send_method` char(1) NOT NULL default 'e',
	  `form_send_to_group` smallint(3) NOT NULL default '0',
	  `form_order` smallint(3) NOT NULL default '0',
	  `form_delimiter` char(1) NOT NULL default 's',
	  `form_title` varchar(255) NOT NULL default '',
	  `form_submit_text` varchar(50) NOT NULL default '',
	  `form_desc` text NOT NULL,
	  `form_intro` text NOT NULL,
	  `form_whereto` varchar(255) NOT NULL default '',
	  PRIMARY KEY  (`form_id`),
	  KEY `form_order` (`form_order`)
	) ENGINE=MyISAM;";

    $msgs[] = 'Insert default data into forms table...';
    $sql[] =
    "INSERT INTO `".$xoopsDB->prefix('xforms_forms')."` VALUES (1, '".$method."', ".intval($sendto).", 1, '".$delimiter."', 'Contact Us', '"._SUBMIT."', 'Tell us about your comments for this site.', 'Contact us by filling out this form.', '');";

    for ( $i=0; $i<count($sql); $i++ ) {
        if ( false != $xoopsDB->query($sql[$i]) ) {
            $ret[] = $msgs[$i].'done.';
        } else {
            $ret[] = $msgs[$i].'failed.';
            $ret[] = '&nbsp;&nbsp;'.$xoopsDB->error().' ('.$xoopsDB->errno().')';
            $error = true;
        }
    }

    if ($error == false) {
        $ret[] = 'Setting up default permissions...';
        $m = '&nbsp;&nbsp;Grant permission of form id 1 to group id %u...%s';
        for ($i=1; $i<4; $i++) {
            $perm = $moduleperm_handler->create();
            $perm->setVar('gperm_name', $xforms_form_mgr->perm_name);
            $perm->setVar('gperm_itemid', 1);
            $perm->setVar('gperm_groupid', $i);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            if ( !$moduleperm_handler->insert($perm) ) {
                $ret[] = sprintf($m, $i, 'failed.');
                $error = true;
            } else {
                $ret[] = sprintf($m, $i, 'done.');
            }
        }
    }

    xoops_cp_header();
    $output = '';
    foreach ($ret as $r) {
        $output .= $r.'<br />';
    }
    echo '<pre><code>'.$output.'</code></pre>';

    if (false != $error) {
        echo '<b>Oh No! Upgrade seems failed... I honestly hope that you have a backup...</b>';
    } else {
        echo 'Upgrade successed. Now go <a href="'.XOOPS_URL.'/modules/system/admin.php?fct=modulesadmin&op=update&module=xforms">update this module</a>.';
    }
} else {
    xoops_cp_header();
    xoops_confirm(array('goupgrade' => 1), XFORMS_URL.'admin/upgrade.php', 'Make sure you have your files and database backuped. Are you really ready to upgrade the module now?', 'Cut the crap and do it');
}

xoops_cp_footer();
