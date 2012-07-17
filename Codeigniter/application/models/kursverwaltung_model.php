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
     * Returns first and last name of prof for given course
     * @param int $course_id
     * @return array
     */
    public function get_profname_for_course($course_id){
	$this->db->distinct();
	$this->db->select('Titel, Vorname, Nachname');
	$this->db->from('stundenplankurs as a');
	$this->db->join('benutzer as b', 'a.DozentID = b.BenutzerID');
	$this->db->where('KursID', $course_id);
	$this->db->where('VeranstaltungsformID', 1);
	
	$q = $this->db->get();
	
	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		foreach($row as $r){
		    $data[] = $r;
		}
	    }
	}
	
	return $data;
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
     * Returns array with all labings/tuts belonging to a single course-id
     * switch labings/tuts with passed table
     * @param int $course_id
     * @param String $table
     * @return array
     */
    public function get_current_labings_tuts_for_course($course_id, $table){
	$this->db->distinct();
	$this->db->select('a.Vorname, a.Nachname, a.BenutzerID');
	$this->db->from('benutzer as a');
	$this->db->join($table.' as b', 'a.BenutzerID = b.BenutzerID');
	$this->db->where('b.KursID', $course_id);
	$q = $this->db->get();
	
	$data = array(); // init
	
	foreach ($q->result_array() as $row){
	    $data[$course_id][] = $row;
	}
	
	return $data;
    }

    
    /**
     * Returns array with all possible labings
     * i.e. Role 2 and Role 3
     * @return array
     */
    public function get_all_possible_labings(){
	$this->db->distinct();
	$this->db->select('a.Vorname, a.Nachname, a.BenutzerID');
	$this->db->from('benutzer as a');
	$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID');
	$this->db->where('b.RolleID', 2)->or_where('b.RolleID', 3);
	$this->db->order_by('a.Nachname', 'ASC');
	$q = $this->db->get();
	
	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
	    }
	}
	
//	$data = $this->clean_nested_array($data);
	
	return $data;
    }
    
    
    /**
     * 
     * @return type
     */
    public function get_all_tuts(){
	$this->db->distinct();
	$this->db->select('a.Vorname, a.Nachname, a.BenutzerID');
	$this->db->from('benutzer as a');
	$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID');
	$this->db->where('b.RolleID', 4);
	$this->db->order_by('a.Nachname', 'ASC');
	$q = $this->db->get();
	
	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
	    }
	}
//	$data = $this->clean_nested_array($data);
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
    
    
    /*
     * ############################################################# SAVING DATA
     */
    
    /**
     * 
     * @param type $profs
     * @param type $labings
     * @param type $tuts
     */
    public function add_person_to_course($profs = '', $labings = '', $tuts = ''){
	if($profs){
	    // when adding profs as labings >> that relation has to be inserted into benutzer_mm_rolle (>> gets betreuer-role)
	    // when removing persons from course_mgt (OR !!!DELETING courses) the other way round - delete entry from benutzer_mm_rolle
	}
	if($labings){
	    
	}
	if($tuts){
	    
	}
    }
    
    /**
     * Saves course and group-data to db.
     * SPKursID passed separately.
     * Each data passed in separate arrays, too.
     * @param int $spkurs_id
     * @param array $spkurs_data
     * @param array $group_data
     */
    public function save_course_details($spkurs_id, $spkurs_data, $group_data){
	$this->db->where('SPKursID', $spkurs_id);
	$this->db->update('stundenplankurs', $spkurs_data);
	
	// if spkurs that should be updated is Ü,L.P
	if($group_data){
	    $group_id = $this->get_group_id_for_spkursid($spkurs_id)->GruppeID;
	    
	    $this->db->where('GruppeID', $group_id);
	    $this->db->update('gruppe', $group_data);
	}
    }
    
    
    /**
     * Helper to get group_id for given sp_course_id
     * @param int $spkurs_id
     * @return object
     */
    private function get_group_id_for_spkursid($spkurs_id){
	$this->db->select('GruppeID');
	$this->db->from('stundenplankurs');
	$this->db->where('SPKursID', $spkurs_id);
	$q = $this->db->get();
	
	$data = ''; // init
	
	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data = $row;
	    }
	}
	return $data;
    }
    
    
    
    
    
    /**
     * used to populate benutzer_mm_rolle-table in database
     */
//    public function update_benutzermmrolle(){
//	$this->db->select('BenutzerID');
//	$this->db->where('TypID', 6);
//	$q = $this->db->get('benutzer');
//	
//	foreach ($q->result_array() as $row) { 
//	    $data[] = $row;
//	}
//	
//	$data = $this->clean_nested_array($data);
//	
//	
//	foreach($data as $d){
//	    $save_data['BenutzerID'] = $d;
//	    $save_data['RolleID'] = 3;
//	    
//	    $this->db->insert('benutzer_mm_rolle', $save_data);
//	    
////	    $a[] = $save_data;
//	}
//	
//	echo '<pre>';
//	print_r($save_data);
//	echo '</pre>';
//	
//    }
    
    
    
    
}
?>
