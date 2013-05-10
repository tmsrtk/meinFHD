<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Admin_model
 * The admin model provides all necessary db operations, that are required for the
 * administrator features.
 *
 * @version 0.0.1
 * @package meinFHD\models
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Konstantin Voth (KV), <konstantin.voth@fh-duesseldorf.de>
 * @author Frank Gottwald (FG), <frank.gottwald@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Admin_model extends CI_Model {

    /*
     * ==================================================================================
     *                               Authorization system start
     * ==================================================================================
     */

    /**
     * Returns all possible permissions, that are configured in the database and in meinFHD.
     *
     * @access public
     * @return array Array with all possible permissions. Every entry in the array is equal
     *               to one permission.
     */
    public function get_all_permissions(){
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
     * Returns all roles, that are configured in the database (meinFHD).
     *
     * @access public
     * @return array Array with all possible roles. Every entry in the array is equal
     *               to one role.
     */
    public function get_all_roles(){
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
     * Returns all role ids, that are configured in the database.
     *
     * @access public
     * @return array Array with all possible role ids. Every entry in the array is equal
     *               to one role id.
     */
    public function get_all_role_ids(){
	    $data = array();
	    
	    $q = $this->db->get('rolle');

	    foreach ($q->result_array() as $row){
			$data[] = $row['RolleID'];
	    }
	    return $data;
	}
	
    /**
     * Returns the count of existing (configured) roles.
     *
     * @access public
     * @return int Count of existing roles
     */
    public function count_roles(){
	    return $this->db->count_all('rolle');
	}

    /*
     * ==================================================================================
     *                                Authorization system end
     * ==================================================================================
     */

    /*
    * ==================================================================================
    *                                User management start
    * ==================================================================================
    */

	/**
	 * Queries / returns all open invitation requests.
     *
     * @access public
	 * @return mixed If there are some open invitations an array will be returned, otherwise NULL is returned.
	 */
	public function request_all_invitations()
	{
		$this->db->select('*');
	    $this->db->from('anfrage');
	    $this->db->order_by('AnfrageID', 'asc');

		$q = $this->db->get();

		return $q->result_array();
	}

	/**
	 * Saves a user in the DB with the given values. Converts the generated password into an md5
	 * hash before writing it into the DB.
     *
     * @access public
	 * @param array $form_data The input values from the form / The information entered by the user.
	 * @param string $password The generated password.
	 */
	public function save_new_user($form_data, $password)
	{
		// prepare data for insert
		$data = array(
				'LoginName'                 => $form_data['loginname'],
				'Email'                     => $form_data['email'],
				'Vorname'                   => $form_data['forename'],
				'Nachname'                  => $form_data['lastname'],
				'StudienbeginnJahr' 		=> $form_data['startjahr'],
				'Passwort' 				    => md5($password)
			);

        // if the form_data - array contains student information (matrikelnummer, studiengang, semesteranfang) add them to the data array
        if (array_key_exists('matrikelnummer',$form_data)){
            $data['Matrikelnummer'] = $form_data['matrikelnummer'];
        }
        else if (array_key_exists('semesteranfang', $form_data)){
            $data['StudienbeginnSemestertyp'] = $form_data['semesteranfang'];
        }
        else if (array_key_exists('studiengang', $form_data)){
            $data['StudiengangID'] = $form_data['studiengang'];
        }

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
	 * Puts / Saves a new user request with the user information in the DB.
     *
     * @access public
	 * @param array $form_data Input values.
	 */
	public function put_new_user_to_invitation_requests($form_data)
	{
		// prepare data for insert
		$data = array(
				'Vorname'					=> $form_data['forename'],
				'Nachname' 					=> $form_data['lastname'],
				'Startjahr'			 		=> $form_data['startjahr'],
				'Matrikelnummer' 		    => $form_data['matrikelnummer'],
				'Emailadresse' 				=> $form_data['email'],
				'Semester'				 	=> $form_data['semesteranfang'],
				'Studiengang' 				=> $form_data['studiengang'],
				'TypID'						=> $form_data['role']
			);

		$this->db->insert('anfrage', $data);
	}

	/**
     * Saves / creates an new user from an invitation with the given id. If the global uid
     * is saved in the invitation dataset, the process to link the local account with the
     * global (sso) account will also be started.
     *
     * @access public
     * @param integer $invitation_id ID of the saved invitation data set
     * @return array Associative array with some information of the created user (Vorname, Nachname, Email, Password)
	 */
	public function save_new_user_from_invitation($invitation_id)
	{
		// query data from invitation_id
		$q = $this->_query_invitation_info($invitation_id);

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
				'Passwort'					=> md5($password),
                'FHD_IdP_UID'               => $q['FHD_IdP_UID'] # edit by CK; Establish global linking
			);

		$this->db->insert('benutzer', $data);

		// query directly the user_id of the created user
		$last_id = mysql_insert_id();

		// insert into benutzer_mm_rolle
		$mm_data = array(
				'BenutzerID' => $last_id,
				'RolleID' => $q['TypID']
			);
		$this->db->insert('benutzer_mm_rolle', $mm_data);

        // remove global uid from blacklist, if the id is on it
        $this->db->where('FHD_IdP_UID', $q['FHD_IdP_UID']);
        $this->db->delete('shibbolethblacklist');

        // delete requested invitation
        $this->delete_invitation($invitation_id);

        // create the array with some info of the accepted user and return it
        $created_user_info = array(
            'Vorname' => $data['Vorname'],
            'Nachname' => $data['Nachname'],
            'Emailadresse' => $data['Email'],
            'Passwort' => $password
        );

        return $created_user_info;
	}

	/**
	 * Deletes an user request by an invitation id and returns some information about the deleted
     * request.
     *
     * @access public
     * @param $invitation_id int The invitation id, which should be deleted.
     * @return array Array with information about the deleted user.
     */
	public function delete_invitation($invitation_id)
	{
        // get information about the asking user to be able to return it
        $deleted_request_info = $this->_query_invitation_info($invitation_id);

        // delete the invitation
		$this->db->where('AnfrageID', $invitation_id);
		$this->db->delete('anfrage');

        return $deleted_request_info;
	}

    /**
     * Selects all saved information for the given invitation id and returns them as
     * an array.
     *
     * @access private
     * @param $invitation_id int ID of the specified invitation
     * @return array Associative array with all saved information about the person, that requested an account.
     */
    private function _query_invitation_info($invitation_id){

        // query data from invitation_id to be able to return some info
        $this->db->select('*');
        $this->db->from('anfrage');
        $this->db->where('AnfrageID', $invitation_id);

        return $this->db->get()->row_array();
    }
	/**
	 * Returns all possible roles prepared for the use in form dropdowns.
     *
     * @access public
	 * @return array Array of all possible roles. Structure: key, value.
     *               The keys correspond to the RoleID and the value is the description
     *               of the role.([0] => 'admin', ...)
	 */
	public function get_all_roles_for_dropdown()
	{
		// query raw data
		$this->db->select('RolleID, bezeichnung');
        $this->db->from('rolle');
		$q = $this->db->get();


		// prepare the one dimensional return array
		$my_result = array();
		foreach ($q->result_array() as $row)
		{
			// prepare for controller and simultanously for <select><option> tags $key, $value
			$my_result[$row['RolleID']] = $row['bezeichnung'];
		}

		return $my_result;
	}

    /*
     * ==================================================================================
     *              (User) Role administration (Benutzerrollenverwaltung) start
     * ==================================================================================
     */

	/**
	 * Deletes all set / saved roles for the given user id.
     * Cleans the database table 'benutzer_mm_rolle' for the given user id.
     *
     * @access public
	 * @param $user_id int ID of the user where the roles should be deleted for.
	 */
	public function clear_userroles($user_id)
	{
        // if the passed user id is not empty
		if ( !empty($user_id) )
		{
			$this->db->delete('benutzer_mm_rolle', array('BenutzerID' => $user_id));
		}
	}

	/**
	 * Saves the passed role for the passed user id in the database table 'benutzer_mm_rolle'.
     * The method only accepts on role for one user id at one time (call).
     *
     * @access public
	 * @param $user_id int ID of the user where the role should be saved for.
	 * @param $role int ID of the role, which the user should get.
	 */
	public function save_userrole($user_id, $role)
	{
        // if the passed user and role id is not empty
		if ( (!empty($user_id)) && (!empty($role)) ){

            // prepare the data to be inserted
			$data = array(
				'BenutzerID' => $user_id,
				'RolleID' => $role
				);

			$this->db->insert('benutzer_mm_rolle', $data);
		}
	}

    /*
     * ==================================================================================
     *           (User) Role administration (Benutzerrollenverwaltung) end
     * ==================================================================================
     */
	/**
	 * Returns all possible degree programs (Studiengaenge) with their latest PO version.
     * Outgoing situation: Creating Accounts for older / deprecated degree programs is not longer necessary.
     *
     * @access public
	 * @return mixed If their are some degree programs an array of all possible degree programs (Studiengaenge)
     *               (Structure -> [0=>Medieninformatik, ...]) will be returned. If their aren`t any degree
     *              programs, nothing will be returned.
	 */
	public function get_all_studiengaenge()
	{
		// query raw data
		$this->db->select('StudiengangID, StudiengangName, Pruefungsordnung');
		$this->db->from('studiengang');
        $this->db->where('Pruefungsordnung >', 2008);

        $q = $this->db->get();

        // generate the result array and return it
		$my_result = array();
		foreach ($q->result_array() as $row)
		{
			$my_result[$row['StudiengangID']] = $row['StudiengangName'] . ' [' . $row['Pruefungsordnung'] . ']';
		}

		return $my_result;
	}


	/**
	 * Returns all user in an array
     *
     * @access public
	 * @return array Array with all users.
	 */
	public function get_all_user()
	{
		return $this->_get_all_user_raw()->result_array();
	}

	/**
	 * Returns an CI Object of all users.
	 *
     * @access private
     * @return object|null If there are some users, an CI-Object will be returned, otherwise nothing is returned.
	 */
	private function _get_all_user_raw()
	{
		$this->db->select('*');
        $this->db->from('benutzer');

		return $this->db->get();
	}

	/**
	 * Returns the whole user information for an user, that is specified by his user id.
     *
     * @access public
	 * @param int $user_id The id of the user.
	 * @return array|null If the user is found, his information will be returned in an array,
     *                    otherwise null will be returned.
	 */
	public function get_user_by_id($user_id)
	{
        $this->db->select('*');
        $this->db->from('benutzer');
        $this->db->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID');
        $this->db->where('benutzer_mm_rolle.BenutzerID', $user_id);

        return $this->db->get()->row_array();
	}

	/**
	 * Looks in the database for user who matches to the passed search letters. Therefore
     * the users can be looked up by their role or an custom search string.
     *
     * @access public
	 * @param string $role_id ID of the role, which the user should have. The default value is an empty string.
	 * @param string $searchstring Letters from the searchbox / custom search string. The default value is an empty string
	 * @return array|null If there are some matching user, they will be returned in an array, otherwise NULL will be returned.
	 */
	public function get_user_per_role_searchletter($role_id='', $searchstring='')
	{
		$this->db->distinct();
        $this->db->select('benutzer.*');
	    $this->db->from('benutzer');
		$this->db->join('benutzer_mm_rolle', 'benutzer_mm_rolle.BenutzerID = benutzer.BenutzerID');

		// if role_id was set
		if( ! empty($role_id))
		{
			$this->db->where('benutzer_mm_rolle.RolleID', $role_id);
		}
		// if searchstring was set
		if( ! empty($searchstring))
		{
			$this->db->where("benutzer.Email LIKE '%{$searchstring}%' OR benutzer.Vorname LIKE '%{$searchstring}%' OR benutzer.Nachname LIKE '%{$searchstring}%' OR benutzer.LoginName LIKE '%{$searchstring}%'");
		}

		return $this->db->get()->result_array();
	}

	/**
	 * Updates the user information for the given user id with the given data.
     *
     * @access public
	 * @param int $user_id ID of the user who should be updated.
	 * @param array $data Associative array with the desired update values.
	 */
	public function update_user($user_id, $data)
	{
		if (!empty($data['Passwort'])) $data['Passwort'] = md5($data['Passwort']);

		$this->db->where('BenutzerID', $user_id);
		$this->db->update('benutzer', $data);
	}

	/**
	 * Reconstruct / renew the semesterplan of an specified user. Therefore the
     * id of the user is given as an parameter.
     *
     * @access public
	 * @param int $user_id ID of the user, for which the semesterplan should be reconstructed.
	 * @return void
	 */
	public function reconstruct_semesterplan($user_id)
	{
		// 1. get the SemesterplanID
		$this->db->select('SemesterplanID')
				 ->from('semesterplan')
				 ->where('BenutzerID', $user_id);
		$q = $this->db->get()->row_array();
		$semesterplan_id = $q['SemesterplanID'];

		// 2.get the StudyCourseID
		$studycourse_id = $this->_query_studycourseid_of_user($user_id);

		// -- delete the old semesterplan

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
		
		// -- create the new semesterplan

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

		// create an new timetable
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
	 * Helper method to get the studycourse id (degree program id) of the given user.
     *
     * @access private
	 * @param int $user_id User ID, to get his studycourse id (degree program id).
	 * @return int Studycourse ID (degree program id) of the user.
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
	 * Returns all permissions of an user. Therefore the user is specified by his id.
     *
     * @access public
	 * @param int $user_id User id to get his permissions.
	 * @return array|null If there are some permissions for the given user id they will be returned as an array,
     *                    otherwise null will be returned.
	 */
	public function get_all_userpermissions($user_id)
	{
		$this->db->select('RolleID');
        $this->db->from('benutzer_mm_rolle');
        $this->db->where('BenutzerID', $user_id);

        $user_id_role = $this->db->get()->result();

		foreach ($user_id_role as $key => $value)
		{
			$this->db->select('BerechtigungID');
            $this->db->from('rolle_mm_berechtigung');
            $this->db->where('RolleID', $value->RolleID);

            $result_raw[] = $this->db->get()->result_array();
		}

        // clean the result and return it
		$result_clean = $this->clean_permissions_array($result_raw);

		return $result_clean;
	}

	/**
	 * Returns all roles of an user. Therefore the user is specified by his user id.
     *
     * @access public
	 * @param int $user_id User ID.
	 * @return array|null If the query returns the roles of the user, they will be returned
     *                    as an array. Otherwise null will be returned.
	 */
	public function get_all_userroles($user_id)
	{
		$this->db->select('RolleID');
        $this->db->from('benutzer_mm_rolle');
        $this->db->where('BenutzerID', $user_id);

        $q = $this->db->get();

		return $q->result_array();
	}

	/**
	 * Checks an given array for duplicate entries and deletes the duplicate entries. Creates an one dimensional array
     * and returns it.
     *
     * @access public
	 * @param mixed $permissions_to_clean Array of permissions, who should be checked for duplicate entries.
	 * @return array The cleaned, one dimensional array of the permissions.
	 */
	public function clean_permissions_array($permissions_to_clean)
	{
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
	 * Returns the loginname for the given user_id.
     *
     * @access public
	 * @param int $user_id ID of the user, where the loginname should be selected for.
	 * @return array|null If the loginname is found an array with the loginname, will returned.
     *                    Otherwise null is returned.
	 */
	public function get_loginname($user_id)
	{
		$this->db->select('LoginName');
        $this->db->from('benutzer');
        $this->db->where('BenutzerID', $user_id);

		return $this->db->get()->row_array();
	}

	/**
	 * Deletes an user by his user_id.
     *
     * @access public
	 * @param int $user_id User ID.
	 */
	public function model_delete_user($user_id)
	{
        // delete all user dependencies and the user itself
		$this->_delete_all_user_dependencies($user_id);
	}

	/**
	 * Helper method. Queries a semesterplan ID of a user.
     *
     * @access private
	 * @param integer $user_id User ID to get his semesterplan id. The default user id is 0.
	 * @return int|null If the SemesterplanID is found, it will be returned, otherwise NULL is returned.
	 */
	private function _query_semesterplanid_of_user($user_id=0)
	{
		$this->db->select('SemesterplanID');
        $this->db->from('semesterplan');
        $this->db->where('BenutzerID', $user_id);

        $q = $this->db->get();
		$qa = $q->row_array();

		if ($q->num_rows() > 0){
            return $qa['SemesterplanID'];
        }
	}

	/**
	 * Deletes all dependencies of a user in the database.
	 * Deletes entries from:
	 * 1. semesterkurs 		(SemesterplanID)
	 * 2. semesterplan 		(SemesterplanID & BenutzerID)
	 * 3. benutzerkurs 		(BenutzerID)
	 * 4. gruppenteilnehmer	(BenutzerID)
	 * 5. benutzer_mm_rolle 	(BenutzerID)
	 * 6. benutzer 			(BenutzerID)
	 *
     * @access private
	 * @param integer $user_id User ID. The default user id is 0.
     * @return void
	 */
	private function _delete_all_user_dependencies($user_id=0)
	{
		// get the needed data to execute this job
		// -SemesterplanID, - StudycourseID (StudiengangID)
		$semesterplan_id = $this->_query_semesterplanid_of_user($user_id);
		$studycourse_id = $this->_query_studycourseid_of_user($user_id);

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


    /**
     * Looks in the 'benutzer' table, if there is an user, that
     * corresponds to the given email address.
     *
     * @access public
     * @param $email String Email address where an user should be looked up for.
     * @return bool TRUE if there is an user that corresponds to the email,
     *              otherwise FALSE
     */
    public function query_existing_user_for_email($email){
        // query the benutzer table for an user with the given email address
        $this->db->select('*');
        $this->db->from('benutzer');
        $this->db->where('Email', $email);

        $query = $this->db->get();

        // check if there is an user, than return true
        if($query->num_rows > 0){
            return TRUE;
        }

        // there is no user, that corresponds to the inputted email address
        return FALSE;
    }

    /**
     * Updates the user password that corresponds to the given email address.
     *
     * @access public
     * @param $email String Email address, that corresponds to the user
     * @param $password String the new password, that should be saved in the database
     * @return void
     */
    public function update_user_password_for_email($email, $password){
        // prepare the data to insert
        $data = array(
            'Passwort' => md5($password),
        );

        // update the password in the user record
        $this->db->where('Email', $email);
        $this->db->update('benutzer', $data);
    }

    /*
     * ==================================================================================
     *                                User management end
     * ==================================================================================
     */

    /*
     * ==================================================================================
     *                     Privilege administration (Rechteverwaltung) start
     * ==================================================================================
     */

    /**
     * Returns all permissions that are assigned to the given role. Therefore the id
     * of the role is passed as an parameter.
     *
     * @access public
     * @param $rid int RoleID where the permissions should be returned for.
     * @return array Array with the permissions for the passed RoleID.
     */
    public function get_all_role_permissions($rid){
        $data = array();

        $this->db->select('BerechtigungID');
        $q = $this->db->get_where('rolle_mm_berechtigung', array('RolleID' => $rid));

        foreach ($q->result_array() as $row){
            $data[] = $row['BerechtigungID'];
        }
        return $data;
    }

    /**
     * Deletes all configured (existing) permissions for every role.
     * Cleans the database table 'rolle_mm_berechtigung'.
     *
     * @access public
     * @return void
     */
    public function delete_role_permissions(){
        $this->db->empty_table('rolle_mm_berechtigung');
    }

    /**
     * Inserts the passed role permissions into the database table
     * 'rolle_mm_berechtigung'. Therefore the method expects an array
     * with the following keys: ['RolleID'] -> ID of the role,
     * ['BerechtigungID'] -> ID of the permission, that should be assigned
     * to the dedicated role.
     *
     * @access public
     * @param array $rp array Array with the role permissions, that should
     *                        be inserted.
     */
    public function update_role_permissions($rp){
        $this->db->insert('rolle_mm_berechtigung', $rp);
    }

    /*
     * ==================================================================================
     *                     Privilege administration (Rechteverwaltung) end
     * ==================================================================================
     */

    /*
     * ==================================================================================
     *                        Global account blacklisting (single sign on)
     * ==================================================================================
     */

    /**
     * Checks in the user table, if the user identity with the given uid is already linked to an global
     * shibboleth account
     *
     * @author Christian Kundruss (CK)
     * @access public
     * @param integer $uid The local uid, that should be checked.
     * @return array|FALSE If the given user id is blacklisted an array with the global uid and username
     *                     will be returned. Otherwise FALSE will be returned.
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
     * Adds an global user id to the shibbolethblacklist.
     *
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
     *
     * @author Christian Kundruss
     * @access public
     * @param string $idp_uid Unique login id from the global user
     * @return void
     */
    public function remove_user_from_blacklist($idp_uid) {
        $this->db->where('FHD_IdP_UID', $idp_uid);
        $this->db->delete('shibbolethblacklist');
    }

    /*
     * ==================================================================================
     *                  Global account blacklisting (single sign on) end
     * ==================================================================================
     */

    /*
     * ==================================================================================
     *              Degree program administration / Studiengangsverwaltung start
     * ==================================================================================
     */
	
	/**
	 * Queries the database for all degree programs and returns an array with the
     * PO, name, abbreviation (unique combination) of all degree programs.
     *
     * @access public
	 * @return array|null If there are any degree programs, they will be returned as an array,
     *                    otherwise null will be returned.
	 */
	public function get_all_degree_programs(){
	    $data = array();

	    $this->db->order_by('pruefungsordnung', 'asc');
	    $q = $this->db->get('studiengang');
		
	    if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		    return $data;
        }

        return NULL;
	}

	/**
	 * Returns all records belonging to a single degree program (specified by his id)
     *
     * @access public
	 * @param int $dp_id id of degree program which should be returned return
	 * @return array|null If the specified degree program is found, the information will be
     *                    returned as an array. Otherwise NULL will be returned.
	 */
	public function get_degree_program_courses($dp_id){
	    $data = array();
	    $course_exams = '';

	    // add exam-type to data
	    $exam_types = $this->get_exam_types();
	    
	    $this->db->order_by('Semester', 'asc');
	    $q = $this->db->get_where('studiengangkurs', array('StudiengangID' => $dp_id));

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
						}
                        else {
							$data[$counter]['pruefungstyp_'.$e_type->PruefungstypID] = '0';
						}
					// otherwise >> 0 for alle exam_types
					}
                    else {
						$data[$counter]['pruefungstyp_'.$e_type->PruefungstypID] = '0';
					}

				}
				$counter++;
			}
		    return $data;
	    }

        return NULL;
	}

	/**
	 * Returns the different exam-types configured in the database
     *
     * @access public
	 * @return array|null If there are any exam types found, they will be returned as an array, otherwise NULL will be returned.
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

        return NULL;
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
	 * Returns all course ids belonging to a specified degree program.
     *
     * @access public
	 * @param int $degree_program_id ID of the degree program, where the course ids should be selected for.
	 * @return array|null If there are any courses that correspond to the degree program their ids will be
     *                    returned as an array, otherwise NULL will be returned.
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

        return NULL;
	}
	
	/**
	 * Returns all details for the passed degree program id as an single row. If there is only one existing
     * dataset, an result will be returned. Otherwise NULL is going to be returned.
     *
     * @access public
	 * @param int $degree_program_id ID of the degree program, where the details should be selected for
	 * @return array|null If the passed degree program is found, the details will be returned as an array,
     *                    otherwise NULL will be returned.
	 */
	public function get_degree_program_details_as_row($degree_program_id){
	    $q = '';
	    
	    $q = $this->db->get_where('studiengang', array('StudiengangID' => $degree_program_id));

	    if($q->num_rows() == 1){
			return $q->row();
	    }

        return NULL;
	}
	
	/**
	 * Updates a single degree program course - record by his id
     *
     * @access public
	 * @param array $data The data, that should be updated.
	 * @param int $degree_program_course_id The ID of the degree program course, that should be updated
     * @return void
	 */
	public function update_degree_program_courses($data, $degree_program_course_id){
	    $this->db->where('KursID', $degree_program_course_id);
	    $this->db->update('studiengangkurs', $data);
	}

	/**
	 * Inserts data of new created course into the database - order is important!
	 * 1. create new course
	 * 2. fetch id of new created course
	 * 3. save exam data
     *
     * @access public
	 * @param array $course_data The data / information about the course
	 * @param array $exam_data The information about the exam type.
     * @return void
	 */
	public function insert_new_course($course_data, $exam_data){
	    $e_data = array();
	    $e_data_tmp = array();
	    
	    // save new course
	    $this->db->insert('studiengangkurs', $course_data);
	    
	    // get highest course_id to save exam_data
	    $course_id_max = $this->_get_highest_degree_program_course_id();
	    
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
	 * Helper to get highest degree program course-id
     *
     * @access private
	 * @return int The highest degree program course id.
	 */
	private function _get_highest_degree_program_course_id(){
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
	 * Updates a single degree program - record by his given id.
     *
     * @access public
	 * @param array $data The data, with which the record should be updated.
	 * @param int $degree_program_id The id of the degree program, that should be updated.
     * @return void
	 */
	public function update_degree_program_description_data($data, $degree_program_id){
	    $this->db->where('StudiengangID', $degree_program_id);
	    $this->db->update('studiengang', $data);
	}
	
	/**
	 * Saves the exam type for a single course into the db. Therefore the course
     * is specified by his id.
     *
     * @access public
	 * @param array $cb_data The data of the (view) checkbox.
     * @param int $course_id The ID of the course, that should be updated.
     * @return void
	 */
	public function save_exam_types_for_course($cb_data, $course_id = ''){
	    // !! deletes all exam-types for that course first
	    if($course_id){
			$this->db->delete('pruefungssammlung', array('KursID' => $course_id));
	    }
	    
	    // add data to table (again)
	    foreach ($cb_data as $value) {
			$this->db->insert('pruefungssammlung', $value);
	    }
	}

	/**
	 * Creates a new degree program in the database. Therefore the information for the new degree program
     * are passed as an one dimensional array and are already formatted. Every key in the array needs to
     * correspond to an database field in the table 'studiengang'.
	 *
     * @access public
	 * @param array $data Degree program information, that should be stored. Every key in the array needs
     *                    to correspond to an database field in the table 'studiengang'.
	 * @return int|0 The ID of the created degree program. If there occures an error during the
     *               insert process of the new degree program 0 will be returned
	 */
	public function create_new_degree_program($data){

	    $this->db->insert('studiengang', $data);
		
		// get id of the newly created degree program
		$this->db->select_max('StudiengangID');
		$q = $this->db->get('studiengang');

        // check if there is at least one result and return the degree program id
	    if($q->num_rows() == 1){
			foreach ($q->result_array() as $row){
				return $row['StudiengangID'];
			}
	    }

		return 0;
	}
	
	/**
	 * Deletes a specified degree program and all his dependencies
     * from the database.
     *
     * @access public
	 * @param int $id ID of the degree-program to delete
     * @return void
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
	 * Deletes a single course from the database (table studiengangkurs). Therefore
     * the id of the course, that should be deleted needs to be passed as an parameter.
     *
     * @access public
	 * @param int $course_id ID of the course that should be deleted
     * @return void
	 */
	public function delete_degree_program_single_course($course_id){
	    $this->db->delete('pruefungssammlung', array('KursID' => $course_id));
	    $this->db->delete('studiengangkurs', array('KursID' => $course_id));
	}

	/**
	 * Copies a specified degree program with all existing courses. Alters the name (adds [KOPIE])
	 * in front of the degree program name. NOTICE: The database field only takes 30 letters.
     *
     * @access public
	 * @param int $dp_id ID of the degree program that should be copied
     * @return int ID of the copied degree program.
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
	    $data['StudiengangName'] = '[KOPIE]' . $data['StudiengangName'];
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
					}
                    else {
						// set new StudiengangID
						if (strstr($key, 'StudiengangID')){
							$course_data[$key] = $max_dp_id;
						}
                        else if (strstr($key, 'KursID')){
						// nothing to do 
						}
                        else {
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
	}
	
	/**
	 * Helper method: Returns the highest degree program id,
     * that is available in the table 'studiengang'.
     *
     * @access private
	 * @return int The ID of the highest degree program.
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
    * ==================================================================================
    *              Degree program administration / Studiengangsverwaltung end
    * ==================================================================================
    */

    /*
     * ==================================================================================
     *             time table administration / Stundenplanverwaltung start
     * ==================================================================================
     */

	/**
	 * Returns all configured timetables prepared for displaying in dropdown / filter boxes.
     *
     * @access public
	 * @return array|null If there are any timetables in the database, they will
     *                    be returned in an array. Otherwise NULL will be returned.
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

        return NULL;
	}

	/**
	 * Returns all configured timetables prepared for displaying in filter boxes.
     * This method includes the timetable id.
     *
     * @access public
     * @return array|null If there are any timetables in the database, they will
     *                    be returned in an array. Otherwise NULL will be returned.
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

        return NULL;
	}
	
	/**
	 * Returns information about a single timetable, that is specified by his id.
     *
     * @access public
	 * @param array $ids unique combination of abbreviation, semester and po of a stdplan
	 * @return array|null If the specified timetable has been found all courses + details
     *                    for that timetable will be returned in an array. Otherwise NULL
     *                    will be returne.
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

        return NULL;
	}

	/**
	 * Returns all timetable course ids with their matching abbreviation, semester and PO for
     * an specified timetable.
     *
     * @access public
	 * @param array $ids Unique combination of abbreviation, semester, PO of the timetable
     *                   where the course should be selected for.
	 * @return array|null If there are any courses for the specified timetable all sp_course_ids
     *                    of the timetable are returned in an array. Otherwiese NULL will be
     *                    returned.
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

        return NULL;
	}
	
	/**
	 * Returns all course ids for an single timetable. Therefore the timetable is
     * specified by his unique combination of abbreviation, semester and po.
     *
     * @access public
	 * @param array $ids unique combination of abbreviation (index 0), semester (index 1), PO (index 2)
	 * @return array|null If there are some courses for the timetable, they will be returned in an array.
     *                    Otherwise NULL will be returned.
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

        return NULL;
	}

	/**
	 * Returns all configured event types in an array.
     *
     * @access public
	 * @return array|null If there are some event types configured, they will be returned
     *                    in an array. Otherwise NULL will ne returned.
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

        return NULL;
	}

	/**
	 * Returns all users (dozents) to whom courses can be assigned.
     *
     * @access public
	 * @return array|null All dozents, or NULL will be returned.
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

        return NULL;
	}

	/**
	 * Returns all existing colors, that are assigned
     * to any timetable course.
     *
     * @access public
	 * @return array|null If there are some colors, they will be returned in an array.
     *                    Otherwise null will be returned.
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

        return NULL;
	}

	/**
	 * Updates a single record in 'stundenplankurs' for a given SPKursID
     *
     * @access public
	 * @param array $data data to be stored in the database
	 * @param int $spkurs_id timetable course id where the details should be changed for
     * @return void
	 */
	public function update_stdplan_details($data, $spkurs_id){
	    $this->db->where('SPKursID', $spkurs_id);
	    $this->db->update('stundenplankurs', $data);
	}
	
	/**
	 * Saves a new timetable course in the database.
     * Therefore the same methods are used as during the parsing process >> helper_model
	 * 1. & 2. create new group to fetch new group_id
	 * 3. & 4. create new sp_course to fetch new sp_course_id
	 * 5. update benuterkurs-table for all students in that po
	 *
     * @access public
	 * @param array $data Array with the attributes / information about the new course that should be stored in the database
	 * @param array $degree_program_ids combination of po(int year), semester(not needed) and dp_abbreviation(string)
     * @return void
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

	//######################### deleting a single timetable ###############################
	
	/**
	 * Deletes all records, that are related to a timetable.
     * Therefore the following operations will be performed:
	 * - get all spkursIDs for this timetable
	 * - get all groups and groupids, that correspond to the timetable
	 * - delete all groups, that corresponds to the timetable
	 * - delete all timetable courses from the benutzerkurs-table
	 * - delete all timetable courses from the stundenplankurs-table
     *
     * @access public
     * @param $timetable_ids array Array with the unique combination of abbreviation, semester and po (degree program)
     * @return void
	 */
	public function delete_stdplan_related_records($timetable_ids){

	    // get all timetable courses (spkursids) that should be deleted
	    $timetable_course_ids = $this->get_stdplan_sp_course_ids($timetable_ids);

	    // get all groupids that should be deleted
	    $group_ids = array();
        // for each single timetable course get the groups that should be deleted
	    foreach($timetable_course_ids as $id){
			$group_ids[] = $this->_get_group_id_to_delete($id->SPKursID);
	    }

	    // delete all groups, that are related to the timetable that are related to the selected timetable
	    foreach($group_ids as $id){
			$this->_delete_from_group($id);
	    }
	    
	    // delete all user courses from benutzerkurs (spkurs_ids), that are related to the selected timetable
	    foreach($timetable_course_ids as $id){
			$this->_delete_from_benutzerkurs($id->SPKursID);
	    }
	    
	    // delete all timetable courses from the table stundenplankurs (spkursids), that are related to the selected timetable
	    foreach($timetable_course_ids as $id){
			$this->_delete_from_stundenplankurs($id->SPKursID);
	    }
	    
	}

	/**
	 * Returns the group_id, that corresponds to the (timetable) course id, which is passed as an parameter.
     *
     * @access private
     * @param $spkurs_id int Timetable course id, where the group id should be determined for.
	 * @return int The group id, that corresponds to the given timetable course id
	 */
	private function _get_group_id_to_delete($spkurs_id){

        // query for the corresponding group id
	    $this->db->select('GruppeID');
	    $this->db->where('SPKursID', $spkurs_id);
	    $q = $this->db->get('stundenplankurs');

        // generate the query result and return it
	    if($q->num_rows() == 1){ // there should be only 1 corresponding group
			foreach ($q->result() as $row){
				return $row->GruppeID;
			}
	    }
	}
	
	/**
     * Deletes a single group from the database (table 'gruppe'). Therefore the id of the group
     * to delete needs to be passed as an parameter.
     *
     * @access private
     * @param int $g_id ID of the group, that should be deleted.
     * @return void
     *
	 */
	private function _delete_from_group($group_id){
        // delete the group with the given id
        $this->db->where('GruppeID', $group_id);
	    $this->db->delete('gruppe');
	}
	
	/**
     * Deletes an single timetable course from the user course (benutzerkurs) table.
     * Therefore the id of the timetable course to delete needs to be passed as an parameter.
     *
     * @access private
     * @param int $spk_id The timetable course id (stundenplankurs id), that should be deleted.
     * @return void
	 */
	private function _delete_from_benutzerkurs($spk_id){
        // delete the timetable course from the user course table
        $this->db->where('SPKursID', $spk_id);
        $this->db->delete('benutzerkurs');
	}
	
	/**
	 * Helper to delete courses from stundenplankurs-table
     * Deletes an single timetable course from the timetable course (stundenplankurs) table.
     * Therefore the id of the timetable course to delete needs to be passed as an parameter
     *
     * @access private
     * @param int $spk_id ID of the timetable course, that should be deleted
     * @return void
	 */
	private function _delete_from_stundenplankurs($spk_id){
        // delete the timetabelcourse from the timetable course table
	    $this->db->where('SPKursID', $spk_id);
	    $this->db->delete('stundenplankurs');
	}

	//################### deleting an single timetable course ######################
	
	/**
	 * Deletes an single event from an timetable and all related data.
     *
     * @access public
	 * @param int $spcourse_id ID of the timetable course, that should be deleted.
     * @return void
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
	}

    //######################### cleaning all event groups ########################

    /**
     * Executes all necessary database operations to clean all existing event groups.
     *
     * @access public
     * @return void
     */
    public function clean_all_event_groups(){

        // get all students, who take part in past courses
        $students_with_groups_to_delete = $this->_get_all_students_with_groups_to_delete();

        // delete all student and group participant combination from the group participants table
        $this->_delete_students_from_past_groups($students_with_groups_to_delete);

        // remove all group participants from the groups that doesn`t exist any longer
        $this->_delete_group_participants_from_non_existing_groups();

    }
    /**
     * Selects the group and user id of all students, who take part in event groups
     * of the past / major semester.
     *
     * @access private
     * @return array An nested array with the group id and the user id, where the students take part in.
     *               Structure: Array -> nested Array -> Keys "GruppeID", "BenutzerID"
     */
    private function _get_all_students_with_groups_to_delete(){

        $this->db->select('a.GruppeID, a.BenutzerID');
        $this->db->from('gruppenteilnehmer as a, stundenplankurs as b, benutzerkurs as c, benutzer as d');
        $this->db->where('a.GruppeID = b.GruppeID');
        $this->db->where('c.BenutzerID = a.BenutzerID');
        $this->db->where('c.SPKursID = b.SPKursID');
        $this->db->where('d.Semester != c.SemesterID');
        $this->db->where('d.BenutzerID = a.BenutzerID');

        // query the database for all students, who take part in past courses
        $query = $this->db->get();

        $return_array = array();

        // generate the query result
        if($query->num_rows() > 0){ // if there is some result

            foreach($query->result_array() as $single_row){ // copy each record to the return array

                $return_array[] = $single_row;
            }

        }

        return $return_array;
    }

    /**
     * Deletes all students from past / major courses, where they do not participate any longer.
     *
     * @param $students_groups_to_clean array Array with the combination of all students and groups that should be
     *                                        be deleted from the group participants table.
     * @access private
     * @return void
     */
    private function _delete_students_from_past_groups($students_groups_to_clean){

        // clean all students and groups from the group participants table (clean all combinations)
        foreach($students_groups_to_clean as $single_group_student_combination){

            $this->db->where('GruppeID', $single_group_student_combination["GruppeID"]);
            $this->db->where('BenutzerID', $single_group_student_combination["BenutzerID"]);
            $this->db->delete('gruppenteilnehmer');
        }
    }

    /**
     * Deletes all group participants from groups, that does not exist any longer.
     *
     * @access private
     * @return void
     */
    private function _delete_group_participants_from_non_existing_groups(){

        $this->db->where('GruppeID NOT IN (SELECT GruppeID FROM stundenplankurs)');
        $this->db->delete('gruppenteilnehmer');
    }

    /*
     * ==================================================================================
     *             time table administration / Stundenplanverwaltung start
     * ==================================================================================
     */
}
/* End of file admin_model.php */
/* Location: ./application/models/admin_model.php */