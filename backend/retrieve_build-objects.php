<?php
define('QUADODO_IN_SYSTEM', true);
require_once '../includes/header.php';
$qls->Security->check_auth_page('user.php');

require_once '../includes/templateFunctions.php';
include_once('./includes/content-build-objectData.php');
include_once('./includes/content-build-objects.php');
?>
