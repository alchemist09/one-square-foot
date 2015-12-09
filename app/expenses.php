<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	$properties = Property::findAll();
	
	///////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SUBMIT ////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	if(isset($_GET['submit'])){
		$prop_id = $_GET['prop'];
		$start = $_GET['start'];
		$end   = $_GET['end'];
		
		if(empty($prop_id) || empty($start) || empty($end)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$property = Property::findById($prop_id);
			$expenses = Expense::findByPeriodForProperty($prop_id, $start, $end);
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants", "payment_search" => "Search Payment", "expense_new" => "New Expense");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <a class="new-item" href="expense_new.php" title="record a new expense">New Expense</a>
    
    <h2>Expenses by Property</h2>
    
    <?php 
        $mesg = $session->message();
        echo output_message($mesg);
    ?>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
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
    	<td><label for="start_date">Start Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start" id="start_date" /></td>
        <td><label for="end_date">End Date(yy-mm-dd)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end" id="end_date" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right"><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form>
    
    <?php
        if(!empty($start) && !empty($end)){
            //$property = Property::findById($prop_id);
            $timestamp_01 = strtotime($start);
            $timestamp_02 = strtotime($end);
            $textual_date_01 = strftime("%B %d, %Y", $timestamp_01);
            $textual_date_02 = strftime("%B %d, %Y", $timestamp_02);
            
            echo "<h3>".$property->getPropertyName()."</h3>";
            echo "<h4 style=\"margin-bottom:20px;\">Expenses for the Period {$textual_date_01} to {$textual_date_02}</h4>";
        }
    ?>
    
    <?php if(!empty($expenses)) { ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Name of Expense</th>
        <th>Month</th>
        <th>Date Made</th>
        <th>Amount</th>
        <th colspan="2">Actions</th>       
    </tr>
    </thead>
    <tbody>
    <?php foreach($expenses as $exp): ?>
    <tr>
    	<td align="right"><?php echo $exp->getName(); ?></td>
        <td align="right"><?php echo $exp->getMonth(); ?></td>
        <td align="right"><?php echo $exp->getDatePaid(); ?></td>
        <td align="right"><?php echo $exp->getPaymentAmount(); ?></td>
        <td align="right"><a href="edit_expense.php?id=<?php echo $exp->id; ?>" title="edit details of expense">Edit</a></td>
        <td align="right"><a class="del-exp" href="delete_expense.php?id=<?php echo $exp->id; ?>" title="delete this expense">X</a></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td colspan="3" align="right" style="font-weight:bold;">Total</td>
        <td align="right" style="font-weight:bold;"><?php echo Expense::calcTotalExpenses($prop_id, $start, $end); ?></td>
    </tr>
    </tbody>
    </table>
    <?php } elseif(empty($expenses)) {
            echo '<p>No expenses were incurred for the property during the specified period</p>';
        } 
	?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>