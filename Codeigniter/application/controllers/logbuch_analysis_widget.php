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
 *
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
        $course_skills = array();
        if (count($running_course) > 0){ // is there an running course
            $students_degree_prog_course = $this->logbuch_model->query_right_stdg_course_for_given_course($running_course['KursID'], $this->authentication->user_id());
            $running_course['KursID'] = $students_degree_prog_course['KursID'];
            // caluclate the act. course skills
            $course_skills = $this->_calculate_skill_data_for_chart($running_course['KursID']);
            // add the course skills to the view
            $this->data->add('act_skills', $course_skills['act_skills']);
            $this->data->add('missing_skills', $course_skills['missing_skills']);
        }
        // add the needed data to the view
        $this->data->add('running_course', $running_course);
        $result = $this->load->view('logbuch/analysis/logbook_analysis_widget', $this->data->load(), TRUE);

        return $result;
    }


    private function _calculate_skill_data_for_chart($course_id){
        // get the the logbook id, that corresponds to the authenticated user and the course_id
        $logbook_id = $this->logbuch_model->get_logbook_id($course_id, $this->authentication->user_id());

        // get the average course rating
        $act_skill_rating = $this->logbuch_model->get_avg_rating_for_logbook($logbook_id);

        // calculate the missing skill
        $missing_skill_rating = 100 - $act_skill_rating;

        $skill_data = array();
        $skill_data['act_skills'] = intval($act_skill_rating);
        $skill_data['missing_skills'] = $missing_skill_rating;

        return $skill_data;
    }

}
/* End of file logbuch_analysis_widget.php.php */
/* Location: ./application/controllers/logbuch_analysis_widget.php */
