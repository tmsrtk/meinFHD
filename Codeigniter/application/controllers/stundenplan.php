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
 * Stundenplan Controller 
 */
class Stundenplan extends FHD_Controller {
    /*
     * data-array, the whole data concerning the timetable will be stored here in nested arrays.
     * 
     * @var Array
     */
    private $data;
    
	
	

    /**
     * Constructor Method
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function __construct()
	{
		parent::__construct();
		$this->data['titel'] = "Stundenplan";
		$this->load->model('Stundenplan_Model');
	}

/**
 * 
 */	
    /**
     * Index-method, loads for testing purposes the Stundenplan_view
     * 
     * @param type name // nicht vorhanden
     * @return type // nicht vorhanden
     */
	public function index()
	
	{
		$stundenplan = $this->Stundenplan_Model->get_stundenplan(1384);

		$this->data['tage'] = $stundenplan[0];
		$this->data['zeiten'] = $stundenplan[1];
		$this->data['stundenplan'] = $stundenplan[2];
		$this->data['aktivekurse'] = $stundenplan[3]; //Debug

		//$this->krumo->dump($this->data);
		$this->load->view('Stundenplan_View', $this->data);
	}

}
	
	
	
	
	