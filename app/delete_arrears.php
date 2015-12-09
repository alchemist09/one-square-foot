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
	   (isset($_GET['aid']) && !empty($_GET['aid'])) ){
		$tenant_id = (int)$_GET['tid'];
		$arrears_id = (int)$_GET['aid'];
		if(!is_int($tenant_id) || !is_int($arrears_id)){
			$mesg = "Arrears could not be edited. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$arrears = Arrears::findById($arrears_id);
			echo var_dump($arrears);
			if($arrears->deleteArrears()){
				$mesg = "Arrears deleted";
				$session->message($mesg);
				redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			}
		}
	}

?>