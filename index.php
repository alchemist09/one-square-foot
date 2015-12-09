<?php require_once('lib/init.php'); ?>
<?php 
	if(!isset($session)) { 
		$session = new Session();
		$session->message();
	}
?>
<?php if($session->isLoggedIn()) { redirect_to("app/index.php"); } ?>
<?php
	if(isset($_POST['submit'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		if(empty($username) || empty($password)){
			$err = "Username or password cannot be empty";	
		} else {
			$found = User::authenticate($username, $password);
			if($found){
				$session->login($found,$ac);
				$session->message("Welcome {$username}");
				$session->sessionVar("user", "You are logged in as {$username}");
				redirect_to("app/index.php");
			} else {
				$err = "Username and/or password is incorrect. Please try again";	
			}	
		}
	} else {
		// Form not submitted
		$msg = "";	
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>App Real Estate</title>
<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
</head>

<body style="background:url(images/bkg_15.jpg);">
    
	<div id="login_form">
    	<h1 style="padding-left:80px;">Real Estate Logix</h1>
        <?php
			if(!empty($err)) { echo display_error($err); }
			$mesg = $session->message();
			if(!empty($mesg)) { echo output_message($mesg); }
		?>
        
    	<h2 style="padding-left:50px;">Sign In to Use Application</h2>
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table>
        <tr>
        	<td><label for="username">Username
            <span class="requiredField">*</span></td>
            <td><input type="text" name="username" id="username" size="40" /></td>
        </tr>
        <tr>
        	<td><label for="password">Password
            <span class="requiredField">*</span></td>
            <td><input type="password" name="password" id="password" size="40" /></td>
        </tr>
        <tr>
        	<td colspan="2" align="right"><input type="submit" name="submit" value="Login" /></td>
        </tr>
        </table>
        </form>
    </div>

</body>
</html>
