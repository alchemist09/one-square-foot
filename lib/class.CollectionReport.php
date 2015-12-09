<?php

	/**
	 * Class to handle generation and rendering of 
	 * rent collection report on screen
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date October 09, 2015
	 */
	class CollectionReport
	{
		public $room_label;
		public $tenant;
		public $receipt_no;
		public $rent_pm;
		public $rent_paid;
		public $arrears_paid;
		public $arrears;
		public $house_deposit;
		public $kplc_deposit;
		public $eldowas_deposit;
		public $totals;
		public $remarks;
		
		/**
		 * Build records that will be used in rendering the reports
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param sring $end Date string specifyinng end of the period
		 * @return array $record_objects An array of record objects
		 */
		public static function buildRecords($prop_id, $start, $end)
		{
			$room_count = Room::getNumRoomsForProperty($prop_id);
			$rooms = Room::findByPropertyId($prop_id);
			$record_objects = array();
			$aobj = new Arrears();
			for($i=0; $i < $room_count; $i++){
				$record = new self;
				$rm = $rooms[$i];
				$record->room_label = $rm->getRoomLabel();
				if(!is_null($rm->getTenantId())){
					$tenant = Tenant::findByRoomId($rm->id);
					$rent_obj = Rent::findByPeriodForTenant($tenant->id, $start, $end);
					$rent_paid = !is_null($rent_obj) ? $rent_obj->getPaymentAmount() : NULL;
					$receipt = !is_null($rent_obj) ? $rent_obj->getReceiptNo() : NULL;
					$remarks = !is_null($rent_obj) ? $rent_obj->getRemarks() : NULL;
					$arrears_paid = ArrearsPaid::calcAmountPaidByTenantDuringPeriod($tenant->id, $start, $end);
					$arrears = $aobj->calcTotalArrearsFromTenant($tenant->id);
					$deposit_obj = Deposit::findByPeriodForTenant($tenant->id, $start, $end);
					$deposit_paid = !is_null($deposit_obj) ? $deposit_obj->getPaymentAmount() : NULL;
					$kplc_obj = DepositKPLC::findByPeriodForTenant($tenant->id, $start, $end);
					$kplc_paid = !is_null($kplc_obj) ? $kplc_obj->getPaymentAmount() : NULL;
					$eldowas_obj = DepositEldowas::findByPeriodForTenant($tenant->id, $start, $end);
					$eldowas_paid = !is_null($eldowas_obj) ? $eldowas_obj->getPaymentAmount() : NULL;
					$totals = $tenant->calcPaymentsMadeDuringPeriod($start, $end);
					
					$record->tenant = $tenant->getFullName();
					$record->receipt_no = $receipt;
					$record->rent_pm = $rm->getRent();
					$record->rent_paid = $rent_paid;
					$record->arrears_paid = $arrears_paid;
					$record->arrears = $arrears;
					$record->house_deposit = $deposit_paid;
					$record->kplc_deposit = $kplc_paid;
					$record->eldowas_deposit = $eldowas_paid;
					$record->totals = $totals;
					$record->remarks = $remarks;
				} else {
					$record->tenant = "VACANT";
					$record->rent_pm = $rm->getRent();	
				}
				$record_objects[] = $record;
			}
			return $record_objects;
		}
		
		/**
		 * Calculate the total of all payments made for a particular property
		 * during a specified period of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return int $total_payments
		 */
		public static function calcTotalPaymentsForPeriod($prop_id, $start, $end)
		{
			$payments = self::buildRecords($prop_id, $start, $end);
			$num_payments = count($payments);
			$total_payments = 0;
			$counter = 0;
			while($counter < $num_payments){
				$record = $payments[$counter];
				if(!is_null($record->totals)){
					$sub_total = $record->totals;
					$sub_total = (int)str_replace(',', '', $sub_total);
					$total_payments += $sub_total;	
				}	
				$counter++;
			}	
			return number_format($total_payments);
		}
		
		/**
		 * Calculate the amount charged as commission on the collected amount
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of period
		 * @param string $end Date string specifying end of period
		 * @return int $commission
		 */
		public static function calcCommissionOnCollection($prop_id, $start, $end)
		{
			$total_collection = self::calcTotalPaymentsForPeriod($prop_id, $start, $end);
			$total_collection = (int)str_replace(',', '', $total_collection);
			$mgt_fee = (int)Property::findById($prop_id)->getManagementFee();
			$commission = $total_collection * ($mgt_fee / 100);
			$commission = round($commission, 2);
			return number_format($commission);
		}
		
		/**
		 * Calculate the sum total of all deductions made on a property during
		 * a specified period of time
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param srting $end Date string specifying end of period
		 * @return int $total_deductions
		 */
		public static function calcTotalDeductionsForPeriod($prop_id, $start, $end)
		{
			$total_deductions = 0;
			$commission = self::calcCommissionOnCollection($prop_id, $start, $end);
			$commission = (int)str_replace(',', '', $commission);
			$expenses = Expense::calcTotalExpenses($prop_id, $start, $end);
			$expenses = (int)str_replace(',', '', $expenses);
			$house_dep_refunds = Deposit::calcRefundsForPeriodByProperty($prop_id, $start, $end);
			$house_dep_refunds = (int)str_replace(',', '', $house_dep_refunds);
			$kplc_refunds = DepositKPLC::calcRefundsForPeriodByProperty($prop_id, $start, $end);
			$kplc_refunds = (int)str_replace(',', '', $kplc_refunds);
			$eldowas_refunds = DepositEldowas::calcRefundsForPeriodByProperty($prop_id, $start, $end);
			$eldowas_refunds = (int)str_replace(',', '', $eldowas_refunds);
			$deductions = array($commission, $expenses, $house_dep_refunds, $kplc_refunds, $eldowas_refunds);
			$total_deductions = array_sum($deductions);
			return number_format($total_deductions);
		}
		
		/**
		 * Calculate the net amount remaining after subtracting all
		 * deductions from the total amount that was collected for 
		 * a particular property during a specified of time
		 * @param int $prop_id The ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of the period
		 * @return int $net_banking
		 */
		public static function calcNetBanking($prop_id, $start, $end)
		{
			$gross_collection = self::calcTotalPaymentsForPeriod($prop_id, $start, $end);
			$gross_collection = (int)str_replace(',', '', $gross_collection);
			$deductions = self::calcTotalDeductionsForPeriod($prop_id, $start, $end);
			$deductions = (int)str_replace(',', '', $deductions);
			$net_banking = $gross_collection - $deductions;
			return number_format($net_banking);
		}
		
		/**
		 * Perform a query that will produce records to be used in the report
		 * @param int $prop_id ID used to identify the property
		 * @param string $start Date string specifying start of the period
		 * @param string $end Date string specifying end of period
		 * @return array $record_objects An array of record objects
		 */
		public static function getReportRecords($prop_id, $start, $end)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT rm.label AS room_label, CONCAT_WS(' ', t.fname, t.lname) AS tenant, ";
			$sql .= "DATE_FORMAT(rt.start_date, '%M, %Y') AS month, CONVERT(rt.date_paid,DATE) ";
			$sql .= "AS date_paid, FORMAT(rm.rent_pm, 0) AS rent_pm, FORMAT(rt.amount, 0) AS ";
			$sql .= "amount FROM room rm LEFT JOIN tenants t ON rm.id = t.rid LEFT JOIN rent rt ";
			$sql .= "ON t.id = rt.tid AND CONVERT(rt.date_paid, DATE) >= '";
			$sql .= $mysqli->real_escape_string($start)."' AND CONVERT(rt.date_paid, DATE) ";
			$sql .= "<= '".$mysqli->real_escape_string($end)."' WHERE rm.prop_id = ".$prop_id;
			$sql .= " ORDER BY rm.id ASC";
			$result = $mysqli->query($sql);
			if(!$result) { throw new Exception("Query Failure"); }
			$record_objects = array();
			while($row = $result->fetch_assoc()){
				$record_objects[] = self::_instantiate($row);
			}
			return $record_objects; 
		}
		
		/**
		 * Get the non-static properties of this obejct that are accessible
		 * from the calling scope
		 * @return array An associative array of object properties
		 */
		protected function _objectProperties()
		{
			$object_vars = get_object_vars($this);
			return $object_vars;
		}
		
		/**
		 * Check whether a property has been defined in a class
		 * @return boolean
		 */
		protected function _hasProperty($property)
		{
			$object_vars = $this->_objectProperties();
			return array_key_exists($property, $object_vars);
		}
		
		/**
		 * Given a record from a database row, use it to instantiate object properties
		 * @param array $record Associative array that defines a database record
		 * @return object A collection_report object
		 */
		protected static function _instantiate($record)
		{
			if(!is_array($record)){
				$record = array($record);	
			}
			$object = new self;
			foreach($record as $k => $v){
				if($object->_hasProperty($k)){
					$object->$k = $v;	
				}
			}
			return $object;
		}
		
	}

?>