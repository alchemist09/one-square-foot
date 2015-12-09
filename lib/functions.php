<?php

	function __autoload($class_name)
	{
		$path = LIB_PATH.DS."class.{$class_name}.php";
		if(file_exists($path)){
			require_once($path);	
		} else {
			die("The file class.{$class_name}.php does not exist");
		}	
	}
	
	function redirect_to($location=NULL)
	{
		if($location != NULL){
			header("Location: {$location}");
			exit;
		}	
	}
	
	function output_message($mesg="")
	{
		if(!empty($mesg)){
			return $mesg = "<p class=\"message\">{$mesg}</p>";
		} else {
			return "";	
		}
	}
	
	function display_error($err_mesg = "")
	{
		if(!empty($err_mesg)) {
			return "<p class=\"err_mesg\">{$err_mesg}</p>";	
		} else {
			return "";	
		}	
	}
	
	function include_layout_template($template="")
	{
		include(SITE_ROOT.DS.'layouts'.DS.$template);
	}
	
	function sanitize_price($price) {
		$price = str_replace(",", "", $price);
		return $price;	
	}
	
	function create_action_links($actions=array())
	{
		if(is_array($actions)){
			$markup = '<ul id="side-nav">';
			foreach($actions as $link_url => $link_name){
				$link_url = $link_url.".php";
				$markup  .= '<li><a href="'.$link_url.'">';
				$markup	.= ''.$link_name.'</a></li>';
			}
			$markup .= '</ul>';
			return $markup;	
		}	
	}
	
	/**
	 * Automatically generate consecutive numbers used for
	 * invoicing and receipt generation
	 * @param int $start The start number
	 * @param int $count How many invoice/receipt numbers you want to generate
	 * @param int $digits How many digits the generated numbers should be
	 */ 
	function generate_numbers($start, $count, $digits)
	{
		$result = array();
		for($n = $start; $n < $start + $count; $n++){
			$result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
		}
		return $result;
	}
	
	/**
	 * Check to see if a valid receipt type is requested
	 * @param string $type
	 * @return boolean
	 */
	function valid_receipt($type)
	{
		$valid_types = array('deposit', 'rent', 'arrears', 'deposit_kplc', 'deposit_eldowas');
		return in_array($type, $valid_types) ? true : false;
	}
	
	/**
	 * Verify is a valid deposit type was requested
	 * @param string $type
	 * @return boolean
	 */
	function valid_deposit($type)
	{
		$valid_types = array('house', 'kplc', 'eldowas');
		return in_array($type, $valid_types) ? true : false;
	}
	
	/**
	 * Confirm that the provided date range is one month
	 * @param string $start Date string specifying start of month
	 * @param string $end Date string specifying end of month
	 * @return boolean
	 */
	function valid_date_range($start, $end)
	{
		$date_info_one = date_parse($start);
		$date_info_two = date_parse($end);
		$month_one = $date_info_one['month'];
		$month_two = $date_info_two['month'];
		$datetime_start = new DateTime($start);
		$datetime_end   = new DateTime($end);
		$interval = $datetime_end->diff($datetime_start);
		//$num_months = $interval->format("%m") + 1;
		$num_days = $interval->days + 1;
		$valid_diff = array(28, 29, 30, 31);
		return (in_array($num_days, $valid_diff) && ($month_one == $month_two)) ? true : false;
	}
	
	/**
	 * Formate a date to show month and year
	 * @param string $date
	 */
	function get_month_from_date($date)
	{
		$timestamp = strtotime($date);
		$date_format = strftime("%B, %Y", $timestamp);
		return $date_format;
	}

?>