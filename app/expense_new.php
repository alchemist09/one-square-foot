<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	$properties = Property::findAll();
	
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
			$exp = new Expense();
			$exp->setPropertyId($prop_id);
			$exp->setName($expense);
			$exp->setStartPeriod($start);
			$exp->setEndPeriod($end);
			$exp->setDatePaid();
			$exp->setPaymentAmount($amount);
			if($exp->save()){
				Logger::getInstance()->logAction("EXPENSE", $amount, $expense);
				/*$log = Logger::getInstance();
				$log->logAction("EXPENSE", $amount, "Payment to security firm");*/
				//echo var_dump($log);
				$mesg = "Expense recorded";
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
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants", "payment_search" => "Search Payment", "expenses" => "Expenses");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Record New Expense</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table cellpadding="5" cols="4">
    <tr>
    	<td><label for="prop">Choose Property
        <span class="required-field">*</span></label></td>
        <td>
        	<select name="prop" id="prop">
            <option value="" selected="selected"></option>
		<?php foreach($properties as $prop): ?>
        	<option value="<?php echo $prop->id; ?>"><?php echo $prop->getPropertyName(); ?></option>
        <?php endforeach; ?>
        	</select>
        </td>
    </tr>
    <tr>
    	<td><label for="name">Name of Expense
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="expense" id="name" /></td>
    </tr>
    <tr>
    	<td><label for="start_date">Start Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start" id="start_date" /></td>
    </tr>
    <tr>
        <td><label for="end_date">End Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end" id="end_date" /></td>
    </tr>
    <tr>
    	<td><label for="amount">Amount
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="amount" id="amount" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right"><input type="submit" name="submit" value="Save" /></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>