<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php

	if(isset($_GET['submit'])){
		$type = (int)$_GET['type'];
		$receipt_no = $_GET['receipt'];
		if(empty($receipt_no)){
			$err = "Enter the receipt number issued for the transaction";	
		} else {
			switch($type){
				case 1:
				$payment = Rent::findByReceiptNo($receipt_no);
				break;
				
				case 0:
				$payment = ArrearsPaid::findByReceiptNo($receipt_no);
				break;	
			}
		}
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants", "status" => "Payment Status");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Lookup a Payment</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <div id="payment-search">
    <form action="payment_search.php" method="get">
    <table cellpadding="5">
    <tr>
    	<td>Payment Type</td>
        <td><label><input type="radio" name="type" value="1" checked="checked" />Rent</label>&nbsp;&nbsp;<label><input type="radio" name="type" value="0" />Arrears</label></td>
    </tr>
    <tr>
        <td><label for="receipt">Receipt No
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="receipt" id="receipt" /></td>
        <td><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form>        
    </div>
    
    <?php if(!empty($payment)){ ?>
    <?php
		$tenant = Tenant::findById($payment->getTenantId())->getFullName();
		$room_no = Room::findByTenantId($payment->getTenantId())->getRoomLabel();
	?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Name of Tenant</th>
        <th>Room No.</th>
        <th>Month</th>
        <th>Amount</th>
        <th>Receipt No</th>
        <th>Date Paid</th>
        <th>Agent</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    	<td align="right"><?php echo $tenant; ?></td>
        <td align="right"><?php echo $room_no; ?></td>
        <td align="right"><?php echo $payment->getMonth(); ?></td>
        <td align="right"><?php echo $payment->getPaymentAmount(); ?></td>
        <td align="right"><?php echo $payment->getReceiptNo(); ?></td>
        <td align="right"><?php echo $payment->getDatePaid(); ?></td>
        <td align="right"><?php echo $payment->getReceivingAgent(); ?></td>
    </tr>
    </tbody>
    </table>
    <?php } elseif(!empty($receipt_no) && empty($payment)) {
		$mesg = "No corresponding payment was found for the provided receipt number";
		echo output_message($mesg);	
	} ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>