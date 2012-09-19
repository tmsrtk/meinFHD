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

    // instance variables
    private $attendance_widget; // variable to store the attendance widget instance

    /**
     * Default constructor used for initialization and security check.
     * @author Christian Kundruss (CK)
     * @access public
     */
    public function __construct(){
        parent::__construct();

        // security check / protection to prevent access for unauthorized users
        // check if user is authenticated, otherwise he will be redirected to the login page
        // $this->authentication->check_for_authentication(); TODO remove comment when sso login is merged

        // include the attendance class and instantiate it
        include( APPPATH . 'controllers/attendance.php');
        $this->attendance_widget = new Attendance();
    }

	/**
	 * Index
	 *
	 * .../dashboard
	 * .../dashboard/index
	 */
	public function index()
	{
        // edit by CK -> load the attendance widget partial and add id to the view
        $attendance_widget_data = $this->attendance_widget->load_attendance_widget_as_string();
        $this->data->add('attendance_widget', $attendance_widget_data);

		$this->load->view('dashboard/index', $this->data->load());
	}
}

/* End of file App.php */
/* Location: ./application/controllers/App.php */