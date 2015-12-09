<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('post_rent')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	
	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Deposit payment could not be recorded. An invalid value was sent through URL";
			$session->message($mesg);
			redirect_to('tenants.php');
		} else {
			$tenant = Tenant::findById($tenant_id);
			// Check if tenant moved out
			if($tenant->hasMovedOut()){
				$mesg = "Invalid operation. This tenant already moved";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			}
			if($tenant->hasPaidDeposit('eldowas')){
				$mesg = "Tenant has already paid water deposit";
				$session->message($mesg);
				redirect_to("tenant_deposit.php?tid={$tenant_id}");
			}	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$date_paid = $_POST['paid'];
		$amount    = $_POST['amount'];
		if(empty($date_paid) || empty($amount)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$tenant_id = (int)$_GET['tid'];
			$dpt = new DepositEldowas();
			$dpt->setTenantId($tenant_id);
			$dpt->setRoomId($tenant_id);
			$dpt->setTenantName($tenant_id);
			$dpt->setPaymentAmount($amount);
			$dpt->generateReceiptNo();
			$dpt->setAgent();
			$dpt->setDatePaid($date_paid);
			/**echo "<tt><pre>".var_export($dpt, true)."</pre></tt>";
			echo "Last Receipt No: ";
			echo $dpt->_lastReceiptNo().'<br />';*/
			/**echo "Tenant ID: ";
			echo $tenant_id;*/
			if($dpt->save()){
				Logger::getInstance()->logAction("ELDOWAS DEPOSIT", $amount, "Eldowas Deposit of {$tenant->getFullName()}");
				$mesg = "Payment posted";
				$session->message($mesg);
				redirect_to("receipt.php?tid={$tenant_id}&type=deposit_eldowas");	
			} else {
				$err = "An error occured preventing the payment from being posted";	
			}
		}
	} else {
		// Form not submitted
		$err = "";	
		$mesg = "";
	}
	
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Tenant Search", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>KPLC Deposit Payment</h2>
    
    <a id="btn-back" href="tenant_deposit.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($mesg)) { echo output_message($mesg); } ?>
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="paid">Date Paid
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="paid" id="paid" /></td>
    </tr>
    <tr>
    	<td><label for="amount">Amount(Ksh)
        <span class="required-field">*</span></label></td>
        <td><input type="amount" name="amount" id="amount" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        	<input type="submit" name="submit" value="Post Payment" />&nbsp;&nbsp;<a href="tenant_deposit.php?tid=<?php echo $tenant->id; ?>" style="text-decoration:none;"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>    
    
    </div>
    </div>

<?php include_layout_template("admin_footer.php"); ?>