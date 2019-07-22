<?php
	$colorTable = array();
	$query = $qls->shared_SQL->select('*', 'table_cable_color');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$colorTable[$row['value']] = $row;
	}
	
	$productTable = array();
	$query = $qls->shared_SQL->select('*', 'table_cable_connectorOptions');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$productTable[$row['value']] = $row;
	}
	
	$connectorTable = array();
	$query = $qls->shared_SQL->select('*', 'table_cable_connectorType');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$connectorTable[$row['value']] = $row;
	}
	
	$mediaTypeTable = array();
	$query = $qls->shared_SQL->select('*', 'table_mediaType');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$mediaTypeTable[$row['value']] = $row;
	}
	
	$lengthTable = array();
	$query = $qls->shared_SQL->select('*', 'table_cable_length');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$lengthTable[$row['value']] = $row;
	}
	
	$mediaCategoryTypeTable = array();
	$query = $qls->shared_SQL->select('*', 'table_mediaCategoryType');
	while($row = $qls->shared_SQL->fetch_assoc($query)) {
		$mediaCategoryTypeTable[$row['value']] = $row;
	}
?>