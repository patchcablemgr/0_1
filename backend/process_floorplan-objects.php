<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
$qls->Security->check_auth_page('operator.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once('../includes/Validate.class.php');
	$validate = new Validate($qls);
	$validate->returnData['success'] = array();
	
	if ($validate->returnData['active'] == 'inactive') {
		echo json_encode($validate->returnData);
		return;
	}

	$data = json_decode($_POST['data'], true);
	validate($data, $validate, $qls);
	
	if (!count($validate->returnData['error'])){
		$action = $data['action'];
		
		if($action == 'add') {
			$nodeID = $data['nodeID'];
			$name = $qls->App->findUniqueName($nodeID, 'table_object');
			$type = $data['type'];
			$positionTop = $data['positionTop'];
			$positionLeft = $data['positionLeft'];
			
			if($type == 'walljack') {
				$templateID = 1;
			} else if($type == 'wap') {
				$templateID = 2;
			} else if($type == 'device') {
				$templateID = 3;
			}
			
			//Insert data into DB
			$qls->app_SQL->insert('table_object', array(
					'env_tree_id',
					'name',
					'template_id',
					'position_top',
					'position_left'
				), array(
					$nodeID,
					$name,
					$templateID,
					$positionTop,
					$positionLeft
				)
			);
			
			//This tells the client what the new object_id is
			$validate->returnData['success']['id'] = $qls->app_SQL->insert_id();
			$validate->returnData['success']['name'] = $name;
		} else if($action == 'editLocation') {
			$objectID = $data['objectID'];
			$positionTop = $data['positionTop'];
			$positionLeft = $data['positionLeft'];
			
			//Update DB entry
			$qls->app_SQL->update('table_object', array(
				'position_top' => $positionTop,
				'position_left' => $positionLeft
				),
				'id = '.$objectID
			);
		} else if($action == 'editName') {
			$objectID = $data['objectID'];
			$name = $data['value'];
			
			//Update DB entry
			$qls->app_SQL->update('table_object', array(
				'name' => $name
				),
				'id = '.$objectID
			);
		} else if($action == 'delete') {
			$objectID = $data['objectID'];
			
			//Delete DB entry
			$qls->app_SQL->delete('table_object', array('id' => array('=', $objectID)));
			$qls->app_SQL->delete('table_object_peer', array('a_id' => array('=', $objectID)));
			$qls->app_SQL->delete('table_populated_port', array('object_id' => array('=', $objectID)));
			$query = $qls->app_SQL->select('*', 'table_inventory', array('a_object_id' => array('=', $objectID), 'OR', 'b_object_id' => array('=', $objectID)));
			while($row = $qls->app_SQL->fetch_assoc($query)) {
				if($row['a_id'] or $row['b_id']) {
					$attrArray = array('a', 'b');
					foreach($attrArray as $attrPrefix) {
						if($row[$attrPrefix.'_object_id'] == $objectID) {
							$set = array(
								$attrPrefix.'_object_id' => 0,
								$attrPrefix.'_port_id' => 0,
								$attrPrefix.'_object_face' => 0,
								$attrPrefix.'_object_depth' => 0
							);
							$qls->app_SQL->update('table_inventory', $set, array('id' => array('=', $row['id'])));
						}
					}
				} else {
					$qls->app_SQL->delete('table_inventory', array('id' => array('=', $row['id'])));
				}
			}
		} else {
			$errorMsg = 'Invalid action.';
			array_push($validate->returnData['error'], $errorMsg);
		}
	}
	echo json_encode($validate->returnData);
	return;
}

function validate($data, &$validate, &$qls){
	
	//Validate action
	$actionArray = array('add', 'editLocation', 'editName', 'delete');
	if($validate->validateInArray($data['action'], $actionArray, 'action')) {
		$action = $data['action'];
		
		if($action == 'add') {
			//Validate object type
			$typeArray = array('walljack', 'wap', 'device');
			$validate->validateInArray($data['type'], $typeArray, 'type');
			
			//Validate positions
			$validate->validateID($data['positionTop'], 'object ID');
			$validate->validateID($data['positionLeft'], 'object position');
			
			//Validate node ID
			$validate->validateID($data['nodeID'], 'cabinet ID');
		} else if($action == 'editLocation') {
			//Validate positions
			$validate->validateID($data['positionTop'], 'object position');
			$validate->validateID($data['positionLeft'], 'object position');
			
			//Validate object ID
			$validate->validateID($data['objectID'], 'object ID');
		} else if($action == 'editName') {
			//Validate object ID
			$validate->validateID($data['objectID'], 'object ID');
			
			//Validate object name
			$validate->validateObjectName($data['value'], 'object name');
		} else if($action == 'editName') {
			//Validate object ID
			$validate->validateID($data['objectID'], 'object ID');
		}
	}
	
	/*
	switch($data['action']){
		case 'add':
			//Validate cabinet ID
			$validate->validateID($data['cabinetID'], 'cabinet ID');
	
			//Validate cabinet RU
			$validate->validateID($data['RU'], 'cabinet RU');
		
			//Validate objectID
			$validate->validateID($data['objectID'], 'object ID');
			
		case 'delete':
			//Validate objectID
			$validate->validateID($data['objectID'], 'object ID');
			break;
			
		case 'updateObject':
			//Validate object ID
			$validate->validateID($data['objectID'], 'object ID');
			
			//Validate cabinet RU
			$validate->validateRUSize($data['RU']);
			break;
			
		case 'updateInsert':
			//Validate object ID
			$validate->validateID($data['objectID'], 'object ID');
			
			//Validate parent ID
			$validate->validateID($data['parent_id'], 'parent ID');
			
			//Validate parent ID
			$validate->validateID($data['parent_depth'], 'parent depth');
			
			//Validate insert slot X
			$validate->validateID($data['insertSlotX'], 'insert slot X');
			
			//Validate insert slot Y
			$validate->validateID($data['insertSlotY'], 'insert slot Y');
			break;
			
		case 'edit':
			//Validate objectID
			if($validate->validateID($data['objectID'], 'object ID')) {
				
				//Validate object existence
				$table = 'table_object';
				$where = array('id' => array('=', $data['objectID']));
				if($object = $validate->validateExistenceInDB($table, $where, 'Object does not exist.')) {
					$parentID = $object['parent_id'];
					//Validate objectName
					$cabinetID = $object['env_tree_id'];
					if($validate->validateNameText($data['value'], 'object name')) {
						$table = 'table_object';
						if($parentID) {
							$where = array('name' => array('=', $data['value']), 'AND', 'env_tree_id' => array('=', $cabinetID), 'AND', 'parent_id' => array('=', $parentID));
						} else {
							$where = array('name' => array('=', $data['value']), 'AND', 'env_tree_id' => array('=', $cabinetID));
						}
						$validate->validateDuplicate($table, $where, 'Duplicate object name found in the same cabinet.');
					}
				}
			}
			
			break;
	}
	*/
	return;
}
?>
