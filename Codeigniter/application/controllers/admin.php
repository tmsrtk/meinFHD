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

		//// data
		// userdata
		$session_userid = 1357;

		$loginname = $this->admin_model->get_loginname($session_userid); 				///////////////////////////////
		$user_permissions = $this->admin_model->get_all_userpermissions($session_userid);
		$roles = $this->admin_model->get_all_roles();
		
		$userdata = array(
				'userid' => $session_userid,
				'username' => $loginname['LoginName'],
				'userpermissions' => $user_permissions,
				'roles' => $roles
			);

		$this->data->add('userdata', $userdata);


	}
	
	
	function index(){
				
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
		
		// Speichern weiterer Daten die in der View benötigt werden in das Data-Array 
		$data['roleCounter'] = $this->admin_model->countRoles(); // Zur Anwendung des Modulo
		$data['roles'] = $this->roles; // Permission-Objekte (ID und Bezeichnung)
		$data['permissions'] = $this->permissions; // Permission-Objekte (ID und Bezeichnung)  
		
// 		echo '<pre>';
// 		print_r($data);
// 		echo '</pre>';
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Rollenverwaltung';
		$data['main_content'] = 'admin_rollenverwaltung';
		
		$this->load->view('includes/template', $data);
		
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
		$data['title'] = 'Benutzer erstellen';
		$data['main_content'] = 'admin_create_user_mask';

		$data['global_data'] = $this->data->load();
		//----------------------------------------------------------------------

		// all studiengänge
		$data['studiengaenge'] = $this->admin_model->get_all_studiengaenge();

		//----------------------------------------------------------------------
		$this->load->view('includes/template', $data);
	}

	/*
	* loads content for the admin_edit_user_mask.php
	*/
	public function edit_user_mask()
	{
		$data['title'] = 'Benutzer anzeigen';
		$data['main_content'] = 'admin_edit_user_mask';

		$data['global_data'] = $this->data->load();
		//----------------------------------------------------------------------

		// all users
		$data['user'] = $this->admin_model->get_all_user();
		

		//----------------------------------------------------------------------
		$this->load->view('includes/template', $data);
	}

	/*
	* loads content for the admin_delete_user_mask.php
	*/
	public function delete_user_mask()
	{
		$data['title'] = 'Benutzer loeschen';
		$data['main_content'] =  'admin_delete_user_mask';

		$data['global_data'] = $this->data->load();
		//----------------------------------------------------------------------
		$data['user'] = $this->admin_model->get_all_user();
		//----------------------------------------------------------------------
		$this->load->view('includes/template', $data);
	}

	/*
	* loads content for the admin_show_permissions.php
	*/
	public function show_permissions()
	{
		$data['title'] = 'Benutzerrechte anzeigen';
		$data['main_content'] =  'admin_show_permissions';

		$data['global_data'] = $this->data->load();
		//----------------------------------------------------------------------
		// everything that is needed is in the global_data var
		//----------------------------------------------------------------------
		$this->load->view('includes/template', $data);
	}





	// action controller =======================================================

	/*
	* creates a new user 
	*/
	public function create_user()
	{
		
		// get values from post
		$form_data = $this->input->post();
		// delete last element (submit button value, not needet for db user save)
		array_pop($form_data);
		// var_dump($form_data);

		// save new user in db
		$this->admin_model->save_new_user($form_data);

		// // redirect to mask again
		// redirect(site_url().'admin/create_user_mask');
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
		// 1. name, 2. human name in errors messages, 3. validation rules
		// $this->form_validation->set_rules('username', 'Benutzername', 'required');
		// $this->form_validation->set_rules('email', 'E-Mail', 'required');

		$rules = array(
				array(
					'field' => 'username',
					'label' => 'Benutzername',
					'rules' => 'required|alpha_dash|min_length[4]|max_length[20]|is_unique[benutzer.LoginName]'
				),
				array(
					'field' => 'email',
					'label' => 'E-Mail',
					'rules' => 'required|valid_email|is_unique[benutzer.Email]'
				),
				array(
					'field' => 'forename',
					'label' => 'Vorname',
					'rules' => ''
				),
				array(
					'field' => 'lastname',
					'label' => 'Nachname',
					'rules' => ''
				)
			);
		$this->form_validation->set_rules($rules);

		// which role was selected?
		$role = $this->input->post('rolle_dd');

		// depending on role, different validations
		// if student
		if ($role === '4'/*student*/)
		{
			// additional validation rules for the student role
			// $this->form_validation->set_rules('matrikelnummer', 'Matrikelnummer', 'required');
			// $this->form_validation->set_rules('startjahr', 'Startjahr', 'required');
			// $this->form_validation->set_rules('semester_def', 'Semesterperiode', 'required');

			$rules = array(
				array(
					'field' => 'matrikelnummer',
					'label' => 'Matrikelnummer',
					'rules' => 'integer|exact_length[6]|is_unique[benutzer.Matrikelnummer]'
				),
				array(
					'field' => 'startjahr',
					'label' => 'Startjahr',
					'rules' => 'integer|exact_length[4]'
				),
				array(
					'field' => 'semester_def',
					'label' => 'Semesterperiode',
					'rules' => ''
				)
			);
			$this->form_validation->set_rules($rules);
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

			// load new view with success message
			$data['title'] = 'Erfolgreich';
			$data['main_content'] = 'admin_create_user_success';

			$data['global_data'] = $this->data->load();

			$this->load->view('includes/template', $data);
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
				$this->validate_edits();
				break;
			case '1':
				$this->reset_pw();
				break;
			case '2':
				$this->reset_semesterplan();
				break;
			case '3':
				$this->login_as();			
				break;

			default:
				# code...
				break;
		}
	}

	/**/
	function validate_edits()
	{
		$rules = array();

		// values, from actual form
		$new_form_values = $this->input->post();

		// current db user data
		$current_user_data = $this->admin_model->get_user_by_id($this->input->post('user_id'));

		// check if current value is different from the value in db
		if ($current_user_data['LoginName'] != $new_form_values['loginname']) 
		{
			// add the rules, if there was a change
			$new_rule = array(
				'field' => 'loginname',
				'label' => 'Benutzername',
				'rules' => 'required|alpha_dash|min_length[4]|max_length[20]|is_unique[benutzer.LoginName]'
			);
			// push value to global rules var
			array_push($rules, $new_rule);
		}

		// same procedure for the other form inputs
		if ($current_user_data['Email'] != $new_form_values['email']) 
		{
			// add the rules, if there was a change
			$new_rule = array(
				'field' => 'email',
				'label' => 'E-Mail',
				'rules' => 'required|valid_email|is_unique[benutzer.Email]'
			);
			// push value to global rules var
			array_push($rules, $new_rule);
		}

		// even if these fields do not need any validation rules, they have to be set, otherwise
		// they are not avaliable after the ->run() method
		if ($current_user_data['Vorname'] != $new_form_values['forename'])
		{
			$new_rule = array(
				'field' => 'forename',
				'label' => 'Vorname',
				'rules' => ''
			);
			array_push($rules, $new_rule);
		}

		if ($current_user_data['Nachname'] != $new_form_values['lastname'])
		{
			$new_rule = array(
				'field' => 'lastname',
				'label' => 'Nachname',
				'rules' => ''
			);
			array_push($rules, $new_rule);
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
	function reset_pw()
	{
		// values, from actual form inputs
		$new_form_values = $this->input->post();

		$data = array(
				'Passwort' => $this->passwort_generator()
			);

		$this->admin_model->update_user($new_form_values['user_id'], $data);

		redirect(site_url().'admin/edit_user_mask');
	}

	/* creates a random pw with a length of 10 chars - jochens function */
	function passwort_generator() 
	{
	
		$laenge = 10;
		$string = md5((string)mt_rand() . $_SERVER["REMOTE_ADDR"] . time());
		  
		$start = rand(0,strlen($string)-$laenge);
		 
		$password = substr($string, $start, $laenge);
		 
		return md5($password);
	}

	/*
	* deletes an user by his id
	*/
	public function delete_user()
	{
		// 
		$user_id = $this->input->post('user_id');

		$this->admin_model->model_delete_user($user_id);

		redirect(site_url().'admin/delete_user_mask');
	}

	/*
	* builds the needed html markup an content (db) from incoming ajax request
	*/
	public function ajax_show_user()
	{
		// get value
		$role_id = $this->input->get('role_id');
		$searchletter = $this->input->get('searchletter');

		$q = $this->admin_model->get_user_per_role_searchletter($role_id, $searchletter);

		$result = '';

		foreach ($q as $key => $value) {
			$result.="<tr>";

			$attrs = array('id' => 'edit_user_row');
			$result.=form_open('admin/validate_edit_user_form/', $attrs);
			$result.=form_hidden('user_id', $value['BenutzerID']);
			// hidden input for deciding the clicked button !!!!!!!!!!!!!!!!!!!!!! <
			$result.=form_hidden('action_to_perform', '0');

			$data = array(
					'class' => 'span2',
					'name' => 'loginname',
					'placeholder' => 'kein Eintrag',
					'value' => $value['LoginName']
				);
			$result.="<td>".form_input($data)."</td>";

			$data = array(
					'class' => 'span2',
					'name' => 'lastname',
					'placeholder' => 'kein Eintrag',
					'value' => $value['Nachname']
				);
			$result.="<td>".form_input($data)."</td>";

			$data = array(
					'class' => 'span2',
					'name' => 'forename',
					'placeholder' => 'kein Eintrag',
					'value' => $value['Vorname']
				);
			$result.="<td>".form_input($data)."</td>";

			$data = array(
					'class' => 'span2',
					'name' => 'email',
					'placeholder' => 'kein Eintrag',
					'value' => $value['Email']
				);
			$result.="<td>".form_input($data)."</td>";

			// function dropdown
			$class_dd = 'id="user_function" class="span2"';
			$dropdown_data = array('Speichern', 'Passwort resetten', 'Stundenplan resetten', 'Als ... anmelden');

			$result.="<td>".form_dropdown('user_function', $dropdown_data, '0', $class_dd)."</td>";

			$submit_data = array(
					'id' 			=> 'save',
					'name'			=> 'submit',
					'class'			=> 'btn btn-mini btn-danger'
				);
			$result.="<td>".form_submit($submit_data, 'LOS!')."</td>";
			

			$result.=form_close();

			$result.="</tr>";
		}

		echo $result;
	}

	/*
	* User management
	* 
	* Konstantin Voth
	***************************************************************************/
	
	
	
	/* *****************************************************
	 * ************** Studiengangverwaltung Anfang *********
	* *****************************************************/
	
	/**
	 * Get all data for a selectable (dropdown) list of Studiengänge
	 * TODO have to be dynamic - right now - static stdgng_id !!!
	 */
	function show_stdgng_course_list(){
		
		// get all stdgnge for filter-view
		$data['all_stdgnge'] = $this->admin_model->getAllStdgnge();
		// set stdgng_id to 0 - indicates, that view has been loaded directly from controller
		// no autoreload without validation
		$data['stdgng_id_automatic_reload'] = 0;
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengangverwaltung';
		$data['main_content'] = 'admin_stdgng_edit';
		
		$this->load->view('includes/template', $data);
		
	}
	
	/**
	 * Show page with empty inpuf-fields 
	 */
	function create_new_stdgng(){
				
		// get all stdgnge for the view
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Neuen Studiengang anlegen';
		$data['main_content'] = 'admin_stdgng_createnew';
		
		$this->load->view('includes/template', $data);
		
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
		$this->save_stdgng_details_changes();
	    }
	}
	
	
	/**
	 * Insert new entry into db with given values ($_POST)
	 */
	function save_new_created_stdgng(){
		// check if given name and version are already used - in this case return show errormessage
		
		
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
		
// 		echo '<pre>';
// 		print_r($insertNewStdgng);
// 		echo '</pre>';
		
		
	}
	
	
	
	
	/**
	 * Shows list of all stdgng to give the opportunity to delete them
	 */
	function delete_stdgng_view(){
		// get all stdgnge for the view
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengang löschen';
		$data['main_content'] = 'admin_stdgng_delete';
		
		$this->load->view('includes/template', $data);
		
	}
	
	/**
	 * Returns an div holding the stdgng-table for a specified stdgng
	 * >> $this->input->get('stdgng_id')
	 */
	function ajax_show_courses_of_stdgng(){

		// get submitted data - AJAX
		$stdgng_chosen_id = $this->input->get('stdgng_id');
		$courses_of_single_stdgng = $this->admin_model->
			get_stdgng_courses($stdgng_chosen_id);
		
		$details_of_single_stdgng = $this->admin_model->
			get_stdgng_details_asrow($stdgng_chosen_id);
	
		// get number of semesters and prepare data for dropdown
		$regelsemester = $details_of_single_stdgng->Regelsemester;
		for($i = 0; $i <= $regelsemester; $i++){
		    if($i != 0){
			    $semester_dropdown_options[$i] = $i;
		    } else {
			    $semester_dropdown_options[$i] = '';
		    }
	        }
		
//		echo '<pre>';
//		print_r($flag);
//		echo '</pre>';
		
		// fill first element of object-array with default-values -
		// >> necessary because first line of table view should be
		// for creation of new courses
		// only KursID is needed, because creation of input-fields grabs
		// KursID to generate unique names
		$courses_of_single_stdgng[0]->KursID = '0';
		$courses_of_single_stdgng[0]->Kursname = '';
		$courses_of_single_stdgng[0]->kurs_kurz = '';
		$courses_of_single_stdgng[0]->Creditpoints = '';
		$courses_of_single_stdgng[0]->SWS_Vorlesung = '';
		$courses_of_single_stdgng[0]->SWS_Uebung = '';
		$courses_of_single_stdgng[0]->SWS_Praktikum = '';
		$courses_of_single_stdgng[0]->SWS_Projekt = '';
		$courses_of_single_stdgng[0]->SWS_Seminar = '';
		$courses_of_single_stdgng[0]->SWS_SeminarUnterricht = '';
		$courses_of_single_stdgng[0]->Semester = '';
		$courses_of_single_stdgng[0]->Beschreibung = '';
		
		//for each record - print out table-row with form-fields
		foreach($courses_of_single_stdgng as $sd){
		    // build a table-row for each course
		    $data['KursID'] = $sd->KursID;
		    $data['Kursname'] = $sd->Kursname;
		    $data['kurs_kurz'] = $sd->kurs_kurz;
		    $data['Creditpoints'] = $sd->Creditpoints;
		    $data['SWS_Vorlesung'] = $sd->SWS_Vorlesung;
		    $data['SWS_Uebung'] = $sd->SWS_Uebung;
		    $data['SWS_Praktikum'] = $sd->SWS_Praktikum;
		    $data['SWS_Projekt'] = $sd->SWS_Projekt;
		    $data['SWS_Seminar'] = $sd->SWS_Seminar;
		    $data['SWS_SeminarUnterricht'] = $sd->SWS_SeminarUnterricht;
		    $data['SemesterDropdown'] = $semester_dropdown_options;	// array holding all dropdown-options
		    $data['Semester'] = $sd->Semester;
		    $data['Beschreibung'] = $sd->Beschreibung;
		    
		    // array holding all rows
		    $rows[] = $this->load->view('admin-subviews/admin_stdgng_coursetable_row', $data, TRUE);
		}
		
		
		// make data available in view
		$data['stdgng_details'] = $details_of_single_stdgng;
		$data['stdgng_course_rows'] = $rows;
		$data['stdgng_id'] = $stdgng_chosen_id;
		
		// return content
		$result = '';
		$result .= $this->load->view('admin-subviews/admin_stdgng_description', $data, TRUE);
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
	    
	    // TODO??? PO, Name, Abk. Kombi muss unique sein
	    
	    
	    
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
	    
	    $data['stdgng_id_automatic_reload'] = $stdgng_id;
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengang Kursliste bearbeiten';
		$data['main_content'] = 'admin_stdgng_edit';
		
		$this->load->view('includes/template', $data);
	    } else {
		$this->save_stdgng_details_changes();
	    }
	}
	
	
	function validate_stdgng_course_changes(){
	    
	    // get all stdgnge for filter-view
	    $data['all_stdgnge'] = $this->admin_model->getAllStdgnge();
	    
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
		$this->form_validation->set_rules(
			$id->KursID.'Semester', 'Wählen Sie ein Semester aus - ID: '.$id->KursID, 'greater_than[0]');
	    }
	    
	    $data['stdgng_id_automatic_reload'] = $stdgng_id;
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengang Kursliste bearbeiten';
		$data['main_content'] = 'admin_stdgng_edit';
		
		$this->load->view('includes/template', $data);
	    } else {
		$this->save_stdgng_course_changes();
	    }
	}
	
	/**
	 * Saving all Values from $_POST after submit button has been clicked.
	 */
	function save_stdgng_course_changes(){
		
		// TODO react on KursID 0 >> CREATE NEW COURSE
		// TODO validation incoming data - specially when course is created
	    	
//		echo '<pre>';
//		print_r($this->input->post());
//		echo '</pre>';
		
		// build an array, containing all keys that have to be updated in db
		$updateFields = array(
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
		
		// get ids of a single studiengang - specified by id
		$stdgngIds = $this->admin_model->getStdgngCourseIds(
			$this->input->post('stdgng_id'));
				
		// get values of nested object - KursIds - to run through the ids and update records
		foreach ($stdgngIds as $si){
		    $stdgngIdValues[] = $si->KursID;
		}
		
		// run through all course-ids that belong to a single Studiengang, build data-array for updating records in db
		// AND update data for every id
		foreach($stdgngIdValues as $id){
		    // produces an array holding db-keys as keys and data as values
		    for ($i = 0; $i < count($updateFields); $i++){
			$updateStdgngData[$updateFields[$i]] = $this->input->post($id.$updateFields[$i]);
		    }
		    // call function in model to update records
		    $this->admin_model->update_stdgng_courses($updateStdgngData, $id);
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
	
	/* *****************************************************
	 * ************** Studienganghandling End **************
	 * *****************************************************/
	
	
	/* *****************************************************
	 * ************** Stundenplanhandling Start ************
	 * *****************************************************/
	
	function show_stdplan_list(){
		// get all stdplan-data
		$data['all_stdplan_filterdata'] = $this->admin_model->get_stdplan_filterdata();
		
		// no autoreload without validation
		$data['stdplan_id_automatic_reload'] = 0;

//		echo '<pre>';
// 		print_r($a);
// 		echo '</pre>';
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Stundenplan anzeigen';
		$data['main_content'] = 'admin_stdplan_edit';
		
		$this->load->view('includes/template', $data);
		
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
	    $times = $this->admin_model->get_start_end_times();
	    $days = $this->admin_model->get_days();
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    
	    // save dropdown-data into $data
	    $data['eventtypes'] = $eventtypes;
	    $data['all_profs'] = $all_profs;
	    $data['times'] = $times;
	    $data['days'] = $days;
	    $data['colors'] = $colors;
	    
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
	    
//	    echo '<pre>';
//	    print_r($data);
//	    echo '</pre>';
	    
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
	    
	    // get all stdplan-data
	    $data['all_stdplan_filterdata'] = $this->admin_model->get_stdplan_filterdata();
	    
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
	    
	    $data['stdplan_id_automatic_reload'] = $stdplan_id[0].'_'.$stdplan_id[1].'_'.$stdplan_id[2];
	    
	    if ($this->form_validation->run() == FALSE) {
		// reload view
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Stundenplan Kursliste bearbeiten';
		$data['main_content'] = 'admin_stdplan_edit';
		
		$this->load->view('includes/template', $data);
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
	    $data['delete_view_data'] = $this->admin_model->get_stdplan_filterdata_plus_id();
	    
	    // VIEW
	    $data['global_data'] = $this->data->load();
	    $data['title'] = 'Stundenplan löschen';
	    $data['main_content'] = 'admin_stdplan_delete';

	    $this->load->view('includes/template', $data);

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
	    
//	    echo '<pre>';
//	    print_r($group_ids);
//	    echo '</pre>';
	}
	
	/* *****************************************************
	 * ************** Stundenplanhandling End **************
	 * *****************************************************/
	
	
}