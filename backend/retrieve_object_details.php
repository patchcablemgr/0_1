<?php
define('QUADODO_IN_SYSTEM', true);
require_once '../includes/header.php';
$qls->Security->check_auth_page('user.php');

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
		$objectFace = $data['objFace'];
		$cabinetFace = $data['cabinetFace'];
		$objectID = $data['objID'];
		$partitionDepth = $data['partitionDepth'];
		
		if ($data['page'] == 'build') {
			//Retreive object info
			$objectInfo = $qls->SQL->select('*', 'app_object', 'id='.$objectID);
			$objectInfo = $qls->SQL->fetch_assoc($objectInfo);
			$templateID = $objectInfo['template_id'];
			$objectName = $objectInfo['name'];
		} else {
			$templateID = $objectID;
			$objectName = $trunkedTo = 'N/A';
		}
		
		//Retrieve partition info
		$query = $qls->SQL->select('*', 'app_object_compatibility', array('template_id' => array('=', $templateID), 'AND', 'side' => array('=', $objectFace), 'AND', 'depth' => array('=', $partitionDepth)));
		$partitionData = $qls->SQL->fetch_assoc($query);
		$partitionType = $partitionData['partitionType'];
		$portNameFormat = $portTotal = false;
		$peerIDArray = array();
		
		if($partitionType == 'Connectable'){
			require_once '../includes/path_functions.php';
			$portNameFormat = json_decode($partitionData['portNameFormat'], true);
			$portLayoutX = $partitionData['portLayoutX'];
			$portLayoutY = $partitionData['portLayoutY'];
			$portTotal = $portLayoutX * $portLayoutY;
			$portIndexFirst = 0;
			$portIndexLast = $portTotal - 1;
			$portNameFirst = $qls->App->generatePortName($portNameFormat, $portIndexFirst, $portTotal);
			if($portTotal > 1) {
				error_log('1: '.$portTotal);
				$portNameLast = '&nbsp;&#8209;&nbsp;'.$qls->App->generatePortName($portNameFormat, $portIndexLast, $portTotal);
			} else {
				error_log('2: '.$portTotal);
				$portNameLast = '';
			}
			$portRange = $portNameFirst.$portNameLast;
			$portProperties = getPortProperties($qls);
			$portType = $portProperties['portType'][$partitionData['portType']];
			$portOrientation = $portProperties['portOrientation'][$partitionData['portOrientation']];
			$mediaType = $partitionData['partitionFunction'] == 'Passive' ? $portProperties['mediaType'][$partitionData['mediaType']] : 'N/A';
			// Get peer ID
			$query = $qls->SQL->select('*', 'app_object_peer', '(a_id ='.$objectID.' AND a_face = '.$objectFace.' AND a_depth = '.$partitionDepth.') OR (b_id = '.$objectID.' AND b_face = '.$objectFace.' AND b_depth = '.$partitionDepth.')');
			if($qls->SQL->num_rows($query)) {
				$peerEntry = $qls->SQL->fetch_assoc($query);
				$peerAttrPrefix = $peerEntry['a_id'] == $objectID ? 'b' : 'a';
				$peerID = $peerEntry[$peerAttrPrefix.'_id'];
				$peerFace = $peerEntry[$peerAttrPrefix.'_face'];
				$peerDepth = $peerEntry[$peerAttrPrefix.'_depth'];
				$peerID = '3-'.$peerID.'-'.$peerFace.'-'.$peerDepth.'-0';
				array_push($peerIDArray, $peerID);
			}
			$trunkFlatPath = buildTrunkFlatPath($objectID, $objectFace, $partitionDepth, $qls);
			$trunkable = true;
		} else if($partitionType == 'Enclosure'){
			$portRange = $portType = $portOrientation = $mediaType = $trunkFlatPath = 'N/A';
			$trunkable = false;
		} else {
			// Generic partition... these won't be in the compatibility table so catch them with an else
			$partitionType = $portRange = $portType = $mediaType = $trunkFlatPath = 'N/A';
			$trunkable = false;
		}

		// Retrieve template info
		$templateInfo = $qls->SQL->select('*', 'app_object_templates', 'id='.$templateID);
		$templateInfo = $qls->SQL->fetch_assoc($templateInfo);
		
		if($templateInfo['templateType'] == 'Standard') {
			$mountConfig = $templateInfo['templateMountConfig'] == 0 ? '2-Post' : '4-Post';
			$RUSize = $templateInfo['templateRUSize'];
		} else if($templateInfo['templateType'] == 'Insert'){
			$mountConfig = $RUSize = 'N/A';
			$insertRUSize = $templateInfo['templateRUSize'];
		}
		
		$templateImgFilename = $objectFace == 0 ? $templateInfo['frontImage'] : $templateInfo['rearImage'];
		if($templateImgFilename !== null) {
			$templateImgExists = true;
			$templateImgAction = 'update';
			$templateImgPath = '/images/templateImages/'.$templateImgFilename;
			if($templateInfo['templateType'] == 'Standard') {
				$templateImgHeight = $RUSize * 25;
				$templateImgWidth = 100;
			} else if($templateInfo['templateType'] == 'Insert'){
				$templateImgHeight = round(($insertRUSize*25)/$templateInfo['templateEncLayoutY']);
				$templateImgWidth = round(($templateInfo['templateHUnits']*10)/$templateInfo['templateEncLayoutX']);
			}
		} else {
			$templateImgExists = false;
			$templateImgAction = 'upload';
			$templateImgPath = '';
			$templateImgHeight = 0;
			$templateImgWidth = 0;
		}
		
		// Retrieve category info
		$categoryArray = array();
		$result = $qls->SQL->select('*', 'app_object_category');
		while($row = $qls->SQL->fetch_assoc($result)){	
			array_push($categoryArray, array('value'=>$row['id'], 'text'=>$row['name']));
			if($row['id'] == $templateInfo['templateCategory_id']){
				$categoryID = $row['id'];
				$categoryName = $row['name'];
			}
		}
		
		// Compile response data
		$returnData = array(
			'objectName' => $objectName,
			'templateName' => $templateInfo['templateName'],
			'trunkedTo' => $trunkedTo,
			'categoryName' => $categoryName,
			'categoryArray' => $categoryArray,
			'categoryID' => $categoryID,
			'objectType' => $templateInfo['templateType'],
			'RUSize' => $RUSize,
			'function' => $templateInfo['templateFunction'],
			'mountConfig' => $mountConfig,
			'partitionType' => $partitionType,
			'portRange' => $portRange,
			'portTotal' => $portTotal,
			'portNameFormat' => $portNameFormat,
			'portType' => $portType,
			'mediaType' => $mediaType,
			'templateImgExists' => $templateImgExists,
			'templateImgAction' => $templateImgAction,
			'templateImgPath' => $templateImgPath,
			'templateImgHeight' => $templateImgHeight,
			'templateImgWidth' => $templateImgWidth,
			'trunkable' => $trunkable,
			'trunkFlatPath' => $trunkFlatPath,
			'peerIDArray' => $peerIDArray
		);
		
		$validate->returnData['success'] = $returnData;
	}
	echo json_encode($validate->returnData);
	return;
}

function validate($data, &$validate){
	//Validate page name
	$validate->validatePageName($data['page']);
	
	//Validate object ID
	$validate->validateObjectID($data['objID']);
	
	//Validate object face
	$validate->validateObjectFace($data['objFace']);

	//Validate partition depth
	$validate->validatePartitionDepth($data['partitionDepth']);
	
	return;
}

function getPortProperties(&$qls){
	$portProperties = array();
	
	$query = $qls->SQL->select('*', 'shared_object_portType');
	while($row = $qls->SQL->fetch_assoc($query)){
		$portProperties['portType'][$row['value']] = $row['name'];
	}
	
	$query = $qls->SQL->select('*', 'shared_object_portOrientation');
	while($row = $qls->SQL->fetch_assoc($query)){
		$portProperties['portOrientation'][$row['value']] = $row['name'];
	}
	
	$query = $qls->SQL->select('*', 'shared_mediaType');
	while($row = $qls->SQL->fetch_assoc($query)){
		$portProperties['mediaType'][$row['value']] = $row['name'];
	}
	return $portProperties;
}
?>
