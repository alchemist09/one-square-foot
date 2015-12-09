<?php

	/**
	 * Report generation class
	 * Uses database to output reports for various entities in 
	 * either PDF, CSV, XML, or RTF formart
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date April 11, 2015
	 */
	
	class Report
	{
		const PDF_MARGIN = 18;
		
		
		/**
		 * Generate a report that outlines the rent collection for a particular 
		 * property over a specified period of time
		 * @param mixed $title The report title
		 * @param object $result_obj A result object obtained from a SELECT query
		 * @param object $arrears_paid_obj A result object obtained from a SELECT query
		 * @param object $arrears_obj A result object obtained from a SELECT query
		 * @param object $expenses_obj A result object obtained from a SELECT query
		 * @param array $headings Array of table heading names
		 * @param array $widths Array of column widths
		 * @param array $aligns Array of column alignments
		 * @param string $orientation The page orientation of the report
		 * @param mixed $page_size Dimensions of page that displays report
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 */
		public function pdf($title, $result_obj, $arrears_paid_obj, $arrears_obj, $expenses_obj, $headings = NULL, $widths = NULL, $aligns=NULL, $orientation = 'P', $page_size = 'A4', $prop_id, $start, $end)
		{
			define('HORZ_PADDING', 2);
			define('VERT_PADDING', 3);
			$filename = date('Y-m-d').'-report-'.uniqid().'.pdf';
			$pdf = new PDF_Report($orientation, 'pt', $page_size);
			$pdf->set_title($title);
			$pdf->setStartPeriod($start);
			$pdf->setEndPeriod($end);
			$pdf->SetX(-1);
			$page_width = $pdf->GetX() + 1;
			$pdf->AliasNbPages();
			$pdf->SetFont('Helvetica', '', 7);
			$pdf->SetLineWidth(.1);
			$pdf->SetMargins(self::PDF_MARGIN, self::PDF_MARGIN);
			$pdf->SetAutoPageBreak(true, self::PDF_MARGIN);
			$pdf->SetHorizontalPadding(HORZ_PADDING);
			$pdf->SetVerticalPadding(VERT_PADDING);
			$ncols = $this->columnCount($result_obj);
			
			// Instantiate Helper Variables
			$rent = new Rent();
			$total_collection = Rent::calcTotalCollection($prop_id, $start, $end);
			$paid_arrears = ArrearsPaid::calcCollectionForPropertyDuringPeriod($prop_id, $start, $end);
			$owed_arrears = Arrears::calcArrearsForPropertyDuringPeriod($prop_id, $start, $end);
			$collection_summary = Rent::calcCollectionSummary($prop_id, $start, $end);
			$mgt_fee = $rent->calcManagementFee($prop_id, $start, $end);
			$net_amount = $rent->calcBalanceAfterMgtFee($prop_id, $start, $end);
			$total_expenses = Expense::calcTotalExpenses($prop_id, $start, $end);
			$net_banking = $rent->calcNetBanking($prop_id, $start, $end);
			
			$rent_collection_array = array(0 => 'Rent Collection', 1 => $total_collection);
			$paid_arrears_array = array(0 => 'Paid Arrears', 1 => $paid_arrears);
			$summary_collection_array = array(0 => 'Total Collection', 1 => $collection_summary);
			$percentage_mgt_fee = Property::findById($prop_id)->getManagementFee();
			$percentage_mgt_fee = '('.$percentage_mgt_fee.'%)';
			$mgt_fee_array = array(0 => 'Management Fee'.$percentage_mgt_fee, 1 => $mgt_fee);
			$net_amount_array = array(0 => 'Net Collection (Balance after Management Fee)', 1 => $net_amount);
			$total_expenses_array = array(0 => 'Total Expenses', 1 => $total_expenses);
			$net_banking_array = array(0 => 'Banking(Money Deposited to Landlord\'s Account)', 1 => $net_banking);
			
			
			if(is_null($headings)){
				$headings = $this->columnHeadings($result_obj);
			}
			//$pdf->set_headings($headings);
			//$pdf->SetFont('Helvetica', 'B', 8);
			//$pdf->RowX($headings, false);/
			if(is_null($widths)){
				$w = ($page_width - 2 * self::PDF_MARGIN) / $ncols;
				for($i = 0; $i < $ncols; $i++){
					$widths[$i] = $w;	
				}
			}
			if(count($widths) == $ncols - 1){
				$n = 0;
				foreach($widths as $w){
					$n += $w;	
				}	
				$widths[$ncols - 1] = $page_width - 2 * self::PDF_MARGIN - $n;
			}
			$pdf->SetWidths($widths);
			if(!is_null($aligns)){
				$a = 'R';
				for($i=0; $i < $ncols; $i++){
					$aligns[$i] = $a;	
				}	
			}
			$pdf->SetAligns($aligns);
			$pdf->AddPage();
			$pdf->SetFont('Helvetica', 'B', 8);
			$pdf->RowX($headings, false);
			$pdf->SetFont('Helvetica', '', 7);
			while($row = $result_obj->fetch_row()){
				$pdf->RowX($row);
			}
			if(!is_null($total_collection)){
				$xPos = $page_width - (self::PDF_MARGIN + $w);
				$pdf->SetX($xPos);
				$pdf->SetFont('Times', 'B', 8);
				$text = "Total: ".$total_collection;
				$pdf->MultiCell($w, 12, $text, 1, 'R');
			}
			
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(0 + self::PDF_MARGIN);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->SetTextColor(24);
			$timestamp_start = strtotime($start);
			$timestamp_end   = strtotime($end);
			$text_arrears_paid  = "Arrears Collected for Period ";
			$text_arrears_paid .= strftime("%B %d, %Y", $timestamp_start);
			$text_arrears_paid .= " to ".strftime("%B %d, %Y", $timestamp_end);
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, $text_arrears_paid, 'B', 'C');
			$pdf->Ln();
			
			// Arrears Paid
			if($arrears_paid_obj->num_rows >= 1){
				$col_count_arrears_paid = $this->columnCount($arrears_paid_obj);
				$arrears_paid_headings = $this->columnHeadings($arrears_paid_obj);
				$arrears_paid_widths = array();
				$w = ($page_width - 2 * self::PDF_MARGIN) / $col_count_arrears_paid;
				for($i = 0; $i < $col_count_arrears_paid; $i++){
					$arrears_paid_widths[$i] = $w;	
				}	
				//$pdf->SetWidths(array(70, 70, 70, 70, 70, 70));
				$pdf->SetWidths($arrears_paid_widths);
				$arrears_paid_aligns = array();
				$a = 'R';
				for($i=0; $i < $col_count_arrears_paid; $i++){
					$arrears_paid_aligns[$i] = $a;	
				}
				$pdf->SetAligns($arrears_paid_aligns);
				$pdf->SetFont('Helvetica', 'B', 8);
				$pdf->RowX($arrears_paid_headings, false);
				$pdf->SetFont('Helvetica', '', 7);
				while($row = $arrears_paid_obj->fetch_row()){
					$pdf->RowX($row);
				}
				if(!is_null($paid_arrears)){
					$xPos = $page_width - (self::PDF_MARGIN + $w);
					$pdf->SetX($xPos);
					$pdf->SetFont('Times', 'B', 8);
					$text = "Total: ".$paid_arrears;
					$pdf->MultiCell($w, 12, $text, 1, 'R');
				}
			} else {
				$no_arrears = "Ksh. 0.00";
				$xPos = $page_width - (self::PDF_MARGIN + $w);
				$pdf->SetX($xPos);
				$pdf->MultiCell($w, 8, $no_arrears, 0, 'R');
				$pdf->Ln();	
			}
			
			// Outstanding Arrears
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(0 + self::PDF_MARGIN);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->SetTextColor(24);
			$arrears_owed  = "Outstanding Rent Arrears for the Period ";
			$arrears_owed .= strftime("%B %d, %Y", $timestamp_start);
			$arrears_owed .= " to ".strftime("%B %d, %Y", $timestamp_end);
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, $arrears_owed, 'B', 'C');
			$pdf->Ln();
			if($arrears_obj->num_rows >= 1){
				$col_count_arrears = $this->columnCount($arrears_obj);
				$arrears_headings = $this->columnHeadings($arrears_obj);
				$arrears_widths = array();
				$w = ($page_width - 2 * self::PDF_MARGIN) / $col_count_arrears;
				for($i = 0; $i < $col_count_arrears; $i++){
					$arrears_widths[$i] = $w;	
				}	
				//$pdf->SetWidths(array(70, 70, 70, 70, 70, 70));
				$pdf->SetWidths($arrears_widths);
				$arrears_aligns = array();
				$a = 'R';
				for($i=0; $i < $col_count_arrears; $i++){
					$arrears_aligns[$i] = $a;	
				}
				$pdf->SetAligns($arrears_aligns);
				$pdf->SetFont('Helvetica', 'B', 8);
				$pdf->RowX($arrears_headings, false);
				$pdf->SetFont('Helvetica', '', 7);
				while($row = $arrears_obj->fetch_row()){
					$pdf->RowX($row);
				}
				if(!is_null($owed_arrears)){
					$xPos = $page_width - (self::PDF_MARGIN + $w);
					$pdf->SetX($xPos);
					$pdf->SetFont('Times', 'B', 8);
					$text = "Total: ".$owed_arrears;
					$pdf->MultiCell($w, 12, $text, 1, 'R');
				}
			} else {
				$no_arrears = "Ksh. 0.00";
				$xPos = $page_width - (self::PDF_MARGIN + $w);
				$pdf->SetX($xPos);
				$pdf->MultiCell($w, 8, $no_arrears, 0, 'R');
				$pdf->Ln();
			}
			
			// Collection Summary
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(0 + self::PDF_MARGIN);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->SetTextColor(24);
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, 'Collection Summary', 'B', 'C');
			$pdf->Ln();
			$summary_aligns = array('R', 'R');
			$pdf->SetAligns($summary_aligns);
			$cell_width = $pdf->get_page_width() / 4;
			$summary_widths = array($cell_width, $cell_width);
			$pdf->SetWidths($summary_widths);
			$pdf->Ln();
			$xPos = $page_width - (self::PDF_MARGIN + ($cell_width * 2));
			//$pdf->SetX($xPos);
			$pdf->RowX($rent_collection_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($paid_arrears_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($summary_collection_array);
			$pdf->Ln();
			$pdf->Ln();
			//$pdf->SetX($xPos);
			$pdf->RowX($mgt_fee_array);
			//$pdf->SetX($xPos);
			//$pdf->RowX($net_amount_array);
			$pdf->Ln();
			$pdf->Ln();
			
			// Outline Expenses
			$pdf->SetX(0 + self::PDF_MARGIN);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->SetTextColor(24);
			$exp_incurred  = "Expenses for the Period ";
			$exp_incurred .= strftime("%B %d, %Y", $timestamp_start);
			$exp_incurred .= " to ".strftime("%B %d, %Y", $timestamp_end);
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, $exp_incurred, 'B', 'C');
			$pdf->Ln();
			if($expenses_obj->num_rows >= 1){
				$col_count_expenses = $this->columnCount($expenses_obj);
				$expense_headings = $this->columnHeadings($expenses_obj);
				$expense_widths = array();
				$w = ($page_width - 2 * self::PDF_MARGIN) / $col_count_expenses;
				for($i = 0; $i < $col_count_expenses; $i++){
					$expense_widths[$i] = $w;	
				}	
				//$pdf->SetWidths(array(70, 70, 70, 70, 70, 70));
				$pdf->SetWidths($expense_widths);
				$expense_aligns = array();
				$a = 'R';
				for($i=0; $i < $col_count_expenses; $i++){
					$expense_aligns[$i] = $a;	
				}
				$pdf->SetAligns($expense_aligns);
				$pdf->SetFont('Helvetica', 'B', 8);
				$pdf->RowX($expense_headings, false);
				$pdf->SetFont('Helvetica', '', 7);
				while($row = $expenses_obj->fetch_row()){
					$pdf->RowX($row);
				}
				if(!is_null($total_expenses)){
					$xPos = $page_width - (self::PDF_MARGIN + $w);
					$pdf->SetX($xPos);
					$pdf->SetFont('Times', 'B', 8);
					$text = "Total: ".$total_expenses;
					$pdf->MultiCell($w, 12, $text, 1, 'R');
				}
			} else {
				$no_expenses = "Ksh. 0.00";
				$xPos = $page_width - (self::PDF_MARGIN + $w);
				$pdf->SetX($xPos);
				$pdf->MultiCell($w, 8, $no_expenses, 0, 'R');
				$pdf->Ln();
			}
			
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(0 + self::PDF_MARGIN);
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->SetTextColor(24);
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, 'Banking', 'B', 'C');
			$banking_aligns = array('R', 'R');
			$pdf->SetAligns($banking_aligns);
			$cell_width = $pdf->get_page_width() / 4;
			$banking_widths = array($cell_width, $cell_width);
			$pdf->SetWidths($banking_widths);
			$pdf->Ln();
			$xPos = $page_width - (self::PDF_MARGIN + ($cell_width * 2));
			//$pdf->SetX($xPos);
			$pdf->RowX($net_amount_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($total_expenses_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($net_banking_array);
			$pdf->Ln();
			
			$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, 'Analysis', 'B', 'C');
			$pdf->Ln();
			$currProp = Property::findById($prop_id);
			$num_of_rooms = $currProp->getNumRooms();
			$num_occupied_rooms = $currProp->getNumRoomsOccupied();
			$occupancy_level = $currProp->calcOccupancyLevel();
			$monthly_collection = $currProp->calcExpectedMonthlyCollection();
			$expected_collection = $currProp->calcExpectedCollectionForPeriod($start, $end);
			$percentage_collection = $currProp->calcCollectionPercentageForPeriod($start, $end);
			
			$period = $currProp->getNumMonthsInPeriod($start, $end);
			$period = '( '.$period.' Month[s] )';
			
			$num_rooms_array = array(0 => 'Number of Rooms in Property', 1 => $num_of_rooms);
			$occupied_rooms_array = array(0 => 'No. of Occupied Rooms', 1 => $num_occupied_rooms);
			$occupancy_level_array = array(0 => 'Occupancy Level (%)', 1 => $occupancy_level.'%');
			$expected_monthly_collection = array(0 => 'Expected Monthly Collection', 1 => $monthly_collection);
			$amount_collected_array = array(0 => "Total Amount Collected in Period{$period}", 1 => $collection_summary);
			$expected_collection_array = array(0 => 'Expected Collection for Period', 1 => $expected_collection);
			$percentage_collection_array = array(0 => 'Collection Level (%)', 1 => $percentage_collection.'%');
						
			//$pdf->SetX($xPos);
			$pdf->RowX($num_rooms_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($occupied_rooms_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($occupancy_level_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($expected_monthly_collection);
			//$pdf->SetX($xPos);
			$pdf->RowX($amount_collected_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($expected_collection_array);
			//$pdf->SetX($xPos);
			$pdf->RowX($percentage_collection_array);
			$pdf->Output($filename, 'D');
		}
		
		/**
		 * Generate a report that outlines the rent payment history 
		 * for a particular tenant over a specified period of time
		 * @param mixed $title The report title
		 * @param object $result A result object obtained from a SELECT query
		 * @param object $arrears_paid_obj A result object obtained from a SELECT query
		 * @param array $headings Array of table heading names
		 * @param array $widths Array of column widths
		 * @param array $aligns Array of column aligns
		 * @param string $orientation The page orientation of the report
		 * @param mixed $page_size Dimensions of page that displays report
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 */
		public function pdfStmt($title, $result_obj, $arrears_paid_obj, $headings = NULL, $widths = NULL, $aligns=NULL, $orientation = 'P', $page_size = 'A4', $start, $end)
		{
			define('HORZ_PADDING', 2);
			define('VERT_PADDING', 3);
			$dir = 'output';
			$filename = date('Y-m-d').'-report-'.uniqid().'.pdf';
			$path = $dir.'/'.$filename;
			$pdf = new PDF_Report_Tenant($orientation, 'pt', $page_size);
			$pdf->set_title($title);
			$pdf->setStartPeriod($start);
			$pdf->setEndPeriod($end);
			$pdf->SetX(-1);
			$page_width = $pdf->GetX() + 1;
			$pdf->AliasNbPages();
			$pdf->SetFont('Helvetica', '', 7);
			$pdf->SetLineWidth(.1);
			$pdf->SetMargins(self::PDF_MARGIN, self::PDF_MARGIN);
			$pdf->SetAutoPageBreak(true, self::PDF_MARGIN);
			$pdf->SetHorizontalPadding(HORZ_PADDING);
			$pdf->SetVerticalPadding(VERT_PADDING);
			$ncols = $this->columnCount($result_obj);
			if(is_null($headings)){
				$headings = $this->columnHeadings($result_obj);
			}
			$pdf->set_headings($headings);
			if(is_null($widths)){
				$w = ($page_width - 2 * self::PDF_MARGIN) / $ncols;
				for($i = 0; $i < $ncols; $i++){
					$widths[$i] = $w;	
				}
			}
			if(count($widths) == $ncols - 1){
				$n = 0;
				foreach($widths as $w){
					$n += $w;	
				}	
				$widths[$ncols - 1] = $page_width - 2 * self::PDF_MARGIN - $n;
			}
			$pdf->SetWidths($widths);
			if(!is_null($aligns)){
				$a = 'R';
				for($i=0; $i < $ncols; $i++){
					$aligns[$i] = $a;	
				}	
			}
			$pdf->SetAligns($aligns);
			$pdf->AddPage();
			while($row = $result_obj->fetch_row()){
				$pdf->RowX($row);
			}
			
			$pdf->Ln();
			$pdf->Ln();
			
			if($arrears_paid_obj->num_rows >= 1){
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetX(0 + self::PDF_MARGIN);
				$pdf->SetFont('Arial', 'B', 10);
				$pdf->SetTextColor(24);
				$timestamp_start = strtotime($start);
				$timestamp_end   = strtotime($end);
				$text_arrears_paid  = "Rent Arrears Paid Between ";
				$text_arrears_paid .= strftime("%B %d, %Y", $timestamp_start);
				$text_arrears_paid .= " and ".strftime("%B %d, %Y", $timestamp_end);
				$pdf->MultiCell($pdf->get_page_width() - self::PDF_MARGIN * 2, 8, $text_arrears_paid, 'B', 'C');
				$pdf->Ln();
				$pdf->SetFont('Helvetica', '', 7);
				while($row = $arrears_paid_obj->fetch_row()){
					$pdf->RowX($row);
				}
			}
			$pdf->Output($filename, 'D');
		}
		
		/**
		 * Get the no. of columns returned from a SELECT statement
		 * @param object $result_obj A result object obtained from 
		 *  a query against the database
		 * @return int $ncols The no. of columns obtained from the query result
		 */
		public function columnCount($result_obj)
		{
			$field_count = $result_obj->field_count;
			return $field_count;
		}
		
		/**
		 * Get the name of columns returned from a SELECT statement
		 * @param object $result_obj A result object obtained from 
		 *  a query against the database
		 * @return array $colNames
		 */
		public function columnHeadings($result_obj)
		{
			$field_names = array();
			$field_info = $result_obj->fetch_fields();
			foreach($field_info as $value){
				$field_names[] = $value->name;	
			}
			return $field_names;
		}
		
		/**
		 * Generate an on-screen report
		 */
		public function html()
		{
			
		}
		
		/**
		 * Generate a report in CSV format
		 */
		public function csv()
		{
			
		}
		
	}

?>