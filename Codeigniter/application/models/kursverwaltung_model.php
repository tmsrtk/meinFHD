<?php
class Kursverwaltung_model extends CI_Model {
    
    /**
     * Returns course/tut-data.
     * @param int $id
     * @param int $eventtype
     * @return type
     */
    public function get_lecture_details($course_id, $eventtype){
	$this->db->select('SPKursID, Raum, StartID, EndeID, TagID');
	$this->db->where('KursID', $course_id);
	$this->db->where('VeranstaltungsformID', $eventtype);
	$q = $this->db->get('stundenplankurs');

	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
	    }
	}
	
	return $data[0];
    }
    
    
    /**
     * Returns name for given course_id
     * @param int $course_id
     * @return String 
     */
    public function get_lecture_name($course_id){
	$this->db->select('kurs_kurz')->where('KursID', $course_id);
	$q = $this->db->get_where('studiengangkurs');
	
	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
	    }
	}
	
	return $data[0];
    }
    
    
    
    /**
     * Returns course/tut-data.
     * @param int $id
     * @param int $eventtype
     * @return type
     */
    public function get_lab_details($course_id, $eventtype){
	$this->db->select('SPKursID, Raum, StartID, EndeID, TagID, VeranstaltungsformAlternative, TeilnehmerMax');
	$this->db->from('stundenplankurs as a');
	$this->db->join('gruppe as b', 'a.GruppeID = b.GruppeID');
	$this->db->where('KursID', $course_id);
	$this->db->where('VeranstaltungsformID', $eventtype);
	$q = $this->db->get();

	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
	    }
	}
	// return all alternatives
	return $data;
    }
    
    
    /**
     * Returns all eventtypes for a course
     * @param int $course_id 
     * @return array holding all eventtypes a course has
     */
    public function get_eventtypes_for_course($course_id){
	$this->db->distinct();
	$this->db->select('VeranstaltungsformID');
	$this->db->order_by('VeranstaltungsformID', 'asc');
	$q = $this->db->get_where('stundenplankurs', array('KursID'=>$course_id));
	
	foreach ($q->result_array() as $row) { 
	    $data[] = $row;
	}
	
	$data = $this->clean_nested_array($data);
	
	return $data;
    }
    
    
    /**
    * Runs through nested array and returns simple indexed array with values
    * @param type $array
    * @return type
    */
    private function clean_nested_array($array){
	$clean = array();
	foreach ($array as $a) {
	    foreach ($a as $key => $value) {
		$clean[] = $value;
	    }
	}
	return $clean;
    }
    
    
    public function add_person_to_course($profs = '', $labings = '', $tuts = ''){
	if($profs){
	    
	}
	if($labings){
	    
	}
	if($tuts){
	    
	}
    }
    
    
    
    
}
?>
