<?php

	/**
	 * Class to handle rent deposits from tenants
	 * @author Luke <mugapedia@gmail.com>
	 * @date Aug 12, 2015
	 */
	
	class Deposit extends DatabaseObject
	{
		protected static $table_name = "deposits";
		protected static $db_fields = array('id', 'tid', 'rid', 'tenant_name', 'amount', 'receipt_no', 'agent', 'date_paid', 'date_ref', 'status');
		public $id;
		protected $_tid;
		protected $_rid;
		protected $_tenant_name;
		protected $_amount;
		protected $_receipt_no;
		protected $_agent;
		protected $_date_paid;
		protected $_date_ref;
		protected $_status;
		
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
		 * Get the amount of money paid for deposit 
		 * for a particular room
		 * @return int
		 */
		public function getPaymentAmount()
		{
			if(isset($this->_amount)){
				return number_format($this->_amount);	
			}
		}
		
		/**
		 * Set the amount of money paid for deposit 
		 * for a particular room
		 * @param int $amount
		 */
		public function setPaymentAmount($amount)
		{
			$amount = str_replace(",", "", $amount);
			$this->_amount = $amount;
		}
		
		/**
		 * Get the ID that identifies the tenant who 
		 * made the payment
		 * @return int
		 */
		public function getTenantId()
		{
			if(isset($this->_tid)){
				return $this->_tid;	
			}
		}
		
		/**
		 * Set the tid property to the ID of the 
		 * tenant who made the payment
		 * @param int $tenant_id
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
		}
		
		/**
		 * Get the ID that identifies the room for which
		 * the deposit was made
		 * @return int
		 */
		public function getRoomId()
		{
			if(isset($this->_rid)){
				return $this->_rid;	
			}
		}
		
		/**
		 * Set the rid property to the ID of the
		 * room occupied by the tenant
		 * @param int $tenant_id
		 */
		public function setRoomId($tenant_id)
		{
			$room = Room::findByTenantId($tenant_id);
			$this->_rid = $room->id;
		}
		
		/**
		 * Get the name of the tenant who made the payment
		 * @return string
		 */
		public function getTenantName()
		{
			if(isset($this->_tenant_name)){
				return $this->_tenant_name;	
			}
		}
		
		/**
		 * Set the tenant name property to the name
		 * of the tenant who made the payment
		 * @param int $tenant_id
		 */
		public function setTenantName($tenant_id)
		{
			$tenant = Tenant::findById($tenant_id);
			$this->_tenant_name = $tenant->getFullName();
		}
		
		/**
		 * Set the name of the company employee who
		 * received and issued receipt for the payment
		 * @todo Use the user_id session variable to retrieve the name of the user
		 */
		public function setAgent()
		{
			$user_id = (int)$_SESSION['user_id'];
			$user = User::findById($user_id);
			$this->_agent = $user->getFullName();
		}
		
		/**
		 * Get the name of the company employee who
		 * received and issued receipt for the payment
		 * @return string
		 */
		public function getAgent()
		{
			if(isset($this->_agent)){
				return $this->_agent;	
			}
		}
		
		/**
		 * Check if a date is a valid Gregorian date
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
		 * Ge the date the deposit payment as made
		 * @return string
		 */
		public function getDatePaid()
		{
			if(isset($this->_date_paid)){
				return $this->_date_paid;	
			}
		}
		
		/**
		 * Set the date the tenant paid deposit for the room
		 * @param string $date_paid
		 */
		public function setDatePaid($date_paid)
		{
			if($this->_isValidDate($date_paid)){
				$this->_date_paid = $date_paid;	
			}
		}
		
		/**
		 * Ge the date the deposit refund was made
		 * @return string
		 */
		public function getDateRefunded()
		{
			if(isset($this->_date_ref)){
				return $this->_date_ref;	
			}
		}
		
		/**
		 * Set the date the tenant was refunded the deposit
		 * @param string $date_paid
		 */
		public function setDateRefunded($date_ref)
		{
			if($this->_isValidDate($date_ref)){
				$this->_date_ref = $date_ref;	
			}
		}
		
		/**
		 * Get the status of the deposit to determine whether 
		 * it has been refunded or not
		 * @return int
		 */
		public function getStatus()
		{
			if(isset($this->_status)){
				return $this->_status;	
			}
		}
		
		/**
		 * Set the status of the payment to determine whether
		 * it has been refunded or not
		 * @param int $status
		 */
		public function setStatus($status)
		{
			$status = (int)$status;
			if($status == 0 || $status == 1){
				$this->_status = $status;	
			}
		}
		
		/**
		 * Lookup a tenant deposit by the specified tenant ID
		 * @param int $tenant_id
		 * @return object A deposit object
		 */
		public static function findByTenantId($tenant_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE tid = ".$mysqli->real_escape_string($tenant_id);
			$found = static::findBySql($sql);
			return !empty($found) ? array_shift($found) : NULL;
		}
		
		/**
		 * Find a deposit payment for a particular tenant that was made during
		 * a specified period
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying start of period
		 * @param strinng $end Date string specifying end of period
		 * @return object A deposit object
		 */
		public static function findByPeriodForTenant($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE date_paid BETWEEN '";
			$sql .= $mysqli->real_escape_string($start)."' AND '".$mysqli->real_escape_string($end);
			$sql .= "' = 1 AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			return !empty($result) ? array_shift($result) : NULL;
		}
		
		/**
		 * Find all deposits that have not been refunded yet
		 * @return array An array of deposit objects
		 */
		public static function findActiveDeposits()
		{
			$sql = "SELECT * FROM ".static::$table_name." WHERE status = 0";
			return static::findBySql($sql);
		}
		
		/**
		 * Find all deposits that have been refunded
		 * @return array An array of deposit objects
		 */
		public static function findRefundedDeposits()
		{
			$sql = "SELECT * FROM ".static::$table_name." WHERE status = 1";
			return static::findBySql($sql);
		}
		
		/**
		 * Retrieve all active deposits for a particular property
		 * @param int $prop_id The ID used to identify the property
		 * @return array An array of deposit objects
		 */
		public static function findByPropertyId($prop_id)
		{
			$deposits = static::findActiveDeposits();
			$property_deposits = array();
			$num_deposits = count($deposits);
			$counter = 0;
			while($counter < $num_deposits){
				$dpt = $deposits[$counter];
				$tenant = Tenant::findById($dpt->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$property_deposits[] = $dpt;
				}	
				$counter++;
			}
			return $property_deposits;
		}
		
		/**
		 * Get all the deposits that have been refunded for a particular property
		 * @param int $prop_id ID used to identify the property
		 * @return array An array of deposit objects
		 */
		public static function findRefundedByPropertyId($prop_id)
		{
			$refunds = static::findRefundedDeposits();
			$property_deposits = array();
			$num_refunds = count($refunds);
			$counter = 0;
			while($counter < $num_refunds){
				$dpt = $refunds[$counter];
				$tenant = Tenant::findById($dpt->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$property_deposits[] = $dpt;
				}	
				$counter++;
			}
			return $property_deposits;
		}
		
		/**
		 * Calculate the amount of deposits held by the company
		 * for a particular property
		 * @param int $prop_id The ID used to identify the property
		 * @return int $total_deposits_for_prop
		 */
		public static function calcDepositsForProperty($prop_id)
		{
			$deposits = static::findByPropertyId($prop_id);
			$num_deposits = count($deposits);
			$total_deposits = 0;
			$counter = 0;
			while($counter < $num_deposits){
				$dpt = $deposits[$counter];
				$amount = $dpt->getPaymentAmount();
				$amount = (int)str_replace(',', '', $amount);
				$total_deposits += $amount;
				$counter++;
			}
			return number_format($total_deposits);
		}
		
		/**
		 * Calculate the entire amount of deposit refunds that have been made
		 * for a particular property
		 * @param int $prop_id The ID used to identify the property
		 * @return int $total_amount_of_refunds
		 */
		public static function calcRefundsForProperty($prop_id)
		{
			$refunds = static::findRefundedByPropertyId($prop_id);
			$num_refunds = count($refunds);
			$total_refunds = 0;
			$counter = 0;
			while($counter < $num_refunds){
				$dpt = $refunds[$counter];
				$amount = $dpt->getPaymentAmount();
				$amount = (int)str_replace(',', '', $amount);
				$total_refunds += $amount;
				$counter++;
			}
			return number_format($total_refunds);
		}
		
		/**
		 * Get a record of all the deposit payments that were made
		 * during a particular period for a specific property
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return array An array of deposit objects
		 */
		public static function findPaymentsForPeriodByProperty($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE (SELECT date_paid ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1) AND status = 0";
			$deposits = static::findBySql($sql);
			$property_deposits = array();
			$num_deposits = count($deposits);
			$counter = 0;
			while($counter < $num_deposits){
				$dpt = $deposits[$counter];
				$tenant = Tenant::findById($dpt->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$property_deposits[] = $dpt;
				}	
				$counter++;
			}
			return $property_deposits;
		}
		
		/**
		 * Get a record of all deposit refunds that were made during specified period
		 * for a particular property
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return array Array of deposit objects
		 */
		public static function findRefundsForPeriodByProperty($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE date_ref ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1 AND status = 1";
			$refunds = static::findBySql($sql);
			$property_deposits = array();
			$num_refunds = count($refunds);
			$counter = 0;
			while($counter < $num_refunds){
				$dpt = $refunds[$counter];
				$tenant = Tenant::findById($dpt->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$property_deposits[] = $dpt;
				}	
				$counter++;
			}
			return $property_deposits;	
		}
		
		/**
		 * Calculate the amount of deposit payments made for a particular property
		 * during a specified period
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $total_amount_of_payments
		 */
		public static function calcPaymentsForPeriodByProperty($prop_id, $start, $end)
		{
			$deposits = static::findPaymentsForPeriodByProperty($prop_id, $start, $end);
			$num_deposits = count($deposits);
			$total_deposits = 0;
			$counter = 0;
			while($counter < $num_deposits){
				$dpt = $deposits[$counter];
				$amount = $dpt->getPaymentAmount();
				$amount = (int)str_replace(',', '', $amount);
				$total_deposits += $amount;
				$counter++;
			}
			return number_format($total_deposits);
		}
		
		/**
		 * Calculate the amount of deposit refunds made for a particular property
		 * during a specified period
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return int $total_amount_of_refunds
		 */
		public static function calcRefundsForPeriodByProperty($prop_id, $start, $end)
		{
			$refunds = static::findRefundsForPeriodByProperty($prop_id, $start, $end);
			$num_refunds = count($refunds);
			$total_refunds = 0;
			$counter = 0;
			while($counter < $num_refunds){
				$dpt = $refunds[$counter];
				$amount = $dpt->getPaymentAmount();
				//$amount = (int)str_replace(',', '', $amount);
				$total_refunds += $amount;
				$counter++;
			}
			number_format($total_refunds);
		}
		
		/**
		 * Generate the HTML of the receipt print
		 */
		public function buildDepositReceipt()
		{
			$html  = '<div id="outerHTML">';
			$html .= '<div id="printArea">';
    		$html .= '<h2 align="center">Salesforce</h2>';
			$html .= '<h3 align="center">Official Receipt: Deposit Payment</h3>';
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
			$html .= $this->_tenant_name.'</p>';
			$html .= '<p><strong>Room No:</strong> &nbsp;';
			$html .= Room::findById($this->_rid)->getRoomLabel();
			$html .= '<p><strong>Payment Amount:</strong> &nbsp;';
			$html .= number_format($this->_amount);
			$html .= '</p><p><strong>Company Agent:</strong> &nbsp;';
			$html .= $this->_agent.'</p>';
			$html .= '<p><strong>Date:</strong> &nbsp;';
			$html .= $this->_date_paid." &nbsp;&nbsp;".strftime("%H:%I:%S");
			$html .= '</p></div>';
			$html .= '</div></div>';
			print $html;
		}
		
	}

?>