<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>, Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Authentication Library
 *
 * Library includes all necessary authentication functions. It implements the main authentication process including the firewall
 * to grant user specific access to the protected content.
 * @author Manuel Moritz (MM) <manuel.moritz@fh-duesseldorf.de>
 * @author Christian Kundruß (CK) <christian.kundruss@fh-duesseldorf.de>
 */
class Authentication {

    /**
     * Holds the uid of the currently authenticated user.
     * @var int
     */
	private $uid = 0;
    /**
     * Holds the fully qualified name for the authenticated user (given name & surname).
     * @var string
     */
	private $name = 'Guest';
    /**
     * Holds the email of the currently authenticated user.
     * @var string
     */
	private $email = '';
    /**
     * Holds the role for the currently authenticated user (used for access protection).
     * @var array
     */
	private $roles = array('guest');
    /**
     * Holds the uid of the logged in admin, if he wants to authenticated himself as another user.
     * Needed to be able to jump back to the admin session.
     * @var int
     */
	private $admin_uid = 0;

    /**
     * Holds the ci instance.
     * @var CI_Controller
     */
	private $CI;
	
	/**
	 * Default constructor. Initializes the data.
     * @access public
     * @return void
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		// Store the user ID even if it does not exist
		$uid = $this->CI->session->userdata('uid');

		/*
		 * If we have an ID which is not NULL, FALSE or 0,
		 * a valid user ID is stored in the session which
		 * means that there's a user logged in.
		 */
		if ($uid)
		{
			// Set the user ID.
			$this->uid = $uid;
            // load roles of the authenticated user
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
	 * @param string name Username of the user, that wants to sign on
	 * @param string pass Password that corresponds to the user id
	 * @return bool TRUE if the user has been logged in successfully, otherwise FALSE will be returned.
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
			// load roles of the authenticated user
            $this->_load_roles();
			// Keep the user logged in by initializing the session
			$this->CI->session->set_userdata('uid', $this->uid);

            // User has logged in successfully
			return TRUE;
		}

        // there is no matching user (e.g. incorrect data ...)
		return FALSE;
	}

    /**
     * Login function for administrators to sign on as another user.
     * The regular login function can not be used, because we need to save the information of the logged in administrator
     * to perform a redirect back into the administrator session, if an logout is performed.
     *
     * @access public
     * @param array user_data provides the whole user-information (user_id, loginname, lastname, forename, email, user_function)
     * @return bool returns a bool whether the login was successful or not
     * @author Christian Kundruss
     */
    public function login_as_user($userdata) {

        // first of all save the user data of the actual user for moving back to the admin session when the user logout is performed
        $this->admin_uid = $this->CI->session->userdata('uid');// save the information of the admin (actual authenticated user) in the authentication object

        // security check if the chosen user is valid, that there is only one user with the equal username (usually there is only one user....)
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

        // authentication wasn`t successful. The user_id exists at least two times
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
        $this->CI->db->select('BenutzerID, Vorname, Nachname');
        $this->CI->db->from('benutzer');
        $this->CI->db->where('LoginName', $username);
        $this->CI->db->where('Passwort', $hashed_password);

        $query = $this->CI->db->get();
        // if there is one user that matches the parameter -> establish the session
        if($query->num_rows() == 1) {
            // store the user id in the authentication object
            $this->uid = $query->row()->BenutzerID;

            // save the name of the logged in user for further displays
            $this->name = $query->row()->Vorname . ' ' . $query->row()->Nachname;

            // establish the session
            $this->CI->session->set_userdata('uid', $this->uid);
            $this->CI->session->set_userdata('SSO-Login', 'TRUE');

            return TRUE; // session has been established
        }

        return FALSE; // session has not been established
    }

	/**
	 * Loads all roles for the current user and saves them in the instance variable roles.
	 * 
	 * There's always the role "user" for authenticated users
	 * and always the role "guest" for... guests :)
	 *
	 * @access private
	 * @return void
     * @todo alle rollen prüfen, ob die gelieferten Werte in irgendeiner Art und Weise brauchbar sind.
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
     * Edit by Christian Kundruss (CK):
     * Checks also whether the initial authentication comes from an sso login or not. If it comes from an sso login it checks, if
     * the global session exists. If the global session doesn`t exists anylonger an local logout will be called.
     *
	 * @access public
	 * @return bool
	 */
	public function is_logged_in()
	{
        // --- EDIT BY Christian Kundruss (CK) for checking if the global session still exists, when the login was provided by sso --
        if ($this->CI->session->userdata('SSO-Login') == 'TRUE') { // local authentication from an global session?
            // is the global session still active?

            // if the global session does not exists any longer, or the linking of the local user account is not provided any longer the session should be destroyed
            if(!$this->CI->samlauthentication->is_authenticated() || !$this->CI->samlauthentication->has_linked_account() ) {
                $this->CI->session->unset_userdata('SSO-Login'); // remove the sso flag from the session object
                $this->logout(); // perform the logout

                return FALSE;
            }
        }
        // --- END EDIT BY CK ---

        return (bool) $this->uid;
	}
	
	/**
     * The function will ask the database, if the user has the permissions.
	 * For detailed permission checks a string can be passed along.
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
     * @todo Prüfen, ob das Löschen der UID ausreichend ist, oder ob nochmehr getan werden muss
	 */
	public function logout()
	{
        // IS DELETING uid SAVE ENOUGH???
        $this->CI->session->unset_userdata('uid');
        $this->CI->session->unset_userdata('admin_uid');
        $this->CI->session->unset_userdata('login_from_admin'); // regular logout -> the login is not provided by an admin

        $this->uid = 0;
        $this->name = 'Guest';
        $this->email = '';
        $this->roles = array('guest');
	}

    /**
     * Function siwtches the user back to his administrator session.
     * Is used if an admin authenticates authenticates himself as another user and then performs an logout.
     *
     * @author Christian Kundruss
     * @access public
     * @return void
     */
    public function switch_back_to_admin() {
        // change the current user-id to the user-id of the admin
        $this->uid = $this->CI->session->userdata('admin_uid');
        $this->name = ' ';

        // set the session information back to the administrator information
        $this->CI->session->set_userdata('uid', $this->uid);
        $this->CI->session->set_userdata('login_from_admin', 'FALSE'); // login does not come any longer from an administrator
        $this->CI->session->unset_userdata('admin_uid');
    }

	/**
	 * Returns the user id.
	 *
	 * @access public
	 * @return mixed Returns the uid of the authenticated user, ore FALSE if there is no authenticated user.
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
    public function get_name()
    {
       return $this->name;
    }
    
	/**
	 * The firewall detects access controlled routes and determines,
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
		$login_page = $real_login_page = $this->CI->config->item('login_page', 'firewall');
		
		if (isset($this->CI->router->routes[$login_page]))
		{
			$real_login_page = $this->CI->router->routes[$login_page];
		}
		
		// Load the current route
		$current_route = $this->CI->uri->ruri_string();

		// If we're on the login page, always allow access
		if ($current_route == "/{$real_login_page}")
		{
			if ($this->is_logged_in())
			{
				redirect($this->CI->router->routes['default_controller'], 403);
			}
			return TRUE;
		}
		
		// We are not on the login page, so we need to check the access.
		// Load access controlled routes
		$controlled_routes = $this->CI->config->item('access_control', 'firewall');

		foreach ($controlled_routes as $controlled_route)
		{
			$pattern = '/' . str_replace('/', '\/', $controlled_route['pattern']) . '/';
			
			if (preg_match($pattern, $current_route) == 1)
			{
				// The current route matches the condition
				// Now let's look, if we have access to that role
				if ( ! $this->_user_can_access($controlled_route['roles'], $this->roles))
				{
					// 403 if access denied, redirect to login page if not logged in
					if ($this->uid == 0)
					{
						redirect($login_page);
					}
					else
					{
						$this->CI->message->set('403 - Forbidden', 'error');
						redirect($this->CI->router->routes['default_controller'], 403);
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
    public function is_logged_in_from_admin() {
        if($this->CI->session->userdata('login_from_admin') == 'TRUE'){
            return TRUE;
        }

        return FALSE;
    }
}
/* End of file Authentication.php */
/* Location: ./application/libraries/Authentication.php */