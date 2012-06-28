<?php

class Test extends FHD_Controller {

	public function __constructor()
	{
		parent::__constructor();

		// $this->load->model('user_model');

		// get userdata
	}

	public function index()
	{
		$siteinfo = array(
			'title'			=> 'Test',
			'main_content'	=> 'test'
			);
		$this->data->add('siteinfo', $siteinfo);

		$this->load->view('includes/template', $this->data->load());
	}

}