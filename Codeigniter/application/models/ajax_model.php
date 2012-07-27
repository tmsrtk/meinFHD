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

    	public function __construct()
    	{
    		parent::__construct();
    	}

    	public function query_module_title($mid)
    	{
    		$this->db->select('Kursname')
    				 ->from('studiengangkurs')
    				 ->where('KursID', $mid)
    				 ;
    		$q = $this->db->get();

    		// if ($q > 0)
    		// {
    		// 	// TODO:
    		// }

    		return $q->row_array();
    	}

    	public function query_module_text($mid)
    	{
    		// TODO: same shit as query_module_title

    		$this->db->select('studiengangkurs.*');
    		$this->db->from('studiengangkurs');
    		// $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
    		$this->db->where('studiengangkurs.KursID', $mid);
    		// $this->db->order_by('studiengangkurs.KursID', 'ASC');
    		$studiengang = $this->db->get();

    		return $studiengang->row_array();
    	}

    public function query_status_pruefung($kurs_id)
    {
	    	$this->db->select('KursSchreiben')
	    			 ->from('semesterkurs')
	    			 ->where('SemesterplanID', $this->user_model->get_semesterplanid())
	    			 ->where('KursID', $kurs_id)
	    			 ;

	    	$q = $this->db->get()->row_array();

	    	echo $q['KursSchreiben'];
    }
    public function write_activate_status_pruefung($kurs_id)
    {
	    	$this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
	    	$this->db->where('KursID', $kurs_id);
	    	$this->db->update('semesterkurs', array('KursSchreiben' => 1) );
    }
    public function write_deactivate_status_pruefung($kurs_id)
    {
	    	$this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
	    	$this->db->where('KursID', $kurs_id);
	    	$this->db->update('semesterkurs', array('KursSchreiben' => 0));
    }


    
    public function query_status_hoeren($kurs_id)
    {
	    	$this->db->select('KursHoeren')
	    			 ->from('semesterkurs')
	    			 ->where('SemesterplanID', $this->user_model->get_semesterplanid())
	    			 ->where('KursID', $kurs_id)
	    			 ;

	    	$q = $this->db->get()->row_array();

	    	echo $q['KursHoeren'];
    }
    public function write_activate_status_hoeren($kurs_id)
    {
	    	$this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
	    	$this->db->where('KursID', $kurs_id);
	    	$this->db->update('semesterkurs', array('KursHoeren' => 1) );
    }
    public function write_deactivate_status_hoeren($kurs_id)
    {
	    	$this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
	    	$this->db->where('KursID', $kurs_id);
	    	$this->db->update('semesterkurs', array('KursHoeren' => 0));
    }





    public function query_status_hoeren_vl($kurs_id)
    {
    		$this->db->select('Semester')
    				->from('studiengangkurs')
    				->where('KursID', $kurs_id)
    				;
    		return $this->db->get()->row_array();
    }


	// alte Methoden --------------------------------------------------------------------

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
		foreach ($neue_reihenfolge as $serialized_position)
		{
			$data = array(
               //'Semesterposition' => $counter,
               'Semester' => $semesternr
               );


			$this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
			$this->db->where('KursID', $serialized_position);
			$this->db->update('Semesterkurs', $data);

			$counter++;
		}
	}


}