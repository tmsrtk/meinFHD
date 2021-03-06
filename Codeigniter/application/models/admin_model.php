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



	/**
	 * Queries all open invitations.
	 * @return mixed an Array with open invitation infos
	 *
	 * @category user_invite.php
	 */
	public function request_all_invitations()
	{
		$this->db->select('*')
				 ->from('anfrage')
				 ->order_by('AnfrageID', 'asc');

		$q = $this->db->get();

		return $q->result_array();
	}

	/**
	 * Saves a user in DB with given values. Convertes the generated password into an md5 
	 * hash before writing into the DB.
	 * @param  mixed $form_data Inupt values.
	 * @param  string $password  Generated password.
	 *
	 * @category user_add.php
	 */
	public function save_new_user($form_data, $password)
	{
		// prepare data for insert
		$data = array(
				'LoginName' 				=> $form_data['loginname'],
				'Email' 					=> $form_data['email'],
				'Vorname'				=> $form_data['forename'],
				'Nachname' 				=> $form_data['lastname'],
				'Matrikelnummer' 			=> $form_data['matrikelnummer'],
				'StudienbeginnJahr' 		=> $form_data['startjahr'],
				'StudienbeginnSemestertyp' 	=> $form_data['semesteranfang'],
				'StudiengangID' 			=> $form_data['studiengang'],
				'Passwort' 				=> md5($password)
			);

		$this->db->insert('benutzer', $data);

		// query directly the user_id of the just created user
		$last_id = mysql_insert_id();

		// insert into benutzer_mm_rolle
		$data = array(
				'BenutzerID' => $last_id,
				'RolleID' => $form_data['role']
			);
		$this->db->insert('benutzer_mm_rolle', $data);
	}

	/**
	 * Puts a new user request with the user information into the DB.
	 * @param  mixed $form_data Input values.
	 *
	 * @category user_invite.php
	 */
	public function put_new_user_to_invitation_requests($form_data)
	{
		// prepare data for insert
		$data = array(
				'Vorname'					=> $form_data['forename'],
				'Nachname' 					=> $form_data['lastname'],
				'Startjahr'			 		=> $form_data['startjahr'],
				'Matrikelnummer' 				=> $form_data['matrikelnummer'],
				'Emailadresse' 				=> $form_data['email'],
				'Semester'				 	=> $form_data['semesteranfang'],
				'Studiengang' 				=> $form_data['studiengang'],
				'TypID'						=> $form_data['role']
			);

		$this->db->insert('anfrage', $data);
	}

	/**
<<<<<<< HEAD
	 * <p>
     * Saves an new user from an invitation with the given id.
     * Edits by Christian Kundruss: Adding the global uid from the invitation data set
     * directly to the new created user, to link the local identity with his global account.
     * </p>
     * @access public
     * @param integer $invitation_id ID of the saved invitation data set
     * @return void
     * @todo missing email functions
=======
	 * Saves an accepted user into the 'benutzer' DB and sends an email to the accepted
	 * user with his login and password.
	 * @param  int $invitation_id Invitation ID of the user request.
	 *
	 * @category user_invite.php
>>>>>>> 02f1c438d9951b146d2587b3e624113793813498
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
				'Matrikelnummer' 				=> $q['Matrikelnummer'],
				'Email' 						=> $q['Emailadresse'],
				'StudienbeginnJahr'		 	=> $q['Semester'],
				'StudiengangID' 			=> $q['Studiengang'],
				'Passwort'					=> md5($password),
                'FHD_IdP_UID'               => $q['FHD_IdP_UID'] # edit by CK; Establish global linking
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

		// send email to just accepted user
		// $this->mailhelper->send_meinfhd_mail(											///////////////////////////////////
		// 	$form_data['email'],
		// 	"Ihre Benutzeranfrage wurde akzeptiert.",
		// 	"Ihr Anmeldename ist Ihre Emailadresse und das Passwort lautet: {$password}"
		// 	);

		// delete requested invitation

        // -- EDIT by CK --
        // remove global uid from blacklist, if the id is on it
        $this->db->where('FHD_IdP_UID', $q['FHD_IdP_UID']);
        $this->db->delete('shibbolethblacklist');
		$this->delete_invitation($invitation_id);
	}

	/**
	 * Deletes an user request by an invitation id.
	 * @param  int $invitation_id The invitation id, which should be deleted.
	 *
	 * @category user_invite.php
	 */
	public function delete_invitation($invitation_id)
	{
		$this->db->where('AnfrageID', $invitation_id);
		$this->db->delete('anfrage'); 
	}
	
	/**
	 * Returns all possible roles.
	 * @return mixed Array of all possible roles. Structure -> [0=>admin, ...]
	 *
	 * @category user_add.php|user_edit.php|user_edit_roles.php
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
			// prepare for controller and simultanously for <select><option> tags $key, $value
			$my_result[$row['RolleID']] = $row['bezeichnung'];
		}

		return $my_result;
	}

	/**
	 * Deletes the roles values of the given user by his id.
	 * @param  int $user_id User id of the user.
	 *
	 * @category user_edit_roles.php
	 */
	public function clear_userroles($user_id)
	{
		if ( ! empty($user_id) )
		{
			$this->db->delete('benutzer_mm_rolle', array('BenutzerID' => $user_id));
		}
	}

	/**
	 * Saves the new role for an user.
	 * @param  int $user_id User id.
	 * @param  int $role    Which role should the user get.
	 *
	 * @category user_edit_roles.php
	 */
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
	 * Returns all possible Studiengänge.
	 * @return mixed Array of all possible Studiengänge. Structure -> [0=>Medieninformatik, ...]
	 * 
	 * @category user_invite.php|user_add.php
	 */
	public function get_all_studiengaenge()
	{
		// query raw data
		$this->db->select('StudiengangID, StudiengangName, Pruefungsordnung')
				 ->from('studiengang');
		$q = $this->db->get();

		// var_dump($q->result_array());

		$my_result = array();
		foreach ($q->result_array() as $row)
		{
			// array_push($my_result, $row['StudiengangName']/*.' '.$row['Pruefungsordnung']*/);
			$my_result[$row['StudiengangID']] = $row['StudiengangName'] . ' [' . $row['Pruefungsordnung'] . ']';
		}

		return $my_result;
	}


	/**
	 * Returns all user.
	 * @return mixed Array of all users.
	 *
	 * @category user_delete.php
	 */
	public function get_all_user()
	{
		return $this->_get_all_user_raw()->result_array();
	}

	/**
	 * Returns an CI Object of all users.
	 * @return mixed All users.
	 * 
	 * @category user_delete.php
	 */
	private function _get_all_user_raw()
	{
		$this->db->select('*')
					->from('benutzer')
					// ->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID')
					// ->limit(50)
					;

		return $this->db->get();
	}

	// /**
	//  * Returns one user by his loginname.
	//  * @param  string $loginname Desired user loginname.
	//  * @return mixed             Array of one user.
	//  */
	// public function get_user_by_loginname($loginname)
	// {
	// 	// if (is_string($loginname))
	// 	// {
	// 		$this->db->select('*')
	// 				 ->from('benutzer')
	// 				 ->where('LoginName', $loginname);
	// 		return $this->db->get()->row_array();
	// 	// }
	// }

	/**
	 * Returns one user by his user id.
	 * @param  int $user_id User´s id.
	 * @return mixed          Array of one user.
	 *
	 * @category user_edit.php
	 */
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

	/**
	 * Returns user matched by the role and the typed letters.
	 * @param  string $role_id      Of which role?
	 * @param  string $searchstring Letters from searchbox.
	 * @return mixed               Array of matched users.
	 *
	 * @category user_edit.php
	 */
	public function get_user_per_role_searchletter($role_id='', $searchstring='')
	{
		$this->db->distinct()
				 ->select('benutzer.*')
				 ->from('benutzer')
				 ->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID')
				 ;
		// if role_id was set
		if( ! empty($role_id))
		{
			$this->db->where('benutzer_mm_rolle.RolleID', $role_id);
		}
		// if searchstring was set
		if( ! empty($searchstring))
		{
			$this->db->where("benutzer.Email LIKE '%{$searchstring}%' OR benutzer.Vorname LIKE '%{$searchstring}%' OR benutzer.Nachname LIKE '%{$searchstring}%' OR benutzer.LoginName LIKE '%{$searchstring}%'");
			// $this->db->like('benutzer.Email', $searchstring);
			// $this->db->or_like('benutzer.Vorname', $searchstring); // did´t work
		}

		return $this->db->get()->result_array();
	}



	/**
	 * Updates the user with given data.
	 * @param  int $user_id Which user should be updated?
	 * @param  mixed $data    Asso Array with desired update values.
	 *
	 * @category user_edit.php|
	 */
	public function update_user($user_id, $data)
	{
		if (!empty($data['Passwort'])) $data['Passwort'] = md5($data['Passwort']);

		$this->db->where('BenutzerID', $user_id);
		$this->db->update('benutzer', $data);
	}

	/**
	 * Reconstruct the Semesterplan of an user.
	 * @param  int $user_id User ID of the user to geht his SemesterplanID.
	 * @return [type]          [description]
	 *
	 * @category user_edit.php
	 */
	public function reconstruct_semesterplan($user_id)
	{
		// get the needed data to execute this job
		// 1. SemesterplanID, 2. StudycourseID

		// 1.
		$this->db->select('SemesterplanID')
				 ->from('semesterplan')
				 ->where('BenutzerID', $user_id);
		$q = $this->db->get()->row_array();
		$semesterplan_id = $q['SemesterplanID'];

		// 2.
		$studycourse_id = $this->_query_studycourseid_of_user($user_id);



		// --

		// delete all entries in semesterkurs
		$this->db->where('SemesterplanID', $semesterplan_id);
		$this->db->delete('semesterkurs');
		
		// delete all entries in semesterplan
		$this->db->where('SemesterplanID', $semesterplan_id);
		$this->db->where('BenutzerID', $user_id);
		$this->db->delete('semesterplan');
		
		// deletes all entries in benutzerkurs
		$this->db->where('BenutzerID', $user_id);
		$this->db->delete('benutzerkurs');
		
		// --

		// create the freshly new stuff for the user

		// query DB for the Regelsemester
		$this->db->select('Regelsemester');
		$this->db->from('studiengang');
		$this->db->where('StudiengangID', $studycourse_id);
		$regelsemester_result = $this->db->get();

		

		foreach($regelsemester_result->result() as $regel)
		{
		    // create a new semsterplan and insert the Regelsemester
		    $dataarray = array(
		        'BenutzerID'    => $user_id,
		        'Semesteranzahl'=> $regel->Regelsemester
		    );

		    $this->db->insert('semesterplan', $dataarray);

		    // query DB for all courses for the studycourse
		    $this->db->select('KursID, Semester');
		    $this->db->from('studiengangkurs');
		    $this->db->where('StudiengangID', $studycourse_id);
		    $kurs_semester = $this->db->get();

		    // get new semesterplan_id
		    $this->db->select('SemesterplanID')
		    		 ->from('semesterplan')
		    		 ->where('BenutzerID', $user_id);
		    $q = $this->db->get()->row_array();
		    $semesterplan_id = $q['SemesterplanID'];

		    // insert all courses of the studycourse in semesterkurs
		    foreach($kurs_semester->result() as $ks)
		    {
		        $dataarray = array(
		            'SemesterplanID'    => $semesterplan_id,
		            'KursID'            => $ks->KursID,
		            'Semester'          => $ks->Semester,
		            'KursHoeren'        => 1,
		            'KursSchreiben'     => 1,
		            'PruefungsstatusID' => 1,
		            'VersucheBislang'   => 0,
		            'Notenpunkte'       => 101
		        );

		        $this->db->insert('semesterkurs', $dataarray);
		    }
		}

		// Eexecute createTimetableCourses method
		$this->db->select('stundenplankurs.*, semesterkurs.Semester');
		$this->db->from('stundenplankurs');
		$this->db->join('kursreferenz', 'kursreferenz.ReferenzKursID = stundenplankurs.KursID');
		$this->db->join('semesterkurs', 'semesterkurs.KursID = kursreferenz.KursID');
		$this->db->join('semesterplan', 'semesterplan.SemesterplanID = semesterkurs.SemesterplanID');
		$this->db->where('semesterplan.BenutzerID', $user_id);
		$this->db->where('semesterplan.SemesterplanID', $semesterplan_id);
		$timetable_result = $this->db->get();

		// insert in benutzerkurs all data from the query above => new timetable
		foreach($timetable_result->result() as $time)
		{
		    $dataarray = array(
		        'BenutzerID'    => $user_id,
		        'KursID'        => $time->KursID,
		        'SPKursID'      => $time->SPKursID,
		        'SemesterID'    => $time->Semester,
		        'aktiv'         => ($time->VeranstaltungsformID == 1 || $time->VeranstaltungsformID == 6) ? '1' : '0',
		        'changed_at'    => 'studienplan_semesterplan: create benutzerkurs',
		        'edited_by'     => $user_id
		    );

		    $this->db->insert('benutzerkurs', $dataarray); 
		}
	}

	/**
	 * Helper method to geht the studycourse id of the given user.
	 * @param  int $user_id User ID, to get his studycourse id.
	 * @return int          Studycourse ID of the user.
	 *
	 * @category user_edit.php
	 */
	private function _query_studycourseid_of_user($user_id)
	{
	    $id = 0;
	    
	    $this->db->select('StudiengangID');
	    $this->db->from('benutzer');
	    $this->db->where('BenutzerID', $user_id);
	    $studycourseID = $this->db->get();
	    $numRows = $studycourseID->num_rows();

	    foreach($studycourseID->result() as $row)
	    {
	        if($numRows != null)
	        {
	            $id = $row->StudiengangID;
	        }
	    }
	        
	    return $id;
	}

	/**
	 * Returns all permissions.
	 * @return mixed Array of all permissions.
	 */
	public function get_all_permissions()
	{
		$this->db->select('*')
				 ->from('berechtigung');

		return $this->db->get()->result_array();
	}

	/**
	 * Returns all permissions of an user.
	 * @param  int $user_id User id to get his permissions.
	 * @return mixed          array of all user permissions.
	 */
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

	/**
	 * Returns all roles of an user.
	 * @param  int $user_id User ID.
	 * @return mixed          Array of all roles of this user.
	 */
	public function get_all_userroles($user_id)
	{
		$this->db->select('RolleID')
				 ->from('benutzer_mm_rolle')
				 ->where('BenutzerID', $user_id)
				 ;
		$q = $this->db->get();
		return $q->result_array();
	}

	/**
	 * Returns all users an their roles.
	 * @return mixed Array of all users and their roles.
	 */
	public function get_all_user_with_roles()
	{
		$result = array();
		$all_user = $this->_get_all_user_raw()->result_array();

		foreach ($all_user as $key => $value) {

			// get user specific roles
			$value['roles'] = $this->get_all_userroles($value['BenutzerID']);
			// add to result array
			$result[] = $value;
		}
		return $result;
	}

	/**
	 * Checks an array for duplicate entries and deletes them. Creates an 1dim array.
	 * @param  mixed $permissions_to_clean Array of permissions.
	 * @return mixed                       Clean, 1dim array of the permissions.
	 */
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

	/**
	 * Return one loginname of the given user_id.
	 * @param  int $user_id User ID.
	 * @return mixed          Array of one loginname.
	 */
	public function get_loginname($user_id)
	{
		$this->db->select('LoginName')
				 ->from('benutzer')
				 ->where('BenutzerID', $user_id);
		return $this->db->get()->row_array();
	}

	/**
	 * Deletes an user by his user_id.
	 * @param  int $user_id User ID.
	 *
	 * @category user_delete.php
	 */
	public function model_delete_user($user_id)
	{
		$this->_delete_all_user_dependencies($user_id);
	}

	/**
	 * Helper method. Queries a semesterplan ID of a user.
	 * @param  integer $user_id User ID to get his semesterplan id.
	 * @return int           SemesterplanID
	 */
	private function _query_semesterplanid_of_user($user_id=0)
	{
		$this->db->select('SemesterplanID')
				 ->from('semesterplan')
				 ->where('BenutzerID', $user_id);
		$q = $this->db->get();
		$qa = $q->row_array();

		if ($q->num_rows() > 0) return $qa['SemesterplanID'];

		// return $q['SemesterplanID'];
	}

	/**
	 * Deletes all dependencies of a user in the DB.
	 * Deletes entries from:
	 * 1. semesterkurs 		(SemesterplanID)
	 * 2. semesterplan 		(SemesterplanID & BenutzerID)
	 * 3. benutzerkurs 		(BenutzerID)
	 * 4. gruppenteilnehmer	(BenutzerID)
	 * 5. benutzer_mm_rolle 	(BenutzerID)
	 * 6. benutzer 			(BenutzerID)
	 * 
	 * @param  integer $user_id User ID.
	 */
	private function _delete_all_user_dependencies($user_id=0)
	{
		// get the needed data to execute this job
		// -SemesterplanID, - StudycourseID (StudiengangID)
		$semesterplan_id = $this->_query_semesterplanid_of_user($user_id);
		$studycourse_id = $this->_query_studycourseid_of_user($user_id);

		// --

		// 1.
		$this->db->where('SemesterplanID', $semesterplan_id);
		$this->db->delete('semesterkurs');
		
		// 2.
		$this->db->where('SemesterplanID', $semesterplan_id);
		$this->db->where('BenutzerID', $user_id);
		$this->db->delete('semesterplan');
		
		// 3.
		$this->db->where('BenutzerID', $user_id);
		$this->db->delete('benutzerkurs');

		// 4.
		$this->db->delete('gruppenteilnehmer', array('BenutzerID' => $user_id));
		
		// 5.
		$this->db->delete('benutzer_mm_rolle', array('BenutzerID' => $user_id));

		// 6.
		$this->db->delete('benutzer', array('BenutzerID' => $user_id));
	}

	/*
	* User management
	* 
	* Konstantin Voth
	***************************************************************************/

    /**
     * global Account blacklisting
     *
     * Christian Kundruss (CK)
     */

    /**
     * Checks in the user table, if the user identity with the given uid is already linked to an global
     * shibboleth account
     *
     * @author Christian Kundruss (CK)
     * @access public
     * @param integer $uid The local uid, that should be checked.
     * @return mixed array with the global uid and username if the user has an linked account, otherwise FALSE
     */
    public function is_user_linked($uid) {

        // check if the given uid is linked to an global id
        $this->db->select('FHD_IdP_UID, Vorname, Nachname');
        $this->db->from('benutzer');
        $this->db->where('BenutzerID',$uid);
        $this->db->where('FHD_IdP_UID IS NOT NULL'); // FHD_IdP_UID is NOT NULL

        $query = $this->db->get(); // query the table

        // is there an matching entry in the database? -> the user is linked -> return selected information
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }

        return FALSE; // the user is not linked
    }

    /**
     * Adds an global user to the shibbolethblacklist.
     * @author Christian Kundruss (CK)
     * @access public
     * @param $user_data array with the user data of the user that should be blacklisted (FHD_IdP_UID, Vorname, Nachname)
     * @return void
     */
    public function add_user_to_blacklist($user_data) {
        // add the user to the shibbolethblacklist
        $this->db->insert('shibbolethblacklist', $user_data);
    }

    /**
     * Removes the given global user id from the blacklist.
     * @author Christian Kundruss
     * @param string $idp_uid Unique login id from the global user
     * @return void
     */
    public function remove_user_from_blacklist($idp_uid) {
        $this->db->where('FHD_IdP_UID', $idp_uid);
        $this->db->delete('shibbolethblacklist');
    }

    /**
     * end global Account blacklisting
     *
     * Christian Kundruss (CK)
     */
	
	
	/* ************************************************************************
	 * 
	 * ******************************* Studiengangverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 */

	
	/**
	 * Returns PO, name, abbreviation (unique combination) of all degree programs
	 * >> used with filter-view
	 * @return array degree program records
	 */
	public function get_all_degree_programs(){
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
	 * @param int $dp_id id of degree program to return
	 * @return array data for single degree program
	 */
	public function get_degree_program_courses($dp_id){
	    $data = array();
	    $course_exams = '';

	    // add exam-type to data
	    $exam_types = $this->get_exam_types();
	    
	    $this->db->order_by('Semester', 'asc');
	    $q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $dp_id));

	    // first line of degree-program-list-view should give the opportunity to create an own course
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
	 * @return array (with Objects)
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
	 * Returns one or more exam-types for a passed course_id
	 * @param int $course_id
	 */
	private function get_exams_for_course($course_id){
	    $data = array();

	    $q = $this->db->get_where('pruefungssammlung', array('KursID' => $course_id));
	    
	    if($q->num_rows() > 0){
			foreach ($q->result_array() as $row){
				$data[] = $row['PruefungstypID'];
			}
			return $data;
	    }
	}
	
	
	
	/**
	 * Returns all ids belonging to a specified Studiengang
	 * @param int $degree_program_id
	 * @return array all course ids of a degree program
	 */
	public function get_degree_program_course_ids($degree_program_id){
	    $data = array();
	    
	    $this->db->select('KursID');
	    $q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $degree_program_id));

	    if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
			return $data;
	    }
	}
	
	/**
	 * Returns all details for a passed degree program id
	 * @param unknown_type $degree_program_id
	 * @return unknown
	 */
	public function get_degree_program_details_asrow($degree_program_id){
	    $q = '';
	    
	    $q = $this->db->get_where('studiengang', array('StudiengangID' => $degree_program_id));

	    if($q->num_rows() == 1){
			return $q->row();
	    }
	}
	
	/**
	 * Updates a single studiengangkurs-record by given id
	 * @param unknown_type $data
	 * @param unknown_type $course_id
	 */
	function update_degree_program_courses($data, $course_id){
	    $this->db->where('KursID', $course_id);
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
	public function update_degree_program_description_data($data, $stdgng_id){
	    $this->db->where('StudiengangID', $stdgng_id);
	    $this->db->update('studiengang', $data);
	}
	
	/**
	 * Saves checkbox-data to db.
	 * Deletes first all checkboxes for that course!!
	 * @param int $course_id
	 * @param array $cb_data
	 */
	public function save_exam_types_for_course($cb_data, $course_id = ''){
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
	 * 
	 * @param array $data data to be stored
	 * @return int new created degree_program_id
	 */
	public function create_new_degree_program($data){
	    $this->db->insert('studiengang', $data);
		
		// get new created dp_id
		$this->db->select_max('StudiengangID');
		$q = $this->db->get('studiengang');
		
	    if($q->num_rows() == 1){
			foreach ($q->result_array() as $row){
				return $row['StudiengangID'];
			}
	    }
				
		return 0;
	}
	
	/**
	 * Deletes a Studiengang from db
	 * @param int $id id of degree-program to be deleted
	 */
	public function delete_degree_program($id){
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

	    // delete all courses with this degree_program_id from studiengangkurs
	    $this->db->where('StudiengangID', $id);
	    $this->db->delete('studiengangkurs');
	    
	}
	
	/**
	 * Deletes a single course from studiengangkurs-table
	 * @param int $course_id course-id of course to be deleted
	 */
	public function delete_degree_program_single_course($course_id){
	    $this->db->delete('pruefungssammlung', array('KursID' => $course_id));
	    $this->db->delete('studiengangkurs', array('KursID' => $course_id));
	}
	
	
	/**
	 * Copies degree program - alters name and short name (adds [KOPIE]
	 * NOTICE: db-field only takes 30 letters - sometimes too less for addition
	 * @param int $dp_id
	 */
	public function copy_degree_program($dp_id){
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
	    $data['StudiengangAbkuerzung'] .= ' - [KOPIE]';
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
		
		// return new id for reload edit-view
		return $max_dp_id;
	}// end
	
	/**
	 * Helper to get highest degree-program-id
	 * @return int highest dp-id
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

	
	/**
	 * Returns all entries to be shown in stdplan-filter
	 * @return array entries to show in filter
	 */
	public function get_stdplan_filterdata(){
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
	public function get_stdplan_filterdata_plus_id(){
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
	

	//// PLAIN SQL !! Stundenplanfilter
	//select distinct b.`StudiengangAbkuerzung`, b.`Pruefungsordnung`, a.`Semester`
	//from studiengangkurs as a
	//inner join studiengang as b
	//on a.`StudiengangID` = b.`StudiengangID`
	//inner join stundenplankurs as c
	//on a.`KursID` = c.`KursID`;

	
	/**
	 * Returns data for stdplan view
	 * @param array $ids unique combination of abbreviation, semester and po of a stdplan
	 * @return array all courses + details for that stdplan
	 */
	public function get_stdplan_data($ids){
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
	
//	/**
//	// TODO: METHOD SEEMS TO BE DUPLICATED - delete if everything works fine
//	 * Returns all SPKursIDs with matching Abkürzung, Semester, PO
//	 * @param array $ids unique combination of abbreviation, semester and po of a stdplan
//	 * @return array all sp_course_ids contained in a stdplan
//	 */
//	public function get_all_stdplan_spkurs_ids($ids){
//	    $data = array();
//	    
//	    $this->db->distinct();
//	    $this->db->select('a.SPKursID');
//	    $this->db->from('stundenplankurs as a');
//	    $this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
//	    $this->db->join('studiengang as c', 'b.StudiengangID = c.StudiengangID');
//	    $this->db->where('c.StudiengangAbkuerzung', $ids[0]);
//	    $this->db->where('b.Semester', $ids[1]);
//	    $this->db->where('c.Pruefungsordnung', $ids[2]);
//	    
//	    $q = $this->db->get();
//	    	    
//	    if($q->num_rows() > 0){
//			foreach ($q->result() as $row){
//				$data[] = $row;
//			}
//			return $data;
//	    }
//	    
//	}

	// PLAIN SQL !! Inhalte der Tabelle
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
	 * Returns all SPKursIDs with matching Abkürzung, Semester, PO
	 * @param array $ids unique combination of Abk, Sem, PO
	 * @return array all sp_course_ids contained in a stdplan
	 */
	public function get_stdplan_sp_course_ids($ids){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.SPKursID, b.Kursname');
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
	 * Returns KursIDs in a single Stundenplan
	 * @param array $ids unique combination of Abk, Sem, PO
	 * @return array all course_ids in stdplan
	 */
	public function get_stdplan_course_ids($ids){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.KursID, b.Kursname');
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
	 * @return array all eventtypes
	 */
	public function get_eventtypes(){
	    $data = array();
	    
	    $q = $this->db->get('veranstaltungsform');
	    
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
	 * @return array all profs
	 */
	public function get_profs_for_stdplan_list(){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('a.DozentID, b.Nachname, b.Vorname');
	    $this->db->from('stundenplankurs as a');
	    $this->db->join('benutzer as b', 'b.BenutzerID = a.DozentID', 'left outer');
	    $q = $this->db->get();
	    
	    if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
			return $data;
	    }
	}
	
	
	/**
	 * Returns als existing colors in Stdplan
	 * @return array all colors
	 */
	public function get_colors_from_stdplan(){
	    $data = array();
	    
	    $this->db->distinct();
	    $this->db->select('Farbe');
	    $q = $this->db->get('stundenplankurs');
	    
	    if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		return $data;
	    }
	}
	
	
	/**
	 * Updates a single record in Stundenplankurs for a given SPKursID
	 * @param array $data data to be stored to db
	 * @param int $spkurs_id id course which details changed
	 */
	public function update_stdplan_details($data, $spkurs_id){
	    $this->db->where('SPKursID', $spkurs_id);
	    $this->db->update('stundenplankurs', $data);
	}
	
	
	/**
	 * Save data to db. Therefore same methods used as during parsing.
	 * >> helper_model
	 * 1. & 2. create new group to fetch new group_id
	 * 3. & 4. create new sp_course to fetch new sp_course_id
	 * 5. update benuterkurs-table for all students in that po
	 * 
	 * @param array $data
	 * @param array $degree_program_ids combination of po(int year), semester(not needed) and dp_abbreviation(string)
	 */
	public function save_new_course_in_stdplan($data, $degree_program_ids){
		// create new group
		$this->helper_model->create_new_group();
		
		// fetch new highest group_id
		$max_group_id = '';
		$max_group_id = $this->helper_model->get_max_group_id_from_gruppe();
		
		// add last data
		$data['GruppeID'] = $max_group_id->GruppeID;
		$data['EditorID'] = $this->user_model->get_userid();
		
		// thoughts about gruppenteilnehmer-table:
		// should be UNcritical >> as soon as course is in benutzerkurs there's one new group for each student
		// gruppenteilnehmer is populated while students register in timetable
		
		// insert new record in stundenplankurs
		$this->db->insert('stundenplankurs', $data);
		
		// fetch new highest spcourse_id
		$max_sp_course_id_tmp = '';
		$max_sp_course_id_tmp = $this->helper_model->get_max_spkurs_id();
		$max_sp_course_id = $max_sp_course_id_tmp->SPKursID;
		
		// update all users in benutzerkurs who got this course-id in semesterplan where semester = semester?
		$this->helper_model->update_benutzerkurs($this->user_model->get_userid(), $data['VeranstaltungsformID'], $data['KursID'], $max_sp_course_id, $degree_program_ids);
		
	}
	
	
	//######################### methods needed to delete a stdplan
	
	/**
	 * Deleting all records related to a stundenplan
	 * - getting spkursIDs for this stundenplan
	 * - getting groupids
	 * - deleting from group
	 * - deleting from benutzerkurs
	 * - deleting from stundenplankurs
	 * @param array $stdplan_ids unique combination of abbreviation, semester, po
	 */
	function delete_stdplan_related_records($stdplan_ids){
	    // get spkursids to delete
	    $stdplan_course_ids = $this->get_stdplan_sp_course_ids($stdplan_ids);
		
		echo $stdplan_course_ids;
	    
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
	
	
	/**
	 * Returns a group_id
	 * @param int $spkurs_id sp_course_id to get group id for
	 * @return int group_id
	 */
	private function get_group_id_to_delete($spkurs_id){
	    $this->db->select('GruppeID');
	    $this->db->where('SPKursID', $spkurs_id);
	    $q = $this->db->get('stundenplankurs');
	    
	    if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				return $row->GruppeID;
			}
	    }
	}
	
	/**
	 * Helper to delete groups from gruppe-table
	 * @param int $g_id group id
	 */
	private function delete_from_group($g_id){
		// from gruppe !!
	    $this->db->where('GruppeID', $g_id);
	    $this->db->delete('gruppe');
		
		// gruppenteilnehmer zu überlegen
		// - mehrere pos in einer gruppe?
		// - dahm: gruppen über das semesterende hinaus behalten
		// wird das referenzmodul oder ?!?!
//		// from gruppenteilnehmer !!
//	    $this->db->where('GruppeID', $g_id);
//	    $this->db->delete('gruppenteilnehmer');
	}
	
	
	/**
	 * Helper to delete sp_courses from benutzerkurs-table
	 * @param int $spk_id sp_course_id
	 */
	private function delete_from_benutzerkurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('benutzerkurs');
	}
	
	/**
	 * Helper to delete courses from stundenplankurs-table
	 * @param int $spk_id sp_course_id
	 */
	private function delete_from_stundenplankurs($spk_id){
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('stundenplankurs');
	}
	

	//######################### methods needed to delete a single course from stdplan
	
	/**
	 * Deletes a single event from stdplan and all related data
	 * @param int $spcourse_id event to delete
	 */
	public function delete_single_event_from_stdplan($spcourse_id){
	    // get groupid
	    $group_id = '';
		$group_id = $this->get_group_id_to_delete($spcourse_id);
	    
	    // delete from gruppe (group_id)
		$this->delete_from_group($group_id);
	    
	    // delete from benutzerkurs (spcourse_id)
		$this->delete_from_benutzerkurs($spcourse_id);
	    
	    // delete from stundenplankurs (spkursids)
		$this->delete_from_stundenplankurs($spcourse_id);
	    
//	    echo '<pre>';
//	    print_r($id);
//	    echo '<p/re>';
	}
	
	
	/* 
	 * 
	 * ******************************* Stundenplanverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
}