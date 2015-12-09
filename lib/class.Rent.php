<?php

	/**
	 * Rent payment class tht handles collection of
	 * rent from tenants, arrears, and the total
	 * amount of money collected for a particular property
	 * during a specified period of time
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date May 22, 2015
	 */
	
	class Rent extends DatabaseObject
	{
		protected static $table_name = "rent";
		protected static $db_fields = array('id', 'pid', 'tid', 'start_date', 'end_date', 'date_paid', 'amount', 'receipt_no', 'mode', 'agent', 'remarks');
		public $id;
		protected $_pid;
		protected $_tid;
		protected $_start_date;
		protected $_end_date;
		protected $_date_paid;
		protected $_amount;
		protected $_receipt_no;
		protected $_mode;
		protected $_agent;
		protected $_remarks;
		
		/**
		 * Magic __construct
		 * @todo Initialize the $receipt_no property by
		 * the receipt number generated during the last transaction
		 */
		function __construct()
		{
			$this->_receipt_no = $this->_lastReceiptNo();
		}
		
		/**
		 * Automatically generate the receipt number issued
		 * for the last transaction
		 * @return int
		 */
		protected function _lastReceiptNo()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT receipt_no FROM ".static::$table_name." ";
			$sql .= "ORDER BY id DESC LIMIT 1";
			$result = $mysqli->query($sql);
			$row = $result->fetch_assoc();
			$receipt_no = $row['receipt_no'];
			$int_val = intval($receipt_no);
			return $int_val;	
		}
		
		/**
		 * Get the ID that identifies the property
		 * for which a rent payment was made
		 * @return int
		 */
		public function getPropertyId()
		{
			if(isset($this->_pid)){
				return $this->_pid;	
			}
		}
		
		/**
		 * Set the ID that identifies the property 
		 * for which the rent payment was made
		 * @param int $prop_id
		 */
		public function setPropertyId($prop_id)
		{
			if(is_int($prop_id)){
				$this->_pid = $prop_id;	
			}
		}
		
		/**
		 * Get the ID that identifies the tenant
		 * who made the payment
		 * @return int
		 */
		public function getTenantId()
		{
			if(isset($this->_tid)){
				return $this->_tid;	
			}
		}
		
		/**
		 * Set the ID that identifies the tenant 
		 * who made the rent payment
		 * @param int $tenant_id
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
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
		 * Get start period for which the payment covers
		 * @return string
		 */
		public function getStartPeriod()
		{
			if(isset($this->_start_date)){
				return $this->_start_date;	
			}
		}
		
		/**
		 * Set the end period for which the payment covers
		 * @param string $start_date
		 */
		public function setStartPeriod($start_date)
		{
			if($this->_isValidDate($start_date)){
				$this->_start_date = $start_date;
			}
		}
		
		/**
		 * Get the end period for which the payment covers
		 * @return string
		 */
		public function getEndPeriod()
		{
			if(isset($this->_end_date)){
				return $this->_end_date;
			}
		}
		
		/**
		 * Set the end period for which the payment covers
		 * @param string $end_date
		 */
		public function setEndPeriod($end_date)
		{
			if($this->_isValidDate($end_date)){
				$this->_end_date = $end_date;	
			}
		}
		
		/**
		 * Get date the rent payment was paid
		 * @return string
		 */
		public function getDatePaid()
		{
			if(isset($this->_date_paid)){
				return $this->_date_paid;	
			}
		}
		
		/**
		 * Set the date and time the rent payment was made
		 * @todo Use current timestamp to create a datetime string
		 */
		public function setDatePaid()
		{
			$this->_date_paid = strftime("%Y-%m-%d %H:%I:%S", time());
		}
		
		/**
		 * Get the amount of money paid for rent 
		 * for a particular month
		 * @return int
		 */
		public function getPaymentAmount()
		{
			if(isset($this->_amount)){
				return number_format($this->_amount);	
			}
		}
		
		/**
		 * Set the amount of money paid for rent 
		 * for a particular month
		 * @param int $amount
		 */
		public function setPaymentAmount($amount)
		{
			$amount = str_replace(",", "", $amount);
			$this->_amount = $amount;
		}
		
		/**
		 * Get the receipt number issued for the transaction
		 * @retun string
		 */
		public function getReceiptNo()
		{
			if(isset($this->_receipt_no)){
				return $this->_receipt_no;	
			}
		}
		
		/**
		 * Set the receipt number issued for the transaction
		 * @todo Pad the transaction_id code with zeros to
		 * create unique numbers of uniform length
		 */
		public function generateReceiptNo()
		{
			$digits = 6;
			$raw_num = ++$this->_receipt_no;
			$number = str_pad($raw_num, $digits, '0', STR_PAD_LEFT);
			$this->_receipt_no = $number;
		}
		
		/**
		 * Get the mode of payment used for making the rent payment
		 * @return string
		 */
		public function getPaymentMode()
		{
			if(isset($this->_mode)){
				return $this->_mode;	
			}
		}
		
		/**
		 * Set the mode of payment used for making the rent payment
		 * @param string $payment_mode
		 */
		public function setPaymentMode($payment_mode)
		{
			if(preg_match('/^[a-zA-Z_]+$/', $payment_mode)){
				$this->_mode = $payment_mode;
			}
		}
		
		/**
		 * Get the person from the company who received the rent payment
		 * @return string
		 */
		public function getReceivingAgent()
		{
			if(isset($this->_agent)){
				return $this->_agent;	
			}
		}
		
		/**
		 * Set the application user receiving the rent payment
		 * @param object $sessionObj A session object
		 */
		public function setReceivingAgent(Session $sessionObj)
		{
			$user = User::findById($sessionObj->userID);
			$this->_agent = $user->getFullName();
		}
		
		/**
		 * Check first name or last name for validity
		 * @param string $name
		 * @return boolean
		 */
		protected function _isValidName($name)
		{
			$valid_string = "/^[a-zA-Z0-9\"\'\.\s\-]+$/";
			return preg_match($valid_string, $name) ? true : false;
		}
		
		/**
		 * Get any remarks associated with this payment
		 * @return string
		 */
		public function getRemarks()
		{
			if(isset($this->_remarks)){
				return $this->_remarks;	
			}
		}
		
		/**
		 * Set any additional remarks that may accompany a rent payment
		 * @param string $remarks
		 */ 
		public function setRemarks($remarks="")
		{
			if($this->_isValidName($remarks)){
				$this->_remarks = $remarks;	
			}
		}
		
		/**
		 * Lookup rent payment details by the ID that identifies
		 * the tenant who made the payment
		 * @param int $tenant_id
		 * @return array An array of rent objects
		 */
		public static function findByTenantId($tenant_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE tid = ".$mysqli->real_escape_string($tenant_id);
			return static::findBySql($sql);
		}
		
		/**
		 * Find a rent payment that was made by a particular tenant during 
		 * a specified period
		 * @param int $tenant_id ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return object A rent object
		 */
		public static function findByPeriodForTenant($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE CONVERT(date_paid, DATE) ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1 AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			return !empty($result) ? array_shift($result) : NULL;
		}
		
		/**
		 * Get the rent payments from a property that is
		 * identified by the ID passed in as parameter
		 * @param int $prop_id
		 * @return array An array of rent objects
		 */
		public static function findByPropertyId($prop_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE pid = ".$mysqli->real_escape_string($prop_id);
			return static::findBySql($sql);
		}
		
		/**
		 * Lookup a payment by its receipt no
		 * @param string $receipt The receipt number issued for the transaction
		 * @return object A payment object
		 */
		public static function findByReceiptNo($receipt)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE receipt_no = '".$receipt."'";
			$result = static::findBySql($sql);
			$found = !empty($result) ?  array_shift($result) : NULL;
			return $found;
		}
		
		/**
		 * Get the rent payments from a specified property for a given month
		 * @param int $prop_id The ID used to identfy a property
		 * @month string $start The start period of rent payment
		 * @param string $end The end period of rent payment
		 * @return array An array of rent objects
		 */
		public static function getPaymentsFromProperty($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT CONVERT(date_paid, DATE) BETWEEN ";
			$sql .= "'".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1) ";
			$sql .= "AND pid = ".$mysqli->real_escape_string($prop_id);
			return static::findBySql($sql);
		}
		
		/**
		 * Calculate the total amount of rent collected from a property
		 * during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start A date specifying the start period of payment
		 * @param string $end The end period of rent collection
		 * @return int $total_amount The total amount collected during the specified period
		 */
		public static function calcTotalCollection($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqil = $db->getConnection();
			$payments = static::getPaymentsFromProperty($prop_id, $start, $end);
			$num_payments = count($payments);
			$total_collection = 0;
			$count = 0;
			while($count < $num_payments){
				$payment = $payments[$count];
				$amount = $payment->getPaymentAmount();
				$amount = str_replace(",", "", $amount);
				$total_collection += (int)$amount;
				$count++;	
			}
			$total_collection = number_format($total_collection);
			return $total_collection;
		}
		
		/**
		 * Remove commas from a currency string
		 * @param string $string
		 * @return string
		 */
		protected function _sanitizeMoneyString($string)
		{
			return str_replace(",", "", $string);
		}
		
		/**
		 * Calculate the sum of rent collection and paid arrears for 
		 * a particular property during a specified period
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying the end of the period
		 * @return int $collection_summary
		 */
		public static function calcCollectionSummary($prop_id, $start, $end)
		{
			$rent_collection = static::calcTotalCollection($prop_id, $start, $end);
			$paid_arrears = ArrearsPaid::calcCollectionForPropertyDuringPeriod($prop_id, $start, $end);
			$rent_collection = (int)str_replace(',', '', $rent_collection);
			$paid_arrears = (int)str_replace(',', '', $paid_arrears);
			$collection_summary = $rent_collection + $paid_arrears;
			return number_format($collection_summary);
		}
		
		/**
		 * Calculate the value charged as management fee based on
		 * the from rent collected during a particular period
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying the end of the period
		 * @return int $mgt_fee Value charged as management fee
		 */
		public function calcManagementFee($prop_id, $start, $end)
		{
			$rent = static::calcCollectionSummary($prop_id, $start, $end);
			$rent = (int)str_replace(',', '', $rent);
			$percentage_fee = Property::findById($prop_id)->getManagementFee();
			$mgt_fee = $rent * ($percentage_fee / 100);
			$mgt_fee = round($mgt_fee, 2);
			return number_format($mgt_fee, 2);
		}
		
		/**
		 * Calculate the amount remaining after deducting management fee
		 * from the amount collected from a particular property over a specifed
		 * period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $balance Rent balance after deducting management fee
		 */
		public function calcBalanceAfterMgtFee($prop_id, $start, $end)
		{
			$rent = static::calcCollectionSummary($prop_id, $start, $end);
			$mgt_fee = $this->calcManagementFee($prop_id, $start, $end);
			$rent = (int)str_replace(',', '', $rent);
			$mgt_fee = str_replace(',', '', $mgt_fee);
			$mgt_fee = floatval($mgt_fee);
			$balance = $rent - $mgt_fee;
			return number_format($balance, 2);
		}
		
		/**
		 * Calculate the net amount remaining after subtracting expenses
		 * and management fee from the amount collected for a particular
		 * property over a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $amount_less_expense
		 */
		public function calcAmoutLessExpenses($prop_id, $start, $end)
		{
			$bal_after_mgt_fee = $this->calcBalanceAfterMgtFee($prop_id, $start, $end);
			$bal_after_mgt_fee = (int)str_replace(',', '', $bal_after_mgt_fee);
			$total_expenses = Expense::calcTotalExpenses($prop_id, $start, $end);
			$total_expenses = (int)str_replace(',', '', $total_expenses);
			$net_banking = $bal_after_mgt_fee - $total_expenses;
			if($bal_after_mgt_fee == 0 || $bal_after_mgt_fee < $total_expenses){
				return number_format(0, 2);	
			}
			return number_format($net_banking, 2);
		}
		
		/**
		 * Calculate rent amount after adding deposit payments to the amount that
		 * was arrived at after subtracting expenses
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $amount_plus_deposit_payments
		 */
		public function calcAmountPlusDepositPayments($prop_id, $start, $end)
		{
			$bal_after_expenses = $this->calcAmoutLessExpenses($prop_id, $start, $end);
			$bal_after_expenses = (int)$this->_sanitizeMoneyString($bal_after_expenses);
			$deposit_payments = Deposit::calcPaymentsForPeriodByProperty($prop_id, $start, $end);
			$deposit_payments = (int)$this->_sanitizeMoneyString($deposit_payments);
			$new_balance = $bal_after_expenses + $deposit_payments;
			return number_format($new_balance, 2);
		}
		
		/**
		 * Calculate rent amount after deducting deposit refunds from the amount
		 * that was arrived at after adding deposit payments to the previous balance
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $amount_less_deposit_refunds
		 */
		public function calcAmountLessDepositRefunds($prop_id, $start, $end)
		{
			$balance_plus_dep = $this->calcAmountPlusDepositPayments($prop_id, $start, $end);
			$balance_plus_dep = (int)$this->_sanitizeMoneyString($balance_plus_dep);
			$deopsit_refunds = Deposit::calcRefundsForPeriodByProperty($prop_id, $start, $end);
			$deopsit_refunds = (int)$this->_sanitizeMoneyString($deopsit_refunds);
			$new_balance = $balance_plus_dep - $deopsit_refunds;
			return number_format($new_balance, 2);
		}
		
		/**
		 * Calculate the net amount remaining after deducting expenses, management fee,
		 * and deposit refunds, then adding deposit payments to the amount collected 
		 * for a particular property over a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $net_amount
		 */
		public function calcNetBanking($prop_id, $start, $end)
		{
			return $this->calcAmountLessDepositRefunds($prop_id, $start, $end);
		}
		
		/**
		 * Generate a PDF report of rent collection for a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start A date specifying the start period of collection
		 * @param string $end The end period of rent collection
		 * @return file A PDF file containing the report in tabular format
		 */
		public static function generatePdfReport($prop_id, $start, $end)
		{
			$hdgs = array('name', 'room_no', 'date_paid', 'receipt_no', 'amount');
			$aligns = array('L', 'L', 'L', 'L', 'R');
			$widths = NULL;
			$property = Property::findById($prop_id);
			$ttl = $property->getPropertyName();
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			
			// Fetch rent payments for period
			$sql  = "SELECT CONCAT_WS(' ', t.fname, t.lname) AS name, rm.label AS room_no, ";
			$sql .= "rt.date_paid, rt.receipt_no, FORMAT(rt.amount,0) AS amount FROM tenants t ";            $sql .= "RIGHT JOIN room rm ON rm.id = t.rid RIGHT JOIN rent rt ON ";
			$sql .= "rt.tid = t.id WHERE rt.date_paid >= '".$mysqli->real_escape_string($start);
			$sql .= "' AND rt.date_paid <= '".$mysqli->real_escape_string($end)."' ";
			$sql .= "AND rt.pid = ".$mysqli->real_escape_string($prop_id);
			$result = $mysqli->query($sql);
			
			// Check Arrears Paid
			$qry  = "SELECT CONCAT_WS(' ', t.fname, t.lname) AS name, rm.label AS room_no, ";
			$qry .= "DATE_FORMAT(ap.start_date, '%M %Y') AS month, ap.date_paid, ap.receipt_no, ";
			$qry .= "FORMAT(ap.amount, 0) as amount FROM tenants t RIGHT JOIN room rm ON ";
			$qry .= "rm.id = t.rid RIGHT JOIN arrears_paid ap ON ap.tid = t.id WHERE (SELECT ";
			$qry .= "CONVERT(ap.date_paid, DATE) BETWEEN '".$mysqli->real_escape_string($start);
			$qry .= "' AND '".$mysqli->real_escape_string($end)."' = 1)";
			$arrears_paid = $mysqli->query($qry);
			
			// Check Outstanding Arrears
			$arr  = "SELECT CONCAT_WS(' ', t.fname, t.lname) AS name, rm.label AS room_no, ";
			$arr .= "DATE_FORMAT(ar.start_date, '%M %Y') AS month, FORMAT(ar.amount, 0) AS ";
			$arr .= "amount FROM tenants t RIGHT JOIN room rm ON rm.id = t.rid RIGHT JOIN ";
			$arr .= "arrears ar ON ar.tid = t.id";
			$arrears = $mysqli->query($arr);
			
			// Expenses
			$exp  = "SELECT name AS name_of_expense, DATE_FORMAT(start_date, '%M %Y') AS month, ";
			$exp .= "incurred AS date_made, FORMAT(amount, 0) AS amount FROM expense ";
			$exp .= "WHERE (SELECT incurred BETWEEN '".$mysqli->real_escape_string($start)."' ";
			$exp .= "AND '".$mysqli->real_escape_string($end)."' = 1)"; 
			$expenses = $mysqli->query($exp);
			
			if($result->num_rows  == 0){
				return false;
			} else {
				$report = new Report();
				$report->pdf($ttl, $result, $arrears_paid, $arrears, $expenses, $hdgs, $widths, $aligns, 'P', 'A4', $prop_id, $start, $end);	
			}
		}
		
		/**
		 * Retrieve rent payment records from tenant over a specified period of time
		 * @param int $ten_id The tenant ID
		 * @param string $start A date specifying the beginning period of rent collection
		 * @param string $end A date specifying the end period of rent collection
		 * @return array $rentObjs An array of rent objects
		 */
		public function getPaymentsFromTenant($ten_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT CONVERT(date_paid, DATE) BETWEEN '";
			$sql .= $mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1) ";
			$sql .= "AND tid = ".$mysqli->real_escape_string($ten_id);
			return static::findBySql($sql);
		}
		
		/**
		 * Generate the rent payment statement from a tenant as a PDF document
		 * @param int $tenant_id The ID used to identify the tenant for which the report belongs
		 * @param string $start The start period of the rent payment
		 * @param string $end The end period of rent payment
		 * @return file A PDF file containing the record
		 */
		public function getPaymentHistoryInPdf($tenant_id, $start, $end)
		{
			$hdgs = array('Start Period(yy-mm-dd)', 'End Period(yy-mm-dd)', 'Date Paid', 'Receipt No', 'Amount');
			$aligns = array('L', 'L', 'L', 'L', 'R');
			$widths = NULL;
			$ttl = Tenant::findById($tenant_id)->getFullName();
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			
			// Fetch Rent Payments for Tenant during  period
			$sql  = "SELECT start_date, end_date, date_paid, receipt_no, FORMAT(amount, 0) ";
			$sql .= " AS amount FROM rent WHERE (SELECT CONVERT(date_paid, DATE) ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1) AND tid = ".$tenant_id;
			$result = $mysqli->query($sql);
			
			// Check Arrears Paid
			$qry  = "SELECT start_date, end_date, date_paid, receipt_no, FORMAT(amount, 0) ";
			$qry .= " AS amount FROM arrears_paid WHERE (SELECT CONVERT(date_paid, DATE) ";
			$qry .= "BETWEEN '".$mysqli->real_escape_string($start);
			$qry .= "' AND '".$mysqli->real_escape_string($end)."' = 1) AND tid = ".$tenant_id;
			$arrears_paid = $mysqli->query($qry);
			
			if($result->num_rows == 0){
				return false;
			} else {
				$report = new Report();
				$report->pdfStmt($ttl, $result, $arrears_paid, $hdgs, $widths, $aligns, 'P', 'A4', $start, $end);	
			}
		}
		
		/**
		 * Generate the HTML of the receipt print
		 */
		public function buildRentReceipt()
		{
			$html  = '<div id="outerHTML">';
			$html .= '<div id="printArea">';
    		$html .= '<h2 align="center">Salesforce</h2>';
			$html .= '<h3 align="center">Official Receipt: Rent Payment</h3>';
			$html .= '<div id="receipt-header">';
			$html .= '<p align="center">';
			$html .= 'Kenyatta Street, New Muya House<br />';
			$html .= '2<sup>nd</sup> Flr, Room 105.<br />';
			$html .= 'Tel: + 254 721 156 315 &nbsp; / &nbsp; + 254 720 711 115<br />';
			$html .= 'www.salesforce.co.ke &nbsp; Email: info@salesforce.co.ke';
			$html .= '</p></div>';
			$html .= '<hr align="center" />';
			
			$html .= '<div id="receipt-body">';
			$html .= '<p>';
			$html .= '<strong>Receipt No:</strong> &nbsp;<span style="color:#F00;">';
			$html .= $this->_receipt_no;
			$html .= '</span></p>';
			$html .= '<p><strong>Tenant:</strong> &nbsp;';
			
			$tenant = Tenant::findById($this->_tid);
			
			$html .= $tenant->getFullName().'</p>';
			$html .= '<p><strong>Room No:</strong> &nbsp;';
			$html .= Room::findById(Tenant::findById($this->_tid)->getRoomId())->getRoomLabel();
			$html .= '</p><p><strong>Month:</strong>&nbsp;';
			$html .= $this->_getMonthFromDate($this->_start_date);
			$html .= '</p><p><strong>Payment Amount:</strong> &nbsp;';
			$html .= number_format($this->_amount);
			
			if($tenant->hasArrears()){
				$arrears = new Arrears();
				$html .= '</p><p><strong>Arrears:</strong>&nbsp;';
				$html .= $arrears->calcTotalArrearsFromTenant($this->_tid);	
			}
			
			$html .= '</p><p><strong>Company Agent:</strong> &nbsp;';
			$html .= $this->_agent.'</p>';
			$html .= '<p><strong>Date:</strong> &nbsp;';
			$html .= $this->_date_paid." &nbsp;&nbsp;";
			$html .= '</p></div>';
			$html .= '</div></div>';
			print $html;
		}
		
		/**
		 * Parse a date string to generate a textual 
		 * representation of month and year
		 * @param string $date The date string to format
		 * @return string
		 */
		protected function _getMonthFromDate($date)
		{
			$timestamp = strtotime($date);
			$date_format = strftime("%B, %Y", $timestamp);
			return $date_format;
		}
		
		/**
		 * Get the month and year for which a payment was made
		 * @return mixed
		 */
		public function getMonth()
		{
			return $this->_getMonthFromDate($this->_start_date);
		}
		
		/**
		 * Retrieve the ID of the last recorded transaction
		 * @return int
		 */
		public static function lastPaymentId()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql = "SELECT * FROM ".static::$table_name." ORDER BY id DESC LIMIT 1";
			$result = $mysqli->query($sql);
			$record = $result->fetch_assoc();
			$last_insert_id = $record['id'];
			return $last_insert_id;
		}
		
		/**
		 * Edit the amount paid as rent for a particular month
		 * @param int $amount The new payment amount
		 * @return boolean
		 */
		public function editPayment($amount)
		{
			$start = $this->_start_date;
			$end   = $this->_end_date;
			$tenant_id = $this->_tid;
			$rent_pm = Room::findByTenantId($tenant_id)->getRent();
			$rent_pm = (int)str_replace(',', '', $rent_pm);
			$amount = (int)str_replace(',', '', $amount);
			$curr_amount = (int)$this->_amount;
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			if(PaymentStatus::paymentStatusOK($tenant_id, $start, $end) &&
			   ($amount < $rent_pm))
			{
				// Change payment status for tenant during period
				$statusObj = PaymentStatus::findByPeriod($tenant_id, $start, $end);	
				$statusObj->setStatus(0);
				// Create a new arrears object
				$arrears = new Arrears();
				$bal_owed = $rent_pm - $amount;
				$arrears->setTenantId($tenant_id);
				$arrears->setStartPeriod($start);
				$arrears->setEndPeriod($end);
				$arrears->setAmountOwed($bal_owed);
				// Change Amount 
				$this->setPaymentAmount($amount);
				$mysqli->autocommit(false);
				$arrears->save();
				$statusObj->save();
				$this->save();
				if(!$mysqli->commit()){
					$mysqli->rollback();
					$mysqli->autocommit(true);
					return false;	
				} else {
					$mysqli->autocommit(true);
					return true;	
				}
			} elseif(!PaymentStatus::paymentStatusOK($tenant_id, $start, $end) &&
			        ($amount == $rent_pm))
			{
				// Change payment status for tenant during period
				$statusObj = PaymentStatus::findByPeriod($tenant_id, $start, $end);	
				$statusObj->setStatus(1);
				// Find corresponding arrears
				$arrears = Arrears::findByPeriodForTenant($tenant_id, $start, $end);
				// Change Amount 
				$this->setPaymentAmount($amount);
				$mysqli->autocommit(false);
				$arrears->delete();
				$statusObj->save();
				$this->save();
				if(!$mysqli->commit()){
					$mysqli->rollback();
					$mysqli->autocommit(true);
					return false;	
				} else {
					$mysqli->autocommit(true);
					return true;	
				}	
			} elseif(!PaymentStatus::paymentStatusOK($tenant_id, $start, $end) &&
					 ($amount < $rent_pm))
			{
				// reduce amount paid and increase amount owed
				$arrears = Arrears::findByPeriodForTenant($tenant_id, $start, $end);
				$new_arrears = $rent_pm - $amount;
				$arrears->setAmountOwed($new_arrears);
				$this->setPaymentAmount($amount);
				$mysqli->autocommit(false);
				$arrears->save();
				$this->save();
				if(!$mysqli->commit()){
					$mysqli->rollback();
					$mysqli->autocommit(true);
					return false;	
				} else {
					$mysqli->autocommit(true);
					return true;
				}
			}
		}
		
		/**
		 * Delete a rent payment
		 * @todo Delete records from both the rent and payment_status tables
		 * @return boolean
		 */
		public function deletePayment()
		{
			$start = $this->getStartPeriod();
			$end   = $this->getEndPeriod();
			$tenant_id = $this->_tid;
			$payment_status = PaymentStatus::findByPeriod($tenant_id, $start, $end);
			$arrears = Arrears::findByPeriodForTenant($tenant_id, $start, $end);
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$mysqli->autocommit(false);
			if(!is_null($arrears)){
				if($arrears->arrearsExist($tenant_id, $start, $end)){
					$arrears->deleteTenantArrearsForPeriod($start, $end);
				}
			}
			$payment_status->delete();
			$this->delete();
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			} else {
				$mysqli->autocommit(true);
				return true;	
			}
		}
		
	}

?>