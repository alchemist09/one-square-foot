<?php require_once('../lib/init.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>App Real Estate</title>
</head>

<body>

 <?php
 
 	//$user = new User();
	$tenant = new Tenant();
	$tenant->setFirstName('New');
	$tenant->setLastName("Tenant");
	$tenant->setEmail('ntu@mrm.co.ke');
	$tenant->setPhoneNumber('0719 644 479');
	$tenant->setNationalIdNumber('29074310');
	
	echo "<tt><pre>".var_dump($tenant)."</pre></tt>";
	/**$sanitized_props = $user->_sanitizedProperties();
	echo "<tt><pre>".var_dump($sanitized_props)."</pre></tt>";*/
	 
	if($tenant->save()){
		$msg = "User details created";	
	} else {
		$msg = "Save failed";
	}
	
	echo "User ID: ";
	echo $tenant->id;
	echo "<br />";
	echo "First Name: ";
	echo $tenant->getFirstName();
	echo "<br />";
	echo "Last Name: ";
	echo $tenant->getLastName();
	echo "<br />";
	echo "Email: ";
	echo $tenant->getEmail();
	echo "<br />";
	echo "National ID: ";
	echo $tenant->getNationalIdNumber();
	echo "<br />";
	echo "Date Joined: ";
	echo $tenant->getDateJoined();
	echo "<br />";
	echo "Rent Paid. :";
	echo $tenant->hasPaidRent();
	echo "<br />";
	
	echo "<br /><br />";
	
	echo $msg;
	echo "<br />";
	
	/**$pattern = '/^[a-zA-Z\']+$/';
	$subject = "O'Reilly";
	trim($subject);
	
	if(preg_match($pattern, $subject)){
		echo "TRUE";	
	} else {
		echo "FALSE";	
	}*/
	
	/**$string = '0722 90 22 61';
	echo var_dump($string);
	echo $string.'<br />';
	
	$string2 = str_replace(' ', '', $string);
	echo var_dump($string2);
	echo $string2.'<br />';
	
	function goodFormat($no){
		//$no = str_replace(' ', '', $no);
		$patten = '/^[0]{1}[0-9]{9}$/';
		return preg_match($patten, $no) ? "TRUE" : "FALSE";
	}
	
	$param = '0720 55 47 47';
	echo goodFormat($param);*/
	
	/**$found_user = User::findById(27);
	echo "<tt><pre>".var_dump($found_user)."</pre></tt>";
	echo "From DB First Name: ";
	echo $found_user->getFirstName();
	echo "<br />";
	echo "Found DB UserID: ";
	echo $found_user->id;
	echo "<br />";
	
	$found_user->setPhoneNumber('0771 157 731');
	echo "Phone Number from Script: ";
	echo $found_user->getPhoneNumber();
	echo "<br />";
	echo "<tt><pre>".var_dump($found_user)."</pre></tt>";
	if($found_user->save()){
		$svg_msg = "User details updated";	
	} else {
		$svg_msg = "Update failed";	
	}
	
	echo $svg_msg;*/
	
	/**$found_user = User::findById(26);
	if($found_user->delete()){
		$del_msg = "User deleted";	
	} else {
		$del_msg = "Delete failed";	
	}
	
	echo $del_msg;*/
	
	/**$sql = "SELECT * FROM user WHERE id = 22";
	$db = Database::getInstance();
	$mysqli = $db->getConnection();
	$result = $mysqli->query($sql);
	$record = $result->fetch_object();
	echo "<tt><pre>".var_dump($record)."</pre></tt>";*/
	
 ?>

</body>
</html>