<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
$qls->Security->check_auth_page('administrator.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	$return = array('error' => '',
			'result' => ''
		);
	
	$data = json_decode($_POST['data'], true);
	$return['error'] = validate($data);
	if (count($return['error']) == 0){
		$return['result'] = "$0.00";
	}
	echo json_encode($return);
}

function validate($data){
	$error = [];
	//Validate addressID
	if (!isset($data['addressID'])){
		error_log('none');
		//array_push($error, array('alert' => 'Error: AddressID is required.'));
	} else {
		if (!preg_match('/^[0-9]+$/', $data['zip'])){
			array_push($error, array('alert' => 'Error: Invalid addressID.'));
		}
	}
	return $error;
}
?>
