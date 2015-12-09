<?php

	/**
	 * Database Class that manages database process such
	 * as connecting to the database and performing queries
	 *
	 * @author Code Warrior <codewarrior@gigaflopp.com>
	 * @date May 03, 2014
	 */
	 
	require_once(LIB_PATH.DS.'config.php');
	 
	class MySQLDatabase
	{
		/**
		 * The last SQL query that was run
		 */
		public $lastQuery;
		
		/**
		 * Link to the current active database
		 * @type resource
		 */
		private $_connection;
		
		/**
		 * Boolean that holds whether a PHP built-in function is defined or not
		 */
		private $_realEscapeStringExists;
		
		/**
		 * Status of magic_quotes_gpc directive
		 */
		private $_magicQuotesActive;
		
		/**
		 * Magic __construct
		 * Automatically establish a database connection
		 * upon instantiation of class
		 */
		function __construct()
		{
			$this->openConnection();
			$this->_magicQuotesActive = get_magic_quotes_gpc();
			$this->_realEscapeStringExists = function_exists('mysql_real_escape_string');	
		}
		
		/**
		 * Opens a connection to the database and selects a database
		 */
		public function openConnection()
		{
			$this->_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
			if(!$this->_connection){
				die("Database connection could not be established: ".mysql_error());	
			} else {
				// select a database
				$db_select = mysql_select_db(DB_NAME, $this->_connection);
				if(!$db_select){
					die("Database could not be selected: ".mysql_error());	
				}	
			}
		}
		
		/**
		 * Performs an SQL Query and return the result on success
		 * @param string $sql The SQL statements to execute
		 * @return mixed The result of the query which can either be
		 * a resource or a boolean value
		 */
		public function query($sql)
		{
			$result = mysql_query($sql, $this->_connection);
			$this->lastQuery = $sql;
			$this->_confirmQuery($result);
			return $result;
		}
		
		/**
		 * Escape user submitted values for use in SQL queries
		 * @param string $value The SQL Statements to be executed
		 * @return string $value An escaped value that is safe for use in database 
		 */
		public function escapeValue($value)
		{
			if($this->_realEscapeStringExists){ // PHP Version 4.30 or higher
				// undo any magic quotes effects i.e strip any slashes from 
				// the input string so mysql_real_escape_string can do the work
				if($this->_magicQuotesActive){ $value = stripslashes($value); }
				$value = mysql_real_escape_string($value, $this->_connection);
			} else { // before PHP v4.30
				// If magic quotes are not active, then add slashes manually
				if(!$this->_magicQuotesActive){ $value = addslashes($value); }
				// If magic quotes are active then the slashes already exist
			}
			return $value;
		}
		
		# Database Neutral Methods
		
		/**
		 * Fetch a result row as an array
		 * @param resource $result_set A result from a mysql query
		 * @retun array $result_set An array that corresponds to the fetched row
		 */
		public function fetchArray($result_set)
		{
			return mysql_fetch_array($result_set);
		}
		
		/**
		 * Return the no. of rows from a result set
		 * @param resource $result_set A result from a database query
		 * @return int The no. of rows from the result reource
		 */
		public function numRows($result_set)
		{
			return mysql_num_rows($result_set);	
		}
		
		/**
		 * Get the no. of affected rows from the previous database operation
		 * @return int The no. of affected rows from the previous operation
		 */
		public function affectedRows()
		{
			return mysql_affected_rows($this->_connection);
		}
		
		/**
		 * Get the ID generated for an auto_increment column by 
		 * the previous INSERT opertation
		 * @return int The last generated ID
		 */
		public function insertId()
		{
			return mysql_insert_id($this->_connection);	
		}
		
		/**
		 * Check if a database query is successful or not
		 * @param mixed $result The result from a database query
		 */
		private function _confirmQuery($result)
		{
			if(!$result){
				/*$output = $this->lastQuery;
				die("Database query failed: ".mysql_error()."<br />
				     Last SQL Query: ".$output);*/
				$output  = "Database query failed: ".mysql_error()."<br />";
				$output .= "Last SQL Query: ".$this->lastQuery;
				die($output);	
			}
		}
		
		/**
		 * Closes the connection to the database server 
		 * that is associated with the current link identifier
		 */
		public function closeConnection()
		{
			if(isset($this->_connection)){
				mysql_close($this->_connection);
				unset($this->_connection);
			}
		}
	}
	
	$db = new MySQLDatabase();

?>