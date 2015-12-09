<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	$properties = Property::findAll();
	
	///////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SEARCH ////////////////////
	///////////////////////////////////////////////////////////////////
	
	if(isset($_GET['submit'])){
		$prop_id = $_GET['prop'];
		$type = $_GET['type'];
		if(empty($type)){
			$err = "Please choose the type of deposit to show";
		} elseif(empty($prop_id)){
			$err = "Please select property from which to show deposits";	
		} else {
			$property = Property::findById($prop_id);
			switch($type){
				case 1:	
				$deposits = Deposit::findByPropertyId($prop_id);
				$total_deposits = Deposit::calcDepositsForProperty($prop_id);
				break;
				
				case 2:
				$deposits = DepositKPLC::findByPropertyId($prop_id);
				$total_deposits = DepositKPLC::calcDepositsForProperty($prop_id);
				break;
				
				case 3:
				$deposits = DepositEldowas::findByPropertyId($prop_id);
				$total_deposits = DepositEldowas::calcDepositsForProperty($prop_id);
				break;
			}
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("status" => "Payment Status", "tenant_search" => "Search Tenant", "property_arrears" => "Arrears");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Tenant Deposits</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <table cellpadding="5">
    <tr>
    	<td><label for="type">Type of Deposit
        <span class="required-field">*</span></label></td>
        <td>
        	<select name="type" id="type">
            	<option value="" selected="selected"></option>
                <option value="1">House</option>
                <option value="2">KPLC</option>
                <option value="3">ELDOWAS</option>
            </select>
        </td>
    </tr>
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
    	<td colspan="2" align="right">
        	<input type="submit" name="submit" value="Search" />
        </td>
    </tr>
    </table>
    </form>
    
    <?php ?>
    
    <?php if(!empty($deposits)){ ?>
    <?php echo "<h3>Tenant Deposits from {$property->getPropertyName()}</h3>"; ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th width="200">Tenant Name</th>
        <th width="200">Date Paid</th>
        <th width="200">Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($deposits as $dpt): ?>
    <tr>
    	<td align="right"><?php echo $dpt->getTenantName(); ?></td>
        <td align="right"><?php echo $dpt->getDatePaid(); ?></td>
        <td align="right"><?php echo $dpt->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td colspan="2" align="right" class="analysis">Total</td>
    	<td align="right" class="analysis"><?php echo $total_deposits; ?></td>
    </tr>
    </tbody>
    </table>
    <?php } elseif(empty($deposits) && !empty($prop_id)) { echo "<p>No tenant deposits have been recorded yet</p>"; } ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>