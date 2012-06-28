<?php

class User_model extends CI_Model {

	private $user_email = '';
	private $loginname = '';
	private $forename = '';
	private $lastname = '';

	private $user_roles = array();
	private $user_permissions_all = array();


	public function __construct()
	{
		parent::__construct();

		$user_id = $this->authentication->user_id();
		if ($user_id)
		{
			$this->user_permissions_all = $this->_query_all_permissions();
		}


		// write userdata in global $data
		$this->data->add('rollentest', $this->user_roles);
	}


	private function _query_all_permissions()
	{
		$this->db->select('RolleID')
					   ->from('benutzer_mm_rolle')
					   ->where('BenutzerID', $user_id);
		$user_id_role = $this->db->get()->result();

		// var_dump($user_id_role);

		// return;

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

}