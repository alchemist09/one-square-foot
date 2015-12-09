<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('delete_tenant')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$session->message("Delete failed. An invalid value was sent through the URL");
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);
			if($tenant->deleteTenant()){
				$session->message("Tenant deleted");
				redirect_to("tenants.php");	
			} else {
				$session->message("An error occured preventing the tenant from being deleted");
				redirect_to("tenants.php");	
			}
		}
	}

?>