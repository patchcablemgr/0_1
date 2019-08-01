<?php
define('QUADODO_IN_SYSTEM', true);
require_once '../includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once '../includes/Validate.class.php';
	
	$validate = new Validate($qls);
	$validate->returnData['success'] = array();

	if ($validate->returnData['active'] == 'inactive') {
		echo json_encode($validate->returnData);
		return;
	}
	
	$data = json_decode($_POST['data'], true);
	validate($data, $validate, $qls);
	
	if (!count($validate->returnData['error'])){
		$configArray = array(
			'mail_method' => '',
			'smtp_auth' => '',
			'from_email' => '',
			'from_name' => '',
			'smtp_username' => '',
			'smtp_password' => '',
			'smtp_port' => '',
			'smtp_server' => ''
		);
		$mailMethod = $data['mailMethod'];
		$fromEmail = $data['fromEmail'];
		$fromName = $data['fromName'];
		if ($mailMethod == 'smtp'){
			$smtpServer = $data['smtpServer'];
			$smtpPort = $data['smtpPort'];
			$smtpAuth = $data['smtpAuth'];
			if ($smtpAuth == 'yes'){
				$smtpUsername = $data['smtpUsername'];
				$smtpPassword = $data['smtpPassword'];
			}
		}
	}
	echo json_encode($validate->returnData);
}

function validate($data, &$validate, &$qls){
	$error = [];
	
	return $error;
}
?>
