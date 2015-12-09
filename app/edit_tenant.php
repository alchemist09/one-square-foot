<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); }
	if(!$ac->hasPermission('edit_tenant')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$session->message("Tenant edit failed. An invalid value was sent throught the URL");
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$fname    = $_POST['fname'];
		$lname    = $_POST['lname'];
		$phone    = $_POST['phone'];
		$id_num   = $_POST['id_num'];
		$email    = $_POST['email'];
		$business = $_POST['business'];
		$joined   = $_POST['joined'];
		
		if(empty($fname) || empty($lname) || empty($phone) 
		  || empty($id_num) || empty($joined)){
			$err = "Form fields marked with an asterix are required";  
		} else {
			$tenant->setFirstName($fname);
			$tenant->setLastName($lname);
			$tenant->setPhoneNumber($phone);
			$tenant->setNationalIdNumber($id_num);
			if(!empty($email)) { $tenant->setEmail($email); }
			if(!empty($business)) { $tenant->setBusinessName($business); }
			$tenant->setDateJoined($joined);
			
			if($tenant->save()){
				$session->message("The changes have been saved");
				redirect_to("tenants.php");	
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
    <h2>Edit Tenant Details</h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
        <td><label for="fname">First Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="fname" id="fname" value="<?php echo $tenant->getFirstName(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="lname">Last Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="lname" id="lname" value="<?php echo $tenant->getLastName(); ?>" /></td>
    </tr>
    <tr>
    <td><label for="phone">Phone No
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="phone" id="phone" value="<?php echo $tenant->getPhoneNumber(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="id_num">National ID No
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="id_num" id="id_num" value="<?php echo $tenant->getNationalIdNumber(); ?>" /></td>
    </tr>
    <tr>
        <td><label for="email">Email</label></td>
        <td><input type="text" name="email" id="email" value="<?php $tenant_email = $tenant->getEmail();  if(!empty($tenant_email)) { echo $tenant->getEmail(); } ?>" /></td>
    </tr>
    <tr>
        <td><label for="business">Business Name</label></td>
        <td><input type="text" name="business" id="business" value="<?php $tenant_biz = $tenant->getBusinessName(); if(!empty($tenant_biz)) { echo $tenant->getBusinessName(); } ?>" /></td>
    </tr>
    <tr>
        <td><label for="joined">Date Joined
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="joined" id="joined" value="<?php echo $tenant->getDateJoined(); ?>" /></td>
    </tr>
    <tr>
        <td colspan="2" align="right">
            <input type="submit" name="submit" value="Save" />&nbsp;<a href="tenants.php" class="btn-cancel"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>
    </div>        
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>