<?php
$_SERVER = array('DOCUMENT_ROOT' => '/var/www/html');
define('QUADODO_IN_SYSTEM', true);
require_once $_SERVER['DOCUMENT_ROOT'].'/app/includes/header.php';

$query = $qls->SQL->select('*', 'email_queue', array('sent' => array('=', 0)));
while($row = $qls->SQL->fetch_assoc($query)) {
	$id = $row['id'];
	$recipient = $row['recipient'];
	$sender = $row['sender'];
	$subject = $row['subject'];
	$msg = $row['message'];
	
	$qls->PHPmailer->addAddress($recipient, '');
	$qls->PHPmailer->setFrom($sender, '');
	$qls->PHPmailer->clearReplyTos();
	$qls->PHPmailer->addReplyTo($sender, '');
	$qls->PHPmailer->Subject = $subject;
	$qls->PHPmailer->msgHTML($msg);
	$qls->PHPmailer->send();
	$qls->PHPmailer->clearAllRecipients();
	
	$qls->SQL->update('email_queue', array('sent' => 1), array('id' => array('=', $id)));
}

?>
