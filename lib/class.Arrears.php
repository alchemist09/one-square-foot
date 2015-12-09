<?php

	/**
	 * Class used to track rent arrears
	 * of tenants. It works in conjuction with the 
	 * Rent and Tenant classes to do computations that
	 * determine how much should be paid by a tenant to 
	 * settle his/her acount
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date May 25, 2015
	 */
	
	class Arrears extends DatabaseObject
	{
		protected static $table_name = "arrears";
		protected static $db_fields = array('id', 'tid', 'amount', 'start_date', 'end_date');
		public $id;
		protected $_tid;
		protected $_amount;
		protected $_start_date;
		protected $_end_date;
		//protected $_agent;
		//protected $_date_paid;
		
		/**
		 * Magic __construct
		 * Automatically called upon object instantiation
		 * @todo Initialize the $_agent field of the classed
		 */
		function __construct()
		{
			/*$this->setReceivingAgent();
			$this->_date_paid = strftime("%b %d, %Y %H:%I:%S", time());*/
		}
		
		/**
		 * Get the ID that identifies the tenant 
		 * for which arrears are due
		 * @return int
		 */
		public function getTenantId()
		{
			if(isset($this->_tid)){
				return $this->_tid;	
			}
		}
		
		/**
		 * Set the ID that identifies the tenant for 
		 * which the arrears are due
		 * @param int $tenant_id
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
		}
		
		/**
		 * Get amount of rent that is in arrears
		 * @return int
		 */
		public function getAmountOwed()
		{
			if(isset($this->_amount)){
				return number_format($this->_amount);	
			}
		}
		
		/**
		 * Set the amount of rent due
		 * @param int $amoun_due
		 */
		public function setAmountOwed($amount_due)
		{
			$amount_due = $this->_sanitizeMoneyString($amount_due);
			$amount_due = (int)$amount_due;
			if(is_int($amount_due)){
				$this->_amount = $amount_due;	
			}
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
		 * Remove commas from a cuurency string. Method
		 * called outside class
		 * @param string $currency
		 * @return string
		 */
		public function removeCommasFromCurrency($currency)
		{
			return str_replace(",", "", $currency);
		}
		
		/**
		 * Validate a Gregorian date
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
				return $this->_formatDateForOutput($this->_start_date);	
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
				return $this->_formatDateForOutput($this->_end_date);
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
		public function setReceivingAgent()
		{
			$user = User::findById($_SESSION['user_id']);
			$this->_agent = $user->getFullName();
		}
		
		/**
		 * Format the way a date string is displayed to
		 * the end user to enable easy reading
		 * @param string $date The date string
		 * @return mixed
		 */
		protected function _formatDateForOutput($date)
		{
			$timestamp = strtotime($date);
			$date_format = strftime("%b %d, %Y", $timestamp);
			return $date_format;
		}
		
		/**
		 * Convert date string to format that corresponds to database's
		 * DATE type format
		 * @param string $date The date string to format
		 * @return string
		 */
		public function formatDateForDatabase($date)
		{
			$timestamp = strtotime($date);
			$date_format = strftime("%Y-%m-%d", $timestamp);
			return $date_format;
		}
		
		/**
		 * Get all arrears recorded during a specified period of time
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying the end of the period
		 * @return array An array of arrearsPaid objects
		 */
		public static function findByPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "start_date >= '".$mysqli->real_escape_string($start)."' ";
			$sql .= "AND end_date <= '".$mysqli->real_escape_string($end)."'";
			return static::findBySql($sql);
		}
		
		/**
		 * Find rent arrears for a particular tenant
		 * @param int $tenant_id The ID used to identify the tenant
		 * @return object A rent arrears object
		 */
		public static function findByTenantId($tenant_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE tid = ".$mysqli->real_escape_string($tenant_id);
			return  static::findBySql($sql);
		}
		
		/**
		 * Get rent arrears of a given tenant for particular period
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return object An arrears object
		 */
		public static function findByPeriodForTenant($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "start_date = '".$mysqli->real_escape_string($start);
			$sql .= "' AND end_date = '".$mysqli->real_escape_string($end)."' ";
			$sql .= "AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			return $found;
		}
		
		/**
		 * Calculate the total amount of rent arrears owed 
		 * by a particular tenant
		 * @param int $tenant_id The ID used to identify the tenant
		 * @return int $total_arrears
		 */
		public function calcTotalArrearsFromTenant($tenant_id)
		{
			$arrears = static::findByTenantId($tenant_id);
			$num_arrears = count($arrears);
			$total_arrears = 0;
			$count = 0;
			while($count < $num_arrears){
				$arrearsObj = $arrears[$count];
				$amount     = $arrearsObj->getAmountOwed();
				$amount     = str_replace(",", "", $amount);
				$total_arrears += (int)$amount;
				$count++;	
			}
			$total_arrears = number_format($total_arrears);
			return $total_arrears;
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
			$date_format = strftime("%b, %Y", $timestamp);
			return $date_format;
		}
		
		/**
		 * Get the month and year for which a their is an
		 * outstanding amount
		 * @return mixed
		 */
		public function getMonth()
		{
			return $this->_getMonthFromDate($this->_start_date);
		}
		
		/**
		 * Check if arrears for the specified period already exist
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return boolean
		 */
		public function arrearsExist($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE tid = ";
			$sql .= $tenant_id." AND start_date = '".$mysqli->real_escape_string($start)."' AND ";
			$sql .= "end_date = '".$mysqli->real_escape_string($end)."' LIMIT 1";
			$result = static::findBySql($sql);
			return (count($result) == 1) ? true : false;
		}
		
		/**
		 * Record rent arrears for a particular month in the event
		 * that no amont was paid as rent
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return boolean
		 */
		public function recordArrears($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			// Get monthly rental amount
			$rent_pm = Room::findByTenantId($tenant_id)->getRent();
			$rent_pm = (int)str_replace(',', '', $rent_pm);
			// Create a new arrears object to hold arrears information
			//$arrears = new Arrears();
			$this->setTenantId($tenant_id);
			$this->setAmountOwed($rent_pm);
			$this->setStartPeriod($start);
			$this->setEndPeriod($end);
			// Payment Status set to 0 - denoting incompletion
			$pst = new PaymentStatus();
			$pst->setTenantId($tenant_id);
			$pst->setStartPeriod($start);
			$pst->setEndPeriod($end);
			$pst->setStatus(0);
			
			$mysqli->autocommit(false);
			$this->save();
			$pst->save();
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			} else {
				$mysqli->autocommit(true);
				return true;	
			}
		}
		
		/**
		 * Delete arrears of a particular tenant for a specified period
		 * @todo Delete an outstanding arrears and set the tenants payment
		 *  status as paid
		 * @return boolean
		 */
		public function deleteArrears()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$tenant_id = $this->_tid;
			$start = $this->_start_date;
			$end   = $this->_end_date;
			if(!PaymentStatus::paymentStatusOK($tenant_id, $start, $end)){
				$status = PaymentStatus::findByPeriod($tenant_id, $start, $end);
				$status->setStatus(1);
				//$arrears = static::findByPeriod($tenant_id, $start, $end);
				$mysqli->autocommit(false);
				$status->save();
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
		
		/**
		 * Delete rent arrears from a tenant during a specified period
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return boolean
		 */
		public function deleteTenantArrearsForPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "DELETE FROM ".self::$table_name." WHERE start_date >= '";
			$sql .= $mysqli->real_escape_string($start)."' AND end_date <= '";
			$sql .= $mysqli->real_escpae_string($end)."' AND tid = ".$this->_tid;
			$result = $mysqli->query($sql);
			if($result){
				return true;	
			} else {
				return false;	
			}
		}
		
		/**
		 * Get oustanding arrears for a property during a specified
		 * period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return array An array of Arrears objects
		 */
		public static function getOutstandingArrearsForProperty($prop_id, $start, $end)
		{
			$arrears = static::findByPeriod($start, $end);
			$arrears_for_prop = array();
			for($i=0; $i < count($arrears); $i++){
				$cur_bal = $arrears[$i];
				$tenant = Tenant::findById($cur_bal->getTenantId());
				if($tenant->getPropertyId() == $prop_id){
					$arrears_for_prop[] = $cur_bal;	
				}	
			}
			return $arrears_for_prop;
		}
		
		/**
		 * Calculate the total amount in outstanding arrears owed
		 * by a property during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string Date string specifying the start of the period
		 * @param string Date string specifying the end of the period
		 * @return int $total_owed
		 */
		public static function calcArrearsForPropertyDuringPeriod($prop_id, $start, $end)
		{
			$arrears = static::getOutstandingArrearsForProperty($prop_id, $start, $end);
			$num_arrears = count($arrears);
			$total_owed = 0;
			$count = 0;
			while($count < $num_arrears){
				$bal = $arrears[$count];
				$amount = $bal->getAmountOwed();
				$amount = str_replace(",", "", $amount);
				$total_owed += (int)$amount;
				$count++;	
			}
			$total_owed = number_format($total_owed);
			return $total_owed;
		}
		
	}

?>