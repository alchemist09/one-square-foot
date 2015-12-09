<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>    
<?php
	$user_id = $_SESSION['user_id'];
	$user = User::findById($user_id);
	/*echo var_dump($user);
	echo var_dump($user_id);*/
	if(isset($_POST['submit'])){
		//echo var_dump($_POST);	
		if(empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email'])){
			$err = "Form fields marked with an asterix are required";
		} else {
			$user->setFirstName($_POST['fname']);
			$user->setLastName($_POST['lname']);
			$user->setEmail($_POST['email']);
			if($user->save()){
				$mesg = "Changes saved";
				$session->message($mesg);
				redirect_to('account.php');	
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
        $actions = array("edit_account" => "Edit Account", "change_pass" => "Change Password");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Account Details</h2>
    <a id="btn-back" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo disply_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?uid=".$_SESSION['user_id']; ?>" method="post">
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
    	<td><label for="email">Email
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="email" id="email" value="<?php echo $user->getEmail(); ?>" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        <input type="submit" name="submit" value="Save Changes" />
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>