<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
	/**$permissions = array('admin');
	try {
		$ac->checkPermissions($permissions);	
	} catch(Exception $e){
		$mesg = $e->getMessage();
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}*/
?>
<?php include_layout_template("admin_header.php"); ?>

        <div id="container">
        	<h3>Actions</h3>
        	<div id="side-bar">
		<?php
        	$actions = array("create_user" => "Create User", "view_users" => "View Users", "view_roles" => "Roles of Users", "manage_perms" => "Manage Permissions", "view_logs" => "View Logs");
			echo create_action_links($actions);
        ?>
        	</div>
            <div id="main-content">
            
            </div> <!-- main-content -->
        </div> <!-- container -->

<?php include_layout_template("../layouts/admin_footer.php"); ?> 