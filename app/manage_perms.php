<?php require_once('../lib/init.php'); ?>
<?php 
	if(!$session->isLoggedIn()) { redirect_to('../index.php'); } 
	if(!$ac->hasPermission('admin')){
		$mesg = "You don't have permission to access this page";
		$session->message($mesg);
		redirect_to($_SERVER['HTTP_REFERER']);
	}
?>
<?php
	$permissions = $ac->findAllPermissions();
	if(isset($_GET['refresh'])){
		//echo '<tt><pre>'.var_dump($_GET).'</pre></tt>';		
		$role_id = (int)$_GET['role'];	
		
	}
	
	/////////////////////////////////////////////////////////////////
	///////////////////////// PROCESS SUBMIT ////////////////////////
	/////////////////////////////////////////////////////////////////
	
	if(isset($_POST['submit'])){
		//echo var_dump($_POST);
		if(empty($_POST['role'])){
			$err = "Form fields marked with an asterix are required";	
		} else {
			$role_id = (int)$_POST['role'];
			if($ac->changePermissions($role_id, $_POST['perms'])){
				$mesg = "Changes saved";
				$session->message($mesg);
				redirect_to("view_roles.php");	
			} else {
				$err = "An error occured preventing the changes from being saved";	
			}
		}
	}
	
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    	<h3>Actions</h3>
        <div id="side-bar">
    <?php
        $actions = array("create_user" => "Create User", "view_users" => "View Users",  "view_roles" => "Roles of Users", "admin" => "Go Back");
        echo create_action_links($actions);
    ?>
    	</div>
    	<div id="main-content">
        
        <h2>Manage Permissions Assigned to a Group</h2>
        
        <?php if(!empty($err)) { echo display_error($err); } ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <table cellpadding="5">
        <tr>
        	<td><label for="role">Role(Group)
            <span class="required-field">*</span>:</label></td>
            <td>
                <?php $ac->displayRoles(); ?>
            </td>
        </tr>
        <tr>
        	<td>Permissions<span class="required-field">*</span></td>
            <td>
            <?php
				$html = '';
				foreach($permissions as $p){  
					$html .= '<p><label><input type="checkbox" name="perms[]" ';
					$html .= 'value="'.$p['id'].'" /><strong>'.$p['name'].'</strong>';
					$html .= '&nbsp;-&nbsp;'.$p['description'].'</label></p>';
                }
				echo $html;
			?>
            </td>
        </tr>
        <tr>
        	<td>
            	<input type="submit" name="submit" value="Save Changes" />
            </td>
        </tr>
        </table>
        </form>
        
        </div>																																																								
    </div>

<?php include_layout_template('admin_footer.php'); ?>