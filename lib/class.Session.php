<?php

	/**
	 * A class to work with sessions, in this case 
	 * primary logging in and logging out
	 *
	 * Keep in mind that when working with sessions, it's generally
	 * inadvisable to store DB-related objects in sessions
	 *
	 * @author Luke <mugapedia@gmail.com>
	 * @date April 11, 2015
	 */ 
	
	class Session
	{
		/**
		 * The ID value of the user who initialized the session
		 * @var int
		 */
		public $userID;
		
		/**
		 * Placeholder for session messages
		 * @var string
		 */
		public $message;
		
		/**
		 * Condition that establishes the login status 
		 * of a user as either true or false
		 * @var boolean
		 */
		private $loggedIn = false;
		
		/**
		 * Field to hold a session varialbles
		 * @var mixed
		 */
		public $sessionVars = array();
		
		/**
		 * Magic construct
		 * Initialize the session class and check whether the 
		 * user is logged in or not
		 */
		function __construct()
		{
			session_start();
			$this->_checkLogin();
			$this->_checkMessage();
		}
		
		/**
		 * Public method that returns the login status of the user
		 * @return boolean
		 */
		public function isLoggedIn()
		{
			return $this->loggedIn;
		}
		
		/**
		 * Given a user object that has been initialized from
		 * a database record, login the user by setting his login
		 * status to true. Load the permissions assigned to the group
		 * that the logged in user belongs to concurrently
		 * @param object $user A user object
		 * @param object $ac An access object to load user permissions
		 */
		public function login($user, $ac)
		{
			// database should find user based on username/password
			if($user){
				$this->userID = $_SESSION['user_id'] = $user->id;
				//$ac = new Access();
				$ac->loadPermissions();
				$this->loggedIn = true;	
			}
		}
		
		/**
		 * Logout the user by setting is logged_in status to false
		 * and unsetting the $user_id property of the class
		 */
		public function logout()
		{
			unset($_SESSION['user_id']);
			unset($this->userID);
			$this->loggedIn = false;
		}
		
		/**
		 * Method that lets the class do an internal check
		 * to see the login status of the user
		 */ 
		private function _checkLogin()
		{
			if(isset($_SESSION['user_id'])) {
				$this->userID = $_SESSION['user_id'];
				$this->loggedIn = true;	
			} else {
				unset($this->userID);
				$this->loggedIn = false;	
			}
		}
		
		/**
		 * Set a session message or get an existing session message
		 */
		public function message($msg="")
		{
			// Case of SETing a session message
			// $this->message = $msg won't work coz it will be just be setting and attribute
			if(!empty($msg)){
				$_SESSION['message'] = $msg;	
			} else {
				// GETing a session message
				return $this->message;
			}
		}
		
		/**
		 * Internally check if an existing session message is set
		 * inorder to set the message property of the class
		 */
		private function _checkMessage()
		{
			// Is there a message stored in session?
			if(isset($_SESSION['message'])){
				// Add it as an attribute an erase the stored version
				$this->message = $_SESSION['message'];
				unset($_SESSION['message']);	
			} else {
				$this->message = "";	
			}
		}
		
		/**
		 * Get or set a session variable
		 * @param string $name The name of the session variable
		 * @param mixed $value The value of the session variable
		 * @return mixed The value of the session variable
		 */
		public function sessionVar($name, $value="")
		{
			$num_args = func_num_args();
			if($num_args == 2){
				// Case of SETing a session variable
				$_SESSION["{$name}"] = $value;	
			} elseif($num_args == 1){
				// Case of GETing a session
				if(isset($_SESSION["{$name}"])){
					return $_SESSION["{$name}"];	
				}
			}
		}
		
		/**
		 * Unset a session variable
		 * @param string The name of the session variable
		 */
		public function destroySessionVar($name)
		{
			if(isset($_SESSION["{$name}"])){
				unset($_SESSION["{$name}"]);	
			}
		}
		
		/**
		 * Unset multiple session variables
		 * @param array $vars Names of session variables to unset
		 */
		public function destroyMultipleSessionVar($session_vars= array())
		{
			foreach($session_vars as $var){
				if(isset($_SESSION["{$var}"])){
					unset($_SESSION["{$var}"]);	
				}	
			}
		}
	}
	
	$session = new Session();
	$message = $session->message();

?>