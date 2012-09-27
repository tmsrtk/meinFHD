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
 * Class App
 *
 * Description...
 */
class App extends FHD_Controller {
	
	// default constructor to prepare all needed stuff
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Index
	 *
	 * .../app
	 * .../app/index
	 */
	public function index()
	{
		$this->load->view('app/index', $this->data->load());
	}
	
	/**
	 * Login
	 *
	 * .../app/login
	 * .../login
	 */
	public function login()
	{
		// read the post parameters
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		// if we have a value
		if ($username || $password)
		{
			// call the login funtion from the authentication class
			if ($this->authentication->login($username, $password))
			{
				// user is logged in -> set message and redirect to frontpage
				if ($this->agent->is_mobile()) {
					redirect('dashboard/mobile');
				}
				else {
					redirect('dashboard/index');
				}
				
			}
			else
			{
				// something got wrong -> set message and redirect to login page
				$this->message->set('User oder Passwort falsch!', 'error');
				redirect('app/login');
			}
		}
		
		// if there's no post data, we should show the login screen
		$this->load->view('app/login', $this->data->load());
	}
	
	/**
	 * Logout
	 *
	 * .../app/logout
	 * .../logout
     * Modifications and minor changes by Christian Kundruss
	 */
	public function logout()
	{
        echo $this->authentication->isLoggedInFromAdmin();

        if ($this->authentication->isLoggedInFromAdmin() == 'TRUE') { // the user is logged in from an admin, change the user data and switch back to admin session
            $this->authentication->switchBackToAdmin();
            // show a message that the return into the admin session was successful
            $this->message->set('Du befindest dich wieder in der Administrator-Session.');
            // redirect to the 'benutzerverwaltung/benutzer bearbeiten'; outgoing point
            redirect('admin/edit_user_mask');
        }

        else { // otherwise perform a regular logut
            $this->message->set(sprintf('Ausgeloggt! (ID: %s)', $this->authentication->user_id()));
            $this->authentication->logout();
            redirect('app/login');
        }
	}
}

/* End of file App.php */
/* Location: ./application/controllers/App.php */