<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Dashboard
 *
 * The class dashboard/ dashboard controller provides all necessary control functions
 * for the dashboard.
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012-2013
 * @link http://www.fh-duesseldorf.de
 * @author Manuel Moritz (MM), <manuel.moritz@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Dashboard extends FHD_Controller {

    /**
     * @var Attendance Variable for storing an Logbuch_Analaysis_Widget instance
     */
    private $attendance_widget;

    /**
     * @var Logbuch_Analysis_Widget Variable for storing an Logbuch_Analaysis_Widget instance
     */
    private $analysis_widget;

    /**
     * @var Admin Variable for storing an Admin-Controller instance
     */
    private $admin_controller;

    /**
     * @var Stundenplan Variable for storing an Stundenplan-Controller instance
     */
    private $stundenplan_controller;

    /**
     * Default constructor used for initalization.
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();

        // include the attendance class and instantiate it
        include(APPPATH . 'controllers/attendance.php');
        $this->attendance_widget = new Attendance();

        // include the analysis widget controller (class) and instantiate it
        include (APPPATH . 'controllers/logbuch_analysis_widget.php');
        $this->analysis_widget = new Logbuch_Analysis_Widget();

        // save instances for the timetable and the admin controller for default routing
        include(APPPATH . 'controllers/admin.php');
        $this->admin_controller = new Admin();

        include(APPPATH . 'controllers/stundenplan.php');
        $this->stundenplan_controller = new Stundenplan();
    }

	/**
	 * Index
     * Default method, loads the index page of the dashboard.
     * Since the dashboard is deactivated the function implements an role
     * specific routing.
	 *
     * The function will be called during the usage of the following url patterns:
	 * .../dashboard
	 * .../dashboard/index
     *
     * @access public
     * @return void
	 */
	public function index()
	{
        // role based default routing - dashboard will be deactivated
        // if the user is an admin redirect to admin backend
        if (in_array(Roles::ADMIN, $this->user_model->get_all_roles())){
            $this->admin_controller->index();
        }
        else { // redirect to timetable
            $this->stundenplan_controller->index();
        }

        /*
        // edit by CK -> load the attendance widget partial and add id to the view
        $attendance_widget_data = $this->attendance_widget->load_attendance_widget_as_string();
        $this->data->add('attendance_widget', $attendance_widget_data);

        // edit by CK -> load the analysis widget partial and add id to the view
        $analysis_widget_data = $this->analysis_widget->load_analysis_widget_as_string();
        $this->data->add('analysis_widget', $analysis_widget_data);

        $this->load->view('dashboard/index', $this->data->load());
        */
	}
	
	public function mobile()
	{
		// There's no need for an extra dashboard on desktop devices
		if ( ! $this->agent->is_mobile())
		{
			redirect('dashboard/index');
		}
		// On mobile devices a list of common tasks is loaded
		$this->load->view('dashboard/mobile', $this->data->load());
	}
}
/* End of file App.php */
/* Location: ./application/controllers/App.php */