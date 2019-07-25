<?php
define('QUADODO_IN_SYSTEM', true);
require_once '../includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once '../includes/Validate.class.php';
	
	$validate = new Validate($qls);
	
	$data = json_decode($_POST['data'], true);
	validate($data, $validate, $qls);
	
	if (!count($validate->returnData['error'])){
		$recipient = $sender = 'admin@otterm8.com';
		$email = $data['email'];
		$time = time();
		
		// Send notification to admin
		$subject = 'New Demo User';
		$msg = file_get_contents('./html/emailAdminNotify.html');
		$msg = str_replace('<!--MESSAGE-->', $email, $msg);
		
		$attributes = array('recipient', 'sender', 'subject', 'message');
		$values = array($recipient, $sender, $subject, $msg);
		
		$qls->SQL->insert('email_queue', $attributes, $values);
		$qls->SQL->insert('demo_contact_emails', array('email', 'date'), array($email, $time));
	}
	echo json_encode($validate->returnData);
}

function validate($data, &$validate, &$qls){
	
	// Validate email
	$validate->validateEmail($data['email']);
	
	return;
}

?>
