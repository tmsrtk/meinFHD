<?php

/**
 * Admin Controller
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Konstantin Voth, <konstantin.voth@fh-duesseldorf.de>
 * @author Frank Gottwald, <frank.gottwald@fh-duesseldorf.de>
 */

class Admin extends FHD_Controller {
	
	private $permissions;
	public $roles;
	private $roleIds;
	
	function __construct(){
	    parent::__construct();

	    $this->load->model('admin_model');

	    // Daten holen - Alle Rollen mit Bezeichnung, Alle Berechtigungen mit Bezeichnung, gesondert die RoleIds
	    $this->roles = $this->admin_model->getAllRoles();
	    $this->permissions = $this->admin_model->getAllPermissions();
	    $this->roleIds = $this->admin_model->getAllRoleIds();

//	    // get all stdgnge for the views
//	    $data['all_degree_programs'] = $this->admin_model->get_all_degree_programs();

        // --- EDIT BY Christian Kundruss (CK) for sso authentication ---
        // call the security_helper and check if the user is authenticated an allowed to call the controller
        $this->authentication->check_for_authenticaton();
        // --- END EDIT --
	}
	
	/**
	 * Admin Interface - Starter Method
	 * Beginning method for the admin interface.
	 *
	 */
	public function index()
	{
		$this->create_user_mask();
	}
	
	/**
	* Edit Permissions - Overview
	* Shows all permissions and roles and lets an admin to edit these.
	*
	* @category permissions_edit.php
	*/
	public function show_role_permissions(){
			
		// Alle RoleIDs durchlaufen und in einem verschachtelten Array speichern
		// >> je RoleID ein Array aller zugeordneter Permissions (array([roleid] => array([index] => permissions)...)
		foreach ($this->roleIds as $rid) {
			// Permissions einer Rolle holen
			// da es vorkommen kann, dass eine Rolle noch keine Permissions hat, wurde das Array mit null initialisiert (siehe getAllRolePermissions in admin_model.php)
			// dadurch ist der 0te Index des Arrays leer 
			$single_role_permissions = $this->admin_model->getAllRolePermissions($rid);
			// sofern es zu dieser Rolle Berechtigungen gibt
			if($single_role_permissions){ 
				foreach ($single_role_permissions as $rp){
					$all_role_permissions[$rid][]= $rp;
				}
			}
		}

// 			echo '<pre>';
// 			print_r($this->roles);
// 			print_r($this->permissions);
// 			print_r($this->roleIds);
// 			print_r($all_role_permissions);
// 			echo '</pre>';
		
		// Erzeugen eines Arrays, das für die Ausgabe genutzt werden kann
		// >> einfaches Array, das der Reihe nach alle genutzten Werte enthält mit: index % 5 == 0 als RoleID
		
		// Alle Permissions durchlaufen
		foreach ($this->permissions as $p) {
		
// 			$data['tableviewData'][] = $p->bezeichnung;
			$data['tableviewData'][] = $p->BerechtigungID; // ID speichern
			
			// Je Permission jede Rolle durchlaufen
			foreach ($this->roles as $r){
		
				// wenn im Array Role_permissions[RoleID] Werte enthalten sind (siehe oben - Index 0 ist ein leeres Feld)
				if(array_key_exists('1', $all_role_permissions[$r->RolleID])){
					// Wenn das zur Rolle zugehörige Array die RechteID als Wert enthält
					if(array_search($p->BerechtigungID, $all_role_permissions[$r->RolleID])){
						// speichern der ID
						$data['tableviewData'][] = $p->BerechtigungID;
					} else {
						// RechteID ist dieser Rolle nicht zugewiesen - x wird gespeichert
						$data['tableviewData'][] = 'x';
					}
				} else {
					// Rolle hat noch gar keine Rechte - 4 mal x wird gespeichert
					$data['tableviewData'][] = 'x';
				}
			}
		}
		$this->data->add('tableviewData', $data['tableviewData']);

		
		// Speichern weiterer Daten die in der View benötigt werden in das Data-Array 
		// $data['roleCounter'] = $this->admin_model->countRoles(); // Zur Anwendung des Modulo
		$this->data->add('roleCounter', $this->admin_model->countRoles());
		// $data['roles'] = $this->roles; // Permission-Objekte (ID und Bezeichnung)
		$this->data->add('roles', $this->roles);
		// $data['permissions'] = $this->permissions; // Permission-Objekte (ID und Bezeichnung)  
		$this->data->add('permissions', $this->permissions);
		
// 		echo '<pre>';
// 		print_r($data);
// 		echo '</pre>';
		
		// VIEW
		// $data['global_data'] = $this->data->load();

		$siteinfo = array(
			'title'			=> 'Rollenverwaltung',
			'main_content'	=>	'admin_rollenverwaltung'
			);
		$this->data->add('siteinfo', $siteinfo);
		
	#	$this->load->view('includes/template', $this->data->load());
		$this->load->view('admin/permissions_edit', $this->data->load());
	}
	
	/**
	* Edit Permissions - Overview
	* Saves all permission edits.
	*
	* @category permissions_edit.php
	*/
	function savePermissions(){

// 		echo '<pre>';
// 		print_r($this->input->post());
// 		echo '</pre>';
		
		$this->admin_model->deleteRolePermissions();
		
		// Durchlaufen für jede Berechtigung und Rolle
		foreach($this->permissions as $p){
			foreach($this->roleIds as $r){
				// wenn für diese Rollen-Permission-Kombination ein Eintrag enthalten ist
				if($this->input->post(($p->BerechtigungID).$r)){
//					echo $_POST;
					$rp['RolleID'] = $r;
					$rp['BerechtigungID'] = $p->BerechtigungID;
					
					// speichern
					$this->admin_model->updateRolePermissions($rp);
				}
			}
		}
		
// 		echo '<pre>';
// 		print_r($rp);
// 		echo '</pre>';

		// View neu laden
		$this->show_role_permissions();
	}
	
	/***************************************************************************
	* User management
	* 
	* Konstantin Voth
	*/
	
	// view controller =========================================================



	/**
	* User Invitation - Overview
	* Shows all open user requests. You can accept or delete the requests.
	*
	* @category user_invite.php
	*/
	public function request_user_invitation_mask()
	{
		// get all possible studiengänge for the form dropdown
		$this->data->add('studiengaenge', $this->admin_model->get_all_studiengaenge());
		// get all open user requests
		$this->data->add('user_invitations', $this->admin_model->request_all_invitations());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_invite', $this->data->load());
	}

	/**
	* Create User - Form
	* Create a new user.
	* 
	* @category user_add.php
	*/
	public function create_user_mask()
	{
		// get all possible roles for the form dropdown
		$this->data->add('all_roles', $this->admin_model->get_all_roles());
		
		// get all possible studiengänge for the form dropdown
		$this->data->add('studiengaenge', $this->admin_model->get_all_studiengaenge());
		
		//----------------------------------------------------------------------
		$this->load->view('admin/user_add', $this->data->load());
	}
	
	/**
	* Edit User - Form
	* 
	* @category user_edit.php
	*/
	public function edit_user_mask()
	{
		// get all possible roles for the form dropdown
		$this->data->add('all_roles', $this->admin_model->get_all_roles());
		
		//----------------------------------------------------------------------
		$this->load->view('admin/user_edit', $this->data->load());
	}

	/**
	* Delete User - Form
	* 
	* @category user_delete.php
	*/
	public function delete_user_mask()
	{
		// get all user	
		$this->data->add('user', $this->admin_model->get_all_user());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_delete', $this->data->load());
	}

	/**
	* Role to Permission - List
	* Shows all possible roles and their associated permissions.
	*
	* @category user_import.php
	*/
	public function import_user_mask()
	{

		//----------------------------------------------------------------------
		$this->load->view('admin/user_import', $this->data->load());
	}

	// ->>> show_role_permissions

	/**
	* Shows all users and their associated roles. 
	*
	* @category user_edit_roles.php
	*/
	public function edit_roles_mask()
	{
		// all users
		$this->data->add('all_user', $this->admin_model->get_all_user_with_roles());

		// all roles
		$this->data->add('all_roles', $this->admin_model->get_all_roles());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_edit_roles', $this->data->load());
	}

	// action controller =======================================================




	/**
	 * Gets the input data, generates a password, routes to the model function to save 
	 * the user in the DB and sends an email to the created user.
	 * 
	 * @category user_add.php
	 */
	private function create_user()
	{
		// get values from post
		$form_data = $this->input->post();

		// generate password
		$password = $this->adminhelper->passwort_generator();

		// save new user in db
		$this->admin_model->save_new_user($form_data, $password);

		// send e-mail to the new user
		$this->mailhelper->send_meinfhd_mail($form_data['email'], "Der User {$form_data['loginname']} wurde erstellt.", "Ihr Passwort lautet: {$password}");
	}

	/**
	 * Gets the input data and puts the user to the invitation requests. Sends an email
	 * to the admin, that a new request is open.
	 *
	 * @category user_invite.php
	 */
	private function put_user_into_invitation_requests()
	{
		// get values from post
		$form_data = $this->input->post();

		// save new user in db
		$this->admin_model->put_new_user_to_invitation_requests($form_data);

		// TODO: send mail to admin, that a new request was send
		// TODO: send mail to user, that he has to wait 
	}

	/**
	 * Creates the user from his invitation request.
	 *
	 * @category user_invite.php
	 */
	public function create_user_from_invitation_requests()
	{
		// get values from post
		$invitation_id = $this->input->post('request_id');

		// 0: create user, 1: delete request

		// get choosen action from "functions dropdown"
		$user_function = $this->input->post('user_function');

		switch ($user_function)
		{
			case '0':
				// save the user into benutzer table
				$this->admin_model->save_new_user_from_invitation($invitation_id);
				$this->message->set('Der User wurde von der Einladungsliste erstellt.', 'error');
				redirect(site_url().'admin/request_user_invitation_mask');
				break;
			case '1':
				$this->admin_model->delete_invitation($invitation_id);
				$this->message->set('Der User wurde von der Einladungsliste gelöscht.', 'error');
				redirect(site_url().'admin/request_user_invitation_mask');
				break;
			default:
				# code...
				break;
		}
	}

	/**
	 * Saves user changes.
	 *
	 * @category user_edit.php
	 */
	public function save_user_changes()
	{
		$user_id = $this->input->post('user_id');

		// var_dump($user_id);

		$data = array(
				'LoginName'					=> $this->input->post('loginname'),
				'Vorname'					=> $this->input->post('forename'),
				'Nachname'					=> $this->input->post('lastname'),
				'Email'						=> $this->input->post('email')
			);
		$this->admin_model->update_user($user_id, $data);
	}

	/**
	 * Validation for a new user.
	 *
	 * @category user_add.php
	 */
	public function validate_create_user_form()
	{
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$rules = array();

		$rules[] = $this->adminhelper->get_formvalidation_role();
		$rules[] = $this->adminhelper->get_formvalidation_loginname();
		$rules[] = $this->adminhelper->get_formvalidation_email();
		$rules[] = $this->adminhelper->get_formvalidation_forename();
		$rules[] = $this->adminhelper->get_formvalidation_lastname();

		$this->form_validation->set_rules($rules);

		// which role was selected?
		$role = $this->input->post('role');

		// depending on role, different validations
		// if student
		if ($role === '5'/*student*/)
		{
			$rules = array();

			$rules[] = $this->adminhelper->get_formvalidation_studiengang();
			$rules[] = $this->adminhelper->get_formvalidation_matrikelnummer();

			$this->form_validation->set_rules($rules);

			// query if erstsemestler checkbox was checked or not
			if ( empty($form_values['erstsemestler']) )
			{
				// if not checked, -> invitation for non erstsemestler, -> more inputs to fill out
				$rules[] = $this->adminhelper->get_formvalidation_startjahr();
				$rules[] = $this->adminhelper->get_formvalidation_semesteranfang();
				
				$this->form_validation->set_rules($rules);
			}
		}

		// check for correctness
		if($this->form_validation->run() == FALSE)
		{
			// call create user mask again
			// $this->message->set('Beim Erstellen des Users ist ein Fehler unterlaufen.', 'error');

			// set flashdata
			// $this->session->set_flashdata('item', 'value');

			// redirect(site_url().'admin/create_user_mask');
			$this->create_user_mask();
		}
		else 
		{
			// save in db
			$this->create_user();

			// flash message
			$this->message->set('User erfolgreich erstellt!', 'error');
			redirect(site_url().'admin/create_user_mask');
		}
	}


	/**
	 * Validation for a new user invitation.
	 *
	 * @category user_invite.php
	 */
	public function validate_request_user_invitation_form()
	{
		// set custom delimiter for validation errors
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

		$rules = array();

		// values, from actual form
		$form_values = $this->input->post();

		// add rules
		$rules[] = $this->adminhelper->get_formvalidation_role();
		$rules[] = $this->adminhelper->get_formvalidation_forename();
		$rules[] = $this->adminhelper->get_formvalidation_lastname();
		$rules[] = $this->adminhelper->get_formvalidation_email();
		$rules[] = $this->adminhelper->get_formvalidation_erstsemestler();

		// set the rules
		$this->form_validation->set_rules($rules);


		// which role was selected?
		$role = $this->input->post('role');

		// depending on role, different validations
		// if student
		if ($role === '5'/*student*/)
		{
			$rules = array();

			$rules[] = $this->adminhelper->get_formvalidation_matrikelnummer();
			$rules[] = $this->adminhelper->get_formvalidation_startjahr();
			$rules[] = $this->adminhelper->get_formvalidation_semesteranfang();
			$rules[] = $this->adminhelper->get_formvalidation_studiengang();

			$this->form_validation->set_rules($rules);

			// generate actual year for the form value "Startjahr", if "Erstsemestler" was selected
			if (isset($form_values['erstsemestler']) && $form_values['erstsemestler'] == 'accept')
			{
				$form_values['startjahr'] = date("Y");
			}
		}

		// check for (in)correctness
		if($this->form_validation->run() == FALSE)
		{
			// $this->message->set('Beim setzten des Users auf die Einladungsliste ' .
				// ' ist ein Fehler unterlaufen', 'error');
			// redirect(site_url().'admin/request_user_invitation_mask');
			$this->request_user_invitation_mask();
		}
		else
		{
			// save in db
			$this->put_user_into_invitation_requests();

			// load new view with success message
			$this->message->set('User wurde erfolgreich auf die Einladungsliste gesetzt!', 'error');
			redirect(site_url().'admin/request_user_invitation_mask');
		}
	}

	/**
	 * Decides which function was selected and routes to the associated methods.
	 *
	 * @category user_edit.php
	 */
	public function validate_edit_user_form()
	{
		// TODO: decide which submit button was clicked by hidden inputs
		// 0: save, 1: pw reset, 2: semesterplan reset, 3: log-in as

		// get choosen action from "functions dropdown"
		$user_function = $this->input->post('user_function');

		switch ($user_function) {
			case '0':
				$this->_validate_edits();
				break;
			case '1':
				$this->_reset_pw();
				break;
			case '2':
				$this->_reset_semesterplan();
				break;
			case '3':
				$this->_login_as_user();
				break;

			default:
				# code...
				break;
		}
	}

	/**
	 * Validation for user changes.
	 *
	 * @category user_edit.php
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

		// search empty validation, to get the value, to put it in the searchbox after invalid validation
		// $rules[] = $this->adminhelper->get_formvalidation_searchbox();

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

		// even if these fields do not need any validation rules, they have to be set, otherwise
		// they are not avaliable after the ->run() method
		if ($current_user_data['Vorname'] != $new_form_values['forename'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_forename();
		}

		if ($current_user_data['Nachname'] != $new_form_values['lastname'])
		{
			$rules[] = $this->adminhelper->get_formvalidation_lastname();
		}

		$this->form_validation->set_rules($rules);

		// check for (in)correctness
		if($this->form_validation->run() == FALSE)
		{
			// call edit user mask again
			// $this->message->set('Beim Bearbeiten des Users ist ein Fehler aufgetreten.', 'error');
			// redirect(site_url().'admin/edit_user_mask');
			$this->edit_user_mask();
		}
		else
		{
			// save in db
			$this->save_user_changes();

			$this->message->set('Der User wurde erfolgreich bearbeitet.', 'error');
			$this->session->set_flashdata('searchbox', $new_form_values['email']);

			redirect(site_url().'admin/edit_user_mask');
		}
	}

	/**
	 * Resets a user´s password, and sends an email to him with the new one.
	 *
	 * @category user_edit.php
	 */
	private function _reset_pw()
	{
		// values, from actual form inputs
		$new_form_values = $this->input->post();

		$data = array(
				'Passwort' => $this->adminhelper->passwort_generator()
			);

		$this->admin_model->update_user($new_form_values['user_id'], $data);

		// send email
		// $this->mailhelper->send_meinfhd_mail(											///////////////////////////////////
		// 	$new_form_values['email'],
		// 	"Ihr Passwort wurde zurückgesetzt",
		// 	"Ihr Passwort lautet: {$data['Passwort']}"
		// 	);

		$this->message->set('Das Passwort wurde erfolgreich zurückgesetzt.', 'error');
		redirect(site_url().'admin/edit_user_mask');
	}

	/**
	 * Resets a user´s semesterplan.
	 *
	 * @category user_edit.php
	 */
	private function _reset_semesterplan()
	{
		// get the id of which user the semesterplan should be deleted
		$input_data = $this->input->post();
		$this->admin_model->reconstruct_semesterplan($input_data['user_id']);

		$this->message->set('Der Studienplan wurde erfolgreich zurückgesetzt.', 'error');
		redirect(site_url().'admin/edit_user_mask');
	}

	/**
	 * <p>deletes an user by his id. The id is submitted via POST.
     * Modifications by Christian Kundruss: If the local account is linked to an global
     * user id, the global uid will be add to the 'shibboleth blacklist'.
     * </p>
     * @access public
     * @return void
	 *
	 * @category user_delete.php
	 */
	public function delete_user()
	{
		$user_id = $this->input->post('user_id');

        // modifications for blacklisting by CK
        // if the users account is already linked to an global identity -> blacklist the global userid before deleting his identity
        $linked_userdata = $this->admin_model->is_user_linked($user_id);
        if ($linked_userdata) { // the user is linked
            // add him to the blacklist
            $this->admin_model->add_user_to_blacklist($linked_userdata);
        }
        // end modifications by CK

		$this->admin_model->model_delete_user($user_id);

		$this->message->set('Der User wurde erfolreich gelöscht.', 'error');
		redirect(site_url().'admin/delete_user_mask');
	}

    /*
     *  authenticates the admin under the account of the seleccted user
     *  by Christian Kundruss (c) 2012
     */
    public function _login_as_user() {
        $user_information = $this->input->post(); // get the whole information of the selected user

        if($this->authentication->login_as_user($user_information)) { // authentication was successful
            $message_body = 'Eingeloggt als ' . $this->authentication->get_name() .  ' (User-ID:  ' . $this->authentication->user_id() . ')';
            // print a message to get to know as who you are logged in, and to show that the authentication was succesful
            $this->message->set(sprintf($message_body));

            // redirect the user to the dashboard
            redirect(site_url().'dashboard/index');
        }
    }

	/*
	* builds the needed html markup an content (db) from incoming ajax request
	*/
	// public function ajax_show_user_backup()
	// {
	// 	// get value
	// 	$role_id = $this->input->get('role_id');
	// 	$searchletter = $this->input->get('searchletter');

	// 	$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);  ///////////////////// query if result 0 !!!!!!!!!!!

	// 	$result = '';

	// 	foreach ($q as $key => $value) {
	// 		$result .= $this->load->view('admin-subviews/user_tr', $value, TRUE);
	// 	}
	// 	echo $result;
	// }


	/**
	 * This method is used to render the needed search-response and HTML Markup.
	 * 
	 * @category user_edit.php
	 */
	public function ajax_show_user()
	{
		$result = '';


		// get value
		$role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

		// if nothing set, query would response all users, so lets prevent this
		if ( empty($role_id) && empty($searchletter) )
		{
			$result = 'Kein Ergebnis';
		}
		else
		{
			$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);

			// get user with needed html markup and return it
			foreach ($q as $key => $value)
			{
				$result .= $this->load->view('admin/partials/user_single_form', $value, TRUE);
			}
		}

		( empty($result)) ? print 'Kein Ergebnis' : print $result;

		// echo $result;
	}

	/**
	 * Returns the sum of all matched users.
	 * @return string Sum of matched users.
	 */
	public function ajax_show_user_count()
	{
		$result = '';


		// get value
		$role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

		// if nothing set, query would response all users, so lets prevent this
		if ( empty($role_id) && empty($searchletter) )
		{
			$result = '0';
		}
		else
		{
			$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);
			$result = count($q);
		}
		echo $result;
	}

	/**
	 * Changes the userroles.
	 * 
	 * @category user_edit_roles.php
	 */
	public function changeroles_user()
	{
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

		redirect('/admin/edit_roles_mask/', 'refresh');
	}

	/*
	* User management
	* 
	* Konstantin Voth
	***************************************************************************/
	
	
	
	/* ************************************************************************
	 * 
	 * ******************************* Studiengangverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	
	/*** add >> ***************************************************************
	**************************************************************************/
	
	/**
	 * Show page with empty input-fields.
	 * Called from within menue
	 * 
	 */
	function degree_program_add(){
				
	    // get all degree programs for the view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    $this->load->view('admin/degree_program_add', $this->data->load());

//	    echo '<div class="well"><pre>';
//	    echo 'DEBUG - if you see this tell developer - Frank ^^';
//	    print_r($this->admin_model->copy_degree_program(2));
//	    echo '</pre></div>';
	}
	
	/**
	 * Validates if degree-program-add-form is filled correctly.
	 * 
	 */
	function validate_new_created_degree_program(){
		
	    $this->form_validation->set_rules(
		    'Pruefungsordnung', 'Pruefungsordnung fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    'StudiengangName', 'Name für den Studiengang fehlt', 'required');
	    $this->form_validation->set_rules(
		    'StudiengangAbkuerzung', 'Abkürzung fehlt', 'required');
	    $this->form_validation->set_rules(
		    'Regelsemester', 'Regelsemester fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    'Creditpoints', 'Creditpoints fehlen', 'required|numeric');
	    $this->form_validation->set_rules(
		    'Beschreibung', 'Beschreibung fehlt', 'required');
	    
	    if ($this->form_validation->run() == FALSE) {
			// reload view
			$this->degree_program_add();
	    } else {
			$this->save_new_created_degree_program();
	    }
	}
	
	
	/**
	 * Save new degree program to db.
	 * Insert new entry into db with given values ($_POST)
	 */
	function save_new_created_degree_program(){
	    // TODO?? check if given name and version are already used
		// perhaps not necessary because admin should know that this isn't possible

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

	    // get data from form-submission
	    for($i = 0; $i < count($insert_fields); $i++){
		    if($_POST[$insert_fields[$i]] != null){
			    $insert_new_dp[$insert_fields[$i]] = $_POST[$insert_fields[$i]];
		    }
	    }

	    // save new record - model returns id of new created dp
		$new_id = 0;
	    $new_id = $this->admin_model->create_new_degree_program($insert_new_dp);

	    // redirect to degree program view with active dropdown (i.e. the new created)
		// pass new id via flashdata
		$this->session->set_flashdata('reload', $new_id);
	    redirect('admin/degree_program_edit');
	}
	
	/*** << add ***************************************************************
	**************************************************************************/
	
	/*** copy/delete >> *******************************************************
	**************************************************************************/
	
	/**
	 * Helper method called when degree programm should be deleted
	 * points to degree_program_copy_delete and passes boolean
	 * called from menue
	 */
	public function degree_program_delete(){
	    $this->degree_program_copy_delete(TRUE);
	}
	
	/**
	 * Helper method called when degree programm should be copied
	 * points to degree_program_copy_delete and passes boolean
	 * called from menue
	 */
	public function degree_program_copy(){
	    $this->degree_program_copy_delete(FALSE);
	}
	
	
	/**
	 * Shows list of all degree programs to give the opportunity to delete/copy them.
	 * @param boolean $delete flag to use the same view for copying and deleting
	 */
	private function degree_program_copy_delete($delete){
	    // get all degree programs for the view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    $this->data->add('delete', $delete);

	    $this->load->view('admin/degree_program_copy_delete', $this->data->load());
	}
	
	/**
	 * Deletes a whole degree program
	 * Called from within view after OK button is clicked
	 */
	public function delete_degree_program() {
	    $delete_id = $this->input->post('degree_program_id');
	    $this->admin_model->delete_degree_program($delete_id);

	    // show view again
//	    $this->degree_program_delete();
	    redirect('admin/degree_program_delete');
	}
	
	/**
	 * Deltes a whole degree program - called when button is clicked
	 * called from within view after button is clicked
	 */
	public function copy_degree_program() {
	    $copy_id = $this->input->post('degree_program_id');
	    // copy that degree program >> returns id of copied dp
		$new_id = 0;
		$new_id = $this->admin_model->copy_degree_program($copy_id);

		// pass new id via flashdata
		$this->session->set_flashdata('reload', $new_id);
	    // call degree-program-edit view of that course
	    redirect('admin/degree_program_edit');
		
	}
	
	/*** << copy/delete *******************************************************
	**************************************************************************/

	/*** edit >> **************************************************************
	**************************************************************************/
	
	/**
	 * Shows view with dropdown for degree programs
	 * If there is something passed (via flashdata) the view loads a specific degree-program
	 * if not: user will see the empty view
	 */
	public function degree_program_edit(){
		$reload = $this->session->flashdata('reload');
		if(!$reload){
			$reload = 0; // if nothing is passed
		}

	    // get all degree programs for filter-view
	    $this->data->add('all_degree_programs', $this->admin_model->get_all_degree_programs());
	    // set degree_program_id to 0 - indicates, that view has been loaded directly from controller
	    // no autoreload without validation
	    $this->data->add('degree_program_id_automatic_reload', $reload);

	    $this->load->view('admin/degree_program_edit', $this->data->load());
	}
	
	
	/**
	 * Returns an div with the degree-program-table for a passed degree-program-id
	 * either passed via GET >> $this->input->get('degree_program_id')
	 * or if called from within this controller (deleting, adding one course) as parameter
	 */
	public function ajax_show_courses_of_degree_program($degree_program_id = '0'){

	    // if parameter is 0 - method called from within view
	    if($degree_program_id === '0'){
			// get submitted data - AJAX
			$degree_program_chosen_id = $this->input->get('degree_program_id');
		// otherwise method called from within controller (delete/add  course)
		// id is passed
	    } else {
			$degree_program_chosen_id = $degree_program_id;
	    }
		
	    $courses_of_single_degree_program = array();
	    $courses_of_single_degree_program = $this->admin_model->get_degree_program_courses($degree_program_chosen_id);

	    $details_of_single_degree_program = $this->admin_model->get_degree_program_details_asrow($degree_program_chosen_id);

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

//		echo '<pre>';
//		print_r($courses_of_single_degree_program);
//		echo '</pre>';

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

	    // return content
	    $result = '';
	    $result .= $this->load->view('admin/partials/degree_program_details', $data, TRUE);
	    $result .= $this->load->view('admin/partials/degree_program_coursetable_content', $data, TRUE);

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
		
//		echo print_r($course_data_save_to_db).print_r($exam_data_save_to_db); // DEBUG
		$this->admin_model->insert_new_course($course_data_save_to_db, $exam_data_save_to_db);
		
		echo $this->ajax_show_courses_of_degree_program($dp_id);
		
	}
	
	/*** << edit **************************************************************
	**************************************************************************/
	
	
	/* 
	 * 
	 * ******************************* Studiengangverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
	
	
	/* ************************************************************************
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	/**
	 * Shows stdplan-edit view
	 * If called from menue: reload is empty >> no active dropdown
	 * otherwise: id to reload is passed via flashdata
	 */
	public function stdplan_edit(){
	    $reload = $this->session->flashdata('reload');

		// when called from parsing-page
		$post = $this->input->post('stdplan_id');
		if($post){
			$reload = $post;
		}
		
		if(!$reload){
			$reload = 0; // if nothing is passed
		}

		// get all stdplan-data
	    $this->data->add('all_stdplan_filterdata', $this->admin_model->get_stdplan_filterdata());

	    // no autoreload without validation
	    $this->data->add('stdplan_id_automatic_reload', $reload);
		$this->load->view('admin/stdplan_edit', $this->data->load());
	}
	
	
	/**
	 * Returns an div with the stdplan-table for a specified stdplan >> $this->input->get('stdplan_id')
	 * !! combined id: StudiengangAbkuerzung, Semester, PO
	 * @param array $reload_ids holding unique abk, sem, po combination - passed when called from within controller >> reload view (dropdown)
	 */
	public function ajax_show_events_of_stdplan($reload_ids = ''){
	    // if reload_ids is empty function has been called from view
		if(!$reload_ids){
			$ids = $this->input->post('stdplan_ids');
			$splitted_ids = explode("_", "$ids");
		// otherwise function called from within controller
		// >> delete or add single row
		} else {
			$splitted_ids = $reload_ids;
		}
//	    $ids = "BMI_2_2010";
	    
	    // get all events of a stundenplan specified by stdgng-abk., semester, po
	    $data['kurs_ids_split'] = $splitted_ids;
	    $stdplan_events_of_id = $this->admin_model->get_stdplan_data($splitted_ids);
	    
	    // get dropdown-data: all event-types, profs, times, days
	    $eventtypes = $this->admin_model->get_eventtypes();
	    $all_profs = $this->admin_model->get_profs_for_stdplan_list();
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    $course_ids = $this->admin_model->get_stdplan_course_ids($splitted_ids);
		
		// getting data directly from helper_model - not implemented for all dropdowns
		$starttimes_dropdown_options = $this->helper_model->get_dropdown_options('starttimes');
		$endtimes_dropdown_options = $this->helper_model->get_dropdown_options('endtimes');
		$days_dropdown_options = $this->helper_model->get_dropdown_options('days');
		
//	    echo '<pre>';
//	    print_r($all_profs[$i]->DozentID);
//	    echo '</pre>';
	    
	    // and prepare for dropdowns
		// courses
	    for($i = 0; $i < count($course_ids); $i++){
			$courses_dropdown_options[$i] = $course_ids[$i]->Kursname;
	    }
	    // eventtypes
	    for($i = 0; $i < count($eventtypes); $i++){
			$eventtype_dropdown_options[$i] = $eventtypes[$i]->VeranstaltungsformName;
	    }
	    // profs
	    for($i = 0; $i < count($all_profs); $i++){
			$profs_dropdown_options[$all_profs[$i]->DozentID] = $all_profs[$i]->Nachname.', '.$all_profs[$i]->Vorname;
		}
		
	    // colors
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }

	    // save dropdown options into $data
	    $data['courses_dropdown_options'] = $courses_dropdown_options;
	    $data['eventtype_dropdown_options'] = $eventtype_dropdown_options;
	    $data['profs_dropdown_options'] = $profs_dropdown_options;
	    $data['starttimes_dropdown_options'] = $starttimes_dropdown_options;
	    $data['endtimes_dropdown_options'] = $endtimes_dropdown_options;
	    $data['days_dropdown_options'] = $days_dropdown_options;
	    $data['colors_dropdown_options'] = $colors_dropdown_options;
	    
		$data['first_row'] = TRUE;
		
		// getting first row - empty fields
		$data['stdplan_first_row'] = $this->load->view('admin/partials/stdplan_coursetable_row', $data, TRUE);
	    
	    foreach ($stdplan_events_of_id as $sp_events){
			$data['first_row'] = FALSE;
			$data['spkurs_id'] = $sp_events->SPKursID;
			$data['kursname'] = $sp_events->Kursname;
			$data['veranstaltungsform_id'] = $sp_events->VeranstaltungsformID;
			$data['alternative'] = $sp_events->VeranstaltungsformAlternative;
			$data['raum'] = $sp_events->Raum;
			$data['dozent_id'] = $sp_events->DozentID;
			$data['beginn_id'] = $sp_events->StartID;
			$data['ende_id'] = $sp_events->EndeID;
			$data['tag_id'] = $sp_events->TagID;
			$data['wpf_flag'] = $sp_events->isWPF;
			$data['wpf_name'] = $sp_events->WPFName;
			$data['farbe'] = $sp_events->Farbe;

			// array holding all rows
			$rows[] = $this->load->view('admin/partials/stdplan_coursetable_row', $data, TRUE);
			
//			echo '<pre>';
//			echo print_r($data['eventtype_dropdown_options']);
//			echo '</pre>';
		
	    }
	    
	    $data['stdplan_course_rows'] = $rows;
	    
	    echo $this->load->view('admin/partials/stdplan_coursetable_content', $data, TRUE);
	    
	}

	/**
	 * Validating all inputs.
	 */
	public function validate_stdplan_changes(){
	    
	    // get all course-ids belonging to a specified stdgng
	    $stdplan_id = array(
		$this->input->post('stdplan_id_abk'),
		$this->input->post('stdplan_id_sem'),
		$this->input->post('stdplan_id_po'));
	    
	    $stdplan_course_ids = $this->admin_model->get_stdplan_sp_course_ids($stdplan_id);
	    
	    
	    foreach($stdplan_course_ids as $id){
			// run through all ids and generate id-specific validation-rules
			$this->form_validation->set_rules($id->SPKursID.'_Raum', 'Fehler', 'required');
	    }
	    
	    $stdplan_id_automatic_reload = $stdplan_id[0].'_'.$stdplan_id[1].'_'.$stdplan_id[2];
	    
	    if ($this->form_validation->run() == FALSE) {
			// reload view
			$this->session->set_flashdata('reload', $stdplan_id_automatic_reload);
			redirect('admin/stdplan_edit');
		} else {
			$this->save_stdplan_changes($stdplan_id_automatic_reload);
	    }
	}
	
	/**
	 * Updates data of Stdplan
	 * @param int $reload id to be reloaded (if passed / 0 >> no active stdplan)
	 */
	public function save_stdplan_changes($reload = 0){
		
	    // build an array, containing all keys that have to be updated in db
	    $update_fields = array(
			'VeranstaltungsformID',
			'VeranstaltungsformAlternative',
			'WPFName',
			'Raum',
			'DozentID',
			'StartID',
			'EndeID',
//			'isWPF', // has to be handled separately
			'TagID',
			'Farbe'
		);
	    
	    // get data from form-submission
	    $post_data = $this->input->post();
		
//		echo '<pre>';
//		print_r($post_data);
//		echo '</pre>';
		
		// get spcourse_ids for stdplan that has been saved
		// 1. get id from submission | 2. get ids from db
		$stdplan_ids[] = $post_data['stdplan_id_abk'];
		$stdplan_ids[] = $post_data['stdplan_id_sem'];
		$stdplan_ids[] = $post_data['stdplan_id_po'];
		$sp_course_ids = $this->admin_model->get_stdplan_sp_course_ids($stdplan_ids);
		
		// getting additonal data for colors - necessaray to map from array
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }
		
		// run through ids to save submitted data
		foreach ($sp_course_ids as $id) {
			$spc_id = $id->SPKursID; // save id from object
			$update_stdplan_data = array(); // array for data to save
			// run through array with fields to update
			foreach ($update_fields as $field_name) {
				// different behaviour depending on field-typ (cb has to be mapped from array-index to ID (+1))
				switch($field_name){
					case 'VeranstaltungsformID' :
					case 'StartID' :
					case 'EndeID' :
					case 'TagID' : $update_stdplan_data[$field_name] = ($post_data[$spc_id.'_'.$field_name] + 1) ; break;
					case 'Farbe' : $update_stdplan_data[$field_name] = $colors_dropdown_options[$post_data[$spc_id.'_'.$field_name]]; break;
					case 'WPFName' :
						if(key_exists($spc_id.'_'.$field_name, $post_data)){
							$update_stdplan_data[$field_name] = $post_data[$spc_id.'_'.$field_name];
						}
						break;
					default : $update_stdplan_data[$field_name] = $post_data[$spc_id.'_'.$field_name]; break;
				}
				
				// handling of checkbox
				if(array_key_exists($spc_id.'_isWPF', $post_data)){
					$update_stdplan_data['isWPF'] = 1;
				} else {
					$update_stdplan_data['isWPF'] = 0;
				}
				
			}
			// update data in db - for every 
			$this->admin_model->update_stdplan_details($update_stdplan_data, $spc_id);
//			echo '<pre>';
//			print_r($update_stdplan_data);
//			echo '</pre>';
			
		}
		
		$this->session->set_flashdata('reload', $reload);
	    redirect('admin/stdplan_edit');
	}
	
	
	/**
	 * Calls view to delete single stdplan
	 */
	public function stdplan_delete(){
	    $this->data->add('delete_view_data', $this->admin_model->get_stdplan_filterdata_plus_id());
		$this->load->view('admin/stdplan_delete', $this->data->load());
	}
	
	
	/**
	 * Deletes single stdplan.
	 * Called from within view - button.
	 */
	public function delete_stdplan(){
	    
	    // get data from post
	    $degree_program_ids = array(
			$this->input->post('stdplan_abk'),
			$this->input->post('stdplan_semester'),
			$this->input->post('stdplan_po'),
	    );

	    // delete all data related to chosen stdplan
	    $this->admin_model->delete_stdplan_related_records($degree_program_ids);
	    
	    // reload view
	    redirect('admin/stdplan_delete');
	    
	}
	
	
	/**
	 * Deletes a single line from stdplan-table-view - after button-click
	 */
	public function ajax_delete_single_event_from_stdplan(){
		// get id from post
		$sp_course_id = $this->input->post('course_data');
		// split ids >> abk, sem, po, SP_COURSE_ID
		$split_ids = explode('_', $sp_course_id);
		
		// delete data DB
		$this->admin_model->delete_single_event_from_stdplan($split_ids[3]);
		
		// delete spcourse_id from array
		unset($split_ids[3]);
		
		// reload view with unique abk, sem, po combination
		echo $this->ajax_show_events_of_stdplan($split_ids);		
		
	}
	
	/**
	 * Creates new event in stdplan after click on Button
	 */
	public function ajax_create_new_event_in_stdplan(){
		$new_course_data = $this->input->post('course_data');
		$save_to_db = array();
		$stdplan_ids = explode('_', $new_course_data[0]);
		
		// delete first key from array
		unset($new_course_data[0]);
		
//		echo print_r($new_course_data); // DEBUG
		
		// additional data of courses - mapping values to ids
	    $courses = $this->admin_model->get_stdplan_course_ids($stdplan_ids);
	    for($i = 0; $i < count($courses); $i++){
			$courses_dropdown_options[$i] = $courses[$i]->KursID;
	    }
		
		// getting additonal data for colors - necessaray to map from array
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    for($i = 0; $i < count($colors); $i++){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
	    }
		
		// run through course-data and prepare for saving
		foreach($new_course_data as $data){
			$split_data = explode('_', $data);
			// prepare data for saving
			// - eventtype, starttime, endtime, day has to be mapped from array-index to id
			// - isWPF: checked = 1 , undefined = 0
			// - color need additional data >> array
			// - KursID same as color
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
		
//		echo print_r($save_to_db, $stdplan_ids);
		$this->admin_model->save_new_course_in_stdplan($save_to_db, $stdplan_ids);
		
		// return updated view
		echo $this->ajax_show_events_of_stdplan($stdplan_ids);
	}
	
	/* 
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
	
	/* ***********************************************************************
	 * 
	 * *********************************** Stundenplanimport
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	/**
	 * Shows view with upload and all uploaded files till now
	 */
	public function stdplan_import(){
	    // inits
		$upload_dir = array();
		$parsing_result = '';
		$this->load->helper('directory');
		
		// getting parsing_result - if there is any
		// and put into dialog
		$parsing_result = $this->session->flashdata('parsing_result');
		
		if($parsing_result){
			if(is_array($parsing_result)){
				$data_for_dialog = '<ul>';
				// run through array and build string for dialog
				foreach ($parsing_result as $item => $value){
					$data_for_dialog .= '<li>'.$item.': '.$value.'</li>';
				}
				$data_for_dialog .= '</ul>';
				$this->data->add('view_feedback_dialog', $data_for_dialog);
			} else {
				// error
				$this->data->add('view_feedback_dialog', 'error');
			}
		} else {
			// default-value: no data was parsed
			$this->data->add('view_feedback_dialog', '');
		}

        // stuff before - Refactoring of import by Frank Gottwald

//	    // get files from upload-folder
//	    $upload_dir = directory_map('./resources/uploads');
//	    // get degree programs
//	    $degree_programs = $this->admin_model->get_all_degree_programs();
//	    $data['stdgng_uploads'] = '';
//	    
//		// init helper var
//	    $last_id = 0;
//	    
//	    if($upload_dir){
//			// prepare data for view
//			// generate array, that contains all 
//			foreach($degree_programs as $dp){
//				$po = $dp->Pruefungsordnung;
//				$abk = $dp->StudiengangAbkuerzung;
//				$id = $dp->StudiengangID;
//				$data['stdgng_uploads_headlines'][$id] = $abk.' - '.$po;
//				// run through dirs and distribute found data to view-array
//				foreach($upload_dir as $dir){
//					$needle_po = strstr($dir, $po);
//					$needle_abk = strstr($dir, $abk);
//					if($needle_po != null && $needle_abk != null){
//						$data['stdgng_uploads'][$id][] = $dir;
//					}
//				}
//				$last_id = $id;
//			}
//
//			if($data['stdgng_uploads'] != null){
//				// prepare data to 
//				foreach($data['stdgng_uploads'] as $nested_array){
//					foreach($nested_array as $file){
//						$files_with_po[] = $file;
//					}
//				}
//
//			//	    echo '<pre>';
//			//	    print_r($clean);
//			//	    echo '</pre>';  
//
//				// one additional field for other
//				// CHECK - will all other files be displayed in here?
//				// perhaps some fine-tuning in recognition of po needed?
//				$data['stdgng_uploads_headlines'][42] = 'Andere:';
//
//				// check if there are dirs, that don't belong to a po
//				// i.e. not in array, that contains the files that are already shown
//				foreach($upload_dir as $dir){
//					if(!in_array($dir, array_values($files_with_po))){
//						$data['stdgng_uploads'][42][] = $dir;
//					}
//				}
//			}
//		}

//	    $this->data->add('stdgng_uploads_headlines', $data['stdgng_uploads_headlines']);
//	    $this->data->add('stdgng_uploads', $data['stdgng_uploads']);
//		$this->data->add('stdgng_uploads_list_filelist',
//				$this->load->view('admin/partials/stdplan_import_filelist', $data, TRUE));
	    
		$this->load->view('admin/stdplan_import', $this->data->load());
	}
	
	
	/**
	 * Starts parsing of a xml-file with a new timetable
	 */
	public function stdplan_import_parse(){
		// init path and type
	    $config['upload_path'] = './resources/uploads/';
	    $config['allowed_types'] = 'xml';

		// load codeigniter-libs and parsing-model
	    $this->load->library('upload', $config);
	    $this->upload->initialize($config);
//	    $this->load->controller('stdplan_parser');
	    $this->load->model('admin_model_parsing');

		// if upload DID NOT work: 
	    if ( ! $this->upload->do_upload()){
			// go back to view and show errors
//			$this->session->set_flashdata('errors', validation_errors());
//			$this->data->add('error', validation_errors());
			
			// TODO redirect to correct view WITH errors not working properly
			$this->stdplan_import($this->upload->display_errors());
//			sleep(5);
//			redirect('admin/stdplan_import');
			
//			$this->stdplan_import($this->upload->display_errors());

		// else: process data and show success view
	    } else {
			// upload data
			$upload_data = $this->upload->data();
		
			// start parsing stdplan - pass data to parsing-model
//			$delete_file = $this->admin_model_parsing->parse_stdplan($upload_data);
			$ids_or_errors = $this->admin_model_parsing->parse_stdplan($upload_data);
			
			if($ids_or_errors[0] != 'errors'){
				$this->data->add('ids', $ids_or_errors[0]);
				unset($ids_or_errors[0]);
				$this->data->add('data', $ids_or_errors);
				$this->load->view('admin/partials/stdplan_import_DEBUG_view', $this->data->load());
			} else {
				$this->data->add('errors', $ids_or_errors);
				$this->load->view('admin/partials/stdplan_import_ERROR_view', $this->data->load());
			}
			
			// if parser returns error-message (PO not found in DB) show message
			// to user and delete temporary stored file
//			if($delete_file){
//				$this->session->set_flashdata('parsing_result', 'Datei wurde nicht hochgeladen - PO noch nicht angelegt.');
//				unlink($config['upload_path'].$upload_data['file_name']);
//
//			// else rediret to view
//			} else {
//				$this->session->set_flashdata('parsing_result', $upload_data);
//			}
//			redirect('admin/stdplan_import');
	    }
	}
	
	/**
	 * Deletes a uploaded file from file-list
	 */
	function delete_stdplan_file(){
		// file passed on button-click
	    $file_to_delete = $this->input->post('std_file_to_delete');
	    
	    // delete file
	    unlink('./resources/uploads/'.$file_to_delete);
	    
	    redirect('admin/stdplan_import');
	}
	
	
	
	/**
	 * Opens file from file-list in notepad
	 */
	function open_stdplan_file(){
		// file passed on button-click
	    $file_to_open = $this->input->post('std_file_to_open');
	    
		$file = './resources/uploads/'.$file_to_open;
		
	    // open file
	    shell_exec('start '.$file);
	    
	    redirect('admin/stdplan_import');
	}
	
	/* 
	 * 
	 * *********************************** Stundenplanimport
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
}