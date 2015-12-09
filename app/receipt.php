<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	/*echo var_dump($session->sessionVar('start'));
	echo '<br />';
	echo var_dump($session->sessionVar('end'));*/
	/*echo "\$_GET['id']";
	echo var_dump($_GET['id']);
	echo '<br />';
	echo 'Cast Type: ';
	echo var_dump((int)$_GET['id']);
	echo 'Session Last ID: ';
	echo var_dump($session->sessionVar('id'));*/
	if((isset($_GET['tid']) && !empty($_GET['tid'])) &&
	   (isset($_GET['type']) && !empty($_GET['type'])) ){
		$tenant_id = (int)$_GET['tid'];
		//$last_id = $session->sessionVar('id');
		if(isset($_GET['id']) && !empty($_GET['id'])) { $id = (int)$_GET['id']; }
		$type = $_GET['type'];
		if(!is_int($tenant_id)){
			$mesg = "Receipt for the transaction could not be generated. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");	
		} elseif(!valid_receipt($type)){
			$mesg = "Receipt for the transaction could not be generated. An invalid value was sent through the URL";
			$session->message($mesg);
			redirect_to("tenants.php");
		} else {
			switch($type){
				case "deposit":
				$deposit = Deposit::findByTenantId($tenant_id);
				break;
				
				case "deposit_kplc":
				$dpt_kplc = DepositKPLC::findByTenantId($tenant_id);
				break;
				
				case "deposit_eldowas":
				$dpt_eldowas = DepositEldowas::findByTenantId($tenant_id);
				break;
				
				case "rent":
				$rent = Rent::findById($id);
				break;
				
				case "arrears":
				$ap = ArrearsPaid::findById($id);
				break;	
			}			
			//echo var_dump($deposit);
		}
	} else {
		$mesg = "Operation Not Supported";
		$session->message($mesg);
		redirect_to("tenants.php");	
	}
	
	//$deposit = Deposit::findById(1);
	//$deposit = Deposit::findByTenantId(1);
	//echo var_dump($deposit);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../css/print.css" media="all" />
<title>Transaction Receipt</title>
<script type="text/javascript">
	function printDiv(){
		var prtContent = document.getElementById("printArea");
		var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
		WinPrint.document.write(prtContent.innerHTML);
		//WinPrint.document.close();
		WinPrint.focus();
		//WinPrint.print();
		//WinPrint.close();
	}
	
	function printWithCss()
	{
		// Get the HTML pf div
		var title = document.title;
		var divElements = document.getElementById('outerHTML').innerHTML;
		var printWindow = window.open('', '', 'left=0, top=0, width=640, height=480');	
		// Open the window
		printWindow.document.open();
		// Write the HTML to the new window, link to CSS file
		printWindow.document.write('<html><head><title>' + title + '</title><link rel="stylesheet" type="text/css" href="../css/print.css" media="all" /></head><body>');
		printWindow.document.write(divElements);
		printWindow.document.write('</body></html>');
		printWindow.document.close();
		printWindow.focus();
		printWindow.print();
		printWindow.close();
	}
</script>
</head>

<body style="margin:10px;">
	
    <?php 
		$mesg = $session->message();
		echo output_message($mesg);
		
		switch($type){
			case "deposit":	
			$deposit->buildDepositReceipt();
			//echo var_dump($deposit);
			break;
			
			case "deposit_kplc":
			$dpt_kplc->buildDepositReceipt();
			break;
			
			case "deposit_eldowas":
			$dpt_eldowas->buildDepositReceipt();
			break;
			
			case "rent":
			$rent->buildRentReceipt();
			//echo var_dump($rent);
			break;
			
			case "arrears":
			$ap->buildArrearsReceipt();
			//echo var_dump($ap);
			break;
		}
		
		
	?>
    
    <!-- <a href="javascript:printDiv()">Print Receipt</a> -->
    <a class="btn-action" id="print-receipt" href="javascript:printWithCss()">Print Receipt</a>
    <a id="btn-back" href="tenant.php?tid=<?php echo $tenant_id; ?>">&laquo;Back</a>

</body>
</html>