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

	if(isset($_GET['pid']) && !empty($_GET['pid'])){
		$prop_id = (int)$_GET['pid'];
		if(!is_int($prop_id)){
			$mesg = "Property could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
		} else {
			$property = Property::findById($prop_id);
			if($property->delete()){
				$mesg = "Property deleted";
				$session->message($mesg);
				redirect_to("property.php");	
			} else {
				// Delete failed
				$mesg  = "An error occured preventing the property from being deleted. ";
				$mesg .= "Please try again later";
				$session->message($mesg);
				redirect_to("property.php");	
			}
		}
	}

?>