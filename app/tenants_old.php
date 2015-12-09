<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	
	$properties = Property::findAll();
	
	//////////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS SUBMIT ///////////////////////////////
	//////////////////////////////////////////////////////////////////////////////
	
	if(isset($_GET['submit'])){
		$prop_id = $_GET['prop'];
		if(empty($prop_id)){
			$err = "Select property from which to show previous tenants";	
		} else {
			$tenants = Tenant::findPreviousByPropertyId($prop_id);	
		}
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Lookup Previous Tenants</h2>
    
    <?php 
		$mesg = $session->message(); 
		echo output_message($mesg);
	?>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <table cellpadding="5">
    <tr>
    	<td>
        	<select name="prop" id="prop">
            	<option value="" selected="selected">Select Property</option>
            <?php foreach($properties as $p): ?>
            <option value="<?php echo $p->id; ?>"><?php echo $p->getPropertyName(); ?></option>
            <?php endforeach; ?>
            </select>
        </td>
        <td><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form> 
    
    <?php if(!empty($tenants)) { ?>
    <?php foreach($tenants as $tnt): ?>
    <p><a href="tenant.php?tid=<?php echo $tnt->id; ?>"><?php echo $tnt->getFullName(); ?></a>&nbsp;&nbsp;<?php echo "Room No: ".Room::findById($tnt->getRoomId())->getRoomLabel(); ?></p>
    <?php endforeach; ?>
    <?php } elseif(empty($tenants) && !empty($prop_id)) { ?>
    <?php
		$mesg = "No previous tenants found from the selected property";
		echo output_message($mesg);
	?>
    <?php } ?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>