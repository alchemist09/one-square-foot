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
	
	if(isset($_POST['submit'])){
		$fname    = $_POST['fname'];
		$lname    = $_POST['lname'];
		$phone    = $_POST['phone'];
		$id_num   = $_POST['id_num'];
		$email    = $_POST['email'];
		$business = $_POST['business'];
		$joined   = $_POST['joined'];
		
		if(empty($fname) || empty($lname) || empty($phone) || empty($id_num) ||
		   empty($joined)){
			$err = "Form fields marked with an asterix are required";	   
		} else {
			// Continue with processing
			$prop_id = (int)$session->sessionVar("prop_id");
			$room_id = (int)$session->sessionVar("room_id");
			$tenant = new Tenant();
			$tenant->setPropertyId($prop_id);
			$tenant->setRoomId($room_id);
			$tenant->setFirstName($fname);
			$tenant->setLastName($lname);
			$tenant->setPhoneNumber($phone);
			$tenant->setNationalIdNumber($id_num);
			if(!empty($email)) { $tenant->setEmail($email); }
			if(!empty($business)) { $tenant->setBusinessName($business); }
			$tenant->setDateJoined($joined);
			#echo var_dump($tenant);
			if($tenant->createNewTenant($room_id)){
				$session->message("Tenant added");
				redirect_to("tenants.php");
			} else {
				$err = "An error occured preventing the tenant from being added. Try again later";	
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
    
    <h2>Add Tenant(s)</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="fname">First Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="fname" /></td>
    </tr>
    <tr>
        <td><label for="lname">Last Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="lname" /></td>
    </tr>
    <tr>
        <td><label for="phone">Phone No
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="phone" /></td>
    </tr>
    <tr>
        <td><label for="id_num">National ID No
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="id_num" /></td>
    </tr>
    <tr>
        <td><label for="email">Email</label></td>
        <td><input type="text" name="email" /></td>
    </tr>
    <tr>
        <td><label for="business">Business Name</label></td>
        <td><input type="text" name="business" /></td>
    </tr>
    <tr>
        <td><label for="joined">Date Joined
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="joined" id="joined" /></td>
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