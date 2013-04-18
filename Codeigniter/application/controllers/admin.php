<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Admin
 *
 * The admin class / admin controller provides all admin features, that can be submitted
 * via the admin views from an administrator.
 *
 * @version 0.0.1
 * @package meinFHD\controllers
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Konstantin Voth (KV), <konstantin.voth@fh-duesseldorf.de>
 * @author Frank Gottwald (FG), <frank.gottwald@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Admin extends FHD_Controller {

    /**
     * @var array Array holds all possible permissions.
     */
	private $permissions;

    /**
     * @var array Array holds all possible user roles.
     */
    public $roles;

    /**
     * @var array Array holds all role ids
     */
    private $role_ids;

    /**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
	public function __construct(){
	    parent::__construct();

        // load necessary models
	    $this->load->model('admin_model');

	    // get all roles, role ids and nomenclature
	    $this->roles = $this->admin_model->get_all_roles();
	    $this->permissions = $this->admin_model->get_all_permissions();
	    $this->role_ids = $this->admin_model->get_all_role_ids();
	}

	/**
	 * Admin Interface - Starter Method
	 * Beginning method for the admin interface.
     *
     * @access public
     * @return void
	 */
	public function index()
	{
		$this->create_user_mask();
	}

    /*
     * ==================================================================================
     *                                   User Management start
     * ==================================================================================
     */

    /**
     * User Invitation - Overview
     * Shows / loads the view with all open user requests. Requests can be accepted or deleted.
     *
     * @access public
     * @return void
     */
    public function open_user_requests()
    {
        // get all open user requests
        $this->data->add('user_invitations', $this->admin_model->request_all_invitations());
        // load the view and add the content of the global data array
        $this->load->view('admin/user_requests', $this->data->load());
    }

    /**
     * Creates an user from an invitation request or deletes
     * otherwise the selected / specified invitation request.
     * The id of the invitation request is therefore passed
     * in the $POST-Array.
     *
     * @access public
     * @return void
     */
    public function create_user_from_invitation_requests()
    {
        // get the invitation id from the post array
        $invitation_id = $this->input->post('request_id');

        // get the chosen action from "functions dropdown"
        $user_function = $this->input->post('user_function');

        /*
         * perform the action, that was chosen by the user
         * 0: create user
         * 1: delete request
         */
        switch ($user_function)
        {
            case '0':
                // create a new user from the invitation
                $new_user_info = $this->admin_model->save_new_user_from_invitation($invitation_id);
                // send mail to the accepted user with login information
                $this->mailhelper->send_meinfhd_mail(
                    $new_user_info['Emailadresse'], "[meinFHD] Herzlich Willkommen bei meinFHD",
                    "Hallo ". $new_user_info['Vorname'] . " " . $new_user_info['Nachname'] . ",<br/><br/>" .
                        "<p>Deine Benutzeranfrage wurde akzeptiert.</br>".
                        "Der Anmeldename ist Deine Emailadresse und das Passwort lautet: {$new_user_info['Passwort']}<br/><br/></p>".
                        "Dein meinFHD-Team"
                );
                $this->message->set('Der User wurde von der Einladungsliste erstellt.', 'success');
                redirect(site_url().'admin/open_user_requests');
                break;
            case '1':
                // delete the user invitation
                $deleted_request_info = $this->admin_model->delete_invitation($invitation_id);
                // send mail that the invitation was not accepted
                $this->mailhelper->send_meinfhd_mail(
                    $deleted_request_info['Emailadresse'],"[meinFHD] Deine Zugangsanforderung f�r meinFHD wurde abgelehnt",
                    "Hallo ". $deleted_request_info['Vorname'] . " " . $deleted_request_info['Nachname'] . ",<br/></br/>" .
                        "<p>Deine Benutzeranfrage wurde nicht akzeptiert.<br/>".
                        "Bei R&uuml;ckfragen wende dich bitte pers&ouml;nlich an das Support-Team!<br/><br/></p>".
                        "Dein meinFHD-Team"
                );
                $this->message->set('Der User wurde von der Einladungsliste gel&ouml;scht.', 'error');
                redirect(site_url().'admin/open_user_requests');
                break;
            default:
                break;
        }
    }

	/**
	* Create User - Form
	* Loads the create user form, that is displayed in the admin backend.
	*
    * @access public
    * @return void
	*/
	public function create_user_mask()
	{
		// get all possible roles for the form dropdown
		$this->data->add('all_roles', $this->admin_model->get_all_roles_for_dropdown());

        // load the view with the specified data
		$this->load->view('admin/user_add', $this->data->load());
	}

    /**
     * Form validation for creating a new user. The inputs will be checked for correctness.
     * If everything is correct, the new user will be created and an email with the login
     * information will be sent to the entered email address.
     *
     * @access public
     * @return void
     */
    public function validate_create_user_mask()
    {
        // set the custom error delimiters for displaying the form validation errors
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

        // specifiy the form validation rules
        $rules = array();

        $rules[] = $this->adminhelper->get_formvalidation_role();
        $rules[] = $this->adminhelper->get_formvalidation_loginname();
        $rules[] = $this->adminhelper->get_formvalidation_email();
        $rules[] = $this->adminhelper->get_formvalidation_forename();
        $rules[] = $this->adminhelper->get_formvalidation_lastname();

        $this->form_validation->set_rules($rules);

        // check for correctness
        if($this->form_validation->run() == FALSE)
        {
            // there occured some errors during the validation -> display the mask with the errors
            $this->create_user_mask();
        }
        else // validation was correct
        {
            // save the input / form data
            $form_data = $this->input->post();
            $form_data['startjahr'] = date("Y"); // add the 'studienbeginnjahr' to the input array (because there are no different form elements depending on the selected role)

            // create the new user, send him an email with the login information
            $this->_create_user($form_data);

            // display a message
            $this->message->set('Der Benutzer wurde erfolgreich erstellt!', 'success');
            redirect(site_url().'admin/create_user_mask');
        }
    }

    /**
     * Creates / adds a new user with the provided information to the database.
     *
     * Therefore it gets the input data, generates a password, routes to the model function to save
     * the user in the DB and sends an email to the created user with his login name and password.
     *
     * @access private
     * @return void
     */
    private function _create_user($input_data)
    {
        // generate password
        $password = $this->adminhelper->passwort_generator();

        // save new user in db
        $this->admin_model->save_new_user($input_data, $password);

        // send e-mail to the new user
        $this->mailhelper->send_meinfhd_mail($input_data['email'], "[meinFHD] Dein Benutzeraccount wurde erstellt","<p>Hallo,<br/><br/>dein Benutzeraccount wurde erfolgreich".
            "erstellt.<br/>Du kannst dich mit folgenden Benutzerdaten anmelden:<br/>".
            "<strong>Username:</strong> {$input_data['loginname']}<br/><strong>Passwort:</string> {$password}</p><p>Das Passwort wurde automatisch generiert ".
            "und sollte aus Sicherheitsgr&uuml;nden nach dem ersten Login manuell von dir ge&auml;ndert werden.</p><br/>Dein meinFHD-Team");
    }

    /**
	* Shows the edit user - form
	*
	* @category user_edit.php
    * @access public
    * @return void
	*/
	public function edit_user_mask()
	{
		// get all possible roles for the form dropdown
		$this->data->add('all_roles', $this->admin_model->get_all_roles_for_dropdown());
        // load the view
		$this->load->view('admin/user_edit', $this->data->load());
	}

    /**
     * Decides which function was selected and routes to the associated method.
     * The different user selected functions are associated with the following numbers:
     * 0: Save; 1: Reset password; 2: Reset Semesterplan; 3: Login as selected user;
     * 4: Delete user. These parameters need to be passed from the view, when the
     * form is submitted. The parameters need to be the option number from
     * the dropdown in the user_edit.php view.
     *
     * @category user_edit.php
     * @access public
     * @return void
     */
    public function validate_edit_user_form()
    {
        /*
         * Get the choosen action from "functions dropdown".
         * 0: save, 1: pw reset, 2: semesterplan reset, 3: log-in as 4: delte user
         */
        $user_function = $this->input->post('user_function');

        switch ($user_function) {

            case '0':
                $this->_validate_edits();
                break;
            case '1':
                $this->_reset_pw();
                break;
            case '2':
                $this->_reset_studienplan();
                break;
            case '3':
                $this->_login_as_user();
                break;
            case '4':
                $this->_delete_user();
                break;
            default:
                $this->message->set('Bei der Verarbeitung der ausgew&auml;hlten Funktion ist ein Fehler aufgetreten.', 'error');
                redirect(site_url().'/admin/edit_user_mask');
                break;
        }
    }

	/**
	 * Validates the made changes, if they should saved.
     * If the changes were correct, they will be saved in the database. Otherwise
     * the view will be repopulated and the error messages will be highlighted.
	 *
	 * @category user_edit.php
     * @access private
     * @return void
	 */
	private function _validate_edits()
	{
		// set custom delimiter for validation errors
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$rules = array();

		// values, from actual form
		$new_form_values = $this->input->post();

		// current user data in db
		$current_user_data = $this->admin_model->get_user_by_id($this->input->post('user_id'));

		// check if current value is different from the value in db
		if ($current_user_data['LoginName'] != $new_form_values['loginname'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_loginname();
		}

		// same procedure for the other form inputs
		if ($current_user_data['Email'] != $new_form_values['email'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_email();
		}

		/*
		 * Even if these fields do not need any validation rules, they have to be set, otherwise
		 * they are not available after the ->run() method.
		 */
		if ($current_user_data['Vorname'] != $new_form_values['forename'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_forename();
		}

		if ($current_user_data['Nachname'] != $new_form_values['lastname'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_lastname();
		}

        // set the validation rules
        $this->form_validation->set_rules($rules);

		// check for (in)correctness
		if($this->form_validation->run() == FALSE)
		{
            $this->session->set_flashdata('searchbox', $new_form_values['email']);
            // call edit user mask again
            redirect(site_url().'admin/edit_user_mask');
		}
		else
		{
			// save in db
			$this->_save_user_changes();
            // display a message
			$this->message->set('Der User wurde erfolgreich bearbeitet.', 'success');
			$this->session->set_flashdata('searchbox', $new_form_values['email']);
            // redirect back to the edit user mask
			redirect(site_url().'admin/edit_user_mask');
		}
	}

    /**
     * Saves the user changes that where made by an admin / inputted in the form for the specified user.
     *
     * @category user_edit.php
     * @access private
     * @return void
     */
    private function _save_user_changes()
    {
        // get the id of the changed user via post
        $user_id = $this->input->post('user_id');

        // create the update array -> new user information
        $data = array(
            'LoginName'					=> $this->input->post('loginname'),
            'Vorname'					=> $this->input->post('forename'),
            'Nachname'					=> $this->input->post('lastname'),
            'Email'						=> $this->input->post('email')
        );
        // update the user dataset
        $this->admin_model->update_user($user_id, $data);
    }

	/**
	 * Resets the login password for the user, whose user_id that is passed in the
     * $POST-Array. The new password will be send in an email to the user.
	 *
	 * @category user_edit.php
     * @access private
     * @return void
	 */
	private function _reset_pw()
	{
		// values, from actual form inputs
		$new_form_values = $this->input->post();

        // generate a new password
        $password = $this->adminhelper->passwort_generator();

        // create the user update array with the md5 hashed password
		$data = array(
				'Passwort' => md5($password),
			);

		$this->admin_model->update_user($new_form_values['user_id'], $data);

		// send email with the new password to the user
        $email_reciever = $new_form_values['email'];
        $email_subject = '[meinFHD] Passwort zur�ckgesetzt';
        $email_body = "Hallo,<br/><br/>dein Passwort wurde durch einen Administrator zur&uuml;ckgesetzt.<p>Dein Passwort lautet nun: {$password}</p>Dein meinFHD-Team";

        $this->mailhelper->send_meinfhd_mail($email_reciever, $email_subject, $email_body);

        // display a message and redirect the user back to the edit user mask
		$this->message->set('Das Passwort wurde erfolgreich zur&uuml;ckgesetzt.', 'success');
        // set flashdata to search / highlight the changed user
        $this->session->set_flashdata('searchbox', $new_form_values['email']);
        redirect(site_url().'admin/edit_user_mask');
	}

	/**
	 * Resets the studienplan of the user, whose user_id
     * is passed in the $POST-Array. Method is used while an admin
     * edits manually some user information. Afterwards the edit user mask
     * is opened again and searches automatically for the dataset of
     * the changed user.
	 *
	 * @category user_edit.php
     * @access private
     * @return void
	 */
	private function _reset_studienplan()
	{
		// get the id of which user the semesterplan should be restored
		$new_form_values = $this->input->post();
        // reset the semesterplan
		$this->admin_model->reconstruct_semesterplan($new_form_values['user_id']);
        // display a message and redirect back to the edit user mask
		$this->message->set('Der Studienplan wurde erfolgreich zur&uuml;ckgesetzt.', 'success');
        // set flashdata to search / highlight the changed user
        $this->session->set_flashdata('searchbox', $new_form_values['email']);
        redirect(site_url().'admin/edit_user_mask');
	}

	/**
	 * <p>
     * Deletes an user by his id. The id is submitted via POST.
     * If the local account is linked to an global
     * user id, the global uid will be add to the 'shibboleth blacklist'.
     * After the user is successfully deleted, the edit user mask will be
     * opened again.
     * </p>
     * @access private
     * @return void
	 */
	private function _delete_user()
	{
        // get the id of the user that should be deleted
		$user_id = $this->input->post('user_id');

        // account blacklisting for single sign on authentication
        // if the users account is already linked to an global identity -> blacklist the global userid before deleting his identity
        $linked_userdata = $this->admin_model->is_user_linked($user_id);
        if ($linked_userdata) { // the user is linked
            // add him to the blacklist
            $this->admin_model->add_user_to_blacklist($linked_userdata);
        }

        // delete the user from the database
		$this->admin_model->model_delete_user($user_id);
        // display a success message and return to the delete user mask
		$this->message->set('Der Benutzer wurde erfolreich gel&ouml;scht.', 'success');
		redirect(site_url().'admin/edit_user_mask');
	}

    /**
     * Authenticates the admin under the account of the selected user.
     *
     * @authro Christian Kunndruss (c) 2012
     * @access private
     * @return void
     */
    private function _login_as_user() {
        $user_information = $this->input->post(); // get the whole information of the selected user

        if($this->authentication->login_as_user($user_information)) { // authentication was successful
            $message_body = 'Eingeloggt als ' . $this->authentication->get_name() .  ' (User-ID:  ' . $this->authentication->user_id() . ')';
            // print a message to get to know as who you are logged in, and to show that the authentication was successful
            $this->message->set(sprintf($message_body));

            // redirect the user to the dashboard
            redirect(site_url().'dashboard/index');
        }
    }

    /*
     * =============================================================================================================
     *   Comprehensive AJAX methods for getting only the user rows for editing, that matches the filter criteria
     * =============================================================================================================
     */

	/**
	 * Method for rendering the needed search-response and HTML Markup. It returns
     * all users, that match the selected role and the inputted searchletter. In
     * addition it renders different row views in dependency of the $GET-Parameter
     * 'subviewtype'.
     * The following subview types are configured:
     * - 'user_edit' -> Renders / displays single edit user user rows
     * - 'role_edit' -> Renders / displays sinlge edit user role rows
     *
     * Method is made for the use via ajax. The result (all single user views) will
     * be echoed out.
	 *
     * @access public
     * @return void|echo The result string ist echoed out directly.
	 */
	public function ajax_show_user()
	{
        // variable to hold the result string
		$result = '';

		// get the selected role and the search input
		$role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

        // get the passed subviewtype that should be loaded
        $subviewtype = $this->input->get('subviewtype');

		// if nothing is set, query would response all users, so lets prevent this
		if ( empty($role_id) && empty($searchletter) )
		{
            // no users are going to be displayed
			$result = 'Kein Ergebnis';
		}
		else // there is some input
		{
            // query the database for a result
			$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);

			// get the user with needed html markup and add it to the result string
			foreach ($q as $key => $value)
			{
                // add the role id to the value array, because in the view we need the role id to provide the role specific functions
                $value['role_id'] = $role_id;

                // load the right (single) subview according the subviewtype-parameter
                if ($subviewtype == 'edit_user_information'){ // the user edit view should be loaded
				    $result .= $this->load->view('admin/partials/user_edit_single_form', $value, TRUE);
                }
			}
		}

        // if the result string is not empty display the result, otherwise display 'Kein Ergebnis'
        if(empty($result)){
            echo 'Kein Ergebnis';
        }
        else {
            echo $result;
        }
	}

	/**
	 * Returns the sum of all matched (searched) users. Therefore the searchletter
     * are passed in the $GET-Array.
     * Method is usually called via ajax. The result is going
     * to be echoed out.
     *
	 * @access public
     * @return void|echo The result / user count is directly echoed / printed out.
	 */
	public function ajax_show_user_count()
	{
        // variable to hold the result string
		$result = '';

        // get the selected role and the search input
        $role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

		// if there is nothing set, the query would response all users, so lets prevent this
		if ( empty($role_id) && empty($searchletter) )
		{
			$result = '0';
		}
		else
		{
            // get the matching users and calculate the count that should be displayed
			$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);
			$result = count($q);
		}

        // echo the result -> equal to return, because the method is called via ajax
		echo $result;
	}

    /*
     * ==================================================================================
     *                  Privilege administration (Rechteverwaltung) start
     * ==================================================================================
     */

    /**
     * Edit Permissions - Overview
     * Shows / displays all permissions and roles and gives an admin the possibility
     * to edit those.
     *
     * @access public
     * @return void
     */
    public function show_role_permissions(){

        /*
         * Iterate through all role ids and save them in an nested array.
         * -> Per role id one array with all corresponding permissions (array([roleid] => array([index] => permissions)...).
         */
        foreach ($this->role_ids as $rid) {
            /*
             * Get the permissions for a specified role id.
             * It is possible that a role has no permissions. Therefore the array is going to be initialized with null. Index 0 of the array
             * will be empty.
             */
            $single_role_permissions = $this->admin_model->get_all_role_permissions($rid);
            // if there are some permissions for the role? if not -> do nothing
            if($single_role_permissions){

                foreach ($single_role_permissions as $rp){

                    $all_role_permissions[$rid][]= $rp;
                }
            }
        }

        /*
         * Create an array, that is going to be used for the data output. -> prepare data to be easily displayed in the view
         * -> Simple array with all used roles and their assigned permissions. The following index encryption is used; index % 5 == 0 is the RoleId.
         */

        // iterate through all permissions
        foreach ($this->permissions as $p) {

            $data['tableviewData'][] = $p->BerechtigungID; // save the permission ID

            // iterate through all permissions for every role
            foreach ($this->roles as $r){

                // if there are values in the array Role_permissions[RoleID] (Index 0 is an empty field)
                if(array_key_exists('1', $all_role_permissions[$r->RolleID])){

                    // if the array, that matches the role contains the permission id
                    if(in_array($p->BerechtigungID, $all_role_permissions[$r->RolleID])){
                        // save the ID
                        $data['tableviewData'][] = $p->BerechtigungID;
                    }
                    else {
                        // the permission_id does not correspond to the role - save x
                        $data['tableviewData'][] = 'x';

                    }
                }
                else {
                    // there are no permissions for the role - save 4 times the x
                    $data['tableviewData'][] = 'x';
                }
            }
        }

        // add the data to the global data(table) array
        $this->data->add('tableviewData', $data['tableviewData']);

        // save the data, that is needed for the view and add them to the global data array
        $this->data->add('roleCounter', $this->admin_model->count_roles());
        $this->data->add('roles', $this->roles);
        $this->data->add('permissions', $this->permissions);
        // load the view and add the global data array content
        $this->load->view('admin/permissions_edit', $this->data->load());
    }

    /**
     * Saves all permission edits, that were made in the
     * edit permissions view / form
     *
     * @access public
     * @return void
     */
    public function save_permissions(){

        // delete the old (existing) role_permission configuration
        $this->admin_model->delete_role_permissions();

        // iterate through each permission
        foreach($this->permissions as $p){
            // iterate through each role
            foreach($this->role_ids as $r){
                // if there are entries for the role-permission-combination -> save the,
                if($this->input->post(($p->BerechtigungID).$r)){

                    $rp['RolleID'] = $r; // save / set the role id
                    $rp['BerechtigungID'] = $p->BerechtigungID; // save the assigned permission id

                    // save the permission changes
                    $this->admin_model->update_role_permissions($rp);
                }
            }
        }

        // set a message and reload the view
        $this->message->set('DIe &Auml;nderungen wurden erfolgreich gespeichert.', 'success');
        redirect(site_url().'/admin/show_role_permissions');
    }

    /*
     * ==================================================================================
     *                  Privilege administration (Rechteverwaltung) end
     * ==================================================================================
     */

    /*
     * ==================================================================================
     *              (User) Role administration (Benutzerrollenverwaltung) start
     * ==================================================================================
     */

    /**
     * Shows all users and their associated roles.
     *
     * @category user_edit_roles.php
     * @access public
     * @return void
     */
    public function edit_roles_mask()
    {
        // get all users
        $this->data->add('all_user', $this->admin_model->get_all_user_with_roles());
        // get all roles
        $this->data->add('all_roles', $this->admin_model->get_all_roles_for_dropdown());
        // load the view
        $this->load->view('admin/user_edit_roles', $this->data->load());
    }


    /**
	 * Changes the role for the selected user.
	 *
	 * @category user_edit_roles.php
     * @access public
     * @return void
	 */
	public function changeroles_user()
	{
        // get the form input from the POST-array
		$formdata = $this->input->post();

		// clear saves for the actual user
		$this->admin_model->clear_userroles($formdata['user_id']);

		// set new settings
		if (isset($formdata['cb_userroles']))
		{
			foreach ($formdata['cb_userroles'] as $role)
			{
				$this->admin_model->save_userrole($formdata['user_id'], $role);
			}
		}

        // reload the edit roles mask after the changes have been saved in the databse
		redirect('/admin/edit_roles_mask/', 'refresh');
	}

    /*
     * ==================================================================================
     *           (User) Role administration (Benutzerrollenverwaltung) end
     * ==================================================================================
     */

    /*
     * ===================================================
     * Degree program management after this comment.
     * ===================================================
     */

    // ==== adding a new degree program ====

	/**
	 * Show the add degree program view with empty input-fields.
	 * Method is usually called from the main menu.
	 *
     * @access public
     * @return void
	 */
	public function degree_program_add(){

	    // get all degree programs for the view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    $this->load->view('admin/degree_program_add', $this->data->load());

	}

	/**
	 * Form validation for the add degree program form.
	 *
     * @access public
     * @return void
	 */
	public function validate_new_created_degree_program(){

        // set the form validation rules
	    $this->form_validation->set_rules('Pruefungsordnung', 'Pruefungsordnung fehlt', 'required|numeric');
	    $this->form_validation->set_rules('StudiengangName', 'Name für den Studiengang fehlt', 'required');
	    $this->form_validation->set_rules('StudiengangAbkuerzung', 'Abkürzung fehlt', 'required');
	    $this->form_validation->set_rules('Regelsemester', 'Regelsemester fehlt', 'required|numeric');
	    $this->form_validation->set_rules('Creditpoints', 'Creditpoints fehlen', 'required|numeric');
	    $this->form_validation->set_rules('Beschreibung', 'Beschreibung fehlt', 'required');

        // run the form validation
	    if ($this->form_validation->run() == FALSE) { // validation was not successful
			// reload the view
			$this->degree_program_add();
	    }
        else { // form validation was successful
			$this->save_new_created_degree_program(); // save the newly created degree program
	    }
	}

	/**
	 * Saves a new degree program into the database wit the given values via POST.
     *
     * @access public
     * @return void
     * TODO check if the given name of the degree programm is already used -> maybe it does not need to be checked
	 */
	public function save_new_created_degree_program(){

        // specify the needed input attributes
	    $insert_fields = array(
			    'Pruefungsordnung',
			    'StudiengangName',
			    'StudiengangAbkuerzung',
			    'Regelsemester',
			    'Creditpoints',
			    'CreditpointsMin',
			    'FachbereichID',
			    'Beschreibung'
			    );

	    // get the data from the form-submission corresponding to the defined input fields above
	    for($i = 0; $i < count($insert_fields); $i++){

		    if($_POST[$insert_fields[$i]] != null){

			        $insert_new_dp[$insert_fields[$i]] = $_POST[$insert_fields[$i]];
		    }
	    }

	    // save new record - model returns id of new created degree program
		$new_id = 0;
	    $new_id = $this->admin_model->create_new_degree_program($insert_new_dp);

	    // redirect to degree program view with active dropdown (i.e. the new created)
		$this->session->set_flashdata('reload', $new_id); // pass new id via flashdata
        redirect('admin/degree_program_edit');
	}

    // ==== copying and deleting a degree program ====

	/**
	 * Helper method called when degree programm should be deleted.
	 * Routes to the degree_program_copy_delete-method and passes a boolean that
	 * specifies that the method has been called from the main-menue.
     *
     * @access public
     * @return void
	 */
	public function degree_program_delete(){
	    $this->_degree_program_copy_delete(TRUE);
	}

	/**
	 * Helper method called when degree program should be copied.
	 * Routes to the degree_program_copy_delete-method and passes a boolean that
	 * specifies that the method has been called from the main menue.
     *
     * @access public
     * @return void
	 */
	public function degree_program_copy(){
	    $this->_degree_program_copy_delete(FALSE);
	}

	/**
	 * Shows a list of all degree programs to give the opportunity to delete/copy them.
	 * @param $delete boolean Flag to use the same view for copying and deleting.
     * @access private
     * @return void
	 */
	private function _degree_program_copy_delete($delete){

	    // get all degree programs for the view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    $this->data->add('delete', $delete);

	    $this->load->view('admin/degree_program_copy_delete', $this->data->load());
	}

	/**
	 * Deletes a whole degree program.
	 * Called from within view after OK button has been submitted.
     *
     * @access public
     * return void
	 */
	public function delete_degree_program() {

        // get the id of the degree program that should be deleted and delete it from the database
	    $delete_id = $this->input->post('degree_program_id');
	    $this->admin_model->delete_degree_program($delete_id);

	    // show view again
	    redirect('admin/degree_program_delete');
	}

	/**
	 * Copies a whole degree program - called when the ok button is submitted.
	 * Called from within view after the OK button is submitted.
     *
     * @access public
     * @return void
	 */
	public function copy_degree_program() {

        // get the id of the degree program that should be copied
	    $copy_id = $this->input->post('degree_program_id');

        // copy that degree program >> returns id of copied dp
        $new_id = 0;
		$new_id = $this->admin_model->copy_degree_program($copy_id);

		// pass new id via flashdata
		$this->session->set_flashdata('reload', $new_id);
	    // call degree-program-edit view of that course
	    redirect('admin/degree_program_edit');
	}

    // ==== editing a degree program ====
    // TODO check for deprecated stuf, test functionality and behaviour. Is everything as expected?

	/**
	 * Shows the edit degree program view with dropdown.
	 * If there is something passed (via flashdata) the view loads a specific degree-program,
	 * if not: user will see the empty view.
     *
     * @access public
     * @return void
	 */
	public function degree_program_edit(){

        // is there something submitted via flashdata?
		$reload = $this->session->flashdata('reload');

		if(!$reload){
			$reload = 0; // if nothing has been passed
		}

	    // get all degree programs for filter-view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    // set degree_program_id to 0 - indicates, that view has been loaded directly from controller
	    // no autoreload without validation
	    $this->data->add('degree_program_id_automatic_reload', $reload);

	    $this->load->view('admin/degree_program_edit', $this->data->load());
	}

	/**
     * Displays / echoes an table with all courses, which belong to the given degree program id.
	 * An div with the degree-program-table for a passed degree-program-id will be "returned".
	 * The degree program id can be passed via GET >> $this->input->get('degree_program_id'),
	 * or it can be passed as an parameter, when the method is called from another controller method(deleting, adding one course).
     * Method is designed to be called via ajax. So the result will be echoed.
     *
     * @access public
     * @param $degree_program_id int ID of the degree program, where the courses should be displayed for. If no parameter is passed, the default value is 0.
     * @return void
     * @TODO check for deprecated (outcommented) stuff, test functionality
	 */
	public function ajax_show_courses_of_degree_program($degree_program_id = '0'){

	    // if parameter is 0 - method called from within view
	    if($degree_program_id === '0'){
			// get submitted data - AJAX
			$degree_program_chosen_id = $this->input->get('degree_program_id');
	    }
        else { // otherwise method called from within controller (delete/add course) an id is passed
            $degree_program_chosen_id = $degree_program_id;
	    }

        // get all courses of the specified degree program
	    $courses_of_single_degree_program = array();
	    $courses_of_single_degree_program = $this->admin_model->get_degree_program_courses($degree_program_chosen_id);
	    $details_of_single_degree_program = $this->admin_model->get_degree_program_details_as_row($degree_program_chosen_id);

	    // get number of semesters and prepare data for dropdown
	    $regelsemester = $details_of_single_degree_program->Regelsemester;

	    for($i = 0; $i < $regelsemester; $i++){
//		if($i != 0){
			$semester_dropdown_options[$i] = $i+1;
//		} else {
//			$semester_dropdown_options[$i] = '';
//		}
	    }

	    // degree_program_id is already needed here to generate unique ids for delete-buttons
	    $data['dp_id'] = $degree_program_chosen_id;
	    $data['semester_dropdown'] = $semester_dropdown_options;

	    // fill first element of object-array with default-values -
	    // >> necessary because first line of table view should be
	    // for creation of new courses
	    // only KursID is needed, because creation of input-fields grabs
	    // KursID to generate unique names => array[0]
//	    $courses_of_single_degree_program[0]['KursID'] = '0';
//	    $courses_of_single_degree_program[0]['Kursname'] = '';
//	    $courses_of_single_degree_program[0]['kurs_kurz'] = '';
//	    $courses_of_single_degree_program[0]['Creditpoints'] = '';
//	    $courses_of_single_degree_program[0]['SWS_Vorlesung'] = '';
//	    $courses_of_single_degree_program[0]['SWS_Uebung'] = '';
//	    $courses_of_single_degree_program[0]['SWS_Praktikum'] = '';
//	    $courses_of_single_degree_program[0]['SWS_Projekt'] = '';
//	    $courses_of_single_degree_program[0]['SWS_Seminar'] = '';
//	    $courses_of_single_degree_program[0]['SWS_SeminarUnterricht'] = '';
//	    $courses_of_single_degree_program[0]['Semester'] = '0';
//	    $courses_of_single_degree_program[0]['Beschreibung'] = '';
//	    // if there will be more exam-types added: this is the place to add them too!!
//	    $courses_of_single_degree_program[0]['pruefungstyp_1'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_2'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_3'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_4'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_5'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_6'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_7'] = FALSE;
//	    $courses_of_single_degree_program[0]['pruefungstyp_8'] = FALSE;


	    // building a first line to save a new course to db
	    $data['new_course'] = $this->load->view('admin/partials/degree_program_coursetable_row_first', $data, TRUE);

	    $rows = array(); // init

	    // if there are courses - otherwise only course-details has been created
	    if($courses_of_single_degree_program){

			//for each record - print out table-row with form-fields
			foreach($courses_of_single_degree_program as $sd){

					// build a table-row for each course
				$data['KursID'] = $sd['KursID'];
				$data['Kursname'] = $sd['Kursname'];
				$data['kurs_kurz'] = $sd['kurs_kurz'];
				$data['Creditpoints'] = $sd['Creditpoints'];
				$data['SWS_Vorlesung'] = $sd['SWS_Vorlesung'];
				$data['SWS_Uebung'] = $sd['SWS_Uebung'];
				$data['SWS_Praktikum'] = $sd['SWS_Praktikum'];
				$data['SWS_Projekt'] = $sd['SWS_Projekt'];
				$data['SWS_Seminar'] = $sd['SWS_Seminar'];
				$data['SWS_SeminarUnterricht'] = $sd['SWS_SeminarUnterricht'];
				$data['SemesterDropdown'] = $semester_dropdown_options;	// array holding all dropdown-options
				$data['Semester'] = $sd['Semester'];
				$data['Beschreibung'] = $sd['Beschreibung'];
				// if there will be more exam-types added: this is the place to add them too!!
				$data['pruefungstyp_1'] = (($sd['pruefungstyp_1'] == '1') ? TRUE : FALSE); // convert data (1/0) to boolean
				$data['pruefungstyp_2'] = (($sd['pruefungstyp_2'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_3'] = (($sd['pruefungstyp_3'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_4'] = (($sd['pruefungstyp_4'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_5'] = (($sd['pruefungstyp_5'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_6'] = (($sd['pruefungstyp_6'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_7'] = (($sd['pruefungstyp_7'] == '1') ? TRUE : FALSE);
				$data['pruefungstyp_8'] = (($sd['pruefungstyp_8'] == '1') ? TRUE : FALSE);

				// array holding all rows
				$rows[] = $this->load->view('admin/partials/degree_program_coursetable_row', $data, TRUE);
			}
	    }

	    // make data available in view
	    $data['dp_details'] = $details_of_single_degree_program;
	    $data['dp_course_rows'] = $rows;
//	    $data['course_tablehead'] = $this->load->view('admin/partials/degree_program_coursetable_head', '', TRUE);

	    // prepare the result content
	    $result = '';
	    $result .= $this->load->view('admin/partials/degree_program_details', $data, TRUE);
	    $result .= $this->load->view('admin/partials/degree_program_coursetable_content', $data, TRUE);

        // return(echo) the result
	    echo $result;
	}

	/**
	 * validates if all changes that've been made are correct
	 * - PO - required, numeric
	 * - Name - required
	 * - Abk. - required
	 * - Regelsemester - required, numeric
	 * - CP - required, numeric
	 */
	public function validate_degree_program_details_changes(){

	    // TODO??? PO-Name-Abk-Kombi must be UNIQUE

	    // get degree_program_id
	    $dp_id = $this->input->post('degree_program_id');

	    $this->form_validation->set_rules(
		    $dp_id.'Pruefungsordnung', 'Pruefungsordnung fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    $dp_id.'StudiengangName', 'Name für den Studiengang fehlt', 'required');
	    $this->form_validation->set_rules(
		    $dp_id.'StudiengangAbkuerzung', 'Abkürzung fehlt', 'required');
	    $this->form_validation->set_rules(
		    $dp_id.'Regelsemester', 'Regelsemester fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    $dp_id.'Creditpoints', 'Creditpoints fehlen', 'required|numeric');
	    $this->form_validation->set_rules(
		    $dp_id.'Beschreibung', 'Beschreibung fehlt', 'required');

	    if ($this->form_validation->run() == FALSE) {
			// reload view
			$this->session->set_flashdata('reload', $dp_id);
			redirect('admin/degree_program_edit');
	    } else {
			$this->save_degree_program_details_changes();
	    }
	}

	/**
	 * Validates if changes match validation-criteria
	 */
	public function validate_degree_program_course_changes(){

	    // get all course-ids belonging to a specified degree program
	    $dp_id = $this->input->post('degree_program_id');
	    $degree_program_course_ids = $this->admin_model->get_degree_program_course_ids($dp_id);

	    foreach($degree_program_course_ids as $id){
			// run through all ids and generate id-specific validation-rules
			$this->form_validation->set_rules(
				$id->KursID.'Kursname', 'Kursname fehlt - ID: '.$id->KursID, 'required');
			$this->form_validation->set_rules(
				$id->KursID.'kurs_kurz', 'Kurzbezeichnung fehlt - ID: '.$id->KursID, 'required');
			$this->form_validation->set_rules(
				$id->KursID.'Creditpoints', 'Creditpoints fehlen oder nicht numerisch - ID: '.$id->KursID, 'required|numeric');
	    }

	    if ($this->form_validation->run() == FALSE) {
			// reload view
			$this->session->set_flashdata('reload', $dp_id);
			$this->degree_program_edit();
		} else {
			$this->save_degree_program_course_changes();
	    }
	}



	/**
	 * Saving all values after submit button has been clicked.
	 */
	public function save_degree_program_course_changes(){
		// getting id from post
		$dp_id = 0; // it shouldn't happen that no id comes from post.. but..
		$dp_id = $this->input->post('degree_program_id');

//		echo '<pre>';
//		print_r($this->input->post());
//		echo '</pre>';

	    // build an array, containing all keys that have to be updated in db
	    $update_fields = array(
			    'Kursname',
			    'kurs_kurz',
			    'Creditpoints',
			    'SWS_Vorlesung',
			    'SWS_Uebung',
			    'SWS_Praktikum',
			    'SWS_Projekt',
			    'SWS_Seminar',
			    'SWS_SeminarUnterricht',
			    'Semester',
			    'Beschreibung');

	    // provide incoming exam-types in array
	    $update_checkboxes = array(
			    'ext_1',
			    'ext_2',
			    'ext_3',
			    'ext_4',
			    'ext_5',
			    'ext_6',
			    'ext_7',
			    'ext_8'
	    );

	    // get ids of a single studiengang - specified by id
	    $dp_ids = $this->admin_model->get_degree_program_course_ids($dp_id);

	    // get values of nested object - KursIds - to run through the ids and update records
	    foreach ($dp_ids as $si){
			$dp_id_values[] = $si->KursID;
	    }

	    // run through all course-ids that belong to a single degree-program, build data-array for updating records in db
	    // AND update data for every id
	    foreach($dp_id_values as $id){
			$update_dp_data = array(); // init
			// produces an array holding db-keys as keys and data as values
			for ($i = 0; $i < count($update_fields); $i++){
				// data from dropdown represents position in array - has to be mapped to real ID (+1)
				switch ($update_fields[$i]) {
					case 'Semester' : $update_dp_data[$update_fields[$i]] = (($this->input->post($id.$update_fields[$i]) + 1) ); break;
					default : $update_dp_data[$update_fields[$i]] = $this->input->post($id.$update_fields[$i]); break;
				}
			}

			// call function in model to update records
			$this->admin_model->update_degree_program_courses($update_dp_data, $id);

			$exam_cb_data = array(); // init
			$tmp_exam_cb_data = array(); // init

			// handle checkboxes
			// !! have to be handled different, because not every field is submitted,
			// but only the ones that are active at the moment
			// >> run through all possible updates and
			foreach ($update_checkboxes as $value) {
				// check if the box is checked
				if($this->input->post($id.$value) === '1'){
					// in case it is checked, extract exam-type and store data in array
					$split = explode('_', $value); // second value is exam-type-id
					$tmp_exam_cb_data['KursID'] = $id;
					$tmp_exam_cb_data['PruefungstypID'] = $split[1];

					// both information are stored into another array
					// this array can be iterated to fetch all checked boxes
					$exam_cb_data[] = $tmp_exam_cb_data;
				}
			}

			// save cb-data to db - passed array contains all checkboxes that have to be stored
			$this->admin_model->save_exam_types_for_course($exam_cb_data, $id);

	    }

	    // show degree-program-edit-view again with activated dp_id
		$this->session->set_flashdata('reload', $dp_id);
	    redirect('admin/degree_program_edit');
	}


	/**
	 * Save all fields (degree program) - getting data from POST
	 */
	public function save_degree_program_details_changes(){
	    $updateFields = array(
			'Pruefungsordnung',
			'StudiengangName',
			'StudiengangAbkuerzung',
			'Regelsemester',
			'Creditpoints',
			'Beschreibung'
	    );

	    // get value via hidden field
	    $dp_id = $this->input->post('degree_program_id');

		$update_dp_description_data = array();
	    // run through fields and produce an associative array holding keys and values - $_POST
	    for($i = 0; $i < count($updateFields); $i++){
			$update_dp_description_data[$updateFields[$i]] = $_POST[$dp_id.$updateFields[$i]];
	    }

	    // save data
	    $this->admin_model->update_degree_program_description_data($update_dp_description_data, $dp_id);

	    // show StudiengangDetails-List again
	    $this->session->set_flashdata('reload', $dp_id);
	    redirect('admin/degree_program_edit');

	}

//	/**
//	 * Gets data of new course to create and validates
//	 * DEPRECATED - use these functions when there is one single line (1! form) for adding new course
//	 */
//	function validate_new_degree_program_course(){
//	    $stdgng_id = $this->input->post('StudiengangID');
//
//	    $this->form_validation->set_rules('Kursname', 'Kursname fehlt', 'required');
//	    $this->form_validation->set_rules('kurs_kurz', 'Abkürzung fehlt', 'required');
//	    $this->form_validation->set_rules('Creditpoints', 'Creditpoints fehlen oder nicht numerisch', 'required|numeric');
//	    $this->form_validation->set_rules('SWS_Vorlesung', 'SWS-Vorlesung nicht numerisch', 'numeric');
//	    $this->form_validation->set_rules('SWS_Uebung', 'SWS-Übung nicht numerisch', 'numeric');
//	    $this->form_validation->set_rules('SWS_Praktikum', 'SWS-Praktikum nicht numerisch', 'numeric');
//	    $this->form_validation->set_rules('SWS_Projekt', 'SWS-Projekt nicht numerisch', 'numeric');
//	    $this->form_validation->set_rules('SWS_Seminar', 'SWS-Seminar nicht numerisch', 'numeric');
//	    $this->form_validation->set_rules('SWS_SeminarUnterricht', 'SWS-SeminarUnterricht nicht numerisch', 'numeric');
//
//
//	    if ($this->form_validation->run() == FALSE) {
//			// reload view
//			$this->session->set_flashdata('reload', $stdgng_id);
//			$this->degree_program_edit();
//		} else {
//			$this->save_degree_program_new_course();
//	    }
//	}
//
//	/**
//	 * After validation, new course is saved here.
//	 * DEPRECATED - use these functions when there is one single line (1! form) for adding new course
//	 */
//	function save_degree_program_new_course(){
//	    $new_course = array();
//	    $new_course = $this->input->post();
//
//	    // data
//	    $course_data = array();
//	    $exam_data = array();
//
//	    // run through data and prepare for saving
//	    foreach ($new_course as $key => $value) {
//			// if not submit-button-data
//			if($key != 'save_new_course'){
//				// and not exam-data
//				if(!strstr($key, 'ext')){
//					$course_data[$key] = $value;
//				} else {
//					// exam data to separate array
//					$exam_data[$key] = $value;
//				}
//			}
//	    }
//
//	    // insert course-data into db
//	    $this->admin_model->insert_new_course($course_data, $exam_data);
//
//	    // back to view
//	    $this->degree_program_edit();
//	}


	/**
	 * Deletes single course from studiengangkurs-table
	 * Called from degree_program_edit view after user confirmed
	 * deletion with click on OK in confirmation-dialog
	 * After altering db, ajax_show_course_of_degree_program
	 * is called (passed parameter indicates dp to load)
	 */
	public function ajax_delete_single_course_from_degree_program(){
	   $delete_course_id =  $this->input->post('course_data');

	   $split = explode('_', $delete_course_id);

	   // $split[0] = course id
	   $this->admin_model->delete_degree_program_single_course($split[0]);

	   // call view with updated data
	   echo $this->ajax_show_courses_of_degree_program($split[1]);
	}


	/**
	 * Creates a new course for that degree program and returns updated view.
	 * After altering db, ajax_show_course_of_degree_program
	 * is called (passed parameter indicates dp to load)
	 */
	public function ajax_create_new_course_in_degree_program(){
		$new_course_data = $this->input->post('course_data');
		$course_data_save_to_db = array();
		$exam_data_save_to_db = array();

		// get degree-program-id for reload
		$dp_id = $new_course_data[0];

		// run through submitted course data
		foreach($new_course_data as $data){
			$split = explode('-', $data);
			if(!stristr($split[1], 'ext')){
				// map array data (Semester) to ID (+1)
				switch ($split[1]) {
					case 'Semester' : $course_data_save_to_db[$split[1]] = $split[0] + 1; break; // array!! +1
					default : $course_data_save_to_db[$split[1]] = $split[0]; break;
				}
			} else {
				// !! only add data to array for exam_types that should be saved
				if($split[0] == 'checked') {
					$exam_data_save_to_db[$split[1]] = 1;
				}
			}
		}

		$this->admin_model->insert_new_course($course_data_save_to_db, $exam_data_save_to_db);

		echo $this->ajax_show_courses_of_degree_program($dp_id);
	}

	/*** << edit **************************************************************
	**************************************************************************/

    /*
     * ===============================================================================
     *                           Timetable administration
     * ===============================================================================
     */

	/**
     * Opens the timetable edit view. The method could be called from the main menue
     * or from another view. If it is called from another view the timetable id needs
     * to be passed via GET, otherwise the id to reload is passed via flashdata.
     *
     * @access public
     * @return void
	 */
	public function stdplan_edit(){

        $reload = 0;

		// when called from the parsing page get the timetable id
		$timetable_id = $this->input->get('timetable_id');

        // if called from another method, the timetable id is already placed in the data array
        if (array_key_exists('stdplan_id_automatic_reload',$this->data->load())){
            $timetable_id = $this->data->load()['stdplan_id_automatic_reload'];
        }

        // if called from the parsing page
        if($timetable_id){
			$reload = $timetable_id;
		}

        // if nothing is passed
		if(!$reload){
			$reload = 0;
		}

		// get all stdplan-data and add it to the data-array
	    $this->data->add('all_stdplan_filterdata', $this->admin_model->get_stdplan_filterdata());

	    // no autoreload without validation and add it to the data-array
	    $this->data->add('stdplan_id_automatic_reload', $reload);

        // load the view and pass the data
		$this->load->view('admin/stdplan_edit', $this->data->load());
    }


	/**
	 * Prints/echo`s an div with the timetable content table for a specified/selected timetable, that the user has chosen in the dropdown field.
     * Therefore the timetable id is passed via post. >> $this->input->post('stdplan_ids')!! combined id: StudiengangAbkuerzung, Semester, PO.
     * To be able to call the function from another controller function, the timetable id could als be passed as an parameter.
     *
	 * @access public
     * @param int $timetable_id_to_load ID of the timetable, where the events should be loaded for. Parameter is optional,
     *                                  because the timetable id is sometimes submitted via post. If nothing is passed, the
     *                                  default value is 0.
     * @return void
     */
	public function ajax_show_events_of_stdplan($timetable_id_to_load  = 0){

	    // read out the id of the timetable, that should be loaded
        $id = $this->input->post('timetable_id');
        /*
         * if the timetable id has been submitted via POST use it, otherwise use the id,
         * that has been passed as an parameter (timetable_id_to_load)
         */
        // use the id that has been submitted via POST
        if ($id){
            $timetable_id_to_load = $id;
        }

        // split the id of the selected timetable and save the parts in an array
        $splitted_id = explode("_", "$timetable_id_to_load");

	    // get all events of the selected timetable specified by degree program abbreviation, semester, po
	    $data['kurs_ids_split'] = $splitted_id;
	    $timetable_events_of_id = $this->admin_model->get_stdplan_data($splitted_id);

	    // get dropdown-data: all event-types, profs, times, days
	    $eventtypes = $this->admin_model->get_eventtypes();
	    $all_profs = $this->admin_model->get_profs_for_stdplan_list();
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    $course_ids = $this->admin_model->get_stdplan_course_ids($splitted_id); // get all course ids

		// getting data directly from helper_model - not implemented for all dropdowns
		$starttimes_dropdown_options = $this->helper_model->get_dropdown_options('starttimes');
		$endtimes_dropdown_options = $this->helper_model->get_dropdown_options('endtimes');
		$days_dropdown_options = $this->helper_model->get_dropdown_options('days');

	    // --- prepare the dropdown data for the view ---

		// prepare all courses
	    for($i = 0; $i < count($course_ids); $i++){
			$courses_dropdown_options[$i] = $course_ids[$i]->Kursname;
	    }

	    // prepare all eventtypes
	    for($i = 0; $i < count($eventtypes); $i++){
			$eventtype_dropdown_options[$i] = $eventtypes[$i]->VeranstaltungsformName;
	    }

	    // prepare all profs
	    for($i = 0; $i < count($all_profs); $i++){
            // prepare entry for first prof (dummy, placeholder)
            if ($i == 0){
                $profs_dropdown_options[0] = "Bitte Dozent ausw&auml;hlen";
            }
            else { // use the name for all other profs
                $profs_dropdown_options[$all_profs[$i]->DozentID] = $all_profs[$i]->Nachname.', '.$all_profs[$i]->Vorname;
            }
        }

	    // prepare all colors
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }

	    // save dropdown options into the $data-array
	    $data['courses_dropdown_options'] = $courses_dropdown_options;
	    $data['eventtype_dropdown_options'] = $eventtype_dropdown_options;
	    $data['profs_dropdown_options'] = $profs_dropdown_options;
	    $data['starttimes_dropdown_options'] = $starttimes_dropdown_options;
	    $data['endtimes_dropdown_options'] = $endtimes_dropdown_options;
	    $data['days_dropdown_options'] = $days_dropdown_options;
	    $data['colors_dropdown_options'] = $colors_dropdown_options;

		$data['first_row'] = TRUE; // variable indicates, that an empty first row should be displayed at the top of the table

		// get the first row - empty fields to be able to add new events
		$data['stdplan_first_row'] = $this->load->view('admin/partials/stdplan_coursetable_row', $data, TRUE);

	    foreach ($timetable_events_of_id as $timetable_event){
			$data['first_row'] = FALSE;
			$data['spkurs_id'] = $timetable_event->SPKursID;
			$data['kursname'] = $timetable_event->Kursname;
			$data['veranstaltungsform_id'] = $timetable_event->VeranstaltungsformID;
			$data['alternative'] = $timetable_event->VeranstaltungsformAlternative;
			$data['raum'] = $timetable_event->Raum;
			$data['dozent_id'] = $timetable_event->DozentID;
			$data['beginn_id'] = $timetable_event->StartID;
			$data['ende_id'] = $timetable_event->EndeID;
			$data['tag_id'] = $timetable_event->TagID;
			$data['wpf_flag'] = $timetable_event->isWPF;
			$data['wpf_name'] = $timetable_event->WPFName;
			$data['farbe'] = $timetable_event->Farbe;

            /*
             * Each timetable course represents an single row -> load the partial view for the currently viewed timetable event
             * and save it in the array, that holds each single row of the selected timetable
             */

            $rows[] = $this->load->view('admin/partials/stdplan_coursetable_row', $data, TRUE);
	    }

        // add the row array to the global data array
	    $data['stdplan_course_rows'] = $rows;

        // load the timetable coursetable view as an string and echo it out
	    echo $this->load->view('admin/partials/stdplan_coursetable_content', $data, TRUE);
	}

    /**
     * Validates all made changes and inputs, that were made during editing an specified timetable.
     * If the validation is correct, the changes will be saved in the database. Otherwise the
     * the validation errors and the timetable will be displayed.
     *
     * @access public
     * @return void
     */
	public function validate_stdplan_changes(){

	    // save the submitted information about the timetable
	    $stdplan_id = array(
		    $this->input->post('stdplan_id_abk'),
		    $this->input->post('stdplan_id_sem'),
		    $this->input->post('stdplan_id_po')
        );

        // get all course-ids, that belong to the timetable that has been changed
        $stdplan_course_ids = $this->admin_model->get_stdplan_sp_course_ids($stdplan_id);

        // construct the id of the timetable, that should be reloaded
        $stdplan_id_automatic_reload = $stdplan_id[0].'_'.$stdplan_id[1].'_'.$stdplan_id[2];

        // run through all ids and generate id-specific validation-rules
	    foreach($stdplan_course_ids as $id){
			$this->form_validation->set_rules($id->SPKursID.'_Raum', 'Raum', 'required');
	    }

        // modify the validation error message
        $this->form_validation->set_message('required', 'Die Angabe eines Raumes zu einer Veranstaltung ist zwingend erforderlich. Die vorgenommenen'.
                                            ' &Auml;nderungen wurden nicht in der Datenbank gespeichert.');

        // set the custom error delimiters
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

        // run the form validation
        // if some errors occured during the form validation repopulate the timetable and display the validation errors
	    if ($this->form_validation->run() == FALSE) {
			// reload timetable with the the validation errors
            $this->data->add('stdplan_id_automatic_reload', $stdplan_id_automatic_reload);
			$this->stdplan_edit();
		}
        // the validation was correct
        else {
            // save the timetable changes
			$this->save_stdplan_changes($stdplan_id_automatic_reload);
	    }
	}

	/**
	 * Updates / saves the changes for the viewed timetable, that have been submitted via POST and reloads the timetable.
     * Therefore the id of the timetable is passed as an parameter. If no id is passed, no timetable will be reloaded.
     *
     * @access public
	 * @param int $reload ID of the timetable, that should be reloaded. If nothing is passed, the default value is 0.
     *                    (No timetable will be reloaded)
	 */
	public function save_stdplan_changes($id_to_reload = 0){

	    // build an array, containing all keys that have to be updated in the database
	    $update_fields = array(
			'VeranstaltungsformID',
			'VeranstaltungsformAlternative',
			'WPFName',
			'Raum',
			'DozentID',
			'StartID',
			'EndeID',
			'TagID',
			'Farbe'
		);

	    // get all data from the form-submission
	    $post_data = $this->input->post();

		// get spcourse_ids for stdplan that has been saved

		// 1. get the timetable id(s) from the form-submission
		$stdplan_ids[] = $post_data['stdplan_id_abk'];
		$stdplan_ids[] = $post_data['stdplan_id_sem'];
		$stdplan_ids[] = $post_data['stdplan_id_po'];

        // 2. get the spcourse-ids from the database
		$sp_course_ids = $this->admin_model->get_stdplan_sp_course_ids($stdplan_ids);

		// get additonal data for colors - necessaray to map from array
	    $colors = $this->admin_model->get_colors_from_stdplan();
        // add each color to an array, that represents the dropdown data for further mapping
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }

		// run through all timetable courses to save the submitted data
		foreach ($sp_course_ids as $id) {

			$spc_id = $id->SPKursID; // save spcourse-id from object
			$update_stdplan_data = array(); // array for the (update)data that should be saved

			// run through all attributes of an course and store the data
			foreach ($update_fields as $field_name) {

				// different behaviour depending on field-type (checkbox has to be mapped from array-index to ID (+1))
				switch($field_name){
					case 'VeranstaltungsformID' :
					case 'StartID' :
					case 'EndeID' :
					case 'TagID' : $update_stdplan_data[$field_name] = ($post_data[$spc_id.'_'.$field_name] + 1) ; break;
					case 'Farbe' : $update_stdplan_data[$field_name] = $colors_dropdown_options[$post_data[$spc_id.'_'.$field_name]]; break;
					case 'WPFName' :
						if(array_key_exists($spc_id.'_'.$field_name, $post_data)){
							$update_stdplan_data[$field_name] = $post_data[$spc_id.'_'.$field_name];
						}
						break;
					default : $update_stdplan_data[$field_name] = $post_data[$spc_id.'_'.$field_name]; break;
				}

				// mapping / handling of the wpf checkbox
				if(array_key_exists($spc_id.'_isWPF', $post_data)){ // course is a wpf
					$update_stdplan_data['isWPF'] = 1;
				}
                else { // course is not a wpf
					$update_stdplan_data['isWPF'] = 0;
                    // delete/remove the name of the wpf
                    $update_stdplan_data['WPFName']='';
				}

			}

			// update data in the database - for each timetable-course
			$this->admin_model->update_stdplan_details($update_stdplan_data, $spc_id);
		}

        // add an success message to the view
        $this->data->add('success_message', 'Die &Auml;nderungen am Stundenplan wurden erfolgreich gespeichert!');

        // reload the timetable edit view
        $this->data->add('stdplan_id_automatic_reload', $id_to_reload);
        $this->stdplan_edit();
	}

	/**
	 * Deletes a single timetable course from edit timetable-view - after the delete button for an
     * specified course.
     * Method is usually called via ajax, so the result will be echoed and not returned!
     *
     * @access public
     * @return void
	 */
	public function ajax_delete_single_event_from_stdplan(){

		// get timetable-course-id from post
		$sp_course_id = $this->input->post('course_data');

		// split the timetable-course-id (sp_course_id) into an array with the following elements:
        // 0-> abk, 1-> sem, 2-> po, 3-> SP_COURSE_ID
		$split_ids = explode('_', $sp_course_id);

		// delete the timetable course in the database
		$this->admin_model->delete_single_event_from_stdplan($split_ids[3]);

		// delete/remove the spcourse_id from the array
		unset($split_ids[3]);

		// reload view with unique abk, sem, po combination
        $this->ajax_show_events_of_stdplan($sp_course_id);

	}

	/**
	 * Creates a new event in the timetable after the add Button (Hinzufuegen) has been clicked
     * and reloads the timetable view.
     * Method is usually called via ajax.
     *
     * @access public
     * @return void
	 */
	public function ajax_create_new_event_in_stdplan(){

        // get the data for the new course from the post array
		$new_course_data = $this->input->post('course_data');

        // split the id of the timetable. The timetable id is the first element (0) in the array
		$stdplan_ids = explode('_', $new_course_data[0]);

        // save the completed timetable id, to be able to reload the timetable view on an easy way
        $timetable_id = $new_course_data[0];

		// delete the first key(timetable id) from the array
		unset($new_course_data[0]);

        // array, that holds the data, that should be inserted into the database
        $save_to_db = array();

        // additional data of courses - mapping values to ids
	    $courses = $this->admin_model->get_stdplan_course_ids($stdplan_ids);
        // prepare the elements to be able to map them to the selected dropdown values
	    for($i = 0; $i < count($courses); $i++){
			$courses_dropdown_options[$i] = $courses[$i]->KursID;
	    }

		// getting additional data for colors - necessary to map from the array
	    $colors = $this->admin_model->get_colors_from_stdplan();
        // prepare the elements to be able to map them to the selected dropdown values
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }

		// run through the course-data and prepare it for saving in the database
		foreach($new_course_data as $data){

            $split_data = explode('_', $data);

            /*
             * prepare data for saving
             * - eventtype, starttime, endtime, day has to be mapped from array-index to id
             * - isWPF: checked = 1 , undefined = 0
             * - color need additional data >> array
             * - KursID same as color
             */
			switch($split_data[1]) {
				case 'VeranstaltungsformID' :
				case 'StartID' :
				case 'EndeID' :
				case 'TagID' : $save_to_db[$split_data[1]] = ($split_data[0] + 1); break; // !! +1
				case 'isWPF' : $save_to_db[$split_data[1]] = (($split_data[0] === 'checked') ? 1 : 0); break;
				case 'Farbe' : $save_to_db[$split_data[1]] = $colors_dropdown_options[$split_data[0]]; break;
				case 'KursID' : $save_to_db[$split_data[1]] = $courses_dropdown_options[$split_data[0]]; break;
				default : $save_to_db[$split_data[1]] = ($split_data[0]); break;
			}

		}

        // save the new course with alle information in the database
		$this->admin_model->save_new_course_in_stdplan($save_to_db, $stdplan_ids);
		// reload the updated timetable view
        $this->ajax_show_events_of_stdplan($timetable_id);
	}

    /**
     * Loads view with all imported timetables and gives the user the ability to delete an single timetable
     *
     * @access public
     * @return void
     */
    public function stdplan_delete(){
        // get all already imported timetables and add them to the data array
        $this->data->add('delete_view_data', $this->admin_model->get_stdplan_filterdata_plus_id());
        $this->load->view('admin/stdplan_delete', $this->data->load()); // load the view
    }


    /**
     * Deletes a single (selected) timetable.
     * Called from the stdplan_delete view (delete button)
     *
     * @access public
     * @return void
     */
    public function delete_stdplan(){

        // get data from post
        $degree_program_ids = array(
            $this->input->post('stdplan_abk'),
            $this->input->post('stdplan_semester'),
            $this->input->post('stdplan_po'),
        );

        // delete all data related to chosen timetable
        $this->admin_model->delete_stdplan_related_records($degree_program_ids);

        // name of the deleted timetable as an string
        $deleted_timetable = $degree_program_ids[0] . ' - ' . $degree_program_ids[2] . ' - ' . $degree_program_ids[1] .'. Semester';
        // set an feedback message!
        $this->message->set('Der ausgew&auml;hlte Stundenplan ('. $deleted_timetable .') wurde erfolgreich aus der Datenbank gel&ouml;scht!', 'success');

        // reload the timetable delete view
        redirect('admin/stdplan_delete');

    }

    /**
     * Shows the view, were the user can start the process to clean up all currently
     * used event groups to delete/remove old timetables for the students.
     *
     * @access public
     * @return void
     */
    public function show_clean_event_groups(){

        // load the clean event groups view
        $this->load->view('admin/clean_event_groups', $this->data->load());
    }

    /**
     * Removes all students from their active event groups / courses.
     *
     * @access public
     * @return void
     */
    public function clean_event_groups(){

        // initiate the process to clean all existing event groups
        $this->admin_model->clean_all_event_groups();

        // the cleaning process is finished -> add an success message and redirect back to the clean event groups view
        $this->message->set('Alle Veranstaltungsgruppen wurden erfolgreich bereinigt.', 'success');
        redirect('admin/show_clean_event_groups');
    }

    /*
     * ===============================================================================
     *                           End timetable administration
     * ===============================================================================
     */

    /*
     * ==================================================================================
     *                   Timetable import
     * ==================================================================================
     */

    /**
     * Shows and loads the timetable import view with all currently uploaded timetable files.
     * Gives the user the possibility to import (and upload) an new timetable xml-file.
     *
     * @access public
     * @return void
     */
    public function show_timetable_import(){

        // needed initializations
        $this->load->helper('directory'); // load the ci directory helper to be able to get access to the upload directory
        $upload_dir = array(); // array for holding the content of the timetable import directory on the server

        // get all uploaded files
        $upload_dir = directory_map('./resources/uploads/stundenplaene');
        $this->data->add('upload_dir', $upload_dir);

        // construct and add the uploaded files list to the main import view
        $this->data->add('stdgng_uploads_list_filelist', $this->_show_uploaded_timetables_file_list());

        // load the timetable import view
       $this->load->view('admin/stdplan_import', $this->data->load());
    }

    /**
     * Shows and loads the list with the already uploaded files and returns the partial view as an string.
     *
     * @access private
     * @return string The uploaded files list partial view as an string.
     */
    private function _show_uploaded_timetables_file_list(){

        $upload_dir = directory_map('./resources/uploads/stundenplaene');

        // declaration of arrays to hold the timetable group headlines and timetable file information
        $degree_program_headlines = array(); // array for group headlines
        $degree_program_files = array(); // array for file information

        // if there are already some uploaded timetable files prepare them to be able to display them
        if($upload_dir){

            // get all degree programs
            $all_degree_programs = $this->admin_model->get_all_degree_programs();

            // for each degree program search the corresponding files and group them for the view
            foreach($all_degree_programs as $single_degree_program){

                // save some values in temp variables
                $po = $single_degree_program->Pruefungsordnung; // po version of the viewed degree program
                $abk = $single_degree_program->StudiengangAbkuerzung; // abbreviation of the viewed degree program
                $id = $single_degree_program->StudiengangID; // id of the viewed degree program

                // construct the headline for the viewed degree program, that is used to group the corresponding files
                $degree_program_headlines[$id] = $abk.' - '.$po;

                // loop through each file in the degree program upload directory and distribute the matching files to the view data-array
                foreach($upload_dir as $single_file){

                    // check if the po and the abbreviation is part of the viewed filename
                    $po_in_filename = strstr($single_file, $po); // look if the po is part of the filename
                    $abk_in_filename = strstr($single_file, $abk); // look if the abbreviation is part of the filename

                    // if the viewed file corresponds to the actual viewed degree program -> add id to the view data array
                    if($po_in_filename != null && $abk_in_filename!= null){
                        $degree_program_files[$id][] = $single_file;
                    }
                }

            }

            // look for files that do not belong to any degree program

            // fetch all files that belong to a degree program in an array
            $files_with_po = array(); // array to hold the files that belong to a degree program

            // loop through the ordered timetable files and add them to an array
            foreach($degree_program_files as $degree_program => $degree_prog_array ){

                foreach($degree_prog_array as $single_file){

                    $files_with_po[] = $single_file;
                }
            }

            // check if every file in the upload directory is already assigned to a degree program -> if not save it under the group 'others'
            $degree_program_headlines['others'] = 'Andere:';

            // loop through all files in the directory and check if they are already assigned to a degree program
            foreach($upload_dir as $single_file){

                // the viewed filed does not belong to a degree program -> add id to the category others
                if(!in_array($single_file,array_values($files_with_po))){
                    $degree_program_files['others'][] = $single_file;
                }
            }
        }

        // add the headlines and files to the data-array and return the view as an string
	    $this->data->add('stdgng_uploads_headlines', $degree_program_headlines);
	    $this->data->add('stdgng_uploads', $degree_program_files);

        return $this->load->view('admin/stdplan_import_filelist', $this->data->load(), TRUE);
    }

    /**
     * Deletes a timetable file from the upload directory / from the server.
     * Method will be called via a submit button. The file that should be deleted is submitted via POST
     * (hidden input field).
     *
     * @access public
     * @return void
     */
    public function delete_timetable_file(){

        // get the file, that should be deleted (the filename is passed on button-click via hidden input)
        $file_to_delete = $this->input->post('timetable_file_to_delete');

        // delete the file
        unlink('./resources/uploads/stundenplaene/'.$file_to_delete);

        // redirect back to the timetable import start view
        redirect('admin/show_timetable_import');
    }

    /**
     * Opens a timetable file from the upload directory / from the server in notepad (text editor).
     * Method will be called via a submit button. The file that should be opened is submitted via POST
     * (hidden input field).
     *
     * @access public
     * @return void
     */
    public function open_timetable_file(){

        // get the file, that should be opened (the filename is passed on button-click via hidden input)
        $file_to_open = $this->input->post('std_file_to_open');

        // construct the filepath
        $filepath = './resources/uploads/stundenplaene/'.$file_to_open;

        // open the file file
        shell_exec('start '.$filepath);

        // redirect back to the timetable import start view
        redirect('admin/show_timetable_import');
    }

    /**
     * Starts uploading and parsing an timetable xml file, that has been chosen by the user in the upload
     * / timetable import mask. Method responds on view activity.
     *
     * @access public
     * @return void
     */
    public function upload_and_parse_timetable(){

        // === file upload  configuration ===

        // init path and upload type
        $config['upload_path'] = './resources/uploads/stundenplaene';
        $config['allowed_types'] = 'xml|XML';

        // load ci upload library and initialize it
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        // === start file upload and parsing ===

        // the upload was not successful
        if (!$this->upload->do_upload()){

            // an error occured during uploading the file -> construct an error message and add it to the view
            $error_message = "Der Datei-Upload war nicht erfolgreich. Entweder du du hast gar keine Datei zum Upload ausgew&auml;hlt, oder es besteht ein Fehler auf Seiten des Servers.";

            $this->data->add('error_starting_import', $error_message); // add the error message to the data array
            $this->show_timetable_import(); // reload the timetable import start view
        }
        // upload was successful
        else {

            // load parser and start parsing
            $this->load->library('timetable_xml_parser');
            $ids_or_errors = $this->timetable_xml_parser->parse_timetable_xml($this->upload->data()); // start parsing and save the written parsing result

            // the parsing process was fine -> show an view with the parsed data, so that the admin can check the parsed data
            if($ids_or_errors[0] != 'errors') {
                $this->data->add('ids', $ids_or_errors[0]);
                $this->data->add('created_courses', $ids_or_errors[1]['created_courses']); // add info about the created courses to the view
                $this->data->add('parsed_data', $ids_or_errors[1]['parsed_data']); // add info about the parsed data to the view
                $this->load->view('admin/stdplan_import_success_view', $this->data->load());
            }

            // there occurred some errors during the parsing process -> show an detailed error view for the admin
            else {
                $this->data->add('errors', $ids_or_errors);
                $this->load->view('admin/stdplan_import_error_view', $this->data->load());
            }
        }
    }

    /*
     * ==================================================================================
     *                                   End timetable import
     * ==================================================================================
     */
}
/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
