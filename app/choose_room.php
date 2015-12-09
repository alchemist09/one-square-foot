<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); } 
	$permissions = array('admin');	
	try {
		$ac->checkPermissions($permissions);	
	} catch(Exception $e){
		$mesg = $e->getMessage();
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	$prop_id = $session->sessionVar("prop_id");
	$rooms = Room::findVacantRoomsByPropertyId($prop_id);
?>
<?php
	
	if(isset($_POST['submit'])){
		$selected_room = $_POST['room_name'];
		if(empty($selected_room)){
			$err = "Please choose a room to assign a tenant";	
		} else {
			$session->sessionVar("room_id", $selected_room);
			$session->message("Room selection saved");
			redirect_to("add_tenant.php");
		}
	} else {
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
    <h2>Choose Room to Assign to Tenant</h2>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="room_name">Choose Room
        <span class="required-field">*</span></label></td>
        <td>
            <select name="room_name" id="room_name">
            <option value="" selected="selected">Choose Room</option>
        <?php foreach($rooms as $room): ?>
            <option value="<?php echo $room->id; ?>"><?php echo $room->getRoomLabel(); echo "&nbsp; [Ksh."; echo $room->getRent(); echo "]"; ?></option>
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