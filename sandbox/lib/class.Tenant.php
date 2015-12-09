<?php

 	/**
	 * Tenant Class
	 * This class handles tenant management operations in the system
	 * like moving in a tenant, taking payments from the tenant
	 * and moving out tenants among other things
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date March 30, 2015
	 */
	
	class Tenant extends DatabaseObject
	{
		protected static $table_name = "tenants";
		protected static $db_fields = array('id', 'pid', 'rid', 'fname', 'lname', 'phone_no', 'id_number', 'email', 'biz_name', 'date_joined', 'date_left', 'active');
		public $id;
		protected $_pid;
		protected $_rid;
		protected $_fname;
		protected $_lname;
		protected $_phone_no;
		protected $_id_number;
		protected $_email;
		protected $_biz_name;
		protected $_date_joined;
		protected $_date_left;
		protected $_active = 1;
		
		protected $_status = 0;
		public $arrears_obj;
		
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
		 * Get the property ID that the tenant is a resident in
		 * @return int
		 */
		public function getPropertyId()
		{
			if(isset($this->_pid)){
				return $this->_pid;	
			}
		}
		
		/**
		 * Set the property ID field of the the property 
		 * that the tenant is a resident in
		 * @param int $prop_id
		 */
		public function setPropertyId($prop_id)
		{
			if(is_int($prop_id)){
				$this->_pid = $prop_id;	
			}
		}
		
		/**
		 * Get the ID of the room the tenant occupies
		 * @return int
		 */
		public function getRoomId()
		{
			if(isset($this->_rid)){
				return $this->_rid;	
			}
		}
		
		/**
		 * Set the room ID field of the room occupied by the tenant
		 * @param int $room_id
		 */
		public function setRoomId($room_id)
		{
			if(is_int($room_id)){
				$this->_rid = $room_id;	
			}
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
			$valid_string = "/^[a-zA-Z\'\.\s\-]+$/";
			return preg_match($valid_string, $name) ? true : false;
		}
		
		/**
		 * Return the tenants's full name
		 * @return string The tenant's full name
		 */
		public function getFullName()
		{
			if(isset($this->_fname) && isset($this->_lname)){
				return $this->_fname." ".$this->_lname;
			}
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
		 * Get tenant's ID number
		 * @return int
		 */
		public function getNationalIdNumber()
		{
			if(isset($this->_id_number)){
				return $this->_id_number;	
			}
		}
		
		/**
		 * Set the tenant's ID number
		 * @param int $id_num 
		 */
		public function setNationalIdNumber($id_num)
		{
			if($this->_isValidIdNumber($id_num)){
				$this->_id_number = $id_num;	
			}
		}
		
		/**
		 * Check ID Number for validity
		 * @return boolean
		 */
		protected function _isValidIdNumber($id_num)
		{
			$valid_id_no = "#^[0-9]{7,9}$#";
			return (preg_match($valid_id_no, $id_num) == 1) ? true : false;
		}
		
		/**
		 * Get the business name of a commercial tenant
		 * @return string
		 */
		public function getBusinessName()
		{
			if(isset($this->_biz_name)){
				return $this->_biz_name;	
			}
		}
		
		/**
		 * Set the business name of a commercial tenant
		 * @param string $bizname The business name of a business
		 */
		public function setBusinessName($biz_name)
		{
			if($this->_isValidName($biz_name)){
				$this->_biz_name = $biz_name;	
			}
		}
		
		/**
		 * Check if a date is a valid Gregorian date
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
		 * Get the date the tenant joined the rooom/house
		 * @return string
		 */
		public function getDateJoined()
		{
			if(isset($this->_date_joined)){
				return $this->_date_joined;	
			}
		}
		
		/**
		 * Set the date the tenant joined the room
		 * @param string $date_joined
		 */
		public function setDateJoined($date_joined)
		{
			if($this->_isValidDate($date_joined)){
				$this->_date_joined = $date_joined;	
			}
		}
		
		/**
		 * Get the date the tenant moved out of the room
		 * @return string
		 */
		public function getDateLeft()
		{
			if(isset($this->_date_left)){
				return $this->_date_left;	
			}
		}
		
		/**
		 * Set the date the tenant moved out of the room
		 * @param string $date_left
		 */
		public function setDateLeft($date_left)
		{
			if($this->_isValidDate($date_left)){
				$this->_date_left = $date_left;	
			}
		}
		
		/**
		 * Get the occupancy status of tenant to determine 
		 * whether they are presently occupying a room or moved out
		 * @return int
		 */
		public function getOccupancyStatus()
		{
			return $this->_active;
		}
		
		/**
		 * Set occupancy status of tenant to a value that indicatees 
		 * whether they are presently active or have moved out
		 * @param int $status occupancy status
		 */
		public function setOccupancyStatus($status)
		{
			if($status == 0 || $status == 1){
				$this->_active = $status;	
			}
		}
		
		/**
		 * Get the rent payment status of a tenant during a specified period
		 * @return int
		 */
		public function getPaymentStatus()
		{
			if(isset($this->_status)){
				return $this->_status;	
			}
		}
		
		/**
		 * Set the rent payment status of a tenant during a specified period
		 * @param int $status An integer value that connotes payment status of the property
		 */
		public function setPaymentStatus($status)
		{
			if($status == 0 || $status == 1){
				$this->_status = $status;	
			}
		}
		
		/**
		 * Get all the tenants from a specified property 
		 * that is identified by the supplied property ID
		 * @param $prop_id ID that identifies the property
		 * @return array An array of tenant objects
		 */
		public static function findByPropertyId($prop_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE pid = ".$prop_id." AND active = 1";
			return static::findBySql($sql);
		}
		
		/**
		 * Get all previous tenants from a specified property
		 * @param int $prop_id ID that identifies the property
		 * @return array An array of tenant objects
		 */
		public static function findPreviousByPropertyId($prop_id)
		{
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE pid = ".$prop_id." AND active = 0";
			return static::findBySql($sql);
		}
		
		/**
		 * Lookup a tenant by the specified ID of the room occupied by the tenant
		 * @param int $room_id The ID used to identify the room
		 * @return object $room A room object
		 */
		public static function findByRoomId($room_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." ";
			$sql .= "WHERE rid = ".$mysqli->real_escape_string($room_id);
			$sql .= " AND active = 1 LIMIT 1";
			$found = static::findBySql($sql);
			return !empty($found) ? array_shift($found) : NULL;
		}
		
		/**
		 * Add a tenant into the system
		 * @todo Save tenant instance into database
		 *  then update the room taken by the tenant by setting 
		 *  it's status as occupied
		 * @param int $room_id
		 * @return boolean
		 */
		public function createNewTenant($room_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$mysqli->autocommit(false);
			if(!$this->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			$room = Room::findById($room_id);
			$room->setTenantId($this);
			$room->setRoomStatus(1);
			if(!$room->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);	
				return false;
			}
			return true;
		}
		
		/**
		 * Delete tenant from system and set the
		 * room he/she had occupied as vacant
		 * @return boolean
		 */
		public function deleteTenant()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$room = Room::findByTenantId($this->id);
			$mysqli->autocommit(false);
			if(!$this->delete()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			$room->setRoomStatus(0);
			$room->unsetTenantId();
			if(!$room->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			return true;
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
		 * Pay the rent of tenant's premises for a specified period
		 * @param object $rentObj
		 * @param string $start The start period of the month
		 * @param string $end The end period of the month
		 * @return boolean
		 */
		public function payRent(Rent $rentObj, $start, $end)
		{			
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$monthly_rent = Room::findByTenantId($this->id)->getRent();
			$monthly_rent = $this->_sanitizeMoneyString($monthly_rent);
			$rent_paid = $rentObj->getPaymentAmount();
			$rent_paid = $this->_sanitizeMoneyString($rent_paid);
			if($monthly_rent == $rent_paid){
				// No outstanding arrears
				$pst = new PaymentStatus();
				$pst->setTenantId($this->id);
				$pst->setStartPeriod($start);
				$pst->setEndPeriod($end);
				$pst->setStatus(1);
				$mysqli->autocommit(false);
				$rentObj->save();
				$pst->save();
				if(!$mysqli->commit()){
					$mysqli->rollback();
					$mysqli->autocommit(true);
					return false;	
				} else {
					$mysqli->autocommit(true);
					return true;	
				}
			} elseif($rent_paid < $monthly_rent){
				// Tenant has arrears
				$bal = $monthly_rent - $rent_paid;
				$arrears = new Arrears();
				$arrears->setTenantId($this->id);
				$arrears->setAmountOwed($bal);
				$arrears->setStartPeriod($start);
				$arrears->setEndPeriod($end);
				
				// Payment Status set to 0 - denoting incompletion
				$pst = new PaymentStatus();
				$pst->setTenantId($this->id);
				$pst->setStartPeriod($start);
				$pst->setEndPeriod($end);
				$pst->setStatus(0);
				
				// Save changes to database
				$mysqli->autocommit(false);
				$rentObj->save();
				$arrears->save();
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
		}
		
		/**
		 * Get the rent payment record from a tenant for a specified period
		 * @param int $tenant_id The ID used to identify the tenant
		 * @param string $start The start period of rent payment
		 * @param string $end The end period of rent payment
		 * @reutn array An array of renr objects
		 */
		public function getPaymentRecord($tenant_id, $start, $end)
		{
			$rentObj = new Rent();
			return $rentObj->getPaymentsFromTenant($this->id, $start, $end);
		}
		
		/**
		 * Generate the rent payment statement from a tenant as a PDF document
		 * @param string $start The start period of the rent payment
		 * @param string $end The end period of rent payment
		 * @return file A PDF file containing the record
		 */
		public function generatePdfRecord($start, $end)
		{
			/*$hdgs = array('Start Period(yy-mm-dd)', 'End Period(yy-mm-dd)', 'Date Paid', 'Receipt No', 'Amount');
			$aligns = array('L', 'L', 'L', 'L', 'R');
			$widths = NULL;
			$ttl = $this->getFullName();
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT start_date, end_date, date_paid, receipt_no, FORMAT(amount, 0) ";
			$sql .= "FROM rent WHERE (SELECT CONVERT(date_paid, DATE) BETWEEN ";
			$sql .= "'".$mysqli->real_escape_string($start);
			$sql .= "' AND '".$mysqli->real_escape_string($end)."' = 1) AND tid = ".$this->id;
			$result = $mysqli->query($sql);
			if($result->num_rows == 0){
				return false;
			} else {
				$report = new Report();
				$report->pdfStmt($ttl, $result, $hdgs, $widths, $aligns, 'P', 'A4', $start, $end);	
			}*/
			$rent = new Rent();
			$rent->getPaymentHistoryInPdf($this->id, $start, $end);
		}
		
		/**
		 * Perform a check to establish whether tenant has paid deposit
		 * @param string $type The kind of deposit you want to check for
		 * @return boolean
		 */
		public function hasPaidDeposit($type="")
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			switch($type){
				case "house":
				$sql = "SELECT * FROM deposits WHERE tid = ".$this->id;	
				break;
				
				case "kplc":
				$sql = "SELECT * FROM deposit_kplc WHERE tid = ".$this->id;
				break;
				
				case "eldowas":
				$sql = "SELECT * FROM deposit_eldowas WHERE tid = ".$this->id;
				break;
				
				default:
				$sql = "SELECT * FROM deposits WHERE tid = ".$this->id;
				break;
			}
			$result = $mysqli->query($sql);
			$row_count = $result->num_rows;
			if($row_count == 1){
				return true;	
			} else {
				return false;
			}
		}
		
		/**
		 * Perform a check to establish whether tenant was already refunded deposit
		 * @param string $type Type of deposit to check for
		 * @return boolean
		 */
		public function hasBeenRefundedDeposit($type="")
		{
			switch($type){
				case "house":
				$deposit = Deposit::findByTenantId($this->id);
				return ($deposit->getStatus() == 1) ? true : false;
				break;
				
				case "kplc":
				$deposit = DepositKPLC::findByTenantId($this->id);
				return ($deposit->getStatus() == 1) ? true : false;
				break;
				
				case "eldowas":
				$deposit = DepositEldowas::findByTenantId($this->id);
				return ($deposit->getStatus() == 1) ? true : false;
				break;
			}
		}
		
		/**
		 * Check to establish whether tenant has paid the rent 
		 * full rent for the specified period
		 * @param int $tenant_id ID used to identify the tenant
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return boolean
		 */
		public function hasPaidFullRent($tenant_id, $start, $end)
		{
			$status = PaymentStatus::paymentStatusOK($tenant_id, $start, $end);
			return $status;
		}
		
		/**
		 * Check whether tenant has paid part of the rent for the specified period
		 * @param int $tenant_id ID used to identify the tenant
		 * @param string $start Date string used to identify start of the period
		 * @param string $end Date string used to identify end of the period
		 * @return boolean
		 */
		public function hasPaidPartialRent($tenant_id, $start, $end)
		{
			return PaymentStatus::isPartlyPaid($tenant_id, $start, $end);
		}
		
		/**
		 * Check to see if tenant has any outstanding rent arrears
		 * @return boolean
		 */
		public function hasArrears()
		{
			$arrears = Arrears::findByTenantId($this->id);
			return !empty($arrears) ? true : false;
		}
		
		/**
		 * Check to determine whether the tenant has vacated the room
		 * @return boolean
		 */
		public function hasMovedOut()
		{
			return ($this->_active == 0) ? true : false;
		}
		
		/**
		 * Get the total amount of outstanding rent payments 
		 * owed by this tenant
		 * @return int
		 */
		public function getTotalArrears()
		{
			if($this->hasArrears()){
				$arrears = new Arrears();
				return $arrears->calcTotalArrearsFromTenant($this->id);	
			}
		}
		
		/**
		 * Record a payment of an outstanding rent balance
		 * @param int $arrears_id The ID used to identify the arrears
		 * @param int $amount The amount payed to settle the arrears
		 * @param string $start Date specifying start of a month
		 * @param string $end Date specifying end of a month
		 * @param string $mode The mode of payment for the transaction
		 * @param object $session A session object declared in global scope
		 * @return boolean
		 */
		public function payArrears($arrears_id, $amount, $start, $end, $mode, $session)
		{
			$arrears = Arrears::findById($arrears_id);
			$bal = $arrears->getAmountOwed();
			$bal = (int)$this->_sanitizeMoneyString($bal);
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			// Initialize object to keep track of transaction details
			$ap = new ArrearsPaid();
			$ap->setTenantId($this->id);
			$ap->setPaymentAmount($amount);
			$ap->setStartPeriod($start);
			$ap->setEndPeriod($end);
			$ap->setDatePaid();
			$ap->generateReceiptNo();
			$ap->setPaymentMode($mode);
			$ap->setReceivingAgent($session);
			if($amount == $bal){
				// All arrears cleared
				$status = PaymentStatus::findByPeriod($this->id, $start, $end);
				$status->setStatus(1);
				$mysqli->autocommit(false);
				$ap->save();
				$arrears->delete();
				$_SESSION['arrears_obj'] = $this->arrears_obj = serialize($arrears);
				$status->save();
				if(!$mysqli->commit()){
					$mysqli->rollback();
					$mysqli->autocommit(true);
					return false;	
				} else {
					$mysqli->autocommit(true);
					return true;	
				}
			} elseif($amount < $bal){
				// Arrears partially cleared
				$new_arrears = (int)$bal - (int)$amount;
				$arrears->setAmountOwed($new_arrears);
				$mysqli->autocommit(false);
				$ap->save();
				$arrears->save();
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
		 * Unserialize the arrears object that was serialized 
		 * during payment of arrears
		 * @return object
		 */
		public function getArrearsObject()
		{
			$this->arrears_obj = $_SESSION['arrears_obj'];
			return unserialize($this->arrears_obj);
		}
		
		/**
		 * Move out a tenant from a room
		 * @return boolean
		 */
		public function moveOut()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			// Initantiate old tenant object and set its
			// properties to correspond to current objects property values
			$this->_date_left = strftime("%Y-%m-%d", time());
			$this->_active = 0;
			
			$mysqli->autocommit(false);
			// Set room's status to vacant
			$room = Room::findByTenantId($this->id);
			$room->setRoomStatus(0);
			$room->unsetTenantId();
			
			if(!$this->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			
			if(!$room->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			
			// Try to commit all the changes to database
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			return true;	
		}
		
		/**
		 * Change the room occupied by the tenant
		 * @todo Check if there are any empty rooms in the
		 *  property, then assign a room to the tenant
		 * @param object $roomObj
		 * @return boolean
		 */
		public function changeRoom($roomObj)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$curr_room = Room::findById($this->_rid);
			$mysqli->autocommit(false);
			// Set room's status to vacant
			$curr_room->setRoomStatus(0);
			$curr_room->unsetTenantId();
			if(!$curr_room->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			// Set room_id property to ID of new room and
			// change stauts of new room
			$this->_rid = $roomObj->id;
			$roomObj->setRoomStatus(1);
			$roomObj->setTenantId($this);
			if(!$this->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			if(!$roomObj->save()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			if(!$mysqli->commit()){
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
			return true;
		}
		
		/**
		 * Show the rent payment status of tenants from a particular property
		 * during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @retun array $tenants An array of tenant objects
		 */
		public static function showPaymentStatusOfTenantsByProperty($prop_id, $start, $end)
		{
			$tenants = static::findByPropertyId($prop_id);
			foreach($tenants as $tnt){
				$status = PaymentStatus::findByPeriod($tnt->id, $start, $end);
				if(!is_null($status) && $status->getTenantId() == $tnt->id){
					$payment_status = $status->getStatus();
					$tnt->setPaymentStatus($payment_status);
				}
			}
			return $tenants;
		}
		
		/**
		 * Search for a tenant(s) with similar name as the provided one
		 * @param string $name Full name of tenant
		 * @return array An array of tenant objects
		 */
		public static function searchTenant($name)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT * FROM ".static::$table_name." WHERE ";
			$sql .= "CONCAT_WS(' ', fname, lname) LIKE '%".$mysqli->real_escape_string($name)."%' AND active = 1";
			return static::findBySql($sql);
		}
		
		/**
		 * Remove commas from a currency string to enable the performing
		 * of arithmetic functions on it
		 * @param string $currency The currency string
		 * @return int $currency Numerical value the currency string represents
		 */
		protected function _formatCurrencyString($currency)
		{
			return (int)str_replace(',', '', $currency);
		}
		
		/**
		 * Calculate the sum of all payments made by a tenant during a specifed
		 * period of time. This includes monthly rent, rent arrears paid, house 
		 * deposit, KPLC deposit and ELDOWAS deposit
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $total_payments
		 */
		public function calcPaymentsMadeDuringPeriod($start, $end)
		{
			$total_amount = 0;
			$rent = Rent::findByPeriodForTenant($this->id, $start, $end);
			$arrers_paid = ArrearsPaid::calcAmountPaidByTenantDuringPeriod($this->id, $start, $end);
			$arrers_paid = $this->_formatCurrencyString($arrers_paid);
			$deposit = Deposit::findByPeriodForTenant($this->id, $start, $end);
			$kplc = DepositKPLC::findByPeriodForTenant($this->id, $start, $end);
			$eldowas = DepositEldowas::findByPeriodForTenant($this->id, $start, $end);
			$rent_amount = !is_null($rent) ? $this->_formatCurrencyString($rent->getPaymentAmount()) : 0;
			$deposit_amount = !is_null($deposit) ? $this->_formatCurrencyString($deposit->getPaymentAmount()) : 0;
			$kplc_amount = !is_null($kplc) ? $this->_formatCurrencyString($kplc->getPaymentAmount()) : 0;
			$eldowas_amount = !is_null($eldowas_amount) ? $this->_formatCurrencyString($eldowas->getPaymentAmount()) : 0;
			$total_amount = $rent_amount + $deposit_amount + $arrers_paid + $kplc_amount + $eldowas_amount;
			return number_format($total_amount);
		}
		
	}

?>