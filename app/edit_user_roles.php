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
			redirect_to('view_users.php');	
		} else {
			$user = User::findById($user_id);
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		echo var_dump($_POST);
		echo var_dump($user_id);
		if(empty($_POST['roles'])){
			$err = "You need to check the roles to assign to this user";	
		} else {
			//$user = User::findById($user_id);
			if($user->changeRoles($_POST['roles'])){
				$mesg = "Changes saved";
				$session->message($mesg);
				redirect_to("user_roles.php?uid={$user_id}");	
			} else {
				$err = "An error occured preventing the changes from being saved";	
			}
		}
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
	<h3>Actions</h3>
    <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_users" => "View Users",  "view_roles" => "Roles of Users", "admin" => "Go Back");
        echo create_action_links($actions);
    ?>
    </div>
	<div id="main-content">
    <h2>Edit Roles of <?php echo $user->getFullName(); ?></h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <p><strong>Check the boxes of the roles you would like to assign this user</strong></p> 
    
    <p><strong>NOTE:</strong>Kindly note that any new role(s) you assign to the user will ovewrite the current role(s) assigned to the user</p><br /><br />
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    <p>
        <label><input type="checkbox" name="roles[]" value="1">Administrator</label><br />
        <label><input type="checkbox" name="roles[]" value="2">Manager</label><br />
        <label><input type="checkbox" name="roles[]" value="3">Employee</label>
    </p>
    </tr>
    <tr>
    	<td><input type="submit" name="submit" value="Save" /></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>