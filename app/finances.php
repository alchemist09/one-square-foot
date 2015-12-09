<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("deposits" => "Tenant Deposits", "status" => "Payment Status", "payment_search" => "Search Payment", "expenses" => "Expenses", "property_arrears" => "Arrears");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <?php 
        $mesg = $session->message();
        echo output_message($mesg);
    ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>