<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('..index.php'); } 
	if(!$ac->hasPermission('edit_rent')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php

	if((isset($_GET['tid']) && !empty($_GET['tid'])) &&
	   (isset($_GET['aid']) && !empty($_GET['aid'])) ){
		$tenant_id = (int)$_GET['tid'];
		$arrears_id = (int)$_GET['aid'];
		if(!is_int($tenant_id) || !is_int($arrears_id)){
			$mesg = "Arrears could not be edited. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);
			if(is_null($tenant)){
				$mesg = "The tenant who owes the arreas could not be found in the system";
				$session->message($mesg);
				redirect_to("tenants.php");	
			}
			$arrears = Arrears::findById($arrears_id);
			if(is_null($arrears)){
				$mesg = "Information regarding the arrears could not be found";
				$session->message($mesg);
				redirect_to("tenant.php?tid={$tenant_id}");	
			} else {
				$start  = $arrears->formatDateForDatabase($arrears->getStartPeriod());
				$end    = $arrears->formatDateForDatabase($arrears->getEndPeriod());
				$amount = $arrears->getAmountOwed();	
			}
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$start_date  = $_POST['start_date'];
		$end_date    = $_POST['end_date'];
		$amount_owed = $_POST['amount'];
		if(empty($start_date) || empty($end_date) || empty($amount_owed)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$arrears = Arrears::findById($arrears_id);
			$amount = (int)$arrears->removeCommasFromCurrency($_POST['amount']);
			$rent_pm = Room::findByTenantId($tenant_id)->getRent();
			$rent_pm = (int)$arrears->removeCommasFromCurrency($rent_pm);
			if($amount > $rent_pm){
				$err = "Arrears cannot exceed the tenants monthly rent";	
			} elseif(PaymentStatus::paymentStatusOK($tenant_id, $start, $end)){
				$mesg = "Tenant already paid the full rent for the specified period and therefore cannot have arrears";
				$session->message($mesg);
				redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			} else {
				$arrears->setTenantId($tenant->id);
				$arrears->setStartPeriod($start_date);
				$arrears->setEndPeriod($end_date);
				$arrears->setAmountOwed($amount_owed);
				if($arrears->save()){
					$mesg = "Changes Saved";
					$session->message($mesg);
					redirect_to("tenant_arrears.php?tid={$tenant_id}");	
				} else {
					$err = "An error occured preventing the arrears from being recorded. Please try again later";	
				}
			}
		}
	} else {
		// Form not submitted
		$err = "";	
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>Edit Details of Arrears</h2>
    
    <a id="btn-back" href="tenant_arrears.php?tid=<?php echo $tenant->id; ?>">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="start_date">Start Date(yy/mm/dd)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="start_date" name="start_date" value="<?php echo $start; ?>" /></td>
    </tr>
    <tr>
    	<td><label for="end_date">End Date(yy/mm/dd)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="end_date" name="end_date" value="<?php echo $end; ?>" /></td>
    </tr>
    <tr>
    	<td><label for="amount">Amount(Ksh)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="amount" name="amount" value="<?php echo $amount; ?>" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        	<input type="submit" name="submit" value="Save Changes" />
            <a href="tenant_arrears.php?tid=<?php echo $tenant->id; ?>" title="cancel edit" style="text-decoration:none;"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>