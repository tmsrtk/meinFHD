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
 * Dozent-Controller 
 *
 */
class Dozent extends FHD_Controller {
 

    /**
     * Constructor Method
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function __construct()
	{
		parent::__construct();
		$this->data->add('titel', "Dozent");
		$this->load->model('Dozent_Model');
	}
	
	

    /**
     * Index-method, loads for testing purposes the Stundenplan_view
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function index()
	{


	}


	public function show($dozent_id)
	{	

		$dozentinfo = $this->Dozent_Model->get_dozentinfo($dozent_id);

		$this->data->add('dozentinfo', $dozentinfo);

		$this->load->view('dozent/index', $this->data->load());
	}

}