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
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xforms
 * @since           1.30
 * @author          Xoops Development Team
 */

use XoopsModules\Xforms;
/** @var Xforms\Helper $helper */
$helper = Xforms\Helper::getInstance();

include __DIR__ . '/admin_header.php';
$version = number_format($xoopsModule->getVar('version') / 100, 2);
$count   = $xforms_form_mgr->getCount();
if ($version >= 1.2 || $count > 0) {
    xoops_cp_header();
    echo 'I guess this module has been upgraded already. Why don\'t you delete this file?';
} elseif (1 == $_POST['goupgrade']) {
    $sql   = $msgs = $ret = [];
    $error = false;

    $msgs[] = 'Rename form elements table...';
    $sql[]  = 'ALTER TABLE `' . $xoopsDB->prefix('xforms') . '` RENAME `' . $xoopsDB->prefix('xforms_formelements') . '`';

    $msgs[] = 'Add form_id to elements table...';
    $sql[]  = 'ALTER TABLE `' . $xoopsDB->prefix('xforms_formelements') . "` ADD `form_id` SMALLINT( 5 ) DEFAULT '1' NOT NULL AFTER `ele_id`";

    $msgs[] = 'Change default value of form_id in elements table...';
    $sql[]  = 'ALTER TABLE `' . $xoopsDB->prefix('xforms_formelements') . "` CHANGE `form_id` `form_id` SMALLINT( 5 ) DEFAULT '0' NOT NULL";

    $method    = $helper->getConfig('method');
    $method    = 'pm' === $helper->getConfig('method') ? 'p' : 'e';
    $sendto    = !empty($helper->getConfig('admin_only')) ? 0 : $helper->getConfig('group');
    $delimiter = 'br' === $helper->getConfig('delimeter') ? 'b' : 's';
    $msgs[]    = 'Create forms table...';
    $sql[]     = 'CREATE TABLE `' . $xoopsDB->prefix('xforms_forms') . "` (
      `form_id` SMALLINT(5) NOT NULL AUTO_INCREMENT,
      `form_send_method` CHAR(1) NOT NULL DEFAULT 'e',
      `form_send_to_group` SMALLINT(3) NOT NULL DEFAULT '0',
      `form_order` SMALLINT(3) NOT NULL DEFAULT '0',
      `form_delimiter` CHAR(1) NOT NULL DEFAULT 's',
      `form_title` VARCHAR(255) NOT NULL DEFAULT '',
      `form_submit_text` VARCHAR(50) NOT NULL DEFAULT '',
      `form_desc` TEXT NOT NULL,
      `form_intro` TEXT NOT NULL,
      `form_whereto` VARCHAR(255) NOT NULL DEFAULT '',
      PRIMARY KEY  (`form_id`),
      KEY `form_order` (`form_order`)
    ) ENGINE=MyISAM;";

    $msgs[] = 'INSERT default DATA INTO forms TABLE...';
    $sql[]  = 'INSERT INTO `' . $xoopsDB->prefix('xforms_forms') . "` VALUES (1, '" . $method . "', " . $sendto . ", 1, '" . $delimiter . "', 'Contact Us', '" . _SUBMIT . "', 'Tell us about your comments for this site.', 'Contact us by filling out this form.', '');";

    for ($i = 0, $iMax = count($sql); $i < $iMax; ++$i) {
        if (false !== $xoopsDB->query($sql[$i])) {
            $ret[] = $msgs[$i] . 'done.';
        } else {
            $ret[] = $msgs[$i] . 'failed.';
            $ret[] = '&nbsp;&nbsp;' . $xoopsDB->error() . ' (' . $xoopsDB->errno() . ')';
            $error = true;
        }
    }

    if (false === $error) {
        $ret[] = 'Setting up default permissions...';
        $m     = '&nbsp;&nbsp;Grant permission of form id 1 to group id %u...%s';
        for ($i = 1; $i < 4; $i++) {
            $perm = $modulepermHandler->create();
            $perm->setVar('gperm_name', $xforms_form_mgr->perm_name);
            $perm->setVar('gperm_itemid', 1);
            $perm->setVar('gperm_groupid', $i);
            $perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
            if (!$modulepermHandler->insert($perm)) {
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
        $output .= $r . '<br>';
    }
    echo '<pre><code>' . $output . '</code></pre>';

    if (false !== $error) {
        echo '<b>Oh No! Upgrade seems failed... I honestly hope that you have a backup...</b>';
    } else {
        echo 'Upgrade successed. Now go <a href="' . XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin&op=update&module=xforms">update this module</a>.';
    }
} else {
    xoops_cp_header();
    xoops_confirm(['goupgrade' => 1], XFORMS_URL . 'admin/upgrade.php', 'Make sure you have your files and database backuped. Are you really ready to upgrade the module now?', 'Cut the crap and do it');
}

xoops_cp_footer();
