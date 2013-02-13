<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Timetable_parsing_model
 *
 * The timetable parsing model provides all database operations, that are required during the timetable
 * parsing process.
 */
class Timetable_parsing_model extends CI_Model{

    /**
     * Writes / saves the parsed timetable course to the database (table 'studiengangkurs'). Therefore all
     * parameters are afforded.
     *
     * @param $course_id int ID of the course that should be added to the database.
     * @param $event_type_id int ID of the course event type.
     * @param $event_type string The alternative event type.
     * @param $wpf_name string Name of the wpf (if it is a wpf).
     * @param $room string The room, where the course takes place.
     * @param $dozent_id int ID of the dozent, that gives the course.
     * @param $start_id int The start hour of the course.
     * @param $end_id int The end hour of the course.
     * @param $is_wpf bool Indicates if the course is an wpf or not
     * @param $day_id int ID of the day, where the course occures.
     * @param $group_id int ID of the course group.
     * @param $color int Excel color code of the course.
     * @param $editor int ID of the editor, who parsed the course.
     * @access private
     * @return void
     */
    public function write_stdplan_data($course_id, $event_type_id, $event_type, $wpf_name, $room, $dozent_id, $start_id,
                                       $end_id, $is_wpf,$day_id, $group_id, $color, $editor){

        // create the array, that should be inserted into the database
        $stdplan_record = array(
            'KursID' => $course_id,
            'VeranstaltungsformID' => $event_type_id,
            'VeranstaltungsformAlternative' => $event_type,
            'WPFName' => $wpf_name,
            'Raum' => $room,
            'DozentID' => $dozent_id,
            'StartID' => $start_id,
            'EndeID' => $end_id,
            'isWPF' => $is_wpf,
            'TagID' => $day_id,
            'GruppeID' => $group_id,
            'Farbe' => $color,
            'EditorID' => $editor
        );

        // insert the course into the database
        $this->db->insert('stundenplankurs', $stdplan_record);
    }


    /**
     * Returns the number of found records for the given degree program PO-abbreviation -combination
     *
     * @param $dp_abr string Abbreviation of the degree program
     * @param $dp_po int PO version of the degree program
     * @access public
     * @return int number of found records or 0 if no records where found
     */
    public function count_degree_programs($dp_abr, $dp_po){

        $data = array();
        $data = $this->db->get_where('studiengang', array('Pruefungsordnung' => $dp_po, 'StudiengangAbkuerzung' => $dp_abr));

        if($data){
            // return number of found records
            return $data->num_rows;
        }
        else {
            return 0;
        }
    }

    // get all users with role dozent
    /**
     * Get all dozents / dozent ids with the given last name.
     * @param $name string The last name, where the dozent should be searched for.
     * @access public
     * @return array Associative array with the 'BenutzerID' and the 'Nachname' of the found dozent.
     *               If there is no dozent nothing will be returned
     */
    public function get_dozentid_for_name($name){

        $data = array();
        // construct the query
        $this->db->distinct();
        $this->db->select('a.BenutzerID, a.Nachname');
        $this->db->from('benutzer as a');
        $this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID and b.RolleID = 2');
        $this->db->like('a.Nachname', $name);

        // query the database for the searched dozent
        $q = $this->db->get();

        if($q->num_rows() == 1){ // if there is one dozent, who has got the searched name

            foreach ($q->result() as $row){
                $data = $row;
            }

            // return information about the dozent
            return $data;
        }
    }


    /**
     * Gets all students for a specific degree program
     *
     * @param $stdgng_id int ID of the degree program where the students should be selected from
     * @access public
     * @return array All found students will be returned in an associative array.
     */
    public function get_student_ids($stdgng_id){

        $data = array();
        // construct the query statement
        $this->db->distinct();
        $this->db->select('a.BenutzerID');
        $this->db->from('benutzer as a');
        $this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID and b.RolleID = 5');
        $this->db->where('StudiengangID', $stdgng_id);

        // query the database for all students, that correspond to the degree program
        $q = $this->db->get();

        // if there is 1 or more students (= there is a result)
        if($q->num_rows() >= 1){

            // construct an array with all students
            foreach ($q->result() as $row){
                $data[] = $row;
            }
            // return the array
            return $data;
        }
    }

    /**
     * Helper to get degree_program-id from po and abk
     *
     * @param int $po pruefungsordnung
     * @param int $stdgng_short abkuerzung
     * @access public
     * @return object
     */
    public function get_stdgng_id($po, $stdgng_short){
        $data = array();
        $this->db->select('StudiengangID');
        $this->db->where('Pruefungsordnung', $po);
        $this->db->where('StudiengangAbkuerzung', $stdgng_short);
        $q = $this->db->get('studiengang');

        if($q->num_rows() == 1){
            foreach ($q->result() as $row){
                $data = $row;
            }
            return $data;
        }
    }


    /**
     * Gets the course_id for the passed course name and degree program id.
     *
     * @param $course_name string The name of the course as an string.
     * @param $stdgng_id int ID of the degree program
     * @access public
     * @return array Associative array with the found course id. The index is ['KursID'].
     */
    public function get_course_id($course_name, $stdgng_id){

        $data = array();
        // construct the query statement
        $this->db->select('KursID');
        $this->db->where('kurs_kurz', $course_name);
        $this->db->where('StudiengangID', $stdgng_id);
        // query the database
        $q = $this->db->get('studiengangkurs');

        // if there is one course that belongs to the given course name and degree program id
        if($q->num_rows() == 1){

            // construct the return array
            foreach ($q->result() as $row){
                $data = $row;
            }
            // return the found course id in an associative array
            return $data;
        }
    }


    /**
     * Creates an empty record in the 'studiengangkurs'-table when the parsed course does not exist.
     *
     * @param $course_name string The name of the course
     * @param $short_name string The short name of the course
     * @param $stdg_semester int The semester, where the course takes part in
     * @param $stdgng_id int The id of the degree program, where the course belongs to.
     * @access public
     * @return void
     */
    public function create_new_course_in_stdgng($course_name, $short_name, $stdg_semester, $stdgng_id){

        // construct the array with the data that should be inserted
        $data = array(
            'Kursname' => $course_name,
            'kurs_kurz' => $short_name,
            'HisposID' => 0,
            'Creditpoints' => 0,
            'SWS_Vorlesung' => 0,
            'SWS_Uebung' => 0,
            'SWS_Praktikum' => 0,
            'SWS_Projekt' => 0,
            'SWS_Seminar' => 0,
            'SWS_SeminarUnterricht' => 0,
            'Semester' => $stdg_semester,
            'StudiengangID' => $stdgng_id,
            'Beschreibung' => '',
            'hatVorlesung' => 0,
            'hatUebung' => 0,
            'hatPraktikum' => 0,
            'hatProjekt' => 0,
            'hatSeminar' => 0,
            'hatSeminarUnterricht' => 0
        );

        // insert it into the database
        $this->db->insert('studiengangkurs', $data);
    }


    /**
     * Returns a new created / the highest course_id from the table 'studiengangkurs'.
     * @access public
     * @return array Associative array with the highest course id. Index is ['KursID']
     */
    public function get_max_course_id_from_studiengangkurs(){
        $data = array();

        // query the database for the highest course id
        $this->db->select_max('KursID');
        $q = $this->db->get('studiengangkurs');

        // if there is a result / a highest course id
        if($q->num_rows() == 1){
            // construct the data that should be returned
            foreach ($q->result() as $row){
                $data = $row;
            }
            // return it
            return $data;
        }
    }


    /**
     * Updates a course in the 'studiengangkurs'-table to the given semester.
     * The semester will be set to value that has been passed.
     * @param $course_id int ID of the course where the record should be updated for.
     * @param $sem int Number of the semester where the recourd / course should be updated to.
     * @access public
     * @return void
     */
    public function update_semester_of_course($course_id, $sem){

        // if there is an record for the given course id update it to the passed semester.
        $this->db->where('KursID', $course_id);
        $this->db->update('studiengangkurs', array('Semester' => $sem));
    }

}
/* End of file timetable_parsing_model.php */
/* Location: ./application/models/timetable_parsing_model.php */