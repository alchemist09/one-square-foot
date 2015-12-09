<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Cheque details of this transaction could not be saved. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$last_id = (int)$_GET['id'];
		}	
		/*echo "Session ['type']";
		echo var_dump($session->sessionVar('type')).'<br />';
		echo "Session ['start']";
		echo var_dump($session->sessionVar('start')).'<br />';
		echo "Session ['end']";
		echo var_dump($session->sessionVar('end'));*/
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$tenant_id = $_GET['tid'];
		$cheque_no = $_POST['cheque_no'];
		$bank      = $_POST['bank'];
		$branch    = $_POST['branch'];
		$drawer    = $_POST['drawer'];
		$start     = $_POST['start_date'];
		$end       = $_POST['end_date'];
		$type      = $session->sessionVar('type');
		if(empty($cheque_no) || empty($bank) || empty($branch) || empty($drawer) ||
		   empty($start) || empty($end)){
			$err = "Form fields marked with an asterix are required";
		} else {
			$cheque = new Cheque();
			$cheque->setTenantId($tenant_id);
			$cheque->setChequeNo($cheque_no);
			$cheque->setBank($bank);
			$cheque->setBranch($branch);
			$cheque->setDrawer($drawer);
			$cheque->setStartPeriod($start);
			$cheque->setEndPeriod($end);
			$cheque->setDatePaid();
			$cheque->setPaymentType($type);
			/*echo var_dump($cheque);
			echo var_dump($session->sessionVar("type"));*/
			if($cheque->save()){
				$mesg = "Payment posted";
				$session->message($mesg);
				redirect_to("receipt.php?tid={$tenant_id}&id={$last_id}&type={$type}");	
			} else {
				$err = "An error occured preventing the cheque details from being saved. Please try again later";	
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
        $actions = array("deposit_new" => "Record Deposit", "deposits_prop" => "Deposits by Property", "rent_payment" => "Rent Payment");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <h2>Enter Cheque Details</h2>
    
    <?php 
		$mesg = $session->message();
		if(!empty($mesg)) { echo output_message($mesg); }
		if(!empty($err)) { echo display_error($err); }
	?>
    
    <p><strong>NOTE:</strong> The &ldquo;Start Date&rdquo; and &ldquo;End Date&rdquo; specify the period/month for which the payment is being made.</p>
        
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="cheque_no">Cheque Number
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="cheque_no" id="cheque_no" /></td>
    </tr>
    <tr>
    	<td><label for="bank">Bank<span class="required-field">*</span></label></td>
        <td><input type="text" name="bank" id="bank" /></td>
    </tr>
    <tr>
    	<td><label for="branch">Bank Branch
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="branch" id="branch" /></td>
    </tr>
    <tr>
    	<td><label for="drawer">Drawer's Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="drawer" id="drawer" /></td>
    </tr>
    <tr>
    	<td><label for="start_date">Start Date(yy/mm/dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start_date" id="start_date" /></td>
    </tr>
    <tr>
    	<td><label for="end_date">End Date(yy/mm/dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end_date" id="end_date" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        <input type="submit" name="submit" value="Save Details" />
        </td>
    </tr>
    </table>
    </form>    
        
    </div>
    </div>

<?php include_layout_template("admin_footer.php"); ?>