<?php

class User_model extends CI_Model {

	private $user_id = 0;

	private $email = '';
	private $loginname = '';
	private $forename = '';
	private $lastname = '';

	private $user_roles = array();
	private $user_permissions_all = array();
	
	// profs, labings, tuts
	private $user_course_ids = array();
	private $user_course_ids_labing = array();
	private $user_course_ids_tut = array();
	
	// studienplan
	private $studiengang_id = 0;
	private $semesterplan_id = 0;
	private $act_semester = 0;
	private $studienbeginn_jahr = 0;
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
			$this->lastname = $this->_query_user_singlecolumndata('Nachname');
			$this->loginname = $this->_query_user_singlecolumndata('LoginName');
			$this->email = $this->_query_user_singlecolumndata('Email');

			$this->user_roles = $this->_query_all_roles();
			$this->user_permissions_all = $this->_query_all_permissions();

			$this->semesterplan_id = $this->_query_semesterplanid();
			$this->act_semester = $this->_query_user_singlecolumndata('Semester');
			$this->studienbeginn_jahr = $this->_query_user_singlecolumndata('StudienbeginnJahr');
			$this->studienbeginn_semestertyp = $this->_query_user_singlecolumndata('StudienbeginnSemestertyp');
			$this->studiengang_id = $this->_query_studiengang_id();
			$this->studiengang_data = $this->_query_studiengang_data();

			
			// course_ids
			// profs
			if(in_array(2, $this->user_roles)){
			    $this->user_course_ids = $this->_get_all_user_courses();
			}
			// labings
			if(in_array(3, $this->user_roles)){
			    $sp_course_ids_labing = $this->_get_user_course_ids_labing('laboringenieur');
			    // necessary at the moment because labing and tut table store sp-course-ids NOT course-ids
			    $this->user_course_ids_labing = $this->_get_course_ids_from_spcourse_ids($sp_course_ids_labing);
			}
			// tuts
			if(in_array(4, $this->user_roles)){
			    $sp_course_ids_tut = $this->_get_user_course_ids_labing('tutor');
			    // necessary at the moment because labing and tut table store sp-course-ids NOT course-ids
			    $this->user_course_ids_tut = $this->_get_course_ids_from_spcourse_ids($sp_course_ids_tut);
			}
		}

		// global data
		$userdata = array(
	                'userid' 					=> $this->user_id,
	                'loginname' 				=> $this->loginname,
	                'userpermissions' 			=> $this->user_permissions_all,
	                'roles' 					=> $this->user_roles,
	                'act_semester'				=> $this->act_semester,
	                'studienbeginn_jahr' 		=> $this->studienbeginn_jahr,
	                'studienbeginn_semestertyp'	=> $this->studienbeginn_semestertyp,
	                'semesterplan_id'			=> $this->semesterplan_id,
	                'studiengang_data'			=> $this->studiengang_data
	            );

		// write userdata in global $data
        $this->data->add('userdata', $userdata);

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
			# laut Dokumentation erwartet in_array() eigentlich einen string.. evtl hat sich in 5.4 da was geändert
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
	 * Returns all course-ids a user has - focus on eventtype 1 !!
	 * WPFs not covered with this query!!
	 * @return array
	 */
	private function _get_all_user_courses(){
	    $this->db->distinct();
	    $this->db->select('KursID');
	    $this->db->from('stundenplankurs');
	    $this->db->where('DozentID', $this->user_id);
	    $this->db->where('isWPF', '0');
	    
	    $q = $this->db->get();
	    
	    foreach ($q->result_array() as $row) { 
		$data[] = $row;
	    }

	    $data = $this->clean_nested_array($data);
	    
	    return $data;
	}
	
	
	/**
	 * Returns all course-ids for that user (labing or tut)
	 * @param boolean $is_tutor indicates if user is tutor or not
	 */
	private function _get_user_course_ids_labing($table){
	    $this->db->select('SPKursID');
	    $q = $this->db->get_where($table, array('BenutzerID' => $this->user_id));
	    
	    foreach ($q->result_array() as $row) { 
		$data[] = $row;
	    }
	    $data = $this->clean_nested_array($data);
	    
	    return $data;
	}
	
	/**
	 * Runs through an array of spcourse_ids to find the matching course_ids
	 * Necessary because labings and tuts-table contain sp_course_ids
	 * @param array $sp_course_ids_tut
	 * @return array $course_ids
	 */
	private function _get_course_ids_from_spcourse_ids($sp_course_ids_tut){

	    $course_ids_duplicates = array(); // init
	    
	    foreach($sp_course_ids_tut as $id){
		$this->db->select('KursID')->from('stundenplankurs')->where('SPKursID', $id);
		$course_ids_duplicates[] = $this->db->get()->result_array();
	    }
	    
	    // clean that result - 2 times nested oO
	    $course_ids_duplicates = $this->clean_nested_array(
				     $this->clean_nested_array($course_ids_duplicates));
	    
	    // remove duplicates
	    $course_ids = array_unique($course_ids_duplicates);
	    
	    return $course_ids;
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
	
	/**
	 * Returns course-ids for a single user
	 * !!! right now user can only have one course-relevant role at one moment
	 * other behaviour could be designated, but not implemented yet
	 * @return type
	 */
	public function get_user_course_ids(){
	    if(in_array(2, $this->user_roles)){
		return $this->user_course_ids;
	    } else if(in_array(3, $this->user_roles)){
		return $this->user_course_ids_labing;
	    } else if(in_array(4, $this->user_roles)){
		return $this->user_course_ids_tut;
	    }
	}

}