<?php

	/**
	 * Logger class to track user activity in the system.
	 * This class keeps track of historical usage patterns
	 * of the application by the different users who are using it.
	 * In this application, it's mainly used in conjuction with the financial
	 * part of the application to be able to relate financial transactions to
	 * a specific user
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date October 12, 2015
	 */
	
	class Logger
	{
		private static $_instance;
		private static $_table_name = "logger";
		private static $db_fields = array('id', 'action_time', 'action', 'amount', 'user', 'message');
		
		// Database Fields
		public $id;
		private $action_time;
		private $action;
		private $amount;
		private $user;
		private $message;
		
		/**
		 * Magic __construct
		 * Set to private to limit instantiation to only
		 * one instance, thus save system resources while logging
		 */
		private function __construct()
		{
			global $session;
			$this->user = User::findById($session->userID)->getFullName();
		}
		
		/**
		 * Accessor method. Get the time the aciton was performed
		 * @return string 
		 */
		public function getLogTime()
		{
			if(isset($this->action_time)){
				return $this->action_time;	
			}
		}
		
		/**
		 * Accessor method. Get the action that was performed
		 * @return string
		 */
		public function getAction()
		{
			if(isset($this->action)){
				return $this->action;	
			}
		}
		
		/**
		 * Accessor method. Get the amount that was logged for the transaction
		 * @return int
		 */
		public function getAmount()
		{
			if(isset($this->amount)){
				return number_format($this->amount);
			}
		}
		
		/**
		 * Accessor method. Get the user who made the transaction
		 * @return string
		 */
		public function getUser()
		{
			if(isset($this->user)){
				return $this->user;	
			}
		}
		
		/**
		 * Accessor method. Get description of the log
		 * @return string
		 */
		public function getMessage()
		{
			if(isset($this->message)){
				return $this->message;	
			}
		}
		
		/**
		 * Public method to get An instance of the Logger class
		 * @return object
		 */
		public static function getInstance()
		{
			if(is_null(self::$_instance)){
				self::$_instance = new self;	
			}
			return self::$_instance;
		}
		
		/**
		 * Return an array of property keys and their values
		 * that are mapped to corresponding database fields
		 * @return array $db_properties
		 */
		protected function _dbProperties()
		{
			$db_properties = array();
			foreach(static::$db_fields as $field){
				if(property_exists($this, $field)){
					$db_properties[$field] = $this->$field;	
				}
			}
			return $db_properties;
		}
		
		/**
		 * Check whether the specified property has been defined in the class
		 * @param string $property Name of the property
		 * @return boolean
		 */
		protected function _hasProperty($property)
		{
			$object_properties = $this->_dbProperties();
			return array_key_exists($property, $object_properties);
		}
		
		/**
		 * Sanitize values meant for database to prevent SQL injection
		 */ 
		protected function cleanedAttributes()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$cleaned_attributes = array();
			$properties = $this->_dbProperties();
			foreach($properties as $key => $value){
				if($this->_hasProperty($key)){
					$cleaned_attributes[$key] = $mysqli->real_escape_string($value);	
				}
			}
			return $cleaned_attributes;
		}
		
		/**
		 * Instantiate an object from a database record
		 * @param array $record An associative array that maps to a database row
		 * @return object
		 */
		protected static function _instantiate($record)
		{
			if(!is_array($record)){
				$record = array($record);
			}
			$object = new self;
			foreach($record as $key => $value){
				if($object->_hasProperty($key)){
					$object->$key = $value;	
				}	
			}
			return $object;
		}
		
		/**
		 * Use provided SQL to load objects from the database
		 * @param string $sql
		 * @return array An array of objects instantiated from a database record set
		 */
		public static function findBySql($sql)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$result = $mysqli->query($sql);
			$object_array = array();
			while($row = $result->fetch_assoc()){
				$object_array[] = self::_instantiate($row);	
			}
			return $object_array;
		}
		
		/**
		 * Log an action performed by the currently logged in user
		 * to the database
		 * @param string $action Short symbolic name of the action
		 * @param int $amount The fiscal value of the transaction made
		 * @param string $mesg Message describing action performed by the user
		 * @return boolean
		 */
		public function logAction($action, $amount, $mesg)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$this->action_time = strftime("%Y-%m-%d %H:%I:%S", time());
			$this->action = $action;
			$this->amount = $this->_sanitizeCurrencyString($amount);
			$this->message = $mesg;
			$sanitized_attributes = $this->cleanedAttributes();
			$sql  = "INSERT INTO ".self::$_table_name." (";
			$sql .= join(', ', array_keys($sanitized_attributes)).") ";
			$sql .= "VALUES ('".join("', '", array_values($sanitized_attributes))."')";
			if($mysqli->query($sql)){
				$this->id = $mysqli->insert_id;
				return true;	
			} else {
				//print_r($mysqli->error_list);	
				return false;
			}
		}
		
		/**
		 * Remove commas from a currency string that's meant 
		 * to be stored in database
		 * @param string $string
		 * @return string
		 */
		protected function _sanitizeCurrencyString($string)
		{
			return str_replace(',', '', $string);
		}
		
		/**
		 * Get activity of all users in the system during a particular date
		 * @param string $date Date specifying a day
		 * @return array An array of logger objects
		 */
		public static function getLogsForDate($date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." ";
			$sql .= "WHERE CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			return self::findBySql($sql);
		}
		
		/**
		 * Get activity of all users in the system during a specified period
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return array An array of logger objects
		 */
		public static function getLogsForPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE CONVERT(action_time, DATE) ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1";
			return self::findBySql($sql);
		}
		
		/**
		 * Calculate the sum total of expenses logged for 
		 * all users during a specific date
		 * @param string $date Date string specifying a day
		 * @return int $total_expenses
		 */
		public function calcExpensesForDate($date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE action = 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			$expenses = self::findBySql($sql);
			$num_exp = count($expenses);
			$total_expenses = 0;
			$counter = 0;
			while($counter < $num_exp){
				$exp = $expenses[$counter];
				$amount = intval($exp->amount);
				$total_expenses += $amount;
				$counter++;	
			}
			return number_format($total_expenses);
		}
		
		/**
		 * Calculate the sum of all non-expense transactions that were
		 * logged for all users during a specified date
		 * @param string $date Date string specifying a day
		 * @return int $total_transactions
		 */
		public function calcTransactionsForDate($date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE action <> 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			$transactions = self::findBySql($sql);
			$num_trans = count($transactions);
			$sum_total = 0;
			$counter = 0;
			while($counter < $num_trans){
				$cur_trans = $transactions[$counter];
				$amount = intval($cur_trans->amount);
				$sum_total += $amount;
				$counter++;
			}
			return number_format($sum_total);
		}
		
		/**
		 * Calculate the amount remaining after subtracting expenses logged
		 * for all users from the all transactions logged in the same day
		 */
		public function calcNetBalanceForDate($date)
		{
			$transactions = $this->calcTransactionsForDate($date);
			$transactions = (int)$this->_sanitizeCurrencyString($transactions);
			$expenses = $this->calcExpensesForDate($date);
			$expenses = (int)$this->_sanitizeCurrencyString($expenses);
			$net_balance = $transactions - $expenses;
			return number_format($net_balance);
		}
		
		/**
		 * Calculate the total of all expenses logged for all the users
		 * for a specified period of time
		 * @param string $start Date string specifying start of period
		 * @param sting $end Date string specifying end of period
		 * @return int $total_expenses
		 */
		public function calcExpensesForPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE action = 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) BETWEEN '".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1";
			$expenses = self::findBySql($sql);
			$num_exp = count($expenses);
			$total_expenses = 0;
			$counter = 0;
			while($counter < $num_exp){
				$exp = $expenses[$counter];
				$amount = intval($exp->amount);
				$total_expenses += $amount;
				$counter++;	
			}
			return number_format($total_expenses);
		}
		
		/**
		 * Calculate the sum of all non-expense transactions logged for
		 * all users during a specified period
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return int $sum_total_of_transactions
		 */
		public function calcTransactionsForPeriod($start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE action <> 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) BETWEEN '".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1";
			$transactions = self::findBySql($sql);
			$num_trans = count($transactions);
			$sub_total = 0;
			$counter = 0;
			while($counter < $num_trans){
				$cur_trans = $transactions[$counter];
				$amount = intval($cur_trans->amount);
				$sub_total += $amount;
				$counter++;
			}
			return number_format($sub_total);
		}
		
		/**
		 * Calculate the balance remaining after deducting the sum of all
		 * expenses logs recorded during a specified period from the the 
		 * sum of transactions recorded during the same period
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return int $net_balance
		 */
		public function calcNetBalanceForPeriod($start, $end)
		{
			$transactions = $this->calcTransactionsForPeriod($start, $end);
			$transactions = (int)$this->_sanitizeCurrencyString($transactions);
			$expenses = $this->calcExpensesForPeriod($start, $end);
			$expenses = (int)$this->_sanitizeCurrencyString($expenses);
			$net_balance = $transactions - $expenses;
			return number_format($net_balance);
		}
		
		
		
		/**
		 * Get list of user activity of all users in the app for a particular date
		 * @param string $name The full name of the user
		 * @param string $date Date string specifying a particular date
		 * @return array An array of logger objects
		 */
		public static function getUserLogsForDate($name, $date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND ";
			$sql .= "CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			return self::findBySql($sql);
		}
		
		/**
		 * Get list of user activity during a specified perriod of time
		 * @param string $name The full name of the user
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return array An array of logger objects
		 */
		public static function getUserLogsForPeriod($name, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND CONVERT(action_time, DATE) ";
			$sql .= "BETWEEN '".$mysqli->real_escape_string($start)."' AND '";
			$sql .= $mysqli->real_escape_string($end)."' = 1";
			return self::findBySql($sql);
		}
		
		/**
		 * Calculate the total of all expense logs that were entered
		 * for a particular user during a particular date
		 * @param string $name Name of the user
		 * @param string $date Date string specifying a particular day
		 * @return int $total_expense
		 */
		public function calcUserExpensesForDate($name, $date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND action = 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			$expenses = self::findBySql($sql);
			$num_exp = count($expenses);
			$count = 0;
			$total_exp = 0;
			while($count < $num_exp){
				$exp = $expenses[$count];
				$amount = intval($exp->amount);
				$total_exp += $amount;	
				$count++;
			}
			return number_format($total_exp);
		}   
		
		/**
		 * Calculate the sum of non-expense transaction logs that were entered
		 * for a particular during a particular date
		 * @param string $name Name of the user
		 * @param string $date Date string specifying a particular day
		 * @return int $sum_of_transactions
		 */
		public function calcUserTransactionsForDate($name, $date)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND action <> 'EXPENSE' ";
			$sql .= "AND CONVERT(action_time, DATE) = '".$mysqli->real_escape_string($date)."'";
			$transactions = self::findBySql($sql);
			$num_trans = count($transactions);
			$count = 0;
			$sum_total = 0;
			while($count < $num_trans){
				$cur_trans = $transactions[$count];
				$amount = intval($cur_trans->amount);
				$sum_total += $amount;
				$count++;
			}
			return number_format($sum_total);
		}
		
		/**
		 * Calculate the net amount of transactions processed by a particular
		 * user after subtracting expenses during a specified day
		 * @param string $name Name of the user
		 * @param string $date Date specifying a particular date
		 * @return int $net_amount
		 */
		public function calcUserNetBalanceForDate($name, $date)
		{
			$transactions_for_date = $this->calcUserTransactionsForDate($name, $date);
			$transactions_for_date = (int)$this->_sanitizeCurrencyString($transactions_for_date);
			$expenses_for_date = $this->calcUserExpensesForDate($name, $date);
			$expenses_for_date = (int)$this->_sanitizeCurrencyString($expenses_for_date);
			$net_amount = $transactions_for_date - $expenses_for_date;
			return number_format($net_amount);
		}
		
		/**
		 * Calculate the total amount of expense logs entered for a particular
		 * user during a specified time period
		 * @param string $name The name of the user
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return int $total_expenses_for_period
		 */
		public function calcUserExpensesForPeriod($name, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND action = 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) BETWEEN '".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1";
			$expenses = self::findBySql($sql);
			$num_exp = count($expenses);
			$total_expenses = 0;
			$count = 0;
			while($count < $num_exp){
				$exp = $expenses[$count];
				$amount = intval($exp->amount);
				$total_expenses += $amount;
				$count++;	
			}
			return number_format($total_expenses);
		}
		
		/**
		 * Calculate the sum of all non-expense transactions that were logged 
		 * for a particular user during a specified period
		 * @param string $name Name of the user
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $sum_total_of_transactions
		 */
		public function calcUserTransactionsForPeriod($name, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".self::$_table_name." WHERE user = '";
			$sql .= $mysqli->real_escape_string($name)."' AND action <> 'EXPENSE' AND ";
			$sql .= "CONVERT(action_time, DATE) BETWEEN '".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1";
			$transactions = self::findBySql($sql);
			$num_trans = count($transactions);
			$sum_total = 0;
			$count = 0;
			while($count < $num_trans){
				$curr_trans = $transactions[$count];
				$amount = intval($curr_trans->amount);
				$sum_total += $amount;
				$count++;	
			}
			return number_format($sum_total);
		}
		
		/**
		 * Calculate amount remaning after subtracting sum total of expenses logged
		 * during a specifed period from sum total of other transactions logged during
		 * the same period for a particular user 
		 * @param string $name Name of the user
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $sum_total_of_transactions
		 */
		public function calcUserNetBalanceForPeriod($name, $start, $end)
		{
			$transactions = $this->calcUserTransactionsForPeriod($name, $start, $end);
			$transactions = (int)$this->_sanitizeCurrencyString($transactions);
			$expenses = $this->calcUserExpensesForPeriod($name, $start, $end);
			$expenses = (int)$this->_sanitizeCurrencyString($expenses);
			$net_balance = $transactions - $expenses;
			return number_format($net_balance);
		}
		
	}

?>