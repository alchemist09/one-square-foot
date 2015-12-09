<?php require_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	
	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$session->message("Tenant details not found. An invalid value was sent through the URL");
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);	
		}
	}
	
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
	<h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <div id="tenant-search">
    <fieldset>
    <legend>Find Tenant</legend>
    <form action="tenant_search.php" method="get">
    <table cellpadding="5">
    <tr>
    	<td><label for="tenant">Enter Tenant Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="tenant" id="tenant" /></td>
        <td><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form>  
    </fieldset>      
    </div>
    
    <h2>Tenant Details</h2>
    
    <a id="btn-back" href="tenants.php" title="go back">&laquo;Back</a>
    
    <?php 
        $mesg = $session->message();
        echo output_message($mesg);
    ?>
    
    <p><strong>Tenant Name:</strong> <?php echo $tenant->getFullName(); ?></p>
    <p><strong>Phone Number:</strong> <?php echo $tenant->getPhoneNumber(); ?></p>
    <p><strong>National ID Number:</strong> <?php echo $tenant->getNationalIdNumber(); ?></p>
    <p><strong>Date Joined:</strong> <?php echo $tenant->getDateJoined(); ?></p>
    <?php if($tenant->hasMovedOut()) { ?><p><strong>Date Left:</strong> <?php echo $tenant->getDateLeft(); ?></p><?php } ?>
    <?php 
        $business = $tenant->getBusinessName(); 
        if(!empty($business)){
    ?>
    <p><strong>Business Name:</strong> <?php echo $tenant->getBusinessName(); } ?></p>
    <?php 
        $email = $tenant->getEmail();
        if(!empty($email)){
    ?>
    <p><strong>Email:</strong> <?php echo $tenant->getEmail(); } ?></p>
    <p><strong>Tenancy Status:</strong> <?php echo ($tenant->hasMovedOut()) ? '<span style="color:red">Moved Out</span>' : '<span style="color:green">Active Tenant</span>'; ?></p>
    <br /><br /> 
    
    <h3>Rent Payment</h3>
    <p>Monthly Rent: <?php 
        echo $room = Room::findById($tenant->getRoomId())->getRent();
    ?></p><br />
    
    <h3>Arrears</h3>
    <p>Rent Arrears: <?php if($tenant->hasArrears()){ $arrears = $tenant->getTotalArrears(); echo $arrears; } else { echo "Ksh. 0.00"; } ?></p><br />
    
    <p><a class="btn-action" id="rent-payment" href="rent_payment.php?tid=<?php echo $tenant_id; ?>" title="record rent payment from tenant">Rent Payment</a></p>
    <p><a class="btn-action" id="payment-stmt" href="payment_record.php?tid=<?php echo $tenant_id; ?>" title="show rent payment history of tenant">Payment Statement</a></p> 
    <p><a class="btn-action" id="arrears" href="tenant_arrears.php?tid=<?php echo $tenant_id; ?>" title="show rent arrears of tenant">Show Arrears</a></p>
    <p><a class="btn-action" id="change-room" href="change_room.php?tid=<?php echo $tenant_id; ?>" title="move tenant to another room within the property">Change Room</a></p>
    <p><a class="btn-action" id="show-payments" href="show_payments.php?tid=<?php echo $tenant_id; ?>" title="show rent payments from this user">Show Payments</a></p>
    <p><a class="btn-action" id="deposit" href="tenant_deposit.php?tid=<?php echo $tenant_id; ?>" title="deposit payment information">Deposit</a></p>
    <p><a class="btn-action" id="cheque-payment" href="cheque_payments.php?tid=<?php echo $tenant_id; ?>" title="view cheque payments from this tenant">Cheque Payments</a></p>
    <p><a class="btn-action" id="move-out" href="move_out.php?tid=<?php echo $tenant_id; ?>" title="move out tenant from room">Move Out</a></p>
    
    </div> <!-- main-content -->
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>