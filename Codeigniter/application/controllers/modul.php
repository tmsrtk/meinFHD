<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Simon vom Eyser (SVE), <simon.vomeyser@fh-duesseldorf.de>
 */

/**
 * Modul-Controller 
 *
 */
class Modul extends FHD_Controller {
 

    /**
     * Constructor Method
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function __construct()
	{
		parent::__construct();
		$this->data->add('titel', "Stundenplan");
		$this->load->model('Modul_Model');
	}
	
	

    /**
     * Index-method, loads for testing purposes the Stundenplan_view
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function index()
	{

		//$this->load->view('modul');

	}

	public function show($course_id)
	{	
		$user_id = $this->authentication->user_id();
		$courseinfo = $this->Modul_Model->get_courseinfo($user_id, $course_id);
		$this->data->add('courseinfo', $courseinfo);

		$this->load->view('modul/index.php', $this->data->load());
	}

}
	
	
	
	
	