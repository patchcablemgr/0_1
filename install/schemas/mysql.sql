DROP TABLE IF EXISTS `{database_prefix_qls}config`;

CREATE TABLE `{database_prefix_qls}config`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`value` VARCHAR(255) DEFAULT '' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX(`name`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}email_queue`;

CREATE TABLE `{database_prefix_qls}email_queue` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`recipient` varchar(255) NOT NULL,
	`sender` varchar(255) NOT NULL,
	`subject` varchar(255) NOT NULL,
	`message` text NOT NULL,
	`sent` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}sessions`;

CREATE TABLE `{database_prefix_qls}sessions`(
	`id` VARCHAR(40) DEFAULT '' NOT NULL,
	`value` VARCHAR(40) DEFAULT '' NOT NULL,
	`time` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `sessions_idx` (`value`,`time`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}users`;

CREATE TABLE `{database_prefix_qls}users`(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(255) DEFAULT '' NOT NULL,
	`password` VARCHAR(40) DEFAULT '' NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	`active` CHAR(3) DEFAULT 'no' NOT NULL,
	`last_login` INT(11) DEFAULT '0' NOT NULL,
	`last_session` VARCHAR(40) DEFAULT '' NOT NULL,
	`last_session_cookie_id` varchar(128) NOT NULL DEFAULT '',
	`blocked` CHAR(3) DEFAULT 'no' NOT NULL,
	`tries` TINYINT(2) DEFAULT '0' NOT NULL,
	`last_try` INT(11) DEFAULT '0' NOT NULL,
	`email` VARCHAR(255) DEFAULT '' NOT NULL,
	`mask_id` SMALLINT(6) DEFAULT '0' NOT NULL,
	`group_id` SMALLINT(6) DEFAULT '2' NOT NULL,
	`activation_time` INT(11) DEFAULT '0' NOT NULL,
	`last_action` INT(11) DEFAULT '0' NOT NULL,
	`timezone` varchar(255) NOT NULL DEFAULT 'America/Los_Angeles',
	`mfa` tinyint(4) NOT NULL DEFAULT '0',
	`mfa_secret` varchar(32) DEFAULT NULL,
	`mfa_auth_token` varchar(40) DEFAULT NULL,
	`scanMethod` tinyint(4) NOT NULL DEFAULT '0',
	PRIMARY KEY(`id`),
	INDEX `users_idx` (`username`),
	INDEX `users_idx2` (`code`),
	INDEX `users_idx3` (`last_login`),
	INDEX `users_idx4` (`last_session`),
	INDEX `users_idx5` (`last_try`),
	INDEX `users_idx6` (`activation_time`),
	INDEX `users_idx7` (`last_action`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}security_image`;

CREATE TABLE `{database_prefix_qls}security_image`(
	`random_id` VARCHAR(40) DEFAULT '' NOT NULL,
	`real_text` VARCHAR(10) DEFAULT '' NOT NULL,
	`date` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`random_id`),
	INDEX `security_image_idx` (`real_text`,`date`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}pages`;

CREATE TABLE `{database_prefix_qls}pages`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`hits` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `pages_idx` (`name`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}groups`;

CREATE TABLE `{database_prefix_qls}groups`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`mask_id` SMALLINT(6) DEFAULT '0' NOT NULL,
	`is_public` TINYINT(1) DEFAULT '0' NOT NULL,
	`leader` INT(11) DEFAULT '0' NOT NULL,
	`expiration_date` TINYINT(3) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `groups_idx` (`name`),
	INDEX `groups_idx2` (`is_public`),
	INDEX `groups_idx3` (`expiration_date`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}masks`;

CREATE TABLE `{database_prefix_qls}masks`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`auth_admin` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_phpinfo` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_configuration` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_add_user` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_user_list` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_remove_user` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_edit_user` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_add_page` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_page_list` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_remove_page` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_edit_page` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_page_stats` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_add_mask` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_list_masks` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_remove_mask` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_edit_mask` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_add_group` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_list_groups` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_remove_group` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_edit_group` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_activate_account` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_admin_send_invite` TINYINT(1) DEFAULT '0' NOT NULL,
	`auth_9e6a55b6b4563e652a23be9d623ca5055c356940` TINYINT(1) NOT NULL DEFAULT '0',
	`auth_b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f` TINYINT(1) NOT NULL DEFAULT '0',
	`auth_91032ad7bbcb6cf72875e8e8207dcfba80173f7c` TINYINT(1) NOT NULL DEFAULT '0',
	`auth_472b07b9fcf2c2451e8781e944bf5f77cd8457c8` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY(`id`),
	INDEX `masks_idx` (`name`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}invitations`;

CREATE TABLE `{database_prefix_qls}invitations`(
	`id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`email` varchar(255) NOT NULL,
	`used` TINYINT(1) DEFAULT '0' NOT NULL,
	`to_id` int(11) NOT NULL,
	`from_id` int(11) NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	`group_id` int(11) NOT NULL DEFAULT '5',
	PRIMARY KEY(`id`),
	INDEX `invitations_idx` (`code`)
);

DROP TABLE IF EXISTS `{database_prefix_qls}password_requests`;

CREATE TABLE `{database_prefix_qls}password_requests`(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) DEFAULT '0' NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	`used` TINYINT(1) DEFAULT '0' NOT NULL,
	`date` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX(`code`),
	INDEX(`date`)
);

DROP TABLE IF EXISTS `{database_prefix_app}env_tree`;

CREATE TABLE `{database_prefix_app}env_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT 'New Node',
  `parent` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` int(11) NOT NULL DEFAULT '42',
  `floorplan_img` varchar(40) DEFAULT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}cabinet_adj`;

CREATE TABLE `{database_prefix_app}cabinet_adj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `left_cabinet_id` int(11) DEFAULT NULL,
  `right_cabinet_id` int(11) DEFAULT NULL,
  `entrance_ru` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}cable_path`;

CREATE TABLE `{database_prefix_app}cable_path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cabinet_a_id` int(11) NOT NULL,
  `cabinet_b_id` int(11) NOT NULL DEFAULT '0',
  `distance` int(11) NOT NULL DEFAULT '1',
  `path_entrance_ru` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}floorplan_object`;

CREATE TABLE `{database_prefix_app}floorplan_object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `env_tree_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position_top` int(11) NOT NULL,
  `position_left` int(11) NOT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}floorplan_object_peer`;

CREATE TABLE `{database_prefix_app}floorplan_object_peer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `peer_id` int(11) NOT NULL,
  `peer_face` int(11) NOT NULL,
  `peer_depth` int(11) NOT NULL,
  `peer_port` int(11) NOT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}inventory`;

CREATE TABLE `{database_prefix_app}inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) NOT NULL DEFAULT '0',
  `a_code39` varchar(255) NOT NULL DEFAULT '0',
  `a_connector` int(11) NOT NULL DEFAULT '0',
  `a_object_id` int(11) NOT NULL DEFAULT '0',
  `a_port_id` int(11) NOT NULL DEFAULT '0',
  `a_object_face` int(11) NOT NULL DEFAULT '0',
  `a_object_depth` int(11) NOT NULL DEFAULT '0',
  `b_id` int(11) NOT NULL DEFAULT '0',
  `b_code39` varchar(255) NOT NULL DEFAULT '0',
  `b_connector` int(11) NOT NULL DEFAULT '0',
  `b_object_id` int(11) NOT NULL DEFAULT '0',
  `b_port_id` int(11) NOT NULL DEFAULT '0',
  `b_object_face` int(11) NOT NULL DEFAULT '0',
  `b_object_depth` int(11) NOT NULL DEFAULT '0',
  `mediaType` int(11) DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '1',
  `color` int(11) DEFAULT '0',
  `editable` tinyint(1) NOT NULL DEFAULT '1',
  `order_id` int(11) DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY(`id`),
  INDEX(`a_id`),
  INDEX(`b_id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}object`;

CREATE TABLE `{database_prefix_app}object` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `env_tree_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'New_Object',
  `template_id` int(11) NOT NULL,
  `RU` int(11) DEFAULT NULL,
  `cabinet_front` int(11) DEFAULT NULL,
  `cabinet_back` int(11) DEFAULT '0',
  `parent_id` int(11) DEFAULT NULL,
  `parent_face` int(11) DEFAULT '0',
  `parent_depth` int(11) DEFAULT NULL,
  `insertSlotX` int(11) DEFAULT NULL,
  `insertSlotY` int(11) DEFAULT NULL,
  `position_top` int(11) DEFAULT NULL,
  `position_left` int(11) DEFAULT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}object_category`;

CREATE TABLE `{database_prefix_app}object_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `defaultOption` int(11) NOT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}object_compatibility`;

CREATE TABLE `{database_prefix_app}object_compatibility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `side` tinyint(4) DEFAULT '0',
  `depth` int(11) DEFAULT '0',
  `portLayoutX` int(11) DEFAULT NULL,
  `portLayoutY` int(11) DEFAULT NULL,
  `portTotal` int(11) DEFAULT NULL,
  `encLayoutX` int(11) DEFAULT NULL,
  `encLayoutY` int(11) DEFAULT NULL,
  `templateType` varchar(255) NOT NULL,
  `partitionType` varchar(255) DEFAULT NULL,
  `partitionFunction` varchar(255) DEFAULT NULL,
  `portOrientation` int(11) DEFAULT NULL,
  `portType` int(11) DEFAULT NULL,
  `mediaType` int(11) DEFAULT NULL,
  `mediaCategory` varchar(255) DEFAULT NULL,
  `mediaCategoryType` int(11) DEFAULT NULL,
  `direction` varchar(255) DEFAULT NULL,
  `flex` varchar(255) DEFAULT NULL,
  `hUnits` int(11) DEFAULT NULL,
  `vUnits` int(11) DEFAULT NULL,
  `portNameFormat` text,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}object_peer`;

CREATE TABLE `{database_prefix_app}object_peer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) DEFAULT NULL,
  `a_face` int(11) DEFAULT NULL,
  `a_depth` int(11) DEFAULT NULL,
  `a_port` int(11) DEFAULT NULL,
  `a_endpoint` tinyint(1) NOT NULL,
  `b_id` int(11) DEFAULT NULL,
  `b_face` int(11) DEFAULT NULL,
  `b_depth` int(11) DEFAULT NULL,
  `b_port` int(11) DEFAULT NULL,
  `b_endpoint` tinyint(1) NOT NULL,
  `floorplan_peer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}object_templates`;

CREATE TABLE `{database_prefix_app}object_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `templateName` varchar(255) NOT NULL,
  `templateCategory_id` int(11) DEFAULT NULL,
  `templateType` varchar(255) NOT NULL,
  `templateRUSize` int(11) DEFAULT NULL,
  `templateFunction` varchar(255) NOT NULL,
  `templateMountConfig` tinyint(1) DEFAULT NULL,
  `templateEncLayoutX` int(11) DEFAULT NULL,
  `templateEncLayoutY` int(11) DEFAULT NULL,
  `templateHUnits` int(11) DEFAULT NULL,
  `templateVUnits` int(11) DEFAULT NULL,
  `templatePartitionData` text,
  `frontImage` varchar(45) DEFAULT NULL,
  `rearImage` varchar(45) DEFAULT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}organization_data`;

CREATE TABLE `{database_prefix_app}organization_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY(`id`)
);

DROP TABLE IF EXISTS `{database_prefix_app}populated_port`;

CREATE TABLE `{database_prefix_app}populated_port` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `object_face` int(11) NOT NULL,
  `object_depth` int(11) NOT NULL,
  `port_id` int(11) NOT NULL,
  PRIMARY KEY(`id`)
);