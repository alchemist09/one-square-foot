<?php require_once("../lib/init.php"); ?>
<?php require_once("../lib/class.Fpdf.php") ?>
<?php require_once("../lib/class.PDF_MC_Table.php"); ?>
<?php require_once("../lib/class.PDF_Report.php"); ?>
<?php require_once("../lib/class.Report.php"); ?>
<?php

	$pdf = new FPDF("P", "pt", "A4");
	$pdf->SetFont("Times", "", 50);
	$pdf->AddPage("P", "A4");
	//$pdf->Text(100, 200, "Hello World");
	/**$pdf->SetLineWidth(2);
	$pdf->SetDrawColor(50, 50, 50);
	$pdf->SetFillColor(220);
	$pdf->Rect(50, 150, 100, 100, 'DF');
	$pdf->SetLineWidth(6);
	$pdf->SetDrawColor(190);
	$pdf->Line(30, 300, 300, 400);
	$pdf->Image("../images/android.png", 60, 160, 50, 50);*/
	
	/**$pdf->Image("../images/zend_framework.png", 50, 20, 100);
	$pdf->SetFont("Helvetica", "B", 36);
	$pdf->SetTextColor(84, 118, 230);
	$pdf->Text(170, 56, "Zend Technologies");
	$pdf->Output("fpdf-drawing", "d");*/
	
	function setup_page($pdf, &$margin_left, &$margin_top, &$height, &$width){
		$pdf->AddPage();
		$pdf->SetX(-1);
		$width = $pdf->GetX() + 1;
		$pdf->SetY(-1);
		$height = $pdf->GetY()+ 1;
		$pdf->SetFillColor(220);
		$pdf->Rect(0, 0, $width, $height, 'F');
		$inset = 18;
		$pdf->SetLineWidth(6);
		$pdf->SetDrawColor(190);
		$pdf->SetFillColor(255);
		$pdf->Rect($inset, $inset, $width - 2 * $inset, $height - 2 * $inset, 'DF');
		$margin_left = $inset + 20;
		$margin_top = $inset + 20;
		$pdf->Image("../images/html5.png", $margin_left, $margin_top, 36, 36, "png");
		$x = $margin_left + 50;
		$pdf->SetFont("Helvetica", "BI", 16);
		$pdf->SetTextColor(220);
		$pdf->Text($x, $margin_top + 20, "HTML5 @ HACK REACTOR");
		$pdf->SetFont("Helvetica", "I", 9);
		$pdf->SetTextColor(180);
		$pdf->Text($x, $margin_top + 30, "220 Fool Street, River Park Drive, CA");
		$pdf->SetLineWidth(1);
		$pdf->Line($margin_left, $margin_top + 50, $width - $margin_left, $margin_top + 50);
		$pdf->SetFont("Times", "", 10);
		$pdf->SetTextColor(0);
	}
	
	/**$db = Database::getInstance();
	$mysqli = $db->getConnection();
	$sql = "SELECT * FROM users LIMIT 4";
	$result = $mysqli->query($sql);
	$doc = new FPDF('P', 'pt', array(5 * 72, 6 * 72));
	while($row = $result->fetch_assoc()){
		setup_page($doc, $margin_left, $margin_top, $height, $width);
		$doc->Text($margin_left, $margin_top + 80,
		"{$row['fname']} {$row['lname']}");
	}
	$doc->Output("letter-head", "D");*/
	
	///////////////// MULTICELL METHOD ///////////////////
	$body = <<<EOT
If you have not heard, our Spring 2013 Meadow Adventure is scheduled for Saturday, June 22. We will meet at the Caribou Ranch trailhead, about 2 miles north of Nederland (make a sharp left at CR 126). Make sure you are ready to go at 9 AM. Bring the usual gear and do not forget rainwear.

See you on the 22nd!

Regards,
Tom Swallowtail,
FRBC Event Coordinator
EOT;
	
	/**$db = Database::getInstance();
	$mysqli = $db->getConnection();
	$sql = "SELECT * FROM users LIMIT 4";
	$result = $mysqli->query($sql);
	$letter = new FPDF('P', 'pt', 'A4');
	while($user = $result->fetch_assoc()){
		$text = date('F j, Y').
		"\n\n Dear {$user['fname']} {$user['lname']}\n\n{$body}";
		setup_page($letter, $margin_left, $margin_top, $height, $width);
		$letter->SetXY($margin_left, $margin_top + 80);
		$letter->MultiCell($width - 2 * $margin_left, 12, $text, 0, 'L');	
	}
	$letter->Output('circular', 'D');*/
	
	//////////////////////////////////////////////////////
	////////////////// MULTICELL TABLE ///////////////////
	//////////////////////////////////////////////////////
	
	/**$db = Database::getInstance();
	$mysqli = $db->getConnection();
	$sql = "SELECT fname, lname, email, username, password, phone_no FROM users u";
	$result = $mysqli->query($sql);
	$report = new PDF_MC_Table('P', 'mm', 'A4');
	$report->SetFont('Helvetica', '', 9);
	$report->SetWidths(array(20, 20, 50, 25, 56, 25));
	$report->SetAligns(array('R', 'R', 'R', 'R', 'R', 'R'));
	$report->SetVerticalPadding(3);
	$report->AddPage();
	while($user = $result->fetch_row()){
		$report->RowX($user);	
	}
	$report->Output();*/
	
	///////////////////////////////////////////////////////
	////////////////// REPORT GENERATION //////////////////
	///////////////////////////////////////////////////////
	
	function generate_report(){
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$hdgs = array('First', 'Last', 'Email', 'Username', 'Phone No');
		//$hdgs = array('First', 'Last');
		$ttl = "Registered Users";
		$sql = "SELECT fname, lname, email, username, phone_no FROM users";
		/**$members = array(
			0 => array(0 => 'Andy', 1 => 'Gutmans'),
			1 => array(0 => 'Leon', 1 => 'Atkinson'),
			2 => array(0 => 'Stigg', 1 => 'Bakken')
		);*/
		//$report = new Report();
		//$report->pdf($ttl, $members, NULL, $hdgs, NULL, 'P', 'A4');	
		$result = $mysqli->query($sql);
		if($result->num_rows  == 0){
			echo "No records found";
		} else {
			$report = new Report();
			$report->pdf($ttl, $result, $hdgs, $widths, $aligns, $orientation, $page_size);
		}
	}
	
	//generate_report();
	
	function retrieve_rows($result_obj){
		$result_rows = array();
		while($row = $result_obj->fetch_row()){
			$result_rows[] = $row;	
		}	
		return $result_rows;
	}
	
	function run_test(){
		$db = Database::getInstance();
		$mysqli = $db->getConnection();
		$sql = "SELECT fname, lname, email, username, phone_no FROM users";
		$result = $mysqli->query($sql);
		/**while($row = $result->fetch_row()){
			$output[] = $row;	
		}	
		return $output;*/
		$result_rows = retrieve_rows($result);
		return $result_rows;
	}
	
	/**$output = run_test();
	echo "<tt><pre>".var_dump($output)."</pre></tt>";
	echo count($output);*/
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Generating PDFs with PHP and fPDF</title>
</head>

<body>



</body>
</html>