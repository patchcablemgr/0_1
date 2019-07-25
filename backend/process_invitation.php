<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/Validate.class.php';
	
	$validate = new Validate($qls);
	$validate->returnData['success'] = array();

	if ($validate->returnData['active'] == 'inactive') {
		echo json_encode($validate->returnData);
		return;
	}
	
	$data = json_decode($_POST['data'], true);
	validate($data, $validate, $qls);
	
	if (!count($validate->returnData['error'])){
		switch($data['action']) {
			case 'create':
				$qls->Security->check_auth_page('administrator.php');
				
				// Secret code so they can register
				$code = sha1(md5($qls->config['sql_prefix']) . time() . $_SERVER['REMOTE_ADDR']);
				$subject = "You\'ve been invited!";
				$recipientEmail = $data['email'];
				$recipientID = 0;
				$orgID = $qls->user_info['org_id'];
				$query = $qls->app_SQL->select('*', 'table_organization_data');
				$orgName = $qls->app_SQL->fetch_assoc($query)['name'];

				if ($qls->User->check_username_existence($recipientEmail)) {
					$query = $qls->SQL->select('*', 'users', array('email' => array('=', $recipientEmail)));
					$recipient = $qls->SQL->fetch_assoc($query);
					$recipientID = $recipient['id'];

					$btnURL = 'https://otterm8.com/app/index.php';
					$btnText = 'Login to Accept';
				} else {
					$btnURL = 'https://otterm8.com/app/register.php?code='.$code;
					$btnText = 'Accept Invitation';
				}

				$qls->SQL->insert('invitations',
					array(
						'email',
						'used',
						'to_id',
						'from_id',
						'code',
						'org_id',
						'org_name'
					),
					array(
						$recipientEmail,
						0,
						$recipientID,
						$qls->user_info['id'],
						$code,
						$orgID,
						$orgName
					)
				);

				$msg = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/html/email_invitation.html');
				$msg = str_replace('<!--btnURL-->', $btnURL, $msg);
				$msg = str_replace('<!--btnText-->', $btnText, $msg);
				$attributes = array('recipient', 'sender', 'subject', 'message');
				$values = array($recipientEmail, 'admin@otterm8.com', $subject, $msg);
				$qls->SQL->insert('email_queue', $attributes, $values);
				
				$validate->returnData['success'] = 'Invitation sent to: '.$recipientEmail;
				
				break;
				
			case 'accept':
				$qls->Security->check_auth_page('user.php');
				
				$userID = $qls->user_info['id'];
				$userGroupID = $qls->user_info['group_id'];
				$code = $data['invitationCode'];

				$queryInvitation = $qls->SQL->select('*', 'invitations', array('to_id' => array('=', $userID), 'AND', 'code' => array('=', $code), 'AND', 'used' => array('=', 0)));
				if($qls->SQL->num_rows($queryInvitation)) {
					if(canDelete($qls, $validate)) {
						$invitation = $qls->SQL->fetch_assoc($queryInvitation);
						$qls->SQL->update('users', array('group_id' => 5, 'org_id' => $invitation['org_id']), array('id' => array('=', $qls->user_info['id'])));
						$qls->SQL->update('invitations', array('used' => 1), array('id' => array('=', $invitation['id'])));
						$validate->returnData['success'] = 'You have joined the new organization.';
					} else {
						$errorMsg = 'You are the only administrator... your team needs you!';
						array_push($validate->returnData['error'], $errorMsg);
					}
				} else {
					$errorMsg = 'Invitation code is invalid, does not exist, or has already been used.';
					array_push($validate->returnData['error'], $errorMsg);
				}
				break;
				
			case 'decline':
				$qls->Security->check_auth_page('user.php');
				
				$code = $data['invitationCode'];
				$qls->SQL->delete('invitations', array('id' => array('=', $code)));
				break;

			case 'revert':
				$qls->Security->check_auth_page('user.php');
			
				if(canDelete($qls, $validate)) {
					$originalOrgID = $qls->user_info['original_org_id'];
					$originalGroupID = $qls->user_info['original_group_id'];
					$userID = $qls->user_info['id'];
					$qls->SQL->update('users', array('org_id' => $originalOrgID, 'group_id' => $originalGroupID), array('id' => array('=', $userID)));
					$validate->returnData['success'] = 'Your account was reverted back to your original organization.';
				} else {
					$errorMsg = 'You are the only administrator... your team needs you!';
					array_push($validate->returnData['error'], $errorMsg);
				}
				break;
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
