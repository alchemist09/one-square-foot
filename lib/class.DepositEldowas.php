<?php

	/**
	 * Class to handle payments of deposits for water meters to the
	 * Eldoret Water and Sanitation Company(ELDOWAS)
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date October 17, 2015
	 */
	
	class DepositEldowas extends Deposit
	{
		protected static $table_name = "deposit_eldowas";
		
		/**
		 * Magic construct
		 * @todo Call the parent constructor
		 */
		function __construct()
		{
			parent::__construct();
		}
		
		/**
		 * Generate the HTML of the receipt print
		 */
		public function buildDepositReceipt()
		{
			$html  = '<div id="outerHTML">';
			$html .= '<div id="printArea">';
    		$html .= '<h2 align="center">Salesforce</h2>';
			$html .= '<h3 align="center">Official Receipt: ELDOWAS Deposit</h3>';
			$html .= '<div id="receipt-header">';
			$html .= '<p align="center">';
			$html .= 'Kenyatta Street, New Muya House<br />';
			$html .= '2<sup>nd</sup> Flr, Room 105.<br />';
			$html .= 'Tel: + 254 721 156 315 &nbsp; / &nbsp; + 254 720 711 115<br />';
			$html .= 'www.salesforce.co.ke &nbsp; Email: info@salesforce.co.ke';
			$html .= '</p></div>';
			$html .= '<hr align="center" />';
			
			$html .= '<div id="receipt-body">';
			$html .= '<p>';
			$html .= '<strong>Receipt No:</strong> &nbsp;<span style="color:#F00;">';
			$html .= $this->_receipt_no;
			$html .= '</span></p>';
			$html .= '<p><strong>Tenant:</strong> &nbsp;';
			$html .= $this->_tenant_name.'</p>';
			$html .= '<p><strong>Room No:</strong> &nbsp;';
			$html .= Room::findById($this->_rid)->getRoomLabel();
			$html .= '<p><strong>Payment Amount:</strong> &nbsp;';
			$html .= number_format($this->_amount);
			$html .= '</p><p><strong>Company Agent:</strong> &nbsp;';
			$html .= $this->_agent.'</p>';
			$html .= '<p><strong>Date:</strong> &nbsp;';
			$html .= $this->_date_paid." &nbsp;&nbsp;".strftime("%H:%I:%S");
			$html .= '</p></div>';
			$html .= '</div></div>';
			print $html;
		}
		
	}

?>