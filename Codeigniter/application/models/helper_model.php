<?php

/**
 * Provides data that is needed in several views/controllers
 * i.e. dropdown data for times, days, ... , excel-colors?
 * 
 * are there any more??
 * 
 * Also methods that are needed at several places
 * >> logging
 * 
 * @author frank gottwald
 * 
 */

class Helper_model extends CI_Model {
    
    public function __construct() {
		parent::__construct();
    }
    
    /**
     * Returns array holding data for codeigniter's form_dropdown(...)-method
	 * Called from Timetable-Mgt. & Course-Mgt.
	 * 
     * @param String $type - so far 'starttimes', 'endtimes' or 'days'
     * @return array holding all OPTIONS for drowpdown - can be used directly in 
     * >> form_dropdown('name', $OPTIONS, $val, $attrs);
     * @return array all options in simple array 
     */
    public function get_dropdown_options($type){
		$data = array();
		$name = '';
		
		// depending on dropdown-type fetching right data
		// table-name is passed as second-parameter
		switch ($type) {
			case 'starttimes' : 
			$name = 'Beginn';
			$data = $this->get_dropdown_data($name, 'stunde');
			break;
			case 'endtimes' : 
			$name = 'Ende';
			$data = $this->get_dropdown_data($name, 'stunde');
			break;
			case 'days' : 
			$name = 'TagName';
			$data = $this->get_dropdown_data($name, 'tag');
			break;
		}
		
		// run through data and build options-array
		for($i = 0; $i < count($data); $i++){
			$options[$i] = $data[$i]->$name;
		}
		return $options;
    }

	/**
	 * Helper-method to get dropdown-data from db
	 * Returns all dropdown-options needed
	 * 
	 * Depending on table the correct data is fetched from db
	 * 
	 * @param string $type attribute of the table that should be fetched
	 * @param string $table the table in db
	 * @return array holding options
	 */
    private function get_dropdown_data($type, $table){
		$data = array(); // init
		$q = ''; // init
		
		$this->db->select($type);
		$q = $this->db->get($table);

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
			$data[] = $row;
			}
			return $data;
		}
    }
    
    
	/**
	 * Helper to fetch name for course_id
	 * 
	 * @param int $course_id course_id to get the name for
	 * @return array
	 */
    public function get_course_name($course_id){
		$this->db->select('Kursname');
		$q = $this->db->get_where('studiengangkurs', $course_id);

		if($q->num_rows() == 0){
			foreach ($q->result() as $row){
			return $row;
			}
		}
	
    }
	
	
	/**
	 * Converts Excel- to rgb-color
	 * @param String $col excel-id of color
	 * @param boolean $light
	 * @return string
	 */
	public function excel_color( $col, $light = false ) 
	{		
		// hellere Farbe
		if( $light ) 
		{		
			// Fallunterscheidung f�r Excel-Farb-IDs
			switch( $col ) 
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// wei�
				case 255: 		$ret = "ffaaaa"; break;	// rot
				case 65280: 	$ret = "aaffaa"; break;	// gr�n
				case 16711680: 	$ret = "aaaaff"; break;	// blau
				case 65535: 	$ret = "ffffaa"; break;	// gelb
				case 16711935: 	$ret = "ffaaff"; break;	// magenta
				case 16776960: 	$ret = "ccffff"; break;	// cyan
				case 128: 		$ret = "baaaaa"; break;
				case 32768: 	$ret = "aabaaa"; break;
				case 8388608: 	$ret = "aaaaba"; break;
				case 32896: 	$ret = "babaaa"; break;
				case 8388736: 	$ret = "baaaba"; break;
				case 8421376: 	$ret = "aababa"; break;
				case 12632256: 	$ret = "cacaca"; break;
				case 8421504: 	$ret = "8a8a8a"; break;
				case 16751001: 	$ret = "ccccff"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 13434879: 	$ret = "fffff0"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 6684774: 	$ret = "99aa99"; break;
				case 8421631: 	$ret = "ffbaba"; break;
				case 13395456: 	$ret = "aa99ff"; break;
				case 16764108: 	$ret = "f0f0ff"; break;
				case 16763904: 	$ret = "aaf0ff"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 13434828: 	$ret = "f0fff0"; break;
				case 10092543: 	$ret = "ffff00"; break;
				case 16764057: 	$ret = "ccf0ff"; break;
				case 13408767: 	$ret = "ffccff"; break;
				case 16751052: 	$ret = "f0ccff"; break;
				case 10079487: 	$ret = "fff5cc"; break;
				case 16737843: 	$ret = "6699ff"; break;
				case 13421619: 	$ret = "66f0f0"; break;
				case 52377: 	$ret = "ccf0aa"; break;
				case 52479: 	$ret = "fff0aa"; break;
				case 39423: 	$ret = "ffcc33"; break;
				case 26367: 	$ret = "ffcc66"; break;
				case 10053222: 	$ret = "9999cc"; break;
				case 9868950: 	$ret = "c9c9c9"; break;
				case 6697728: 	$ret = "aa6699"; break;
				case 6723891: 	$ret = "66cc99"; break;
				case 13209: 	$ret = "cc66aa"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 10040115: 	$ret = "6666cc"; break;
			}
			
		// normale Farbe
		} 
		else 
		{		
			// Fallunterscheidung f�r Excel-Farb-IDs
			switch( $col ) 
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// wei�
				case 255: 		$ret = "ff0000"; break;	// rot
				case 65280: 	$ret = "00ff00"; break;	// gr�n
				case 16711680: 	$ret = "0000ff"; break;	// blau
				case 65535: 	$ret = "ffff00"; break;	// gelb
				case 16711935: 	$ret = "ff00ff"; break;	// magenta
				case 16776960: 	$ret = "00ffff"; break;	// cyan
				case 128: 		$ret = "800000"; break;
				case 32768: 	$ret = "008000"; break;
				case 8388608: 	$ret = "000080"; break;
				case 32896: 	$ret = "808000"; break;
				case 8388736: 	$ret = "800080"; break;
				case 8421376: 	$ret = "008080"; break;
				case 12632256: 	$ret = "c0c0c0"; break;
				case 8421504: 	$ret = "808080"; break;
				case 16751001: 	$ret = "9999ff"; break;
				case 6697881: 	$ret = "993366"; break;
				case 13434879: 	$ret = "ffffcc"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 6684774: 	$ret = "660066"; break;
				case 8421631: 	$ret = "ff8080"; break;
				case 13395456: 	$ret = "0066cc"; break;
				case 16764108: 	$ret = "ccccff"; break;
				case 16763904: 	$ret = "00ccff"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 13434828: 	$ret = "ccffcc"; break;
				case 10092543: 	$ret = "ffff99"; break;
				case 16764057: 	$ret = "99ccff"; break;
				case 13408767: 	$ret = "ff99cc"; break;
				case 16751052: 	$ret = "cc99ff"; break;
				case 10079487: 	$ret = "ffcc99"; break;
				case 16737843: 	$ret = "3366ff"; break;
				case 13421619: 	$ret = "33cccc"; break;
				case 52377: 	$ret = "99cc00"; break;
				case 52479: 	$ret = "ffcc00"; break;
				case 39423: 	$ret = "ff9900"; break;
				case 26367: 	$ret = "ff6600"; break;
				case 10053222: 	$ret = "666699"; break;
				case 9868950: 	$ret = "969696"; break;
				case 6697728: 	$ret = "003366"; break;
				case 6723891: 	$ret = "339966"; break;
				case 13209: 	$ret = "993300"; break;
				case 6697881: 	$ret = "993366"; break;
				case 10040115: 	$ret = "333399"; break;
			}		
		}
		
		// Gebe Farbwert zur�ck
		return $ret; 
	}
	

	/**
	 * Creates array with activity-data of a user to save to db
	 * Passed data
	 * @param int $logging_typ_id look at logtyp-table to get the correct logtype
	 * @param int $zielkurs SPKursID or null if not bound to a course
	 */
	public function log_activities ($logging_typ_id, $user_id, $tutor = NULL){
		
		// build array with data to be stored
		$log_data = array(
			'LogtypID' => $logging_typ_id,
			'BenutzerID' => $user_id,
			'TutorID' => $tutor
		);
		
		// save data
		$this->db->insert('logging_kursverwaltung', $log_data);
	}
	
	
	
	/**
	 * Helper function to create a new group-entry in db
	 */
    public function create_new_group(){
		$a = array(
			'TeilnehmerMax' => 20,
			'TeilnehmerWartelisteMax' => 0,
			'Anmeldung_zulassen' => 0
		);
		$this->db->insert('gruppe', $a);
    }
	
	/**
	 * Helper to get the highest (i.e. mostly newest) group_id from gruppe-table
	 * @return object
	 */
    public function get_max_group_id_from_gruppe(){
		$data = array();
		$this->db->select_max('GruppeID');
		$q = $this->db->get('gruppe');

		if($q->num_rows() == 1){
			foreach ($q->result() as $row){
				$data = $row;
			}
			return $data;
		}
    }
	
	
	/**
	 * Helper to get the highest (i.e. mostly newest) sp_course_id from stundenplankurs-table
	 * @return object
	 */
    public function get_max_spkurs_id(){
		$data = array();
		$this->db->select_max('SPKursID');
		$q = $this->db->get('stundenplankurs');

		if($q->num_rows() == 1){
			foreach ($q->result() as $row){
				$data = $row;
			}
			return $data;
		}
    }
	

	/**
	 * Helper to update benutzerkurs-table for each student in this degree_program
	 * 
	 * @param int $editor_id
	 * @param int $event_type_id
	 * @param int $course_id
	 * @param int $sp_course_id
	 * @param int/array $stdgng_id passed during parsing; when not parsed >> id has to be generated from unique combi po, abk that is passed
	 */
	public function update_benutzerkurs($editor_id, $event_type_id, $course_id, $sp_course_id, $stdgng_id){
		
		// if stdgng_id = -1 >> get it through course_id
		if(is_array($stdgng_id)){
			$stdgng_id_tmp = ''; // init
			$stdgng_id_tmp = $this->get_stdgng_id($stdgng_id[0], $stdgng_id[2]);
			$stdgng_id = $stdgng_id_tmp->StudiengangID;
		}
		
		// get all students for degree program
		$students = array();
		$students = $this->get_student_ids($stdgng_id);


		// run through students and generate benutzerkurse
		foreach ($students as $s){
			$isActive = false; // init
			// mark courses as active if they are 'vorlesung' = 1 or 'tutorium' = 6
			if($event_type_id == 1 || $event_type_id == 6){
				$isActive = true;
			} else {
				// all other courses are inactive
				$isActive = false;
			}
			// get semester that should be added to benutzerkurs
			$semester_tmp = array();
			$semester_tmp = $this->get_user_course_semester($s->BenutzerID, $course_id);
			$semester = '';
			if(isset($semester_tmp)){
				$semester = $semester_tmp->Semester;
			}

//						echo $this->pre($semester);

			// proceed only if there is a course_name
			// otherwise this part of array is empty (i.e. no courses at this time)
			if($semester){
				$this->save_data_to_benutzerkurs(
					$s->BenutzerID,
					$course_id,
					$sp_course_id,
					$semester,
					(($isActive) ? 1 : 0),
					'stdplan_parsing',
					$editor_id
				);
			}
		}
	}
	
	
	/**
	 * Helper
	 * Returns semester in which a given user put the course
	 * 
	 * @param int $user_id
	 * @param int $course_id
	 * @return object
	 */
    private function get_user_course_semester($user_id, $course_id){
		$data = array();
		$this->db->select('b.Semester');
		$this->db->from('semesterplan as a');
		$this->db->join('semesterkurs as b', 'a.SemesterplanID = b.SemesterplanID');
		$this->db->where('b.KursID = '.$course_id . ' and a.BenutzerID = '. $user_id);

		$q = $this->db->get();

		if($q->num_rows() == 1){
			foreach ($q->result() as $row){
				$data = $row;
			}
			return $data;
		}
	
//	select b.`Semester`
//	from semesterplan as a
//	inner join semesterkurs as b
//	on a.`SemesterplanID` = b.`SemesterplanID`
//	where b.`KursID` = 1 and a.`BenutzerID` = 1383;
    }
	
	
	/**
	 * Helper to save data to benutzerkurs-table for a single user
	 * 
	 * @param int $user_id
	 * @param int $course_id
	 * @param int $spcourse_id
	 * @param int $semester 
	 * @param int $active_flag 1/0
	 * @param string $comment where does the update come from - admin-view or parsing
	 * @param int $edit_id 
	 */
	private function save_data_to_benutzerkurs($user_id, $course_id, $spcourse_id, $semester, $active_flag, $comment, $edit_id){
		$benutzerkurs_record = array(
			'BenutzerID' => $user_id,
			'KursID' => $course_id,
			'SPKursID' => $spcourse_id,
			'SemesterID' => $semester,
			'aktiv' => $active_flag,
			'changed_at' => $comment,
			'edited_by' => $edit_id
		);

		$this->db->insert('benutzerkurs', $benutzerkurs_record);
    } 
	

	/**
	 * Helper to get all students for a specific degree program
	 * 
	 * @param int $stdgng_id id is passed
	 * @return array students
	 */
    private function get_student_ids($stdgng_id){
		$data = array();
		$this->db->distinct();
		$this->db->select('a.BenutzerID');
		$this->db->from('benutzer as a');
		$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID and b.RolleID = 5');
		$this->db->where('StudiengangID', $stdgng_id);

		$q = $this->db->get();

		if($q->num_rows() >= 1){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
			return $data;
		}
    }
	
	
	/**
	 * Helper to get degree_program-id from po and abk
	 * 
	 * @param int $po pruefungsordnung
	 * @param int $stdgng_short abkuerzung
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
	
}

?>
