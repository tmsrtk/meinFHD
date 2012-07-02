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