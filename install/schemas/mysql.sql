DROP TABLE IF EXISTS `{database_prefix}config`;

CREATE TABLE `{database_prefix}config`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`value` VARCHAR(255) DEFAULT '' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX(`name`)
);

DROP TABLE IF EXISTS `{database_prefix}sessions`;

CREATE TABLE `{database_prefix}sessions`(
	`id` VARCHAR(40) DEFAULT '' NOT NULL,
	`value` VARCHAR(40) DEFAULT '' NOT NULL,
	`time` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `sessions_idx` (`value`,`time`)
);

DROP TABLE IF EXISTS `{database_prefix}users`;

CREATE TABLE `{database_prefix}users`(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(255) DEFAULT '' NOT NULL,
	`password` VARCHAR(40) DEFAULT '' NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	`active` CHAR(3) DEFAULT 'no' NOT NULL,
	`last_login` INT(11) DEFAULT '0' NOT NULL,
	`last_session` VARCHAR(40) DEFAULT '' NOT NULL,
	`blocked` CHAR(3) DEFAULT 'no' NOT NULL,
	`tries` TINYINT(2) DEFAULT '0' NOT NULL,
	`last_try` INT(11) DEFAULT '0' NOT NULL,
	`email` VARCHAR(255) DEFAULT '' NOT NULL,
	`mask_id` SMALLINT(6) DEFAULT '0' NOT NULL,
	`group_id` SMALLINT(6) DEFAULT '2' NOT NULL,
	`activation_time` INT(11) DEFAULT '0' NOT NULL,
	`last_action` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `users_idx` (`username`),
	INDEX `users_idx2` (`code`),
	INDEX `users_idx3` (`last_login`),
	INDEX `users_idx4` (`last_session`),
	INDEX `users_idx5` (`last_try`),
	INDEX `users_idx6` (`activation_time`),
	INDEX `users_idx7` (`last_action`)
);

DROP TABLE IF EXISTS `{database_prefix}security_image`;

CREATE TABLE `{database_prefix}security_image`(
	`random_id` VARCHAR(40) DEFAULT '' NOT NULL,
	`real_text` VARCHAR(10) DEFAULT '' NOT NULL,
	`date` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`random_id`),
	INDEX `security_image_idx` (`real_text`,`date`)
);

DROP TABLE IF EXISTS `{database_prefix}pages`;

CREATE TABLE `{database_prefix}pages`(
	`id` SMALLINT(6) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) DEFAULT '' NOT NULL,
	`hits` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `pages_idx` (`name`)
);

DROP TABLE IF EXISTS `{database_prefix}groups`;

CREATE TABLE `{database_prefix}groups`(
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

DROP TABLE IF EXISTS `{database_prefix}masks`;

CREATE TABLE `{database_prefix}masks`(
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
	`auth_356a192b7913b04c54574d18c28d46e6395428ab` TINYINT(1) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `masks_idx` (`name`)
);

DROP TABLE IF EXISTS `{database_prefix}invitations`;

CREATE TABLE `{database_prefix}invitations`(
	`id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT,
	`used` TINYINT(1) DEFAULT '0' NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `invitations_idx` (`code`)
);

DROP TABLE IF EXISTS `{database_prefix}password_requests`;

CREATE TABLE `{database_prefix}password_requests`(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) DEFAULT '0' NOT NULL,
	`code` VARCHAR(40) DEFAULT '' NOT NULL,
	`used` TINYINT(1) DEFAULT '0' NOT NULL,
	`date` INT(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY(`id`),
	INDEX(`code`),
	INDEX(`date`)
);