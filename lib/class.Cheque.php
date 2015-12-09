<?php

	/**
	 * Class for handling information about payments
	 * made by cheque. It helps for cross-referencing 
	 * purposes to enable relate a cheque payment as payment
	 * for a certain period
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date September 01, 2015
	 */
	
	class Cheque extends DatabaseObject
	{
		protected static $table_name = "cheque";
		protected static $db_fields = array('id', 'tid', 'cheque_no', 'bank', 'branch', 'drawer', 'start_date', 'end_date', 'date_paid', 'type'); 
		public $id;
		protected $_tid;
		protected $_cheque_no;
		protected $_bank;
		protected $_branch;
		protected $_drawer;
		protected $_start_date;
		protected $_end_date;
		protected $_date_paid;
		protected $_type;
		
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
		 * who made the rent payment
		 * @param int $tenant_id
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
		}
		
		/**
		 * Get the cheque number of the cheque
		 * @return string
		 */
		public function getChequeNo()
		{
			if(isset($this->_cheque_no)){
				return $this->_cheque_no;	
			}
		}
		
		public function setChequeNo($cheque_no)
		{
			if($this->_isValidChequeNo($cheque_no)){
				$this->_cheque_no = $cheque_no;	
			}
		}
		
		/**
		 * Check a cheque number for validity
		 * @param int $cheque_no
		 * @return boolean
		 */
		protected function _isValidChequeNo($cheque_no)
		{
			$vaild_cheque = '/^[0-9]{4,8}$/';
			return preg_match($vaild_cheque, $cheque_no) ? true : false;
		}
		
		/**
		 * Get the bank name of the cheque
		 * @return string
		 */
		public function getBank()
		{
			if(isset($this->_bank)){
				return htmlspecialchars_decode($this->_bank);	
			}
		}
		
		/**
		 * Set the name of the bank for the cheque
		 * @param string $bank_name
		 */
		public function setBank($bank_name)
		{
			if($this->_isValidName($bank_name)){
				$bank_name = htmlspecialchars($bank_name);
				$this->_bank = ucwords(strtolower($bank_name));	
			}
		}
		
		/**
		 * Check for valid string
		 * @param string $name
		 * @return boolean
		 */
		protected function _isValidName($name)
		{
			$valid_string = "/^[a-zA-Z\'\.\s\-\$\&]+$/";
			return preg_match($valid_string, $name) ? true : false;
		}
		
		/**
		 * Get the bank branch for the cheque
		 * @return string
		 */
		public function getBranch()
		{
			if(isset($this->_branch)){
				return htmlspecialchars_decode($this->_branch);	
			}
		}
		
		/**
		 * Setht the branch name for the cheque
		 * @param string $branch_name
		 */
		public function setBranch($branch_name)
		{
			if($this->_isValidName($branch_name)){
				$branch_name = htmlspecialchars($branch_name);
				$this->_branch = ucwords(strtolower($branch_name));	
			}
		}
		
		/**
		 * Get the drawer of the cheque
		 * @return string
		 */
		public function getDrawer()
		{
			if(isset($this->_drawer)){
				return htmlspecialchars_decode($this->_drawer);	
			}
		}
		
		/**
		 * Set the drawer of the cheque 
		 * @param string $drawer
		 */
		public function setDrawer($drawer)
		{
			if($this->_isValidName($drawer)){
				$drawer = htmlspecialchars($drawer);
				$this->_drawer = ucwords(strtolower($drawer));	
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
		 * Get the payment type for which the cheque was used
		 * Payment type can either be rent payment or arrears payment
		 * @return string
		 */
		public function getPaymentType()
		{
			if(isset($this->_type)){
				return $this->_type;	
			}
		}
		
		/**
		 * Set the payment type for which the cheque was used
		 * @param string $type 
		 */
		public function setPaymentType($type)
		{
			if($type == "rent" || $type == "arrears"){
				$this->_type = $type;	
			}
		}
		
		/**
		 * Retrieve cheque payments for a given tenant
		 * @param int $tenant_id The ID used to identify the tenant
		 * @return array Array of cheque objects
		 */
		public static function findByTenantId($tenant_id)
		{
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE tid = ".$tenant_id;
			return static::findBySql($sql);
		}
		
		/**
		 * Retrieve cheque payments made during a given period
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return array An array of cheque objects 
		 */
		public static function findByPeriod($start, $end)
		{
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT CONVERT(date_paid, DATE) BETWEEN '".$start."' ";
			$sql .= "AND '".$end."' = 1)";
			return static::findBySql($sql);
		}
		
		/**
		 * Get cheque payments made by a particular tenant during a specifed period 
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string that specfies start of the period
		 * @param string $end Date string that specifies end of the period
		 * @return object A cheque object
		 */
		public static function findByPeriodForTenant($tenant_id, $start, $end)
		{
			$sql  = "SELECT * FROM ".static::$table_name." WHERE tid = ";
			$sql .= $tenant_id." AND (SELECT CONVERT(date_paid, DATE) BETWEEN '".$start."' ";
			$sql .= "AND '".$end."' = 1) '";
			$result = static::findBySql($sql);
		}
		
		/**
		 * Fetch information about a cheque payment by its number
		 * @param int $cheque_no 
		 * @return object A cheque object
		 */
		public static function findByChequeNo($cheque_no)
		{
			$sql  = "SELECT * FROM ".self::$table_name." WHERE ";
			$sql .= "cheque_no = '".$cheque_no."'";
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			return $found;
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
		 * Get the month and year for which a cheque payment was madet
		 * @return mixed
		 */
		public function getMonth()
		{
			return $this->_getMonthFromDate($this->_start_date);
		}
		
	}

?>