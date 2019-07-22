<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
$qls->Security->check_auth_page('user.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/templateFunctions.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/app/includes/content-build-objectData.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/app/includes/content-build-objects.php');
?>
