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
 * The 'logbuch model' deals with all necessary db operations for the students 'logbuch'
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

        // query for the inserted id and return it
        $inserted_id = mysql_insert_id();
        // return the inserted id
        return $inserted_id;
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
            WHERE logbuch.BenutzerID = ".$user_id . "
            ORDER BY logbuch.LogbuchID"
        );

        $logbooks = array();

        // if there are results construct the array
        if ($query->num_rows() > 0) {
            // prepare the data to return
            foreach ($query->result() as $row) { // foreach result row
                $logbooks[$row->LogbuchID]['LogbuchID'] = $row->LogbuchID;
                $logbooks[$row->LogbuchID]['kurs_kurz'] = $row->kurs_kurz;
                $logbooks[$row->LogbuchID]['Kursname'] = $row->kurs_kurz;
                $logbooks[$row->LogbuchID]['Bewertung'] = $this->_get_avg_rating_for_logbook($row->LogbuchID);
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

    /**
     * Fetches all entries for a given logbook and returns them in an array.
     * @access public
     * @param $logbook_id ID of the specified logbook
     * @return array Array with the single entries. If there are no entries an empty array will be returned
     */
    public function get_all_entries_for_logbook($logbook_id){
        $this->db->select('LogbucheintragID, Thema, Erlaeuterung, Bewertung');
        $this->db->from('logbucheintrag');
        $this->db->where('LogbuchID', $logbook_id);

        $query = $this->db->get(); // query the db

        $entries = array();

        if ($query->num_rows() > 0){ // if there are any results prepare the data to return
            foreach ($query->result() as $row){
                $entries[$row->LogbucheintragID]['LogbucheintragID'] = $row->LogbucheintragID;
                $entries[$row->LogbucheintragID]['Thema'] = $row->Thema;
                $entries[$row->LogbucheintragID]['Erlaeuterung'] = $row->Erlaeuterung;
                $entries[$row->LogbucheintragID]['Bewertung'] = $row->Bewertung;
            }
        }
        return $entries;
    }

    /**
     * Deletes an logbook entry by his id. For verification the logbook id will also be passed and used
     * while deleting.
     * @access public
     * @param $lb_entry_id ID of the logbook entry to delete
     */
    public function delete_logbook_entry($lb_entry_id){
        $this->db->where('LogbucheintragID', $lb_entry_id);
        $this->db->delete('logbucheintrag');
    }

    /**
     * Fetches an single logbook entry from the database and returns it.
     * @access public
     * @param $lb_entry_id ID of the entry that should be selected.
     * @return array Array with the singe logbook entry
     */
    public function get_single_logbook_entry($lb_entry_id) {
        $this->db->select('LogbucheintragID, Thema, Erlaeuterung, Bewertung, LogbuchID');
        $this->db->from('logbucheintrag');
        $this->db->where('LogbucheintragID', $lb_entry_id);

        $query = $this->db->get(); // query the database

        $data = array();

        // if there are results prepare the data to return
        if ($query->num_rows() == 1) {
            $data['LogbucheintragID'] = $query->row()->LogbucheintragID;
            $data['Thema'] = $query->row()->Thema;
            $data['Erlaeuterung'] = $query->row()->Erlaeuterung;
            $data['Bewertung'] = $query->row()->Bewertung;
            $data['LogbuchID'] = $query->row()->LogbuchID;
        }

        return $data;
    }

    /**
     * Saves an new entry for the given (existing) logbook.
     * @access public
     * @param logbook_id Integer value with the ID of the logbook where the entry should be inserted.
     * @param $topic String with the name of the new entry.
     * @param $annotation String with the annotation.
     * @param $rating Integer value with the rating between 0 and 100
     */
    public function save_new_logbook_entry($logbook_id, $topic, $annotation, $rating) {
        // prepare the data, that should be inserted
        $data_to_insert = array(
                            'Thema' => $topic,
                            'Erlaeuterung' => $annotation,
                            'Bewertung' => $rating,
                            'LogbuchID' => $logbook_id
        );

        $this->db->insert('logbucheintrag', $data_to_insert);
    }

    /**
     * Updates the selected logbook entry with the given informations.
     * @access public
     * @param $lb_entry_id ID of the selected logbook entry
     * @param $topic new topic for the entry
     * @param $annotation new annotation for the entry
     * @param $rating new rating for the entry
     */
    public function update_logbook_entry($lb_entry_id, $topic, $annotation, $rating) {
        // prepare the data array
        $data = array(
            'Thema' => $topic,
            'Erlaeuterung' => $annotation,
            'Bewertung' => $rating
        );

        $this->db->where('LogbucheintragID', $lb_entry_id);
        $this->db->update('logbucheintrag', $data);
    }

    /**
     * Returns the course abbreviation, that corresponds to the given logbook id.
     * @param $logbook_id
     * @return string String with the coursename abbreviation for the given logbook_id
     */
    public function get_course_name_for_logbook($logbook_id) {
        $this->db->select('studiengangkurs.Kursname');
        $this->db->from('logbuch');
        $this->db->join('studiengangkurs', 'studiengangkurs.KursID = logbuch.KursID');
        $this->db->where('logbuch.LogbuchID', $logbook_id);

        // query the db and save the coursename in an string
        $course_name= $this->db->get()->row()->Kursname;

        return $course_name;
    }

    /**
     * Calculates / returns the average rating for the given logbook.
     * @access private
     * @param $logbook_id ID of the given logbook.
     * @return INTEGER average rating of the given logbook.
     */
    private function _get_avg_rating_for_logbook($logbook_id) {
        $this->db->select_avg('Bewertung');
        $this->db->from('logbucheintrag');
        $this->db->where('LogbuchID', $logbook_id);

        $avg_rating = $this->db->get()->row()->Bewertung;;

        return $avg_rating;
    }

    /**
     * Fetches all base topics for the given course id and returns them in an array.
     * @access public
     * @param $course_id ID of the course, where the topics should be selected for
     * @return ARRAY Array with all base topics, if there are no base topics an empty array will be returned
     */
    public function get_all_base_topics_for_course($course_id){
        $this->db->select('Thema, Erlaeuterung');
        $this->db->from('basislogbucheintrag');
        $this->db->where('KursID', $course_id);

        $query = $this->db->get();

        $base_topics = array();

        if ($query->num_rows > 0) { // there are base topics for the given course id
            // generate query result and prepare the data
            foreach($query->result() as $row) {
                $entry = array(
                    'Thema' => $row->Thema,
                    'Erlaeuterung' => $row->Erlaeuterung
                );
                $base_topics[] = $entry;
            }
        }
        return $base_topics;
    }

    /**
     * Returns the newest course_id, that has got the same name as the given "old_course_id" (input course_id) and has got the
     * highest degree program.
     * @access public
     * @param $old_course_id ID of the "old course"
     */
    public function get_newest_course_id($old_course_id){

        // query for the coursename
        $this->db->select('Kursname');
        $this->db->from('studiengangkurs');
        $this->db->where('KursID', $old_course_id);

        $course_name = $this->db->get()->row()->Kursname;

        // query for the newes corse
        $query = $this->db->query("
                        SELECT KursID, studiengangkurs.Kursname, studiengangkurs.StudiengangID
                        FROM studiengangkurs
                        WHERE studiengangkurs.Kursname IN (
                                                           SELECT studiengangkurs.Kursname
                                                           FROM studiengangkurs
                                                           WHERE studiengangkurs.Kursname = '".$course_name."')
                       AND studiengangkurs.StudiengangID = (SELECT max(studiengangkurs.StudiengangID)
                                                             FROM studiengangkurs
                                                             WHERE studiengangkurs.Kursname='".$course_name."')
                        LIMIT 1");

        // generate the result and return it
        $newest_course_id = $query->row()->KursID;

        return $newest_course_id;
    }

    /**
     * Inserts / copies the given base topics to the specified logbook.
     * @access public
     * @param $logbook_id ID of the logbook, where the topics should be inserted
     * @param $base_topics Array with alle Base topics -> Structure per entry Thema => VALUE, Erlaeuterung => Value
     */
    public function insert_base_topics_into_logbook($logbook_id, $base_topics){
         // prepare the data for being inserted
         foreach($base_topics as $single_topic){
            $data_to_insert = array(
                'Thema' => $single_topic['Thema'],
                'Erlaeuterung' => $single_topic['Erlaeuterung'],
                'LogbuchID' => $logbook_id
            );

             // insert it
            $this->db->insert('logbucheintrag', $data_to_insert);
        }
    }

    /**
     * Checks if there is already an logbook for the given combination of course and user_id
     * @access public
     * @param $course_id ID of the selected course
     * @param $user_id ID of the accessing user
     * @return BOOL TRUE if there is an logbook for the given combination, otherwise FALSE
     */
    public  function check_logbook_course_existence_for_user($course_id, $user_id) {
        $this->db->select('*');
        $this->db->from('logbuch');
        $this->db->where('KursID', $course_id);
        $this->db->where('BenutzerID', $user_id);

        $query = $this->db->get();

        if ($query->num_rows() > 0 ){
            return TRUE;
        }

        return FALSE;
    }
}