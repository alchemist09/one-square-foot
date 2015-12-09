<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('create_room')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	if(isset($_POST['submit'])){
		// Initialize an Empty Array to Hold Query Values
		$values = array();
		// The message array holds messages
		$message = array();
		// An Array to Hold Error Messages
		$err_msg = array();
		
		// GET POST LENGTH
		$post_values = array();
		foreach($_POST['label'] as $key => $value) {
			if(!empty($_POST['label'][$key])) {
				$post_values[] = $_POST['label'][$key];	
			}	
		}
		
		$posted_length = count($post_values);
		
		if($posted_length >= 1) {
			
	    $db = Database::getInstance();
		$mysqli = $db->getConnection();
			
		for($i = 0; $i < $posted_length; $i++) {
			// Escape Submitted Values
			$label   = $mysqli->real_escape_string($_POST['label'][$i]);
			$vacant  = $mysqli->real_escape_string($_POST['vacant'][$i]);
			$rent_pm = $mysqli->real_escape_string($_POST['rent_pm'][$i]);
			
			// Check For Form Fields Submitted With Empty Values
			if(empty($label) || empty($rent_pm)) {
				$err_msg[] = "Form fields marked with an asterix are required";	
			} else {
				// Sanitize the Price To Remove Any Commas
				$rent_pm = sanitize_price($rent_pm);
				// Confirm That Size And Price Have Numeric Values
				if(is_numeric($rent_pm)) {					
					// Get the Location ID and Construct INSERT Query
					$prop_id = $session->sessionVar("prop_id");
					$room_data = "($prop_id, '$label', $vacant, '$rent_pm')";
					$values[] = $room_data;	
				} else {
					$err_msg[]  = "Rent/month can only be an integer value.";
				}
			}
				
		} // End For
		
		if(isset($values) && !empty($values)) {
			$query_values = implode(", ", $values);
			// Construct INSERT Query
			$sql = "INSERT INTO room (prop_id, label, occupied, rent_pm) VALUES {$query_values}";
			$mysqli->query($sql);
			
			// Confirm Successfull INSERT
			if($mysqli->affected_rows == $posted_length) {
				if($posted_length == 1) {
					$message[] = "The room details have been saved";
					$session->destroySessionVar("prop_id");	
				} else {
					$message[] = "All room details have been saved";	
				}	
			} else {
				// Not All Plot Details Could Be Saved
				$err_msg[] = "Only rooms submitted with correct details were saved. ";
				$err_msg[] = "Rooms submitted with invalid values for either their label or ";
				$err_msg[] = "rent/month could not be saved";	
			}	
		} // End isset($values)
		
		// Change Confirmation Messaging Variable to Strings
		if(isset($message) && !empty($message)) {
			$message_parts = implode("<br />", array_unique($message));	
		} 
		// Change The Error Messaging Variable to Strings
		if(isset($err_msg) && !empty($err_msg)) {
			$err_msg_parts = implode("<br />", array_unique($err_msg));	
		} 
		
		} else { // End if
			// Form Submitted With No Entries
			$empty_form  = "Form Empty. Add details of rooms you would like to add to the ";	
			$empty_form .= "property you have selected";
		}
			
	} else {
		// Form not submitted
		$empty_form = "";	
	}
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    	<?php include_layout_template("nav.php"); ?>
        <div id="main-content">
        	<h2>Add Room(s) to a Property</h2>
            
            <?php if(!empty($message_parts)) { echo output_message($message_parts); } ?>
			<?php if(!empty($err_msg_parts)) { echo display_error($err_msg_parts); } ?>
            <?php if(!empty($empty_form)) { echo display_error($empty_form); }?>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <fieldset>
            <legend>Room 1</legend>
            <table cellpadding="5">
            <tr>
            	<td><label for="label">Room Label
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="label[]" id="label" /></td>
            </tr>
            <tr>
            	<td><label for="vacant">Vacant
                <span class="required-field">*</span></label></td>
                <td>
                	<select name="vacant[]" id="vacant_one">
                    	<option value="0" selected="selected">Yes</option>
                        <option value="1">No</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td><label for="rent_pm">Rent/Month
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rent_pm[]" id="rent_pm" /></td>
            </tr>
            </table>
            </fieldset>
            <fieldset>
            <legend>Room 2</legend>
            <table cellpadding="5">
            <tr>
            	<td><label for="label">Room Label
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="label[]" id="label" /></td>
            </tr>
            <tr>
            	<td><label for="vacant">Vacant
                <span class="required-field">*</span></label></td>
                <td>
                	<select name="vacant[]" id="vacant_two">
                    	<option value="0" selected="selected">Yes</option>
                        <option value="1">No</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td><label for="rent_pm">Rent/Month
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rent_pm[]" id="rent_pm" /></td>
            </tr>
            </table>
            </fieldset>
            <fieldset>
            <legend>Room 3</legend>
            <table cellpadding="5">
            <tr>
            	<td><label for="label">Room Label
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="label[]" id="label" /></td>
            </tr>
            <tr>
            	<td><label for="vacant">Vacant
                <span class="required-field">*</span></label></td>
                <td>
                	<select name="vacant[]" id="vacant_three">
                    	<option value="0" selected="selected">Yes</option>
                        <option value="1">No</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td><label for="rent_pm">Rent/Month
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rent_pm[]" id="rent_pm" /></td>
            </tr>
            </table>
            </fieldset>
            <fieldset>
            <legend>Room 4</legend>
            <table cellpadding="5">
            <tr>
            	<td><label for="label">Room Label
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="label[]" id="label" /></td>
            </tr>
            <tr>
            	<td><label for="vacant">Vacant
                <span class="required-field">*</span></label></td>
                <td>
                	<select name="vacant[]" id="vacant_four">
                    	<option value="0" selected="selected">Yes</option>
                        <option value="1">No</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td><label for="rent_pm">Rent/Month
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rent_pm[]" id="rent_pm" /></td>
            </tr>
            </table>
            </fieldset>
            <fieldset>
            <legend>Room 5</legend>
            <table cellpadding="5">
            <tr>
            	<td><label for="label">Room Label
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="label[]" id="label" /></td>
            </tr>
            <tr>
            	<td><label for="vacant">Vacant
                <span class="required-field">*</span></label></td>
                <td>
                	<select name="vacant[]" id="vacant_five">
                    	<option value="0" selected="selected">Yes</option>
                        <option value="1">No</option>
                    </select>
                </td>
            </tr>
            <tr>
            	<td><label for="rent_pm">Rent/Month
                <span class="required-field">*</span></label></td>
                <td><input type="text" name="rent_pm[]" id="rent_pm" /></td>
            </tr>
            </table>
            </fieldset>
            <table>
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