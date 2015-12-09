<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Rent arrears of this tenant could not be shown. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");	
		} else {
			$tenant  = Tenant::findById($tenant_id);
			$arrears = Arrears::findByTenantId($tenant_id);	
		}
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <a class="new-item" href="record_arrears.php?tid=<?php echo $tenant->id; ?>" title="record new arrears for this tenant">New Arrears</a>
    
    <h2>Rent Arrears of <?php echo $tenant->getFullName(); ?></h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php
		
		$mesg = $session->message();
		echo output_message($mesg);
	
		if(empty($arrears)){
			$mesg = "Tenant has no outstanding rent arrears";
			echo output_message($mesg);	
		} else {
			
	?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Start Date</th>
        <th>End Date</th>
        <th>Amount</th>
        <th colspan="3">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($arrears as $bal): ?>
    <tr>
    	<td><?php echo $bal->getStartPeriod(); ?></td>
        <td><?php echo $bal->getEndPeriod(); ?></td>
        <td align="right"><?php echo $bal->getAmountOwed(); ?></td>
        <td><a href="pay_arrears.php?tid=<?php echo $tenant->id; ?>&aid=<?php echo $bal->id; ?>" title="settle outstanding arrears for the specified period">Pay Arrears</a></td>
        <td><a href="edit_arrears.php?tid=<?php echo $tenant->id; ?>&aid=<?php echo $bal->id; ?>" title="edit details of arrears">Edit</a></td>
        <td><a class="del-arrears" href="delete_arrears.php?tid=<?php echo $tenant->id; ?>&aid=<?php echo $bal->id; ?>" title="delete arrears"><strong>X</strong></a></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td align="right" colspan="2" style="font-weight:bold;">Total: </td>
        <td align="right" style="font-weight:bold"><?php echo $tenant->getTotalArrears(); ?></td>
    </tr>
    </tbody>
    </table>
    <?php } ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>