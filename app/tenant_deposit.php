<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Deposit payment from this tenant could not be found. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");	
		} else {
			$tenant  = Tenant::findById($tenant_id);
			$deposit = Deposit::findByTenantId($tenant_id);
			$kplc    = DepositKPLC::findByTenantId($tenant_id);
			$eldowas = DepositEldowas::findByTenantId($tenant_id);
		}
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Tenant Search", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <a class="new-item kplc-eldowas" href="deposit_payment.php?tid=<?php echo $tenant->id; ?>" title="Record house deposit payment for tenant">New Tenant(s)</a> 
    <a class="kplc-eldowas" id="kplc" href="deposit_kplc_payment.php?tid=<?php echo $tenant_id; ?>" title="record KPLC deposit payment for tenant">KPLC</a>
    <a class="kplc-eldowas" id="eldowas" href="deposit_eldowas_payment.php?tid=<?php echo $tenant_id; ?>" title="record ELDOWAS deposit payment for tenant">ELDOWAS</a>
    <h2>Deposit from <?php echo $tenant->getFullName(); ?></h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;</a>
    
    <?php $mesg = $session->message(); echo output_message($mesg); ?>
    
    
    <?php if(!is_null($deposit)){ ?>
    <h3>House Deposit</h3>
    <table cellpadding="5" class="bordered">
    <thead>
    	<th>Date Paid</th>
        <th>Date Refunded</th>
        <th>Amount</th>
        <th>Status</th>
        <th colspan="3">Actions</th>
    </thead>
    <tbody>
    <tr>
    	<td align="right"><?php echo $deposit->getDatePaid(); ?></td>
        <td align="right"><?php $date_ref = $deposit->getDateRefunded(); echo ($date_ref != '0000-00-00') ? $date_ref : '----'; ?></td>
        <td align="right"><?php echo $deposit->getPaymentAmount(); ?></td>
        <td align="right"><?php echo ($deposit->getStatus() == 0) ? 'ACTIVE' : 'REFUNDED'; ?></td>
        <td><a href="deposit_refund.php?tid=<?php echo $tenant->id; ?>&type=house" title="refund deposit">Refund</a></td>
        <td><a href="edit_deposit.php?tid=<?php echo $tenant->id; ?>&type=house" title="edit deposit information">Edit</a></td>
        <td><a class="del-deposit" href="delete_deposit.php?tid=<?php echo $tenant->id; ?>&type=house" title="delete deposit"><strong>X</strong></a></td>
    </tr>
    </tbody>
    </table>
    <?php } else { echo "<h3>House Deposit</h3><p>Tenant has not yet paid house deposit</p><br /><br />"; } ?>
    
    <?php if(!is_null($kplc)){ ?>
    <h3>KPLC Deposit</h3>
    <table cellpadding="5" class="bordered">
    <thead>
    	<th>Date Paid</th>
        <th>Date Refunded</th>
        <th>Amount</th>
        <th>Status</th>
        <th colspan="3">Actions</th>
    </thead>
    <tbody>
    <tr>
    	<td align="right"><?php echo $kplc->getDatePaid(); ?></td>
        <td align="right"><?php $date_ref = $kplc->getDateRefunded(); echo ($date_ref != '0000-00-00') ? $date_ref : '----'; ?></td>
        <td align="right"><?php echo $kplc->getPaymentAmount(); ?></td>
        <td align="right"><?php echo ($kplc->getStatus() == 0) ? 'ACTIVE' : 'REFUNDED'; ?></td>
        <td><a href="deposit_refund.php?tid=<?php echo $tenant->id; ?>&type=kplc" title="refund deposit">Refund</a></td>
        <td><a href="edit_deposit.php?tid=<?php echo $tenant->id; ?>&type=kplc" title="edit deposit information">Edit</a></td>
        <td><a class="del-deposit" href="delete_deposit.php?tid=<?php echo $tenant->id; ?>&type=kplc" title="delete deposit"><strong>X</strong></a></td>
    </tr>
    </tbody>
    </table>
    <?php } else { echo "<h3>KPLC Deposit</h3>"; echo "<p>Tenant has not yet paid KPLC deposit</p><br /><br />"; } ?>
    
    <?php if(!is_null($eldowas)){ ?>
    <h3>ELDOWAS Deposit</h3>
    <table cellpadding="5" class="bordered">
    <thead>
    	<th>Date Paid</th>
        <th>Date Refunded</th>
        <th>Amount</th>
        <th>Status</th>
        <th colspan="3">Actions</th>
    </thead>
    <tbody>
    <tr>
    	<td align="right"><?php echo $eldowas->getDatePaid(); ?></td>
        <td align="right"><?php $date_ref = $eldowas->getDateRefunded(); echo ($date_ref != '0000-00-00') ? $date_ref : '----'; ?></td>
        <td align="right"><?php echo $eldowas->getPaymentAmount(); ?></td>
        <td align="right"><?php echo ($eldowas->getStatus() == 0) ? 'ACTIVE' : 'REFUNDED'; ?></td>
        <td><a href="deposit_refund.php?tid=<?php echo $tenant->id; ?>&type=eldowas" title="refund deposit">Refund</a></td>
        <td><a href="edit_deposit.php?tid=<?php echo $tenant->id; ?>&type=eldowas" title="edit deposit information">Edit</a></td>
        <td><a class="del-deposit" href="delete_deposit.php?tid=<?php echo $tenant->id; ?>&type=eldowas" title="delete deposit"><strong>X</strong></a></td>
    </tr>
    </tbody>
    </table>
    <?php } else { echo "<h3>ELDOWAS Deposit</h3>";  echo "<p>Tenant has not yet paid ELDOWAS deposit</p><br /><br />"; } ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>