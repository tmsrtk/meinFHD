<?php

class Admin extends FHD_Controller {
	
	private $permissions;
	private $roles;
	private $roleIds;
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('admin_model');

		// @frank, no need to load it here every time, take a look at autolaod.php
		// $this->load->helper(array('form', 'url'));
		
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
	function showStdgngCourseList(){
		
		// get all stdgnge for filter-view
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengangverwaltung';
		$data['main_content'] = 'admin_stdgng_list';
		
		$this->load->view('includes/template', $data);
		
	}
	
	/**
	 * Show page with empty inpuf-fields 
	 */
	function createNewStdgng(){
				
		// get all stdgnge for the view
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Neuen Studiengang anlegen';
		$data['main_content'] = 'admin_stdgng_createnew';
		
		$this->load->view('includes/template', $data);
		
	}
	
	function showStdgngList(){
		// get all stdgnge for the view
		$data['allStdgnge'] = $this->admin_model->getAllStdgnge();
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Studiengang löschen';
		$data['main_content'] = 'admin_stdgng_delete';
		
		$this->load->view('includes/template', $data);
		
	}
	
	/**
	 * Returns an div holding the stdgng-table for a specified stdgng >> $this->input->get('stdgng_id')
	 */
	function ajax_show_courses_of_stdgng(){

		// get submitted data - AJAX
		$stdgng_chosen_id = $this->input->get('stdgng_id');
		$chosen_stdgng_data = $this->admin_model->getStdgngDetails($stdgng_chosen_id);
		
		$stdgng_details = $this->admin_model->get_stdgng_details_asrow($stdgng_chosen_id);
		$attributes = array('class' => 'listform', 'id' => 'stdgngform');
	
		// get number of semesters an prepare data for dropdown
		$regelsemester = $stdgng_details->Regelsemester;
		for($i = 0; $i <= $regelsemester; $i++){
		    if($i != 0){
			    $semester_dropdown_options[$i] = $i;
		    } else {
			    $semester_dropdown_options[$i] = '';
		    }
	        }
		
//		echo '<pre>';
//		print_r($semester_dropdown_options);
//		echo '</pre>';
		
		// fill first element of object-array with default-values - necessary because first line of table view should be for creation of new course
		// only KursID is needed, because creation of input-fields grabs KursID to generate unique names
		$chosen_stdgng_data[0]->KursID = '0';
		$chosen_stdgng_data[0]->Kursname = '';
		$chosen_stdgng_data[0]->kurs_kurz = '';
		$chosen_stdgng_data[0]->Creditpoints = '';
		$chosen_stdgng_data[0]->SWS_Vorlesung = '';
		$chosen_stdgng_data[0]->SWS_Uebung = '';
		$chosen_stdgng_data[0]->SWS_Praktikum = '';
		$chosen_stdgng_data[0]->SWS_Projekt = '';
		$chosen_stdgng_data[0]->SWS_Seminar = '';
		$chosen_stdgng_data[0]->SWS_SeminarUnterricht = '';
		$chosen_stdgng_data[0]->Semester = '';
		$chosen_stdgng_data[0]->Beschreibung = '';
		
		// init result and put data into a div
		$result = ''; // init
		$result = '<div id="stdgng-change-view"><div id="stdgng-details">';
		
		// first table holding the details of a single stdgng
		$result .= form_open('admin/saveStdgngDescriptionChanges');
		
		    // fields to fill in details-form (Name, Abk., PO, Semester, CP)...
		    $result .= '<div id="stdgng-details-1" style=\'float:left;\'><table></tbody>';
		    

			    foreach ($stdgng_details as $key => $value){
				    if($key == 'StudiengangName' || $key == 'StudiengangAbkuerzung' || $key == 'Pruefungsordnung'
						    || $key == 'Regelsemester' || $key == 'Creditpoints'){

					    $result .= '<tr><td>'.$key.'</td><td>';

					    // get data to display in input-field
					    $inputFieldData = array(
						'name' => ($stdgng_details->StudiengangID).$key,
						'id' => 'input-stdgng-details',
						'value' => $value,
						'rows' => 7,
						'cols' => 40
					    );
					    $result .= form_input($inputFieldData).'</td></tr>';
				    }
			    }
			
		    // ... (Beschreibung) ...
		    $result .= '</tbody></table></div><div id="stdgng-details-2">';
			    $stdgngDetailTextareaData = array(
				'name' => ($stdgng_details->StudiengangID).'Beschreibung',
				'id' => 'input-stdgng-beschreibung',
				'value' => $stdgng_details->Beschreibung,
				'rows' => 7,
				'cols' => 40
			    );
			    $result .= form_textarea($stdgngDetailTextareaData).'</div>';

		    // .. Buttons
		    $result .= '<div id="stdgng-details-3" style=\'clear:both;\'>';
			
		// hidden field to transmit the stdgng-id
		$result .= form_hidden('stdgng_id', $stdgng_chosen_id);
			
		// return submit-button for details
		$btn_attributes = 'class = "btn-warning"';
		$result .= form_submit('save_stdgng_detail_changes', 'Änderungen an den Details speichern', $btn_attributes);
		$result .= form_close();
		
		$result .= '</div>'; // close stdgng-details-3
		
		
		// return dividing div
		$result .= '<div id="stdgng-data-list"  style=\'clear:both; background:#fff;\'>';
		
		// open form
		$result .= form_open('admin/save_stdgng_changes', $attributes);
		
		// second table holding the course-list
		$result .= 
			'<table class="table table-striped table-bordered table-condensed"><thead><tr>
			<th>Kursname:</hd>
			<th>Abk.:</th>
			<th>CP:</th>
			<th>SWS:</th>
			<th>Sem.:</th>
			<th>Beschreibung:</th>
			<th>Aktion:</th>
			</tr></thead>';
		
		// tablebody
		$result .= '<tbody>';
		
		//for each record - print out table-row with form-fields
		foreach($chosen_stdgng_data as $sd){
		
			// building a single row in detail-list-table
			$result .= '<tr>';
			
			// get data and store in associative array to use code-igniters form_input
			$kursnameData = array(
			    'name' => $sd->KursID.'Kursname',
			    'id' => 'Kursname',
			    'value' => $sd->Kursname
			);
			$result .= '<td>'.form_input($kursnameData);
			
			$kursnameKurzData = array(
			    'name' => $sd->KursID.'kurs_kurz',
			    'id' => 'KursnameKurz',
			    'value' => $sd->kurs_kurz,
			    'class' => 'span1'
			);
			$result .= '</td><td>'.form_input($kursnameKurzData);
			
			$creditpointsData = array(
			    'name' => $sd->KursID.'Creditpoints',
			    'id' => 'CP',
			    'value' => $sd->Creditpoints,
			    'class' => 'span1'
			);
			$result .= '</td><td>'.form_input($creditpointsData);
			
			
			// run through all 6 SWS-types and generate data-array for usage with input-field
			// get data for Vorlesung
			if($sd->SWS_Vorlesung === '0'){
				$sd_SWS_Vorlesung = ' ';
			} else {
				$sd_SWS_Vorlesung = $sd->SWS_Vorlesung;
			}
			$swsDataVorl = array(
			    'name' => $sd->KursID.'SWS_Vorlesung',
			    'id' => 'SWS_Vorl',
			    'value' => $sd_SWS_Vorlesung,
			    'class' => 'span1'
			);
			
			// get data for Uebung
			if($sd->SWS_Uebung === '0'){
				$sd_SWS_Uebung = ' ';
			} else {
				$sd_SWS_Uebung = $sd->SWS_Uebung;
			}
			$swsDataUeb = array(
			    'name' => $sd->KursID.'SWS_Uebung',
			    'id' => 'SWS_Ueb',
			    'value' => $sd_SWS_Uebung,
			    'class' => 'span1'
			);
			
			// get data for Praktikum
			if($sd->SWS_Praktikum === '0'){
				$sd_SWS_Praktikum = ' ';
			} else {
				$sd_SWS_Praktikum = $sd->SWS_Praktikum;
			}
			$swsDataPrakt = array(
			    'name' => $sd->KursID.'SWS_Praktikum',
			    'id' => 'SWS_Prakt',
			    'value' => $sd_SWS_Praktikum,
			    'class' => 'span1'
			);
			
			// get data for Projekt
			if($sd->SWS_Projekt === '0'){
				$sd_SWS_Projekt = ' ';
			} else {
				$sd_SWS_Projekt = $sd->SWS_Projekt;
			}
			$swsDataPro = array(
			    'name' => $sd->KursID.'SWS_Projekt',
			    'id' => 'SWS_Pro',
			    'value' => $sd_SWS_Projekt,
			    'class' => 'span1'
			);
			
			// get data for Seminar
			if($sd->SWS_Seminar === '0'){
				$sd_SWS_Seminar = ' ';
			} else {
				$sd_SWS_Seminar = $sd->SWS_Seminar;
			}
			$swsDataSem = array(
			    'name' => $sd->KursID.'SWS_Seminar',
			    'id' => 'SWS_Sem',
			    'value' => $sd_SWS_Seminar,
			    'class' => 'span1'
			);
			
			// get data for Seminarunterricht - ?? // TODO check if this field is still needed / in use?
			if($sd->SWS_SeminarUnterricht === '0'){
				$sd_SWS_SeminarUnterricht = ' ';
			} else {
				$sd_SWS_SeminarUnterricht = $sd->SWS_SeminarUnterricht;
			}
			$swsDataSemU = array(
			    'name' => $sd->KursID.'SWS_SeminarUnterricht',
			    'id' => 'SWS_SemU',
			    'value' => $sd_SWS_SeminarUnterricht,
			    'class' => 'span1'
			);
			
			$result .= '</td><td><table class="">
			    <tbody><tr><td>'.form_input($swsDataVorl);
			$result .= '</td><td>'.form_input($swsDataUeb);
			$result .= '</td><td>'.form_input($swsDataPrakt);
			$result .= '</td><td>'.form_input($swsDataPro);
			$result .= '</td><td>'.form_input($swsDataSem);
			$result .= '</td><td>'.form_input($swsDataSemU);
			$result .= '</td></tr></tbody></table></td><td>';
				
			// output dropdown to choose the Regelsemester in which the course takes place
			$dropdown_attributes = 'class = "span1"';
			$result .= form_dropdown($sd->KursID.'Semester', $semester_dropdown_options,
				$sd->Semester, $dropdown_attributes).'</td><td>';
			
			$textareaData = array(
			    'name' => $sd->KursID.'Beschreibung',
			    'id' => 'Beschreibung',
			    'value' => $sd->Beschreibung,
			    'rows' => 3,
			    'cols' => 5
			);
			$result .= form_textarea($textareaData).'</td><td>';
			
			if($sd->KursID == 0){ 
				$buttonData = array(
				    'name' => $sd->KursID.'createCourse',
				    'id' => 'create_btn_stdgng',
				    'value' => true,
				    'content' => 'Hinzufügen'
				);
			} else {
				$buttonData = array(
				    'name' => $sd->KursID.'deleteCourse',
				    'id' => 'delete_btn_stdgng',
				    'data-id' => $sd->KursID,
				    'value' => true,
				    'content' => 'Löschen'
				);
			}
			// TODO event for button-click - id vergeben und über AJAX
			$result .= form_button($buttonData);	
			
			$result .= '</td></tr>';
			
		}
		
		// close table, table-div and surrounding-div
		$result .= '</tbody></table>';
		
		// hidden field to transmit the stdgng-id
		$result .= form_hidden('stdgng_id', $stdgng_chosen_id);
		
		$btn_attributes = 'class = "btn-warning"';
		$result .= form_submit('save_stdgng_changes', 'Änderungen speichern', $btn_attributes);
		$result .= form_close();
		
		$result .= '</div>';
		
		echo $result;
	}
	
	
	/**
	 * Deltes a whole Stdgng
	 */
	function deleteStdgng() {
		$deleteId = $this->input->post('deleteStdgngId');
		$this->admin_model->deleteStdgng($deleteId);
		
		$this->showStdgngList();
	}
	
	/**
	 * Saving all Values from $_POST after submit button has been clicked.
	 */
	function save_stdgng_changes(){
		
		// TODO react on KursID 0 >> CREATE NEW COURSE
		// TODO validation incoming data - specially when course is created
	    
		
		echo '<pre>';
		print_r($this->input->post());
		echo '</pre>';
		
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
		    $this->admin_model->updateStdgngDetails($updateStdgngData, $id);
		}
		
		// show StudiengangDetails-List again
		$this->showStdgngCourseList();
		
	}
	
	
	/**
	 * Save all fields (studiengangkurs) - getting data from $_POST, after button-click
	 */
	function saveStdgngDescriptionChanges(){
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
		$this->admin_model->updateStdgngDescriptionData($updateStdgngDescriptionData, $stdgngId);
		
		// show StudiengangDetails-List again
		$this->showStdgngCourseList();
		
	}
	
	
	/**
	 * Insert new entry into db with given values ($_POST)
	 */
	function saveNewCreatedStdgng(){
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
		$this->admin_model->createNewStdgng($insertNewStdgng);
		
// 		echo '<pre>';
// 		print_r($insertNewStdgng);
// 		echo '</pre>';
		
		
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

//		echo '<pre>';
// 		print_r($a);
// 		echo '</pre>';
		
		
		// VIEW
		$data['global_data'] = $this->data->load();
		$data['title'] = 'Stundenplan anzeigen';
		$data['main_content'] = 'admin_stdplan_list';
		
		$this->load->view('includes/template', $data);
		
	}
	
	
	/**
	 * Returns an div holding the stdgng-table for a specified stdgng >> $this->input->get('stdplan_id')
	 * !! combined id: StudiengangAbkuerzung, Semester, PO
	 */
	function ajax_show_events_of_stdplan(){
	    $ids = $this->input->post('stdplan_ids');
	    
	    // get all events of a stundenplan specified by stdgng-abk., semester, po
	    $splitted_ids = explode("_", $ids);
	    $stdplan_events_of_id = $this->admin_model->get_stdplan_data($splitted_ids);
	    
	    // get dropdown-data: all event-types, profs, times, days
	    $eventtypes = $this->admin_model->get_eventtypes();
	    $all_profs = $this->admin_model->get_profs_for_stdplan_list();
	    $times = $this->admin_model->get_start_end_times();
	    $days = $this->admin_model->get_days();
	    $colors = $this->admin_model->get_colors_from_stdplan();
	    
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
	    // common dropdown attrs
	    $dropdown_attributes = 'class = "span2"';
	    
	    
//	    echo '<pre>';
//	    print_r($profs_dropdown_options);
//	    echo '</pre>';
	    
	    
	    // init result, table and form
	    $result = '';
	    $result = '<div id="stdplan-change-view">';
	    $result .= form_open('admin/save_stdplan_changes');
	    $result .= 
		'<table class="table table-striped table-bordered table-condensed"><thead><tr>
		<th>Veranstaltungsname:</th>
		<th>Veranstaltungsform:</th>
		<th>Alternative:</th>
		<th>Raum:</th>
		<th>verantw. Lehrkörper:</th>
		<th>Beginn:</th>
		<th>Ende:</th>
		<th>Tag:</th>
		<th>WPF?:</th>
		<th>WPF-Name:</th>
		<th>Farbe:</th>
		<th>Aktion:</th>
		</tr></thead>';
	    
	    // ########## structure of table
	    $result .= '<tbody>';
	    
	    // build first row static - empty values TODO
	    
	    
	    // !! important: to save changed data correctly, name has to consist of SPKursID and the collumn-name in database
	    foreach($stdplan_events_of_id as $sp_events){
//		echo '<pre>';
//		print_r($sp_events);
//		echo '</pre>';
	
		// input-field for course-name - cannot be changed!! --> change via studiengangkurs!!
//		$course_name_data = array(
//		    // attention: changes data in studiengangkurs NOT stundenplankurs!!
//		    'name' => $sp_events->SPKursID.'_'.$sp_events->KursID.'Kursname',
//		    'id' => 'stdplan-list-coursename',
//		    'value' => $sp_events->Kursname
//		);
//		$result .= '<tr><td>'.form_input($course_name_data);
		$course_name_attrs = array(
		    'id' => 'stdplan-list-coursename',
		);
		$result .= '<tr><td>'.form_label($sp_events->Kursname, $course_name_attrs);

		// dropdown for event-types
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_VeranstaltungsformID', $eventtype_dropdown_options,
			$eventtypes[$sp_events->VeranstaltungsformID]->VeranstaltungsformID, $dropdown_attributes);

		// input-field for alternatives
		$eventy_alt_data = array(
		    'name' => $sp_events->SPKursID.'_VeranstaltungsformAlternative',
		    'id' => 'stdplan-list-alternative',
		    'value' => $sp_events->VeranstaltungsformAlternative,
		    'class' => 'span1'
		);
		$result .= '</td><td>'.form_input($eventy_alt_data);

		// room
		$room_data = array(
		    'name' => $sp_events->SPKursID.'_Raum',
		    'id' => 'stdplan-list-room',
		    'value' => $sp_events->Raum,
		    'class' => 'span2'
		);
		$result .= '</td><td>'.form_input($room_data);
		
		// dropdown for profs
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_DozentID', $profs_dropdown_options,
			$sp_events->DozentID, $dropdown_attributes);

		// dropdown for starttime
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_StartID', $starttimes_dropdown_options,
			$times[$sp_events->StartID]->StundeID, $dropdown_attributes);
		
		// dropdown for endtime
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_EndeID', $endtimes_dropdown_options,
			$times[$sp_events->EndeID]->StundeID, $dropdown_attributes);

		// dropdown for day
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_TagID', $days_dropdown_options,
			$days[$sp_events->TagID]->TagID, $dropdown_attributes);

		// checkbox for wpf
		$wpf_cb_data = array(
		    'name' => $sp_events->SPKursID.'_isWPF',
		    'id' => 'stdplan-list-wpfcheckbox',
		    'value' => 'accept',
		    'checked' => ($sp_events->isWPF === '1') ? true : false
		);
		$result .= '</td><td>'.  form_checkbox($wpf_cb_data);
		

		// inputfield for wpf-name
		$wpf_data = array(
		    'name' => $sp_events->SPKursID.'_WPFName',
		    'id' => 'stdplan-list-wpfname',
		    'value' => $sp_events->WPFName,
		    'class' => 'span2'
		);
		$result .= '</td><td>'.form_input($wpf_data);

		// dropdown for color - at first: find out key
		$ck = '';
		foreach ($colors_dropdown_options as $key => $value){
		    if($value == $sp_events->Farbe) {
			$ck = $key;
		    }
		}
		$result .= '</td><td>'.form_dropdown($sp_events->SPKursID.'_'.'Farbe', $colors_dropdown_options,
			$ck, $dropdown_attributes);;
		

		// delete/add button
		if($sp_events->SPKursID == 0){ 
			$buttonData = array(
			    'name' => $sp_events->SPKursID.'createCourse',
			    'id' => 'create_btn_stdpln',
			    'value' => true,
			    'content' => 'Hinzufügen'
			);
		} else {
			$buttonData = array(
			    'name' => $sp_events->SPKursID.'deleteCourse',
			    'id' => 'delete_btn_stdpln',
			    'data-id' => $sp_events->SPKursID,
			    'value' => true,
			    'content' => 'Löschen'
			);
		}
		// TODO event for button-click - id vergeben und über AJAX
		$result .= '</td><td>'.form_button($buttonData);	

		$result .= '</td></tr>';
	    
		
	    } // ende foreach
	    
	    $result .= '</tbody></table>';
	    
	    // submitbutton
	    $btn_attributes = 'class = "btn-warning"';
	    $result .= form_submit('savestdplanchanges', 'Änderungen speichern', $btn_attributes);
	    $result .= form_close().'</div>';
	    
	    
	    echo $result;
	    
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
	
	/* *****************************************************
	 * ************** Stundenplanhandling End **************
	 * *****************************************************/
	
	
}