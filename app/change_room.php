<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	if(!$ac->hasPermission('change_room')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Tenant could not be moved to another room. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to('tenants.php');	
		} else {
			// Check if tenant exists
			$tenant = Tenant::findById($tenant_id);
			if(is_null($tenant)){
				$mesg = "Information regarding the tenant could not be found. Room change failed";
				$session->message($mesg);
				redirect_to('tenants.php');	
			}	
			
			// Check if tenant moved out
			if($tenant->hasMovedOut()){
				$mesg = "Invalid operation tenant already moved out";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			}
			
			$prop_id = $tenant->getPropertyId();
			$property = Property::findById($prop_id);
			if($property->isFullyOccupied()){
				$mesg = "Room change failed. All the rooms in the property are occupied";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant->id}");	
			} else {
				// Get the vacant rooms from the property
				$vacant_rooms = $property->getVacantRooms();	
			}
		}
	}
	
	///////////////////////////////////////////////////////////////////////////
	///////////////////////////// PROCESS SUBMIT //////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		//echo var_dump($_POST);	
		$room_id = $_POST['room'];
		if(!isset($room_id)){
			$err = "Choose room to move tenant to";	
		} else {
			$new_room = Room::findById($room_id);
			if($tenant->changeRoom($new_room)){
				$mesg = "Tenant has been moved to new room";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant->id}");	
			} else {
				$err = "An error occured preventing the tenant from being moved into the selected room";
			}
		}
	}
	
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Assign New Room to Tenant</h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <?php foreach($vacant_rooms as $room): ?>
    <p><label><input type="radio" name="room" value="<?php echo $room->id; ?>" /><?php echo $room->getRoomLabel(); echo ' ['.$room->getRent().' / month]'; ?></label></p>
    <?php endforeach; ?>
    <br />
    <input type="submit" name="submit" value="Save Changes" />
    </form>
    
    </div>
    </div>

<?php include_layout_template("admin_footer.php"); ?>