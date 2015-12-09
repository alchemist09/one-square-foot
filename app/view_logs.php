<?php require_once('../lib/init.php'); ?>
<?php
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); }
	$permissions = array('admin');
	try {
		$ac->checkPermissions($permissions);	
	} catch(Exception $e){
		$mesg = $e->getMessage();
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);	
	}
?>
<?php

	$users = User::findAll();
	
	///////////////////////////////////////////////////////////////////////////
	/////////////////////////////// PROCESS LOOKUP ////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	if(isset($_GET['refresh'])){
		$user_id  = $_GET['user'];
		$date     = $_GET['date'];
		$start    = $_GET['start_date'];
		$end      = $_GET['end_date'];
		
		$logger = Logger::getInstance();
		
		// Check for errors
		if(empty($user_id)){
			$err = "Choose a user from which to show activity from";	
		} elseif(!empty($user_id) && empty($date) && empty($start) && empty($end)){
			$err = "Choose date for which to show user activity";	
		} elseif(!empty($user_id) && !empty($date) && (!empty($start) || !empty($end))){
			$err  = "You cannot select both a date and a period from which to show user ";
			$err .= "activity from";
		} elseif(!empty($user_id) && empty($date) && (empty($start) || empty($end))){
			$err  = "When specifying a range, you must specify both the start and end dates ";
			$err .= "of the range";
		}
		
		// Display Logs According to entered parameters
		# Logs For Date
		if(!empty($user_id) && !empty($date)){
			if($user_id == "all"){
				$logs         = Logger::getLogsForDate($date);	
				$transactions = $logger->calcTransactionsForDate($date);
				$expenses     = $logger->calcExpensesForDate($date);
				$net_balance  = $logger->calcNetBalanceForDate($date);
			}
			
			if($user_id != "all"){
				$name         = User::findById($user_id)->getFullName();
				$logs         = Logger::getUserLogsForDate($name, $date);
				$transactions = $logger->calcUserTransactionsForDate($name, $date);
				$expenses     = $logger->calcUserExpensesForDate($name, $date);
				$net_balance  = $logger->calcUserNetBalanceForDate($name, $date);
			}	
		}
		
		# Logs For Period
		if(!empty($user_id) && !empty($start) && !empty($end)){
			if($user_id == "all"){
				$logs         = Logger::getLogsForPeriod($start, $end);
				$transactions = $logger->calcTransactionsForPeriod($start, $end);
				$expenses     = $logger->calcExpensesForPeriod($start, $end);
				$net_balance  = $logger->calcNetBalanceForPeriod($start, $end);	
			}	
			
			if($user_id != "all"){
				$name         = User::findById($user_id)->getFullName();
				$logs         = Logger::getUserLogsForPeriod($name, $start, $end);
				$transactions = $logger->calcUserTransactionsForPeriod($name, $start, $end);
				$expenses     = $logger->calcUserExpensesForPeriod($name, $start, $end);
				$net_balance  = $logger->calcUserNetBalanceForPeriod($name, $start, $end);	
			}
		}
		
	} else {
		// Form not submitted
		$err = "";	
		$logs = "";
		$transactions = "";
		$expenses = "";
		$net_balance = "";
	}

?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_roles" => "Roles of Users", "manage_perms" => "Manage Permissions", "admin" => "Go Back");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <h2>View User Activity</h2>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    <?php
		$mesg = $session->message();
		echo output_message($mesg);
	?>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
    <table cellpadding="5" cols="4">
    <tr>
    	<td><label for="user">Choose User
        <span class="required-field">*</span></label></td>
        <td>
        	<select name="user" id="user">
                <option value="" selected="selected"></option>
                <option value="all">All Users</option>
            <?php foreach($users as $u): ?>
                <option value="<?php echo $u->id; ?>"><?php echo $u->getFullName(); ?></option>
            <?php endforeach; ?>
        	</select>
        </td>
    </tr>
    <tr>
    	<td><label for="date">For Date
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="date" id="date" /></td>
    </tr>
    <tr>
    	<td>For Period</td>
        <td>Specify Date Range from Fields Below</td>
    </tr>
    <tr>
    	<td><label for="start_date">Start Date
        <span class="required-field">*</span></label></td>
    	<td><input type="text" name="start_date" id="start_date" /></td>
        <td><label for="end_date">End Date
        <span class="required-field">*</span></label></td>
    	<td><input type="text" name="end_date" id="end_date" /></td>
    </tr>
    <tr>
    	<td colspan="2" align="right">
        	<input type="submit" name="refresh" value="Show Activity" />
        </td>
    </tr>
    </table>
    </form>
    
    <?php if(!empty($logs)) { ?>
    <table cellpadding="5" class="bordered">
    <thead>
    	<th>User</th>
        <th>Date-Time</th>
        <th>Action</th>
        <th>Description</th>
        <th>Amount</th>
        <th>&nbsp;</th>
    </thead>
    <tbody>
    <?php foreach($logs as $lg): ?>
    <tr>
    	<td align="right"><?php echo $lg->getUser(); ?></td>
        <td align="right"><?php echo $lg->getLogTime(); ?></td>
        <td align="right"><?php echo $lg->getAction(); ?></td>
        <td align="left"><?php echo $lg->getMessage(); ?></td>
        <td align="right"><?php echo $lg->getAmount(); ?></td>
        <td align="center"><a class="del-log" href="delete_log.php?id=<?php echo $lg->id; ?>" title="delete log"><strong>X</strong></a></td>
    </tr>
    <?php endforeach; ?>
    <tr>
    	<td align="right" colspan="4" class="analysis">Non-Expense Transactions</td>
        <td align="right" class="analysis"><?php echo $transactions; ?></td>
    </tr>
    <tr>
    	<td align="right" colspan="4">Total Expenses</td>
        <td align="right"><?php echo $expenses; ?></td>
    </tr>
    <tr>
    	<td align="right" colspan="4" class="analysis">Net Balance</td>
        <td align="right" class="analysis"><?php echo $net_balance; ?></td>
    </tr>
    </tbody>
    </table>
    <?php } elseif(empty($logs) && isset($logs)) { 
			$mesg = "No user activity was recorded during the period";
			echo output_message($mesg);
		}
	?>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>