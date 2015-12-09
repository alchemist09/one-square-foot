<?php

	/**
	 * Class that holds information about rooms 
	 * in a building and performs opeations on room details
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date April 9, 2015
	 */
	
	class Room extends DatabaseObject
	{
		protected static $table_name = "room";
		protected static $db_fields = array('id', 'prop_id', 'tenant_id', 'label', 'occupied', 'rent_pm');
		public $id;
		protected $_prop_id;
		protected $_tenant_id;
		protected $_label;
		protected $_occupied = 0;
		protected $_rent_pm;
		
		/**
		 * Get the label used to identify the room
		 * @return mixed
		 */
		public function getRoomLabel()
		{
			if(isset($this->_label)){
				return $this->_label;	
			}
		}
		
		/**
		 * Set label used to identify a room
		 * @param mixed $label
		 */
		public function setRoomLabel($label)
		{
			if($this->_isValidRoomLabel($label)){
				$this->_label = $label;	
			}
		}
		
		/**
		 * Check room label for validity
		 * @param mixed $label
		 */
		protected function _isValidRoomLabel($label)
		{
			$valid_label = '/^[a-zA-Z0-9_\-\']$/';
			return preg_match($valid_label, $label) ? true : false;
		}
		
		/**
		 * Get the rent per month of a rooom
		 * @return int
		 */
		public function getRent()
		{
			if(isset($this->_rent_pm)){
				return number_format($this->_rent_pm);	
			}
		}
		
		/**
		 * Set the monthly rent of a room
		 * @param int $rent_pm
		 */
		public function setRent($rent_pm)
		{
			if($this->_isValidRent($rent_pm)){
				$this->_rent_pm = $rent_pm;	
			}
		}
		
		/**
		 * Verify that the rent is a valid integer
		 * @return boolean
		 */
		protected function _isValidRent($rent)
		{
			$valid_rent = '/^[0-9]+$/';
			return preg_match($valid_rent, $rent) ? true : false;
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
		 * Set the property ID field of this room
		 * @param object $propObj
		 */
		public function setPropId($probObj)
		{
			$this->_prop_id = $probObj->id;
		}
		
		/**
		 * Get the ID used to identify the tenant occupying this room
		 * @return int $tenant_id
		 */
		public function getTenantId()
		{
			if(isset($this->_tenant_id) && !is_null($this->_tenant_id) && $this->_tenant_id != 0){
				return $this->_tenant_id;
			}
		}
		
		/**
		 * Set the tenant ID field of this room
		 * @param object $tenantObj
		 */
		public function setTenantId($tenantObj)
		{
			$this->_tenant_id = $tenantObj->id;
		}
		
		/**
		 * Set the tenant ID field of this room to NULL upon
		 * deleting the tenant who had occupied the room
		 */
		public function unsetTenantId()
		{
			if(isset($this->_tenant_id)){
				unset($this->_tenant_id);
			}
		}
		
		/**
		 * Tell whether the room is vacant or occupied
		 * @return boolean
		 */
		public function isVacant()
		{
			return $this->_occupied == 0 ? true : false;
		}
		
		/**
		 * Get a room's occupancy status
		 * @return int An integer code indicating a room's occupancy status
		 */
		public function getRoomStatus()
		{
			return $this->_occupied;
		}
		
		/**
		 * Set a room's occupancy status
		 * @param int $status_code
		 */
		public function setRoomStatus($status_code)
		{
			$status_code = (int)$status_code;
			if($status_code == 0 || $status_code == 1){
				$this->_occupied = $status_code;	
			}
		}
		
		/**
		 * Find rooms that belong to a particular property
		 * @param int $prop_id The property id of the rooms
		 * @return array An array of room objects
		 */
		public static function findByPropertyId($prop_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE prop_id = ".$mysqli->real_escape_string($prop_id);
			return static::findBySql($sql);			
		}
		
		/**
		 * Fetch all rooms for a given property that are occupied 
		 * by a tenant
		 * @param int $prop_id The ID used to identify the property
		 * @return array An array of room objects
		 */
		public static function findOccupiedRoomsForProperty($prop_id)
		{
			$sql  = "SELECT * FROM ".static::$table_name." WHERE prop_id = ";
			$sql .= $prop_id." AND occupied = 1";
			return static::findBySql($sql);
		}
		
		/**
		 * Given a property ID, return rooms from the corresponding
		 * property that are vacant
		 * @param int $prop_id The property ID of the rooms
		 * @return array An array of room objects
		 */
		public static function findVacantRoomsByPropertyId($prop_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE prop_id = ".$mysqli->real_escape_string($prop_id);
			$sql .= " AND occupied = 0";
			return static::findBySql($sql);
		}
		
		/**
		 * Lookup a room by the specified ID of the tenant assigned to it
		 * @param int $tenant_id
		 * @return object A room object
		 */
		public static function findByTenantId($tenant_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE tenant_id = ".$mysqli->real_escape_string($tenant_id);
			$sql .= " LIMIT 1";
			$found = static::findBySql($sql);
			return !empty($found) ? array_shift($found) : NULL;
		}
		
		/**
		 * Get the no. of rooms that belong to a particular property
		 * @param int $prop_id ID used to identify the property
		 * @return int $room_count
		 */
		public static function getNumRoomsForProperty($prop_id)
		{
			$sql = "SELECT COUNT(*) FROM ".static::$table_name." WHERE prop_id = ".$prop_id;
			return static::countBySql($sql);
		}
		
	}

?>