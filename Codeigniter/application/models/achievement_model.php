<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @copyright Christian Kundruß, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Achievement Model
 * The achievement model deals with all db operations, that are necessary for
 * showing, saving, and checking for (new) achievements.
 *
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Achievement_Model extends CI_Model {

    /**
     * Searches for the achievement object, that matches to the given achievement type and the given (count) value
     * @access public
     * @param string $activity_name Name of the user triggered event / activity / action type which is configured for the achivement system.
     * @param integer $value Actual value of the activity (attendance count, or skill rating)
     * @return array Returns the matching achievement dataset in an array.
     */
    public function get_matching_achievement($activity_name, $value){
        // query for the matching achievement
        $query = $this->db->query("
                            SELECT achievement.AchievementID, achievement.Achievementname, achievement.Motivationstext, achievement.Minimalgrenze,
                            achievement.Maximalgrenze, achievement.LevelNr, achievementart.Aktivitaetenname
                            FROM achievement, achievementart
                            WHERE " . $value ." BETWEEN achievement.Minimalgrenze AND achievement.Maximalgrenze
                            AND achievement.AchievementartID = ( SELECT AchievementartID
                                                       FROM achievementart
                                                       WHERE Aktivitaetenname = '". $activity_name ."'
                                                     )
                                ");

        // return the matching achievement dataset as an array
        return $query->row_array();
    }

    /**
     * Checks if the user has already unlocked the given achievement (check by using the achievement id, it`s unique) for the
     * given course id.
     * @access public
     * @param integer $course_id ID of the course where the achievement should be unlocked for
     * @param integer $achievement_id ID of the achievement that can be unlocked
     * @return bool TRUE if the user has already unlocked the given achievement, otherwise FALSE
     */
    public function check_user_has_unlocked_achievement_for_course($course_id, $achievement_id){
        $this->db->select('*');
        $this->db->from('benutzer_achievement');
        $this->db->where('BenutzerID', $this->authentication->user_id());
        $this->db->where('KursID', $course_id);
        $this->db->where('AchievementID', $achievement_id);

        $query = $this->db->get(); // query the database to check if the achievement has already been unlocked

        if($query->num_rows() > 0){ // the achievement has been unlocked for the current course
            return TRUE; // achievement has already been unlocked
        }

        return FALSE; // achievement has not been unlocked
    }

    /**
     * Grant`s the specified achievement for the given course to the specified user and
     * saves it persistent in the user_achievement table.
     * @access public
     * @param integer $course_id ID of the course, where the achievement has been unlocked for.
     * @param integer $user_id ID of the user who unlocked the achievement.
     * @param integer $achievement_id ID of the achievement, that has been unlocked.
     * @return void
     */
    public function grant_achievement_for_course($course_id, $user_id, $achievement_id){
        // prepare the data to insert
        $data = array(
            'BenutzerID' => $user_id,
            'KursID' => $course_id,
            'AchievementID' => $achievement_id
        );

        // insert the data into the user_achievement table
        $this->db->insert('benutzer_achievement', $data);
    }

    /**
     * Fetches all distinct achievement levels, where achievements have been unlocked by the given user.
     * @access private
     * @param integer $user_id ID of the user where the distinct achievement levles should be fetched.
     * @return array The array with the different user unlocked achievement levels
     */
    private function _get_distinct_user_achievement_levels($user_id){
        // query the database for the different user earned achievement levels
        $query = $this->db->query("
                                    SELECT distinct(achievement.LevelNr)
                                    FROM achievement, achievementart, benutzer_achievement
                                    WHERE achievement.AchievementartID = achievementart.AchievementartID
                                    AND achievement.AchievementID = benutzer_achievement.AchievementID
                                    AND benutzer_achievement.BenutzerID =" . $user_id . "
                                ");

        // generate the result and return it
        return $query->result_array();
    }

    /**
     * Returns the unlocked achievement 'objects' for the given level and the given user_id.
     * @access private
     * @param integer $user_id ID of the user, who has unlocked the different achievements
     * @param integer $level_nr ID / number of the level for which the unlocked achievements should be fetched.
     * @return array The array with the distinct achievements per course.
     */
    private function _get_distinct_user_achievement_objects_for_level($user_id, $level_nr){

        // query the database for the distinct achievementtypes, for the given level and the given user_id
        $query = $this->db->query("
                                    SELECT distinct(achievement.AchievementID), achievement.Achievementname,
                                           achievementart.Aktivitaetenname, achievement.LevelNr, achievement.Motivationstext
                                    FROM benutzer_achievement, achievement, achievementart
                                    WHERE benutzer_achievement.AchievementID = achievement.AchievementID
                                    AND achievement.AchievementArtID = achievementart.AchievementartID
                                    AND achievement.LevelNr = " . $level_nr . "
                                    AND benutzer_achievement.BenutzerID = " . $user_id . "
                                ");

        // array to hold the courses to return
        $achievements_per_level_w_courses = array();

        foreach($query->result_array() as $row){
            $row['courses'] = $this->_get_courses_for_unlocked_achievement($user_id, $row['AchievementID']);
            $achievements_per_level_w_courses[] = $row;
        }

        return $achievements_per_level_w_courses;
    }

    /**
     * Returns all courses, for which the specified (given) achievement has been unlocked by the given user.
     * @access private
     * @param integer $user_id ID of the, who has unlocked the achievement type
     * @param integer $achievement_id ID of the achievement that has been unlocked by the user.
     * @return array Array with the different courses where the given achievement has been unlocked for.
     */
    private function _get_courses_for_unlocked_achievement($user_id, $achievement_id){

        // query the database and ask for the course names, for which the achivement has been unlocked
        $query = $this->db->query("
                                    SELECT studiengangkurs.Kursname, studiengangkurs.kurs_kurz
                                    FROM benutzer_achievement, achievement, studiengangkurs
                                    WHERE benutzer_achievement.AchievementID = achievement.AchievementID
                                    AND benutzer_achievement.KursID = studiengangkurs.KursID
                                    AND achievement.AchievementID = " . $achievement_id . "
                                    AND benutzer_achievement.BenutzerID = " . $user_id . "
                                ");

        return $query->result_array();
    }

    /**
     * Provides and get`s all necessary data for the achievement gallery for the authenticated (given) user_id.
     * @access public
     * @param integer $user_id ID of the user where the achievement data should be generated for
     * @return array Complex array With the different unlocked achievement levels, the different unlocked achievement objects per level
     *               and the courses, where the achievements have been unlocked for.
     */
    public function generate_user_achievement_data($user_id){
        // get the different (unlocked)achievement levels for the user
        $user_unlocked_achievement_levels = $this->_get_distinct_user_achievement_levels($user_id);

        // array for storing the data, that should be displayed in the view
        $view_achievement_data = array();

        /*
         * for each achievementlevel get the unlocked achievementtypes and the corresponding courses, for
         * which the achievemnt has been unlocked.
         */
         foreach($user_unlocked_achievement_levels as $level){
             // get the achievements per level
             $achievements_per_level = $this->_get_distinct_user_achievement_objects_for_level($user_id, $level['LevelNr']);
             // add them into to the levels array
             $level[] = $achievements_per_level;

             $view_achievement_data[] = $level; // save the actual level with the achievement information per level
         }

        // return the user unlocked achievements
        return $view_achievement_data;
    }

    /**
     * Calculates the count of saved topics for the given logbook and returns it.
     * @access public
     * @param integer $logbook_id The ID of the logbook where the topics count should be calculated for.
     * @return integer Count of the saved topics for the given logbook id.
     */
    public function get_saved_topic_count_for_logbook($logbook_id){
        $query = $this->db->query("
                                    SELECT COUNT(LogbucheintragID) as Anzahl
                                    FROM logbucheintrag
                                    WHERE LogbuchID = ". $logbook_id . "
                                  ");

        // return the number of saved topics for the specified logbook
        return $query->row()->Anzahl;
    }
}
/* End of file achievement_model.php */
/* Location: ./application/models/achievement_model.php */