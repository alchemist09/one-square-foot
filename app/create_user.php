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
	if(isset($_POST['submit'])){
		if(empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['username']) ||
		   empty($_POST['passwd']) || empty($_POST['roles'])){
			$err = "Form fields marked with an asterix are required";	
		} else {
			/*echo var_dump($_POST);
			echo 'Number of Roles: ';
			echo count($_POST['roles']);
			echo '<br />';*/
			$fname    = $_POST['fname'];
			$lname    = $_POST['lname'];
			$username = $_POST['username'];
			$password = $_POST['passwd'];
			$user = new User();
			$user->setFirstName($fname);
			$user->setLastName($lname);
			$user->setUserName($username);
			$user->setPassword($password);
			if($user->createAccount($_POST['roles'])){
				$mesg = "User account created";
				$session->message($mesg);
				redirect_to("view_users.php");
				//echo var_dump($user);	
			} else {
				$err = "An error occured preventing the user account from being created";
				//echo var_dump($user);
			}
		}
	}
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    	<h3>Actions</h3>
        <div id="side-bar">
		<?php
            $actions = array("view_users" => "View Users", "view_roles" => "Roles of Users", "manage_perms" => "Manage Permissions", "view_logs" => "View Logs", "admin" => "Go Back");
            echo create_action_links($actions);
        ?>
        </div>
    	<div id="main-content">
        <h2>Create a User Account</h2>
        
        <?php if(!empty($mesg)) { echo output_message($mesg); } ?>
        <?php if(!empty($err)) { echo display_error($err); } ?>
        
        <a id="btn-back" href="admin.php" title="go back">&laquo;Back</a>
        
    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <legend>User Details</legend>
        <fieldset>
        <table cellpadding="5">
        <tr>
        	<td><label for="fname">First Name
            <span class="required-field">*</span></label></td>
            <td><input type="text" name="fname" id="fname" /></td>
        </tr>
        <tr>
        	<td><label for="lname">Last Name
            <span class="required-field">*</span></label></td>
            <td><input type="text" name="lname" id="lname" /></td>
        </tr>
        <tr>
        	<td><label for="username">Username
            <span class="required-field">*</span></label></td>
            <td><input type="text" name="username" id="username" /></td>
        </tr>
        <tr>
        	<td><label for="passwd">Password
            <span class="required-field">*</span></label></td>
            <td><input type="password" name="passwd" id="passwd" /></td>
        </tr>
        </table>
        </fieldset>
        <legend>User Role(s)</legend>
        <fieldset>
        <p>Roles<span class="required-field">*</span>:</p>
        <p>
        	<label><input type="checkbox" name="roles[]" value="1">Administrator</label><br />
            <label><input type="checkbox" name="roles[]" value="2">Manager</label><br />
            <label><input type="checkbox" name="roles[]" value="3">Employee</label>
        </p>
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