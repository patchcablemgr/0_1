<?php
define('QUADODO_IN_SYSTEM', true);
require_once '../includes/header.php';
$qls->Security->check_auth_page('administrator.php');
require_once '../includes/path_functions.php';

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
		$userID = $data['userID'];
		
		if($data['action'] == 'role') {
			$groupID = $data['groupID'];
			
			if($data['userType'] == 'active') {
				$qls->SQL->update('users', array('group_id' => $groupID), array('id' => array('=', $userID), 'AND', 'org_id' => array('=', $qls->user_info['org_id'])));
			} else if($data['userType'] == 'invitation') {
				$qls->SQL->update('invitations', array('group_id' => $groupID), array('id' => array('=', $userID), 'AND', 'org_id' => array('=', $qls->user_info['org_id'])));
			}
		} else if($data['action'] == 'delete') {
			if($data['userType'] == 'active') {
				$query = $qls->SQL->select('*', 'users', array('id' => array('=', $userID)));
				if($userID != $qls->user_info['id']) {			
					if($qls->SQL->num_rows($query)) {
						$user = $qls->SQL->fetch_assoc($query);
						if($user['original_org_id'] != $user['org_id']) {
							$originalOrgID = $user['original_org_id'];
							$originalGroupID = $user['original_group_id'];
							$qls->SQL->update('users', array('org_id' => $originalOrgID, 'group_id' => $originalGroupID), array('id' => array('=', $userID), 'AND', 'org_id' => array('=', $qls->user_info['org_id'])));
						} else {
							$qls->Admin->remove_user($userID);
						}
					} else {
						$errMsg = 'User ID does not exist.';
						array_push($validate->returnData['error'], $errMsg);
					}
				} else {
					$errMsg = 'Cannot remove yourself.';
					array_push($validate->returnData['error'], $errMsg);
				}
			} else if($data['userType'] == 'invitation') {
				$qls->SQL->delete('invitations', array('id' => array('=', $userID), 'AND', 'org_id' => array('=', $qls->user_info['org_id']), 'AND', 'used' => array('=', 0)));
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
