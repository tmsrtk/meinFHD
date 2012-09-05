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

}