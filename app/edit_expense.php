<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isloggedIn()) { redirect_to('../index.php'); } ?>
<?php
	$properties = Property::findAll();
	
	if(isset($_GET['id']) && !empty($_GET['id'])){
		$exp_id = (int)$_GET['id'];
		if(!is_int($exp_id)){
			$mesg = "Edit operation failed. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('expenses.php');
		} else {
			$expense = Expense::findById($exp_id);
		}
	}
	
	///////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SUBMIT ////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$prop_id = $_POST['prop'];
		$expense = $_POST['expense'];
		$start   = $_POST['start'];
		$end     = $_POST['end'];
		$amount  = $_POST['amount'];
		
		if(empty($prop_id) || empty($expense) || empty($start) ||
		   empty($end) || empty($amount)){
			$err = "Form fields marked with an asterix are required";	   
		} else {
			$exp = Expense::findById($exp_id);
			$exp->setPropertyId($prop_id);
			$exp->setName($expense);
			$exp->setStartPeriod($start);
			$exp->setEndPeriod($end);
			//$exp->setDatePaid();
			$exp->setPaymentAmount($amount);
			if($exp->save()){
				$mesg = "Changes saved";
				$session->message($mesg);
				redirect_to("expenses.php");	
			} else {
				$err = "An error occured preventing the expense from being recorded";	
			}
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("deposits" => "Tenant Deposits", "status" => "Payment Status", "payment_search" => "Search Payment", "expenses" => "Expenses");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Details of Expense</h2>
    
    <a id="btn-back" href="expenses.php" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5" cols="4">
    <tr>
    	<td><label for="prop">Choose Property
        <span class="required-field">*</span></label></td>
        <td>
        	<select name="prop" id="prop">
            <option value="" selected="selected"></option>
		<?php foreach($properties as $prop): ?>
        	<option value="<?php echo $prop->id; ?>" <?php if($prop->id == $expense->getPropertyId()) { echo ' selected="selected"'; } ?>><?php echo $prop->getPropertyName(); ?></option>
        <?php endforeach; ?>
        	</select>
        </td>
    </tr>
    <tr>
    	<td><label for="name">Name of Expense
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="expense" id="name" value="<?php echo $expense->getName(); ?>" /></td>
    </tr>
    <tr>
    	<td><label for="start_date">Start Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start" id="start_date" value="<?php echo $expense->getStartPeriod(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="end_date">End Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end" id="end_date" value="<?php echo $expense->getEndPeriod(); ?>" /></td>
    </tr>
    <tr>
    	<td><label for="amount">Amount
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" value="<?php echo $expense->getPaymentAmount(); ?>" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right"><input type="submit" name="submit" value="Save" />&nbsp;&nbsp;<a href="expenses.php" style="text-decoration:none;"><input type="button" value="Cancel" /></a></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>