<?php date_default_timezone_set('Africa/Nairobi'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Date-Time Format</title>
</head>

<body>
<?php
	$timestamp = strtotime("18-05-2015");
	echo "Timestamp: ";
	echo $timestamp;
	echo "<br />";
	echo "Formatted Date(yy-mm-dd): ";
	$formated_date = strftime("%Y-%m-%d", $timestamp);
	echo $formated_date;
	echo "<br />";
	echo "Another Formart (mm-dd-yy): ";
	$another_format = strftime("%m-%d-%Y", $timestamp);
	echo $another_format;
	echo "<br />";
	echo "Replace Dashes in Date with Commas: ";
	$replace_dashes = str_replace("-", ", ", $another_format);
	echo $replace_dashes;
	echo "<br />";
	echo "Is VALID Date: ";
	$parts = explode(", ", $replace_dashes);
	echo var_dump(checkdate($parts[0], $parts[1], $parts[2]));
	
	echo "<br /><br />";
	
	$uniqueID = uniqid("report");
	echo "Unique ID: ";
	echo $uniqueID;
	echo "<br />";
	$another_unique_id = uniqid("report");
	echo "Another ID: ";
	echo $another_unique_id;
	echo "<br /><br />";
	
	echo "MD5 HASHING - md5(uniqid(mt_rand))";
	echo "<br /><br />";
	for($i = 0; $i < 10; $i++){
		echo "Report-".$i.": ";
		echo md5(uniqid(mt_rand()));
		echo "<br />";	
	}
	
	echo "<br /><br />";
	
	echo "CRYPT HASHING <br />";
	$password = 'secret';
	echo crypt($password);
	
	echo "<br ><br />";
	
	echo "<strong>TIMEZONE SETTINGS</strong><br />";
	echo strftime("%Y-%m-%d %H:%I:%S", time());
?>
</body>
</html>