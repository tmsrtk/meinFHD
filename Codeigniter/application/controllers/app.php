<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class App
 *
 * Description...
 */
class App extends FHD_Controller {

	/**
     * Default constructor to prepare all needed stuff.
     *
     * @return void
     */
	function __construct(){
		parent::__construct();

        // load the admin model
        $this->load->model('admin_model');

	}
	
	/**
	 * Index
	 *
	 * .../app
	 * .../app/index
     *
     * @access public
     * @return void
	 */
	public function index()
	{
		// user is logged in -> set message and redirect to frontpage
		if ($this->agent->is_mobile()) {
			redirect('dashboard/mobile');
		}
		else {
			redirect('dashboard/index');
		}
	}
	
	/**
	 * Login
	 *
	 * .../app/login
	 * .../login
     * Modifications and minor changes by Christian Kundruss (CK), 2012
     *
     * @access public
     * @return void
	 */
	public function login()
	{
        // --- Modification for automatic authentication if an global session exists Begin ---
        // a global session exists and the user has got an linked account -> log him in
        //if ($this->samlauthentication->is_authenticated() && has_linked_account()) {
        if ($this->samlauthentication->is_authenticated() && $this->samlauthentication->has_linked_account()) {
            redirect('sso/establish_local_session');
        }
        // --- Modification End ---

        // there is no global session & no linked account -> show the local login page when visiting the page
        else {

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
                    $this->message->set(sprintf('Eingeloggt! (ID: %s)', $this->authentication->user_id()));
                    redirect('dashboard/index');
                }
                else
                {
                    // something got wrong -> set message and redirect to login page
                    $this->message->set('User oder Passwort falsch!', 'error');
                    redirect('app/login');
                }
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
     *
     * @access public
     * @return void
	 */
	public function logout()
	{
        // --- Modification for returning to admin session, if the admin is logged in as another user Begin ---
        if ($this->authentication->is_logged_in_from_admin() == 'TRUE') { // the user is logged in from an admin, change the user data and switch back to admin session
            $this->authentication->switch_back_to_admin();
            // show a message that the return into the admin session was successful
            $this->message->set('Du befindest dich wieder in der Administrator-Session.');
            // redirect to the 'benutzerverwaltung/benutzer bearbeiten'; outgoing point
            redirect('admin/edit_user_mask');
        }
        // --- Main Modification End ---
        // --- Modifications to check if the user is logged in via an global session by CK ---
        else if ($this->samlauthentication->has_linked_account()){
            $message = 'Da Du dich mit Deinem zentralen Account angemeldet hast bist du in meinFHD so lange angemeldet, '.
                       'wie deine globale Session im Browser besteht. Sobald Du Deinen Browser beendest wirst Du aus meinFHD ausgeloggt.';
            $this->message->set(sprintf($message));
            // redirect user to dashboard
            redirect('dashboard/index');
        }
        // --- Modification End ---
        else { // otherwise perform a regular logout
            //$this->message->set(sprintf('Ausgeloggt! (ID: %s)', $this->authentication->user_id()));
            $this->authentication->logout();
            redirect('login');
        }
	}

    /**
     * Opens up and shows the imprint view.
     *
     * @access public
     * @return void
     */
    public function imprint(){
        $this->load->view('app/imprint', $this->data->load());
    }

    /**
     * Generates a new password for the user that corresponds to the submitted
     * email address. If the email does not correspond to an user an error message
     * will be populated.
     * If the input corresponds not to an email address an error message will
     * be populated.
     *
     * @access public
     * @return void
     */
    public function forgot_password(){
        // save the inputted email
        $email = $this->input->post('forgot-email');

        /*
         * Set the form validation to validate if the user input is an email address.
         * Only valid user email addresses will be accepted.
         */
        $this->form_validation->set_rules('forgot-email', 'E-Mail', 'required|valid_email');

        // validate if the submitted user input (email address) corresponds to an existing user
        if($this->form_validation->run()){ // inputted email address is valid
            // check if the email corresponds to an existing user
            if($this->admin_model->query_existing_user_for_email($email)){
                // generate a new password for the corresponding user
                $new_password = $this->adminhelper->passwort_generator();

                // update the user record
                $this->admin_model->update_user_password_for_email($email, $new_password);

                // send an email with the password to the user
                $message = "Hallo, <br/><p>Dein Passwort wurde ge&auml;ndert.<br/> Dein neues Passwort lautet: " . $new_password . "</p><p>Bis bald, bei meinFHD!</p>";
                $this->mailhelper->send_meinfhd_mail($email, 'Passwort geändert', $message);

                // show an feedback message
                $this->message->set(sprintf('Ein neues Passwort wurde dir an die hinterlegte E-Mail-Adresse geschickt!'));
                redirect('app/login');
            }
            else{
                // there is no user with the inputted email address
                $this->message->set(sprintf('Es existiert kein Benutzer mit der E-Mail-Adresse: %s', $email), 'error');
                redirect('app/login'); // reload the login page
            }

        }
        else{ // the inputted email address corresponds not to an existing user
            // construct an error message and show it
            $this->message->set(sprintf('Sorry, aber die von dir eingegebene E-Mail-Adresse entspricht leider nicht dem korrekten Format.'), 'error');
            redirect('app/login');
        }
    }
}
/* End of file App.php */
/* Location: ./application/controllers/App.php */