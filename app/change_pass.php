<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	if(isset($_POST['submit'])){
		if(empty($_POST['curr_pass']) || empty($_POST['new_pass']) || 
		   empty($_POST['confirm_pass'])){
			$err = "Form fields marked with an asterix are requierd";	   
		} else {
			$user_id = (int)$_SESSION['user_id'];
			$user = User::findById($user_id);
			try {
				
				if($user->changePassword($_POST['curr_pass'], $_POST['new_pass'], $_POST['confirm_pass'])){
					$mesg = "Changes saved";
					$session->message($mesg);
					redirect_to('account.php');
				}
			} catch(Exception $e){
				$err = $e->getMessage();	
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
        $actions = array("account" => "Account Details", "edit_account" => "Edit Account", "change_pass" => "Change Password");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    	<h2>Change Account Password</h2>
        <a id="btn-back" href="account.php">&laquo;Back</a>
        
        <?php if(!empty($err)) { echo display_error($err); } ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table cellpadding="5">
        <tr>
        	<td><label for="curr_pass">Current Password
            <span class="required-field">*</span></label></td>
            <td><input type="password" name="curr_pass" id="curr_pass" /></td>
        </tr>
        <tr>
        	<td><label for="new_pass">New Password
            <span class="required-field">*</span></label></td>
            <td><input type="password" name="new_pass" id="new_pass" /></td>
        </tr>
        <tr>
        	<td><label for="confirm_pass">Confirm Password
            <span class="required-field">*</span></label></td>
            <td><input type="password" name="confirm_pass" id="confirm_pass" /></td>
        </tr>
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