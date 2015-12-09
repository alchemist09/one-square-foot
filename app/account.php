<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	$user_id = (int)$_SESSION['user_id'];
	$user = User::findById($user_id);
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("account" => "Account Details", "edit_account" => "Edit Account", "change_pass" => "Change Password");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>My Account Details</h2>
    
    <?php
		$mesg = $session->message();
		echo output_message($mesg);
		echo $user;
	?>
    
    </div>
    </div>

<?php include_layout_template("admin_footer.php"); ?>