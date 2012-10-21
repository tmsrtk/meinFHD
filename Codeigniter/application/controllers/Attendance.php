<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class Attendance
 *
 * The Attendance-Controller / Attendance-Class provides the attendance widget on the dashboard and the base functions to document
 * the students attendance for his courses.
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

class Attendance extends FHD_Controller {

    /**
     * Base constructor, used for initialization.
     * @access public
     * @return void
     * @todo embed authentification check, if it is implemented
     */
    public function __construct() {
        parent::__construct();

        // security check / protection to prevent access for unauthorized users
        // check if user is authenticated, otherwise he will be redirected to the login page
        // $this->authentication->check_for_authentication(); TODO remove comment when sso login is merged

        // load the logbuch model
        $this->load->model('logbuch_model');
    }

    /**
     * Initializes and loads the attendance widget as an partial view and returns it
     * as an string.
     * @access public
     * @return string The loaded attendance widget view in an string.
     */
    public function load_attendance_widget_as_string() {

        $result = '';

        $running_course = $this->logbuch_model->get_running_course(); // get the act running course
        $max_events_semester = $this->adminhelper->get_semesterweeks($this->adminhelper->getSemesterTyp()); // get the max. semesterweeks

        // only do the following if there is an running course
        if (count($running_course) > 0){
            // query out the right course id, if the user is got from an older degree program he will have got an different courseid (maybe)
            $students_degree_prog_course = $this->logbuch_model->query_right_stdg_course_for_given_course($running_course['KursID'], $this->authentication->user_id());
            $running_course['KursID'] = $students_degree_prog_course['KursID'];
            $running_course['attended_events'] = $this->logbuch_model->get_attendance_count_for_course_and_act_semester($running_course['KursID'], $this->authentication->user_id()); // get the count of attendance and pass it to the view
            // get the number of occured events for the act semester and return it to the view
            $running_course['occured_events'] = $this->logbuch_model->get_number_of_course_events_till_today($running_course['KursID']);
            $running_course['attended_events_percent'] = ($running_course['attended_events'] / $running_course['occured_events']) * 100;

            $btn_attend_state = '';
            // already tracked attendance or reached the maximum value of events?
            if($this->logbuch_model->already_attending_today($running_course['KursID']) || $running_course['attended_events'] == $max_events_semester){
                $btn_attend_state = 'disabled';
            }

            $running_course['btn_attend_state'] = $btn_attend_state;
        }

        $this->data->add('running_course', $running_course); // add data to the view
        $this->data->add('max_events', $max_events_semester);
        // check if user has already attended the course for today and add the info to the view
        $result = $this->load->view('logbuch/partials/logbook_attendance_widget', $this->data->load(), TRUE);

        return $result;
    }

    /**
     * Saves a new attendance record to the database with the submitted data for the actual authenticated user.
     * The function will be called if a user submits the 'Ich bin hier'- Button on the attendance widget.
     * @access public
     * @return void
     */
    public function save_new_attendance() {

        // save the posted course id
        $running_course_id = $this->input->get('running_course_id');
        // query out the right course id, if the user is got from an older degree program he will have got an different courseid (maybe)
        $students_degree_prog_course_id = $this->logbuch_model->query_right_stdg_course_for_given_course($running_course_id, $this->authentication->user_id());
        $course_id_to_use = $students_degree_prog_course_id['KursID'];

        // check if there is an entry for the current event in the studiengangkurs_veranstaltung table
        if(!$this->logbuch_model->is_course_event_stored($course_id_to_use)){        // if not insert it
            $this->logbuch_model->add_course_event($course_id_to_use);
        }
        // write the attendance record to the database
        $this->logbuch_model->save_attendance_for_course_with_current_time($course_id_to_use, $this->authentication->user_id());

        // display out the result view
        echo $this->load_attendance_widget_as_string();
    }

    /**
     * Searches a logbook for the course id that is submitted via POST.
     * Method is usually called via ajax from the attendance widget.
     * If there is an logbook the link to the logbook will be echoed.
     * @return void
     */
    public function ajax_search_logbook_for_course() {

        // get the course_id via post
        $course_id = $this->input->post('course_id');
        // query out the right course id, if the user is got from an older degree program he will have got an different courseid (maybe)
        $students_degree_prog_course = $this->logbuch_model->query_right_stdg_course_for_given_course($course_id, $this->authentication->user_id());
        $course_id_to_use = $students_degree_prog_course['KursID'];

        // check if an logbook exists
        if($this->logbuch_model->check_logbook_course_existence_for_user($course_id_to_use, $this->authentication->user_id())){ // yes there is an logbook
            // generate the link that should be called from ajax / the view
            $open_logbook_link = base_url() . 'logbuch/open_logbook_for_course_and_user/'.$course_id.'/'.$this->authentication->user_id();
            echo $open_logbook_link;
        }
        else {
            // there is no logbook
            echo 'no_logbook';
        }
    }

}
/* End of file attendance.php */
/* Location: ./application/controllers/attendance.php */