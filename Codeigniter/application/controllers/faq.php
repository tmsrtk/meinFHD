<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Peter Jaraczewski (PJ), <peter.jaraczewski@fh-duesseldorf.de>
 */

/**
 * Class Faq
 *
 * Description...
 */
class Faq extends FHD_Controller {
	
	// default constructor to prepare all needed stuff
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * Index
	 *
	 * .../faq
	 * .../faq/index
	 */
	public function index()
	{
		$this->load->view('faq/index', $this->data->load());
	}
}

/* End of file faq.php */
/* Location: ./Application/Controllers/faq.php */