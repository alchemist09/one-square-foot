<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('create_prop')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	if(isset($_POST['submit'])){
		$name       = $_POST['name'];
		$num_rooms  = $_POST['rooms'];
		$fee        = $_POST['fee'];
		$landlord   = $_POST['owner'];
		$date_added = $_POST['added'];
		// Confirm that all fields are submitted with values
		if(empty($name) || empty($num_rooms) || empty($fee) || empty($landlord) || empty($date_added)) {
			$err = "Form fields marked with an asterix are required";
		} elseif(!is_numeric($fee)){
			$err = "Management fee can only be specified as a numeric value";	
		} else {
			// Continue with processing	
			$property = new Property();
			$property->setPropertyName($name);
			$property->setNumRooms($num_rooms);
			$property->setManagementFee($fee);
			$property->setLandLord($landlord);
			$property->setDateAdded($date_added);
			if($property->save()){
				$mesg = "Property Created";
				$session->message($mesg);
				redirect_to("property.php");	
			} else {
				$err  = "An error occured preventing the property from being saved. Please ";
				$err .= "try again later";	
			}
		}
	} else {
		// Form not submitted
		$err = "";	
	}
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    	<?php include_layout_template("nav.php"); ?>
        <div id="main-content">
        	<h2>Add a Property</h2>
            
            <?php if(!empty($err)) { echo display_error($err); } ?>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table cellpadding="5">
            <tr>
            	<td><label for="name">Name
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="name" id="name" /></td>
            </tr>
            <tr>
            	<td><label for="rooms">No. of Rooms
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rooms" id="rooms" /></td>
            </tr>
            <tr>
            	<td><label for="fee">Management Fee
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="fee" id="fee" /></td>
            </tr>
            <tr>
            	<td><label for="owner">Landlord
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="owner" id="owner" /></td>
            </tr>
            <tr>
            	<td><label for="added">Date Added
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="added" id="added" /></td>
            </tr>
            <tr>
            	<td><input type="submit" name="submit" value="Save" /></td>
            </tr>
            </table>
            </form>
            
        </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>