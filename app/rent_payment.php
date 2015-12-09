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
			$session->message("Process could not be initiated. An invalid value was sent through the URL");
			redirect_to("tenants.php");	
		} else {
			$tenant  = Tenant::findById($tenant_id);	
			$prop_id = (int)$tenant->getPropertyId();
			$tid     = $tenant->id;
			$session->sessionVar("pid", $prop_id);
			$session->sessionVar("tid", $tid);
			//echo var_dump($tenant);
			
			// Check if tenant moved out
			if($tenant->hasMovedOut()){
				$mesg = "Invalid operation. Tenant already moved out";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tid}");	
			}
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$prop_id    = $session->sessionVar("pid");
		$tenant_id  = $session->sessionVar("tid");
		$start_date = $_POST['start_date'];
		$end_date   = $_POST['end_date'];
		$amount     = $_POST['amount'];
		$mode       = $_POST['mode'];
		$remarks    = $_POST['remarks'];
		if(empty($start_date) || empty($end_date) || empty($amount)){
			$err = "Form fields marked with an asterx are required";	   
		} elseif(!valid_date_range($start_date, $end_date)){
			$mesg  = "Rent payments can only be entered monthly. Specify a month ";
			$mesg .= "by choosing the start and end dates of the month";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");	
		}elseif($tenant->hasPaidFullRent($tenant_id, $start_date, $end_date)){
			$mesg = "This tenant has already paid the rent for the specified period";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");
		} elseif($tenant->hasPaidPartialRent($tenant_id, $start_date, $end_date)){
			$mesg  = "This tenant has paid PART of the rent for the specified period. ";
			$mesg .= "Check the amount of arrears owed by the tenant for the period ";
			$mesg .= "you've specified and deduct the necessary amount from it";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");	
		} else {
			// Continue with processing
			$rent = new Rent();
			$rent->setPropertyId($prop_id);
			$rent->setTenantId($tenant_id);
			$rent->setStartPeriod($start_date);
			$rent->setEndPeriod($end_date);
			$rent->setDatePaid();
			$rent->setPaymentAmount($amount);
			$rent->generateReceiptNo();
			$rent->setPaymentMode($mode);
			$rent->setReceivingAgent($session);
			$rent->setRemarks($remarks);
			//echo var_dump($rent);
			if($tenant->payRent($rent, $start_date, $end_date)){
				Logger::getInstance()->logAction("RENT", $amount, "Rent of {$tenant->getFullName()} for {$rent->getMonth()}");
				$last_id = Rent::lastPaymentId();
				/*$session->sessionVar('start', $start_date);
				$session->sessionVar('end', $end_date)*/;
				if($mode == "cheque"){
					// Cheque Payment
					$session->sessionVar("type", "rent");
					$mesg = "Payment details saved";
					$session->message($mesg);
					redirect_to("cheque_details.php?tid={$tenant_id}&id={$last_id}");
				} else {
					// Cash Payment
					$session->message("Payment recorded");
					redirect_to("receipt.php?tid={$tenant_id}&id={$last_id}&type=rent");
					//redirect_to("tenant.php?tid={$tenant_id}");
				}
			} else {
				$session->message("An error occured preventing the payment from being recorded. Please try again later");
				redirect_to("tenant.php?tid={$tenant_id}");	
			}
		}
	} else {
		// Form not submitted
		$err = "";	
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
    <h2>Enter Payment Details</h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="start_date">Start Date:
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start_date" id="start_date" /></td>
    </tr>
    <tr>
        <td><label for="end_date">End Date:
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end_date" id="end_date" /></td>
    </tr>
    <tr>
        <td><label for="amount">Amount Paid:
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" /></td>
    </tr>
    <tr>
        <td><label for="mode">Payment Mode:
        <span class="required-field">*</span></label></td>
        <td>
            <select name="mode" id="mode">
                <option value="cash" selected="selected">Cash</option>
                <option value="cheque">Cheque</option>
                <!-- <option value="mpesa">Mpesa Paybill</option>
                <option value="airtel_money">Airtel Money</option>
                <option value="orange_money">Orange Money</option> -->
            </select>
        </td>
    </tr>
    <tr>
    	<td><label for="remarks">Remarks</label></td>
        <td><input type="text" name="remarks" id="remarks" size="50" /></td>
    </tr>
    <tr>
        <td colspan="2" align="right">
            <input type="submit" name="submit" value="Post Payment" />
        </td>
    </tr>
    </table>
    </form>
    </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>