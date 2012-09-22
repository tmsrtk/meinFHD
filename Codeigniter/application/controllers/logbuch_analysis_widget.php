<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Logbuch_Analysis_Widget-Controller
 * The Logbuch_Analysis_Widget-Controller provides the analysis widget, which
 * is displayed on the dashboard (frontpage) of meinFHD.
 * @author Christian Kundruss, <christian.kundruss@fh-duesseldorf.de>
 */
class Logbuch_Analysis_Widget extends FHD_Controller {

    /**
     * Default constructor, used for initialization.
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();

        // load the logbuch model
        $this->load->model('logbuch_model');

        // security check / protection to prevent access for unauthorized users
        // check if user is authenticated, otherwise he will be redirected to the login page
        // $this->authentication->check_for_authentication(); TODO remove comment when sso login is merged
    }

    /**
     * Initializes and loads the analysis widget as an partial view and returns it
     * as an string.
     * @access public
     * @return string The widget content view as an string
     */
    public function load_analysis_widget_as_string() {
        $result = '';
        $running_course = $this->logbuch_model->get_running_course();

        if (count($running_course) > 0){ // is there an running course
            $students_degree_prog_course = $this->logbuch_model->query_right_stdg_course_for_given_course($running_course['KursID'], $this->authentication->user_id());
            $running_course['KursID'] = $students_degree_prog_course['KursID'];
        }

        $this->data->add('running_course', $running_course);
        $result = $this->load->view('logbuch/analysis/logbook_analysis_widget', $this->data->load(), TRUE);

        return $result;
    }

}
