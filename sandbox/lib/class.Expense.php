<?php

	/**
	 * Class to keep track of expenses incurred 
	 * while managing properties such as renovations, paintings, e.t.c
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date October 01, 2015
	 */
	class Expense extends DatabaseObject
	{
		protected static $table_name = "expense";
		protected static $db_fields = array('id', 'pid', 'name', 'start_date', 'end_date', 'incurred', 'amount');
		public $id;
		protected $_pid;
		protected $_name;
		protected $_start_date;
		protected $_end_date;
		protected $_incurred;
		protected $_amount;
		
		/**
		 * Get the ID that identifies the property
		 * for which an expense was incurred
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
		 * for which the expense was incurred
		 * @param int $prop_id
		 */
		public function setPropertyId($prop_id)
		{
			$prop_id = (int)$prop_id;
			if(is_int($prop_id)){
				$this->_pid = $prop_id;	
			}
		}
		
		/**
		 * Check name of the expense for validity
		 * @param string $name
		 * @return boolean
		 */
		protected function _isValidName($name)
		{
			$valid_string = "/^[a-zA-Z\'\.\s\-]+$/";
			return preg_match($valid_string, $name) ? true : false;
		}
		
		/**
		 * Get the name of the expense which as incurred
		 * @return string 
		 */
		public function getName()
		{
			if(isset($this->_name)){
				return $this->_name;	
			}
		}
		
		/**
		 * Set the name of the expense which was incurred
		 * @param string $name 
		 */
		public function setName($name)
		{
			if($this->_isValidName($name)){
				$this->_name = $name;
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
		 * Get start period during which the expense was incurred
		 * @return string
		 */
		public function getStartPeriod()
		{
			if(isset($this->_start_date)){
				return $this->_start_date;	
			}
		}
		
		/**
		 * Set the end period during which the expense was incurred
		 * @param string $start_date
		 */
		public function setStartPeriod($start_date)
		{
			if($this->_isValidDate($start_date)){
				$this->_start_date = $start_date;
			}
		}
		
		/**
		 * Get the end period during which the expense was incurred
		 * @return string
		 */
		public function getEndPeriod()
		{
			if(isset($this->_end_date)){
				return $this->_end_date;
			}
		}
		
		/**
		 * Set end period during which end period was incurred
		 * @param string $end_date
		 */
		public function setEndPeriod($end_date)
		{
			if($this->_isValidDate($end_date)){
				$this->_end_date = $end_date;	
			}
		}
		
		/**
		 * Get date the expense was incurred
		 * @return string
		 */
		public function getDatePaid()
		{
			if(isset($this->_incurred)){
				return $this->_incurred;	
			}
		}
		
		/**
		 * Set the date the expense was incurred
		 * @todo Use current timestamp to create a datetime string
		 */
		public function setDatePaid()
		{
			$this->_incurred = strftime("%Y-%m-%d", time());
		}
		
		/**
		 * Get the amount of money paid for the expense
		 * @return int
		 */
		public function getPaymentAmount()
		{
			if(isset($this->_amount)){
				return number_format($this->_amount);	
			}
		}
		
		/**
		 * Set the amount of money paid for the expense
		 * @param int $amount
		 */
		public function setPaymentAmount($amount)
		{
			$amount = str_replace(",", "", $amount);
			$this->_amount = $amount;
		}
		
		/**
		 * Get expenses incurred for a particular property during 
		 * a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return array Array of expense objects
		 */
		public static function findByPeriodForProperty($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "(SELECT incurred BETWEEN ";
			$sql .= "'".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1) ";
			$sql .= "AND pid = ".$mysqli->real_escape_string($prop_id);
			return static::findBySql($sql);
		}
		
		/**
		 * Calculate the total amount of expenses incurred for a property
		 * during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start A date specifying the start period of payment
		 * @param string $end The end period of rent collection
		 * @return int $total_amount The total amount of expense incurred during period
		 */
		public static function calcTotalExpenses($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqil = $db->getConnection();
			$expenses = static::findByPeriodForProperty($prop_id, $start, $end);
			$num_expenses = count($expenses);
			$total_expense = 0;
			$count = 0;
			while($count < $num_expenses){
				$exp = $expenses[$count];
				$amount = $exp->getPaymentAmount();
				$amount = str_replace(",", "", $amount);
				$total_expense += (int)$amount;
				$count++;	
			}
			$total_expense = number_format($total_expense);
			return $total_expense;
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
		
	}

?>