<?php require_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php

	if(isset($_GET['tid']) && !empty($_GET['tid'])){
		$tenant_id = (int)$_GET['tid'];
		if(!is_int($tenant_id)){
			$session->message("Tenant details not found. An invalid value was sent through the URL");
			redirect_to("tenants.php");	
		} else {
			$tenant = Tenant::findById($tenant_id);	
		}
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		$start_date = $_POST['start_date'];
		$end_date   = $_POST['end_date'];
		//$tenant_id  = $_POST['ten_id'];
		if(empty($start_date) || empty($end_date)){
			$err = "Choose a month from which to display rent payments from";	
		} else {
			// continue with processing
			$tenant = Tenant::findById($tenant_id);
			//echo var_dump($tenant);
			$payments = $tenant->getPaymentRecord($tenant->id, $start_date, $end_date);	
			//echo var_dump($payments);
			$arrears_paid = ArrearsPaid::findByPeriodForTenant($tenant->id, $start_date, $end_date);
		}
	} elseif(isset($_POST['report'])){
		// Generate PDF report	
		$start_date = $_POST['start_date'];
		$end_date   = $_POST['end_date'];
		//$tenant_id  = $_POST['ten_id'];
		if(empty($start_date) || empty($end_date)){
			$err = "Choose a period to use in generating report";	
		} else {
			$tenant = Tenant::findById($tenant_id);
			$tenant->generatePdfRecord($start_date, $end_date);
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
    <h2>Rent Payment Record for <?php echo $tenant->getFullName(); ?></h2>
    
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant->id; ?>" title="go back">&laquo;Back</a>
        
    <?php
        if(!empty($err)) { echo display_error($err); }
    ?>
        
    <form action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>" method="post" class="date-form">
    <table cellpadding="5">
    <tr><!--<input type="hidden" name="ten_id" value="<?php //echo $tenant_id; ?>" /> -->
        <td><label for="start_date">Start Date
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="start_date" id="start_date" /></td>
    </tr>
    <tr>
        <td><label for="end_date">End Date
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="end_date" id="end_date" /></td>
    </tr>
    <tr>
        <td><input type="submit" name="submit" value="Show Payments" /></td>
        <td><input type="submit" name="report" value="Generate PDF Report" /></td>
    </tr>
    </table>
    </form>
    
    <?php 
        if(!empty($start_date) && !empty($end_date)){
            echo '<div id="outerHTML"><div id="report">';
            $timestamp_01 = strtotime($start_date);
            $timestamp_02 = strtotime($end_date);
            $textual_date_01 = strftime("%B %d, %Y", $timestamp_01);
            $textual_date_02 = strftime("%B %d, %Y", $timestamp_02);
            echo "<h3>".$tenant->getFullName()."</h3>";
            echo "<p style=\"margin-bottom:20px;\">Rent Payment Statement Between {$textual_date_01} and {$textual_date_02}</p>";
        }
    ?>
    <?php if(!empty($payments)){ ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
        <th>Start Period(yy-mm-dd)</th>
        <th>End Period(yy-mm-dd)</th>
        <th>Date Paid</th>
        <th>Receipt No.</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($payments as $payment): ?>
    <tr>
        <td align="right"><?php echo $payment->getStartPeriod(); ?></td>
        <td align="right"><?php echo $payment->getEndPeriod(); ?></td>
        <td align="right"><?php echo $payment->getDatePaid(); ?></td>
        <td align="right"><?php echo $payment->getReceiptNo(); ?></td>
        <td align="right"><?php echo $payment->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?php } elseif(isset($payments) && empty($payments)) { ?>
    <?php
        $mesg = "No rent payment record was found within that specified period";
        echo output_message($mesg);
    ?>
    <?php } ?>
    
    <?php if(!empty($arrears_paid)){ ?>
    <?php
        $para = "<p style=\"margin-bottom:20px;\">Rent Arrears Paid Between {$textual_date_01} and {$textual_date_02}</p>";
        echo $para;
    ?>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
        <th>Start Period(yy-mm-dd)</th>
        <th>End Period(yy-mm-dd)</th>
        <th>Date Paid</th>
        <th>Receipt No.</th>
        <th>Amount</th>
    </tr>
    <tbody>
    <?php foreach($arrears_paid as $ap): ?>
    <tr>
        <td align="right"><?php echo $ap->getStartPeriod(); ?></td>
        <td align="right"><?php echo $ap->getEndPeriod(); ?></td>
        <td align="right"><?php echo $ap->getDatePaid(); ?></td>
        <td align="right"><?php echo $ap->getReceiptNo(); ?></td>
        <td align="right"><?php echo $ap->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </thead>
    </table>
    <?php } ?>
    
    <?php echo '</div></div>'; ?>
     <a class="btn-action" id="print-report" href="javascript:printWithCss()">Print Report</a>   
    </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>