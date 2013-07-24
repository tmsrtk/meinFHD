<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Kursverwaltung_model
 * The kursverwaltung / course administration model provides all necessary db operations, that are required for the
 * course administration.
 *
 * @version 0.0.1
 * @package meinFHD\models
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Frank Gottwald (FG), <frank.gottwald@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Kursverwaltung_model extends CI_Model {

    /*
     * ==================================================================================
     *                                   Course administration start
     * ==================================================================================
     */

    /**
     * Returns course or tut-details for the passed course-id and eventtype.
	 * This implementation is used for lectures (Vorlesungen) and tuts (Tutorien).
	 * 
	 * IMPORTANT:
	 * function returns only first!! index of the found data.
	 * That means only one lecture or tut is being returned and showed.
	 * 
	 * For more than one lecture or tut another implementation is necessary.
	 *
     * @access public
     * @param int $course_id the course-id to get the details for
     * @param int $eventtype the eventtype to get the details for
     * @return array Array with all details for that lecture/tut
     */
    public function get_lecture_details($course_id, $eventtype){
		$data = array(); // init
		$q = '';

		$this->db->select('SPKursID, Raum, StartID, EndeID, TagID, GruppeID');
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
     * Returns lab, seminar, tut - details - for an passed course id depending
     * on the passed eventtype.
	 * Method used for showing all lab groups and notes with participants.
     *
     * @access public
     * @param int $course_id ID of the course, where the details should be selected for
     * @param int $eventtype The eventtype/VeranstaltungsformID of the course
     * @return array
     */
    public function get_course_details($course_id, $eventype){
		$data = array(); // init
		$q = '';
		
		$this->db->distinct();
		$this->db->select('a.SPKursID, b.Kursname, b.kurs_kurz, a.Raum, t.TagName, s.Beginn, ss.Ende, a.GruppeID, c.VeranstaltungsformName');
		$this->db->from('stundenplankurs as a');
		$this->db->join('studiengangkurs as b', 'a.KursID = b.KursID');
		$this->db->join('veranstaltungsform as c', 'a.VeranstaltungsformID = c.VeranstaltungsformID');
		$this->db->join('tag as t', 't.TagID = a.TagID');
		$this->db->join('stunde as s', 's.StundeID = a.StartID');
		$this->db->join('stunde as ss', 'ss.StundeID = a.EndeID');
		$this->db->where('a.KursID', $course_id);
		$this->db->where('a.VeranstaltungsformID', $eventype);
		$q = $this->db->get();

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}
		
		return $data;
    }
    
	
    /**
     * Returns the course-short-name, the course-long-name and the course-description for the course with
     * the given id
	 *
     * @access public
     * @param int $course_id course_id to get the name for
     * @return array $data[0] first index of the result -
	 *         containing the shortname, the longname and the description for that course.
     *         To access the result you have the following options to access the content
     *         of the array, that is going to be returned:
     *              ->Kursname, ->kurs_kurz, ->Beschreibung
     */
    public function get_lecture_name($course_id){
        $data = array(); // init
		$q = '';
		
		$this->db->select('Kursname, kurs_kurz, Beschreibung')->where('KursID', $course_id);
		$q = $this->db->get_where('studiengangkurs');

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		}

		return $data[0];
    }

    /**
     *
     *
     * @access public
     * @return array
     */
    public function check_names(){
		$data = array();

		$lecture_ids = $this->_get_lecture_ids();
		$non_lecture_ids = $this->_get_non_lecture_ids();

		foreach($non_lecture_ids as $nl_id){
			if(!in_array($nl_id, $lecture_ids)){
				$data[] = $nl_id;
			}
		}

		return $data;
    }

    /**
     * Returns all distinct course/lecture ids, that correspond to the VeranstaltungsformID 1.
     *
     * @access private
     * @return array Simple 1-dimensional array with all course ids. There are no keys required to access
     *              the content of the array.
     */
    private function _get_lecture_ids(){
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

    /**
     * Returns all course ids / courses where the VeranstaltungsformID is not 1 (Vorlesung)
     * from the database table stundenplankurs.
      *
     * @access private
     * @return array Simple 1-dimensional array with all course ids. There are no keys required to access
     *              the content of the array.
     */
    private function _get_non_lecture_ids(){
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
     * Returns first and last name of the professor, who is assigned to the given course.
     *
     * @access public
     * @param int $course_id The course-id where the prof / dozent should be selected for.
     * @return array Simple 2-dimensional array with the data. There are no keys required to
     *               access the data. Each entry in the first dimension corresponds to
     *               one prof, the second dimension contains the information for the prof.
     *               The information could be accessed via the keys 'Titel', 'Vorname',
     *               'Nachname'.
     */
    public function get_profname_for_course($course_id){
		$data = array(); // init
		$q = ''; // init
		
		$this->db->distinct();
		$this->db->select('Titel, Vorname, Nachname');
		$this->db->from('stundenplankurs as a');
		$this->db->join('benutzer as b', 'a.DozentID = b.BenutzerID');
		$this->db->where('KursID', $course_id);
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
     * Returns the course details for the given course id, that correspond to
     * the given eventtype id.
     *
     * @access public
     * @param int $course_id ID of the course, where the information should be selected for.
     * @param int $eventtype Eventtype id (VeranstaltungsformID) of the course
     * @return array Simple 1-dimensional array that contains the details for the given course.
     *               The information about the course are going to be saved in the array as an
     *               object and need to be accessed via ->Attribute_name
     */
    public function get_lab_details($course_id, $eventtype){
		$data = array(); // init
		$q = ''; // init
		
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
     * Returns all distinct / different eventtypes for an given course id.
	 *
     * @access public
     * @param int $course_id The course id, where the eventtypes should be selected for.
     * @return array Simple indexed array, holding all eventtypes the desired course has got.
     */
    public function get_eventtypes_for_course($course_id){
		$data = array(); // init
		$q = ''; // init
		
		$this->db->distinct();
		$this->db->select('VeranstaltungsformID');
		$this->db->order_by('VeranstaltungsformID', 'asc');
		$q = $this->db->get_where('stundenplankurs', array('KursID'=>$course_id));

		foreach ($q->result_array() as $row) { 
			$data[] = $row;
		}

		// clean to have simple indexed array
		$data = $this->_clean_nested_array($data);

		return $data;
    }

    /**
     * Returns the eventtype (eventtype id) for an given spcourse id.
     *
     * @access public
     * @param int $spcourse_id The spcours id, where the eventtype should be selected for
     * @return int The eventtype of the desired spcourse
     */
    public function get_eventtype_for_spcourse($spcourse_id){

        $this->db->select('VeranstaltungsformID');
        $this->db->from('stundenplankurs');
        $this->db->where('SPKursID', $spcourse_id);

        $query = $this->db->get();

        $eventtype = 0; // init of return variable for the eventtype
        if($query->num_rows() == 1){
            foreach($query->result() as $row){
                $eventtype = $row->VeranstaltungsformID;
            }
        }

        return $eventtype;
    }
    
    
    /**
     * Returns array with all labings/tuts belonging to a single course-id
     * Depending on passed table, passedswitch labings/tuts with passed table
	 *
     * @access public
     * @param int $course_id the course-id to get staff for
     * @param String $table table to get staff from
     * @return array array with all labings/tuts mapped to course-id [course_id] => [staff]
     */
    public function get_current_labings_tuts_for_course($course_id, $table){
		$data = array(); // init
		$q = ''; // init
		
		$this->db->distinct();
		$this->db->select('a.Vorname, a.Nachname, a.BenutzerID');
		$this->db->from('benutzer as a');
		$this->db->join($table.' as b', 'a.BenutzerID = b.BenutzerID');
        $this->db->where('b.KursID',$course_id);

		$q = $this->db->get();

		foreach ($q->result_array() as $row){
			$data[$course_id][] = $row;
		}

		return $data;
    }

    
    /**
     * Returns array with all possible labings
     * Labings for a course are profs (RolleID=2) AND labings (RolleID=3)
	 *
     * @access public
     * @return array simple array with all labings
     */
    public function get_all_possible_labings(){
		$data = array(); // init
		$q = ''; // init
		
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

		return $data;
    }
    
    
    /**
     * Returns array with all possible tutors
     * Tutors (RolleID=4)
	 *
     * @access public
     * @return array simple array with all tutors
     */
    public function get_all_tuts(){
		$data = array(); // init
		$q = ''; // init
		
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
		
		return $data;
    }
    
	/**
	 * Helper function to clean an nested array
	 * Runs through nested array and returns simple indexed array with values
	 *
     * @access private
	 * @param array $array the array to clean
	 * @return array simple indexed array
	 */
    private function _clean_nested_array($array){
		$clean = array(); // init
		foreach ($array as $a) {
			foreach ($a as $key => $value) {
				$clean[] = $value;
			}
		}
		return $clean;
    }

    /**
     * Saves the course and group detail data for an given course into the database.
     * Therefore the SPKursID (timetable course id) is passed separately. Also the
     * group and course information are passed in different arrays.
     *
     * @access public
     * @param int $spkurs_id The timetable course id, where the information should be saved for.
     * @param array $spkurs_data The course information, that should be stored.
     * @param array $group_data The group information, that shoould be stored
     * @return void
     */
    public function save_course_details($spkurs_id, $spkurs_data, $group_data){

        // update the course information
        $this->db->where('SPKursID', $spkurs_id);
		$this->db->update('stundenplankurs', $spkurs_data);


		// if there are some group information save them
		if($group_data){
			$group_id = $this->_get_group_id_for_spkursid($spkurs_id)->GruppeID;

			$this->db->where('GruppeID', $group_id);
			$this->db->update('gruppe', $group_data);
		}
    }
	
    
    /**
     * Helper function to get the group_id for an given sp_course_id
     *
     * @access private
     * @param int $spkurs_id The id of the spcourse (stundenplankurs), where the group_id should be selected for.
     * @return object The database object, that contains the group id. The result (group_id) could be accessed via ->GruppeID
     */
    private function _get_group_id_for_spkursid($spcourse_id){
		$this->db->select('GruppeID');
		$this->db->from('stundenplankurs');
		$this->db->where('SPKursID', $spcourse_id);
		$q = $this->db->get();

		$data = ''; // init

		if($q->num_rows() == 1){
			foreach ($q->result() as $row){
				$data = $row;
			}
		}
		return $data;
    }
	
	
	/**
     * Helper function to get the course_id for an given sp_course_id
     *
     * @access public
     * @param int $spcourse_id The id of the spcourse (stundenplankurs), where the group_id should be selected for.
     * @return object The database object, that contains the group id. The result (group_id) could be accessed via ->KursID
     */
    public function get_course_id_for_spkursid($spcourse_id){
		$this->db->select('KursID');
		$this->db->from('stundenplankurs');
		$this->db->where('SPKursID', $spcourse_id);
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
	 * Saves the description typed into the textfield on my courses view.
     *
     * @access public
	 * @param int $cid The id of the course, where the information should be saved for.
	 * @param string $desc The description, that should be saved in the database.
	 */
	public function save_course_description($cid, $desc){
		$this->db->where('KursID', $cid);
		$this->db->update('studiengangkurs', array('Beschreibung' => $desc));
	}


    /**
     * Saves all assigned staff for the given course id in the database.
     *
     * @access public
     * @param int $course_id The id of the course, where the information should be saved for-
     * @param array $new_staff_ids Array with the new staff, that should be saved.
     * @param string $table String with the name of the database table, where the information should be saved in.
     */
    public function save_staff_to_db($course_id, $new_staff_ids, $table){
		// get old staff for that course
		$former_labings_tuts = array();
		$former_labings_tuts = $this->_get_ids_of_labings_tuts_for_course($course_id, $table);

		// only if there are former labings
		if($former_labings_tuts){
			// run through OLD and check if in NEW >> delete if not
			foreach($former_labings_tuts as $fl){

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

		// role-modifications only relevant for labings - roles set/revoked implicitly
		// note: tut-roles has to be set by admin
		if($table == 'kursbetreuer'){
			$this->_update_roles();
		}

    }
    
    /**
     * Helper function to update the roles
     *
     * @access public
     * @return void
     */
    private function _update_roles(){
		$former_prof_ids = array();
		$current_prof_ids = array();

		// get profs with role_id 3 >> i.e. labings
		$former_prof_ids = $this->_get_ids_of_profs_who_have_labing_role();

		// get profs from *kurs*betreuer - (laboringenieur table deprecated)
		$current_prof_ids = $this->_get_ids_of_profs_from_labing_table();


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
     * Helper function to get all  labing-/tut-ids for the given course
     *
     * @access private
     * @param int $course_id ID of the course, where the information should be selected for.
     * @param string $table Name of the database table, where the information should be selected from.
     * @return array Simple 1-dimensional array with all User / DozentIDs, that are assigned to the course.
     */
    private function _get_ids_of_labings_tuts_for_course($course_id, $table){
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
     * Helper function to get the prof user ids with the labing-role.
     *
     * @access private
     * @return array Simple 1-dimensional array with all UserIDs, that have got the labing role.
     */
    private function _get_ids_of_profs_who_have_labing_role(){
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
     * Helper function to get profs that are currently in labing-table for at least one course.
     *
     * @access private
     * @return array Simple 1-dimensional array with all UserIDs, who are labings and who are assigned
     *               to at least one course.
     */
    private function _get_ids_of_profs_from_labing_table(){
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

    /**
     * Returns an string with the data, that needs to be placed inside the download file with the
     * course participants. Therefore the data is already prepared for being placed inside the
     * file. The names etc. are separated by ';'.
     *
     * @access public
     * @param $course_id int ID of the course, where participants list should be generated for.
     * @param $is_spcpourse bool Boolean flag if the course is an timetable (TRUE) or not (FALSE)
     * @return string String with the file data
     */
    public function get_data_for_course_participants_list($course_id, $is_spcourse){

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
        // if there is only 1 result
        if($q_course->num_rows() === 1){
            foreach($q_course->result() as $row){
                $course_data = $row;
            }
        }

        // init
        $file_data = '';

        // store some general course data to put into file
        $file_data .= "Fach:;".$course_data->Kursname." (".$course_data->kurs_kurz.");\r";
        $file_data .= 'Gruppe:;'.$course_data->VeranstaltungsformAlternative.";\r";
        $file_data .= "Tag:;".$course_data->TagName.";\r";
        $file_data .= "Beginn:;".$course_data->Beginn.";\r";
        $file_data .= "Ende:;".$course_data->Ende.";\r";

        // getting participants of a single sp_course - if course or sp_course depending on passed bool-flag
        $participants_data = $this->get_participants_for_single_sp_course($course_id, $is_spcourse);

        // place the count of max participants inside the file
        $file_data .= "Anzahl Teilnehmer:;".count($participants_data).";\r";
        // header for participants list
        $file_data .= "Nachname:;Vorname:;Emailadresse:;\r";

        // put every participant into the 'file'
        foreach ($participants_data as $key => $value) {
            $file_data .= $value->Nachname.";";
            $file_data .= $value->Vorname.";";
            $file_data .= $value->Email.";\r";
        }

        return $file_data;
    }

	/**
	 * Returns all participants for a single course with the following information:
     * - Name, surname, email. The result depends on the boolean flag that is
     * passed. The boolean flag indicates whether the desired course is an
     * timetable course or not.
     *
     * @access public
	 * @param int $sp_course_id The id of the course, where the participants should be selected for.
     * @param boolean $is_sp_course Boolean flag, that indicates whether the course is an spcourse or not.
	 * @return array Simple 1-dimensional array with all participant information saved as an object.
     *               Each array entry represents an object. The information stored in the object could
     *               be accessed via the -> selector. (Nachname, Vorname, Email)
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

		}
        else { // else: querying for participants of course >>
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
	 * Counts all participants belonging to a single sp_course.
	 * courses and sp_courses (differentiation necessary for labs) depending on passed boolean
	 *
     * @access public
	 * @param int $id always sp_course_id
	 * @param boolean $is_sp_course sp_course or course
	 * @return int number of participants in that course/sp_course
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

	/**
	 * De/activation of application / registration - for whole course (course_id)
	 * Find all sp_course_ids / group_ids for a course that should be activated.
	 *
     * @access public
	 * @param int $id course_id to de/activate courses for
	 * @param boolean $enable current status
	 */
	public function update_benutzerkurs_application($id, $enable){
		$data = array(); // init
		$q = ''; // init
		
		// find all group_ids and eventtypes for that course_id
		$this->db->select('KursID, GruppeID');
		$this->db->from('stundenplankurs');
		$this->db->where('VeranstaltungsformID', 2);
		$this->db->or_where('VeranstaltungsformID', 3);
		$this->db->or_where('VeranstaltungsformID', 4);
		$q = $this->db->get();

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				// if the id to search for matches
				if($row->KursID == $id){
					$data[] = $row;
				}
			}
		}
		
		// switch status to save depending on passed flag
		if($enable){
			$status = 1;
		}
        else {
			$status = 0;
		}
		
		// run through all found group-ids and activate or deactivate the registration depending on the status
        // flag
		foreach($data as $d){
			$this->db->where('GruppeID', $d->GruppeID);
			$this->db->update('gruppe', array('Anmeldung_zulassen' => $status));
		}
		
	}
	
	/**
	 * Checks if application for the given course is already enabled or not.
	 * 
	 * @param int $course_id ID of the course, where the application-status should be checked for
	 * @return int Flag that shows if activation is enabled (1) or not (2);
	 *             returns -1 if there is no course to enable.
	 */
	public function get_application_status($course_id){
		$data = array(); // init
		$q = ''; // init
		
		// find all group_ids and eventtypes for that course_id
		$this->db->select('a.KursID, a.GruppeID, b.Anmeldung_zulassen');
		$this->db->from('stundenplankurs as a');
		$this->db->join('gruppe as b', 'a.GruppeID = b.GruppeID');
		$this->db->where('VeranstaltungsformID', 2);
		$this->db->or_where('VeranstaltungsformID', 3);
		$this->db->or_where('VeranstaltungsformID', 4);
		$q = $this->db->get();

		// if there are results, get status
		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				// if the id to search for matches
				if($row->KursID == $course_id){
					$data[] = $row;
				}
			}
		// otherwise there are no courses to set status for
		} else {
			return -1;
		}
		
		// return status
		if($data[0]->Anmeldung_zulassen == 1){
			return 1;
		} else {
			return 2;
		}
		
	}
	

	/**
	 * Searches an student by his matrikelnummer.
     *
     * @access public
	 * @param int $matrno Matrikelnummer, where the user should be searched vor
	 * @return array holding user-data as an object (vorname, Nachname, Matrikelnummer, BenutzerID)
     *               OR -1 if the user already has got the role.
	 */
	public function search_student_by_matrno($matrno){
		$q = ''; // init
		$data = array(); // init
		
		$this->db->select('Vorname, Nachname, Matrikelnummer, BenutzerID');
		$this->db->from('benutzer');
		$this->db->where('Matrikelnummer', $matrno);
		$q = $this->db->get();

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
		
			// check if matrno isn't already assigned as tutor
			$this->db->select('*');
			$this->db->where('BenutzerID', $row->BenutzerID);
			$this->db->where('RolleID', 4); // tutor-role-id = 4
			$this->db->from('benutzer_mm_rolle');
			$q = $this->db->get();

			if($q->num_rows() == 1){
				return -1;
			}
		}
		
		return $data;
	}

	/**
	 * Assigns the tutor role to an given student.
	 *
     * @access public
     * @param array $student_data [0] => matrno; [1] => courseId
	 * @return boolean TRUE if the process is finished without errors, otherwise FALSE
	 */
	public function assign_tut_role_to_student($student_data, $assign_id){
		$q = ''; // init

		// find user_id for that matrno
		$this->db->select('BenutzerID');
		$this->db->where('Matrikelnummer', $student_data[0]);
		$this->db->from('benutzer');
		$q = $this->db->get();
		
		if($q->num_rows() == 1){
			$user_id = '';
			foreach ($q->result_array() as $row) {
				$user_id = $row['BenutzerID'];
			}
			
			// check if matrno isn't already assigned as tutor
			$this->db->select('*');
			$this->db->where('BenutzerID', $user_id);
			$this->db->from('benutzer_mm_rolle');
			$q = $this->db->get();
						
			// assign tut-role to that user_id - rolle 4=tutor
			$this->db->insert('benutzer_mm_rolle', array('BenutzerID' => $user_id, 'RolleID' => '4'));
			
			// log assign-activity
			$this->helper_model->log_activities(6, $assign_id, $user_id);

			// add course to kurstutor-table
			$this->db->insert('kurstutor', array('BenutzerID' => $user_id, 'KursID' => $student_data[1]));
			
			return true;
		}
        else {
			return false;
		}
	}

    /**
     * Returns the user information (Vorname, Nachname, Email) for the course advisers (dozent, adviser, tutor(s)),
     * that are assigned to the given course. The select statement will be generated depending on the
     * parameter 'adviser_type'. The following input is allowed for the parameter:
     *      - 'dozent'  -> returns the assigned dozent
     *      - 'tutor'   -> all assigned tutors will be returned
     *      - 'advisor' -> all assigned advisers will be returned
     *
     * @access public
     * @param int $course_id ID of the course, where the dozent(s) should be selected for
     * @param string $advisor_type The type of advisor that should be selected from the database. The following
     *               options will be considered: 'dozent', 'advisor', 'tutor'
     * @return array Simple 2-dimensional array with all user and their corresponding information.
     *               Structure: [][attributes] (in the first dimension there are all dozents saved and in the
     *               second dimension the single attributes like Nachname, Vorname, Email).
     */
    public function get_assigned_adviser_information_for_single_course($course_id, $advisor_type){
        $this->db->distinct();
        $this->db->select('b.Vorname, b.Nachname, b.Email');
        $this->db->from('stundenplankurs as a, benutzer as b');

        // modify the statement depending on the advisor_flag that is passed
        if ($advisor_type == 'dozent'){
            $this->db->where('b.BenutzerID = a.DozentID');
            // the smallest VeranstaltungsformID should be the first one
            $this->db->order_by('a.VeranstaltungsformID','asc');
            // limit the result, because there could be only one 'dozent' for one course.
            // usually it is the person with that belongs to the course with the "smallest" VeranstaltungsformID
            $this->db->limit(1);
            $this->db->where('a.KursID', $course_id);
            $this->db->where_in('a.VeranstaltungsformID', array(1,3,5));
        }
        else if($advisor_type == 'advisor'){
            $this->db->join('kursbetreuer as c', 'c.BenutzerID = b.BenutzerID');
        }
        else if($advisor_type == 'tutor'){
            $this->db->join('kurstutor as d', 'd.BenutzerID = b.BenutzerID' );
            $this->db->where('d.KursID', $course_id);
        }


        $query = $this->db->get();

        $result_array = array(); // init of the result array
        // generate the query result
        if($query->num_rows() > 0){ // check that there is really only 1 result
            // construct the array to return
            foreach($query->result_array() as $row){
                $result_array[] = $row;
            }
        }

        return $result_array;
    }

    /*
     * ==================================================================================
     *                                   Course administration end
     * ==================================================================================
     */
	
	
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

	
	/**
	 * Updating all checkboxes in lab-notes-view.
	 * Each checkbox is updated separately.
	 * Because data is stored as string of 1 and 0 in one field,
	 * old data has to be fetched before and updated at the desired position.
	 * 
	 * 
	 * @param string $cb_status 
	 * @param int $user_id user to store the data for
	 * @param string $attr attribute in table to store data to
	 * @param int $event_id the event to store the data for - only for testat and presence
	 */
	public function update_group_cbs($cb_status, $user_id, $attr, $event_id = '-1'){
		// event-checkbox changed
		if($event_id != -1){
			$q = '';
			
			// get current status
			$this->db->select($attr);
			$this->db->where('BenutzerID', $user_id);
			$q = $this->db->get('gruppenteilnehmer_aufzeichnungen');
			
			if($q->num_rows() > 0){
				foreach($q->result() as $row){
					$current_status = $row->$attr;
				}
			}
			
			// get length of string in db - needed in for-loop
			$length = strlen($current_status);
			// new variable, that represents the new status
			$new_status = '';
			
//			print_r($cb_status);
//			echo '--';
			
			// generate new string that reprensents the new status
			// find position that has been changed and append to string
			for($i = 0; $i < $length; $i++){
				// replace value for changed event with new status
				if($i == $event_id){
					$new_status .= $cb_status;
				// otherwise take the value that was stored before
				} else {
					$new_status .= substr($current_status, $i, 1);
				}
			}
			
//			print_r($current_status);
//			echo '--';
//			print_r($new_status);

			$save = array(
				$attr => $new_status
			);
		} else {
			$save = array(
				$attr => $cb_status
			);
		}
		
		// save to gruppenteilnehmer_aufzeichnungen for that user
		$this->db->where('BenutzerID', $user_id);
		$this->db->update('gruppenteilnehmer_aufzeichnungen', $save);
	}
	
	
	/**
	 * Method to update user-notes in gruppenteilnehmer_aufzeichnungen
	 * 
	 * @param int $user_id
	 * @param string $user_notes
	 */
	public function update_group_notes($user_id, $user_notes){
		$save = array(
			'notizen' => $user_notes
		);
		
		// save to gruppenteilnehmer_aufzeichnungen for that user
		$this->db->where('BenutzerID', $user_id);
		$this->db->update('gruppenteilnehmer_aufzeichnungen', $save);
	}
	
	
	/**
	 * Helper methode to get the notes stored for a passed user-id
	 * @param int $user_id user-id
	 * @return string notes that has been stored for that user
	 */
	public function get_participant_notes($user_id){
		$user_notes = ''; // init
		$q = '';
		
		// get current notes
		$this->db->select('notizen');
		$this->db->where('BenutzerID', $user_id);
		$q = $this->db->get('gruppenteilnehmer_aufzeichnungen');

		if($q->num_rows() > 0){
			foreach($q->result() as $row){
				$user_notes = $row->notizen;
			}
		}
		
		return $user_notes;
	}
	
	
	/**
	 * Update date for the passe event.
	 * 
	 * @param int $sp_course_id sp_course_id to save new event-date for
	 * @param int $event_id event that should be updated
	 * @param date $date the new date
	 */
	public function update_eventdate($sp_course_id, $event_id, $date){
		$group_id = '';
		$collumn = '';

		// get group_id for sp_course_id
		$group_id = $this->_get_group_id_for_spkursid($sp_course_id);
		
		// prepare collumn-name
		if(($event_id - 9) <= 0){
			$collumn = 'termin0'.$event_id;
		} else {
			$collumn = 'termin'.$event_id;
		}
		
		$save = array(
			$collumn => $date
		);
		
		// save to gruppenteilnehmer_aufzeichnungen for that user
		$this->db->where('GruppeID', $group_id->GruppeID);
		$this->db->update('gruppentermin', $save);
	}
	
	
	public function update_xtra_event($sp_course_id, $text1, $text2, $number_of_events){
		$group_id = '';
		$collumn = '';

		// get group_id for sp_course_id
		$group_id = $this->_get_group_id_for_spkursid($sp_course_id);
		
		$save = array(
			'zeigezwischentestat1' => $text1,
			'zeigezwischentestat2' => $text2,
			'anzahltermine' => $number_of_events > 20 ? 20 : $number_of_events
		);
		
		// save to gruppenteilnehmer_aufzeichnungen for that user
		$this->db->where('GruppeID', $group_id->GruppeID);
		$this->db->update('gruppentermin', $save);
		
	}

	/* 
	 * 
	 * ********************************************* lab-mgt
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
}
?>