<?php

	/**
	 * Application User Class to handle user 
	 * management in the application
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date March 30, 2015
	 */
	
	class User extends DatabaseObject
	{
		protected static $table_name = "users";
		protected static $db_fields = array('id', 'fname', 'lname', 'email', 'username', 'password', 'phone_no');
		public    $id;
		protected $_fname;
		protected $_lname;
		protected $_email;
		protected $_username;
		protected $_password;
		protected $_phone_no;
		
		/**
		 * Magic Construct
		 * @todo Initialize user object
		 */
		function __construct()
		{
					
		}
		
		/**
		 * Magic __toString
		 * Render object as a string
		 */
		function __toString()
		{
			$html  = "<p><strong>Name:</strong> ".$this->getFullName()."<br />";
			$html .= "<strong>Username:</strong> ".$this->_username."<br />";
			$html .= "<strong>Password:</strong> ".$this->_password."<br />";
			$html .= "<strong>Email:</strong> ".$this->_email."<p>";
			return $html;
		}
		
		/**
		 * Magic __get
		 * @param mixed $name Protected property name
		 */
		function __get($name)
		{
			$protected_property_name = '_'.$name;
			if(property_exists($this, $protected_property_name)){
				return $this->$protected_property_name;	
			}
			
			// Unable to access property, trigger error
			trigger_error("Undefined property via __get: ".$name, E_USER_NOTICE);
			return NULL;
		}
		
		/**
		 * Magic __set
		 * @param string $name
		 * @param mixed $value
		 */
		function __set($name, $value)
		{
			$protected_property_name = '_'.$name;
			if(property_exists($this, $protected_property_name)){
				$this->$protected_property_name = $value;	
			}
			
			// Unable to access property, trigger error
			trigger_error('Undefined property via __set: '.$name, E_USER_NOTICE);
			return NULL;
		}
		
		/**
		 * Check whether a user record with the given username
		 * and password combination exists in the database
		 * @param mixed $username
		 * @param mixed $password
		 * @return object User object on success, false on failure
		 */
		public static function authenticate($username="", $password="")
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			
			// Escape values to prevent SQL injection
			$username = $mysqli->real_escape_string($username);
			$password = $mysqli->real_escape_string($password);
			
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE username = '".$username."' AND password = '".$password."'";
			$sql .= "LIMIT 1";
			
			$result_array = static::findBySql($sql);
			return !empty($result_array) ? array_shift($result_array) : false;
		}
		
		/**
		 * Get the user's first name
		 * @return string
		 */
		public function getFirstName()
		{
			if(isset($this->_fname)){
				return $this->_fname;	
			}
		}
		
		/**
		 * Set the user's first name
		 * @param $first_name
		 * @return boolean
		 */
		public function setFirstName($first_name)
		{
			if($this->_isValidName($first_name)){
				$this->_fname = $first_name;	
			}
		}
		
		/**
		 * Get the user's last name
		 * @return string
		 */
		public function getLastName()
		{
			if(isset($this->_lname)){
				return $this->_lname;	
			}
		}
		
		/**
		 * Set the user's last name
		 * @param $last_name
		 * @return boolean
		 */
		public function setLastName($last_name)
		{
			if($this->_isValidName($last_name)){
				$this->_lname = $last_name;
			}
		}
		
		/**
		 * Check first name or last name for validity
		 * @param string $name
		 * @return boolean
		 */
		protected function _isValidName($name)
		{
			$valid_string = "/^[a-zA-Z\']+$/";
			return preg_match($valid_string, $name) ? true : false;
		}
		
		/**
		 * Return the user's full name
		 * @return string The user full name
		 */
		public function getFullName()
		{
			if(isset($this->_fname) && isset($this->_lname)){
				return $this->_fname." ".$this->_lname;
			}
		}
		
		/**
		 * Get the user's username
		 * @erutn string
		 */
		public function getUserName()
		{
			if(isset($this->_username)){
				return $this->_username;	
			}
		}
		
		/**
		 * Set user's username
		 * @param mixed $username
		 */
		public function setUserName($username)
		{
	    	if($this->_isValidUserName($username)){
				$this->_username = $username;	
			}
		}
		
		/**
		 * Check username to be of valid format
		 * @param mixed $username
		 * @return boolean
		 */
		protected function _isValidUserName($username)
		{
			$valid_username = "/^[a-zA-Z0-9_\-\']+$/";
			return preg_match($valid_username, $username) ? true : false;
		}
		
		/**
		 * Get user's email
		 * @return string
		 */
		public function getEmail()
		{
			if(isset($this->_email)){
				return $this->_email;	
			}
		}
		
		/**
		 * Set user's email
		 * @param mixed $email The user's email
		 * @return string
		 */
		public function setEmail($email)
		{
			if($this->_isValidEmail($email)){
				$this->_email = $email;	
			}
		}
		
		/**
		 * Validate email address
		 * @param mixed $email Email address to validate
		 * @return boolean
		 */
		protected function _isValidEmail($email)
		{
			$valid_email = "/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$/";
			return preg_match($valid_email, $email) ? true : false;
		}
		
		/**
		 * Get user's phone number
		 * @return int
		 */
		public function getPhoneNumber()
		{
			if(isset($this->_phone_no)){
				return $this->_phone_no;	
			}
		}
		
		/**
		 * Set phone number of user
		 * @param int $phone_no
		 */ 
		public function setPhoneNumber($phone_no)
		{
			$phone_no = str_replace(' ', '', $phone_no);
			if($this->_isValidPhoneNumber($phone_no)){
				$this->_phone_no = $phone_no;	
			}	
		}
		
		/**
		 * Check if a phone number is valid
		 * @param int $phone_no
		 * @return boolean
		 */
		protected function _isValidPhoneNumber($phone_no)
		{
			$valid_phone_no = "#^[0]{1}[0-9]{9}$|^\+?[254]{3}[0-9]{9}$#";
			return preg_match($valid_phone_no, $phone_no) ? true : false;
		}
		
		/**
		 * Change username
		 * @param mixed $oldUsername Initial username
		 * @param mixed $newUsername The new username
		 * @return boolean
		 */
		public function changeUsername($oldUsername, $newUsername)
		{
			if(!$this->_isValidUserName($username)){
				throw new Exception("Username can only contain alphanumeric characters");	
			}
			if(strcmp($oldUsername, $newUsername) == 0){
				throw new Exception("The new username cannot be the same as old username");	
			}
		}
		
		/**
		 * Get the password
		 * @return mixed
		 */
		public function getPassword()
		{
			if(isset($this->_password)){
				return $this->_password;	
			}
		}
		
		/**
		 * Set the user's password
		 * @param mixed $password
		 */
		public function setPassword($password)
		{
			if($this->_isValidPassword($password)){
				$this->_password = $password;	
			}
		}
		
		/**
		 * Check password for validity
		 * @param string $password
		 * @retun boolean
		 */
		protected function _isValidPassword($password)
		{
			$valid_password = "/^[a-zA-Z0-9_\-\']+$/";
			if(preg_match($valid_password, $password)){
				return true;
			} else {
				return false;	
			}
		}
		
		/**
		 * Check if the password meets the minimum length set
		 * @param mixed $password
		 * @return boolean
		 */
		protected function _isValidMinLength($password)
		{
			$min_pass_length = 8;
			if(strlen($password) >= $min_pass_length){
				return true;	
			} else {
				return false;	
			}
		}
		
		/**
		 * Change the password of a logged in user
		 * @param mixed $oldPass The current password
		 * @param mixed $newPass The new password
		 * @param mixed $confirmPass The confirm for the new password
		 * @return boolean
		 */
		public function changePassword($oldPass, $newPass, $confirmPass)
		{
			if($this->_password != $oldPass){
				throw new Exception("The current password you entered doesn't match the one for this account");	
			}
			if(!$this->_isValidPassword($newPass)){
				throw new Exception("Password can only contain alphanumeric characters");
			}
			if(!$this->_isValidMinLength($newPass)){
				throw new Exception("Minimum length of password is 8 characters");	
			}
			if(strcmp($this->_password, $newPass) == 0){
				throw new Exception("New password cannot be the same as old password");	
			}
			if($newPass != $confirmPass){
				throw new Exception("The passwords you entered do not match");	
			}
			$this->_password = $newPass;
			// Save password change to database
			return $this->save();
		}
		
		/**
		 * Create an account for the app user
		 * @param array $roles The different groups a user belongs to
		 * @return boolean
		 */
		public function createAccount($roles)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$mysqli->autocommit(false);
			if(!$this->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			$values = array();
			$user_id = $this->id;
			for($i=0; $i < count($roles); $i++){
				$values[] = "(".$user_id.", ".$roles[$i].")";	
			}
			$query_values = implode(', ', $values);
			$sql = "INSERT INTO user2collection (user_id, collection_id) VALUES {$query_values}";
			$mysqli->query($sql);
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			$mysqli->autocommit(true);
			return true;
		}
		
		/**
		 * Change the different groups that this user belongs to
		 * @param array $roles An array of membership groups 
		 * @return boolean
		 */
		public function changeRoles($roles)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$values = array();
			$user_id = $this->id;
			for($i=0; $i < count($roles); $i++){
				$values[] = "(".$user_id.", ".$roles[$i].")";	
			}
			$query_values = implode(", ", $values);
			$mysqli->autocommit(false);
			$sql = "DELETE FROM user2collection WHERE user_id = ".$mysqli->real_escape_string($user_id);
			$mysqli->query($sql);
			$sql = "INSERT INTO user2collection (user_id, collection_id) VALUES {$query_values}";
			$mysqli->query($sql);
			if($mysqli->commit()){
				$mysqli->autocommit(true);
				return true;	
			} else {
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
		}
		
		/**
		 * Check if a user with the specified username exists
		 * in the system to prevent creating users with conflicting usernames
		 * @param mixed $username
		 * @retrun boolean
		 */
		public function userExists($username)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT username FROM ".static::$table_name." ";
			$sql .= "WHERE username = ".$mysqli->real_escape_string($username);
			$result = $mysqli->query($sql);
			if($result->num_rows == 1){
				return true;
			} else {
				return false;	
			}
		}
	}

?>