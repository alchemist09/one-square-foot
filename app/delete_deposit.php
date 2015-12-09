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
	   (isset($_GET['type']) && !empty($_GET['type']))){
		$tenant_id = (int)$_GET['tid'];
		$type = $_GET['type'];
		if(!is_int($tenant_id)){
			$mesg = "Deposit could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} elseif(!valid_deposit($type)){
			$mesg = "Deposit could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");
		} else {
			switch($type){
				case "house":
				$deposit = Deposit::findByTenantId($tenant_id);
				break;
				
				case "kplc":
				$deposit = DepositKPLC::findByTenantId($tenant_id);
				break;
				
				case "eldowas":
				$deposit = DepositEldowas::findByTenantId($tenant_id);
				break;
			}
			if($deposit->delete()){
				$mesg = "Deposit deleted";
				$session->message($mesg);
				redirect_to("tenant_deposit.php?tid={$tenant_id}");	
			} else {
				$mesg = "An error occured preventing the deposit from being deleted";
				$session->message($mesg);
				redirect_to("tenant_deposit.php?tid={$tenant_id}");	
			}
		}
	}

?>