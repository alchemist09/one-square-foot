<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	$properties = Property::findAll();
	
	///////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SEARCH ////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	if(isset($_GET['submit'])){
		$prop_id = $_GET['prop'];
		$start = $_GET['start'];
		$end   = $_GET['end'];	
		
		/*$date_info_one = date_parse($start);
		$date_info_two = date_parse($end);
		$month_one = $date_info_one['month'];
		$month_two = $date_info_two['month'];
		$datetime_start = new DateTime($start);
		$datetime_end   = new DateTime($end);
		//$interval = $datetime_start->diff($datetime_end);
		$interval = $datetime_end->diff($datetime_start);
		$num_months = $interval->format("%m") + 1;
		$num_days = $interval->days + 1;
		$valid_diff = array(28, 29, 30, 31);
		
		echo var_dump($interval);
		echo var_dump($date_info_one);
		echo var_dump($date_info_two);
		echo 'Month One: ';
		echo var_dump($month_one);
		echo 'Month Two: ';
		echo var_dump($month_two);
		echo 'No. of Months: ';
		echo var_dump($num_months);
		echo 'No. of Days: ';
		echo var_dump($num_days);
		if(in_array($num_days, $valid_diff)){
			echo "One Month<br />";	
		} else {
			echo "Not one month<br />";	
		}
		echo 'Valid Date Range: ';
		echo var_dump(valid_date_range($start, $end));
		echo 'Month One == Month Two: ';
		echo var_dump($month_one == $month_two);*/
			
		if(empty($prop_id) || empty($start) || empty($end)){
			$err = "Form fields marked with an asterix are required";	
		} elseif(!valid_date_range($start, $end)){
			$err = "Rent payment status of tenants can only be specified monthly. Specify a month by entering the start and end dates of the month";
		} else {
			$month = get_month_from_date($start);
			$property = Property::findById($prop_id);
			$tenants = Tenant::showPaymentStatusOfTenantsByProperty($prop_id, $start, $end);
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants", "payment_search" => "Search Payment");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Rent Payment Status of Tenants</h2>
    
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
    
    
    <h3>Payment Status</h3>
    <p id="status">
    	<strong>1 = Paid</strong><br />
        <strong>0 = Paid Partly / Not Paid</strong>
    </p>
    
    
    <?php if(!empty($tenants)){ ?>
    <?php echo "<h3>Rent payment status of tenants from {$property->getPropertyName()} for $month</h3>"; ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Name of Tenant</th>
        <th>Room No</th>
        <th>Phone No</th>
        <th>Business Name</th>
        <th>Status</th>
        <th>Rent/M</th>
        <th>Arrears(<?php echo $month; ?>)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($tenants as $tnt): ?>
    <?php
		$bal = Arrears::findByPeriodForTenant($tnt->id, $start, $end);
		//echo var_dump($bal);
		$arrears = 0.00;
		if(is_null($bal) && $tnt->getPaymentStatus() == 0){
			$arrears = Room::findByTenantId($tnt->id)->getRent();	
		} elseif(!is_null($bal)){
			$arrears = $bal->getAmountOwed();	
		}
	?>
    <tr <?php if($tnt->getPaymentStatus() == 0) { echo ' style="background:red;color:white;"'; } ?>>
    	<td align="right"><a href="tenant.php?tid=<?php echo $tnt->id; ?>" title="view tenant details"><?php echo $tnt->getFullName(); ?></a></td>
        <td align="right"><?php echo Room::findByTenantId($tnt->id)->getRoomLabel(); ?></td>
        <td align="right"><?php echo $tnt->getPhoneNumber(); ?></td>
        <td align="right"><?php echo $tnt->getBusinessName(); ?></td>
        <td align="right"><?php echo $tnt->getPaymentStatus(); ?></td>
        <td align="right"><?php echo Room::findByTenantId($tnt->id)->getRent(); ?></td>
        <td align="right"><?php echo $arrears; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?php } elseif(!empty($prop_id)) { echo "<p>The selected property has no tenants in it yet</p>"; } ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>