<?php

class Admin extends FHD_Controller {
	
	private $permissions;
	private $roles;
	private $roleIds;
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('admin_model');

		// Daten holen - Alle Rollen mit Bezeichnung, Alle Berechtigungen mit Bezeichnung, gesondert die RoleIds
		$this->roles = $this->admin_model->getAllRoles();
		$this->permissions = $this->admin_model->getAllPermissions();
		$this->roleIds = $this->admin_model->getAllRoleIds();
		
		// get all stdgnge for the views
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
	}
	
	
	public function index()
	{
		$this->create_user_mask();
	}
	
	
	function show_role_permissions(){
			
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

	/*
	* loads content for the admin_create_user_mask.php
	*/
	public function create_user_mask()
	{
		// siteinfo
	##	$siteinfo = array(
	#		'title'			=> 'Benutzer erstellen',
	#		'main_content'	=> 'admin_create_user_mask'
	#		);
	#	$this->data->add('siteinfo', $siteinfo);
		
		// all roles
		$this->data->add('all_roles', $this->admin_model->get_all_roles());
		
		// all studiengänge
		$this->data->add('studiengaenge', $this->admin_model->get_all_studiengaenge());
		
		//----------------------------------------------------------------------
		$this->load->view('admin/user_add', $this->data->load());
	}
	
	/*
	* loads content for the admin_edit_user_mask.php
	*/
	public function edit_user_mask()
	{
		// siteinfo
		$siteinfo = array(
			'title'			=> 'Benutzer anzeigen',
			'main_content'	=> 'admin_edit_user_mask'
			);
		$this->data->add('siteinfo', $siteinfo);
		
		// all users
		// $data['user'] = $this->admin_model->get_all_user();
		
		// all roles
		$this->data->add('all_roles', $this->admin_model->get_all_roles());
		
		//----------------------------------------------------------------------
		$this->load->view('admin/user_edit', $this->data->load());
	}

	/*
	* loads content for the admin_delete_user_mask.php
	*/
	public function delete_user_mask()
	{
		// siteinfo
		$siteinfo = array(
			'title'			=> 'Benutzer loeschen',
			'main_content'	=> 'admin_delete_user_mask'
			);
		$this->data->add('siteinfo', $siteinfo);

		// all users	
		$this->data->add('user', $this->admin_model->get_all_user());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_delete', $this->data->load());
	}

	/*
	* loads content for the admin_show_permissions.php
	*/
	public function show_permissions()
	{
		// siteinfo
		$siteinfo = array(
			'title'			=> 'Benutzerrechte anzeigen',
			'main_content'	=> 'admin_show_permissions'
			);
		$this->data->add('siteinfo', $siteinfo);

		//----------------------------------------------------------------------
		$this->load->view('admin/permissions_list', $this->data->load());
	}

	/*
	* loads content for the admin_request_user_invitation_mask.php
	*/
	public function request_user_invitation_mask()
	{
		// siteinfo
		$siteinfo = array(
			'title'			=> 'Einladungsaufforderungen anzeigen',
			'main_content'	=> 'admin_request_user_invitation_mask'
			);
		$this->data->add('siteinfo', $siteinfo);
		// all studiengänge
		$this->data->add('studiengaenge', $this->admin_model->get_all_studiengaenge());
		// user invitations
		$this->data->add('user_invitations', $this->admin_model->request_all_invitations());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_invite', $this->data->load());
	}

	public function edit_roles_mask()
	{
		$siteinfo = array(
			'title' => 'Rollen bearbeiten'
			);
		$this->data->add('siteinfo', $siteinfo);

		// all users
		$this->data->add('all_user', $this->admin_model->get_all_user_with_roles());

		// all roles
		$this->data->add('all_roles', $this->admin_model->get_all_roles());

		//----------------------------------------------------------------------
		$this->load->view('admin/user_edit_roles', $this->data->load());
	}

	public function show_successful_page()
	{
		// siteinfo
		$siteinfo = array(
			'title'			=> 'Erfolgreich',
			'main_content'	=> 'admin_create_user_success'
			);
		$this->data->add('siteinfo', $siteinfo);

		//----------------------------------------------------------------------
		$this->load->view('includes/template', $this->data->load());
	}


	// action controller =======================================================




	/*
	* creates a new user 
	*/
	public function create_user()
	{
		// get values from post
		$form_data = $this->input->post();

		// generate password
		$password = $this->adminhelper->passwort_generator();

		// save new user in db
		$this->admin_model->save_new_user($form_data, $password);

		// TODO: send mail with password

	}

	/*
	* puts a user invitation request 
	*/
	public function put_user_into_invitation_requests()
	{
		// get values from post
		$form_data = $this->input->post();

		// save new user in db
		$this->admin_model->put_new_user_to_invitation_requests($form_data);

		// TODO: send mail to admin, that a new request was send
		// TODO: send mail to user, that he has to wait 
	}

	/*
	* creates a new user from invitation
	*/
	public function create_user_from_invitation()
	{
		// get values from post | 
		$invitation_id = $this->input->post('request_id');

		// 0: create user, 1: delete request

		// get choosen action from "functions dropdown"
		$user_function = $this->input->post('user_function');

		switch ($user_function)
		{
			case '0':
				// save the user into benutzer table
				$this->admin_model->save_new_user_from_invitation($invitation_id);
				$this->request_user_invitation_mask();
				break;
			case '1':
				$this->admin_model->_delete_invitation($invitation_id);
				$this->request_user_invitation_mask();
				break;
			default:
				# code...
				break;
		}
	}

	/*
	* saves user changes
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

	/*
	* validates the form from admin_create_user_mask
	*/
	public function validate_create_user_form()
	{
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
			$this->create_user_mask();
		}
		else 
		{
			// save in db
			$this->create_user();

			// flash message
			$this->message->set('User erfolgreich erstellt!', 'error');  //////////////// NOT WORKING

			$this->show_successful_page();
		}
	}

	/*
	*
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
		}

		// check for (in)correctness
		if($this->form_validation->run() == FALSE)
		{
			// call edit user mask again
			$this->request_user_invitation_mask();
		}
		else
		{
			// save in db
			$this->put_user_into_invitation_requests();

			// load new view with success message
			$this->show_successful_page();
		}
	}

	/*
	* validates the form from admin_edit_user_mask
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
				$this->_login_as();			
				break;

			default:
				# code...
				break;
		}
	}

	/**/
	private function _validate_edits()
	{
		// set custom delimiter for validation errors
		$this->form_validation->set_error_delimiters('<div class="val_error">', '</div>');


		$rules = array();

		// values, from actual form
		$new_form_values = $this->input->post();

		// current user data in db
		$current_user_data = $this->admin_model->get_user_by_id($this->input->post('user_id'));

		// check if current value is different from the value in db
		if ($current_user_data['LoginName'] != $new_form_values['loginname']) 
		{
			// // add the rules, if there was a change
			// $new_rule = array(
			// 	'field' => 'loginname',
			// 	'label' => 'Benutzername',
			// 	'rules' => 'required|alpha_dash|min_length[4]|max_length[20]|is_unique[benutzer.LoginName]'
			// );
			// // push value to global rules var
			// array_push($rules, $new_rule);
			$rules[] = $this->adminhelper->get_formvalidation_loginname();
		}

		// same procedure for the other form inputs
		if ($current_user_data['Email'] != $new_form_values['email']) 
		{
			// // add the rules, if there was a change
			// $new_rule = array(
			// 	'field' => 'email',
			// 	'label' => 'E-Mail',
			// 	'rules' => 'required|valid_email|is_unique[benutzer.Email]'
			// );
			// // push value to global rules var
			// array_push($rules, $new_rule);
			$rules[] = $this->adminhelper->get_formvalidation_email();
		}

		// even if these fields do not need any validation rules, they have to be set, otherwise
		// they are not avaliable after the ->run() method
		if ($current_user_data['Vorname'] != $new_form_values['forename'])
		{
			// $new_rule = array(
			// 	'field' => 'forename',
			// 	'label' => 'Vorname',
			// 	'rules' => ''
			// );
			// array_push($rules, $new_rule);

			$rules[] = $this->adminhelper->get_formvalidation_forename();
		}

		if ($current_user_data['Nachname'] != $new_form_values['lastname'])
		{
			// $new_rule = array(
			// 	'field' => 'lastname',
			// 	'label' => 'Nachname',
			// 	'rules' => ''
			// );
			// array_push($rules, $new_rule);

			$rules[] = $this->adminhelper->get_formvalidation_lastname();
		}

		$this->form_validation->set_rules($rules);

		// check for (in)correctness
		if($this->form_validation->run() == FALSE)
		{
			// call edit user mask again
			$this->edit_user_mask();
		}
		else
		{
			// save in db
			$this->save_user_changes();

			redirect(site_url().'admin/edit_user_mask');
		}
	}

	/**/
	private function _reset_pw()
	{
		// values, from actual form inputs
		$new_form_values = $this->input->post();

		$data = array(
				'Passwort' => $this->adminhelper->passwort_generator()
			);

		$this->admin_model->update_user($new_form_values['user_id'], $data);

		redirect(site_url().'admin/edit_user_mask');
	}

	/*
	* deletes an user by his id
	*/
	public function delete_user()
	{
		$user_id = $this->input->post('user_id');

		$this->admin_model->model_delete_user($user_id);

		redirect(site_url().'admin/delete_user_mask');
	}

	/*
	* builds the needed html markup an content (db) from incoming ajax request
	*/
	/*
	* builds the needed html markup an content (db) from incoming ajax request
	*/
	public function ajax_show_user_backup()
	{
		// get value
		$role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

		$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);  ///////////////////// query if result 0 !!!!!!!!!!!

		$result = '';

		foreach ($q as $key => $value) {
			$result .= $this->load->view('admin-subviews/user_tr', $value, TRUE);
		}
		echo $result;
	}


	/**
	 * ajax_show_user for the div table version
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
		echo $result;
	}

	public function changeroles_user()
	{
		$formdata = $this->input->post();

		// clear saves for the actual user
		$this->admin_model->clear_userroles($formdata['user_id']);

		// set new settings
		foreach ($formdata['cb_userroles'] as $role)
		{
			$this->admin_model->save_userrole($formdata['user_id'], $role);
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
	
	/**
	 * Get all data for a selectable (dropdown) list of Studiengänge
	 */
	function show_stdgng_course_list($reload = 0){

	    // get all stdgnge for filter-view
	    $this->data->add('all_stdgnge', $this->admin_model->getAllStdgnge());
	    // set stdgng_id to 0 - indicates, that view has been loaded directly from controller
	    // no autoreload without validation
	    $this->data->add('stdgng_id_automatic_reload', $reload);

	    $siteinfo = array(
		'title' => 'Studiengangverwaltung',
		'main_content' => 'admin_stdgng_edit'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('admin/studiengang_edit', $this->data->load());
	}
	
	/**
	 * Show page with empty inpuf-fields 
	 */
	function create_new_stdgng(){
				
	    // get all stdgnge for the view
	    $this->data->add('allStdgnge', $this->admin_model->getAllStdgnge());

	    $siteinfo = array(
		'title' => 'Neuen Studiengang anlegen',
		'main_content' => 'admin_stdgng_createnew'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('admin/studiengang_add', $this->data->load());

	    echo '<div class="well"><pre>';
	    echo 'DEBUG - if you see this tell developer - Frank ^^';
	    print_r($this->admin_model->get_stdgng_courses(2));
	    echo '</pre></div>';
	}
	
	/**
	 * Validates if form is filled correctly.
	 */
	function validate_new_created_stdgng(){
		
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
		$this->create_new_stdgng();
	    } else {
		$this->save_new_created_stdgng();
	    }
	}
	
	
	/**
	 * Insert new entry into db with given values ($_POST)
	 */
	function save_new_created_stdgng(){
	    // TODO check if given name and version are already used - in this case return show errormessage

	    $insertFields = array(
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
	    for($i = 0; $i < count($insertFields); $i++){
		    if($_POST[$insertFields[$i]] != null){
			    $insertNewStdgng[$insertFields[$i]] = $_POST[$insertFields[$i]];
		    }
	    }

	    // save
	    $this->admin_model->create_new_stdgng($insertNewStdgng);

	    // load stdgng view with dropdown
	    $this->show_stdgng_course_list();
	}
	
	
	
	
	/**
	 * Shows list of all stdgng to give the opportunity to delete them
	 */
	function delete_stdgng_view(){
	    // get all stdgnge for the view
	    $this->data->add('allStdgnge', $this->admin_model->getAllStdgnge());

	    $siteinfo = array(
		'title' => 'Studiengang löschen',
		'main_content' => 'admin_stdgng_delete'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('includes/template', $this->data->load());
	}
	
	/**
	 * Returns an div holding the stdgng-table for a passed stdgng
	 * >> $this->input->get('stdgng_id')
	 */
	function ajax_show_courses_of_stdgng($stdgng_id = '0'){

	    // if parameter is 0 - method called from within view
	    if($stdgng_id === '0'){
		// get submitted data - AJAX
		$stdgng_chosen_id = $this->input->get('stdgng_id');
	    // otherwise methode called from within controller (delete course)
	    // id is passed
	    } else {
		$stdgng_chosen_id = $stdgng_id;
	    }
		
	    $courses_of_single_stdgng = $this->admin_model->
		    get_stdgng_courses($stdgng_chosen_id);

	    $details_of_single_stdgng = $this->admin_model->
		    get_stdgng_details_asrow($stdgng_chosen_id);

	    // get number of semesters and prepare data for dropdown
	    $regelsemester = $details_of_single_stdgng->Regelsemester;
	    for($i = 0; $i < $regelsemester; $i++){
//		if($i != 0){
			$semester_dropdown_options[$i] = $i+1;
//		} else {
//			$semester_dropdown_options[$i] = '';
//		}
	    }

	    // stdgng_id is already needed here to generate unique ids for delete-buttons
	    $data['stdgng_id'] = $stdgng_chosen_id;
	    $data['semester_dropdown'] = $semester_dropdown_options;

//		echo '<pre>';
//		print_r($courses_of_single_stdgng);
//		echo '</pre>';

	    // fill first element of object-array with default-values -
	    // >> necessary because first line of table view should be
	    // for creation of new courses
	    // only KursID is needed, because creation of input-fields grabs
	    // KursID to generate unique names => array[0]
//	    $courses_of_single_stdgng[0]['KursID'] = '0';
//	    $courses_of_single_stdgng[0]['Kursname'] = '';
//	    $courses_of_single_stdgng[0]['kurs_kurz'] = '';
//	    $courses_of_single_stdgng[0]['Creditpoints'] = '';
//	    $courses_of_single_stdgng[0]['SWS_Vorlesung'] = '';
//	    $courses_of_single_stdgng[0]['SWS_Uebung'] = '';
//	    $courses_of_single_stdgng[0]['SWS_Praktikum'] = '';
//	    $courses_of_single_stdgng[0]['SWS_Projekt'] = '';
//	    $courses_of_single_stdgng[0]['SWS_Seminar'] = '';
//	    $courses_of_single_stdgng[0]['SWS_SeminarUnterricht'] = '';
//	    $courses_of_single_stdgng[0]['Semester'] = '0';
//	    $courses_of_single_stdgng[0]['Beschreibung'] = '';
//	    // if there will be more exam-types added: this is the place to add them too!!
//	    $courses_of_single_stdgng[0]['pruefungstyp_1'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_2'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_3'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_4'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_5'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_6'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_7'] = FALSE;
//	    $courses_of_single_stdgng[0]['pruefungstyp_8'] = FALSE;

	    
	    // building a first line to save a new course to db
	    $data['new_course'] = $this->load->view('admin-subviews/admin_stdgng_coursetable_row_first', $data, TRUE);
	    
	    //for each record - print out table-row with form-fields
	    foreach($courses_of_single_stdgng as $sd){
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
		$rows[] = $this->load->view('admin-subviews/admin_stdgng_coursetable_row', $data, TRUE);
	    }
	    
	    // make data available in view
	    $data['stdgng_details'] = $details_of_single_stdgng;
	    $data['stdgng_course_rows'] = $rows;
	    $data['course_tablehead'] = $this->load->view('admin-subviews/admin_stdgng_coursetable_head', '', TRUE);

	    // return content
	    $result = '';
	    $result .= $this->load->view('admin/partials/studiengang_details', $data, TRUE);
	    $result .= $this->load->view('admin-subviews/admin_stdgng_coursetable_content', $data, TRUE);

	    echo $result;

	}
	
	
	/**
	 * Deltes a whole Stdgng - called when button is clicked
	 */
	function delete_stdgng() {
	    $deleteId = $this->input->post('deleteStdgngId');
	    $this->admin_model->delete_stdgng($deleteId);

	    $this->delete_stdgng_view();
	}
	
	
	/**
	 * validates if all changes that've been made are correct
	 * - PO - required, numeric
	 * - Name - required
	 * - Abk. - required
	 * - Regelsemester - required, numeric
	 * - CP - required, numeric
	 */
	function validate_stdgng_details_changes(){
	    
	    // TODO??? PO-Name-Abk-Kombi muss UNIQUE sein
	    
	    
	    // get all stdgnge for filter-view
	    $data['all_stdgnge'] = $this->admin_model->getAllStdgnge();
	    
	    // get stdgng_id
	    $stdgng_id = $this->input->post('stdgng_id');
	    
	    $this->form_validation->set_rules(
		    $stdgng_id.'Pruefungsordnung', 'Pruefungsordnung fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    $stdgng_id.'StudiengangName', 'Name für den Studiengang fehlt', 'required');
	    $this->form_validation->set_rules(
		    $stdgng_id.'StudiengangAbkuerzung', 'Abkürzung fehlt', 'required');
	    $this->form_validation->set_rules(
		    $stdgng_id.'Regelsemester', 'Regelsemester fehlt', 'required|numeric');
	    $this->form_validation->set_rules(
		    $stdgng_id.'Creditpoints', 'Creditpoints fehlen', 'required|numeric');
	    $this->form_validation->set_rules(
		    $stdgng_id.'Beschreibung', 'Beschreibung fehlt', 'required');
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$this->show_stdgng_course_list($stdgng_id);
	    } else {
		$this->save_stdgng_details_changes();
	    }
	}
	
	
	function validate_stdgng_course_changes(){
	    
	    // get all stdgnge for filter-view
//	    $data['all_stdgnge'] = $this->admin_model->getAllStdgnge();
	    
	    // get all course-ids belonging to a specified stdgng
	    $stdgng_id = $this->input->post('stdgng_id');
	    $stdgng_course_ids = $this->admin_model->getStdgngCourseIds($stdgng_id);
	    
	    foreach($stdgng_course_ids as $id){
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
		$this->show_stdgng_course_list($stdgng_id);
	    } else {
		$this->save_stdgng_course_changes();
	    }
	}
	
	
	/**
	 * Gets data of new course to create and validates
	 */
	function validate_new_stdgng_course(){
	    $stdgng_id = $this->input->post('StudiengangID');
	    
	    $this->form_validation->set_rules('Kursname', 'Kursname fehlt', 'required');
	    $this->form_validation->set_rules('kurs_kurz', 'Abkürzung fehlt', 'required');
	    $this->form_validation->set_rules('Creditpoints', 'Creditpoints fehlen oder nicht numerisch', 'required|numeric');
	    $this->form_validation->set_rules('SWS_Vorlesung', 'SWS-Vorlesung nicht numerisch', 'numeric');
	    $this->form_validation->set_rules('SWS_Uebung', 'SWS-Übung nicht numerisch', 'numeric');
	    $this->form_validation->set_rules('SWS_Praktikum', 'SWS-Praktikum nicht numerisch', 'numeric');
	    $this->form_validation->set_rules('SWS_Projekt', 'SWS-Projekt nicht numerisch', 'numeric');
	    $this->form_validation->set_rules('SWS_Seminar', 'SWS-Seminar nicht numerisch', 'numeric');
	    $this->form_validation->set_rules('SWS_SeminarUnterricht', 'SWS-SeminarUnterricht nicht numerisch', 'numeric');
	    
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$this->show_stdgng_course_list($stdgng_id);
	    } else {
		$this->save_stdgng_new_course();
	    }
	}
	
	/**
	 * Saving all Values from $_POST after submit button has been clicked.
	 */
	function save_stdgng_course_changes(){
		
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

	    // TODO handle checkboxes different - values only submitted when checked
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
	    $stdgng_ids = $this->admin_model->getStdgngCourseIds(
		    $this->input->post('stdgng_id'));

	    // get values of nested object - KursIds - to run through the ids and update records
	    foreach ($stdgng_ids as $si){
		$stdgng_id_values[] = $si->KursID;
	    }

	    // run through all course-ids that belong to a single Studiengang, build data-array for updating records in db
	    // AND update data for every id
	    foreach($stdgng_id_values as $id){
		$update_stdgng_data = array(); // init
		// produces an array holding db-keys as keys and data as values
		for ($i = 0; $i < count($update_fields); $i++){
		    $update_stdgng_data[$update_fields[$i]] = $this->input->post($id.$update_fields[$i]);
		}
		// call function in model to update records
		$this->admin_model->update_stdgng_courses($update_stdgng_data, $id);

		$cb_data = array(); // init
		$tmp_cb_data = array(); // init
		// handle checkboxes
		foreach ($update_checkboxes as $value) {
		    if($this->input->post($id.$value) === '1'){
			$split = explode('_', $value); // second value is exam-type-id
			$tmp_cb_data['KursID'] = $id;
			$tmp_cb_data['PruefungstypID'] = $split[1];
			// build array to save data
			$cb_data[] = $tmp_cb_data;
		    }
		}
		// save cb-data to db - passed array contains all checkboxes that have to be stored
		$this->admin_model->save_exam_types_for_course($id, $cb_data);
	    }

	    // show StudiengangDetails-List again
	    $this->show_stdgng_course_list();	
	}
	
	
	/**
	 * Save all fields (studiengang) - getting data from $_POST, after button-click
	 */
	function save_stdgng_details_changes(){
	    $updateFields = array(
		'Pruefungsordnung',
		'StudiengangName',
		'StudiengangAbkuerzung',
		'Regelsemester',
		'Creditpoints',
		'Beschreibung'
	    );

	    // get value via hidden field
	    $stdgngId = $this->input->post('stdgng_id');

	    // run through fields and produce an associative array holding keys and values - $_POST
	    for($i = 0; $i < count($updateFields); $i++){
		$updateStdgngDescriptionData[$updateFields[$i]] = $_POST[$stdgngId.$updateFields[$i]];
	    }

	    // save data
	    $this->admin_model->update_stdgng_description_data($updateStdgngDescriptionData, $stdgngId);

	    // show StudiengangDetails-List again
	    $this->show_stdgng_course_list();
		
	}
	
	/**
	 * After validation, new course is saved here.
	 */
	function save_stdgng_new_course(){
	    $new_course = array();
	    $new_course = $this->input->post();
	    
	    // data
	    $course_data = array();
	    $exam_data = array();
	    
	    // run through data and prepare for saving
	    foreach ($new_course as $key => $value) {
		// if not submit-button-data
		if($key != 'save_new_course'){
		    // and not exam-data
		    if(!strstr($key, 'ext')){
			$course_data[$key] = $value;
		    } else {
			// exam data to separate array
			$exam_data[$key] = $value;
		    }
		}
	    }
	    
	    // insert course-data into db
	    $this->admin_model->insert_new_course($course_data, $exam_data, $new_course['StudiengangID']);
	    
	    //back to view
	    $this->show_stdgng_course_list();
	}
	
	
	/**
	 * Deletes single course from studiengangkurs-table
	 * called from stdgng_edit view after user confirmed
	 * deletion with click on OK in confirmation-dialog
	 */
	function ajax_delete_single_course_from_stdgng(){
	   $delete_course_id =  $this->input->post('delete_course_id');
	   
	   $split = explode('_', $delete_course_id);
	   
	   // TODO delete course with $split[0] - course id
	   $this->admin_model->delete_stdgng_single_course($split[0]);
	   
	   // call view with updated data	   
	   echo $this->ajax_show_courses_of_stdgng($split[1]);
	}
	
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
	
	function show_stdplan_list($reload = 0){
	    // get all stdplan-data
	    $this->data->add('all_stdplan_filterdata', $this->admin_model->get_stdplan_filterdata());

	    // no autoreload without validation
	    $this->data->add('stdplan_id_automatic_reload', $reload);

	    $siteinfo = array(
		'title' => 'Stundenplan anzeigen',
		'main_content' => 'admin_stdplan_edit'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('includes/template', $this->data->load());
	}
	
	
	/**
	 * Returns an div holding the stdgng-table for a specified stdgng >> $this->input->get('stdplan_id')
	 * !! combined id: StudiengangAbkuerzung, Semester, PO
	 */
	function ajax_show_events_of_stdplan(){
	    $ids = $this->input->post('stdplan_ids');
//	    $ids = "BMI_2_2010";
	    
	    // get all events of a stundenplan specified by stdgng-abk., semester, po
	    $splitted_ids = explode("_", "$ids");
	    $data['kurs_ids_split'] = $splitted_ids;
	    $stdplan_events_of_id = $this->admin_model->get_stdplan_data($splitted_ids);
	    
	    // get dropdown-data: all event-types, profs, times, days
	    $eventtypes = $this->admin_model->get_eventtypes();
	    $all_profs = $this->admin_model->get_profs_for_stdplan_list();
		$times = $this->admin_model->get_start_end_times(); // also used to select active option
	    $days = $this->admin_model->get_days(); // also used to select active option
	    $colors = $this->admin_model->get_colors_from_stdplan();
		
		// getting data directly from helper_model - not implemented for all dropdowns
		$starttimes_dropdown_options = $this->helper_model->get_dropdown_options('starttimes');
		$endtimes_dropdown_options = $this->helper_model->get_dropdown_options('starttimes');
		$days_dropdown_options = $this->helper_model->get_dropdown_options('starttimes');
		
	    // save dropdown-data into $data
	    $data['eventtypes'] = $eventtypes;
	    $data['all_profs'] = $all_profs;
	    $data['times'] = $times;
	    $data['days'] = $days;
	    $data['colors'] = $colors;
	    
//	    echo '<pre>';
//	    print_r($all_profs[$i]->DozentID);
//	    echo '</pre>';
	    
	    // and prepare for dropdowns
	    // evenettypes
	    for($i = 0; $i < count($eventtypes); $i++){
		if($i != 0){
			$eventtype_dropdown_options[$i] = $eventtypes[$i]->VeranstaltungsformName;
		} else {
			$eventtype_dropdown_options[$i] = '';
		}
	    }
	    // profs
	    for($i = 0; $i < count($all_profs); $i++){
		if($i != 0){
			$profs_dropdown_options[$all_profs[$i]->DozentID] =
				$all_profs[$i]->Nachname.', '.$all_profs[$i]->Vorname;
		} else {
			$profs_dropdown_options[$i] = '';
		}
		}
		
		// start/endtimes
		for($i = 0; $i < count($times); $i++){
			if($i != 0){
				$starttimes_dropdown_options[$i] = $times[$i]->Beginn;
				$endtimes_dropdown_options[$i] = $times[$i]->Ende;
			} else {
				$starttimes_dropdown_options[$i] = '';
				$endtimes_dropdown_options[$i] = '';
			}
		}
		
		// days
		for($i = 0; $i < count($days); $i++){
			if($i != 0){
				$days_dropdown_options[$i] = $days[$i]->TagName;
			} else {
				$days_dropdown_options[$i] = '';
			}
		}
		
	    // colors
	    for($i = 0; $i < count($colors); $i++){
		if($i != 0){
			$colors_dropdown_options[$i] = $colors[$i]->Farbe;
		} else {
			$colors_dropdown_options[$i] = '';
		}
	    }

	    // save dropdown options into $data
	    $data['eventtype_dropdown_options'] = $eventtype_dropdown_options;
	    $data['profs_dropdown_options'] = $profs_dropdown_options;
	    $data['starttimes_dropdown_options'] = $starttimes_dropdown_options;
	    $data['endtimes_dropdown_options'] = $endtimes_dropdown_options;
	    $data['days_dropdown_options'] = $days_dropdown_options;
	    $data['colors_dropdown_options'] = $colors_dropdown_options;
	    
	    
	    foreach ($stdplan_events_of_id as $sp_events){
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
		$rows[] = $this->load->view('admin-subviews/admin_stdplan_coursetable_row', $data, TRUE);
		
	    }
	    
	    $data['stdplan_course_rows'] = $rows;
	    
	    echo $this->load->view('admin-subviews/admin_stdplan_coursetable_content', $data, TRUE);
	    
	}

	function validate_stdplan_changes(){
	    
	    // get all course-ids belonging to a specified stdgng
	    $stdplan_id = array(
		$this->input->post('stdplan_id_abk'),
		$this->input->post('stdplan_id_sem'),
		$this->input->post('stdplan_id_po'));
	    
	    $stdplan_course_ids = $this->admin_model->get_stdplan_course_ids($stdplan_id);
	    
//	    echo '<pre>';
//	    echo print_r($stdplan_course_ids);
//	    echo '</pre>';
	    
	    foreach($stdplan_course_ids as $id){
		// run through all ids and generate id-specific validation-rules
		$this->form_validation->set_rules(
			$id->SPKursID.'_VeranstaltungsformID', 'Fehler', 'greater_than[0]');
		$this->form_validation->set_rules(
			$id->SPKursID.'_Raum', 'Fehler', 'required');
		$this->form_validation->set_rules(
			$id->SPKursID.'_DozentID', 'Fehler', 'greater_than[0]');
		$this->form_validation->set_rules(
			$id->SPKursID.'_StartID', 'Fehler', 'greater_than[0]');
		$this->form_validation->set_rules(
			$id->SPKursID.'_EndeID', 'Fehler', 'greater_than[0]');
		$this->form_validation->set_rules(
			$id->SPKursID.'_TagID', 'Fehler', 'greater_than[0]');
		$this->form_validation->set_rules(
			$id->SPKursID.'_Farbe', 'Fehler', 'greater_than[0]');
	    }
	    
	    $stdplan_id_automatic_reload = $stdplan_id[0].'_'.$stdplan_id[1].'_'.$stdplan_id[2];
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$this->show_stdplan_list($stdplan_id_automatic_reload);
	    } else {
		$this->save_stdplan_changes();
	    }
	}
	
	/**
	 * Updates data of Stdplan
	 */
	function save_stdplan_changes(){
	    // build an array, containing all keys that have to be updated in db
	    $updateFields = array(
		'VeranstaltungsformID',
		'VeranstaltungsformAlternative',
		'WPFName',
		'Raum',
		'DozentID',
		'StartID',
		'EndeID',
		'isWPF',
		'TagID',
		'Farbe');
	    
//	    // get data to convert $_POST-data into ids
//	    $eventtypes = $this->admin_model->get_eventtypes();
//	    $all_profs = $this->admin_model->get_profs_for_stdplan_list();
//	    $times = $this->admin_model->get_start_end_times();
//	    $days = $this->admin_model->get_days();
//	    $colors = $this->admin_model->get_colors_from_stdplan();
	    
	    // get data from form-submission
	    $post_data = $this->input->post();
	    
	    $update_data = null;
	    
	    // run through data and generate a associative array per id
	    // holding collumns as keys and data as values
	    foreach($post_data as $key => $value){
		if($key != 'savestdplanchanges'){
		    // don't save name of submit-button
		    $split = explode('_', $key);
		    // don't save color >>>>>>>>>> TODO
		    if($split[1] != 'Farbe'){
			$update_data[$split[0]][$split[1]] = $value;
		    }
		    if ($split[1] == 'isWPF'){
			$update_data[$split[0]][$split[1]] = 1;
		    }
		}
		// update data in db - for every 
		$this->admin_model->update_stdplan_details($update_data[$split[0]], $split[0]);
	    }
//	    
//	    echo '<pre>';
//	    print_r($update_data);
//	    echo '</pre>';
	    
	    $this->show_stdplan_list();
	}
	
	
	function delete_stdplan_view(){
	    $this->data->add('delete_view_data', $this->admin_model->get_stdplan_filterdata_plus_id());
	    
	    $siteinfo = array(
		'title' => 'Stundenplan löschen',
		'main_content' => 'admin_stdplan_delete'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('includes/template', $this->data->load());
	}
	
	
	function delete_stdplan(){
	    
	    // get data from post
	    $stdgng_ids = array(
		$this->input->post('stdplan_abk'),
		$this->input->post('stdplan_semester'),
		$this->input->post('stdplan_po'),
	    );

	    // delete all data related to chosen stdplan
	    $this->admin_model->delete_stdplan_related_records($stdgng_ids);
	    
	    // reload view
	    $this->delete_stdplan_view();
	    
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
	
	function import_stdplan_view($error = ''){
	    
	    $this->load->helper('directory');
	    
	    $this->data->add('error', $error);
	    // get files from upload-folder
	    $upload_dir = directory_map('./resources/uploads');
	    // get stdgnge
	    $stdgnge = $this->admin_model->getAllStdgnge();
	    $data['stdgng_uploads'] = '';
	    
	    $last_id = 0;
	    
	    
	    // prepare data for view
	    // generate array, that contains all 
	    foreach($stdgnge as $sg){
		$po = $sg->Pruefungsordnung;
		$abk = $sg->StudiengangAbkuerzung;
		$id = $sg->StudiengangID;
		$data['stdgng_uploads_headlines'][$id] = $abk.' - '.$po.':';
		// run through dirs and distribute found data to view-array
		foreach($upload_dir as $dir){
		    $needle_po = strstr($dir, $po);
		    $needle_abk = strstr($dir, $abk);
		    if($needle_po != null && $needle_abk != null){
			$data['stdgng_uploads'][$id][] = $dir;
		    }
		}
		$last_id = $id;
	    }
	    
	    if($data['stdgng_uploads'] != null){
		// prepare data to 
		foreach($data['stdgng_uploads'] as $nested_array){
		    foreach($nested_array as $file){
			$files_with_po[] = $file;
		    }
		}

    //	    echo '<pre>';
    //	    print_r($clean);
    //	    echo '</pre>';  

		// one additional field for other
		$data['stdgng_uploads_headlines'][42] = 'Andere:';
		// check if there are dirs, that don't belong to a po
		// i.e. not in array, that contains the files that are already shown
		foreach($upload_dir as $dir){
		    if(!in_array($dir, array_values($files_with_po))){
			$data['stdgng_uploads'][42][] = $dir;
		    }
		}
	    }
	    
//	    $this->data->add('stdgng_uploads_headlines', $data['stdgng_uploads_headlines']);
//	    $this->data->add('stdgng_uploads', $data['stdgng_uploads']);
	    $this->data->add('stdgng_uploads_list_filelist', $this->load->view(
		    'admin-subviews/admin_stdplan_import_filelist', $data, TRUE));
	    
	    $siteinfo = array(
		'title' => 'Stundenplan importieren',
		'main_content' => 'admin_stdplan_import'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('includes/template', $this->data->load());
	}
	
	
	function import_stdplan_and_parse(){
	    $config['upload_path'] = './resources/uploads/';
	    $config['allowed_types'] = 'xml';

	    $this->load->library('upload', $config);
	    $this->upload->initialize($config);
//	    $this->load->controller('stdplan_parser');
	    $this->load->model('admin_model_parsing');

	    if ( ! $this->upload->do_upload()){
		// VIEW
		$this->import_stdplan_view($this->upload->display_errors());
	    } else {
		$upload_data = $this->upload->data();
		
		$this->data->add('upload_data', $upload_data);
		
		// start parsing stdplan
//		$returned = $this->stdplan_parser->parse_stdplan($data['upload_data']);
		$this->admin_model_parsing->parse_stdplan($upload_data);
		
//		echo '<pre>';
//		print_r($returned);
//		echo '</pre>';  

		// VIEW
		$siteinfo = array(
		    'title' => 'Stundenplan importieren',
		    'main_content' => 'admin_stdplan_import_success'
		);
		$this->data->add('siteinfo', $siteinfo);

		$this->load->view('includes/template', $this->data->load());
	    }
	}
	
	
	function delete_stdplan_file(){
	    $file_to_delete = $this->input->post('std_file_to_delete');
	    
	    // delete file
	    unlink('./resources/uploads/'.$file_to_delete);
	    
	    $this->import_stdplan_view();
	}
	
	/* 
	 * 
	 * *********************************** Stundenplanimport
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
}