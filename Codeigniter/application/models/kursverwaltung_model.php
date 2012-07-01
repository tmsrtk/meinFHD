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
     * Returns course/tut-data.
     * @param int $id
     * @param int $eventtype
     * @return type
     */
    public function get_lab_details($course_id, $eventtype){
	$this->db->select('SPKursID, Raum, StartID, EndeID, TagID, TeilnehmerMax');
	$this->db->from('stundenplankurs as a');
	$this->db->join('gruppe as b', 'a.GruppeID = b.GruppeID');
	$this->db->where('KursID', $course_id);
	$this->db->where('VeranstaltungsformID', $eventtype);
	$q = $this->db->get();

	if($q->num_rows() > 0){
	    foreach ($q->result() as $row){
		$data[] = $row;
		// if theres only one row >> data from lecture or tut
		if($q->num_rows == 1){
		    return $data[0];
		}
	    }
	}
	// else data is for lab >> alternatives
	return $data;
    }
    
    
    
}
?>
