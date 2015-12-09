<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('delete_rent')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if((isset($_GET['tid']) && !empty($_GET['tid'])) &&
	   (isset($_GET['id']) && !empty($_GET['id'])) ){
		$tenant_id = (int)$_GET['tid'];
		$payment_id = (int)$_GET['id'];
		if(!is_int($tenant_id) || !is_int($payment_id)){
			$mesg = "Rent payment could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');
		} else {
			$rent = Rent::findById($payment_id);
			if(is_null($rent)){
				$mesg = "Rent payment could not be found in the system";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			} else {
				if($rent->deletePayment()){
					$mesg = "Rent payment deleted";
					$session->message($mesg);
					redirect_to("tenant.php?tid={$tenant_id}");	
				} else {
					$mesg = "An error occured preventing the rent payment from deleted";
					$session->message($mesg);
					redirect_to("tenant.php?tid={$tenant_id}");
				}
			}
		}	
	}

?>