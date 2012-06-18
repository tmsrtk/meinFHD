<?php

class Admin_model extends CI_Model {

	// ################ RECHTESYSTEM
	
	/**
	 * Liefert alle Permissions die in meinFHD gesetzt werden können
	 * @return unknown
	 */
	function getAllPermissions(){
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
		$q = $this->db->get('rolle');
		
		foreach ($q->result_array() as $row)
		{
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
		$this->db->select('BerechtigungID');
		$q = $this->db->get_where('rolle_mm_berechtigung', array('RolleID' => $rid));
		
// 		if($q->num_rows() > 0){
// 			foreach ($q->result() as $row){
// 				$data[] = $row;
// 			}
// 			return $data;
// 		}

		$data[] = null;
		
		foreach ($q->result_array() as $row)
		{
			$data[] = $row['BerechtigungID'];
		}
		return $data;
	}





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
				'StudiengangID' 			=> $form_data['studiengang_dd'],
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
				'Studiengang' 				=> $form_data['studiengang_dd'],
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
		$this->delete_invitation($invitation_id);
	}

	function delete_invitation($invitation_id)
	{
		$this->db->where('AnfrageID', $invitation_id);
		$this->db->delete('anfrage'); 
	}
	
	/*
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
		$this->db->select('*')  // !!!!!!!!!!!!!!!!!!! LIMIT
					->from('benutzer')
					->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID');

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
		$this->db->select('*')
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
		$user_id_role = $this->db->get()->row();

		// var_dump($user_id_role);

		$this->db->select('BerechtigungID')
					  ->from('rolle_mm_berechtigung')
					  ->where('RolleID', $user_id_role->RolleID);
		$result_raw = $this->db->get()->result_array();
		$result_clean = $this->clean_permissions_array($result_raw);

		return $result_clean;
	}

	// checks array for duplicates and deletes these. creates a 1dim array
	function clean_permissions_array($permissions_to_clean)
	{
		$permissions_cleaned = array();
		foreach ($permissions_to_clean as $key => $value) 
		{
			if ( ! in_array($value['BerechtigungID'], $permissions_cleaned))
			{
				array_push($permissions_cleaned, $value['BerechtigungID']);
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
	
	
	
	
	
	
	
	
	/* *****************************************************
	 * ************** Studiengangverwaltung Anfang *********
	 * *****************************************************/

	
	/**
	 * Returns PO, name, abbreviation of all Studiengänge
	 * >> used with filter-view
	 * @return unknown
	 */
	function getAllStdgnge(){
		//$this->db->select('Pruefungsordnung, StudiengangName, StudiengangAbkuerzung, Regelsemester');
		$q = $this->db->get('studiengang');
		
// 		foreach ($q->result_array() as $row)
// 		{
// 			$data[] = $row;
// 		}
// 		return $data;
		
		
		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
			return $data;
		}
	}
	
	
	/**
	 * Returns all records belonging to a single Studiengang (specified by id)
	 * @param unknown_type $stdgng_id
	 * @return unknown
	 */
	function get_stdgng_courses($stdgng_id){
		$q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $stdgng_id));
		
		// first line of stdgng-list-view should give the opportunity to create an own course
		// therefore first index of data-array must be filled with a 'default' Kurs
		// KursID 0 won't be in studiengangkurs-table and cann be used as flag for course-creating
		$data[] = null;
		
		if($q->num_rows() > 0){
		    foreach ($q->result() as $row){
			    $data[] = $row;
		    }
		    return $data;
		}

// 		foreach ($q->result_array() as $row)
// 		{
// 			$data[] = $row;
// 		}
// 		return $data;
		
	}
	
	/**
	 * Returns all ids belonging to a specified Studiengang
	 * @param unknown_type $stdgng_id
	 * @return unknown
	 */
	function getStdgngCourseIds($stdgng_id){
		$this->db->select('KursID');
		$q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $stdgng_id));
	
		if($q->num_rows() > 0){
		    foreach ($q->result() as $row){
			    $data[] = $row;
		    }
		    return $data;
		}
		
// 		foreach ($q->result_array() as $row)
// 		{
// 			$data[] = $row;
// 		}
// 		return $data;
	
	}
	
	/**
	 * Returns all details from a specified Stdgng
	 * @param unknown_type $stdgng_id
	 * @return unknown
	 */
	function get_stdgng_details_asrow($stdgng_id){
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
	function update_stdgng_courses($data, $kurs_id){
		$this->db->where('KursID', $kurs_id);
		$this->db->update('studiengangkurs', $data);
	}
	
	
	/**
	 * Updates a single studiengang-record by given id
	 * @param unknown_type $data
	 * @param unknown_type $stdgng_id
	 */
	function update_stdgng_description_data($data, $stdgng_id){
		$this->db->where('StudiengangID', $stdgng_id);
		$this->db->update('studiengang', $data);
	}
	
	
	function create_new_stdgng($data){
		$this->db->insert('studiengang', $data);
	}
	
	function delete_stdgng($id){
		// delete from studiengang-table
		$this->db->where('StudiengangID', $id);
		$this->db->delete('studiengang');
		
		// delete all courses with this stdgng_id from studiengangkurs
		$this->db->where('StudiengangID', $id);
		$this->db->delete('studiengangkurs');		
		
	}
	
	
	/* *****************************************************
	 * ************** Stundenplanverwaltung Anfang *********
	 * *****************************************************/

	
	function get_stdplan_filterdata(){
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
	

//// Stundenplanfilter
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

// Inhalte der Tabelle
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
//	    foreach ($q->result_array() as $row) { 
//		$data[] = $row;
//	    }
// 		return $data;
		
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
	
	function delete_from_benutzerkurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('benutzerkurs');
	}
	
	function delete_from_stundenplankurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('stundenplankurs');
	}
	
	
	
	
}