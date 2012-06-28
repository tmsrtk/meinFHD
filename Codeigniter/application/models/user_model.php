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
			$this->user_roles = $this->_query_all_roles();
		}


		// write userdata in global $data
		$this->data->add('rollentest', $this->user_roles);
	}

	private function _query_all_roles()
	{
		$this->db->select('RolleID, bezeichnung')
				 ->from('rolle');
		$q = $this->db->get();

		return $q->result_array();
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