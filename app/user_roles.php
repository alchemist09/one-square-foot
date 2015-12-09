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
	if(isset($_GET['uid']) && !empty($_GET['uid'])){
		$user_id = (int)$_GET['uid'];
		if(!is_int($user_id)){
			redirect_to('view_users.php');	
		} else {
			$roles = $ac->findRolesOfUser($user_id);
			//echo var_dump($roles);
			$user = User::findById($user_id);	
		}
	}
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_users" => "View Users",  "view_roles" => "Roles of Users", "view_logs" => "View Logs", "admin" => "Go Back");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <h2>Roles Assigned to <?php echo $user->getFullName(); ?></h2>
    
    <?php 
		$mesg = $session->message();
		echo output_message($mesg);
	?>
    
    <?php foreach($roles as $r): ?>
    <p><?php echo $r['name']; ?></p>
    <?php endforeach; ?>
    <br /><br />
    <p><a href="edit_user_roles.php?uid=<?php echo $user->id; ?>" title="edit roles of this user">Edit Roles</a></p>
    
    </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>