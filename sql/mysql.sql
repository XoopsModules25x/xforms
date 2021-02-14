CREATE TABLE `xforms_form` (
  `form_id` smallint(5) NOT NULL auto_increment,
  `form_save_db` tinyint(1) NOT NULL default '1',
  `form_send_method` char(1) NOT NULL default 'e',
  `form_send_to_group` smallint(3) NOT NULL default '0',
  `form_send_to_other` varchar(255) NOT NULL default '',
  `form_send_copy` tinyint(1) NOT NULL default '1',
  `form_order` smallint(3) NOT NULL default '0',
  `form_delimiter` char(1) NOT NULL default 's',
  `form_title` varchar(255) NOT NULL default '',
  `form_submit_text` varchar(50) NOT NULL default '',
  `form_desc` text NOT NULL,
  `form_intro` text NOT NULL,
  `form_email_header` text NOT NULL,
  `form_email_footer` text NOT NULL,
  `form_email_uheader` text NOT NULL,
  `form_email_ufooter` text NOT NULL,
  `form_whereto` varchar(255) NOT NULL default '',
  `form_display_style` varchar(1) NOT NULL default 'f',
  `form_begin` int(10) unsigned NOT NULL default '0',
  `form_end` int(10) unsigned NOT NULL default '0',
  `form_active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`form_id`),
  KEY `form_order` (`form_order`)
) ENGINE=MyISAM;

CREATE TABLE `xforms_element` (
  `ele_id` mediumint(8) NOT NULL auto_increment,
  `form_id` smallint(5) NOT NULL default '0',
  `ele_type` varchar(10) NOT NULL default '',
  `ele_caption` text NOT NULL,
  `ele_order` smallint(2) NOT NULL default '0',
  `ele_req` tinyint(1) NOT NULL default '1',
  `ele_display_row` tinyint(1) NOT NULL default '1',
  `ele_value` text NOT NULL,
  `ele_display` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`ele_id`),
  KEY `ele_display` (`ele_display`),
  KEY `disp_ele_by_form` (`form_id`, `ele_display`),
  KEY `ele_order` (`ele_order`)
) ENGINE=MyISAM;

CREATE TABLE `xforms_userdata` (
  `udata_id` int(11) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `form_id` smallint(5) NOT NULL,
  `ele_id` mediumint(8) NOT NULL,
  `udata_time` int(10) unsigned NOT NULL default '0',
  `udata_ip` varchar(100) NOT NULL default '0.0.0.0',
  `udata_agent` varchar(500) NOT NULL default '',
  `udata_value` text NOT NULL,
  PRIMARY KEY  (`udata_id`)
) ENGINE=MyISAM;
