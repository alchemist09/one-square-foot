<?php

	/**
	 * Property Class
	 * This class handles the logic of creating properties
	 * in the system
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date March 26, 2015
	 */
	
	class Property extends DatabaseObject
	{
		protected static $table_name = "property";
		protected static $db_fields = array('id', 'name', 'num_rooms', 'fee', 'landlord', 'added', 'end');
		public $id;
		protected $_name;
		protected $_num_rooms;
		protected $_fee;
		protected $_landlord;
		protected $_added;
		protected $_end;
		
		/**
		 * Get the name, or label used to identify a property
		 * @return mixed Name of property
		 */
		public function getPropertyName()
		{
			if(isset($this->_name)){
				return $this->_name;	
			}
		}
		
		/**
		 * Set the name, or label used to identify a property
		 * @param mixed $name Name of propperty
		 */
		public function setPropertyName($name)
		{
			if($this->_isValidName($name)){
				$this->_name = $name;	
			}
		}
		
		/**
		 * Check property name for validity
		 * @param mixed $name Name of property
		 * @return boolean
		 */
		protected function _isValidName($name)
		{
			$valid_name = '/^[\w\s\'\.]+$/';
			return preg_match($valid_name, $name) ? true : false;
		}
		
		/**
		 * Get the no. of rooms for a property
		 * @return int No. of rooms
		 */
		public function getNumRooms()
		{
			if(isset($this->_num_rooms)){
				return $this->_num_rooms;	
			}
		}
		
		/**
		 * Set the no. of rooms for a property
		 * @param int $rooms No. of rooms
		 */
		public function setNumRooms($rooms)
		{
			if(is_int((int)$rooms)){
				$this->_num_rooms = $rooms;	
			}
		}
		
		/**
		 * Get the amount/percentage charged as management fee
		 * @return int
		 */
		public function getManagementFee()
		{
			if(isset($this->_fee)){
				return $this->_fee;	
			}
		}
		
		/**
		 * Set the amount/percentage charged as management fee
		 * @param int $fee 
		 */
		public function setManagementFee($fee)
		{
			$this->_fee = $fee;
		}
		
		/**
		 * Get the occupancy level of the property
		 * @return string
		 */
		public function getOccupancyLevel()
		{
			return $this->calcOccupancyLevel();
		}
		
		/**
		 * Check if location is a valid string
		 * @param mixed $location The property location
		 * @return boolean
		 */
		protected function _isValidLocation($location)
		{
			$valid_location = '/^[\w\s\'\.]+$/';
			return preg_match($valid_location, $location) ? true : false;
		}
		
		/**
		 * Get the name of property owner
		 * @return string
		 */
		public function getLandLord()
		{
			if(isset($this->_landlord)){
				return $this->_landlord;
			}
		}
		
		/**
		 * Set the name of the landlord
		 * @param string $land_lord
		 */
		public function setLandLord($land_lord)
		{
			if($this->_isValidName($land_lord)){
				$this->_landlord = $land_lord;	
			}
		}
		
		/**
		 * Check if a date is valid 
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
		 * Get the date that the property was added
		 * @return string
		 */
		public function getDateAdded()
		{
			if(isset($this->_added)){
				return $this->_added;
			}
		}
		
		/**
		 * Set the date that the property was added
		 * @param string $date_added
		 */
		public function setDateAdded($date_added)
		{
			if($this->_isValidDate($date_added)){
				$this->_added = $date_added;	
			}
		}
		
		/**
		 * Get the date that the property was removed
		 * @return string
		 */
		public function getDateRemoved()
		{
			if(isset($this->_end)){
				return $this->_end;	
			}
		}
		
		/**
		 * Set the date property was removed
		 * @param string $date_removed
		 */
		public function setDatedRemoved($date_removed)
		{
			if($this->_isValidDate($date_removed)){
				$this->_end = $date_removed;	
			}
		}
		
		/**
		 * Return a subset of property objects to use 
		 * in pagination of records
		 * @param object $paginationObj A pagination object
		 * @return array An array of property objects
		 */
		public static function getSubsetOfProperties(Pagination $paginationObj)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();			
			$sql  = "SELECT * FROM property p ORDER BY name ASC ";
			$sql .= "LIMIT ".$mysqli->real_escape_string($paginationObj->per_page)." ";
			$sql .= "OFFSET ".$paginationObj->offset();					
			return static::findBySql($sql);
		}
		
		/**
		 * Return the no. of rooms occupied for this property
		 * @todo Use the Room class to check occupied rooms that
		 * have a property ID that corresponds to the ID property
		 * @return int $num_rooms_occupied
		 */
		public function getNumRoomsOccupied()
		{
			$occupied_rooms = Room::findOccupiedRoomsForProperty($this->id);
			return count($occupied_rooms);
		}
		
		/**
		 * Calculate the occupancy level of the property 
		 * against the no. of rooms in the property
		 * @return int
		 */
		public function calcOccupancyLevel()
		{
			$occupied_rooms = Room::findOccupiedRoomsForProperty($this->id);
			$num_rooms_occupied = count($occupied_rooms);
			$occupancy = ($num_rooms_occupied/(int)$this->_num_rooms) * 100;
			return round($occupancy);
		}
		
		/**
		 * Determine whether all the rooms in a property
		 * have been occupied
		 * @todo Check whether the occupancy level of the property is 100%
		 * @return boolean
		 */
		public function isFullyOccupied()
		{
			$occupancy_level = $this->calcOccupancyLevel();
			return ($occupancy_level == 100) ? true : false;
		}
		
		/**
		 * Calculate the expected monthly rent collection based
		 * on the occupancy level of the property
		 * @return int $expected_collection
		 */
		public function calcExpectedMonthlyCollection()
		{
			$rooms = Room::findOccupiedRoomsForProperty($this->id);
			$num_rooms = count($rooms);
			$expected_collection = 0;
			$count = 0;
			while($count < $num_rooms){
				$this_room = $rooms[$count];
				$rent =  $this_room->getRent();
				$rent = str_replace(',', '', $rent);
				$expected_collection += (int)$rent;
				$count++;	
			}
			$expected_collection = number_format($expected_collection);
			return $expected_collection;
		}
		
		/**
		 * Calculate the collection percentage against the expected 
		 * collection for a given property during a specified period
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int
		 */
		public function calcCollectionPercentageForPeriod($start, $end)
		{
			$datetime_start = new DateTime($start);
			$datetime_end   = new DateTime($end);
			$interval = $datetime_end->diff($datetime_start);
			$num_days = $interval->days + 1;
			//$valid_diff = array(28, 29, 30, 31);
			if(valid_date_range($start, $end)){
				$expected_collection = $this->calcExpectedMonthlyCollection();
				$expected_collection = (int)str_replace(',', '', $expected_collection);	
			} else {
				$expected_collection = $this->calcExpectedCollectionForPeriod($start, $end);
				$expected_collection = (int)str_replace(',', '', $expected_collection);	
			}
			$collection_summary = Rent::calcCollectionSummary($this->id, $start, $end);
			$collection_summary = (int)str_replace(',', '', $collection_summary);
			//$expected_collection = $this->calcExpectedMonthlyCollection();
			if($collection_summary == 0){
				return 0.00;
			}
			$percentage_collection = ($collection_summary / $expected_collection) * 100;
			return number_format($percentage_collection, 2);
		}
		
		/**
		 * Calculate the expected rent collection for a property
		 * for a specified interval of time, typically a no. of month
		 * @param string $start Date string that specifies the start of the period
		 * @param string $end Date string that specifies end of the period
		 * @return int 
		 */
		public function calcExpectedCollectionForPeriod($start, $end)
		{
			$expcted_monthly_collection = $this->calcExpectedMonthlyCollection();
			$expcted_monthly_collection = (int)str_replace(',', '', $expcted_monthly_collection);
			$datetime_start = new DateTime($start);
			$datetime_end   = new DateTime($end);
			$interval = $datetime_end->diff($datetime_start);
			//$num_months = $interval->format("%m") + 1;
			$num_days = $interval->days + 1;
			//$valid_diff = array(28, 29, 30, 31);
			if(valid_date_range($start, $end)){
				$num_months = 1;	
			} else {
				$num_months = floor($num_days / 29.5);	
			}
			$expected_collection_for_period = $num_months * $expcted_monthly_collection;
			return number_format($expected_collection_for_period);
		}
		
		/**
		 * Get the final amount deposited into landlords account
		 * after deducting both management fee and expenses for a 
		 * a particular property over a specified period of time
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 */
		public function getFinalBanking($start, $end)
		{
			$rent = new Rent();
			$net_banking = $rent->calcNetBanking($this->id, $start, $end);
			return $net_banking;
		}
		
		/**
		 * Calculate the no. of months in the specified period
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return int $num_months
		 */
		public function getNumMonthsInPeriod($start, $end)
		{
			$datetime_start = new DateTime($start);
			$datetime_end   = new DateTime($end);
			$interval = $datetime_end->diff($datetime_start);
			//$num_months = $interval->format("%m") + 1;
			$num_days = $interval->days + 1;
			//$valid_diff = array(28, 29, 30, 31);
			if(valid_date_range($start, $end)){
				$num_months = 1;	
			} else {
				$num_months = floor($num_days / 29.5);	
			}
			return $num_months;
		}
		
		/**
		 * Find rooms in this property that are vavant
		 * @todo Use the room class to lookup rooms that have not been occupied
		 * @return array An array of room objects
		 */
		public function getVacantRooms()
		{
			$vacant_rooms = Room::findVacantRoomsByPropertyId($this->id);
			return $vacant_rooms;
		}
		
	}

?>