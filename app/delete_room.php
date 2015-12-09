<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if(isset($_GET['rid']) && !empty($_GET['rid'])){
		$room_id = (int)$_GET['rid'];
		if(!is_int($room_id)){
			$session->message("Delete failed. An invalid value was sent through the URL");
			redirect_to("rooms.php");
		} else {
			$room = Room::findById($room_id);
			if($room->delete()){
				$session->message("Room deleted");
				redirect_to("rooms.php");	
			} else {
				$session->message("An error occured preventing the room from being deleted");
				redirect_to("rooms.php");	
			}
		}
	}

?>