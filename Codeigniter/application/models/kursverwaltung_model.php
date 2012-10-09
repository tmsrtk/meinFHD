<?php
class Kursverwaltung_model extends CI_Model {
    
    /**
     * Returns course/tut-data.
     * @param int $id
     * @param int $eventtype
     * @return type
     */
    public function get_lecture_details($course_id, $eventtype){
		$this->db->select('SPKursID, Raum, StartID, EndeID, TagID, GruppeID');
		$this->db->where('KursID', $course_id);
		$this->db->where('VeranstaltungsformID', $eventtype);
		$q = $this->db->get('stundenplankurs');

		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
			$data[] = $row;
			}
		}

		return $data[0];
    }
    
	
    /**
     * Returns lab, seminar, tut - details - eventtype passed
	 * Method used for showing all lab groups and notes with participants
     * @param int $id
     * @param int $eventtype
     * @return type
     */
    public function get_course_details($course_id, $eventype){
		$this->db->distinct();
		$this->db->select('a.SPKursID, b.Kursname, b.kurs_kurz, a.Raum, t.TagName, s.Beginn, a.GruppeID, c.VeranstaltungsformName');
		$this->db->from('stundenplankurs as a');
		$this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
		$this->db->join('veranstaltungsform as c', 'a.VeranstaltungsformID = c.VeranstaltungsformID');
		$this->db->join('tag as t', 't.TagID = a.TagID');
		$this->db->join('stunde as s', 's.StundeID = a.StartID');
		$this->db->where('a.KursID', $course_id);
		$this->db->where('a.VeranstaltungsformID', $eventype);
		$q = $this->db->get();

		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}
		
		return $data;
    }
    
	
    /**
     * Returns name for given course_id
     * @param int $course_id
     * @return String 
     */
    public function get_lecture_name($course_id){
		$this->db->select('kurs_kurz, Beschreibung')->where('KursID', $course_id);
		$q = $this->db->get_where('studiengangkurs');

		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		return $data[0];
    }
    
    
    public function check_names(){
		$data = array();

		$lecture_ids = $this->get_lecture_ids();
		$non_lecture_ids = $this->get_non_lecture_ids();

		foreach($non_lecture_ids as $nl_id){
			if(!in_array($nl_id, $lecture_ids)){
				$data[] = $nl_id;
			}
		}

		return $data;
    }
    
    private function get_lecture_ids(){
		$this->db->distinct();
		$this->db->select('KursID');
		$this->db->where('VeranstaltungsformID', 1);
		$q = $this->db->get('stundenplankurs');

		$ids = array(); // init
		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		foreach($data as $d){
			$ids[] = $d->KursID;
		}
		return $ids;
    }
    
    private function get_non_lecture_ids(){
		$this->db->distinct();
		$this->db->select('KursID');
		$this->db->where('VeranstaltungsformID !=', 1);
		$q = $this->db->get('stundenplankurs');

		$ids = array(); // init
		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		foreach($data as $d){
			$ids[] = $d->KursID;
		}
		return $ids;
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

		$q = $this->db->get();

		$data = array(); // init

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

		$data = array(); // init

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

		$data = array(); // init

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

		$data = array(); // init

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
    
    
//  ################################################################ SAVING DATA
    
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
	 * Saves the description typed into textfield on my courses view.
	 * @param type $cid
	 * @param type $desc
	 */
	public function save_course_description($cid, $desc){
		$this->db->where('KursID', $cid);
		$this->db->update('studiengangkurs', array('Beschreibung' => $desc));
	}
    
    
    public function save_staff_to_db($course_id, $new_staff_ids, $table){
		// get old staff for that course
		$former_labings_tuts = array();
		$former_labings_tuts = $this->get_ids_of_labings_tuts_for_course($course_id, $table);

	//	echo '<pre>';
	//	echo '<div>former_labings</div>';
	//	print_r($former_labings);
	//	echo '</pre>';

		// only if there are former labings
		if($former_labings_tuts){
			// run through OLD and check if in NEW >> delete if not
			foreach($former_labings_tuts as $fl){

				// TODO ?? ggf. hier wenn keine neuen personen hinzugefügt werden ALLE löschen?!?!?!?!?!?!!?

				if(!in_array($fl, $new_staff_ids)){
					// delete from table
					$this->db->delete($table, array('BenutzerID' => $fl, 'KursID' => $course_id));
				}
			}
		}

		// if there is new staff - add to db
		if($new_staff_ids){
			// run through NEW and check if in OLD >> add if not
			foreach($new_staff_ids as $nsi){
				if(!in_array($nsi, $former_labings_tuts)){
					// add to table
					$data = array(
					'BenutzerID' => $nsi,
					'KursID' => $course_id
					);
					$this->db->insert($table, $data);
				}
			}
		}

	//	echo '<pre>';
	//	print_r($former_labings_tuts);
	//	echo '</pre>';

		// role-modifications only relevant for labings - roles set/revoked implicitly
		// note: tut-roles has to be set by admin
		if($table == 'kursbetreuer'){
			$this->update_roles();	    
		}

    }
    
    /**
     * Helper just to split method
     */
    private function update_roles(){
		$former_prof_ids = array();
		$current_prof_ids = array();

	//	    $this->update_roles();
		// get profs with role_id 3 >> i.e. labings
		$former_prof_ids = $this->get_ids_of_profs_who_have_labing_role();

		// get profs from *kurs*betreuer - (laboringenieur table deprecated)
		$current_prof_ids = $this->get_ids_of_profs_from_labing_table();


		// if there are former profs
		if($former_prof_ids){
			// run through old profs check if in profs from labing >> delete if not
			foreach ($former_prof_ids as $fp) {
				// if id is no longer in labing-table
				if(!in_array($fp, $current_prof_ids)){
					// delete
					$this->db->delete('benutzer_mm_rolle', array('BenutzerID' => $fp, 'RolleID' => 3));
				}
			}
		} // endif former_prof_ids

		// if there are any profs assigned to courses
		if($current_prof_ids){
			// run through NEW profs (from labing) check if in OLD >> add if not
			foreach ($current_prof_ids as $cp_id) {
				// if there is a new prof in labing-table
				if(!in_array($cp_id, $former_prof_ids)){
					// add to benutzer_mm_rolle
					$data = array(
						'BenutzerID' => $cp_id,
						'RolleID' => 3
					);
					$this->db->insert('benutzer_mm_rolle', $data);
				}
			}
		} // endif current_prof_ids
    }
    
    
    /**
     * Helper to get labing-/tut-ids for given course
     * @param type $course_id
     * @param type $table
     * @return type
     */
    private function get_ids_of_labings_tuts_for_course($course_id, $table){
		$this->db->select('BenutzerID');
		$this->db->from($table);
		$this->db->where('KursID', $course_id);
		$q = $this->db->get();

		$ids = array(); // init
		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		foreach($data as $d){
			$ids[] = $d->BenutzerID;
		}
		return $ids;
    }
    
    /**
     * Helper to get profs with labing-role
     * @return type
     */
    private function get_ids_of_profs_who_have_labing_role(){
		$this->db->select('a.BenutzerID, a.RolleID, b.RolleID as RolleID2');
		$this->db->from('benutzer_mm_rolle as a');
		$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID');
		$this->db->where('a.RolleID = 2');
		$this->db->where('b.RolleID = 3');
		$q = $this->db->get();

		$ids = array(); // init
		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		foreach($data as $d){
			$ids[] = $d->BenutzerID;
		}
		return $ids;
	
    }
    
    /**
     * Helper to get profs that are currently in labing-table for a course
     * @return type
     */
    private function get_ids_of_profs_from_labing_table(){
		$this->db->distinct();
		$this->db->select('a.BenutzerID');
		$this->db->from('kursbetreuer as a');
		$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID');
		$this->db->where('b.RolleID', 2);
		$q = $this->db->get();

		$ids = array(); // init
		$data = array(); // init

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		foreach($data as $d){
			$ids[] = $d->BenutzerID;
		}
		return $ids;
    }
	
	
	
	
	public function create_file_with_participants_for_course($course_id, $is_spcourse){
		$course_data = array();
		$participants_data = array();
		
		// getting course-details
		$this->db->select('a.Kursname, a.kurs_kurz, b.VeranstaltungsformAlternative, t.TagName, s.Beginn, r.Ende, g.TeilnehmerMax');
		$this->db->from('studiengangkurs as a');
		$this->db->join('stundenplankurs as b', 'a.KursID = b.KursID');
		$this->db->join('gruppe as g', 'b.GruppeID = g.GruppeID');
		$this->db->join('tag as t', 'b.TagID = t.TagID');
		$this->db->join('stunde as s', 'b.StartID = s.StundeID');
		$this->db->join('stunde as r', 'b.EndeID = r.StundeID');
		$this->db->where('b.SPKursID', $course_id);
		$q_course = $this->db->get();
		
		if($q_course->num_rows()  === 1){
			foreach($q_course->result() as $row){
				$course_data = $row;
			}
		}		
		
		// init
		$file_data = '';
		
		// store some genereal course data to put into file
		$file_data .= "Fach:;".$course_data->Kursname." (".$course_data->kurs_kurz.");\r";
		$file_data .= 'Gruppe:;'.$course_data->VeranstaltungsformAlternative.";\r";
		$file_data .= "Tag:;".$course_data->TagName.";\r";
		$file_data .= "Beginn:;".$course_data->Beginn.";\r";
		$file_data .= "Ende:;".$course_data->Ende.";\r";
		$file_data .= "Teilnehmer:;".$course_data->TeilnehmerMax.";\r";
		$file_data .= "Nachname:;Vorname:;Emailadresse:;\r";
		
//		$file_data .= "COURSE_ID:; ".$course_id.";\r";
		
		// getting participants of a single sp_course - if course or sp_course depending on passed bool-flag
		$participants_data = $this->get_participants_for_single_sp_course($course_id, $is_spcourse);
		
		// save data to 'file'
		foreach ($participants_data as $key => $value) {
			$file_data .= $value->Nachname.";";
			$file_data .= $value->Vorname.";";
			$file_data .= $value->Email.";\r";
		}
		
		return $file_data;
		
	}
	
	
	/**
	 * Returns participants for a single course depending on bool that's passed
	 * @param type $sp_course_id
	 * @return type
	 */
	public function get_participants_for_single_sp_course($sp_course_id, $is_sp_course){
		$participants_data = array();
		
		$this->db->select('Nachname, Vorname, Email');
		$this->db->from('benutzer as a');
		// if querying for participants of SP_course >> 
		if($is_sp_course){
			$this->db->join('gruppenteilnehmer as b', 'a.BenutzerID = b.BenutzerID');
			$this->db->join('stundenplankurs as c', 'b.GruppeID = c.GruppeID');
			$this->db->where('c.SPKursID', $sp_course_id);
		// else: querying for participants of course >> 
		} else {
			$this->db->join('benutzerkurs as b', 'a.BenutzerID = b.BenutzerID');
			$this->db->where('b.SPKursID', $sp_course_id);
		}
		$q_part = $this->db->get();

		if($q_part->num_rows() > 0){
			foreach($q_part->result() as $row){
				$participants_data[] = $row;
			}
		}
		
		return $participants_data;
		
	}
	
	/**
	 * Counts all participants belonging to a single sp_course
	 * courses and sp_courses (differentiation necessary for labs) depending on passed boolean
	 * @param int $id always sp_course_id
	 * @param boolean $is_sp_course sp_course or course
	 * @return int
	 */
	public function count_participants_for_course($id, $is_sp_course){
		// getting participants
		$data = $this->get_participants_for_single_sp_course($id, $is_sp_course);
		
		$counter = 0;
		foreach ($data as $value) {
			$counter++;
		}
		
		return $counter;
	}
	
	
	public function update_benutzerkurs_activation($id, $enable){
		
	}
	
	

	/**
	 * 
	 * @param type $matrno
	 * @return array holding 
	 */
	public function search_student_by_matrno($matrno){
		$q = ''; // init
		$data = array(); // init
		
		$this->db->select('Vorname, Nachname, Matrikelnummer');
		$this->db->from('benutzer');
		$this->db->where('Matrikelnummer', $matrno);
		$q = $this->db->get();

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}
		return $data;
		
	}
	

	
	/* 
	 * 
	 * ****************************************** course-mgt
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
	
	
	/* ************************************************************************
	 * 
	 * ********************************************* lab-mgt
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	
	/**
	 * Returns all notes for one lab-group
	 * @param int $group_id
	 * @return array
	 */
	public function get_lab_notes($group_id){
		$q = ''; // init
		$data = array(); // init
		
		$this->db->select('b.Vorname, b.Nachname, a.*');
		$this->db->from('gruppenteilnehmer_aufzeichnungen as a');
		$this->db->join('benutzer as b', 'a.BenutzerID = b.BenutzerID');
		$this->db->where('GruppeID', $group_id);
		$this->db->order_by('ende');
		$q = $this->db->get();

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		return $data;
	}
	
	/**
	 * Returns all dates - as far as some labing stored dates before
	 * @param array $group_id holding GruppeID (always) and all dates (if stored otherwise nothing)
	 */
	public function get_lab_dates($group_id){
		$q = ''; // init
		$data = array(); // init
		
		$this->db->from('gruppentermin');
		$this->db->where('GruppeID', $group_id);
		$q = $this->db->get();

		$index = 0;
		if($q->num_rows() > 0){
			foreach ($q->result_array() as $row ){
				// preparing data to get array (starting with GruppeID, then counting up from 0 to 19
				foreach($row as $i => $r){
					if($i == 'GruppeID'){
						$data['GruppeID'] = $r;
					} else {
						$data[$index] = $r;
						$index++;
					}
				}
			}
		}

		return $data;
	}
	
	
	/* 
	 * 
	 * ********************************************* lab-mgt
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
    
    
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
