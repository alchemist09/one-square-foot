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
	   (isset($_GET['id']) && !empty($_GET['id'])) ){
		$tenant_id = (int)$_GET['tid'];
		$payment_id = (int)$_GET['id'];
		if(!is_int($tenant_id) && !is_int($payment_id)){
			$mesg = "Payment could not be edited. An invalid was sent through the URL";
			$session->message($mesg);
			redirect_to("tenant.php?tid={$tenant_id}");	
		} else {
			$rent = Rent::findById($payment_id);
				
		}	   
	}
	
	////////////////////////////////////////////////////////////////////////////
	//////////////////////////////// PROCESS SUBMIT ////////////////////////////
	////////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		/*echo var_dump($_POST);
		echo var_dump($rent);*/
		$amount = $_POST['amount'];
		if(empty($amount)){
			$err = "Kindly provide the amount paid for the transaction";	
		} else {
			$rent->setPaymentAmount($amount);
			//echo var_dump($rent);
			if($rent->editPayment($amount)){
				$mesg = "Changes Saved";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			} else {
				$mesg = "An error occured preventing the changes from being saved";
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
        $actions = array("deposit_new" => "Record Deposit", "deposits_prop" => "Deposits by Property", "rent_payment" => "Rent Payment");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Payment Amount For This Transaction</h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant_id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="amount">Enter Payment Amount(Ksh)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" value="<?php echo $rent->getPaymentAmount(); ?>" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        <input type="submit" name="submit" value="Save Changes" />
        <a href="tenant.php?tid=<?php echo $tenant_id; ?>" style="text-decoration:none;"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>