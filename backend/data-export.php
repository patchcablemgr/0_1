<?php
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';
$qls->Security->check_auth_page('administrator.php');

// Templates
$templateArray = array();
$query = $qls->app_SQL->select('*', 'table_object_templates');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	if($row['id'] != 1 and $row['id'] != 2 and $row['id'] != 3)
	$templateArray[$row['id']] = $row;
}

// Categories
$categoryArray = array();
$query = $qls->app_SQL->select('*', 'table_object_category');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	$categoryArray[$row['id']] = $row;
}

// Template Compatibility
$templateEnclosureArray = array();
$query = $qls->app_SQL->select('*', 'table_object_compatibility');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	$templateID = $row['template_id'];
	$templateFace = $row['side'];
	$templateDepth = $row['depth'];
	
	if($row['partitionType'] == 'Enclosure') {
		if(!array_key_exists($templateID, $templateEnclosureArray)) {
			$templateEnclosureArray[$templateID] = array(
				$templateFace => array(
					$templateDepth => $row
			));
		} else if(!array_key_exists($templateFace, $templateEnclosureArray[$templateID])) {
			$templateEnclosureArray[$templateID][$templateFace] = array(
				$templateDepth => $row
			);
		} else {
			$templateEnclosureArray[$templateID][$templateFace][$templateDepth] = $row;
		}
	}
}

// Cabinet Adjacencies
$envAdjArray = array();
$query = $qls->app_SQL->select('*', 'table_cabinet_adj');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	$envAdjArray[$row['left_cabinet_id']]['right'] = $row['right_cabinet_id'];
	$envAdjArray[$row['right_cabinet_id']]['left'] = $row['left_cabinet_id'];
}

// Objects
$objectArray = array();
$insertArray = array();
$query = $qls->app_SQL->select('*', 'table_object');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	if($row['template_id'] != 1 and $row['template_id'] != 2 and $row['template_id'] != 3) {
		if($row['parent_id'] == 0) {
			$objectArray[$row['id']] = $row;
		} else {
			$parentID = $row['parent_id'];
			$parentFace = $row['parent_face'];
			$parentDepth = $row['parent_depth'];
			$encX = $row['insertSlotX'];
			$encY = $row['insertSlotY'];
			
			if(!array_key_exists($parentID, $insertArray)) {
				$insertArray[$parentID] = array(
					$parentFace => array(
						$parentDepth => array(
							$encX => array(
								$encY => null
				))));
			} else if(!array_key_exists($parentFace, $insertArray[$parentID])) {
				$insertArray[$parentID][$parentFace] = array(
					$parentDepth => array(
						$encX => array(
							$encY => null
				)), 'enclosureCount' => 0);
			} else if(!array_key_exists($parentDepth, $insertArray[$parentID][$parentFace])) {
				$insertArray[$parentID][$parentFace][$parentDepth] = array(
					$encX => array(
						$encY => null
				));
			} else if(!array_key_exists($encX, $insertArray[$parentID][$parentFace][$parentDepth])) {
				$insertArray[$parentID][$parentFace][$parentDepth][$encX] = array(
					$encY => null
				);
			}
			
			$insertArray[$parentID][$parentFace]['enclosureCount'] = $insertArray[$parentID][$parentFace]['enclosureCount'] + 1;
			$insertArray[$parentID][$parentFace][$parentDepth][$encX][$encY] = $row;
		}
	}
}
		
// Open ZIP File
$zip = new ZipArchive();
$zipFilename = $_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/export.zip';
if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

// ####### Cabinets #######
// Create File
$fileCabinets = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinets.csv', 'w');

$csvHeader = array(
	'Name',
	'Type',
	'RU Size',
	'Adj Left',
	'Adj Right',
	'*Original Cabinet'
);

fputcsv($fileCabinets,$csvHeader);

$envTreeArray = array();
$query = $qls->app_SQL->select('*', 'env_tree');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	if($row['type'] != 'floorplan') {
		$envTreeArray[$row['id']] = $row;
	}
}

foreach($envTreeArray as &$entry) {
	$parentID = $entry['parent'];
	$nameString = $entry['name'];
	while($parentID != '#') {
		$nameString = $envTreeArray[$parentID]['name'].'.'.$nameString;
		$parentID = $envTreeArray[$parentID]['parent'];
	}
	$entry['nameString'] = $nameString;
}

$csvArray = array();
foreach($envTreeArray as $location) {
	$size = $location['type'] == 'cabinet' ? $location['size'] : '';
	$adjLeft = isset($envAdjArray[$location['id']]) ? $envTreeArray[$envAdjArray[$location['id']]['left']]['nameString'] : '';
	$adjRight = isset($envAdjArray[$location['id']]) ? $envTreeArray[$envAdjArray[$location['id']]['right']]['nameString'] : '';
	$line = array(
		$location['nameString'],
		$location['type'],
		$size,
		$adjLeft,
		$adjRight,
		$location['nameString']
	);
	$csvArray[$location['nameString']] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileCabinets, $line);
}

// ####### Cabinet Cable Paths #######
// Create File
$fileCabinetCablePaths = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinet Cable Paths.csv', 'w');

$csvHeader = array(
	'Cabinet A',
	'Cabinet B',
	'Distance (m.)',
	'Notes'
);
fputcsv($fileCabinetCablePaths, $csvHeader);

$csvArray = array();
$query = $qls->app_SQL->select('*', 'table_cable_path');
while($row = $qls->app_SQL->fetch_assoc($query)) {
	$line = array(
		$envTreeArray[$row['cabinet_a_id']]['nameString'],
		$envTreeArray[$row['cabinet_b_id']]['nameString'],
		$row['distance']*.001,
		$row['notes']
	);
	$csvArray[$envTreeArray[$row['cabinet_a_id']]['nameString']] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileCabinetCablePaths, $line);
}

// ####### Cabinet Objects #######
// Create File
$fileCabinetObjects = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinet Objects.csv', 'w');

$csvHeader = array(
	'Name',
	'Cabinet',
	'**Template',
	'RU',
	'Cabinet Face',
	'*Original Object'
);
fputcsv($fileCabinetObjects, $csvHeader);

$csvArray = array();
foreach($objectArray as $object) {
	$name = $object['name'];
	$cabinet = $envTreeArray[$object['env_tree_id']]['nameString'];
	$template = $templateArray[$object['template_id']]['templateName'];
	$RUSize = $templateArray[$object['template_id']]['templateRUSize'];
	$topRU = $object['RU'];
	$bottomRU = $topRU - ($RUSize - 1);
	$cabinetFace = $object['cabinet_front'] == 0 ? 'Front' : 'Rear';
	$original = $cabinet.'.'.$name;
	
	$line = array(
		$name,
		$cabinet,
		$template,
		$bottomRU,
		$cabinetFace,
		$original
	);
	$csvArray[$original] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileCabinetObjects, $line);
}

// ####### Object Inserts #######
// Create File
$fileObjectInserts = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Object Inserts.csv', 'w');

$csvHeader = array(
	'**Object',
	'**Face',
	'**Slot',
	'Insert Name',
	'**Insert Template',
	'*Original Insert'
);
fputcsv($fileObjectInserts, $csvHeader);

$csvArray = array();
foreach($objectArray as $object) {
	$templateID = $object['template_id'];
	
	if(array_key_exists($templateID, $templateEnclosureArray)) {
		$objectID = $object['id'];
		$cabinetID = $object['env_tree_id'];
		$objectName = $object['name'];
		$parentID = $object['parent_id'];
		$parentFace = $object['parent_id'];
		$parentDepth = $object['parent_id'];
		$slotX = $object['insertSlotX'];
		$slotY = $object['insertSlotY'];
		$cabinetNameString = $envTreeArray[$cabinetID]['nameString'];
		$objectNameString = $cabinetNameString.'.'.$objectName;
	
		foreach($templateEnclosureArray[$templateID] as $face=>$templateFace) {
			$faceString = $face == 0 ? 'Front' : 'Rear';
			
			foreach($templateFace as $depth=>$templatePartition) {

				for($y=0; $y<$templatePartition['encLayoutY']; $y++) {
					for($x=0; $x<$templatePartition['encLayoutX']; $x++) {

						$enc = $depth;
						$row = chr($y+65);
						$col = $x+1;
						$slotID = 'Enc'.$enc.'Slot'.$row.$col;
						$line = array(
							$objectNameString,
							$faceString,
							$slotID
						);
						
						if(isset($insertArray[$objectID][$face][$depth][$x][$y])) {
							$insert = $insertArray[$objectID][$face][$depth][$x][$y];
							$insertName = $insert['name'];
							$insertTemplateID = $insert['template_id'];
							$insertTemplateName = $templateArray[$insertTemplateID]['templateName'];
							array_push($line, $insertName);
							array_push($line, $insertTemplateName);
							array_push($line, $objectNameString.'.'.$faceString.'.'.$slotID.'.'.$insertName);
						} else {
							array_push($line, '');
							array_push($line, '');
							array_push($line, '');
						}
						
						if(!array_key_exists($objectNameString, $csvArray)) {
							$csvArray[$objectNameString] = array();
						}
						
						array_push($csvArray[$objectNameString], $line);
					}
				}
			}
		}
	}
}

ksort($csvArray);
foreach($csvArray as $object) {
	foreach($object as $encSlot) {
		fputcsv($fileObjectInserts, $encSlot);
	}
}

// ####### Templates #######
// Create File
$fileTemplates = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Templates.csv', 'w');

$csvHeader = array(
	'Name',
	'Category',
	'*Original Template',
	'**Type',
	'**Function',
	'**RU Size',
	'**Mount Config',
	'**Template Structure'
);
fputcsv($fileTemplates, $csvHeader);

$csvArray = array();
foreach($templateArray as $template) {
	$templateID = $template['id'];
	$templateCategoryID = $template['templateCategory_id'];
	$templateName = $template['templateName'];
	$templateCategoryName = $categoryArray[$templateCategoryID]['name'];
	$templateType = $template['templateType'];
	$templateFunction = $template['templateFunction'];
	$templateRUSize = $template['templateRUSize'];
	$templateMountConfig = $template['templateMountConfig'];
	$sizeX = $template['templateEncLayoutX'] ? $template['templateEncLayoutX'] : null;
	$sizeY = $template['templateEncLayoutY'] ? $template['templateEncLayoutY'] : null;
	$parentH = $template['templateHUnits'] ? $template['templateHUnits'] : null;
	$parentV = $template['templateVUnits'] ? $template['templateVUnits'] : null;
	$templateStructure = json_decode($template['templatePartitionData'], true);
	$templateFrontImage = $template['frontImage'] ? $template['frontImage'] : null;
	$templateRearImage = $template['rearImage'] ? $template['rearImage'] : null;
	$templateJSON = json_encode(array(
		'sizeX' => $sizeX,
		'sizeY' => $sizeY,
		'parentH' => $parentH,
		'parentV' => $parentV,
		'frontImage' => $templateFrontImage,
		'rearImage' => $templateRearImage,
		'structure' => $templateStructure,
	));
	if($templateMountConfig !== null) {
		$templateMountConfigString = $templateMountConfig == 0 ? '2-Post' : '4-Post';
	} else {
		$templateMountConfigString = 'N/A';
	}
	
	$line = array(
		$templateName,
		$templateCategoryName,
		$templateName,
		$templateType,
		$templateFunction,
		$templateRUSize,
		$templateMountConfigString,
		$templateJSON
	);
	
	$csvArray[$templateName] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileTemplates, $line);
}

// ####### Categories #######
// Create File
$fileCategories = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Categories.csv', 'w');

$csvHeader = array(
	'Name',
	'Color',
	'Default',
	'*Original Category'
);
fputcsv($fileCategories, $csvHeader);

$csvArray = array();
foreach($categoryArray as $category) {
	$categoryID = $category['id'];
	$categoryName = $category['name'];
	$categoryColor = $category['color'];
	$categoryDefault = $category['defaultOption'] == 1 ? 'X' : '';
	
	$line = array(
		$categoryName,
		$categoryColor,
		$categoryDefault,
		$categoryName
	);
	
	$csvArray[$categoryName] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileCategories, $line);
}

// ####### Connections #######
// Create File
$fileConnections = fopen($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Connections.csv', 'w');

$csvHeader = array(
	'PortA',
	'CableA ID',
	'CableA Connector Type',
	'PortB',
	'CableB ID',
	'CableB Connector Type',
	'Media Type',
	'Length'
);
fputcsv($fileConnections, $csvHeader);

$csvArray = array();
foreach($qls->App->inventoryAllArray as $connection) {
	$aObjID = $connection['a_object_id'];
	$aObjFace = $connection['a_object_face'];
	$aObjDepth = $connection['a_object_depth'];
	$aObjPort = $connection['a_port_id'];
	$aCode39 = $connection['a_code39'] ? $connection['a_code39'] : 'None';
	$aConnectorID = $connection['a_connector'];
	$aConnector = $aConnectorID ? $qls->App->connectorTypeValueArray[$aConnectorID]['name'] : 'None';
	
	$bObjID = $connection['b_object_id'];
	$bObjFace = $connection['b_object_face'];
	$bObjDepth = $connection['b_object_depth'];
	$bObjPort = $connection['b_port_id'];
	$bCode39 = $connection['b_code39'] == 0 ? 'None' : $connection['b_code39'];
	$bConnectorID = $connection['b_connector'];
	$bConnector = $bConnectorID ? $qls->App->connectorTypeValueArray[$bConnectorID]['name'] : 'None';
	
	if($aObjID) {
		error_log('A');
		$aObjectName = $qls->App->getPortNameString($aObjID, $aObjFace, $aObjDepth, $aObjPort);
	} else {
		$aObjectName = 'None';
	}
	
	if($bObjID) {
		error_log('B');
		$bObjectName = $qls->App->getPortNameString($bObjID, $bObjFace, $bObjDepth, $bObjPort);
	} else {
		$bObjectName = 'None';
	}
	
	$mediaTypeID = $connection['mediaType'];
	$mediaType = $mediaTypeID ? $qls->App->mediaTypeValueArray[$mediaTypeID]['name'] : 'None';
	$length = $connection['length'];
	if($mediaTypeID and $length) {
		$lengthString = $qls->App->calculateCableLength($mediaTypeID, $length, true);
	} else {
		$lengthString = 'None';
	}
	
	$line = array(
		$aObjectName,
		$aCode39,
		$aConnector,
		$bObjectName,
		$bCode39,
		$bConnector,
		$mediaType,
		$lengthString
	);
	
	$csvArray[$aObjectName] = $line;
}

foreach($qls->App->populatedPortAllArray as $port) {
	$objID = $port['object_id'];
	$objFace = $port['object_face'];
	$objDepth = $port['object_depth'];
	$objPort = $port['port_id'];
	error_log('here');
	$objectName = $qls->App->getPortNameString($objID, $objFace, $objDepth, $objPort);
	
	$line = array(
		$objectName,
		'None',
		'None',
		'None',
		'None',
		'None',
		'None',
		'None'
	);
	
	$csvArray[$objectName] = $line;
}

ksort($csvArray);
foreach($csvArray as $line) {
	fputcsv($fileConnections, $line);
}




fclose($fileCabinets);
fclose($fileCabinetCablePaths);
fclose($fileCabinetObjects);
fclose($fileObjectInserts);
fclose($fileTemplates);
fclose($fileCategories);
fclose($fileConnections);

$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Categories.csv', '01 - Categories.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Templates.csv', '02 - Templates.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinets.csv', '03 - Cabinets.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinet Cable Paths.csv', '04 - Cabinet Cable Paths.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Cabinet Objects.csv', '05 - Cabinet Objects.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Object Inserts.csv', '06 - Object Inserts.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/Connections.csv', '07 - Connections.csv');
$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/README.txt', 'README.txt');

$zip->close();

$yourfile = $_SERVER['DOCUMENT_ROOT'].'/app/userDownloads/export.zip';

$file_name = basename($yourfile);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename='.$file_name);
header('Content-Length: '.filesize($yourfile));

readfile($yourfile);
exit;
?>
