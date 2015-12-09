<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$mesg = "Rent arrears for the tenant could not be recorded. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$start  = $_POST['start_date'];
		$end    = $_POST['end_date'];
		$arrears = new Arrears();
		if(empty($start) || empty($end)){
			$err = "Form fields marked with an asterix are required";	
		} elseif(!valid_date_range($start, $end)){
			$err  = "You must specify arrears monthly. Specify a month by ";
			$err .= "choosing the start and end dates of the month";
		} elseif($tenant->hasPaidFullRent($tenant_id, $start, $end)){
			$err = "Tenant has already paid the full rent of the period you specified";	
		} elseif($arrears->arrearsExist($tenant_id, $start, $end)){
			$err  = "You cannot record arrears for a particular month more than once. ";
			$err .= "Go to the arrears menu and edit the amount entered as arrears";	
		} else {
			//$arrears = new Arrears();
			if($arrears->recordArrears($tenant_id, $start, $end)){
				$mesg = "Rent arrears for tenant recorded";
				$session->message($mesg);
				redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			} else {
				$err = "An error occured preventing the arrears from being recorded";	
			}
		}
		//$amount = $_POST['amount'];
		/*if(empty($start) || empty($end) || empty($amount)){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$arrears = new Arrears();
			$amount = (int)$arrears->removeCommasFromCurrency($_POST['amount']);
			$rent_pm = Room::findByTenantId($tenant_id)->getRent();
			$rent_pm = (int)$arrears->removeCommasFromCurrency($rent_pm);
			if($amount > $rent_pm){
				$err = "Arrears cannot exceed the tenants monthly rent";	
			} elseif(PaymentStatus::paymentStatusOK($tenant_id, $start, $end)){
				$mesg = "Tenant already paid the full rent for the specified period and therefore cannot have arrears";
				$session->message($mesg);
				redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			} elseif($arrears->arrearsExist($tenant->id, $start, $end)){
				$mesg = "You cannot record arrears for a particular period twice. Click on &quot;Edit&quot; on the arrears table to edit information about the arrears";
				$session->message($mesg);
				redirect_to("tenant_arrears.php?tid={$tenant_id}");	
			} else {
				$arrears->setTenantId($tenant->id);
				$arrears->setStartPeriod($start);
				$arrears->setEndPeriod($end);
				$arrears->setAmountOwed($amount);
				if($arrears->save()){
					$mesg = "Rent arrears for tenant recorded";
					$session->message($mesg);
					redirect_to("tenant_arrears.php?tid={$tenant_id}");	
				} else {
					$err = "An error occured preventing the arrears from being recorded. Please try again later";	
				}
			}
		}*/
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
    
    <h2>Record Rent Arrears</h2>
    
    <a id="btn-back" href="tenant_arrears.php?tid=<?php echo $tenant->id; ?>">&laquo;Back</a>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post">
    <table cellpadding="5">
    <tr>
    	<td><label for="start_date">Start Date(yy/mm/dd)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="start_date" name="start_date" /></td>
    </tr>
    <tr>
    	<td><label for="end_date">End Date(yy/mm/dd)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="end_date" name="end_date" /></td>
    </tr>
    <!--<tr>
    	<td><label for="amount">Amount(Ksh)
        <span class="required-field">*</span>:</label></td>
        <td><input type="text" id="amount" name="amount" /></td>
    </tr>-->
    <tr>
    	<td colspan="2" align="right">
        	<input type="submit" name="submit" value="Post Arrears" />&nbsp;&nbsp;<a href="tenant_arrears.php?tid=<?php echo $tenant->id; ?>" style="text-decoration:none;"><input type="button" value="Cancel" /></a>
        </td>
    </tr>
    </table>
    </form>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>