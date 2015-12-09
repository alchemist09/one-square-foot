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
	if(isset($_GET['rid']) && !empty($_GET['rid'])){
		$room_id = (int)$_GET['rid'];
		if(!is_int($room_id)){
			$session->message("Room edit failed. An invalid value was sent through the URL");
			redirect_to("rooms.php");	
		} else {
			$room = Room::findById($room_id);
		}
	}
	
	//echo "<tt><pre>".var_dump($room)."</pre></tt>";
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$label   = $_POST['label'];
		$vacant  = $_POST['vacant'];
		$rent_pm = (int)$room->removeCommasFromCurrency($_POST['rent_pm']);
		
		if(empty($label) || empty($rent_pm)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$room->setRoomLabel($label);
			$room->setRoomStatus($vacant);
			$room->setRent($rent_pm);
			if($room->save()){
				$session->message("The changes have been saved");
				redirect_to("rooms.php");	
			} else {
				$err = "An error occured preventing the changes from being saved";	
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
        $actions = array("tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Details of this Room</h2>
    
    <a id="btn-back" href="rooms.php" title="go back">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_err($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="label">Room Label
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="label" id="label" value="<?php echo $room->getRoomLabel() ?>" /></td>
    </tr>
    <tr>
        <td><label for="vacant">Vacant
        <span class="required-field">*</span></label></td>
        <td>
            <select name="vacant" id="vacant_five">
                <option value="0" <?php if($room->isVacant()) { echo ' selected="selected"'; } ?>>Yes</option>
                <option value="1" <?php if(!$room->isVacant()) { echo ' selected="selected"'; } ?>>No</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="rent_pm">Rent/Month
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="rent_pm" id="rent_pm" value="<?php echo $room->getRent(); ?>" /></td>
    </tr>
    <tr>
        <td colspan="2" align="right">
            <input type="submit" name="submit" value="Save" />&nbsp;<a href="rooms.php" class="btn-cancel"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>