<?php

	/**
	 * A Database abstraction class that handles routine
	 * CRUD functions. This class defines common methods 
	 * for objects that use the database in manipulating their properties
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date April 9, 2015
	 */
	
	class DatabaseObject
	{
		/**
		 * The database table related to this class
		 * @var string
		 */
		protected static $table_name;
		
		protected static $db_fields = array();
		
		/**
		 * Fetch a database record given its ID
		 * @param int $id The ID of the database row
		 */
		public static function findById($id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "id = ".$mysqli->real_escape_string($id)." LIMIT 1";
			$found = static::findBySql($sql);
			return !empty($found) ? array_shift($found) : NULL;
		}
		
		/**
		 * Get all records from the database and instantiate an object for each row
		 * @return array Array of instantiated objects
		 */
		public static function findAll()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql = "SELECT * FROM ".static::$table_name;
			return static::findBySql($sql);
		}
		
		/**
		 * Use the provided SQL query to load objects from the database
		 * @param string $sql
		 * @return array An array of objects instantiated from 
		 * a database recordset
		 */
		public static function findBySql($sql)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$result = $mysqli->query($sql);
			$object_array = array();
			while($row = $result->fetch_assoc()){
				$object_array[] = static::_instantiate($row);
			}
			return $object_array;
		}
		
		/**
		 * Get count of all database records
		 * @return int 
		 */
		public static function countAll()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql = "SELECT COUNT(*) FROM ".static::$table_name;
			$result = $mysqli->query($sql);
			$row = $result->fetch_assoc();
			return array_shift($row);
		}
		
		/**
		 * Get count of records returned by an SQL query
		 * @oaram string $sql An SQL query
		 * @return int
		 */
		public static function countBySql($sql)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$result = $mysqli->query($sql);
			$row = $result->fetch_assoc();
			return array_shift($row);
		}
		
		/**
		 * Given a record from the database, use it to initialize object properties
		 * @param array $record A database row that has been parsed into an array
		 * @rerun object
		 */
		protected static function _instantiate($record)
		{
			if(is_array($record)){
				$class_name = get_called_class();
				$object = new $class_name;
				foreach($record as $prop => $value){
					if($prop != "id"){ $prop = '_'.$prop; }
					if($object->_hasProperty($prop)){
						$object->$prop = $value;
					}	
				}
				return $object;
			}
	    }
		
		/**
		 * Check if object has specified property
		 * @return boolean
		 */
		protected function _hasProperty($property)
		{
			// get_obj_vars returns the non-static properties of an object
			// that are accessible from the scope on which it is called
			// Using get_obj_vars under this context may result in unexpected
			// behaviour in the event that a class has a property that is not
			// mapped to a corresponding database table field
			# $object_vars = get_object_vars($this);
			$object_vars = $this->_properties();
			return array_key_exists($property, $object_vars);
		}
		
		/**
		 * Return an array of property keys and their values
		 * that are mapped to corresponding database fields
		 * @return array
		 */
		protected function _properties()
		{
			$properties = array();
			foreach(static::$db_fields as $field){
				if($field != "id") { $field = '_'.$field; }
				if(property_exists($this, $field)){
					$properties[$field] = $this->$field;	
				}	
			}
			return $properties;
		}
		
		/**
		 * Escape properties that are meant to be saved to database
		 * to prevent SQL injection
		 * @return array
		 */
		protected function _sanitizedProperties()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$cleaned_attributes = array();
			$obj_properties = $this->_properties();
			foreach($obj_properties as $key => $value){
				if($key != "id") {
					$pattern = '/^[_]{1}/i';
					$replacement = '';
					$key = preg_replace($pattern, $replacement, $key, 1);
			    }
				$cleaned_attributes[$key] = $mysqli->real_escape_string($value);	
			}
			
			return $cleaned_attributes;
		}
		
		/**
		 * Method to check under which context to either run 
		 * create() or update()
		 */
		public function save()
		{
			// A new record won't have an ID yet
			return isset($this->id) ? $this->_update() : $this->_create();
		}
		
		/**
		 * Add a User to the database
		 * @return boolean
		 */ 
		protected function _create()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sanitized_attributes = $this->_sanitizedProperties();
			$sql  = "INSERT INTO ".static::$table_name." (";
			$sql .= join(", ", array_keys($sanitized_attributes));
			$sql .= ") VALUES ('";
			$sql .= join("', '", array_values($sanitized_attributes));
			$sql .= "')";
			
			/**$sql  = "INSERT INTO user (fname, lname, email, username, password, phone_no) ";
			$sql .= "VALUES ('".$this->_fname."', '".$this->_lname."', '".$this->_email."', '";
			$sql .= $this->_username."', '".$this->_password."', ".$this->_phone_no.")";*/
			
			if($mysqli->query($sql)){
				$this->id = $mysqli->insert_id;
				return true;	
			} else {
				return false;	
			}
		}
		
		/**
		 * Update user record in database
		 * @return boolean
		 */
		protected function _update()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$attributes = $this->_sanitizedProperties();
			$attribute_pairs = array();
			foreach($attributes as $key => $value){				
				$attribute_pairs[] = "$key = '{$value}'";	
			}
			$sql  = "UPDATE ".static::$table_name." SET ";
			$sql .= join(", ", $attribute_pairs);
			$sql .= " WHERE id = ".$mysqli->real_escape_string($this->id);
			
			/**$sql  = "UPDATE user SET ";
			$sql .= "fname = '".$mysqli->real_escape_string($this->_fname)."', ";
			$sql .= "lname = '".$mysqli->real_escape_string($this->_lname)."', ";
			$sql .= "email = '".$mysqli->real_escape_string($this->_email)."', ";
			$sql .= "username = '".$mysqli->real_escape_string($this->_username)."', ";
			$sql .= "password = '".$mysqli->real_escape_string($this->_password)."', ";
			$sql .= "phone_no = ".$mysqli->real_escape_string($this->_phone_no)." ";
			$sql .= "WHERE id = ".$mysqli->real_escape_string($this->id);*/
			$mysqli->query($sql);
			return $mysqli->affected_rows == 1 || $mysqli->affected_rows == 0 ? true : false;
		}
		
		/**
		 * Delete record from database
		 * @return boolean
		 */
		public function delete()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "DELETE FROM ".static::$table_name." ";
			$sql .= "WHERE id = ".$mysqli->real_escape_string($this->id);
			$sql .= " LIMIT 1";
			$mysqli->query($sql);
			return $mysqli->affected_rows == 1 ? true : false;
		}
	}

?>