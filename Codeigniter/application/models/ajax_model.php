<?php

/**
 * meinFHD 2.0
 * http://www..de/
 *
 * @author Konstantin Voth
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
	public function set_reihenfolge($module, $semesternr, $hoeren, $pruefen, $mark)
	{
        FB::log($module);

		// counter für die Reihenfolge
		// $counter = 1;
		// speichere neue Reihenfolge in die DB
         $i = 0;
		foreach ($module as $kursid)
		{
            if ( empty($mark[$i]) ) $mark[$i] = 101; /*else $mark[$i] = 100;*/

			$data = array(
               //'Semesterposition' => $counter,
               'Semester' => $semesternr,
               'KursHoeren' => $hoeren[$i],
               'KursSchreiben' => $pruefen[$i],
               'Notenpunkte' => $mark[$i]
               );


            $this->db->where('SemesterplanID', $this->user_model->get_semesterplanid());
            $this->db->where('KursID', $kursid);
            $this->db->update('semesterkurs', $data);

            $i++;
            // $counter++;
        }
	}


    public function set_reihenfolge_benutzerkurs($module, $semesternr)
    {
        foreach ($module as $kursid)
        {
            $data = array(
                'SemesterID'        => $semesternr,
                'changed_at'        => 'studienplan_semesterplan: Änderung vornehmen',
                'edited_by'         => $this->user_model->get_userid()
                );
            $this->db->where('BenutzerID', $this->user_model->get_userid());
            $this->db->where('KursID', $kursid);                                            ///////////// EVLT. kursreferenz!!!!!!!!!!!!!!!!!!!!
            $this->db->update('benutzerkurs', $data);                                       ///////////// WHERE `KursID` = (
                                                                                            ///////////// SELECT referenzkursid
                                                                                            ///////////// FROM kursreferenz
                                                                                            ///////////// WHERE kursid = '".$_POST["kursid".$i]."'
                                                                                            ///////////// )
        }
    }


    /**
     * Get all events, where the user is able to sub-/unsubscribe. (All, except VL)
     *
     * @param  int $kursid      Which Studiengangkurs should be looked for.
     *
     * @return mixed            all withdrawable events. [SPKursID, GruppeID]
     */
    public function get_withdrawable_events($kursid)
    {
        $this->db->select('bk.SPKursID, spk.GruppeID, bk.SemesterID, sk.Semester')
                 ->from('benutzerkurs bk')
                 ->join('stundenplankurs spk', 'spk.KursID = bk.KursID AND spk.SPKursID = bk.SPKursID')
                 ->join('studiengangkurs sk', 'sk.KursID = spk.KursID')
                 ->where('bk.KursID', $kursid)
                 ->where('bk.BenutzerID', $this->user_model->get_userid())
                 ->where('spk.VeranstaltungsformID !=', 1);
        $q = $this->db->get();

        return $q->result_array();
    }


}