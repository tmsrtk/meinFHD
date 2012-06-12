<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Global_data {

	private $global_data = array();
	private $CI;

	//1361 admin, 1360 dozent
	private $userid = 1357;  // muss dynamisch von der aktuellen session geladen werden

	/*
	*/
	function __construct()
	{
		// !! Assigning by reference allows you to use the original CodeIgniter 
		// object rather than creating a copy of it. !!
		$this->CI =& get_instance();
		$this->init();
	}

	/*
	*/
	function init()
	{
		$this->CI->load->model('admin_model');

		// userdata
		$loginname = $this->CI->admin_model->get_loginname($this->userid);
		$user_permissions = $this->CI->admin_model->get_all_userpermissions($this->userid);
		$roles = $this->CI->admin_model->get_all_roles();
		
		$userdata = array(
				'userid' => $this->userid,
				'username' => $loginname['LoginName'],
				'userpermissions' => $user_permissions,
				'roles' => $roles
			);

		// var_dump($userdata);

		$this->add_global_data('userdata', $userdata);
	}

	/*
	*/
	public function add_global_data($key, $value = '')
	{
		$this->global_data[$key] = $value;
	}

	/*
	*/
	public function get_global_data()
	{
		return $this->global_data;
	}


}

/* End of file Global_data.php */