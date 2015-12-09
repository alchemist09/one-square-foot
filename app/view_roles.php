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
	if(isset($_GET['refresh'])){
		$role_id = (int)$_GET['role'];
		if(empty($role_id) || !is_int($role_id)){
			$err = "Select a role to display permission assigned to it";	
		} else {
			$permissions = $ac->getRolePermissions($role_id);
		}
	}
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    	<h3>Actions</h3>
        <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_users" => "View Users", "manage_perms" => "Manage Permissions", "view_roles" => "Roles of Users", "view_logs" => "View Logs");
        echo create_action_links($actions);
    ?>
    	</div>
        <div id="main-content">
        	<h2>Permissions Assigned to Various Roles</h2>
            
            <?php $mesg = $session->message(); if(!empty($mesg)) { echo output_message($mesg); } ?>
            <?php if(!empty($err)) { echo display_error($err); } ?>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
            <table cellpadding="5">
            <tr>
            	<td><label for="role">Select Role/Group:</label></td>
                <td>
                	<?php $ac->displayRoles(); ?>
                </td>
            </tr>
            <tr>
            	<td colspan="2" align="right">
                	<input type="submit" name="refresh" value="Show Permissions" />
                </td>
            </tr>
            </table>
            </form>
            
            <?php
				if(!empty($permissions)){
					foreach($permissions as $p){
						$paragraph  = "<p><strong>";
						$paragraph .= $p['name']."&nbsp;-</strong>";
						$paragraph .= $p['description'];
						echo $paragraph;	
					}	
				} elseif(empty($permissions) && !empty($role_id)){
					echo "<p>That role has no permissions assigned to it yet</p>";	
				}
			?>
            
            <p><a href="manage_perms.php" title="change permissions assigned to this role">Edit Permissions</a></p>
        </div>
    </div>

<?php include_layout_template('admin_footer.php'); ?>