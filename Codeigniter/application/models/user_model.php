<?php

class User_model extends CI_Model {

	private $user_id = 0;

	private $email = '';
	private $loginname = '';
	private $forename = '';
	private $lastname = '';

	private $user_roles = array();
	private $user_permissions_all = array();
	
	// course_ids (mapped with roles)
	private $user_course_ids = array();

	// studienplan
	private $studiengang_id = 0;
	private $semesterplan_id = 0;
	private $act_semester = 0;
	private $studienbeginn_jahr = 0;
	private $studienbeginn_typ = '';

	private $studiengang_data = array();

	/**
	 * 
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_init();
	}

	/**
	 * Responsible to get all needed userdata
	 */
	private function _init()
	{
		$uid = $this->authentication->user_id();
		if ($uid)
		{
			$this->user_id = $uid;

			$this->lastname = $this->_query_user_singlecolumndata('Vorname');
			$this->forename = $this->_query_user_singlecolumndata('Nachname');
			$this->loginname = $this->_query_user_singlecolumndata('LoginName');
			$this->email = $this->_query_user_singlecolumndata('Email');

			$this->user_roles = $this->_query_all_roles();
			$this->user_permissions_all = $this->_query_all_permissions();

			$this->studienbeginn_jahr = $this->_query_user_singlecolumndata('StudienbeginnJahr');
			$this->studienbeginn_typ = $this->_query_user_singlecolumndata('StudienbeginnSemestertyp');

			$this->semesterplan_id = $this->_query_semesterplanid();
			// get actual Semester every time when the user connects and save it in the db
			// $this->act_semester = $this->adminhelper->getSemester($this->studienbeginn_typ, $this->studienbeginn_jahr);
			$this->act_semester = $this->adminhelper->get_act_semester($this->studienbeginn_typ, $this->studienbeginn_jahr);
			// TODO: need to check if its a student/tutor?
			$this->update_usersemester($this->user_id, $this->act_semester);

			// log_message('error', $this->act_semester);

			$this->studiengang_id = $this->_query_studiengang_id();
			$this->studiengang_data = $this->_query_studiengang_data();

			
			// course_ids
			$this->user_course_ids = $this->get_course_ids_with_roles();
			
		}

		// global data
		$userdata = array(
	                'userid' 					=> $this->user_id,
	                'loginname' 				=> $this->loginname,
	                'userpermissions' 			=> $this->user_permissions_all,
	                'roles' 					=> $this->user_roles,
	                'act_semester'				=> $this->act_semester,
	                'studienbeginn_jahr' 		=> $this->studienbeginn_jahr,
	                'studienbeginn_semestertyp'	=> $this->studienbeginn_typ,
	                'semesterplan_id'			=> $this->semesterplan_id,
	                'studiengang_data'			=> $this->studiengang_data
	            );

		// write userdata in global $data
        $this->data->add('userdata', $userdata);

	}

	public function update_usersemester($user_id, $sem_count=0)
	{
		$this->db->update('benutzer', array("Semester" => $sem_count), "BenutzerID = {$user_id}");
	}

	/** */
	private function _query_user_singlecolumndata($columnname)
	{
		$this->db->select($columnname)
				 ->from('benutzer')
				 ->where('BenutzerID', $this->user_id);
		$q = $this->db->get()->row_array();

		return ($q[$columnname]);
	}

	public function _query_studiengang_id()
	{
		$this->db->select('StudiengangID')
				->from('benutzer')
				->where('BenutzerID', $this->user_id)
				;
		$q = $this->db->get()->row_array();
		return $q['StudiengangID'];
	}


	public function _query_studiengang_data()
	{
		$this->db->select('*')
				->from('studiengang')
				->where('StudiengangID', $this->studiengang_id)
				;
		$q = $this->db->get()->row_array();
		return $q;
	}

	private function _query_semesterplanid()
	{
		$this->db->select('SemesterplanID')
				->from('semesterplan')
				->where('BenutzerID', $this->user_id)
				;
		$q = $this->db->get()->row_array();

		if ( ! empty($q)) return $q['SemesterplanID'];
	}

	/** */
	private function _query_all_permissions()
	{
		$this->db->select('RolleID')
					   ->from('benutzer_mm_rolle')
					   ->where('BenutzerID', $this->user_id);
		$user_id_role = $this->db->get()->result();

		// var_dump($user_id_role);

		// return;
		
		$result_raw = array();
		
		foreach ($user_id_role as $key => $value) {
			$this->db->select('BerechtigungID')
					  ->from('rolle_mm_berechtigung')
					  ->where('RolleID', $value->RolleID);
			$result_raw[] = $this->db->get()->result_array();
		}

		// var_dump($result_raw);

		$result_clean = $this->_clean_permissions_array($result_raw);

		return $result_clean;
	}


	/** */
	// checks array for duplicates and deletes these. creates a 1dim array
	private function _clean_permissions_array($permissions_to_clean)
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

	/** */
	// checks array for duplicates and deletes these. creates a 1dim array
	private function _clean_roles_array($permissions_to_clean)
	{
		// var_dump($permissions_to_clean);

		$permissions_cleaned = array();
		foreach ($permissions_to_clean as $role) 
		{
			#echo '<div style="height: 100px;"></div>';
			#krumo($role);
			if ( ! in_array($role['RolleID'], $permissions_cleaned))
				{
					array_push($permissions_cleaned, $role['RolleID']);
				}
			
			/*
			# PHP 5.4: illegal string offset warning 'RolleID' (jetzt) in zeile 150
			# -> seit 5.4 wird genauer auf datentypen geachtet und $v['RolleID'] ist ein string und kein array
			# warum und weshalb es ein array sein muss bzw der fehler auftaucht, kann ich nicht genau sagen ...
			# laut Dokumentation erwartet in_array() eigentlich einen string.. evtl hat sich in 5.4 da was ge�ndert
			# Peter Jaraczewski: ich hab das mal VOR den foreach loops gedumpt und das array hatte nur eine dimension
			# Peter Jaraczewski: oder kann es eine 2. auch kriegen ?
			# Frank Gottwald: ich denke ja - kontext ist immer wichtig :\
			# TODO: verification needed here!
			foreach ($role as $v)
			{
				krumo($v);
				if ( ! in_array($v['RolleID'], $permissions_cleaned))
				{
					array_push($permissions_cleaned, $v['RolleID']);
				}
			}
			*/
		}
		return $permissions_cleaned;
	}


	private function _query_all_roles()
	{
		$this->db->select('RolleID')
				 ->from('benutzer_mm_rolle')
				 ->where('BenutzerID', $this->user_id);

		return $this->_clean_roles_array($this->db->get()->result_array());
	}

	
	/**
	 * Returns all ids for this user mapped to containing roles.
	 * @return array(int => int) all ids mapped to roles
	 */
	private function get_course_ids_with_roles(){
	    $ids = array();
	    $course_ids_prof = array();
	    $course_ids_labing = array();
	    $course_ids_tut = array();
	    // profs
	    if(in_array(2, $this->user_roles)){
		$course_ids_prof = $this->_get_user_course_ids_from_spkurs();
			if($course_ids_prof){
				foreach ($course_ids_prof as $cid) {
					$ids[$cid] = 2;
				}
			}
	    }
	    // labings
	    if(in_array(3, $this->user_roles)){
		$course_ids_labing = $this->_get_user_course_ids_from_labing_tut('kursbetreuer');
			if($course_ids_labing){
				foreach ($course_ids_labing as $cid) {
					$ids[$cid] = 3;
				}
			}
	    }
	    // tuts
	    if(in_array(4, $this->user_roles)){
		$course_ids_tut = $this->_get_user_course_ids_from_labing_tut('kurstutor');
			if($course_ids_tut){
				foreach ($course_ids_tut as $cid) {
					$ids[$cid] = 4;
				}
			}
	    }
	    return $ids;
	}
	
	/**
	 * Returns all course-ids a user has - focus on eventtype 1 !!
	 * WPFs not covered with this query!!
	 * @return array
	 */
	private function _get_user_course_ids_from_spkurs(){
	    $data = ''; // init

	    $this->db->distinct();
	    $this->db->select('KursID');
	    $this->db->from('stundenplankurs');
	    $this->db->where('DozentID', $this->user_id);
	    $this->db->where('isWPF', '0');
	    
	    $q = $this->db->get();
	    
	    foreach ($q->result_array() as $row) { 
			$data[] = $row;
	    }

	    if ($data) {
			$data = $this->clean_nested_array($data);
	    }

	    
	    return $data;
	}
	
	
	/**
	 * Returns all course-ids for that user (labing or tut)
	 * @param String $table name of table that should be used
	 * @return array with all containing course_ids
	 */
	private function _get_user_course_ids_from_labing_tut($table){
	    $this->db->select('KursID');
	    $q = $this->db->get_where($table, array('BenutzerID' => $this->user_id));
	    
	    $data = ''; // init
	    
	    foreach ($q->result_array() as $row) { 
			$data[] = $row;
			}
			if($data){
				$data = $this->clean_nested_array($data);
			}
	    
	    return $data;
	}
	
	
	/**
	 * Runs through nested array and returns simple indexed array with values
	 * @param type $array
	 * @return type
	 */
	private function clean_nested_array($array){
	    $clean = array();
	    foreach ($array as $a) {
			foreach ($a as $key => $value) {
				$clean[] = $value;
			}
	    }
	    return $clean;
	}





	// getter and setter

	public function get_all_roles()
	{
		return $this->user_roles;
	}

	public function get_all_permissions()
	{
		return $this->user_permissions_all;
	}

	public function get_permission_by_role($role)
	{

	}

	public function get_permission_by_roles($roles)
	{

	}

	public function get_userid()
	{
		if ( ! empty($this->user_id) )
		{
			return $this->user_id;
		}
	}

	public function get_semesterplanid()
	{
		if ( ! empty($this->semesterplan_id) )
		{
			return $this->semesterplan_id;
		}
	}

	public function get_actsemester()
	{
		if ( ! empty($this->act_semester) )
		{
			return $this->act_semester;
		}
	}

	public function get_studienbeginn_semestertyp()
	{
		if ( ! empty($this->studienbeginn_typ) )
		{
			return $this->studienbeginn_typ;
		}
	}

	public function get_studienbeginn_jahr()
	{
		if ( ! empty($this->studienbeginn_jahr) )
		{
			return $this->studienbeginn_jahr;
		}
	}









	
	/**
	 * Returns course-ids for a single user mapped to roles
	 * @return array [course_id] => [role_id]
	 */
	public function get_user_course_ids(){
	    return $this->user_course_ids;
	}


	
	
	
	
	
	
//	// HAS BEEN NECESSARY AS LONG AS LAGING- & TUT-TABLE CONTAIN SPKURSID (CHANGED TO KURSID)
//	/**
//	 * Runs through an array of spcourse_ids to find the matching course_ids
//	 * Necessary because labings and tuts-table contain sp_course_ids
//	 * @param array $sp_course_ids_tut
//	 * @return array $course_ids
//	 */
//	private function _get_course_ids_from_spcourse_ids($sp_course_ids_tut){
//
//	    $course_ids_duplicates = array(); // init
//	    
//	    foreach($sp_course_ids_tut as $id){
//		$this->db->select('KursID')->from('stundenplankurs')->where('SPKursID', $id);
//		$course_ids_duplicates[] = $this->db->get()->result_array();
//	    }
//	    
//	    // clean that result - 2 times nested oO
//	    $course_ids_duplicates = $this->clean_nested_array(
//				     $this->clean_nested_array($course_ids_duplicates));
//	    
//	    // remove duplicates
//	    $course_ids = array_unique($course_ids_duplicates);
//	    
//	    return $course_ids;
//	}
	
}