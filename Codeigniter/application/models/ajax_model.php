<?php

/*
 * meinFHD 2.0
 * http://www..de/
 *
 * Konstantin Voth
 * Copyright 2012
 *
 */

/* 
 * Ajax Model
 * Kümmert sich um die Anfragen des Ajax Controllers und lädt/speichert die Daten
 */

class Ajax_model extends CI_Model {
    
        private $semesterplanID;

	public function __construct()
	{
		parent::__construct();
                $this->setSemesterplanID();
	}

	// public function get_request()
	// {
	// 	$this->db->select('semesterplanposition');
	// 	$this->db->from('modul');

	// 	$q = $this->db->get();

	// 	return $q->result_array();
	// }

	// hole die gespeicherte Reihenfolge der Module
	public function get_reihenfolge($semesternr)
	{
		// schon richtig sortiert zurückgeben
		$this->db->where("im_semester", $semesternr);
		$this->db->order_by("semesterplanposition", "asc");
		$q = $this->db->get('modul');
		return $q->result_array();
	}

	// speichere die übergebene Reihenfolge für ein Semester
	public function set_reihenfolge($neue_reihenfolge, $semesternr)
	{
		// counter für die Reihenfolge
		$counter = 1;
		// speichere neue Reihenfolge in die DB
		foreach ($neue_reihenfolge as $serialized_position) {
			$data = array(
               //'Semesterposition' => $counter,
               'Semester' => $semesternr
            );

            // FB::log($serialized_position);

			$this->db->where('SemesterplanID', $this->semesterplanID);     // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			$this->db->where('KursID', $serialized_position);
			$this->db->update('Semesterkurs', $data);

			$counter++;
		}
	}
        
        
        public function setSemesterplanID()
        {
            $this->load->model('Studienplan_Model');
            $this->semesterplanID = $this->Studienplan_Model->getStudyplanID();
        }

}