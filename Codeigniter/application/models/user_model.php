<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class User_model
 * The user model provides all necessary db operations, to access user specific
 * data, which is stored in the database. Moreover it provides all necessary functions
 * and operations to provide user specific content.
 *
 * @version 0.0.1
 * @package meinFHD\models
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Frank Gottwald (FG), <frank.gottwald@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class User_model extends CI_Model {

    /**
     * @var int Stores the user id of the authenticated user.
     */
    private $user_id = 0;

    /**
     * @var string Stores the email address of the authenticated user.
     */
    private $email = '';

    /**
     * @var string Stores the loginname of the authenticated user.
     */
    private $loginname = '';

    /**
     * @var string Stores the forename of the authenticated user.
     */
    private $forename = '';

    /**
     * @var string Stores the lastname of the authenticated user.
     */
    private $lastname = '';

    /**
     * @var array Array, that stores all roles, that are assigned to the authenticated user.
     */
    private $user_roles = array();

    /**
     * @var array Array, that stores all permissions, that are assigned to the authenticated user
     */
    private $user_permissions_all = array();
	
	// course_ids (mapped with roles)
    /**
     * @var array Array, that stores all courses (course ids) with the mapped role the authenticated user
     * has got.
     */
    private $user_course_ids = array();

    /**
     * @var int Stores the degree program id, which the authenticated user participates in. The default value is 0.
     */
    private $studiengang_id = 0;

    /**
     * @var int Stores the id of the semesterplan, that is owned by the authenticated user. The default value is 0.
     */
    private $semesterplan_id = 0;

    /**
     * @var int Stores the semester number of the authenticated user. The default value is 0.
     */
    private $act_semester = 0;

    /**
     * @var int Stores the year, in which the authenticated user (student) started studying. The default value is 0.
     */
    private $studienbeginn_jahr = 0;

    /**
     * @var string Stores the type of the startsemester (wintersemester(ws) or sommersemester (ss)) of the
     *             authenticated user. The default value is 0.
     */
    private $studienbeginn_typ = '';

    /**
     * @var array Array to store all necessary degree program data.
     */
    private $studiengang_data = array();

    /**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
    public function __construct()
	{
		parent::__construct();

        // cal the init function to get all necessary data, while initializing the instance.
		$this->_init();
	}

	/**
	 * Responsible to get all needed userdata
	 */

    /**
     * Gets all necessary userdata from the database and saves it in the instance
     * variables. In addition the userdata is added to the global data array, to provide
     * the information to the views.
     *
     * @access private
     * @return void
     */
    private function _init()
    {
        // get the id of the authenticated user
		$uid = $this->authentication->user_id();
        /*
         * if there is an user id (authenticated user) get all user specific information and store them in the
         * instance variables.
         */
        if ($uid)
		{
            // query and save all user specific information
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
			$this->act_semester = $this->adminhelper->get_act_semester($this->studienbeginn_typ, $this->studienbeginn_jahr);
			$this->update_usersemester($this->user_id, $this->act_semester);

            // get and save information about the degree program of the user
			$this->studiengang_id = $this->_query_studiengang_id();
			$this->studiengang_data = $this->_query_studiengang_data();
			
			// get and save the course ids the user participates in mapped to the roles he has got there
			$this->user_course_ids = $this->_get_course_ids_with_roles();
		}

		// build an array with global userdata
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

		// write userdata to the global $data-array
        $this->data->add('userdata', $userdata);
	}

	/**
	 * Updates the current Semester of the user. Called everytime the user
	 * logs into the system.
	 *
     * @access public
	 * @param integer $user_id UserID of the user.
	 * @param integer $sem_count actual semester.
	 * @return void
	 */
	public function update_usersemester($user_id, $sem_count=0)
	{
		$this->db->update('benutzer', array("Semester" => $sem_count), "BenutzerID = {$user_id}");
	}

	/**
	 * Abstract method to get all the DB data of one row.
     * Selects data from an specified column out of the user table (Datenbanktabelle 'benutzer') for the authenticated
     * user. Therefore the name of the column, needs to be passed as an parameter. The information stored in the
     * desired column will be returned as an array with the column name as an key.
	 *
	 * @param String $columnname Name of the column, from which you want to receive the data.
	 * @return array|null If there is any data for the authenticated user it will be returned in an array with the
     *                    coloumnname as the key. Otherwise null will be returned.
	 */
	private function _query_user_singlecolumndata($columnname)
	{
		$this->db->select($columnname)
				 ->from('benutzer')
				 ->where('BenutzerID', $this->user_id);
		$q = $this->db->get()->row_array();

		return ($q[$columnname]);
	}

	/**
	 * Queries the degree program id for the actual authenticated user and returns it.
	 *
     * @access private
	 * @return integer The desired degree program id.
	 */
	private function _query_studiengang_id()
	{
		$this->db->select('StudiengangID')
				->from('benutzer')
				->where('BenutzerID', $this->user_id)
				;
		$q = $this->db->get()->row_array();

		return $q['StudiengangID'];
	}

	/**
	 * Queries the degree program data by the studiengang id, that is stored
     * in the instance variable $studiengang_id and returns it as an
     * row array.
	 *
     * @access private
	 * @return array Array with all degree program data (every column from the database table 'studiengang').
	 */
	private function _query_studiengang_data()
	{
		$this->db->select('*')
				->from('studiengang')
				->where('StudiengangID', $this->studiengang_id)
				;
		$q = $this->db->get()->row_array();

        return $q;
	}

	/**
	 * Queries the semesterplan id by the actual user id.
	 *
	 * @return mixed All semesterplan data.
	 */
	private function _query_semesterplanid()
	{
		$this->db->select('SemesterplanID')
				->from('semesterplan')
				->where('BenutzerID', $this->user_id)
				;
		$q = $this->db->get()->row_array();

		if ( ! empty($q)) return $q['SemesterplanID'];
	}

	/**
	 * Queries all permissions of the actual authenticated user and returns them as an result array.
	 *
     * @access private
	 * @return Array one-dim array with all permissions the user has got
	 */
	private function _query_all_permissions()
	{
        // get the role of the authenticated user
		$this->db->select('RolleID')
					   ->from('benutzer_mm_rolle')
					   ->where('BenutzerID', $this->user_id);
		$user_id_role = $this->db->get()->result();

		// get the permissions and clean them
		$result_raw = array();
		
		foreach ($user_id_role as $key => $value) {
			$this->db->select('BerechtigungID')
					  ->from('rolle_mm_berechtigung')
					  ->where('RolleID', $value->RolleID);
			$result_raw[] = $this->db->get()->result_array();
		}

		$result_clean = $this->_clean_permissions_array($result_raw);

		return $result_clean;
	}

    /**
     * Cleans the permissions array: Searches for duplicate entries, deletes them
     * and creates an 1 dimensional array and returns it.
     *
     * @access private
     * @param $permissions_to_clean The raw array, that should be cleaned.
     * @return array 1 dimensional array with the cleaned permissions.
     */
    private function _clean_permissions_array($permissions_to_clean)
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

	// checks array for duplicates and deletes these. creates a 1dim array
    /**
     * Cleans the roles array: Searches for duplicate entries, deletes them
     * and creates an 1 dimensional array and returns it.
     *
     * @access private
     * @param $roles_to_clean The raw array, that should be cleaned.
     * @return array 1 dimensional array with the cleaned roles.
     */
	private function _clean_roles_array($roles_to_clean)
	{
		$roles_cleaned = array();
		foreach ($roles_to_clean as $role)
		{
			if ( ! in_array($role['RolleID'], $roles_cleaned))
				{
					array_push($permissions_cleaned, $role['RolleID']);
				}
		}
		return $permissions_cleaned;
	}

	/**
	 * Queries all roles for the actual authenticated user and returns
     * them.
	 *
     * @access private
	 * @return array All roles, the actual authenticated user has got.
	 */
	private function _query_all_roles()
	{
		$this->db->select('RolleID')
				 ->from('benutzer_mm_rolle')
				 ->where('BenutzerID', $this->user_id);

        $all_roles_uncleaned = $this->db->get()->result_array();
        // clean the roles array
        $cleaned_user_roles = $this->_clean_roles_array($all_roles_uncleaned);

        return $cleaned_user_roles;
	}

	/**
     * Query the database for all course ids for the actual authenticated user
     * mapped to the associated roles the user has got in the courses.
     *
     * @access private
	 * @return array(int => int) All course ids mapped to the user roles in an 1 dimensional array.
     *                           The course id is the key of the array.
	 */
	private function _get_course_ids_with_roles(){

	    $ids = array();
	    $course_ids_prof = array();
	    $course_ids_labing = array();
	    $course_ids_tut = array();

	    // look for courses, where the user could be an professor
	    if(in_array(2, $this->user_roles)){
		$course_ids_prof = $this->_get_user_course_ids_from_spkurs();
			if($course_ids_prof){
				foreach ($course_ids_prof as $cid) {
					$ids[$cid] = 2;
				}
			}
	    }
	    // look for courses, where the user could be an labings
	    if(in_array(3, $this->user_roles)){
		$course_ids_labing = $this->_get_user_course_ids_from_labing_tut('kursbetreuer');
			if($course_ids_labing){
				foreach ($course_ids_labing as $cid) {
					$ids[$cid] = 3;
				}
			}
	    }

	    // look for courses, where the user could be an tutor
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
	 * Returns all course-ids a user has got, which are part of the timetable courses (database table
     * 'stundenplankurs') - focus on eventtype 1 !!. WPFs wont`be considered.
     *
     * @access private
	 * @return array All user courses, which are part of the 'stundenplankurs'-table in an 1 dimensional array.
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
			$data = $this->_clean_nested_array($data);
	    }

	    return $data;
	}
	
	/**
	 * Returns all course-ids for that user (labing or tut).
     * Therefore the name of the table where the courses should
     * be selected needs to be passed as an parameter.
	 *
     * @param string $table Name of table that should be used
	 * @return array Array with all containing course_ids
	 */
	private function _get_user_course_ids_from_labing_tut($table){
	    $this->db->select('KursID');
	    $q = $this->db->get_where($table, array('BenutzerID' => $this->user_id));
	    
	    $data = ''; // init
	    
	    foreach ($q->result_array() as $row) { 
			$data[] = $row;
        }

        if($data){
            $data = $this->_clean_nested_array($data);
        }
	    
	    return $data;
	}

	/**
	 * Runs through an nested array and creates an simple indexed array with values,
     * which will be returned afterwards.
     *
     * @access private
	 * @param array $array The array, that should be cleaned.
	 * @return array The cleaned, simple indexed array.
	 */
	private function _clean_nested_array($array){
	    $clean = array();
	    foreach ($array as $a) {
			foreach ($a as $key => $value) {
				$clean[] = $value;
			}
	    }
	    return $clean;
	}

    /*
     * ==================================================================================
     *                                getter and setter methods
     * ==================================================================================
     */

	/**
	 * Returns all roles to which the actual authenticated user is assigned to in an simple
     * array.
     *
     * @access public
	 * @return array Simple (one dimensional) array containing all roles.
	 */
	public function get_all_roles()
	{
		return $this->user_roles;
	}

	/**
	 * Returns all permissions the actual authenticated user has got.
     *
     * @access public
	 * @return array Simple (one dimensional) array with all permissions the user has got.
	 */
	public function get_all_permissions()
	{
		return $this->user_permissions_all;
	}

	/**
	 * Returns the user-id of the authenticated user.
     *
     * @access public
	 * @return int ID of the authenticated user.
	 */
	public function get_userid()
	{
		if ( ! empty($this->user_id) )
		{
			return $this->user_id;
		}
	}

    /**
     * Returns the loginname of the authenticated user.
     *
     * @access public
     * @return string The loginname of the actual authenticated user
     */
    public function get_loginname(){

        // if there is a loginname return it
        if(!empty($this->loginname))
            return $this->loginname;
    }

	/**
	 * Returns the semesterplan-id of the authenticated user. If there is no
     * semesterplan id set, nothing will be returned.
	 *
     * @access public
	 * @return int|nothing If there is an semesterplan id it will be returned, otherwise
     *                  nothing will be returned.
	 */
	public function get_semesterplanid()
	{
		if ( ! empty($this->semesterplan_id) )
		{
			return $this->semesterplan_id;
		}
	}

	/**
	 * Returns the semester number of the actual authenticated user.
     * If there is no semester number saved, nothing will be returned.
	 *
     * @access public
	 * @return int|nothing actual semester.
	 */
	public function get_actsemester()
	{
		if ( ! empty($this->act_semester) )
		{
			return $this->act_semester;
		}
	}

	/**
	 * Returns the Studienbeginn Semestertyp of the authenticated user,
     * if it is set. Otherwise nothing will be returned.
	 *
     * @access public
	 * @return string|nothing WS or SS if it is set, otherwise nothing
     *                        will be returned.
	 */
	public function get_studienbeginn_semestertyp()
	{
		if ( ! empty($this->studienbeginn_typ) )
		{
			return $this->studienbeginn_typ;
		}
	}

	/**
	 * Returns the study start year for the authenticated user,
     * if it is set. Otherwise nothing will be returned.
	 *
     * @access public
	 * @return int|nothing The year in which the user started studying if
     *                     it is set. If it is not set, nothing will be returned.
	 */
	public function get_studienbeginn_jahr()
	{
		if ( ! empty($this->studienbeginn_jahr) )
		{
			return $this->studienbeginn_jahr;
		}
	}

	/**
	 * Returns all course-ids for the authenticated user mapped to the roles
     * he has got.
     *
     * @access public
	 * @return array 1 dimensional array with all courses mapped to the roles. The array
     *               has got the following key-value-structure: [course_id] => [role_id]
	 */
	public function get_user_course_ids(){
	    return $this->user_course_ids;
	}
}