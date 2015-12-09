<?php
	require_once('../lib/init.php');
	$session->logout();
	$_SESSION = array();
	$session->message('You have signed out from your account. You will need to login to continue using the application');
	redirect_to('../index.php');

?>