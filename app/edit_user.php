<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
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

	if(isset($_GET['uid']) && !empty($_GET['uid'])){
		$user_id = (int)$_GET['uid'];
		if(!is_int($user_id)){
			$session->message("User edit failed. An invalid value was sent throught the URL");
			redirect_to("view_users.php");	
		} else {
			$user = User::findById($user_id);	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$fname    = $_POST['fname'];
		$lname    = $_POST['lname'];
		$username = $_POST['username'];
		$password = $_POST['passwd'];
		
		if(empty($fname) || empty($lname) || empty($username) || empty($password)){
			$err = "Form fields marked with an asterix are required";  
		} else {
			$user->setFirstName($fname);
			$user->setLastName($lname);
			$user->setUserName($phone);
			$user->setPassword($password);
			
			if($user->save()){
				$session->message("The changes have been saved");
				redirect_to("view_users.php");	
			} else {
				$err = "An error occured preventing the changes from being saved";	
			}
		}
	} else {
		// Form not submitted
		$err = "";	
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_users" => "View Users",  "view_roles" => "Roles of Users", "view_logs" => "View Logs", "admin" => "Go Back");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <h2>Edit Details of User</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']; ?>" method="post">
    <legend>User Details</legend>
    <fieldset>
    <table cellpadding="5">
    <tr>
        <td><label for="fname">First Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="fname" id="fname" value="<?php echo $user->getFirstName(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="lname">Last Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="lname" id="lname" value="<?php echo $user->getLastName(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="username">Username
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="username" id="username" value="<?php echo $user->getUserName(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="passwd">Password
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="passwd" id="passwd" value="<?php echo $user->getPassword(); ?>" /></td>
    </tr>
    </table>
    </fieldset>
    <table cols="2" cellpadding="5">
    <tr>
        <td colspan="2" align="right">
            <input type="submit" name="submit" value="Save" />
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>