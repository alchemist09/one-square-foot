<?php require_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php

	$properties = Property::findAll();
	
	////////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SEARCH /////////////////////////////
	////////////////////////////////////////////////////////////////////////////
	
	if(isset($_GET['submit'])){
		$prop_id = $_GET['prop'];
		$start   = $_GET['start_date'];
		$end     = $_GET['end_date'];
		if(empty($prop_id) || empty($start) || empty($end)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$property = Property::findById($prop_id);
			$arrears = Arrears::getOutstandingArrearsForProperty($prop_id, $start, $end);
		}
	}
	
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("deposits" => "Tenant Deposits", "status" => "Payment Status", "payment_search" => "Search Payment", "expenses" => "Expenses");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Rent Arrears By Property</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <table cellpadding="5">
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
    	<td><label for="start_date">Start Date
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start_date" id="start_date" /></td>
    </tr>
    <tr>
    	<td><label for="end_date">End Date
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end_date" id="end_date" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        	<input type="submit" name="submit" value="Search" />
        </td>
    </tr>
    </table>
    </form><br /><br />
    
    <?php if(!empty($arrears)){ ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th style="width:200px;">Tenant</th>
        <th style="width:100px;">Month</th>
        <th style="width:150px;">Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($arrears as $ar): ?>
    <tr>
    	<td align="right"><?php echo Tenant::findById($ar->getTenantId())->getFullName(); ?></td>
        <td align="right"><?php echo $ar->getMonth(); ?></td>
        <td align="right"><?php echo $ar->getAmountOwed(); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td colspan="2" class="analysis" align="right">Total</td>
        <td align="right" class="analysis"><?php echo Arrears::calcArrearsForPropertyDuringPeriod($prop_id, $start, $end) ?></td>
    </tr>
    </tbody>
    </table>
    <?php } elseif(empty($arrears) && !empty($prop_id) && !empty($start) && !empty($end)){
		echo "<p>Property doesn't have any arrears for the specified period</p>";	
	}
    ?>
    
    </div>
    </div>

<?php include_layout_template("admin_footer.php"); ?>