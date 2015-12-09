<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); } 
	if(!$ac->hasPermission('post_rent')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	
	if((isset($_GET['tid']) && !empty($_GET['tid'])) &&
	   (isset($_GET['aid']) && !empty($_GET['aid'])) ){
		$tenant_id = (int)$_GET['tid'];
		$arrears_id = (int)$_GET['aid'];
		if(!is_int($tenant_id) || !is_int($arrears_id)){
			$mesg = "An invalid value sent through the URL prevented the payment from being made";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);
			if(is_null($tenant)){
				$mesg = "The tenant who owes the arreas could not be found in the system";
				$session->message($mesg);
				redirect_to("tenants.php");	
			}
			$arrears = Arrears::findById($arrears_id);
			if(is_null($arrears)){
				$mesg = "Information regarding the arrears could not be found";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			}
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$mode = $_POST['mode'];
		$amount = (int)$arrears->removeCommasFromCurrency($_POST['amount']);
		$bal = $arrears->getAmountOwed();
		$bal = (int)$arrears->removeCommasFromCurrency($bal);
		if(empty($amount)){
			$err = "Form fields marked with an asterix are required";	
		} elseif(!is_int($amount)){
			$err = "Please provide a numeric value for the amount of payment made";	
		} elseif($amount > $bal){
			$err = "Amount paid cannot exceed the amount owed in arrears";	
		} else {
			$tenant_id = (int)$_GET['tid'];
			$tenant = Tenant::findById($tenant_id);
			$arrears_id = (int)$_GET['aid'];
			$arrears = Arrears::findById($arrears_id);
			$start = $arrears->getStartPeriod();
			$start = $arrears->formatDateForDatabase($start);
			$end   = $arrears->getEndPeriod();
			$end   = $arrears->formatDateForDatabase($end);
			/**$status = PaymentStatus::findByPeriod($tenant->id, $start, $end);
			echo "Tenant: ";
			echo var_dump($tenant);
			echo "Arrears: ";
			echo var_dump($arrears);
			echo "Start Date: ";
			echo var_dump($start)."<br />";
			echo "End Date: ";
			echo var_dump($end);
			echo "Payment Status: ";
			echo var_dump($status);*/
			if($tenant->payArrears($arrears->id, $amount, $start, $end, $mode, $session)){
				Logger::getInstance()->logAction("ARREARS", $amount, "Arrears payment of {$tenant->getFullName()} for {$arrears->getMonth()}");
				$last_id = ArrearsPaid::lastPaymentId();
				/*$session->sessionVar('start', $start);
				$session->sessionVar('end', $end);
				$session->sessionVar('amount', $amount);*/
				if($mode == "cheque"){
					// Cheque Payment
					$session->sessionVar("type", "arrears");
					$mesg = "Payment details saved";
					$session->message($mesg);
					redirect_to("cheque_details.php?tid={$tenant_id}&id={$last_id}");	
				} else {
					// Cash Payment
					$session->message('Payment posted');	
					redirect_to("receipt.php?tid={$tenant_id}&id={$last_id}&type=arrears");
				}
				//redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			} else {
				$err = "An error occured preventing the payment from being posted";	
			}
		}
	} else {
		// Form not submitted
		$mesg = "";	
		$err  = "";
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
    
    <h2>Record Payment of Rent Arrears</h2>
    
    <a id="btn-back" href="tenant_arrears.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="amount">Enter Amount
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" /></td>
    </tr>
    <tr>
    	<td><label for="mode">Payment Mode
        <span class="required-field">*</span></label></td>
        <td>
        	<select name="mode" id="mode">
            	<option value="cash" selected="selected">Cash</option>
                <option value="cheque">Cheque</option>
            </select>
        </td>
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

<?php include_layout_template('admin_footer.php'); ?>