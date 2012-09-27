<?php   if (!defined('BASEPATH')) exit('No direct script access allowed');

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
class Veranstaltungen extends FHD_Controller {
 

    /**
     * Constructor Method
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function __construct()
	{
		parent::__construct();
		$this->data->add('titel', "Veranstaltungen");
		//Loads Stundenplan_Model because only its functions are needed
		$this->load->model('Stundenplan_Model');
	}
	
	

    /**
     * Index-method, loads for testing purposes the Stundenplan_view
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function index()
	{

		$stundenplan = $this->Stundenplan_Model->get_stundenplan($this->authentication->user_id());
		
		$this->data->add('kurse', $stundenplan[3]);
			$this->krumo->dump($stundenplan[3]);
		
		$this->load->view('veranstaltungen/index', $this->data->load());
		
	}

}