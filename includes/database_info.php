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

#define('SYSTEM_INSTALLED', false);
$database_prefix = '';
$database_type = 'MySQLi';
$database_server_name = 'localhost';
$database_username = 'qls';
$database_password = 'qlsTillie=-01';
$database_name = 'qls';
$database_port = false;

/**
 * Use persistent connections?
 * Change to true if you have a high load
 * on your server, but it's not really needed.
 */
$database_persistent = false;
?>