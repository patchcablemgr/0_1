<?php
/*** *** *** *** *** ***
* @package   Quadodo Login Script
* @file      database_info.php
* @author    Douglas Rennehan
* @generated November 26th, 2016
* @link      http://www.quadodo.net
*** *** *** *** *** ***
* Comments are always before the code they are commenting
*** *** *** *** *** ***/
if (!defined('QUADODO_IN_SYSTEM')) {
exit;
}

//define('SYSTEM_INSTALLED', true);
$database_prefix = '';
$database_type = 'MySQLi';
$database_server_name = 'localhost';
$database_username = 'app';
$database_password = '4o7bYoLaV7AR8lmv';
//If this is a new registration, then the app database name needs to be manually defined
$database_name = $org_id != NULL ? CUSTOM_APP_DB_PREFIX.$org_id : CUSTOM_APP_DB_PREFIX.$this->qls->user_info['org_id'];
$database_port = false;

/**
 * Use persistent connections?
 * Change to true if you have a high load
 * on your server, but it's not really needed.
 */
$database_persistent = false;
?>
