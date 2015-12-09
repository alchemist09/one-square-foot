<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('admin')){
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
			$mesg = "Deposit refund failed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');	
		} elseif(!valid_deposit($type)) {
			$mesg = "Deposit refund failed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');
		} else {
			$tenant = Tenant::findById($tenant_id);
			// Check if tenant paid any deposit
			switch($type){
				case "house":
				if(!$tenant->hasPaidDeposit('house')){
					$mesg = "Tenant did not pay any house deposit";
					$session->message($mesg);
					redirect_to($_SERVER['HTTP_REFERER']);	
				}	
				break;
				
				case "kplc":
				if(!$tenant->hasPaidDeposit('kplc')){
					$mesg = "Tenant did not pay any KPLC deposit";
					$session->message($mesg);
					redirect_to($_SERVER['HTTP_REFERER']);	
				}
				break;
				
				case "eldowas":
				if(!$tenant->hasPaidDeposit('eldowas')){
					$mesg = "Tenant did not pay any ELDOWAS deposit";
					$session->message($mesg);
					redirect_to($_SERVER['HTTP_REFERER']);	
				}
				break;
			}	
			
			// Check if tenant was refunded deposit
			switch($type){
				case "house":
				if($tenant->hasBeenRefundedDeposit('house')){
					$mesg = "Tenant has already been refunded his/her house deposit payment";
					$session->message($mesg);
					redirect_to("tenant_deposit.php?tid={$tenant_id}");	
				}
				break;
				
				case "kplc":
				if($tenant->hasBeenRefundedDeposit('kplc')){
					$mesg = "Tenant has already been refunded his/her KPLC deposit payment";
					$session->message($mesg);
					redirect_to("tenant_deposit.php?tid={$tenant_id}");	
				}
				break;
				
				case "eldowas":
				if($tenant->hasBeenRefundedDeposit('eldowas')){
					$mesg = "Tenant has already been refunded his/her ELDOWAS deposit payment";
					$session->message($mesg);
					redirect_to("tenant_deposit.php?tid={$tenant_id}");	
				}
				break;
			}
		}
	} else {
		$mesg = "Depost refund failed. An invalid value was sent through the URL";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);	
	}
	
	/////////////////////////////////////////////////////////////////////////
	//////////////////////////// PROCESS SUBMIT /////////////////////////////
	/////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$date_ref = $_POST['refunded'];
		$type = $_GET['type'];
		if(empty($date_ref)){
			$err = "Enter the date the refund was made";	
		} else {
			switch($type){
				case "house":
				$deposit = Deposit::findByTenantId($tenant_id);
				$deposit->setDateRefunded($date_ref);
				$deposit->setStatus(1);
				break;
				
				case "kplc":
				$deposit = DepositKPLC::findByTenantId($tenant_id);
				$deposit->setDateRefunded($date_ref);
				$deposit->setStatus(1);
				break;
				
				case "eldowas":
				$deposit = DepositEldowas::findByTenantId($tenant_id);
				$deposit->setDateRefunded($date_ref);
				$deposit->setStatus(1);
				break;
			}
			//echo var_dump($deposit);
			if($deposit->save()){
				$mesg = "Refund of deposit recorded. Company no longer holding funds from tenant";
				$session->message($mesg);
				redirect_to("tenant_deposit.php?tid={$tenant_id}");	
			} else {
				$err = "An error occured preventing the refund from being recorded. Please try again later";	
			}
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("deposit_new" => "Record Deposit", "deposits_prop" => "Deposits by Property", "rent_payment" => "Rent Payment");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Refund Deposit</h2>
    
    <a id="btn-back" href="tenant_deposit.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="refunded">Date Refunded
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="refunded" id="refunded" /></td>
    </tr>
    <tr>
    	<td align="right" colspan="2"><input type="submit" name="submit" value="Record Refund" />&nbsp;&nbsp;<a href="tenant_deposit.php?tid=<?php echo $tenant_id; ?>" style="text-decoration:none;"><input type="button" value="Cancel" /></a></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>