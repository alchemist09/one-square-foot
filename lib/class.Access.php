<?php

	/**
	 * Class to implement Role Based Access Control(RBAC) in the 
	 * application by controlling access to any resource or operation
	 * that is associated with a permission
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date July 27, 2015
	 */
	
	class Access
	{
		
		/**
		 * Store a user's permissions in the $_SESSION
		 * array when they login
		 * @param int $user_id
		 */
		public function loadPermissions()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			if(isset($_SESSION)){
				$_SESSION['permissions'] = array();
				$sql  = "SELECT DISTINCT p.name AS permission FROM permission p ";
				$sql .= "INNER JOIN collection2permission cp ON p.id = cp.permission_id ";
				$sql .= "INNER JOIN user2collection uc ON cp.collection_id = uc.collection_id ";
				$sql .= "WHERE uc.user_id = ".$_SESSION['user_id'];
				$result = $mysqli->query($sql);
				while($permission = $result->fetch_assoc()){
					$_SESSION['permissions'][$permission['permission']] = 1;	
				}
			}
		}
		
		/**
		 * Check if the logged in user has permission to perform
		 * a certain action by comparing that permission against
		 * that user's loaded permissions
		 * @param string $permission
		 * @return boolean
		 */
		public function hasPermission($permission)
		{
			return isset($_SESSION['permissions']['admin']) || isset($_SESSION['permissions'][$permission]);
		}
		
		/**
		 * Check if the logged in user has the set of permissions
		 * required to perform a certain action by comparing those set
		 * of permission against the user's loaded permissions 
		 * @param array $permissions
		 */
		public function checkPermissions($permissions)
		{
			if(isset($_SESSION['permissions']['admin'])){
				return;	
			}
			if(isset($permissions)){
				if(!is_array($permissions)){
					$permissions = array($permissions);	
				}	
				foreach($permissions as $p){
					if(empty($_SESSION['permissions'][$p])){
						throw new Exception('You do not have permission to access this page');
						/**$mesg = "You don't have permission to access this page";
						global $session;
						$session->message($mesg);
						redirect_to($_SERVER['HTTP_REFERER']);*/	
					}	
				}
			}
		}
		
		/**
		 * Get all the roless that have been defined 
		 * @return array
		 */
		public function findAllRoles()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql = "SELECT * FROM collection c";
			$result = $mysqli->query($sql);
			$roles = array();
			while($row = $result->fetch_assoc()){
				$roles[] = $row;	
			}
			return $roles;
		}
		
		/**
		 * Create a HTML SELECT element from the roles defined in the system
		 */
		public function displayRoles()
		{
			$roles_array = $this->findAllRoles();
			$html  = '<select name="role" id="roles">';
			$html .= '<option value="" selected="selected">Select a Role</option>';
			foreach($roles_array as $role){
				$html .= '<option value="'.$role['id'].'">'.$role['name'].'</option>';	
			}
			$html .= '</select>';
			echo $html;
		}
		
		/**
		 * Find the permission(s) assigned to a particular group
		 * @param int $role_id The ID of the group/role
		 * @return array
		 */
		public function getRolePermissions($role_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT DISTINCT p.* FROM permission p INNER JOIN collection2permission cp ";
			$sql .= "ON p.id = cp.permission_id INNER JOIN user2collection uc ON ";
			$sql .= "cp.collection_id = uc.collection_id WHERE uc.collection_id = ".$role_id;
			$result = $mysqli->query($sql);
			if($result->num_rows >= 1){
				while($row = $result->fetch_assoc()){
					$permissions[] = $row;	
				}
				return $permissions;
			} else {
				return false;
			}
		}
		
		/**
		 * Get all the permission that have been defined in the system
		 * @return array 
		 */
		public function findAllPermissions()
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql = "SELECT * FROM permission p";
			$result = $mysqli->query($sql);
			$permissions = array();
			while($row = $result->fetch_assoc()){
				$permissions[] = $row;	
			}
			return $permissions;
		}
		
		/**
		 * Get the different roles a user has been assigned
		 * @param int $user_id
		 * @return array
		 */
		public function findRolesOfUser($user_id)
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$sql  = "SELECT c.id, c.name FROM collection c INNER JOIN user2collection uc ";
			$sql .= "ON c.id = uc.collection_id WHERE uc.user_id = ";
			$sql .= $mysqli->real_escape_string($user_id);
			$result = $mysqli->query($sql);
			$roles = array();
			while($row = $result->fetch_assoc()){
				$roles[] = $row;
			}
			return $roles;
		}
		
		/**
		 * Change the permissions assigned to a role by overiting 
		 * the original permissions with new ones
		 * @param int $role_id The ID of the group
		 * @param array $perms An array of permissions
		 * @return boolean
		 */
		public function changePermissions($role_id, $perms=array())
		{
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$values = array();
			// Case of revoking all permissions from the group
			if(empty($perms)){
				$sql = "DELETE FROM collection2permission WHERE collection_id = ".$mysqli->real_escape_string($role_id);
				$mysqli->query($sql);
				return true;
			}
			for($i=0; $i < count($perms); $i++){
				$values[] = "(".$role_id.", ".$perms[$i].")";	
			}
			$query_values = implode(", ", $values);
			$mysqli->autocommit(false);
			$sql = "DELETE FROM collection2permission WHERE collection_id = ".$mysqli->real_escape_string($role_id);
			$mysqli->query($sql);
			$sql  = "INSERT INTO collection2permission (collection_id, permission_id) ";
			$sql .= "VALUES {$query_values}";
			$mysqli->query($sql);
			if($mysqli->commit()){
				$mysqli->autocommit(true);
				return true;
			} else {
				$mysqli->rollback();
				$mysqli->autocommit(true);
				return false;	
			}
		}
		
	}
	
	$ac = new Access();

?>