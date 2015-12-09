<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	$permissions = array('create_room', 'create_tenant');
	try {
		$ac->checkPermissions($permissions);	
	} catch(Exception $e){
		$mesg = $e->getMessage();
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	// Get all Properties from database
	$properties = Property::findAll();
	
	$request_uri = $_SERVER['REQUEST_URI'];
	$http_referer = $_SERVER['HTTP_REFERER'];
	
	//echo "\$_SERVER['REQUEST_URI']: ".$request_uri."<br />";
	//echo "\$_SERVER['HTTP_REFERER']: ".$http_referer."<br />";
	$referer_filename = pathinfo($http_referer, PATHINFO_FILENAME);
	//echo "REFERER FILENAME: ".$referer_filename;
?>
<?php
	if(isset($_POST['submit'])){
		$referer_filename = $_POST['referer'];
		$selected_prop = $_POST['prop_name'];
		//echo var_dump($referer_filename);
		if(empty($selected_prop)){
			if($referer_filename == "rooms"){
				$err = "Please choose a property to add rooms to";
			} elseif($referer_filename == "tenants") {
				$err = "Please choose a property from which to select a vacant room for the tenant";	
			}
		} else {
			$session->sessionVar("prop_id", $selected_prop);
			$session->message("Property selection saved");
			$p = Property::findById($selected_prop);
			if($referer_filename == "rooms"){
				if($p->getNumRooms() == Room::getNumRoomsForProperty($selected_prop)){
					$mesg = "You cannot create additional rooms on the selected property. The property already has the maximum number of rooms it can hold. You will first need to adjust the number of rooms the property holds before proceeding";	
					$session->message($mesg);
					redirect_to("rooms.php");
				}
				redirect_to("add_room.php");
			} elseif($referer_filename == "tenants"){
				if($p->isFullyOccupied()){
					$mesg = "The property you selected is fully occupied. Choose another property to add the tenant to";
					$session->message($mesg);
					redirect_to("tenants.php");	
				}
				redirect_to("choose_room.php");	
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
            $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
            echo create_action_links($actions);
        ?>
        </div>
        <div id="main-content">
        	<h2>Select a Property</h2>
            
            <?php if(!empty($err)) { echo display_error($err); } ?>
            
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <table cellpadding="5">
            <tr>
            	<input type="hidden" name="referer" value="<?php echo $referer_filename; ?>" />
            	<td><label for="prop_name">Property Name
                <span class="required-field">*</span></td>
                <td>
                	<select name="prop_name" id="prop_name">
                    	<option value="" selected="selected">Choose a Property</option>
                    <?php foreach($properties as $property): ?>
                    	<option value="<?php echo $property->id; ?>"><?php echo $property->getPropertyName(); ?></option>
                    <?php endforeach; ?>	
                    </select>
                </td>
            </tr>
            <tr>
            	<td colspan="2" align="right">
                	<input type="submit" name="submit" value="Save" />
                </td>
            </tr>
            </table>
            </form>
        </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>