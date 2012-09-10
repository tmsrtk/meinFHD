<?php
/**
 * meinFHD WebApp
 *
 * @copyright Christian Kundruss, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Logbuch Model
 * The 'logbuch model' deals with all neccssary db operations for the students 'logbuch'
 * and 'logbuch' administration for course instructors.
 *
 */
class Logbuch_Model extends CI_Model {

    /**
     * Inserts the given array with base topics into the database. Expects an array with the topics to insert.
     * 1 Array entry is equal to 1 topic
     * @access public
     * @param $course_id
     * @param $topics array with topics to insert
     */
    public function save_all_base_topics($course_id, $topics) {
        // for each element in array insert it
        foreach($topics as $single_topic) {
            // prepare the data to be inserted
            if ($single_topic != ''){ // save no empty topic
                $data = array (
                    'Thema' => $single_topic,
                    'KursID' => $course_id
                );
                // insert it
                $this->db->insert('basislogbucheintrag', $data);
            }
        }
    }

    /**
     * Deletes all base topics for the given course_id in the table 'basislogbucheintrag'
     * @access public
     * @param $course_id
     */
    public function delete_all_base_topics($course_id) {
        $this->db->where('KursID', $course_id);
        $this->db->delete('basislogbucheintrag');
    }

    /**
     * Returns all base topics for the given course id in an array.
     * @access public
     * @param $course_id
     * @return array Array with all the base topics for the given course id. Every topic is one entry, if there is no base topic for the
     *               given course an empty array will be returned
     */
    public function get_all_base_topics($course_id) {
        $this->db->select('Thema');
        $this->db->from('basislogbucheintrag');
        $this->db->where('KursID',$course_id);

        $query = $this->db->get();

        $topics = array(); // init the return array

        if ($query->num_rows() > 0) { // is there any result?
            foreach ($query->result() as $row) { // every database row is an topic
                $topics[] = $row->Thema;
            }
        }

        return $topics;
    }

    /**
     * Returns all possible courses, for which the user (user_id) is able to create a logbook
     * @param $user_id ID of the current (asking) user
     * @return array Array with the possible courses, if there are no courses FALSE is returned
     */
    public function get_all_possible_courses($user_id){

        $query = $this->db->query("
            SELECT studiengangkurs.KursID, studiengangkurs.Kursname, studiengangkurs.kurs_kurz
            FROM benutzer, studiengangkurs
            WHERE benutzer.StudiengangID = studiengangkurs.StudiengangID
            AND (studiengangkurs.hatVorlesung = 1 OR studiengangkurs.hatSeminarUnterricht = 1)
            AND studiengangkurs.KursID NOT IN (
				                            SELECT logbuch.KursID
        		                            FROM logbuch
        			                        WHERE logbuch.BenutzerID = benutzer.BenutzerID)
            AND benutzer.BenutzerID = ".$user_id."
            ORDER BY studiengangkurs.Semester, studiengangkurs.Kursname");

        $courses = array();

        // if there are some results
        if ($query->num_rows() > 0){
            // prepare the data to return
            foreach ($query->result() as $row) {
                $courses[$row->KursID]['KursID'] = $row->KursID;
                $courses[$row->KursID]['Kursname'] = $row->Kursname;
                $courses[$row->KursID]['kurs_kurz'] = $row->kurs_kurz;
            }
        }

        return $courses;
    }

    /**
     * Inserts/saves a new logbook for the given course_id and the given user_id
     * @access public
     * @param $course_id ID of the specified course
     * @param $user_id ID of the specified user
     * @return integer the id of the created logbook
     */
    public function insert_new_logbook($course_id, $user_id) {

        $data_to_add = array (
            'BenutzerID' => $user_id,
            'KursID' => $course_id
        );

        $this->db->insert('logbuch',$data_to_add);

        // return the inserted id
        return mysql_insert_id();
    }

    /**
     * Returns all logbooks for a given user id to display them in the view.
     * @param $user_id user_id of the logbooks owner to search for
     */
    public function get_all_logbooks($user_id){

        // select all logbooks for the given user_id
        $query = $this->db->query("
            SELECT logbuch.LogbuchID, logbuch.KursID, studiengangkurs.kurs_kurz, studiengangkurs.Kursname
            FROM logbuch
            JOIN studiengangkurs ON logbuch.KursID = studiengangkurs.KursID
            WHERE logbuch.BenutzerID = ".$user_id
        );

        $logbooks = array();

        // if there are results construct the array
        if ($query->num_rows() > 0) {
            // prepare the data to return
            foreach ($query->result() as $row) { // foreach result row
                $logbooks[$row->LogbuchID]['LogbuchID'] = $row->LogbuchID;
                $logbooks[$row->LogbuchID]['kurs_kurz'] = $row->kurs_kurz;
                $logbooks[$row->LogbuchID]['Kursname'] = $row->kurs_kurz;
            }
        }

        return $logbooks;
    }

    /**
     * Deletes an logbook by his id.
     * @param $logbook_id ID of the logbook that should be deleted
     */
    public function delete_logbook($logbook_id){
        // delete the logbook with the given id
        $this->db->where('LogbuchID',$logbook_id);
        $this->db->delete('logbuch');
    }
}