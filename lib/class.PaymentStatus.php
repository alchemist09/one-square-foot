<?php

	/**
	 * Class to track rent payment status of tenants
	 * for the different months of the year. This class
	 * helps enforce control on the rent table by preventing
	 * redundancy in posting rent payment for the same month 
	 * more than once.
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date Aug 25, 2015
	 */
	class PaymentStatus extends DatabaseObject
	{
		protected static $table_name = "payment_status";
		protected static $db_fields = array('id', 'tid', 'start_date', 'end_date', 'status');
		public $id;
		protected $_tid;
		protected $_start_date;
		protected $_end_date;
		protected $_status = 0;
		
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
		 */
		public function setTenantId($tenant_id)
		{
			$this->_tid = $tenant_id;
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
		 * Get the rent payment status of a tenant for a particular 
		 * period to establish whether they settled their rent completely
		 * @return int
		 */
		public function getStatus()
		{
			if(isset($this->_status)){
				return $this->_status;	
			}
		}
		
		/**
		 * Set tenant's rent payment status for a particular period
		 * in regard to the amount of rent they paid as compared to expected amount
		 * @param int $status
		 */
		public function setStatus($status = 0)
		{
			if(is_int($status)){
				$this->_status = $status;	
			}
		}
		
		/**
		 * Check if tenant has already paid rent for a specified
		 * period. Method prevents the posting of redundant records
		 * @param int $tid The ID of the tenant
		 * @param string $start The beginning period
		 * @param string $end The end of the specified period
		 * @return boolean
		 */
		public static function paymentStatusOK($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE start_date >= '".$start."' AND end_date <= '".$end;
			$sql .= "' AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			if(is_null($found)){
				return false;
			}
			if(intval($found->_status) == 1){
				return true;
			} else {
				return false;	
			}
		}
		
		/**
		 * Check to establish whether tenant has made a partial payment
		 * for the specified period
		 * @param int $tenant_id ID used to identify the tenant
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return boolean
		 */
		public static function isPartlyPaid($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE start_date >= '".$start."' AND end_date <= '".$end;
			$sql .= "' AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			if(is_null($found)){
				return false;
			}
			if(intval($found->_status) == 0){
				return true;
			} else {
				return false;	
			}
		}
		
		/**
		 * Get as status object that corresponds to the specified period
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start Date string specifying the start of the period
		 * @param string $end Date string specifying the end of the period
		 * @return object A status object 
		 */
		public static function findByPeriod($tenant_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE start_date = '".$start."' AND end_date = '".$end."'";
			$sql .= " AND tid = ".$tenant_id;
			$result = static::findBySql($sql);
			$found = !empty($result) ? array_shift($result) : NULL;
			return $found;
		}
	}

?>