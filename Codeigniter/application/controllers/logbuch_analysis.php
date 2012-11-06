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
 * Logbuch_Analysis-Controller
 * The Logbuch_Analysis-Controller implements and provides the functions for the different analysis,
 * that are available for the authenticated user.
 * which are part of the logbook functionality.
 * @author Christisan Kundruss, <christian.kundruss@fh-duesseldorf.de>
 */
class Logbuch_Analysis extends FHD_Controller {

    /**
     * Default constructor, used for initialization.
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();

        // load the logbuch model
        $this->load->model('logbuch_model');
    }

    /**
     * Opens the analysis overview and displays all courses, for which the user has created an logbook and is able
     * to view course based analysis.
     * @access public
     * @return void
     */
    public function show_possible_courses(){

        $number_of_semesterweeks =  $this->adminhelper->get_semesterweeks($this->adminhelper->getSemesterTyp());

        // get all possible logbook courses
        $all_logbook_courses = $this->logbuch_model->get_all_logbooks($this->authentication->user_id());
        // add all logbook courses to the view
        $this->data->add('logbook_courses',$all_logbook_courses);

        // -- setup for the overall skills chart --
        // get the skills for every logbook
        $logbook_courses_skills_data = $this->_get_overall_skill_chart_data($all_logbook_courses);
        // add the logbook course names in an json object to the view (for displaying in the chart)
        $this->data->add('all_logbook_courses',json_encode($logbook_courses_skills_data['courses']));
        // add the course rating for each user logbook course in an json object to the view (for displaying in the chart)
        $this->data->add('all_logbook_courses_rating', json_encode($logbook_courses_skills_data['skills_per_course']));
        // -- end setup for the overall skill chart

        // -- setup for the overall attendance chart --
        $this->data->add('overall_attendance',json_encode($this->_get_overall_attendance_chart_data($all_logbook_courses)));
        // get the values for the x-axis and add them to the view
        $this->data->add('att_chart_x_scaling', $this->_get_attendance_chart_scaling($this->adminhelper->getSemesterTyp()));
        // get max. count for y-axis
        // calculate the max. count of attendable events -> number of logbook courses * semesterweeks
        $attendable_events = count($all_logbook_courses) * $number_of_semesterweeks;
        $this->data->add('att_chart_y_scaling', $attendable_events);

        // -- end setup for the overall attendance chart --
        // load the view
        $this->load->view('logbuch/analysis/logbook_analysis_course_overview', $this->data->load());
    }

    /**
     * Opens the detail analysis view for the given course id and shows the analysis for the authenticated user
     * and the selected course.
     * @access public
     * @param integer $course_id ID of the selected course
     * @return void
     */
    public function show_analysis_for_course($course_id){

        $number_of_semesterweeks =  $this->adminhelper->get_semesterweeks($this->adminhelper->getSemesterTyp());

        // -- Data for skills chart --
        // get data single topics and ratings and add them to the view
        $this->data->add('skill_data', $this->_get_data_for_skills_chart($course_id));
        // -- end data for skills chart --

        // -- Data for attendance chart --
        // get count of semester weeks and add them to the view
        $this->data->add('max_semester_weeks', $number_of_semesterweeks);

        // get the attended events and add them to the view
        $this->data->add('attended_events', $this->logbuch_model->get_attendance_count_for_course_and_act_semester($course_id, $this->authentication->user_id()));

        // get the occured events and add them to the view
        $this->data->add('occured_events', $this->logbuch_model->get_number_of_course_events_till_today($course_id));

        // get some course informations and add them to the view
        $this->data->add('course_info', $this->logbuch_model->get_course_information($course_id));

        // get the semester type
        $semester_type = $this->adminhelper->getSemesterTyp();

        // set the attendance chart scaling depending on the semester type and add it to the view
        $this->data->add('att_chart_x_scaling', $this->_get_attendance_chart_scaling($semester_type));

        // get data for the attendance chart and pass it as an json object to the view
        $attendance_data_series = json_encode($this->_get_data_for_attendance_chart($course_id));
        $this->data->add('attendance_chart_series', $attendance_data_series);
        // -- end data for attendance chart --

        // load the logbook detail analysis for the selected (logbook)course
        $this->load->view('logbuch/analysis/logbook_detail_analysis', $this->data->load());
    }

    /**
     * Returns the x-axis scaling (range) for the attendance chart depending on the given semester type.
     * @access private
     * @param String $semester_type Type of the Semester(WS / SS)
     * @return Array Returns an one dimensional array with two entries for the attendance chart x-scaling
     */
    private function _get_attendance_chart_scaling($semester_type){
        $scaling = array();
        $act_year = date('Y');

        switch($semester_type){
            case 'SS': // define the scaling for the summer term
                $scaling['min_value'] = 'Date.UTC('. $act_year.', 01, 25)';
                $scaling['nax_value'] = 'Date.UTC('.$act_year.', 06, 25)';
                break;
            case 'WS':
                $scaling['min_value'] = 'Date.UTC('.$act_year.', 07, 30)';
                $scaling['max_value'] = 'Date.UTC('.($act_year+1).', 01,20)';
                break;
        }

        return $scaling;
    }

    /**
     * Fetches the attendance chart series and adds it to the view. The data is fetched in weekly intervals.
     * @access private
     * @param integer $course_id ID of the course where the attendance data should be selected for.
     * @return Array The array with the series data
     */
    private function _get_data_for_attendance_chart($course_id){
        // get the actual date
        $actual_day_date = date("Y-m-d",time());

        // array to hold the semester date information
        $semester_data = $this->_get_semester_dates($this->adminhelper->getSemesterTyp());

        $attendance_count_data_per_date = array();
        // loop from the beginning of the semester to the actual date in 7 day interval and construct the data array for the view
        for($date = $semester_data['start_date']; $date <= strtotime($actual_day_date); $date+= (7 * 24 * 3600)){
            // construct the utc date
            $utc_date = 'Date.UTC('.date("Y", $date).', '.date("m", $date) .', ' . date("d", $date) .')';
            $unix_date = $date;

            // get the count for the acutal looped date
            // construct the range dates from the beginning of the semester till the looped date
            $start_date = date("Y-m-d", $semester_data['start_date']);
            $end_date = date("Y-m-d", $date);

            // multiply unix timestamp by 1000 to get it to the js format
            $series_element = '[' . ($unix_date * 1000).', ' . $this->logbuch_model->get_attendance_count_for_date_range($course_id, $start_date, $end_date).']';

            // add the selected item to the return array
            $attendance_count_data_per_date[] = $series_element;
        }

        // return series data
        return $attendance_count_data_per_date;
    }

    /**
     * Get`s and calculates the data for the skills chart.
     * @access private
     * @param integer $course_id ID of the course where the skills should be selected for
     * @return Array Array with the skills data
     */
    private function _get_data_for_skills_chart($course_id){
        // get the logbook id for the given course and the authenticated user
        $logbook_id = $this->logbuch_model->get_logbook_id($course_id, $this->authentication->user_id());
        // get all logbook entries
        $logbook_entries = $this->logbuch_model->get_all_entries_for_logbook($logbook_id);
        // get the average rating
        $average_rating = $this->logbuch_model->get_avg_rating_for_logbook($logbook_id);

        $skill_data = array();
        // for each logbook entry extract the topic name and the rating
        foreach($logbook_entries as $entry){
            $skill_data['topic'][] = $entry['Thema']; // extract topic name and add it to the skill_data
            $skill_data['rating'][] = intval($entry['Bewertung']); // extract topic rating and add it to the skill data
            $skill_data['avg_rating'][] = intval($average_rating);
        }
        return $skill_data; // return the skill data
    }

    /**
     * Returns the start and end date of the semester type that is passed as an parameter.
     * @access private
     * @param integer $semester_type Type of the semester where the date range should be returned for.
     * @return Array Returns the start and end date of the given semester type in an one
     *               dimensional array with the keys 'start_date' and 'end_date'
     */
    private function _get_semester_dates($semester_type){
        $semester = array();

        switch ($semester_type){
            case 'SS':
                $semester['start_date'] = strtotime(date('Y').'-03-01');
                $semester['end_date'] = strtotime('Y').'-08-31';
                break;
            case 'WS':
                $semester['start_date'] = strtotime(date('Y').'-09-01');
                $semester['end_date'] = strtotime((date('Y')+1).'-02-28');
                break;
        }

        return $semester;
    }

    /**
     * Returns the average course skills rating for the given list (array) of logbook courses.
     * @param Array $all_logbook_courses The array with all user logbook courses
     * @return Array Holds the different logbook names and the correspondig skills array
     */
    private function _get_overall_skill_chart_data($all_logbook_courses) {

        $skills_per_course = array(); // array to hold the skills per course
        $courses = array(); // array to hold each course name

        // get the course name and the average skill for each user logbook course
        foreach($all_logbook_courses as $single_course){
            $courses[] = $single_course['Kursname']; // wirte the act course name into the courses array
            $skills_per_course[] = intval($this->logbuch_model->get_avg_rating_for_logbook($single_course['LogbuchID'])); // get the average raitong and wirte it to the skills array
        }

        $course_skills = array(); // the return array

        // add the course names and the skills array to the return array
        $course_skills['courses'] = $courses;
        $course_skills['skills_per_course'] = $skills_per_course;

        return $course_skills;
    }

    /**
     * Returns the attendance data for all documentated courses for the authenticated user.
     * The attendance count is fetched in weekly (7 days) intervals.
     * @param Array $all_logbook_courses Array / list with all logbooks for the authenticated user
     * @return Array Array, that holds the attendance count for all courses, the overall attendance count, and the format / series
     * dates for the chart.
     */
    private function _get_overall_attendance_chart_data(){
        // get the actual date
        $actual_day_date = date("Y-m-d",time());

        // array to hold the semester date information
        $semester_data = $this->_get_semester_dates($this->adminhelper->getSemesterTyp());

        $overall_course_attendance = array();
        // loop from the beginning of the semester to the actual date in 7 day interval and construct the data array for the view
        for($date = $semester_data['start_date']; $date <= strtotime($actual_day_date); $date+= (7 * 24 * 3600)){
            // construct the utc date
            $utc_date = 'Date.UTC('.date("Y", $date).', '.date("m", $date) .', ' . date("d", $date) .')';
            $unix_date = $date;

            // get the count for the acutal looped date
            // construct the range dates from the beginning of the semester till the looped date
            $start_date = date("Y-m-d", $semester_data['start_date']);
            $end_date = date("Y-m-d", $date);

            // multiply unix timestamp by 1000 to get it to the js format
            $series_element = '[' . ($unix_date * 1000).', ' . $this->logbuch_model->get_attendance_count_for_all_courses_in_range($start_date, $end_date).']';

            // add the selected item to the return array
            $overall_course_attendance[] = $series_element;
        }

        // return series data
        return $overall_course_attendance;
    }
}
/* End of file logbuch_analysis.php */
/* Location: ./application/controllers/logbuch_analysis.php */