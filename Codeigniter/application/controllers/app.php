<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.2
 * @package meinFHD\controllers
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class App
 *
 * The app-controller implements the login / logout - routines and deals with all
 * necessary operations to give the user access to. (Ask for an account, reset the password, start sso login etc.)
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
     * Loads the standard/start page of meinfhd (dashboard) for authenticated users.
     *
     * @access public
     * @return void
	 */
	public function index()
	{
		// user is logged in -> set message and redirect to frontpage
		if ($this->agent->is_mobile()) {
//			redirect('dashboard/mobile');
		    redirect('stundenplan/woche');
        }
		else {
//			redirect('dashboard/index');
            redirect('stundenplan/woche');
		}
	}
	
	/**
	 * Login
	 *
	 * .../app/login
	 * .../login
     *
     * Main login function. Loads the login view and controls the login process.
     * Supports the following login processes: Regular authentication, permanent login authentication, shibboleth authentication.
     * Modifications and minor changes by Christian Kundruss (CK), 2012
     *
     * @access public
     * @return void
	 */
	public function login()
	{
        // --- Modification for automatic authentication if an global session exists Begin ---
        // a global session exists and the user has got an linked account -> log him in
        if ($this->samlauthentication->is_authenticated() && $this->samlauthentication->has_linked_account()) {
            redirect('sso/establish_local_session');
        }
        // --- Modification End ---

        // check if the requesting user has got an valid permanent login cookie
        if($this->input->cookie('meinFHD_remember_me')){

            // validate the permanent login cookie value. If the cookie value is valid the user will be validated.
            $this->authentication->validate_permanent_login_cookie_and_authenticate($this->input->cookie('meinFHD_remember_me'));
//            redirect('dashboard/index'); // the session has been established
            redirect('stundenplan/woche');
        }

        // there is no global session & no linked account & no permanent login cookie -> show the local login page when visiting the page
        else {
            // read the post parameters
            $username = $this->input->post('username'); // read the username input
            $password = $this->input->post('password'); // read the corresponding password input
            $permanent_login = $this->input->post('permanent_login'); // read the value of the permanent_login checkbox

            // if we have a value
            if ($username || $password){

                // call the login function from the authentication class -> regular authentication
                if ($this->authentication->login($username, $password))
                {
                    // the login was successful and the permanent login - button was "checked"
                    if($permanent_login == "yes"){

                        // generate and set an permanent login cookie for the visiting user
                        $this->authentication->set_permanent_login_cookie($username);
                    }

                    // user is logged in -> set message and redirect to the frontpage / dashboard
                    $this->message->set(sprintf('Eingeloggt! (ID: %s)', $this->authentication->user_id()));
//                    redirect('dashboard/index');
                    redirect('stundenplan/woche');
                }
                else // the authentication was not successful
                {
                    // something got wrong -> set message and redirect to login page
                    $this->message->set('User oder Passwort falsch!', 'error');
                    redirect('app/login');
                }
            }

        }
        // load user request subview and add the data
        $this->data->add('request_account_mask', $this->_load_request_account_form());
		// if there's no post data, we should show the login screen
		$this->load->view('app/login', $this->data->load());
	}

	/**
	 * Logout
	 *
	 * .../app/logout
	 * .../logout
     *
     * Controls and executes the logout process.
     * Modifications and minor changes by Christian Kundruss
     *
     * @access public
     * @return void
	 */
	public function logout()
	{
        // --- Modification for returning to admin session, if the admin is logged in as another user Begin ---
        if ($this->authentication->is_logged_in_from_admin() == 'TRUE') { // the user is logged in from an admin, change the user data and switch back to admin session
            $email_old_user = $this->authentication->switch_back_to_admin(); // switch back to the admin session and get the email of the user the admin was authenticated as
            // show a message that the return into the admin session was successful
            $this->message->set('Du befindest dich wieder in der Administrator-Session.');

            $this->session->set_flashdata('searchbox', $email_old_user); // set flashdata to search / highlight the changed user
            // redirect to the 'benutzerverwaltung/benutzer bearbeiten'; outgoing point
            redirect(site_url().'admin/edit_user_mask');
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
            $this->message->set(sprintf('Ausgeloggt! (ID: %s)', $this->authentication->user_id()));
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

    /**
     * Loads the request user mask as an subview and returns the view as an string.
     *
     * @access private
     * @return String The request user view as an string.
     */
    private function _load_request_account_form(){

        // get all possible degree programs for the form dropdown
        $this->data->add('studiengaenge', $this->admin_model->get_all_studiengaenge());

        // load the request user mask as an subview (string) and return it
        return $this->load->view('app/partials/request_account', $this->data->load(), TRUE);
    }


    /**
     * Form validation for the user invitation mask.
     * If all inputs are correct a new invitation is going to be saved in the database and an
     * e-mail notification will be sent to the support and the user.
     * The validation method will be called from the app / login page (View request_account.php)
     *
     * @access public
     * @return void
     * @category app/partials/request_account.php
     */
    public function validate_user_invitation_form()
    {
        // set custom delimiter for validation errors
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

        // read the values from actual form
        $form_values = $this->input->post();

        $rules = array();

        // add the form validation rules to the rules array
        $rules[] = $this->adminhelper->get_formvalidation_role();
        $rules[] = $this->adminhelper->get_formvalidation_forename();
        $rules[] = $this->adminhelper->get_formvalidation_lastname();
        $rules[] = $this->adminhelper->get_formvalidation_email();
        $rules[] = $this->adminhelper->get_formvalidation_erstsemestler();

        // set the rules
        $this->form_validation->set_rules($rules);

        // which role was selected?
        $role = $this->input->post('role');

        // depending on the selected role, different validations are necessary

        // role -> student
        if ($role === '5')
        {
            $rules = array();

            // configure the student specific validation rules
            $rules[] = $this->adminhelper->get_formvalidation_matrikelnummer();
            $rules[] = $this->adminhelper->get_formvalidation_startjahr();
            $rules[] = $this->adminhelper->get_formvalidation_semesteranfang();

            $this->form_validation->set_rules($rules);
            // custom rule to validate the selected degree program
            $this->form_validation->set_rules('studiengang', 'Studiengang', 'callback_validate_selected_degree_program');

            // generate actual year for the form value "Startjahr", if "Erstsemestler" was selected
            if (isset($form_values['erstsemestler']) && $form_values['erstsemestler'] == 'accept')
            {
                $form_values['startjahr'] = date("Y");
            }
        }

        // check for input errors
        if($this->form_validation->run() == FALSE) // errors during validation
        {
            $this->message->set('Beim Speichern der Einladung ist ein Fehler aufgetreten.', 'error');
            redirect('app/login');
        }
        else // validation was successful -> all required input fields have got an correct input
        {
            // save new user in the database and send an ma
            $this->admin_model->put_new_user_to_invitation_requests($form_values);

            // send mail to admin, that a new request was saved
        //    $email_reciever = 'meinfhd.medien@fh-duesseldorf.de';
            $email_reciever = 'christian.kundruss@fh-duesseldorf.de'; // TODO LIVE CONFIGURATION USE THE OTHER EMAIL ADDRESS (MAYBE CONFIG FILE??)

            $email_subject = '[meinFHD] Neue Einladungs-Anforderung wurde gespeichert';

            $email_message_body = '<h2>Einladungs-Anfoderung</h2><p>Es wurde eine neue eine Einladungs-Aufforderung f&uuml;r folgenden Benutzer gespeichert: </p>' .
                                  '<p><strong>Vorname: </strong>'. $form_values['forename'].'</br>' .
                                  '<strong>Nachname: </strong>' . $form_values['lastname'] . '</br>' .
                                  '<strong>E-Mail: </strong>' . $form_values['email'] . '</br>' .
                                  '<p>Bitte &uuml;berpr&uuml;fe die vorliegende Anfrage.</p>';

            // call the sendmail method
            $this->mailhelper->send_meinfhd_mail($email_reciever, $email_subject, $email_message_body);

            // send mail to the user, that he has to wait
            $email_reciever = $form_values['email']; // the users email address

            $email_subject = '[meinFHD] Deine Anfrage wurde gespeichert';

            $email_message_body = '<p>Hallo, <br/>Deine Anfrage wurde als Einladung gespeichert. Bitte habe Verst&auml;ndnis, dass die Freischaltung nicht sofort erfolgen kann, ' .
                'sondern erst durch einen Administrator pers&ouml;nlich freigeschaltet werden muss. In der Regel dauert das nicht l&auml;nger als einen Tag.</p>'.
                'Dein meinFHD-Team';

            // call the sendmail method
            $this->mailhelper->send_meinfhd_mail($email_reciever, $email_subject, $email_message_body);

            // reload the startpage (loginpage) with an success message
            $this->message->set('Die Einladungsanforderung wurde erfolgreich abgeschickt! '.
            'Bitte haben Sie Verst&auml;ndnis daf&auml;r, dass Ihre Freischaltung nicht sofort erfolgen kann, '.
            'da Sie durch einen Administrtor pers&ouml;nlich freigeschaltet werden muss. In der Regel dauert dies nicht l&auml;nger als einen Tag.');
            // load the login page another time to show the info
            redirect('app/login');
        }
    }

    /**
     * Custom validation method, to validate the selected degree program in the user invitation form.
     * The validation will return true, if an degree program id rather than 0 is selected by the user.
     *
     * @param $degree_program_id Integer ID of the selected degree program
     * @return bool TRUE if an valid degree program was selected by the user, otherwise the return value is FALSE
     */
    public function validate_selected_degree_program($degree_program_id) {

        // is the selected degree program != 0 than return TRUE
        if($degree_program_id != 0){
            return TRUE;
        }

        return FALSE;
    }
}
/* End of file App.php */
/* Location: ./application/controllers/app.php */