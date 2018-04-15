CREATE TABLE `xforms_form` (
  `form_id`            SMALLINT(5)      NOT NULL AUTO_INCREMENT,
  `form_save_db`       TINYINT(1)       NOT NULL DEFAULT '1',
  `form_send_method`   CHAR(1)          NOT NULL DEFAULT 'e',
  `form_send_to_group` SMALLINT(3)      NOT NULL DEFAULT '0',
  `form_send_to_other` VARCHAR(255)     NOT NULL DEFAULT '',
  `form_send_copy`     TINYINT(1)       NOT NULL DEFAULT '1',
  `form_order`         SMALLINT(3)      NOT NULL DEFAULT '0',
  `form_delimiter`     CHAR(1)          NOT NULL DEFAULT 's',
  `form_title`         VARCHAR(255)     NOT NULL DEFAULT '',
  `form_submit_text`   VARCHAR(50)      NOT NULL DEFAULT '',
  `form_desc`          TEXT             NOT NULL,
  `form_intro`         TEXT             NOT NULL,
  `form_email_header`  TEXT             NOT NULL,
  `form_email_footer`  TEXT             NOT NULL,
  `form_email_uheader` TEXT             NOT NULL,
  `form_email_ufooter` TEXT             NOT NULL,
  `form_whereto`       VARCHAR(255)     NOT NULL DEFAULT '',
  `form_display_style` VARCHAR(1)       NOT NULL DEFAULT 'f',
  `form_begin`         INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `form_end`           INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `form_active`        TINYINT(1)       NOT NULL DEFAULT '1',
  PRIMARY KEY (`form_id`),
  KEY `form_order` (`form_order`)
)
  ENGINE = MyISAM;

CREATE TABLE `xforms_element` (
  `ele_id`          MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
  `form_id`         SMALLINT(5)  NOT NULL DEFAULT '0',
  `ele_type`        VARCHAR(10)  NOT NULL DEFAULT '',
  `ele_caption`     TEXT         NOT NULL,
  `ele_order`       SMALLINT(2)  NOT NULL DEFAULT '0',
  `ele_req`         TINYINT(1)   NOT NULL DEFAULT '1',
  `ele_display_row` TINYINT(1)   NOT NULL DEFAULT '1',
  `ele_value`       TEXT         NOT NULL,
  `ele_display`     TINYINT(1)   NOT NULL DEFAULT '1',
  PRIMARY KEY (`ele_id`),
  KEY `ele_display` (`ele_display`),
  KEY `disp_ele_by_form` (`form_id`, `ele_display`),
  KEY `ele_order` (`ele_order`)
)
  ENGINE = MyISAM;

CREATE TABLE `xforms_userdata` (
  `udata_id`    INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `uid`         MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `form_id`     SMALLINT(5)           NOT NULL,
  `ele_id`      MEDIUMINT(8)          NOT NULL,
  `udata_time`  INT(10) UNSIGNED      NOT NULL DEFAULT '0',
  `udata_ip`    VARCHAR(100)          NOT NULL DEFAULT '0.0.0.0',
  `udata_agent` VARCHAR(500)          NOT NULL DEFAULT '',
  `udata_value` TEXT                  NOT NULL,
  PRIMARY KEY (`udata_id`)
)
  ENGINE = MyISAM;
