<?php
	
	/**
	 * PDF report generation class that enables the
	 * generation of multi-page reports that have 
	 * standard headers and footers
	 * 
	 * This class enables the generation of tenant rent
	 * payment records
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date June 17, 2015
	 */
	
	class PDF_Report_Tenant extends PDF_MC_Table
	{
		const PDF_MARGIN = 18;
		
		protected $_page_title;
		protected $_page_width;
		protected $_page_height;
		protected $_headings;
		protected $_upload_dir = "../images";
		protected $_prop_name;
		protected $_start;
		protected $_end;
		
		/**
		 * Overide FPDF Header() method to enable
		 * the creation of standard page headings
		 */
		public function Header()
		{
			$this->SetX(-1);
			$this->_page_width = $this->GetX() + 1;
			$this->SetY(-1);
			$this->_page_height = $this->GetY() + 1;
			$logo = $this->_imagePath('salesforce.png');
			$this->Image($logo, self::PDF_MARGIN, self::PDF_MARGIN, 50, 40, "png");
			$this->SetFont('Helvetica', 'B', 18);
			$this->SetTextColor(136, 146, 219);
			$this->Text(100, 36, "SALESFORCE INC");
			$this->SetFont('Times', '', 9);
			$this->SetTextColor(96);
			$this->Text(100, 48, "The Leading Estate Management Company");
			$this->SetLineWidth(1);
			$this->SetDrawColor(47);
			$this->Line(self::PDF_MARGIN, 70, $this->_page_width - self::PDF_MARGIN, 70);
			$this->SetFont('Helvetica', 'B', 15);
			$this->SetTextColor(184, 45, 212);
			$this->SetXY(0,80);
			$this->MultiCell($this->_page_width, 8, $this->_page_title, 0, 'C');
			$this->Ln();
			$info = "Rent Payment Statement for the period ".$this->getStartPeriod()." to ".$this->getEndPeriod();
			$this->SetFont('Arial', '', 10);
			$this->SetTextColor(24);
			$this->MultiCell($this->_page_width, 8, $info, 0, 'C');
			$this->SetY(self::PDF_MARGIN * 7);
			$this->SetFont('Helvetica', 'B', 8);
			$this->RowX($this->_headings, false);
		}
		
		/**
		 * Return the server path for an image to be used in header e.g. like a logo
		 * @param mixed $filename Name of the image file
		 * @return mixed $path
		 */
		protected function _imagePath($filename)
		{
			return $this->_upload_dir.'/'.$filename;
		}
		
		/**
		 * Overide FPDF Footer() method to enable 
		 * the creation of standard page footers
		 */
		public function Footer()
		{
			$this->SetFont('Helvetica', 'I', 8);
			$y = $this->_page_height - self::PDF_MARGIN / 2 - 8;
			$cell_width = $this->_page_width - self::PDF_MARGIN * 2;
			$this->SetXY(self::PDF_MARGIN, $y);
			$this->MultiCell($cell_width, 8, date('Y-m-d H:i:s'), 0, 'L');
			$this->SetXY(self::PDF_MARGIN, $y);
			$this->MultiCell($cell_width, 8, $this->PageNo().' of {nb}', 0, 'R');
		}
		
		/**
		 * Set the names of table headings of the report
		 * @param array $headings 
		 */
		public function set_headings($headings)
		{
			if(is_array($headings)){
				$this->_headings = $headings;	
			}
		}
		
		/**
		 * Set the title of the report
		 * @param string $title
		 */
		public function set_title($title)
		{
			$this->_page_title = $title;
		}
		
		/**
		 * Return the width of the page
		 * @return int $page_width
		 */
		public function get_page_width()
		{
			if(isset($this->_page_width)){
				return $this->_page_width;	
			}
		}
		
		/**
		 * Check a date for validity
		 * @param string $date
		 * @return boolean
		 */
		protected function _isValidDate($date)
		{
			$timestamp = strtotime($date);
			$date_format = strftime("%m-%d-%Y", $timestamp);
			$valid_date = str_replace("-", ",", $date_format);
			$date_parts = explode(",", $valid_date);
			$month = $date_parts[0];
			$day   = $date_parts[1];
			$year  = $date_parts[2];
			return checkdate($month, $day, $year) ? true : false;
		}
		
		/**
		 * Set the beginning period of rent collection
		 * @param string $start
		 */
		public function setStartPeriod($start)
		{
			if($this->_isValidDate($start)){
				$this->_start = $start;	
			}
		}
		
		/**
		 * Get the beginining period of rent collection
		 * @return string
		 */
		public function getStartPeriod()
		{
			if(isset($this->_start)){
				$timestamp = strtotime($this->_start);
				$date_format = strftime("%B %d, %Y", $timestamp);
				return $date_format;	
			}
		}
		
		/**
		 * Set the end period of rent collection
		 * @param string $end
		 */
		public function setEndPeriod($end)
		{
			if($this->_isValidDate($end)){
				$this->_end = $end;	
			}
		}
		
		/**
		 * Get the end period of rent collection
		 * @return string
		 */
		public function getEndPeriod()
		{
			if(isset($this->_end)){
				$timestamp = strtotime($this->_end);
				$date_format = strftime("%B %d, %Y", $timestamp);
				return $date_format;	
			}
		}
		
	}

?>