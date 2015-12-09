<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	$permissions = array('admin');	
	try {
		$ac->checkPermissions($permissions);	
	} catch(Exception $e){
		$mesg = $e->getMessage();
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if(isset($_GET['id']) && !empty($_GET['id'])){
		$exp_id = (int)$_GET['id'];
		if(!is_int($exp_id)){
			$mesg = "Expense could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("expenses.php");	
		} else {
			$exp = Expense::findById($exp_id);
			if(is_null($exp)){
				$mesg = "Delete failed. Details of expense could not be found";
				$session->message($mesg);
				redirect_to("expenses.php");	
			} else {
				if($exp->delete()){
					$mesg = "Expenses deleted";
					$session->message($mesg);
					redirect_to("expenses.php");	
				} else {
					$mesg = "An error occured preventing the expense from deleted";
					$session->message($mesg);
					redirect_to("expenses.php");	
				}
			}
		}
	}

?>