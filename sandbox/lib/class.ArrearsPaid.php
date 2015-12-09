<?php

	/**
	 * Class for manipulating information on payment
	 * of arrears. Helps for back referencing since 
	 * information about payment of arrears is deleted 
	 * automatically upon the complete payment of arrears
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date September 01, 2015
	 */
	
	class ArrearsPaid extends DatabaseObject
	{
		protected static $table_name = "arrears_paid";
		protected static $db_fields = array('id', 'tid', 'amount', 'start_date', 'end_date', 'date_paid', 'receipt_no', 'mode', 'agent');
		public $id;
		protected $_tid;
		protected $_amount;
		protected $_start_date;
		protected $_end_date;
		protected $_date_paid;
		protected $_receipt_no;
		protected $_mode;
		protected $_agent;
		
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
		 * Return the ID of the tenant who made the payment
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
		 * who made the payment
		 * @param int $tenant_id
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
		}
		
		/**
		 * Get the amount of money paid as payment of arrears
		 * @return int
		 */
		public function getPaymentAmount()
		{
			if(isset($this->_amount)){
				return number_format($this->_amount);	
			}
		}
		
		/**
		 * Set the amount of money paid as settlement of
		 * rent arrears
		 * @param int $amount
		 */
		public function setPaymentAmount($amount)
		{
			$amount = str_replace(",", "", $amount);
			$this->_amount = $amount;
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
		 * Lookup arrears payment details by the ID that identifies
		 * the tenant who made the payment
		 * @param int $tenant_id
		 * @return object An arrearsPaid object
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
		 * Get arrears payment of a given tenant for a particular period
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return array Array of arrearsPaid objects
		 */
		public static function findByPeriodForTenant($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT CONVERT(date_paid, DATE) BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1) AND tid = ".$tenant_id;
			return static::findBySql($sql);
		}
		
		/**
		 * Get arrears payment of all the tenants for a particular period
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying the end of the period
		 * @return array An array of arrearsPaid objects
		 */
		public static function findByPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT CONVERT(date_paid, DATE) BETWEEN '".$mysqli->real_escape_string($start)."' ";
			$sql .= "AND '".$mysqli->real_escape_string($end)."' = 1)";
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
			$sql .= "WHERE receipt_no = '".$mysqli->real_escape_string($receipt)."'";
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			return $found;
		}
		
		/**
		 * Calculate the total amount paid by tenant as rent arrears during 
		 * a specified period of time
		 * @param int $tenant_id ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $total_arrears_paid
		 */
		public static function calcAmountPaidByTenantDuringPeriod($tenant_id, $start, $end)
		{
			$arrears_paid = static::findByPeriodForTenant($tenant_id, $start, $end);
			$num_payments = count($arrears_paid);
			$total_amount = 0;
			$counter = 0;
			while($counter < $num_payments){
				$ap = $arrears_paid[$counter];
				$amount = $ap->getPaymentAmount();
				$amount = (int)str_replace(',', '', $amount);
				$total_amount += $amount;
				$counter++;
			}
			return number_format($total_amount);
		}
		
		/**
		 * Get oustanding arrears for a property that have been paid
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return array An array of ArrearsPaid objects
		 */
		public static function getPaidArrearsForProperty($prop_id, $start, $end)
		{
			$paid_arrears = static::findByPeriod($start, $end);
			$arrears_for_prop = array();
			for($i=0; $i < count($paid_arrears); $i++){
				$cur_bal = $paid_arrears[$i];
				$tenant = Tenant::findById($cur_bal->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$arrears_for_prop[] = $cur_bal;	
				}	
			}
			return $arrears_for_prop;
		}
		
		/**
		 * Calculate the total amount in outstanding arrears collected
		 * from a property during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string Date string specifying the start of the period
		 * @param string Date string specifying the end of the period
		 * @return int $total_collection
		 */
		public static function calcCollectionForPropertyDuringPeriod($prop_id, $start, $end)
		{
			$arrears = static::getPaidArrearsForProperty($prop_id, $start, $end);
			$num_arrears = count($arrears);
			$total_collection = 0;
			$count = 0;
			while($count < $num_arrears){
				$payment = $arrears[$count];
				$amount = $payment->getPaymentAmount();
				$amount = str_replace(",", "", $amount);
				$total_collection += (int)$amount;
				$count++;	
			}
			$total_collection = number_format($total_collection);
			return $total_collection;
		}
		
		/**
		 * Check if a particular property has outstanding arrears for a certain period paid
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return boolean
		 */
		public function propertyHasArrearsPaid($prop_id, $start, $end)
		{
			$arrears = static::getPaidArrearsForProperty($prop_id, $start, $end);
			return !empty($arrears) ? true : false;
		}
		
		/**
		 * Generate the HTML of the receipt print
		 */
		public function buildArrearsReceipt()
		{
			global $session;
			$html  = '<div id="outerHTML">';
			$html .= '<div id="printArea">';
    		$html .= '<h2 align="center">Salesforce</h2>';
			$html .= '<h3 align="center">Official Receipt: Arrears Payment</h3>';
			$html .= '<div id="receipt-header">';
			$html .= '<p align="center">';
			$html .= 'Kenyatta Street, New Muya House<br />';
			$html .= '2<sup>nd</sup> Flr, Room 105.<br />';
			$html .= 'Tel: + 254 721 156 315 &nbsp; / &nbsp; + 254 720 711 115<br />';
			$html .= 'www.salesforce.co.ke &nbsp; Email: info@salesforce.co.ke';
			$html .= '</p></div>';
			$html .= '<hr align="center" />';
			
			$html .= '<div id="receipt-body">';
			$html .= '<p><strong>Receipt No:</strong> &nbsp;<span style="color:#F00;">';
			$html .= $this->_receipt_no;
			$html .= '</span></p>';
			$html .= '<p><strong>Tenant:</strong> &nbsp;';
			
			$tenant = Tenant::findById($this->_tid);
			
			$html .= $tenant->getFullName().'</p>';
			$html .= '<p><strong>Room No:</strong> &nbsp;';
			$html .= Room::findById(Tenant::findById($this->_tid)->getRoomId())->getRoomLabel();
			$html .= '</p><p><strong>Payment Amount:</strong> &nbsp;';
			//$html .= number_format($session->sessionVar('amount'));
			$html .= number_format($this->_amount);
			
			if($tenant->hasArrears()){
				$arrears = new Arrears();
				$html .= '</p><p><strong>Arrears</strong>&nbsp;';
				$html .= $arrears->calcTotalArrearsFromTenant($this->_tid);	
			}
			
			$html .= '</p><p><strong>Month:</strong>&nbsp;';
			$html .= $this->_getMonthFromDate($this->_start_date);
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
		
	}

?>