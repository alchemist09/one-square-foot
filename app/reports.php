<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to('view_users.php');
	}
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("deposits" => "Tenant Deposits", "rent_collection" => "Rent Collection", "property_arrears" => "Arrears");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>