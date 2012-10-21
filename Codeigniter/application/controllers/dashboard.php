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
    private $analysis_widget; // variable to store the analysis widget instance

    // default constructor
    public function __construct(){
        parent::__construct();

        // --- EDIT BY Christian Kundruss (CK) for sso authentication ---
        // call the security_helper and check if the user is authenticated an allowed to call the controller
        $this->authentication->check_for_authenticaton();
        // --- END EDIT --

        // include the attendance class and instantiate it
        include( APPPATH . 'controllers/attendance.php');
        $this->attendance_widget = new Attendance();

        // include the analysis widget controller (class) and instantiate it
        include ( APPPATH . 'controllers/logbuch_analysis_widget.php');
        $this->analysis_widget = new Logbuch_Analysis_Widget();

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

        // edit by CK -> load the analysis widget partial and add id to the view
        $analysis_widget_data = $this->analysis_widget->load_analysis_widget_as_string();
        $this->data->add('analysis_widget', $analysis_widget_data);

        $this->load->view('dashboard/index', $this->data->load());
	}
}
/* End of file App.php */
/* Location: ./application/controllers/App.php */