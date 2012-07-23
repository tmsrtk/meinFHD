<?php

class Test extends FHD_Controller {

	public function __constructor()
	{
		parent::__constructor();

		$this->load->model('test_model');

		// get userdata
		//// data
		// userdata
        // $session_userid = $this->authentication->user_id();

        // $loginname = $this->admin_model->get_loginname($session_userid);                ///////////////////////////////
        // $user_permissions = $this->admin_model->get_all_userpermissions($session_userid);
        // $roles = $this->admin_model->get_all_roles();
        
        // $userdata = array(
        //         'userid' => $session_userid,
        //         'loginname' => $loginname['LoginName'],
        //         'userpermissions' => $user_permissions,
        //         'roles' => $roles
        //     );

        // $this->data->add('userdata', $userdata);
	}

	public function index()
	{
		$siteinfo = array(
			'title'			=> 'Test',
			'main_content'	=> 'test'
			);
		$this->data->add('siteinfo', $siteinfo);

		$this->load->view('test/index', $this->data->load());
	}

}