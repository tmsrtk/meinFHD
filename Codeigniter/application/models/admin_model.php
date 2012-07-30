<?php

class Admin_model extends CI_Model {

	/* ***********************************************************************
	 * 
	 * ********************************* Rechtesystem ANFANG
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	/**
	 * Liefert alle Permissions die in meinFHD gesetzt werden können
	 * @return unknown
	 */
	function getAllPermissions(){
	    $data = array();
	    
	    $q = $this->db->get('berechtigung');

	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	}

	
	/**
	 * Rückgabe alle Rollen denen Nutzer zugeordnet sind
	 * @return unknown
	 */
	function getAllRoles(){
	    $data = array();
	    
	    $q = $this->db->get('rolle');

	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	}
	
	/**
	 * Liefert alle RollenIDs in einem Array zurück.
	 * @return unknown
	 */
	function getAllRoleIds(){
	    $data = array();
	    
	    $q = $this->db->get('rolle');

	    foreach ($q->result_array() as $row){
		$data[] = $row['RolleID'];
	    }
	    return $data;
	}
	
	
	/**
	 * Liefert die Anzahl der angelegten Rollen.
	 */
	function countRoles(){
	    return $this->db->count_all('rolle');
	}

	
	/**
	 * >> Löschen aller Einträge und befüllen mit neuen Daten.
	 */
	function deleteRolePermissions(){
	    $this->db->empty_table('rolle_mm_berechtigung');
	}
	
	
	/**
	 * Update aller RolePermissions
	 * @param unknown_type $rid - RoleIDs
	 * @param unknown_type $pid - PermissionIDs
	 */
	function updateRolePermissions($rp){
	    $this->db->insert('rolle_mm_berechtigung', $rp);
	}
	
		
	/**
	 * Alle Berechtigungen die einer Rolle zugeordnet sind holen
	 * @param unknown_type $rid
	 */
	function getAllRolePermissions($rid){
	    $data = array();
	    
	    $this->db->select('BerechtigungID');
	    $q = $this->db->get_where('rolle_mm_berechtigung', array('RolleID' => $rid));

	    $data[] = null; // necessary?!

	    foreach ($q->result_array() as $row){
		$data[] = $row['BerechtigungID'];
	    }
	    return $data;
	}


	/* 
	 * 
	 * *********************************** Rechtesystem ENDE
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/



	/***************************************************************************
	* User management
	* 
	* Konstantin Voth
	*/



	/*
	*
	*/
	public function request_all_invitations()
	{
		$this->db->select('*')
				 ->from('anfrage')
				 ->order_by('AnfrageID', 'asc');

		$q = $this->db->get();

		return $q->result_array();
	}


	/*
	*
	*/
	public function save_new_user($form_data, $password)
	{
		// prepare data for insert
		$data = array(
				'LoginName' 				=> $form_data['loginname'],
				'Email' 					=> $form_data['email'],
				'Vorname'					=> $form_data['forename'],
				'Nachname' 					=> $form_data['lastname'],
				'Matrikelnummer' 			=> $form_data['matrikelnummer'],
				'StudienbeginnJahr' 		=> $form_data['startjahr'],
				'StudienbeginnSemestertyp' 	=> $form_data['semesteranfang'],
				'StudiengangID' 			=> $form_data['studiengang'],
				'Passwort' 					=> md5($password)
			);

		$this->db->insert('benutzer', $data);

		// query directly the user_id of the created user
		$last_id = mysql_insert_id();

		// insert into benutzer_mm_rolle
		$data = array(
				'BenutzerID' => $last_id,
				'RolleID' => $form_data['role']
			);
		$this->db->insert('benutzer_mm_rolle', $data);
	}

	public function put_new_user_to_invitation_requests($form_data)
	{
		// prepare data for insert
		$data = array(
				'Vorname'					=> $form_data['forename'],
				'Nachname' 					=> $form_data['lastname'],
				'Startjahr'			 		=> $form_data['startjahr'],
				'Matrikelnummer' 			=> $form_data['matrikelnummer'],
				'Emailadresse' 				=> $form_data['email'],
				'Semester'				 	=> $form_data['semesteranfang'],
				'Studiengang' 				=> $form_data['studiengang'],
				'TypID'						=> $form_data['role']
			);

		$this->db->insert('anfrage', $data);
	}

	/*
	*
	*/
	public function save_new_user_from_invitation($invitation_id)
	{

		// query data from invitation_id
		$this->db->select('*')
				 ->from('anfrage')
				 ->where('AnfrageID', $invitation_id);
		$q = $this->db->get()->row_array();

		// generate password
		$password = $this->adminhelper->passwort_generator();

		// prepare data to save
		$data = array(
				'LoginName'					=> $q['Emailadresse'],
				'Vorname'					=> $q['Vorname'],
				'Nachname' 					=> $q['Nachname'],
				'StudienbeginnJahr'	 		=> $q['Startjahr'],
				'Matrikelnummer' 			=> $q['Matrikelnummer'],
				'Email' 					=> $q['Emailadresse'],
				'StudienbeginnJahr'		 	=> $q['Semester'],
				'StudiengangID' 			=> $q['Studiengang'],
				'Passwort'					=> md5($password)
			);

		$this->db->insert('benutzer', $data);

		// query directly the user_id of the created user
		$last_id = mysql_insert_id();

		// insert into benutzer_mm_rolle
		$data = array(
				'BenutzerID' => $last_id,
				'RolleID' => $q['TypID']
			);
		$this->db->insert('benutzer_mm_rolle', $data);

		// TODO: send email to user
		// $message
		// $password


		// delete requested invitation
		$this->_delete_invitation($invitation_id);
	}

	/**
	 *
	 */
	public function _delete_invitation($invitation_id)
	{
		$this->db->where('AnfrageID', $invitation_id);
		$this->db->delete('anfrage'); 
	}
	
	/**
	 *
	 */
	public function get_all_roles()
	{
		// query raw data
		$this->db->select('RolleID, bezeichnung')
				 ->from('rolle');
		$q = $this->db->get();

		// var_dump($q->result_array());

		// edit array to 1dim
		$my_result = array();
		foreach ($q->result_array() as $row)
		{
			// push into result array and uppercase first letter
			// array_push($my_result, ucfirst($row['bezeichnung']));

			// prepare for controller and simultanously for <select><option> tags $key, $value
			$my_result[$row['RolleID']] = $row['bezeichnung'];
		}

		// var_dump($my_result);

		return $my_result;
	}

	public function clear_userroles($user_id)
	{
		if ( ! empty($user_id) )
		{
			$this->db->delete('benutzer_mm_rolle', array('BenutzerID' => $user_id));
		}
	}

	public function save_userrole($user_id, $role)
	{
		if ( ! empty($user_id) && ! empty($role) )
		{
			$data = array(
				'BenutzerID' => $user_id,
				'RolleID' => $role
				);
			// $this->db->where('BenutzerID', $user_id);
			// $this->db->update('benutzer_mm_rolle', $data);

			$this->db->insert('benutzer_mm_rolle', $data);
		}
	}

	/**
	 *
	 */
	public function get_all_studiengaenge()
	{
		// query raw data
		$this->db->select('StudiengangID, StudiengangName')
				 ->from('studiengang');
		$q = $this->db->get();

		// var_dump($q->result_array());

		$my_result = array();
		foreach ($q->result_array() as $row)
		{
			// array_push($my_result, $row['StudiengangName']/*.' '.$row['Pruefungsordnung']*/);
			$my_result[$row['StudiengangID']] = $row['StudiengangName'];
		}

		return $my_result;
	}

	// get all user
	function get_all_user_raw()
	{
		$this->db->select('*')
					->from('benutzer')
					// ->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID')
					// ->limit(50)
					;

		return $this->db->get();
	}

	// get all users as array
	public function get_all_user()
	{
		return $this->get_all_user_raw()->result_array();
	}

	// get specific user
	public function get_user_by_loginname($loginname)
	{
		// if (is_string($loginname))
		// {
			$this->db->select('*')
					 ->from('benutzer')
					 ->where('LoginName', $loginname);
			return $this->db->get()->row_array();
		// }
	}

	public function get_user_by_id($user_id)
	{
		// if (is_string($user_specification))
		// {
			$this->db->select('*')
					 ->from('benutzer')
					 ->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID')
					 ->where('benutzer_mm_rolle.BenutzerID', $user_id);
			return $this->db->get()->row_array();
		// }
	}

	public function get_user_per_role_searchletter($role_id='', $searchstring='')
	{
		$this->db->distinct()
				 ->select('benutzer.*')
				 ->from('benutzer')
				 ->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID');
		// if role_id was set
		if( ! empty($role_id))
		{
			$this->db->where('benutzer_mm_rolle.RolleID', $role_id);
		}
		// if searchstring was set
		if( ! empty($searchstring))
		{
			$this->db->like('Email', $searchstring);
		}

		return $this->db->get()->result_array();
	}



	// save user changes
	public function update_user($user_id, $data)
	{
		$this->db->where('BenutzerID', $user_id);
		$this->db->update('benutzer', $data);
	}

	// get all permissions
	public function get_all_permissions()
	{
		$this->db->select('*')
				 ->from('berechtigung');

		return $this->db->get()->result_array();
	}

	// get all userpermissions
	public function get_all_userpermissions($user_id)
	{
		$this->db->select('RolleID')
					   ->from('benutzer_mm_rolle')
					   ->where('BenutzerID', $user_id);
		$user_id_role = $this->db->get()->result();

		// var_dump($user_id_role);

		// return;

		foreach ($user_id_role as $key => $value)
		{
			$this->db->select('BerechtigungID')
					  ->from('rolle_mm_berechtigung')
					  ->where('RolleID', $value->RolleID);
			$result_raw[] = $this->db->get()->result_array();
		}

		// var_dump($result_raw);

		// var_dump(expression)

		// $this->db->select('BerechtigungID')
		// 			  ->from('rolle_mm_berechtigung')
		// 			  ->where('RolleID', $user_id_role->RolleID);
		$result_clean = $this->clean_permissions_array($result_raw);

		return $result_clean;
	}

	/** */
	public function get_all_userroles($user_id)
	{
		$this->db->select('RolleID')
				 ->from('benutzer_mm_rolle')
				 ->where('BenutzerID', $user_id)
				 ;
		$q = $this->db->get();
		return $q->result_array();
	}

	/** */
	public function get_all_user_with_roles()
	{
		$result = array();
		$all_user = $this->get_all_user_raw()->result_array();

		foreach ($all_user as $key => $value) {

			// get user specific roles
			$value['roles'] = $this->get_all_userroles($value['BenutzerID']);
			// add to result array
			$result[] = $value;

		}

		return $result;
	}

	// checks array for duplicates and deletes these. creates a 1dim array
	function clean_permissions_array($permissions_to_clean)
	{
		// var_dump($permissions_to_clean);

		$permissions_cleaned = array();
		foreach ($permissions_to_clean as $role) 
		{
			foreach ($role as $v)
			{
				if ( ! in_array($v['BerechtigungID'], $permissions_cleaned))
				{
					array_push($permissions_cleaned, $v['BerechtigungID']);
				}
			}
		}
		return $permissions_cleaned;
	}

	/*
	* get the loginname by users id
	*/
	public function get_loginname($user_id)
	{
		$this->db->select('LoginName')
				 ->from('benutzer')
				 ->where('BenutzerID', $user_id);
		return $this->db->get()->row_array();
	}

	/*
	* deletes user by his id
	*/	
	public function model_delete_user($user_id)
	{
		$this->db->delete('benutzer', array('BenutzerID' => $user_id)); 
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
	 * Returns PO, name, abbreviation of all degree programs
	 * >> used with filter-view
	 * @return unknown
	 */
	function get_all_degree_programs(){
	    $data = array();

	    $this->db->order_by('StudiengangID', 'desc');
	    $q = $this->db->get('studiengang');
		
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns all records belonging to a single degree program (specified by id)
	 * @param unknown_type $dp_id
	 * @return unknown
	 */
	function get_degree_program_courses($dp_id){
	    $data = array();
	    $course_exams = '';

	    // add exam-type to data
	    $exam_types = $this->get_exam_types();
	    
	    $this->db->order_by('Semester', 'asc');
	    $q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $dp_id));

	    // first line of stdgng-list-view should give the opportunity to create an own course
	    // therefore first index of data-array must be filled with a 'default' Kurs
	    // KursID 0 won't be in studiengangkurs-table and cann be used as flag for course-creating
//	    $data[] = null;

	    // count rows to fill additional data to data-array
	    $counter = 0;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result_array() as $row){
		    $data[] = $row;
		    
		    // get exams for that course
		    $course_exams = $this->get_exams_for_course($row['KursID']);

		    // run through all types and add field to data
		    foreach ($exam_types as $e_type) {
			// if there are exam-types
			if($course_exams){
			    // check if TypID is in course_exams array
			    if(in_array($e_type->PruefungstypID, $course_exams)){
				$data[$counter]['pruefungstyp_'.$e_type->PruefungstypID] = '1';
			    } else {
				$data[$counter]['pruefungstyp_'.$e_type->PruefungstypID] = '0';
			    }
			// otherwise >> 0 for alle exam_types
			} else {
			    $data[$counter]['pruefungstyp_'.$e_type->PruefungstypID] = '0';
			}
			
		    }
		    $counter++;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns the exam-types
	 * @return Array (with Objects)
	 */
	private function get_exam_types(){
	    $data = array();
	    
	    $this->db->select('PruefungstypID');
	    $q = $this->db->get('pruefungstyp');
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			    $data[] = $row;
		}
		return $data;
	    }
	}
	
	/**
	 * Returns (0) one or more exam-types for a passed course_id
	 * @param int $course_id
	 */
	private function get_exams_for_course($course_id){
	    $data = array();

	    $q = $this->db->get_where('pruefungssammlung', array('KursID' => $course_id));
	    
	    if($q->num_rows() > 0){
//		foreach ($q->result() as $row){
//			    $data[] = $row;
//		    }
//		    return $data;
//		}
		foreach ($q->result_array() as $row){
		    $data[] = $row['PruefungstypID'];
		}
		return $data;
	    }
	}
	
	
	
	/**
	 * Returns all ids belonging to a specified Studiengang
	 * @param unknown_type $stdgng_id
	 * @return unknown
	 */
	function get_degree_program_course_ids($stdgng_id){
	    $data = array();
	    
	    $this->db->select('KursID');
	    $q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $stdgng_id));

	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	}
	
	/**
	 * Returns all details from a passed Stdgng
	 * @param unknown_type $stdgng_id
	 * @return unknown
	 */
	function get_degree_program_details_asrow($stdgng_id){
	    $q = '';
	    
	    $q = $this->db->get_where('studiengang', array('StudiengangID' => $stdgng_id));

	    if($q->num_rows() == 1){
// 			foreach ($q->result() as $row){
// 				$data[] = $row;
// 			}
		return $q->row();
	    }
	}
	
	/**
	 * Updates a single studiengangkurs-record by given id
	 * @param unknown_type $data
	 * @param unknown_type $stdgng_id
	 */
	function update_degree_program_courses($data, $kurs_id){
	    $this->db->where('KursID', $kurs_id);
	    $this->db->update('studiengangkurs', $data);
	}
	
	
	/**
	 * Inserts data of new created course into db - order is important!
	 * 1. create new course
	 * 2. fetch id of new created course
	 * 3. save exam data
	 * @param array $course_data
	 * @param array $exam_data
	 */
	function insert_new_course($course_data, $exam_data){
	    $e_data = array();
	    $e_data_tmp = array();
	    
	    // save new course
	    $this->db->insert('studiengangkurs', $course_data);
	    
	    // get highest course_id to save exam_data
	    $course_id_max = $this->get_highest_course_id();
	    
	    // modify exam_array to store
	    foreach ($exam_data as $key => $value) {
		// get exam-type from key
		$split = explode('_', $key); // $split[1] holds id
		$e_data_tmp['KursID'] = $course_id_max; // course_id_max is course_id of new course
		$e_data_tmp['PruefungstypID'] = $split[1];
		$e_data[] = $e_data_tmp;
	    }
	    
	    // store data
	    foreach ($e_data as $e) {
		$this->db->insert('pruefungssammlung', $e);
	    }
	    
	}
	
	
	/**
	 * Helper to get highest course-id
	 * @return int
	 */
	private function get_highest_course_id(){
	    $data = array();
	    
	    $this->db->select_max('KursID');
	    $q = $this->db->get('studiengangkurs');
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data = $row->KursID;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Updates a single studiengang-record by given id
	 * @param unknown_type $data
	 * @param unknown_type $stdgng_id
	 */
	function update_degree_program_description_data($data, $stdgng_id){
	    $this->db->where('StudiengangID', $stdgng_id);
	    $this->db->update('studiengang', $data);
	}
	
	/**
	 * Saves checkbox-data to db.
	 * Deletes first all checkboxes for that course!!
	 * @param int $course_id
	 * @param array $cb_data
	 */
	function save_exam_types_for_course($cb_data, $course_id = ''){
	    // !! deletes all exam-types for that course first
	    // >> perhaps not desired behaviour!!
	    if($course_id){
		$this->db->delete('pruefungssammlung', array('KursID' => $course_id));
	    }
	    
	    // add data to table (again)
	    foreach ($cb_data as $value) {
		$this->db->insert('pruefungssammlung', $value);
	    }
	}
	
	
	/**
	 * Creates new Studiengang in db
	 * @param array $data
	 */
	function create_new_stdgng($data){
	    $this->db->insert('studiengang', $data);
	}
	
	/**
	 * Deletes a Studiengang from db
	 * @param int $id
	 */
	function delete_degree_program($id){
	    // delete all exam-types stored in 'pruefungssammlung'
	    $course_ids = array();
	    $course_ids = $this->get_degree_program_course_ids($id);
	    // if there are courses - otherwise dp was created without courses
	    if($course_ids){
		foreach ($course_ids as $c_id) {
		    $this->db->where('KursID', $c_id->KursID);
		    $this->db->delete('pruefungssammlung');
		}
	    }
	    
	    // delete from studiengang-table
	    $this->db->where('StudiengangID', $id);
	    $this->db->delete('studiengang');

	    // delete all courses with this stdgng_id from studiengangkurs
	    $this->db->where('StudiengangID', $id);
	    $this->db->delete('studiengangkurs');
	    
	}
	
	/**
	 * Deletes a single course from studiengangkurs-table
	 * @param int $course_id
	 */
	function delete_stdgng_single_course($course_id){
	    $this->db->delete('pruefungssammlung', array('KursID' => $course_id));
	    $this->db->delete('studiengangkurs', array('KursID' => $course_id));
	}
	
	
	/**
	 * Copies stdgng - creates other name
	 * @param int $dp_id
	 */
	function copy_degree_program($dp_id){
	    // fetching data for degree program to copy
	    $q = $this->db->get_where('studiengang', array('StudiengangID' => $dp_id));

	    $data = '';
	    if($q->num_rows() > 0){
		foreach ($q->result_array() as $row){
		    $data = $row;
		}
	    }
	    
	    // alter name of degree program and delete old id!!
	    $data['StudiengangName'] .= ' - [KOPIE]';
	    unset($data['StudiengangID']);
	    
	    // inserting new degree program into db 'studiengang'
	    $this->db->insert('studiengang', $data);
	    
	    // fetch highest (new) degree program id
	    $max_dp_id = $this->get_highest_degree_program_id();
	    
	    // getting all course_data of course to be copied
	    $dp_to_copy = array();
	    $dp_to_copy = $this->get_degree_program_courses($dp_id);
	    
	    // if the degree program already has data - if not degree program was only created (without courses)
	    if($dp_to_copy){
		// run through courses
		foreach($dp_to_copy as $dp_course){
		    // split data for course and exam-table !! empty arrays for each course!!
		    $course_data = array();
		    $exam_data = array();

		    // run through course-data
		    foreach ($dp_course as $key => $value) {
			// store exam-types to different array than course-data
			if(strstr($key, 'pruefungstyp')){
			    // only add data to array, when the box is checked
			    if($value !== '0'){
				$exam_data[$key] = $value;
			    }
			} else {
			    // set new StudiengangID
			    if (strstr($key, 'StudiengangID')){
				$course_data[$key] = $max_dp_id;
			    } else if (strstr($key, 'KursID')){
				// nothing to do 
			    } else {
				$course_data[$key] = $value;
			    }
			}
		    } // endforeach course-data

		    // call function to save a new course
		    $this->insert_new_course($course_data, $exam_data);

		} // endforeach courses
	    }
	}// end
	
	/**
	 * Helper to get highest degree-program-id
	 * @return int
	 */
	private function get_highest_degree_program_id(){
	    $data = array();
	    
	    $this->db->select_max('StudiengangID');
	    $q = $this->db->get('studiengang');
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data = $row->StudiengangID;
		}
		return $data;
	    }
	}
	
	
	/* 
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
	
	
	/* ************************************************************************
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 */

	
	function get_stdplan_filterdata(){
	    $q = '';
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.Semester, b.StudiengangAbkuerzung, b.Pruefungsordnung');
	    $this->db->from('studiengangkurs as a');
	    $this->db->join('studiengang as b', 'a.StudiengangID = b.StudiengangID');
	    $this->db->join('stundenplankurs as c', 'a.KursID = c.KursID');
	    $this->db->order_by('b.StudiengangAbkuerzung asc, a.Semester asc');
	    
	    $q = $this->db->get();
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns filter-data with id
	 * needed, to provide deletion list-view with id-data
	 * @return type
	 */
	function get_stdplan_filterdata_plus_id(){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.StudiengangID, b.StudiengangName, a.Semester, b.StudiengangAbkuerzung, b.Pruefungsordnung');
	    $this->db->from('studiengangkurs as a');
	    $this->db->join('studiengang as b', 'a.StudiengangID = b.StudiengangID');
	    $this->db->join('stundenplankurs as c', 'a.KursID = c.KursID');
	    $this->db->order_by('b.StudiengangAbkuerzung asc, a.Semester asc');
	    
	    $q = $this->db->get();
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	}
	

	//// KEEP !! Stundenplanfilter
	//select distinct b.`StudiengangAbkuerzung`, b.`Pruefungsordnung`, a.`Semester`
	//from studiengangkurs as a
	//inner join studiengang as b
	//on a.`StudiengangID` = b.`StudiengangID`
	//inner join stundenplankurs as c
	//on a.`KursID` = c.`KursID`;

	
	/**
	 * Returns data for stdplan view
	 * @param type $ids
	 * @return type
	 */
	function get_stdplan_data($ids){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.SPKursID, b.KursID, b.Semester, c.Pruefungsordnung, c.StudiengangAbkuerzung,
		    b.Kursname, a.VeranstaltungsformID, a.VeranstaltungsformAlternative,
		    a.Raum, a.DozentID, a.StartID, a.EndeID, a.TagID,
		    a.isWPF, a.WPFName, a.Farbe');
	    $this->db->from('stundenplankurs as a');
	    $this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
	    $this->db->join('studiengang as c', 'b.StudiengangID = c.StudiengangID');
	    $this->db->where('c.StudiengangAbkuerzung', $ids[0]);
	    $this->db->where('b.Semester', $ids[1]);
	    $this->db->where('c.Pruefungsordnung', $ids[2]);
	    
	    
	    $q = $this->db->get();
	    	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	    
	}
	
	/**
	 * Returns all SPKursIDs with matching Abkürzung, Semester, PO
	 * @param type $ids
	 * @return type
	 */
	function get_all_stdplan_spkurs_ids($ids){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.SPKursID');
	    $this->db->from('stundenplankurs as a');
	    $this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
	    $this->db->join('studiengang as c', 'b.StudiengangID = c.StudiengangID');
	    $this->db->where('c.StudiengangAbkuerzung', $ids[0]);
	    $this->db->where('b.Semester', $ids[1]);
	    $this->db->where('c.Pruefungsordnung', $ids[2]);
	    
	    $q = $this->db->get();
	    	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	    
	}

	// KEEP!! Inhalte der Tabelle
	// 
	//select distinct
	//    c.`Pruefungsordnung`, c.`StudiengangAbkuerzung`, b.`Semester`,
	//    b.`Kursname`, a.`VeranstaltungsformID`, a.`VeranstaltungsformAlternative`,
	//    a.`Raum`, a.`DozentID`, a.`StartID`, a.`EndeID`, a.`TagID`, a.`isWPF`, a.`WPFName`, a.`Farbe`
	//from stundenplankurs as a
	//inner join studiengangkurs as b
	//on a.`KursID` = b.`KursID`
	//inner join studiengang as c
	//on b.`StudiengangID` = c.`StudiengangID`
	//where c.`Pruefungsordnung` = 2010 and b.`Semester` = 1 and c.`StudiengangAbkuerzung` = "BMI";
	// letzte zeile dann mit den einzelnen werten aus dem filter füllen

	
	/**
	 * Returns StundenplankursIDs
	 * @param type $ids
	 * @return type
	 */
	function get_stdplan_course_ids($ids){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.SPKursID');
	    $this->db->from('stundenplankurs as a');
	    $this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
	    $this->db->join('studiengang as c', 'b.StudiengangID = c.StudiengangID');
	    $this->db->where('c.StudiengangAbkuerzung', $ids[0]);
	    $this->db->where('b.Semester', $ids[1]);
	    $this->db->where('c.Pruefungsordnung', $ids[2]);
	    
	    
	    $q = $this->db->get();
	    	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
			$data[] = $row;
		}
		return $data;
	    }
	    
	}


	/**
	 * Returns all enventtypes
	 */
	function get_eventtypes(){
	    $data = array();
	    
	    $q = $this->db->get('veranstaltungsform');
	    
	    $data[] = null;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns all users to whom courses can be assigned
	 * TODO get ALL users - not only profs (uid = dozentid)
	 */
	function get_profs_for_stdplan_list(){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.DozentID, b.Nachname, b.Vorname');
	    $this->db->from('stundenplankurs as a');
	    $this->db->join('benutzer as b', 'b.BenutzerID = a.DozentID', 'left outer');
	    
	    $q = $this->db->get();
	    
	    $data[] = null;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns all start and end times for dropdown
	 */
	function get_start_end_times(){
	    $data = array();
	    
	    $q = $this->db->get('stunde');
	    
	    $data[] = null;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
	}
	
	
	/**
	 * Returns all days
	 */
	function get_days(){
	    $data = array();
	    
	    $q = $this->db->get('tag');
	    
	    $data[] = null;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
	}
	
	/**
	 * Returns als existing colors in Stdplan
	 */
	function get_colors_from_stdplan(){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('Farbe');
	    
	    $q = $this->db->get('stundenplankurs');
	    
	    $data[] = null;
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    $data[] = $row;
		}
		return $data;
	    }
		
	}
	
	/**
	 * Updates a single record in Stundenplankurs for a given SPKursID
	 * @param type $data
	 * @param type $spkurs_id
	 */
	function update_stdplan_details($data, $spkurs_id){
	    $this->db->where('SPKursID', $spkurs_id);
	    $this->db->update('stundenplankurs', $data);
	}
	
	
	//######################### methods needed to delete a stdplan
	
	/**
	 * Deleting all records related to a stundenplan
	 * - getting spkursIDs for this stundenplan
	 * - getting groupids
	 * - deleting from group
	 * - deleting from benutzerkurs
	 * - deleting from stundenplankurs
	 * @param type $stdplan_ids
	 */
	function delete_stdplan_related_records($stdplan_ids){
	    // get spkursids to delete
	    $stdplan_course_ids = $this->get_stdplan_course_ids($stdplan_ids);
	    
	    // get groupids
	    $group_ids = '';
	    foreach($stdplan_course_ids as $id){
		$group_ids[] = $this->get_group_id_to_delete($id->SPKursID);
	    }
	    
	    // delete from gruppe (group_ids)
	    foreach($group_ids as $id){
		$this->delete_from_group($id);
	    }
	    
	    // delete from benutzerkurs (spkurs_ids)
	    foreach($stdplan_course_ids as $id){
		$this->delete_from_benutzerkurs($id->SPKursID);
	    }
	    
	    // delete from stundenplankurs (spkursids)
	    foreach($stdplan_course_ids as $id){
		$this->delete_from_stundenplankurs($id->SPKursID);
	    }
	    
//	    echo '<pre>';
//	    print_r($id);
//	    echo '<p/re>';
	}
	
	
	// get group_ids from stundenplankurs (spkurs_id = )
	function get_group_id_to_delete($spkurs_id){
	    $this->db->select('GruppeID');
	    $this->db->where('SPKursID', $spkurs_id);
	    $q = $this->db->get('stundenplankurs');
	    
	    if($q->num_rows() > 0){
		foreach ($q->result() as $row){
		    return $row->GruppeID;
		}
	    }
	}
	
	
	function delete_from_group($g_id){
	    $this->db->where('GruppeID', $g_id);
	    $this->db->delete('gruppe');
	}
	
	
	// gruppenteilnehmer zu überlegen
	// - mehrere pos in einer gruppe?
	// - dahm: gruppen über das semesterende hinaus behalten
	// wird das referenzmodul oder ?!?!
	
	
	function delete_from_benutzerkurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('benutzerkurs');
	}
	
	function delete_from_stundenplankurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('stundenplankurs');
	}
	
	
	/* 
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
}