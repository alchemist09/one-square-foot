<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Cheque payments from user could not be displayed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');	
		} else {
			$tenant = Tenant::findById($tenant_id);
			if(is_null($tenant)){
				$mesg = "Tenant could not be found in the system";
				$session->message($mesg);
				redirect_to("tenants.php");	
			}
			$cheques = Cheque::findByTenantId($tenant_id);
			if(empty($cheques)){
				$mesg = "No corresponding cheque payments found";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");
			}	
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
    
    <h2>Cheque Payments from <?php echo $tenant->getFullName(); ?></h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Cheque No</th>
        <th>Bank</th>
        <th>Branch</th>
        <th>Drawer</th>
        <th>Month</th>
        <th>Date Paid</th>
        <th>Type</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($cheques as $payment): ?>
    <tr>
    	<td align="right"><?php echo $payment->getChequeNo(); ?></td>
        <td align="right"><?php echo $payment->getBank(); ?></td>
        <td align="right"><?php echo $payment->getBranch(); ?></td>
        <td align="right"><?php echo $payment->getDrawer(); ?></td>
        <td align="right"><?php echo $payment->getMonth(); ?></td>
        <td align="right"><?php echo $payment->getDatePaid(); ?></td>
        <td align="right"><?php echo $payment->getPaymentType(); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>