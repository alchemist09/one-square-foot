<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<?php require_once("../lib/init.php"); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to("../index.php"); } 
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to('view_users.php');
	}
?>
<?php
	$prop_id = $session->sessionVar("prop_id");
	//echo var_dump($prop_id);
	if(isset($_POST['submit'])){
		$start_date = $_POST['start_date'];
		$end_date   = $_POST['end_date'];
		if(empty($start_date) || empty($end_date)){
			$err = "Choose a month from which to display rent payments from";	
		} elseif(!valid_date_range($start_date, $end_date)){
			$err = "Reports can only be specified monthly";	
		} else {
			// continue with processing
			//echo var_dump($prop_id);
			$payments = Rent::getPaymentsFromProperty($prop_id, $start_date, $end_date);	
			//echo var_dump($payments);
		    $arrears_paid = ArrearsPaid::getPaidArrearsForProperty($prop_id, $start_date, $end_date);
			$arrears = Arrears::getOutstandingArrearsForProperty($prop_id, $start_date, $end_date);
			$expenses = Expense::findByPeriodForProperty($prop_id, $start_date, $end_date);
			
			$deposits = Deposit::findPaymentsForPeriodByProperty($prop_id, $start_date, $end_date);
			$refunds = Deposit::findRefundsForPeriodByProperty($prop_id, $start_date, $end_date);
			$refunds_kplc = DepositKPLC::findRefundsForPeriodByProperty($prop_id, $start_date, $end_date);
			$refunds_eldowas = DepositEldowas::findRefundsForPeriodByProperty($prop_id, $start_date, $end_date);
			$records = CollectionReport::buildRecords($prop_id, $start_date, $end_date);
			$deductions = CollectionReport::calcTotalDeductionsForPeriod($prop_id, $start_date, $end_date);
			//echo var_dump($records);
			//echo var_dump($arrears_paid);
		}
	} elseif(isset($_POST['report'])){
		// Generate PDF report	
		$start_date = $_POST['start_date'];
		$end_date   = $_POST['end_date'];
		if(empty($start_date) || empty($end_date)){
			$err = "Choose a period to use in generating report";	
		} else {
			// continue with processing
			//echo var_dump($prop_id);
			//Rent::generatePdfReport($prop_id, $start_date, $end_date);
			//Rent::runReport($prop_id, $start_date, $end_date);
		}
	} else {
		// Form not submitted
		$err = "";	
	}
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
    <!--<h3>Actions</h3>
    <div id="side-bar">
    <?php
        /*$actions = array("deposits" => "Tenant Deposits", "rent_collection" => "Rent Collection");
        echo create_action_links($actions);*/
    ?>
    </div> -->
    <div id="page">
    
    <h2>Rent Collection Reports</h2>
    
    <a id="btn-back" href="rent_collection.php">&laquo;Back</a>
    
    <?php 
        $mesg = $session->message();
        echo output_message($mesg);
    ?>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="date-form">
    <table cellpadding="5">
    <tr>
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
        <!-- <td><input type="submit" name="report" value="Generate PDF Report" /></td> -->
    </tr>
    </table>
    </form>
    
    <?php
        if(!empty($start_date) && !empty($end_date)){
            $property = Property::findById($prop_id);
            $timestamp_01 = strtotime($start_date);
            $timestamp_02 = strtotime($end_date);
            $textual_date_01 = strftime("%B %d, %Y", $timestamp_01);
            $textual_date_02 = strftime("%B %d, %Y", $timestamp_02);
            
            echo '<div id="outerHTML"><div id="report">';
            
            echo "<h3>".$property->getPropertyName()."</h3>";
            echo "<p style=\"margin-bottom:20px;\">Rent Collection Report for the Period {$textual_date_01} to {$textual_date_02}</p>";
        }
    ?>
    
    <?php if(!empty($records)){ ?>
    <table cellpadding="5" class="bordered" id="cr">
    <thead>
    <tr>
        <th>Room No</th>
        <th style="width:160px;">Tenant</th>
        <th>Receipt</th>
        <th>Rent/Month</th>
        <th style="width:75px;">Rent Paid</th>
        <th style="width:90px;">Rent Bal.P</th>
        <th style="width:90px;">Rent Arrears</th>
        <th style="width:70px;">Hse Dep</th>
        <th style="width:80px;">KPLC Dep</th>
        <th style="width:75px;">Eldo Dep</th>
        <th style="width:90px;">Totals</th>
        <th style="width:100px;">Remks</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($records as $rcd): ?>
    <?php 
        /*$tenant_id = $payment->getTenantId();
        $tenant = Tenant::findById($tenant_id);
        $room = Room::findById($tenant->getRoomId());*/
    ?>
    <tr>
        <td align="right"><?php echo $rcd->room_label; ?></td>
        <td align="right"><?php echo $rcd->tenant; ?></td>
        <td align="right"><?php echo $rcd->receipt_no; ?></td>
        <td align="right"><?php echo $rcd->rent_pm; ?></td>
        <td align="right"><?php echo $rcd->rent_paid; ?></td>
        <td align="right"><?php echo $rcd->arrears_paid; ?></td>
        <td align="right"><?php echo $rcd->arrears; ?></td>
        <td align="right"><?php echo $rcd->house_deposit; ?></td>
        <td align="right"><?php echo $rcd->kplc_deposit; ?></td>
        <td align="right"><?php echo $rcd->eldowas_deposit; ?></td>
        <td align="right"><?php echo $rcd->totals ?></td>
        <td align="right"><?php echo $rcd->remarks; ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td colspan="2" class="analysis">TOTAL</td>
        <td colspan="8"></td>
        <td class="analysis" align="right"><?php echo CollectionReport::calcTotalPaymentsForPeriod($prop_id, $start_date, $end_date); ?></td>
    </tr>
    <tr>
    	<td colspan="2" class="analysis">Commission( <?php echo $property->getManagementFee()."%"; ?> )</td>
        <td colspan="8"></td>
        <td class="analysis" align="right"><?php echo CollectionReport::calcCommissionOnCollection($prop_id, $start_date, $end_date); ?></td>
    </tr>
    <?php if(!empty($expenses)): ?>
    <?php foreach($expenses as $exp): ?>
    <tr>
    	<td colspan="2"><?php echo $exp->getName(); ?></td>
        <td><?php echo $exp->getDatePaid(); ?></td>
        <td colspan="7"></td>
        <td align="right"><?php echo $exp->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if(!empty($refunds)): ?>
    <?php foreach($refunds as $ref): ?>
    <tr>
    	<td colspan="2">Deposit Refund [ <?php echo $ref->getTenantName(); ?> ]</td>
        <td><?php echo $ref->getDateRefunded(); ?></td>
        <td colspan="7"></td>
        <td align="right"><?php echo $ref->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if(!empty($refunds_kplc)): ?>
    <?php foreach($refunds_kplc as $r_kplc): ?>
    <tr>
    	<td colspan="2">KPLC Refund [ <?php echo $r_kplc->getTenantName(); ?> ]</td>
        <td><?php echo $r_kplc->getDateRefunded(); ?></td>
        <td colspan="7"></td>
        <td align="right"><?php echo $r_kplc->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if(!empty($refunds_eldowas)): ?>
    <?php foreach($refunds_eldowas as $r_eld): ?>
    <tr>
    	<td colspan="2">ELDOWAS Refund [ <?php echo $r_eld->getTenantName(); ?> ]</td>
        <td><?php echo $r_eld->getDateRefunded(); ?></td>
        <td colspan="7"></td>
        <td align="right"><?php echo $r_eld->getPaymentAmount(); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    <tr>
    	<td colspan="2" class="analysis">TOTAL DEDUCTIONS</td>
        <td colspan="8"></td>
        <td align="right" class="analysis"><?php echo CollectionReport::calcTotalDeductionsForPeriod($prop_id, $start_date, $end_date); ?></td>
    </tr> 
    <tr>
    	<td colspan="2" class="analysis">LANDLORD BALANCE</td>
        <td colspan="8"></td>
        <td align="right" class="analysis"><?php echo CollectionReport::calcNetBanking($prop_id, $start_date, $end_date); ?></td>
    </tr>
    </tbody>
    </table><br /><br />
    <div id="director">
    <p>DIRECTOR</p>
    <p>Sign</p>
    _______________________________
    </div>
    
    <div id="landlord">
    <p>LANDLORD</p>
    <p>Sign</p>
    _______________________________
    </div>
    <?php } elseif(!empty($start_date) && !empty($end_date)) {
            echo '<p style="text-align:right;border:2px solid #3C3C3C; width: 100px; padding: 3px 6px;">Ksh. 0.00</p>';
        }
	?>
    <br  /><br />
    
    
    
    
    <?php if(!empty($start_date) && !empty($end_date)) { echo "</div></div>"; } ?>
    
    <a class="btn-action" id="print-report" href="javascript:printWithCss()">Print Report</a>
    <!--</div></div>-->
    
    <div id="clear_float"></div>
    
    </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>