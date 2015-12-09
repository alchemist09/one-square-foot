<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); } 
	if(!$ac->hasPermission('move_out')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Tenant could not be moved out from room. An invalid paramter was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);
			if($tenant->hasMovedOut()){
				$mesg = "Invalid operation. Tenant already moved out";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			}
			if($tenant->moveOut()){
				$mesg = "Tenant has been moved out";
				$session->message($mesg);
				redirect_to("tenants.php");
			} else {
				$mesg = "An error occured preventing the tenant from being moved out";
				$session->message($mesg);
				redirect_to("tenants.php");	
			}
		}
	}
	
?>