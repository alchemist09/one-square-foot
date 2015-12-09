<?php

	// Set the default timezone to be used by executing scripts
	date_default_timezone_set('Africa/Nairobi');

	// Define core paths
	// Define them as absolutes to ensure that require_once works as expected
	
	// DIRECTORY_SEPARATOR is a PHP pre-defined constant
	// Backslash for Windows(\) , Forward slash for Unix(/)

	defined('DS') ? NULL : define('DS', DIRECTORY_SEPARATOR);
	
	// // Define a constant that represent an absolute URL to the site's root folder
	// Not that the URL is a local server file system path, 
	defined('SITE_ROOT') ? NULL : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].DS.'app_real_estate-v02');
	
	// Define a constant that represents an absolute URL to the site's library files
	defined('LIB_PATH') ? NULL : define('LIB_PATH', SITE_ROOT.DS.'lib');
	
	// Load basic functions next  so that everything after can use them
	require_once(LIB_PATH.DS.'functions.php');
	
	// Load Core Classes
	require_once(LIB_PATH.DS.'class.Session.php');
	require_once(LIB_PATH.DS.'class.Database.php');
	require_once(LIB_PATH.DS.'class.DatabaseObject.php');
	require_once(LIB_PATH.DS.'class.Pagination.php');
	require_once(LIB_PATH.DS.'class.Access.php');
	require_once(LIB_PATH.DS.'class.CollectionReport.php');
	
	// Load database-related classes
	require_once(LIB_PATH.DS.'class.User.php');
	require_once(LIB_PATH.DS.'class.Tenant.php');
	require_once(LIB_PATH.DS.'class.Property.php');
	require_once(LIB_PATH.DS.'class.Room.php');
	require_once(LIB_PATH.DS.'class.Rent.php');
	require_once(LIB_PATH.DS.'class.Arrears.php');
	require_once(LIB_PATH.DS.'class.Deposit.php');
	require_once(LIB_PATH.DS.'class.DepositKPLC.php');
	require_once(LIB_PATH.DS.'class.DepositEldowas.php');
	require_once(LIB_PATH.DS.'class.PaymentStatus.php');
	require_once(LIB_PATH.DS.'class.ArrearsPaid.php');
	require_once(LIB_PATH.DS.'class.Cheque.php');
	require_once(LIB_PATH.DS.'class.Expense.php');
	require_once(LIB_PATH.DS.'class.Logger.php');
	
	// Load Report Generation Classes
	require_once(LIB_PATH.DS.'class.Fpdf.php');
	require_once(LIB_PATH.DS.'class.PDF_MC_TABLE.php');
	require_once(LIB_PATH.DS.'class.PDF_Report.php');

?>