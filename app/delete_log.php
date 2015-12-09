<?php
	require_once("../lib/init.php");
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(isset($_GET['id']) && !empty($_GET['id'])){
		$log_id = (int)$_GET['id'];
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$sql = "DELETE FROM logger WHERE id = ".$mysqli->real_escape_string($log_id);
		$mysqli->query($sql);
		if($mysqli->affected_rows == 1){
			$mesg = "User activity deleted";
			$session->message($mesg);
			redirect_to("view_logs.php");	
		} else {
			$mesg = "An error occured preventing the log from being deleted";
			$session->message($mesg);
			redirect_to("view_logs.php");	
		}
	}

?>