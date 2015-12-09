<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('edit_rent')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if((isset($_GET['tid']) && !empty($_GET['tid'])) &&
	   (isset($_GET['type']) && !empty($_GET['type']))){
		$tenant_id = (int)$_GET['tid'];
		$type = $_GET['type'];
		if(!is_int($tenant_id)){
			$mesg = "Deposit edit failed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');	
		} elseif(!valid_deposit($type)){
			$mesg = "Deposit edit failed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');	
		} else {
			switch($type){
				case "house":
				$deposit = Deposit::findByTenantId($tenant_id);
				break;
				
				case "kplc":
				$deposit = DepositKPLC::findByTenantId($tenant_id);
				break;
				
				case "eldowas":
				$deposit = DepositEldowas::findByTenantId($tenant_id);
				break;
			}
		}
	} else {
		$mesg = "Deposit edit failed. An invalid value was sent through the URL";
		$session->message($mesg);
		redirect_to("tenants.php");	
	}
	
	/////////////////////////////////////////////////////////////////////////
	//////////////////////////// PROCESS SUBMIT /////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$amount   = $_POST['amount'];
		$date_paid = $_POST['date_paid'];
		if(empty($amount) || empty($date_paid)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			//$deposit = Deposit::findByTenantId($tenant_id);
			$deposit->setDatePaid($date_paid);
			$deposit->setPaymentAmount($amount);
			if($deposit->save()){
				$mesg = "Changes Saved";
				$session->message($mesg);
				redirect_to("tenant_deposit.php?tid={$tenant_id}");	
			} else {
				$err = "An error occured preventing the changes from being saved. Please try again later";	
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
    
    <h2>Edit Details of Deposit Payment</h2>
    
    <a id="btn-back" href="tenant_deposit.php?tid=<?php echo $tenant_id; ?>" title="go back">&laquo;Back</a>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="amount">Amount(Ksh)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" value="<?php echo $deposit->getPaymentAmount(); ?>" /></td>
    </tr>
    <tr>
    	<td><label for="paid">Date Paid(yy/mm/dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="date_paid" id="paid" value="<?php echo $deposit->getDatePaid(); ?>" /></td>
    </tr>
    <tr>
    	<td align="right" colspan="2"><input type="submit" name="submit" value="Save Changes" />&nbsp;&nbsp;<a href="tenant_deposit.php?tid=<?php echo $tenant_id; ?>" style="text-decoration:none;"><input type="button" value="Cancel" /></a></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>