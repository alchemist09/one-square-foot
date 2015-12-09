<?php

	require_once('../lib/init.php');
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to('view_users.php');
	}
	
	if(isset($_GET['uid']) && !empty($_GET['uid'])){
		$user_id = (int)$_GET['uid'];
		if(!is_int($user_id)){
			$mesg = "User could not be deleted. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('view_users.php');	
		} else {
			$user = User::findById($user_id);
			if($user->delete()){
				$mesg = "User deleted";
				$session->message($mesg);
				redirect_to('view_users.php');	
			} else {
				$mesg = "An error occured preventing the user from being deleted";
				$session->message($mesg);
				redirect_to('view_users.php');	
			}
		}
	} 

?>