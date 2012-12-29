<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Fabian Martinovic (FM), <fabian.martinovic@fh-duesseldorf.de>
 */

/**
 * FAQ-Controller
 *
 * Controller with all necessary functions for the frequently asked questions.
 */
class FAQ extends FHD_Controller {
	
    /**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * Loads the faq index view.
	 * Function is usually called when the following url patterns are submitted:
	 * .../faq
	 * .../faq/index
     *
     * @access public
     * @return void
	 */
	public function index()
	{
		$this->load->view('faq/index', $this->data->load());
	}
}
/* End of file faq.php */
/* Location: ./application/controllers/faq.php */
