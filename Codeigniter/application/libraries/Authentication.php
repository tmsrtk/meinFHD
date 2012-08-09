<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>
 */

/**
 * Authentication Library
 *
 * Description...
 */
class Authentication {
	
	private $uid = 0;
	private $name = 'Guest';
	private $email = '';
	private $roles = array('guest');
	private $admin_uid = 0;
	private $CI;
	
	/**
	 * Initialize the data
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		// Store the user ID even if it does not exist
		$uid = $this->CI->session->userdata('uid');

		// If we have an ID which is not NULL, FALSE or 0,
		// a valid user ID is stored in the session which
		// means that there's a user logged in.
		if ($uid)
		{
			// Set the user ID.
			$this->uid = $uid;
			$this->_load_roles();
		}
		// Invoke the firewall to see if the current user has access
		$this->_invoke_firewall();
	}
	
	/**
	 * This is the main login function.
	 *
	 * We check the database for the given username and password.
	 * If they are available and match, the user can be logged in.
	 * To do so, we save the user's data to the user object and
	 * initialize a session where we store the users's ID (uid)
	 *
	 * @access public
	 * @param string name
	 * @param string pass
	 * @return bool
	 */
	public function login($name, $pass)
	{
		// Load the user ID that matches the username and password
		$query = $this->CI->db->query('SELECT BenutzerID 
									FROM benutzer 
									WHERE LoginName = ? AND Passwort = MD5(?)', array($name, $pass));
		
		// There is a user that matches the parameters
		if ($query->num_rows() == 1)
		{
			// The uid ist stored as BenutzerID
			$this->uid = $query->row()->BenutzerID;
			//$this->_load();
			// Keep the user logged in by initializing the session
			$this->CI->session->set_userdata('uid', $this->uid);

            // User has logged in successfully
			return TRUE;
		}

		return FALSE;
	}

    /**
     * Login function for administrators to login as another user.
     * The regular login function can not used, because we need to save the information of the logged in administrator
     * to perform a redirect back into the administrator session, if an logut is performed.
     *
     * @access public
     * @param array user_data provides the whole user-information (user_id, loginname, lastname, forename, email, user_function)
     * @return bool returns a bool wether the login was successful or not
     * @author Christian Kundruss
     */
    public function login_as_user($userdata) {

        // first of all save the user data of the actual user for moving back to the admin session when the user logout is performed
        $this->admin_uid = $this->CI->session->userdata('uid');// save the information of the admin (actual authenticated user) in the authentication object

        // security check if the choosen user is valid, that there is only one user with the equal username (usually there is only one user....)
        $query = $this->CI->db->query('SELECT BenutzerID, Vorname, Nachname
                                       FROM benutzer
                                       where BenutzerID = ?',$userdata['user_id']);

        if ($query->num_rows() == 1){ // the user only exists once in the database
            $this->CI->session->set_userdata('admin_uid', $this->admin_uid);
            $this->CI->session->set_userdata('login_from_admin', 'TRUE'); // save that the login comes from the admin backend

            // save the "new" user with the given user_id
            $this->uid = $userdata['user_id'];

            // save the name of the logged in user for further displays
            $this->name = $query->row()->Vorname . ' ' . $query->row()->Nachname;
            // initialize the session with the new user-id
            $this->CI->session->set_userdata('uid', $this->uid);

            // authentication was successful
            return TRUE;
        }

        // authentication wasn`t successful. the user_id exists at least two times
        return FALSE;
    }
	
    /**
     * This is the login function for the Single-Sign-On process.
     *
     * The information of the local account, which is linked with the idp user are assigned as parameters
     * and will be used for establishing the local session.
     *
     * @author Christian Kundruss (CK), 2012
     * @access public
     * @param string $username
     * @param string $hashed_password
     * @return bool TRUE if the login was successful, otherwise FALSE
     */
    public function sso_login($username, $hashed_password) {

        // select the needed uid of the local user
        $this->CI->db->select('BenutzerID');
        $this->CI->db->from('benutzer');
        $this->CI->db->where('LoginName', $username);
        $this->CI->db->where('Passwort', $hashed_password);

        $query = $this->CI->db->get();
        // if there is one user that matches the parameter -> establish the session
        if($query->num_rows() == 1) {
            // store the user id in the authentication object
            $this->uid = $query->row()->BenutzerID;

            // establish the session
            $this->CI->session->set_userdata('uid', $this->uid);

            return TRUE; // session is established
        }

        return FALSE; // session is not established
    }

	/**
	 * Loads all roles for the current user.
	 * 
	 * There's always the role "user" for authenticated users
	 * and always the role "guest" for... guests :)
	 *
	 * @access private
	 * @return void
	 */
	private function _load_roles()
	{
		// Fetch all roles for the current user
		$query = 'SELECT * FROM rolle NATURAL JOIN benutzer_mm_rolle WHERE BenutzerID = ?';
		
		// Perform the query
		$result = $this->CI->db->query($query, array($this->uid));
		
		$this->roles = array('user');
		
		// Save all roles to the roles array
		foreach ($result->result() as $row)
		{
			$this->roles[] = $row->bezeichnung;
		}
	}
		
	/**
	 * Determines whether the user is logged in or not.
	 *
	 * @access public
	 * @return bool
	 */
	public function is_logged_in()
	{
		return (bool) $this->uid;
	}
	
	/**
	 * For detailed permission checks a string can be passed along.
	 * The function will ask the database if the user has the permissions.
	 *
	 * @access public
	 * @param string
	 * @return bool
	 */
	public function has_permissions($action = '')
	{
		// Do we have an action?
		if ( ! empty($action) && is_string($action))
		{
			// Select all roles that have the permission to
			// perform the given action.
			$sql = 'SELECT
						r.bezeichnung
					FROM
						berechtigung b, rolle_mm_berechtigung rb, rolle r
					WHERE
						b.BerechtigungID = rb.BerechtigungID AND
						r.RolleID = rb.RolleID AND
						b.bezeichnung = ?';
						
			// Perform the query
			$result = $this->CI->db->query($sql, array($action));
			
			// This array holds all roles, that have permissions
			// for the given action.
			$allowed = array();
			// Save all roles for lookup
			foreach ($result->result() as $row)
			{
				$allowed[] = $row->bezeichnung;
			}
			
			// Check if the current user has one of the roles
			return $this->_user_can_access($allowed, $this->roles);
		}
		
		return FALSE;
	}
	
	/**
	 * Destroys the current session so that the user is logged out.
	 *
	 * @access public
	 * @return void
	 */
	public function logout()
	{
        // IS DELETING uid SAVE ENOUGH???
        $this->CI->session->unset_userdata('uid');
        $this->CI->session->unset_userdata('admin_uid');
        $this->CI->session->unset_userdata('login_from_admin'); // regular logout -> the login is not provided by an admin

        //$this->CI->session->sess_destroy();
        $this->uid = 0;
        $this->name = 'Guest';
        $this->email = '';
        $this->roles = array('guest');
	}

    /**
     * Switch back the user back to his administrator session.
     * Is used if an admin authenticates him as an user and then performs an logout
     *
     * @author Christian Kundruss
     * @return void
     */
    public function switchBackToAdmin() {
        // change the current user-id to the user-id of the admin
        $this->uid = $this->CI->session->userdata('admin_uid');
        $this->name = ' ';

        // set the session information back to the administrator information
        $this->CI->session->set_userdata('uid', $this->uid);
        $this->CI->session->set_userdata('login_from_admin', 'FALSE'); // login does not come anylonger from an administrator
        $this->CI->session->unset_userdata('admin_uid');
    }

	/**
	 * Returns the user id.
	 *
	 * @access public
	 * @return int
	 */
	public function user_id()
	{
		if (is_numeric($this->uid))
		{
            return $this->uid;
		}	
		return FALSE;
	}

    /**
     * Returns the constructed name of the user
     * @access public
     * @return string the provided name of the user
     */
    public function get_name() {
       return $this->name;
        //return 'TEST';
    }
	/**
	 * The firewall detects access controled routes and determines,
	 * if the current user has access to it.
	 *
	 * @access private
	 * @return TRUE if the user has access, void on fail
	 */
	private function _invoke_firewall()
	{
		// Load firewall config
		$this->CI->config->load('firewall');

		// Load the login route
		$login_page = $this->CI->config->item('login_page', 'firewall');

		// Load the current route
		$current_route = $this->CI->uri->ruri_string();

		// If we're on the login page, always allow access
		if ($current_route == "/{$login_page}")
		{
			return TRUE;
		}
		
		// We are not on the login page, so we need to check the access.
		// Load access controled routes
		$controled_routes = $this->CI->config->item('access_control', 'firewall');

		foreach ($controled_routes as $controled_route)
		{
			$pattern = '/' . str_replace('/', '\/', $controled_route['pattern']) . '/';
			
			if (preg_match($pattern, $current_route) == 1)
			{
				// The current route matches the condition
				// Now let's look, if we have access to that role
				if ( ! $this->_user_can_access($controled_route['roles'], $this->roles))
				{
					// 403 if access denied, redirect to login page if not logged in
					if ($this->uid == 0)
					{
						redirect($login_page);
					}
					else
					{
						$this->CI->message->set('403 - Forbidden', 'error');
						redirect('app', 403);
					}
				}	
			}
		}
		return TRUE;
	}
	
	/**
	 * Walks through two arrays looking for matches.
	 * If there is a match, we know, that the user has access.
	 *
	 * @access private
	 * @param Array $allowed
	 * @param Array $given
	 * @return TRUE on match, otherwise FALSE
	 */
	private function _user_can_access($allowed, $given)
	{
		foreach ($allowed as $a)
		{
			if (in_array($a, $given))
			{
				return TRUE;
			}
		}
		return FALSE;
	}

    /**
     * Returns if the user login comes from an admin account or not.
     * @author Christian Kundruss
     * @access public
     * @return TRUE if logged in from admin-account, FALSE otherwise
     */
    public function isLoggedInFromAdmin() {
        if($this->CI->session->userdata('login_from_admin') == 'TRUE'){
            return TRUE;
        }

        return FALSE;
    }
}
/* End of file Authentication.php */
/* Location: ./application/libraries/Authentication.php */