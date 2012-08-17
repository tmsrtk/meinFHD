<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>
 */

/**
 * Class App
 *
 * Description...
 */
class Dashboard extends FHD_Controller {

    // default constructor
    public function __construct(){
        parent::__construct();

        // --- EDIT BY Christian Kundruss (CK) for sso authentication ---
        // call the security_helper and check if the user is authenticated an allowed to call the controller
        $this->authentication->check_for_authenticaton();
        // --- END EDIT --

    }
	/**
	 * Index
	 *
	 * .../dashboard
	 * .../dashboard/index
	 */
	public function index()
	{
		$this->load->view('dashboard/index', $this->data->load());
	}
}

/* End of file App.php */
/* Location: ./application/controllers/App.php */