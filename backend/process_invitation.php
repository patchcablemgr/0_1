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
		if($data['action'] == 'create') {
			$qls->Security->check_auth_page('administrator.php');
			
			// Super secret code so they can register
			$code = sha1(md5($qls->config['sql_prefix']) . time() . $_SERVER['REMOTE_ADDR']);
			$subject = "You\'ve been invited!";
			$recipientEmail = $data['email'];

			$btnURL = 'https://'.$qls->config['cookie_domain'].$qls->config['cookie_path'].'register.php?code='.$code;
			$btnText = 'Accept Invitation';

			$msg = file_get_contents('../html/email_invitation.html');
			$msg = str_replace('<!--btnURL-->', $btnURL, $msg);
			$msg = str_replace('<!--btnText-->', $btnText, $msg);
			
			//$qls->PHPmailer->SMTPDebug = 3;
			$qls->PHPmailer->addAddress($recipientEmail, '');
			$qls->PHPmailer->Subject = $subject;
			$qls->PHPmailer->msgHTML($msg);
			if(!$qls->PHPmailer->send()) {
				array_push($validate->returnData['error'], $qls->PHPmailer->ErrorInfo);
			} else {
				$validate->returnData['success'] = 'Invitation sent to: '.$recipientEmail;
			}
			$qls->PHPmailer->clearAllRecipients();
		}
	}
	echo json_encode($validate->returnData);
}

function validate($data, &$validate, &$qls){
	$error = [];
	
	return $error;
}

function canDelete(&$qls, &$validate){
	$userGroupID = $qls->user_info['group_id'];
	$userOrgID = $qls->user_info['org_id'];
	$administratorCount = 0;
	$queryUsers = $qls->SQL->select('*', 'users', array('org_id' => array('=', $userOrgID)));
	$userCount = $qls->SQL->num_rows($queryUsers);
	while($row = $qls->SQL->fetch_assoc($queryUsers)) {
		if($row['group_id'] == 3) {
			$administratorCount++;
		}
	}

	if($userGroupID == 3 and ($userCount > 1 and $administratorCount < 2)) {
		return false;
	} else {
		return true;
	}
}
?>
