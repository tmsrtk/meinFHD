<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Fabian Martinovic (FM), <fabian.martinovic@fh-duesseldorf.de>
 */

/**
 * Hilfe-Controller
 */
class Hilfe extends FHD_Controller {
	
	// default constructor to prepare all needed stuff
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Index
	 *
	 * .../hilfe
	 * .../hilfe/index
	 */
	public function index()
	{
		$this->load->view('hilfe/index', $this->data->load());
	}
}

/* End of file App.php */
/* Location: ./application/controllers/hilfe.php */
