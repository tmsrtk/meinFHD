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
        // check if the user is logged in, if he is not logged in he can`t access the requested site
        if(!$this->authentication->is_logged_in()) { // the user is not logged in -> redirect him to the login page
            redirect('app/login');
        }
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