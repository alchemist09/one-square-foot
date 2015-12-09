<?php require_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	$properties = Property::findAll();
	//echo var_dump($properties);
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
	<h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("add_prop" => "New Property", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants",  "status" => "Payment Status");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
        <a class="new-item" href="add_prop.php" title="add a new property">New Property</a>
        <h2>Properties Under Management</h2>
        
        <?php 
            $mesg = $session->message();
            if(!empty($mesg)) { echo output_message($mesg); }
        ?>
        
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
            <td><a href="edit_prop.php?pid=<?php echo $property->id; ?>" title="edit details of this property">Edit</a></td>
            <td><a class="del-prop" href="delete_prop.php?pid=<?php echo $property->id; ?>" title="delete this property">Del</a></td>
        </tr>
        <?php endforeach; ?>
        
        <?php
            /*$pt = Property::findById(1);
            echo var_dump($pt);
            $occ_rms = Room::findOccupiedRoomsForProperty(1);
            echo "Occupied Rooms";
            echo var_dump($occ_rms);
            echo "Calculate Occupancy Level<br />";
            echo $pt->calcOccupancyLevel();*/
        ?>
        </tbody>
        </table>
        
    </div> <!-- main-content -->
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>