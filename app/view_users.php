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
?>
<?php include_layout_template('admin_header.php'); ?>

    <div id="container">
   	<h3>Actions</h3>
    <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_roles" => "Roles of Users", "manage_perms" => "Manage Permissions", "view_logs" => "View Logs");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <a class="new-item" href="create_user.php" title="create a new user">Create User</a>
    
    <h2>Users in System</h2>
    
    <?php 
		$mesg = $session->message();
		echo output_message($mesg);
	?>
    
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
    	<th>Name</th>
        <th>Username</th>
        <th>User Details</th>
        <th>User Roles</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
	<?php foreach($users as $u): ?>
    <tr>
    	<td><?php echo $u->getFullName(); ?></td>
        <td><?php echo $u->getUserName(); ?></td>
        <td><a href="edit_user.php?uid=<?php echo $u->id; ?>" title="edit details of this user">Change Details</a></td>
        <td><a href="user_roles.php?uid=<?php echo $u->id; ?>" title="view roles of this user">Roles/Group</a></td>
        <td><a class="del-user" href="delete_user.php?uid=<?php echo $u->id; ?>" title="delete this user account"><strong>X</strong></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>