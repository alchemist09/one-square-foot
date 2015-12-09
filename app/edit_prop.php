<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	if(isset($_GET['pid']) && !empty($_GET['pid'])){
		$prop_id = (int)$_GET['pid'];	
		if(!is_int($prop_id)){
			$session->message("Property edit failed. An invalid value was sent through the URL");
			redirect_to("property.php");	
		} else {
			$property = Property::findById($prop_id);	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$name       = $_POST['name'];
		$num_rooms  = $_POST['rooms'];
		$mgt_fee    = $_POST['fee'];
		$landlord   = $_POST['landlord'];
		$date_added = $_POST['added'];
		
		if(empty($name) || empty($num_rooms) || empty($mgt_fee) || 
		   empty($landlord) || empty($date_added)){
			$err = "Form fields marked with an asterix are required";	   
	    } else {
			$property->setPropertyName($name);
			$property->setNumRooms($num_rooms);
			$property->setManagementFee($mgt_fee);
			$property->setLandLord($landlord);
			$property->setDateAdded($date_added);
			if($property->save()){
				$mesg = "The changes have been saved";
				$session->message($mesg);
				redirect_to("property.php");	
			} else {
				$err = "An error occured preventing the changes from being from saved";	
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
        $actions = array("add_prop" => "New Property", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Details of this Property</h2> 
    
    <a id="btn-back" href="property.php" title="go back">&laquo;Back</a> 
    
    <?php if(!empty($err)) { echo output_message($err); } ?>
    
    
    <form action="edit_prop.php?pid=<?php echo $prop_id; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="name">Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="name" id="name" value="<?php echo $property->getPropertyName(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="rooms">No. of Rooms
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="rooms" id="rooms" value="<?php echo $property->getNumRooms(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="fee">Management Fee(%)
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="fee" id="fee" value="<?php echo $property->getManagementFee(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="landlord">Landlord
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="landlord" id="landlord" value="<?php echo $property->getLandLord(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="added">Date Added
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="added" id="added" value="<?php echo $property->getDateAdded(); ?>" /></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="submit" name="submit" value="Save" />&nbsp;&nbsp;<a href="property.php" class="btn-cancel"><input type="button" value="Cancel" /></a></td>
    </tr>
    </table>
    </form>
    
    </div>
    </div> <!--  container -->

<?php include_layout_template("admin_footer.php"); ?>