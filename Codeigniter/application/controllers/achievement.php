<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Achievement Controller (Class)
 *
 * The achievement controller class implements the logic for the achievement-system.
 * It check`s for new achievements (the function need to be called after each action),
 * gives the user new achievements, gives the user a feedback if an new achievement is
 * earned.
 *
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

class Achievement extends FHD_Controller {

    // declaration of instance variables
    private $CI;
    private $user_id;

    /**
     * Default Constructor, used for initialization
     * @access public
     */
    public function __construct(){
        parent::__construct();

        $this->CI =& get_instance(); // get the ci instance

        $this->user_id = $this->authentication->user_id(); // save the uid of the current authenticated user

        // load the logbuch and the achievement model
        $this->load->model('logbuch_model');
        $this->load->model('achievement_model');
    }

    /**
     * Checks if the user has unlocked an new attendance achievement. If he has unlocked an new achievement,
     * a message will be returned.
     * @access public
     * @param $course_id ID of the course where the attendance achievements should be checked for
     */
    public function ajax_check_for_new_attendance_achievement($course_id){
        // get the number of actual attended events, for the given course, the authenticated user and the act semester
        $attended_events = $this->logbuch_model->get_attendance_count_for_course_and_act_semester($course_id, $this->user_id);
        // get the matching achievement (achievementlevel), if there is no matching achievement FALSE will be the value
        $matching_achievement = $this->achievement_model->get_matching_achievement('Anwesenheit', $attended_events);

        $result = ''; // result variable to hold the information, which should be 'returned' to the view

        $result = $this->_unlock_achievement($matching_achievement, $course_id);

        // 'return' / echo the result to the view
        echo $result;
    }

    /**
     * Checks if the user has unlcoked an new skills achievement. If he has unlocked an new achievement, a message
     * (modal) will bei displayed in the view.
     * @access public
     * @param $course_id ID of the course where the skills should be checked for.
     */
    public function ajax_check_for_new_skill_achievement($course_id){

        // get the id of the user specific (course-)logbook
        $logbook_id = $this->logbuch_model->get_logbook_id($course_id, $this->user_id);

        $result = ''; // result variable to hold the information, which should be 'returned' to the view

        // if the user has saved more than 5 topics in the logbook, than check for an achievement
        if($this->achievement_model->get_saved_topic_count_for_logbook($logbook_id) >= 5){
            // get the avg course / logbook rating
            $course_skill_rating = $this->logbuch_model->get_avg_rating_for_logbook($logbook_id);
            // get the matching achievement (achievementlevel), if there is no matching achievement the value will be FALSE
            $matching_achievement = $this->achievement_model->get_matching_achievement('Kenntnisse', $course_skill_rating);
            // unlock the achievement, if it has not unlocked by the specified course
            $result = $this->_unlock_achievement($matching_achievement, $course_id);
        }

        // 'return' / echo the result to the view
        echo $result;
    }

    /**
     * Checks if the matching achievement exists and is not already earned for the given course. If the matching achievement
     * has not been unlocked for the given course, it will be unlocked and the modal message view will be returned as an string.
     * Otherwise there won`t be any action, and an string with the info 'no_achievement_unlocked' will be returned.
     * @access public
     * @param $matching_achievement Array with information about the achievement type, that matches
     *        to the course activity.
     * @param $course_id ID of the course, where the achievement should be unlocked for.
     * @return string If an achievement has been successfully unlocked the modal view will be returned as an string, otherwise there
     *         there will be 'no_achievement_unlocked' returned.
     */
    private function _unlock_achievement($matching_achievement, $course_id){
        $result = ''; // variable for the string, that should be returned

        // check if there is any matching achievement object
        if($matching_achievement){
            // check if the user has already got the matching achievement for the given course
            if(!($this->achievement_model->check_user_has_unlocked_achievement_for_course($course_id, $matching_achievement['AchievementID']))){
                // the achievement has not been unlocked -> unlock / give it to the user
                $this->achievement_model->grant_achievement_for_course($course_id, $this->user_id, $matching_achievement['AchievementID']);

                // get the course information and add them to the view
                $this->data->add('course_information',$this->logbuch_model->get_course_information($course_id));
                // add the achievement information to the view
                $this->data->add('achievement_information', $matching_achievement);

                // construct the badge (image) url and add it to the view
                $badge_url = base_url() . 'resources/img/achievement_badges/' . $matching_achievement['Aktivitaetenname'] . '/level_' . $matching_achievement['LevelNr'] . '.png';
                $this->data->add('badge_url', $badge_url);

                // load the modal as an string
                $result = $this->load->view('logbuch/achievement/achievement_modal', $this->data->load(), TRUE);
            }
            else {
                $result = 'no_achievment_unlocked';
            }
        }
        else { // the user has already unlocked the appropriate achievement
            // message to the view, that the modal does not need to be shown
            $result = 'no_achievement_unlocked';
        }

        return $result;
    }

    /**
     * Shows and opens up the achievement gallery to show the user specific
     * achievements. If the authenticated user hasn`t unlocked any achievements
     * a motivation message will be displayed.
     * @access public
     * @return void
     */
    public function show_achievement_gallery(){
        // get the user achievement data and add it to the view
        $user_achievement_data = $this->achievement_model->generate_user_achievement_data($this->user_id);
        $this->data->add('achievement_data', $user_achievement_data);
        // load the view
        $this->load->view('logbuch/achievement/achievement_gallery', $this->data->load());
    }
}
/* End of file achievement.php */
/* Location: ./application/controllers/achievement.php */