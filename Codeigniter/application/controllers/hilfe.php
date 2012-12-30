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
 * Hilfe-Controller
 *
 * Controller provides all methods for displaying the help content.
 */
class Hilfe extends FHD_Controller {
	
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
	 * Loads the help/hilfe index view
	 * Function is usually called when the following url patterns are submitted:
	 * .../hilfe
	 * .../hilfe/index
     *
     * @access public
     * @return void
	 */
	public function index()
	{
		$this->load->view('hilfe/index', $this->data->load());
	}
}
/* End of file hilfe.php */
/* Location: ./application/controllers/hilfe.php */
