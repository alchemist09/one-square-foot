<?php require_once("../lib/init.php"); ?>
<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	$properties = Property::findAll();
	
	/////////////////////////////////////////////////////////
	///////////////////// PROCESS SUBMIT ////////////////////
	/////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$prop_id = (int)$_POST['property'];
		if(empty($prop_id)){
			$err = "Please choose a property to display rent payments from";	
		} else {
			$session->sessionVar("prop_id", $prop_id);
			$session->message("Property selected");
			redirect_to("choose_period.php");	
		}
	}
	
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    	<h3>Actions</h3>
        <div id="side-bar">
        <?php
            $actions = array("deposits" => "Tenant Deposits", "rent_collection" => "Rent Collection");
            echo create_action_links($actions);
        ?>
        </div>
        <div id="main-content">
        
        <h2>Rent Payments</h2>
        
        <p><strong>Choose property from which to show rent collection report</strong></p><br />
        
        <?php if(!empty($err)) { echo display_error($err); } ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table cellpadding="5" class="bordered">
        <thead>
        <tr>
            <th>Property Name</th>
            <th>No. of Rooms</th>
            <th>Mgt Fee(%)</th>
            <th>Occupancy Level(%)</th>
            <th>Landlord</th>
            <th>Date Added</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($properties as $property): ?>
        <tr>
            <td><?php echo $property->getPropertyName(); ?></td>
            <td><?php echo $property->getNumRooms(); ?></td>
            <td><?php echo $property->getManagementFee(); ?></td>
            <td><?php echo $property->getOccupancyLevel(); ?></td>
            <td><?php echo $property->getLandLord(); ?></td>
            <td><?php echo $property->getDateAdded(); ?></td>
            <td><input type="radio" name="property" value="<?php echo $property->id; ?>" /></td>
        </tr>
        <?php endforeach; ?>
        <tr>
        <td colspan="6" align="right">
        	<input type="submit" name="submit" value="Show Rent Payments" />
        </td>	
        </tr>
        </tbody>
        </table>
        </form>
        
        </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>