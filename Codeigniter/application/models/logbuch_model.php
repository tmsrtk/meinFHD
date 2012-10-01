<?php
/**
 * meinFHD WebApp
 *
 * @copyright Christian Kundruß, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Logbuch Model
 * The 'logbuch model' deals with all necessary db operations for the students 'logbuch'
 * and 'logbuch' administration for course instructors.
 *
 * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
 * @todo remove debug / development time class variable and class variables usage.
 */

class Logbuch_Model extends CI_Model {

    private $actual_day_date = "2012-10-01"; // debug (development) date to be able to test the attendance widget
    private $actual_time = "16:15:00";

    /**
     * Inserts the given array with base topics into the database. Expects an array with the topics to insert.
     * One Array entry is equal to one topic.
     * @access public
     * @param integer $course_id ID of the course where the base topics should be saved for
     * @param integer $topics Array with the topics that should be inserted
     * @return void
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
     * @param integer $course_id ID of the course where the existing base topics should be deleted for.
     * @return void
     */
    public function delete_all_base_topics($course_id) {
        $this->db->where('KursID', $course_id);
        $this->db->delete('basislogbucheintrag');
    }

    /**
     * Returns all base topics for the given course id in an array.
     * @access public
     * @param integer $course_id
     * @return array Array with all the base topics for the given course id. Every topic is one entry, if there are no base topic for the
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
     * Returns all possible courses, for which the user (user_id) is able to create a logbook.
     * @param integer $user_id ID of the current (asking) user
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
            // prepare the data to be returned
            foreach ($query->result() as $row) {
                $courses[$row->KursID]['KursID'] = $row->KursID;
                $courses[$row->KursID]['Kursname'] = $row->Kursname;
                $courses[$row->KursID]['kurs_kurz'] = $row->kurs_kurz;
            }
        }

        return $courses;
    }

    /**
     * Inserts/saves a new logbook for the given course_id and the given user_id.
     * @access public
     * @param integer $course_id ID of the specified course
     * @param integer $user_id ID of the specified user
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
     * @param integer $user_id user_id of the logbooks owner to search for
     * @return array Array with all logbooks of the given user
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
                $logbooks[$row->LogbuchID]['Kursname'] = $row->Kursname;
                $logbooks[$row->LogbuchID]['Bewertung'] = $this->get_avg_rating_for_logbook($row->LogbuchID);
                $logbooks[$row->LogbuchID]['KursID'] = $row->KursID;
            }
        }

        return $logbooks;
    }

    /**
     * Deletes an logbook by his id.
     * @param integer $logbook_id ID of the logbook that should be deleted
     * @return void
     */
    public function delete_logbook($logbook_id){
        // delete the logbook with the given id
        $this->db->where('LogbuchID',$logbook_id);
        $this->db->delete('logbuch');
    }

    /**
     * Fetches all entries for a given logbook and returns them in an array.
     * @access public
     * @param integer $logbook_id ID of the specified logbook
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
     * Deletes an logbook entry by his id. For verification the logbook id will be passed and used
     * while deleting.
     * @access public
     * @param integer $lb_entry_id ID of the logbook entry to delete
     * @return void
     */
    public function delete_logbook_entry($lb_entry_id){
        $this->db->where('LogbucheintragID', $lb_entry_id);
        $this->db->delete('logbucheintrag');
    }

    /**
     * Fetches an single logbook entry from the database and returns it.
     * Used for the logbook entry detail view.
     * @access public
     * @param integer $lb_entry_id ID of the entry that should be selected.
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
     * @param integer $logbook_id Integer value with the ID of the logbook where the entry should be inserted.
     * @param String $topic String with the name of the new entry.
     * @param String $annotation String with the annotation.
     * @param integer $rating Integer value with the rating between 0 and 100
     * @return void
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
     * Updates the selected logbook entry with the given information.
     * @access public
     * @param integer $lb_entry_id ID of the selected logbook entry
     * @param String $topic new topic for the entry
     * @param String $annotation new annotation for the entry
     * @param integer $rating new rating for the entry
     * @return void
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
     * Returns the course name, that corresponds to the given logbook id.
     * @param integer $logbook_id ID of the logbook, where the corresponding course should be looked up.
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
     * Returns the course id, that corresponds to the given logbook id.
     * @param integer $logbook_id ID of the logbook, where the corresponding course should be looked up for.
     * @return integer The id of the course, that corresponds to the logbook id
     */
    public function get_course_id_for_logbook($logbook_id){
        $this->db->select('KursID');
        $this->db->from('logbuch');
        $this->db->where('LogbuchID', $logbook_id);

        $course_id = $this->db->get()->row()->KursID;

        return $course_id;
    }

    /**
     * Calculates / returns the average rating for the given logbook.
     * @access public
     * @param integer $logbook_id ID of the logbook where the rating should be looked up for.
     * @return integer average rating of the given logbook.
     */
    public function get_avg_rating_for_logbook($logbook_id) {
        $this->db->select_avg('Bewertung');
        $this->db->from('logbucheintrag');
        $this->db->where('LogbuchID', $logbook_id);

        $avg_rating = $this->db->get()->row()->Bewertung;

        return $avg_rating;
    }

    /**
     * Fetches all base topics for the given course id and returns them in an array.
     * @access public
     * @param integer $course_id ID of the course, where the topics should be selected for
     * @return array Array with all base topics, if there are no base topics an empty array will be returned
     */
    public function get_all_base_topics_for_course($course_id){
        $this->db->select('Thema');
        $this->db->from('basislogbucheintrag');
        $this->db->where('KursID', $course_id);

        $query = $this->db->get();

        $base_topics = array();

        if ($query->num_rows > 0) { // there are base topics for the given course id
            // generate query result and prepare the data
            foreach($query->result() as $row) {
                $entry = array(
                    'Thema' => $row->Thema,
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
     * @param integer $old_course_id ID of the "old course"
     * @return integer ID of the newest degree program course that has got the same name as the given course id.
     */
    public function get_newest_course_id($old_course_id){

        // query for the coursename
        $this->db->select('Kursname');
        $this->db->from('studiengangkurs');
        $this->db->where('KursID', $old_course_id);

        $course_name = $this->db->get()->row()->Kursname;

        // query for the newest course
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
     * @param integer $logbook_id ID of the logbook, where the topics should be inserted
     * @param array $base_topics Array with all base topics -> structure per entry 'Thema => VALUE'
     * @return void
     */
    public function insert_base_topics_into_logbook($logbook_id, $base_topics){
         // prepare the data for being inserted
         foreach($base_topics as $single_topic){
            $data_to_insert = array(
                'Thema' => $single_topic['Thema'],
                'LogbuchID' => $logbook_id
            );

             // insert it
            $this->db->insert('logbucheintrag', $data_to_insert);
        }
    }

    /**
     * Checks if there is already an logbook for the given combination of course and user_id
     * @access public
     * @param integer $course_id ID of the user selected course
     * @param integer $user_id ID of the accessing user
     * @return bool TRUE if there is an logbook for the given combination, otherwise FALSE
     */
    public function check_logbook_course_existence_for_user($course_id, $user_id) {
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

    /**
     * Returns the currently running course for the authenticated user.
     * @access public
     * @return array The array with the currently running course
     * @todo remove development time, needed to be able to test some functions, because of missing time table data
     */
    public function get_running_course(){

        // get the actual week day
        $actual_week_day = date("w",time());
        // get the actual time
        $actual_time = date("H:i", time());

        // for development purposes static times to be able to test the functionality
        $actual_time = "16:15";
        $actual_week_day = "1";
        // end development time data

        // get all user information
        $user_information = $this->_get_user_information();

        // get the actual semester
        $num_act_semester = $this->get_Semester($user_information['StudienbeginnSemestertyp'], $user_information['StudienbeginnJahr']);

        $running_course = $this->_fetch_running_course($num_act_semester, $user_information['BenutzerID'], $actual_week_day, $actual_time);

        return $running_course;
    }

    /**
     * Fetches the actual running course and returns the specified information in an array.
     * Only courses with the 'veranstalungsform 1 = Vorlesung' will be queried.
     * @access private
     * @param integer $act_semester Actual semester number of the authenticated user
     * @param integer $user_id ID of the currently authenticated user
     * @param integer $act_wekday Act. weekday as an integer (1 = monday, 2 = tuesday, ...)
     * @param String $act_time Act time in the format HH:mm
     * @return Array Array with the specified information about the running course in an key / value array.
     */
    private function _fetch_running_course($act_semester, $user_id, $act_weekday, $act_time){
        $query = $this->db->query("
                	SELECT
                        b.SemesterID, sg.Kursname, sg.kurs_kurz,sp.Raum,
                        v.VeranstaltungsformName, sp.StartID, sp.EndeID, (sp.EndeID-sp.StartID)+1 AS 'Dauer',
                        t.TagName,t.TagID,
                        s_beginn.Beginn, s_ende.Ende,
                        b.Aktiv,
                        b.KursID,b.SPKursID
                    FROM
                        benutzerkurs b,
                        studiengangkurs sg,
                        stundenplankurs sp,
                        veranstaltungsform v,
                        tag t,
                        stunde s_beginn, stunde s_ende,
                        benutzer d,
                        gruppe g
                    WHERE
                        b.kursID = sg.kursID AND
                        sp.kursID = b.KursID AND
                        sp.SPKursID = b.SPKursID AND
                        v.veranstaltungsformID = sp.veranstaltungsformID AND
                        s_beginn.StundeID = sp.StartID AND
                        s_ende.StundeID = sp.EndeID AND
                        t.TagID = sp.TagID AND
                        sp.DozentID = d.BenutzerID AND
                        b.BenutzerID = ". $user_id . " AND
                        b.SemesterID = " . $act_semester . " AND
                        sp.GruppeID = g.GruppeID AND
                        sp.IsWPF = 0 AND
                        b.Aktiv = 1 AND
                        v.VeranstaltungsformID = 1 AND
                        t.TagID = " .$act_weekday ." AND
                        s_beginn.Beginn <= '". $act_time ."' AND
                        s_ende.Ende >= '".$act_time."'
                    ORDER BY
                        sp.tagID, sp.StartID;
        ");

        return $query->row_array();
    }

    /**
     * Gets all information about the actual authenticated user and returns them in an array.
     * @access private
     * @return Array The array with the user information.
     */
    private function _get_user_information(){
        $this->db->select('*');
        $this->db->from('benutzer');
        $this->db->where('BenutzerID', $this->authentication->user_id());
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * Saves a new attendance record for the given user_id in the database table 'anwesenheit'
     * with the current time and the given course id.
     * @access public
     * @param integer $course_id ID of the course, where the attendance should be saved for.
     * @param integer $user_id ID of the specified user
     * @return void
     * @todo remove development time usage (class varaiable)
     */
    public function save_attendance_for_course_with_current_time($course_id, $user_id) {
        // get the actual week day
        $actual_week_day = date("w",time());
        // get the actual time
        $actual_time = date("H:i", time());

        // for development purposes static times to be able to test the functionality
        $actual_time = "16:15:00";
        $actual_week_day = "1";
        $timestamp_to_insert = $this->actual_day_date . ' ' . $actual_time;
        // end development time data

        // prepare the data, that should be inserted
        $data = array(
            'BenutzerID' => $user_id,
            'KursID' => $course_id,
            'Datum' => $timestamp_to_insert
        );
        // insert the data into the table 'anwesenheit'
        $this->db->insert('anwesenheit', $data);
    }

    /**
     * Returns the count of attended course events for the actual semester and the given user. Checks at first the actual semestertype
     * and then sets the date limits.
     * @param integer $course_id ID of the course where the attended events should be selected.
     * @param integer $user_id ID of the user, for who the attendance should be selected.
     * @return integer The count of the attended events for the given user and the given course id is returned. If there are no entries, zero
     *              will be returned.
     */
    public function get_attendance_count_for_course_and_act_semester($course_id, $user_id) {
        // get the semester type
        $semester_type = $this->adminhelper->getSemesterTyp();
        // set the date limits according to the type of the semester
        $begin_date = '';
        $end_date = '';
        $act_year = date('Y', time()); // get the act year

        switch($semester_type){
            case 'SS':
                $begin_date = $act_year . '-03-01 00:00:00';
                $end_date = $act_year . '-07-31 00:00:00';
                break;
            case 'WS':
                $begin_date = $act_year . '-09-01 00:00:00';
                $end_date = ($act_year+1) . '-02-28 00:00:00';
                break;
        }

        // define the date_range to select for
        $date_range = 'Datum BETWEEN ' . '"' . $begin_date . '" AND "' . $end_date . '"';
        // query for the count
        $this->db->from('anwesenheit');
        $this->db->where('BenutzerID', $user_id);
        $this->db->where('KursID', $course_id);
        $this->db->where($date_range, NULL, FALSE); // set the date range
        $attended_events = $this->db->count_all_results();

        return $attended_events;
    }

    /**
     * Checks if the current authenticated user has already tracked his attendance for the
     * given course id and the actual day.
     * @param integer $course_id The course id where the attendance should be checked for.
     * @return bool TRUE if the user has already tracked his attendance, otherwise FALSE
     */
    public function already_attending_today($course_id){
        // get the actual week day
        $actual_day_date = date("Y-m-d",time());

        // create the date range for the act date
        $beginn_date = $this->actual_day_date . " 00:00:00"; // for testing -> using the class variable
        $end_date = $this->actual_day_date . " 23:59:00"; // for testing -> using the class variable
        $date_range = 'Datum BETWEEN "'.$beginn_date.'" AND "'.$end_date.'"';

        // query the database and check if there is an record, that is like the actual date
        $this->db->from('anwesenheit');
        $this->db->where($date_range, NULL, FALSE);
        $this->db->where('KursID', $course_id);

        $query = $this->db->get();

        if($query->num_rows() > 0){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Queries the database for the right course_id that corresponds to the degree-program of the student.
     * The timetable only holds courses from the newest degree programs. Some courses in the newer degree-
     * programs are the same than the old ones. Because of the data integrity, we are going to save everything
     * to the course id, that corresponds to the degree programm of the student. Method is only afforded for students
     * from older degree programs.
     * @access public
     * @param integer $course_id The course_id from the timetable
     * @param integer $user_id The id of the currently authenticated user
     * @return Array Array with the selected course_id and the coursename will be returned
     */
    public function query_right_stdg_course_for_given_course($course_id, $user_id){
        $query = $this->db->query("
                                   SELECT studiengangkurs.KursID, studiengangkurs.Kursname
                                   FROM benutzer, studiengangkurs
                                   WHERE benutzer.StudiengangID = studiengangkurs.StudiengangID
                                   AND benutzer.BenutzerID = ". $user_id ."
                                   AND studiengangkurs.kurs_kurz = ( SELECT stdg.kurs_kurz
				                                                      FROM studiengangkurs as stdg
                                                                      WHERE stdg.KursID = ".$course_id."
                                                                    )
                                   LIMIT 1");
        // create the query result and return it
        return $query->row_array();
    }

    /**
     *	get_semester()
     *
     *  wirtten by Jochen Sauer, copyed function, function in adminhelper calculates wrong (2 semester to much)
     *
     *	Gibt anhand der übergebenen persönlichen Daten das aktuelle
     *	Semester des Studenten zurück.
     *
     *	@param	String $semestertyp	 (WS or SS)
     *
     *	@param	integer $studienbeginn	Four-digit-number, year the study started
     *
     *	@return	String $semester 		Actual Semester as String.
     */
    private function get_semester( $semestertyp, $studienbeginn )
    {

        // definiere Rückgabewert
        $semester = "";

        // ermittel semestertyp
        $errechneter_semestertyp = $this->adminhelper->getSemesterTyp();

        // stimmt aktueller Semestertyp mit Studienbeginn-Semestertyp überein?
        $gleicher_semestertyp = ($errechneter_semestertyp == $semestertyp) ? true : false;

        // Errechne aktuelles Semester
        $semester = (($gleicher_semestertyp) ? 1 : 0) + 2 * ((($gleicher_semestertyp && date("n") < 3) ? date("Y")-1 : date("Y")) - $studienbeginn);

        // Gebe String zurück
        return $semester;
    }

    /**
     * Returns the logbook id for the given combination of course and user id
     * @access public
     * @param integer $course_id Course ID where the logbook should be selected for
     * @param integer $user_id Owner of the logbook to select
     * @return integer The id of the logbook
     */
    public function get_logbook_id($course_id, $user_id){
        $this->db->select('LogbuchID');
        $this->db->from('logbuch');
        $this->db->where('KursID', $course_id);
        $this->db->where('BenutzerID', $user_id);

        $query = $this->db->get();

        return $query->row()->LogbuchID;
    }

    /**
     * Returns the attendance count for the given course_id in an specified date range.
     * @access public
     * @param integer $course_id The course, where the attendance should be counted for
     * @param String $begin_date Start date of the range as an string (format YYYY-MM-DD)
     * @param String $end_date End date of the range as an string (format YYYY-MM-DD)
     * @return integer The count of the attended events for the given course in the given range
     */
    public function get_attendance_count_for_date_range($course_id, $begin_date, $end_date){
        // construct the date range
        $date_range = 'Datum BETWEEN ' . '"' . $begin_date . ' 00:00:00" AND "' . $end_date . ' 00:00:00"';

        // query attendance count for date range
        $this->db->from('anwesenheit');
        $this->db->where('BenutzerID', $this->authentication->user_id());
        $this->db->where('KursID', $course_id);
        $this->db->where($date_range, NULL, FALSE); // set the date range
        $attended_events = $this->db->count_all_results();

        return $attended_events;
    }

    /**
     * Returns the count of all attended events in the given date range for the authenticated user.
     * @access public
     * @param String $begin_date Start date of the range as an string (format YYYY-MM-DD)
     * @param String $end_date End date of the range as an string (format YYYY-MM-DD)
     * @return integer The count of attendance over all courses for the given date range
     */
    public function get_attendance_count_for_all_courses_in_range($begin_date, $end_date){
        // construct the date range
        $date_range = 'Datum BETWEEN ' . '"' . $begin_date . ' 00:00:00" AND "' . $end_date . ' 00:00:00"';

        // query the attendance count for all courses in the given date range for the actual authenticated user
        $this->db->from('anwesenheit');
        $this->db->where('BenutzerID', $this->authentication->user_id());
        $this->db->where($date_range, NULL, FALSE);
        $overall_attended_events = $this->db->count_all_results();

        return $overall_attended_events;
    }

    /**
     * Fetches information form the 'studiengangkurs'-table for the given course id and returns them.
     * @access public
     * @param integer $course_id ID of the course where the information should be selected for
     * @return Array Array with the course information
     */
    public function get_course_information($course_id){
        $this->db->select('Kursname, kurs_kurz');
        $this->db->from('studiengangkurs');
        $this->db->where('KursID', $course_id);
        // query the db and return the selected row
        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * Checks for the given course and the current time, if there is already an event stored.
     * @param integer $course_id ID of the course, where it should be checked for, if an event for the act
     *                  day is already saved
     * @return bool TRUE if an event has already been stored, otherwise FALSE is returned
     */
    public function is_course_event_stored($course_id){

        // get the actual day date
        $actual_day_date = date("Y-m-d",time());

        // create the date range for the act date
        $beginn_date = $this->actual_day_date . " 00:00:00"; // for testing -> using the class variable
        $end_date = $this->actual_day_date . " 23:59:00"; // for testing -> using the class variable
        $date_range = 'Datum BETWEEN "'.$beginn_date.'" AND "'.$end_date.'"';

        $this->db->select('*');
        $this->db->from('studiengangkurs_veranstaltung');
        $this->db->where($date_range, NULL, FALSE);
        $this->db->where('KursID', $course_id);

        $query = $this->db->get();

        // is there any result, so return true, otherwise return false
        if($query->num_rows() != 0){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Add`s an new entry to the studiengangkurs_veranstaltung table, which indicates that an event
     * has been happened for the actual day date.
     * @access public
     * @param integer $course_id ID of the course where the event timestamp should be saved for
     * @return void
     */
    public function add_course_event($course_id){

        // get the actual day date
        $actual_day_date = date("Y-m-d",time());
        // get the actual time
        $actual_time = date("H:i", time());

        // construct the timestamp, that should be inserted
        $timestamp_to_insert = $this->actual_day_date . ' ' . $this->actual_time;

        // prepare the data, that should be inserted
        $data = array(
            'KursID' => $course_id,
            'Datum' => $timestamp_to_insert
        );

        // insert the data into the table 'studiengangkurs_veranstaltung'
        $this->db->insert('studiengangkurs_veranstaltung', $data);
    }

    /**
     * Returns the number of course events for the actual semester, that have been occurred
     * till today.
     * @access public
     * @param integer $course_id ID of the course where the number of events should be fetched for.
     * @return integer Number of occured events for the actual day
     */
    public function get_number_of_course_events_till_today($course_id){

        // get the semester type
        $semester_type = $this->adminhelper->getSemesterTyp();
        // set the date limits according to the type of the semester
        $beginn_date = '';
        $act_year = date('Y', time()); // get the act year

        // depending of the semester type switch the begin date of the range
        switch($semester_type){
            case 'SS':
                $beginn_date = $act_year . '-03-01 00:00:00';
                break;
            case 'WS':
                $beginn_date = $act_year . '-09-01 00:00:00';
                break;
        }

        // save the actual day date
        $actual_day_date = date("Y-m-d",time());

        // define the date_range to select for
        $date_range = 'Datum BETWEEN ' . '"' . $beginn_date . '" AND "' . $this->actual_day_date . ' 23:59:00"';
        // query for the count
        $this->db->from('studiengangkurs_veranstaltung');
        $this->db->where('KursID', $course_id);
        $this->db->where($date_range, NULL, FALSE); // set the date range
        $occured_events = $this->db->count_all_results();

        return $occured_events;
    }
}