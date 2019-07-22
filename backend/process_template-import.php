<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
$qls->Security->check_auth_page('operator.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once('../includes/Validate.class.php');
	$validate = new Validate($qls);
	
	if ($validate->returnData['active'] == 'inactive') {
		echo json_encode($validate->returnData);
		return;
	}
	
	$data = json_decode($_POST['data'], true);
	validate($data, $validate);
	
	if (!count($validate->returnData['error'])){
		$templateID = $data['templateID'];
		
		$query = $qls->app_SQL->select('*', 'table_object_category', array('defaultOption' => array('=', 1)));
		$defaultCategory = $qls->app_SQL->fetch_assoc($query);
		$defaultCategoryName = $defaultCategory['name'];
		$defaultCategoryID = $defaultCategory['id'];
		
		$query = $qls->shared_SQL->select('*', 'table_template_object_templates', array('id' => array('=', $templateID)));
		$template = $qls->shared_SQL->fetch_assoc($query);
		
		$templateNameArray = array();
		$templateValueArray = array();
		foreach($template as $name => $value) {
			if($name != 'id') {
				array_push($templateNameArray, $name);
				if($name == 'templateCategory_id') {
					array_push($templateValueArray, $defaultCategoryID);
				} else {
					array_push($templateValueArray, $value);
				}
			}
		}
		
		$templateCompatibilityArray = array();
		$query = $qls->shared_SQL->select('*', 'table_template_object_compatibility', array('template_id' => array('=', $templateID)));
		while($row = $qls->shared_SQL->fetch_assoc($query)) {
			array_push($templateCompatibilityArray, $row);
		}
		
		$qls->app_SQL->insert('table_object_templates', $templateNameArray, $templateValueArray);
		$newTemplateID = $qls->app_SQL->insert_id();
		
		foreach($templateCompatibilityArray as $templateCompatibility) {
			$templateCompatibilityNameArray = array();
			$templateCompatibilityValueArray = array();
			foreach($templateCompatibility as $name => $value) {
				if($name != 'id') {
					array_push($templateCompatibilityNameArray, $name);
					if($name == 'template_id') {
						array_push($templateCompatibilityValueArray, $newTemplateID);
					} else {
						array_push($templateCompatibilityValueArray, $value);
					}
				}
			}
			
			$qls->app_SQL->insert('table_object_compatibility', $templateCompatibilityNameArray, $templateCompatibilityValueArray);
		}
		$validate->returnData['success'] = 'This template has been imported to the default category named '.$defaultCategoryName;
	}
	echo json_encode($validate->returnData);
	return;
}

function validate($data, &$validate){
	//Validate template ID
	$validate->validateID($data['templateID'], 'template ID');
}
?>
