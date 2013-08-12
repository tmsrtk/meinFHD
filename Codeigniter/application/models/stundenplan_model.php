<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Stundenplan
 *
 * The Stundenplan-Model provides all necessary db operations, that are required for the
 * displaying the timetable.
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Simon vom Eyser (SVE), <simon.vomeyser@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Stundenplan_Model extends CI_Model {

	/**
	 * Constructs and returns an empty array which will contain the times
	 *
     * @access private
	 * @return array structure of the times table
	 */	
	private function _create_times_array(){
		$query_times = $this->db->query("SELECT StundeID, Beginn, Ende FROM stunde");
		$times = $query_times->result_array();

		return $times;
	}

	/**
	 * Constructs and returns an empty array which will contain the days and their date.
	 * The date depends on the actual day and is calculated for monday in this week.
	 *
     * @access private
	 * @return array Days in a array with their date
	 */	
	private function _create_days_array(){
		// create row array containing the names of the days
		$query_days = $this->db->query("SELECT TagName FROM tag");
		$days = $query_days->result_array();

		// clear Saturday and Sunday
		unset($days[5]);
		unset($days[6]);

		// add the belonging date to the specific day

		// contains 1 if it's Monday, 2 if Tuesday..
		$actual_day = date('w');

		// for every day past since monday, substract the time for 24 hours (86400 Sek)
		$time_since_monday = 0;

		while ($actual_day > 1){
			$time_since_monday = $time_since_monday + 86400;
			$actual_day--;
		}
		
		// bugfix / workaround, if it's Sunday, it's the day before monday, so the past time is negative
		if ($actual_day == 0){
			$time_since_monday = -86400;
        }

		$date_monday = date('d.m.Y', time() - $time_since_monday);

		// add to the row array the specific date counting from Monday to Friday
		$actual_date = $date_monday;
		
		// we need to exclude Saturday and Sunday.
		// PHP day numbers start at 0 for Sunday, so 6 is for Saturday
		$valid_days = array(1, 2, 3, 4, 5);
		
		// reset Variable for actual day (needed to markup the day in Array)
		$day_number = date('w');
		$actual_day = (in_array($day_number, $valid_days)) ? $day_number : 1;

		$day_in_loop = 1;
		
		foreach ($days as $key => $value){
			$days[$key]["Datum"] = $actual_date;
			$time_since_monday = $time_since_monday - 86400;

			$actual_date = date('d.m.Y', time() - $time_since_monday);

			$days[$key]["IstHeute"] = 0;

			if ($day_in_loop == $actual_day){
				$days[$key]["IstHeute"] = 1;
			}

			$day_in_loop++;
		}

		// bugfix / workaround, if it is weekend show the next monday
		if (($actual_day == 0) or ($actual_day == 6)) {
			$days[0]["IstHeute"] = 1;
		}

		return $days;
	}

	/**
	 * Constructs and returns an empty 2D array which will contain the whole timetable.
	 *
     * @access private
	 * @return array Empty 2D array which will contain the timetable
	 */
	private function _create_timetable_array(){
		$query_days = $this->db->query("SELECT TagName FROM tag");
		$days = $query_days->result_array();

		$query_stunden = $this->db->query("SELECT StundeID FROM stunde");
		$stunden = $query_stunden->result_array();

		$stundenplan = array();

		foreach ($days as $tag){
			foreach ($stunden as $stunde) {
			$stundenplan[$tag['TagName']][$stunde['StundeID']] = array();
			}
		}

		// erase saturday and sunday
		unset($stundenplan['Samstag']);
		unset($stundenplan['Sonntag']);

		return $stundenplan;
	}

	/**
	 * Returns all courses in an complex query.
	 * (For documentation of query look inside the function)
	 *
     * @access private
	 * @return row-array List of all courses in this semster linked to the USER-ID
	 */	
	private function _get_courses_student(){
		$id = $this->user_model->get_userid();

        /*
		 benutzerkurs: Enthaeltlt die aktiven Module
		 studiengangkurs: Die Namen im Klartext (Nur deswegen in Query)
		 stundenplankurs: Genauere Informationen zu Kursen(Startzeit, Typ)
		 veranstaltungsform: Die Namen der Veranstaltungsformen im Klartext
		 Tag: Um TagIds von timetablekurs in Klartext darzustellen, wichtig
		 Stunde: Doppelt verwendet fuer Startzeitpunkt und Endzeitpunkt des Kurses
		 Dozent: Fuer DozentID im Klartext benutzer noch einmal
		 Gruppe: Enthaelt maximale Anzahl, ob Anmeldung freigeschaltet ist
		 Gruppenteilnehmer: mit count(*) als Subquery
		 Sollen kein WPF sein! (Feature not supported yet)
		*/
        $query = $this->db->query("
            SELECT
                sg.Kursname, sg.kurs_kurz,
                v.VeranstaltungsformName, v.VeranstaltungsformID,
                sp.VeranstaltungsformAlternative, sp.DozentID, sp.StartID, sp.EndeID, (sp.EndeID-sp.StartID)+1 AS 'Dauer', sp.GruppeID, sp.Farbe, sp.Raum,
                d.Vorname AS 'DozentVorname', d.Nachname AS 'DozentNachname', d.Email AS 'DozentEmail',
                t.TagName,t.TagID,
                s_beginn.Beginn, s_ende.Ende,
                b.Aktiv,
                b.KursID,b.SPKursID,
                g.TeilnehmerMax, g.Anmeldung_zulassen,
                (SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.GruppeID = sp.GruppeID) AS 'Anzahl Teilnehmer',
                sempla.SemesterplanID,
                sk.KursHoeren
            FROM
                benutzerkurs b,
                studiengangkurs sg,
                stundenplankurs sp,
                veranstaltungsform v,
                tag t,
                stunde s_beginn, stunde s_ende,
                benutzer d,
                gruppe g,
                semesterplan sempla,
                semesterkurs sk
            WHERE
                b.kursID = sg.kursID AND
                sp.kursID = b.KursID AND
                sp.SPKursID = b.SPKursID AND
                v.veranstaltungsformID = sp.veranstaltungsformID AND
                s_beginn.StundeID = sp.StartID AND
                s_ende.StundeID = sp.EndeID AND
                t.TagID = sp.TagID AND
                sp.DozentID = d.BenutzerID AND
                b.BenutzerID = ".$id." AND
                b.SemesterID = ".$this->adminhelper->get_act_semester($this->user_model->get_studienbeginn_semestertyp() , $this->user_model->get_studienbeginn_jahr() )." AND
                sp.GruppeID = g.GruppeID AND
                sp.IsWPF = 0 AND
                sempla.BenutzerID = ".$id." AND
                sk.SemesterplanID = sempla.SemesterplanID AND
                sk.KursID = b.KursID AND
                sk.Semester = ".$this->adminhelper->get_act_semester($this->user_model->get_studienbeginn_semestertyp() , $this->user_model->get_studienbeginn_jahr() )."
            ORDER BY
                sp.tagID, sp.StartID
		");
		
		$result = $query->result_array();

		return $result;
	}

    /**
     * Returns all courses, where the currently authenticated user has been assigned as an dozent.
     *
     * @access private
     * @return array Query result array with all courses, that match the query.
     */
    private function _get_courses_dozent(){
		$id = $this->user_model->get_userid();
		$semestertyp = $this->adminhelper->getSemesterTyp();

		$this->db
				    ->select('
						sgk.Kursname, sgk.kurs_kurz, sgk.KursID, 
						spk.SPKursID, spk.Raum, spk.VeranstaltungsformAlternative, spk.DozentID, spk.StartID, spk.EndeID, (spk.EndeID-spk.StartID)+1 AS Dauer, spk.GruppeID, spk.Farbe,
						vf.VeranstaltungsformName, vf.VeranstaltungsformID,
						ben.Vorname AS DozentVorname, ben.Nachname AS DozentNachname, ben.Email as DozentEmail,
						tag.TagName, tag.TagID,
						s_beginn.Beginn, s_ende.Ende,
						gruppe.TeilnehmerMax, gruppe.Anmeldung_zulassen,
						(SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.GruppeID = spk.GruppeID) AS "Anzahl Teilnehmer"
						')
					->from('
						studiengangkurs sgk, 
						stundenplankurs spk,
						veranstaltungsform vf,
						benutzer ben,
						tag tag,
						stunde s_beginn, stunde s_ende,
						gruppe gruppe
						')
					->where('sgk.KursID = spk.KursID 
						AND vf.VeranstaltungsformID = spk.VeranstaltungsformID
						AND ben.BenutzerID = spk.DozentID
						AND ben.BenutzerID = '.$id.'
						AND tag.TagID = spk.TagID
						AND s_beginn.StundeID = spk.StartID
						AND s_ende.StundeID = spk.EndeID
						AND gruppe.GruppeID = spk.GruppeID
						AND spk.IsWPF = 0
						')
					;

		$where_in = array();

		if ($semestertyp == 'WS'){
			$where_in = array('1', '3', '5', '7', '9', '11', '13', '15', '17', '19');
		}
		else{
			$where_in = array('2', '4', '6', '8', '10', '12', '14', '16', '18', '20');
		}

		$this->db->where_in('sgk.Semester', $where_in);

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}

    /**
     * Returns all courses, where the currently authenticated user has been assigned as an tutor.
     *
     * @access private
     * @return array Result array, with all courses that match the query.
     */
    private function _get_courses_tutor(){
        $id = $this->user_model->get_userid();
        $semestertyp = $this->adminhelper->getSemesterTyp();

        $this->db
                ->select('sgk.Kursname, sgk.kurs_kurz, sgk.KursID,
						  spk.SPKursID, spk.Raum, spk.VeranstaltungsformAlternative, spk.DozentID, spk.StartID, spk.EndeID, (spk.EndeID-spk.StartID)+1 AS Dauer, spk.GruppeID, spk.Farbe,
						  vf.VeranstaltungsformName, vf.VeranstaltungsformID,
					 	  ben.Vorname AS TutorVorname, ben.Nachname AS TutorNachname, ben.Email as TutorEmail,
						  tag.TagName, tag.TagID,
						  s_beginn.Beginn, s_ende.Ende,
						  gruppe.TeilnehmerMax, gruppe.Anmeldung_zulassen,
						  (SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.GruppeID = spk.GruppeID) AS "Anzahl Teilnehmer"
						')
                ->from('studiengangkurs sgk,
						stundenplankurs spk,
						veranstaltungsform vf,
						benutzer ben,
						tag tag,
						stunde s_beginn, stunde s_ende,
						gruppe gruppe,
						kurstutor kt
						')
                ->where('sgk.KursID = spk.KursID
						 AND vf.VeranstaltungsformID = spk.VeranstaltungsformID
						 AND ben.BenutzerID = kt.BenutzerID
						 AND kt.BenutzerID = '.$id.'
						 AND spk.KursID = kt.KursID
						 AND tag.TagID = spk.TagID
						 AND s_beginn.StundeID = spk.StartID
						 AND s_ende.StundeID = spk.EndeID
						 AND gruppe.GruppeID = spk.GruppeID
						 AND spk.IsWPF = 0
						')
        ;

        $this->db->where_in('spk.VeranstaltungsformID', array('4', '6'));

        $where_in = array();

        if ($semestertyp == 'WS'){
            $where_in = array('1', '3', '5', '7', '9', '11', '13', '15', '17', '19');
        }
        else{
            $where_in = array('2', '4', '6', '8', '10', '12', '14', '16', '18', '20');
        }

        $this->db->where_in('sgk.Semester', $where_in);

        $query = $this->db->get();

        $result = $query->result_array();

        return $result;
	}

    /**
     * Returns all courses, where the currently authenticated user has been assigned as an advisor.
     *
     * @access private
     * @return array Result array, with all courses that match the query.
     */
    private function _get_courses_advisor(){

        $id = $this->user_model->get_userid();
        $semestertyp = $this->adminhelper->getSemesterTyp();

        $this->db
            ->select('sgk.Kursname, sgk.kurs_kurz, sgk.KursID,
					  spk.SPKursID, spk.Raum, spk.VeranstaltungsformAlternative, spk.DozentID, spk.StartID, spk.EndeID, (spk.EndeID-spk.StartID)+1 AS Dauer, spk.GruppeID, spk.Farbe,
					  vf.VeranstaltungsformName, vf.VeranstaltungsformID,
				      ben.Vorname AS TutorVorname, ben.Nachname AS TutorNachname, ben.Email as TutorEmail,
					  tag.TagName, tag.TagID,
					  s_beginn.Beginn, s_ende.Ende,
					  gruppe.TeilnehmerMax, gruppe.Anmeldung_zulassen,
				      (SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.GruppeID = spk.GruppeID) AS "Anzahl Teilnehmer"
					')
            ->from('studiengangkurs sgk,
					stundenplankurs spk,
					veranstaltungsform vf,
					benutzer ben,
					tag tag,
					stunde s_beginn, stunde s_ende,
					gruppe gruppe,
					kursbetreuer kb
				')
            ->where('sgk.KursID = spk.KursID
					 AND vf.VeranstaltungsformID = spk.VeranstaltungsformID
					 AND ben.BenutzerID = kb.BenutzerID
					 AND kb.BenutzerID = '.$id.'
					 AND spk.KursID = kb.KursID
					 AND tag.TagID = spk.TagID
					 AND s_beginn.StundeID = spk.StartID
					 AND s_ende.StundeID = spk.EndeID
					 AND gruppe.GruppeID = spk.GruppeID
					 AND spk.IsWPF = 0
					 AND spk.VeranstaltungsformID = 4
					')
        ;

        $where_in = array();

        if ($semestertyp == 'WS'){
            $where_in = array('1', '3', '5', '7', '9', '11', '13', '15', '17', '19');
        }
        else{
            $where_in = array('2', '4', '6', '8', '10', '12', '14', '16', '18', '20');
        }

        $this->db->where_in('sgk.Semester', $where_in);

        $query = $this->db->get();

        $result = $query->result_array();

        return $result;
    }

	/**
	 * Function sets the courses in the timetable active, if they should be displayed.
	 * The "aktiv"-Flag in the database needs to be set manually in certain courses:
	 * 1. If there is no alternative course
	 *
     * @access private
	 * @param array $courses Array with all courses, that should be activated.
	 * @return array Array with all courses that are now activated.
	 */
	private function _set_active($courses){

		foreach ($courses as $index =>$course) {
			if (!$course['VeranstaltungsformAlternative']) {
				$courses[$index]['Aktiv'] = "1";
			}
		}

		return $courses;
	}

	/**
	 * Important Function for displaying events in week-view. Adds the display flag to the courses, so that they
     * will be shown in the view.
	 * Depending on their alternative and if the user is already enrolled, a flag named "Anzeigen" is added.
	 *
     * @access private
     * @param array $courses Array with all courses, where the display flag should be added.
     * @return array The array where the flags have been modified.
	 */
	private function _add_displayflag($courses){
		// run through array
		foreach ($courses as $key => $course) {

			// if KursHoeren was deactivated in the "Studienplan" and course is a Vorlesung, do not show
			if ($course["KursHoeren"] == 0){
				$courses[$key]["Anzeigen"] = 0;
			}

			// when there is no alternative
            else if ($course["VeranstaltungsformAlternative"] == ""){
				$courses[$key]["Anzeigen"] = 1;
			}  
			// when there is an alternative
			else{
				if ($course["Aktiv"] == 1){
					$courses[$key]["Anzeigen"] = 1;
				}
				else{ // Aktiv == 0

					// Flag to be altered when there is a active one
					$user_enrolled = false;

					foreach ($courses as $ikey => $icourse){
						// Run through array again, if there is an course with Aktiv flag and same
						// vernastaltungsformID and same KursID set user_enrolled = true
						if ($icourse["VeranstaltungsformID"] == $course["VeranstaltungsformID"]  &&
							$icourse["KursID"] == $course["KursID"]  &&
							$icourse["Aktiv"] == 1){
							$user_enrolled = true;
						}	

						// if user is somwhere else enrolled, the actual curse should not be displayed
						if ($user_enrolled){
							$courses[$key]["Anzeigen"] = 0;
                        }
						else{
							$courses[$key]["Anzeigen"] = 1;
                        }

					}// end inner foreach
				}
			}// end else there is alternative
		}// end outer foreach

		return $courses;
	}

    /**
     * Adds the display flag to all dozent, advisors, tutors courses, that are part of the array that is passed as an parameter.
     *
     * @access private
     * @param $courses
     * @return array The modified input array
     */
    private function _add_displayflag_dozent_advisor_tutor($courses){
		// run through array
		foreach ($courses as $key => $course) {

			$courses[$key]["Anzeigen"] = 1;

		}//end outer foreach

		return $courses;
	}

	/**
	 * Sorts courses into the timetable-array-structure
	 *
     * @access private
	 * @param array $coures Array of courses(already set active!!)
     * @param array $timetable Empty timetable array, where the courses should be placed in.
	 * @return array The filled timetable
	 */
	private function _courses_into_timetable($courses, $timetable){
		// insert courses in the empty timetable array via for-each
		foreach ($timetable as $TagName => $TagInhalt) {
			foreach ($TagInhalt as $StundeID => $StundeInhalt) {
				// For every course in $courses check, if it is at that specific day/hour
				foreach ($courses as $course) {
					if ($course['TagName'] == $TagName && $course['StartID'] == $StundeID) {
							array_push($timetable[$TagName][$StundeID], $course);
					}
				}
			}
		}
		return $timetable;
	}

	/**
     * Returns the students timetable with some other arrays.
	 * Central function, returns various arrays, the most important one is the "stundenplan"-Array (found under index [0])
	 * The Array is 3-dimensional.
	 * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
	 * then an Array for all courses in this hour. 
	 * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
	 * @access public
	 * @return array Three dimensional array structured like mentioned above
	 */	
	public function get_stundenplan(){
		// query all (student) courses from the database
		$courses = $this->_get_courses_student();

		// control active-flag of courses, change if necsassary(see function-doc)
		$courses = $this->_set_active($courses);

		// add display flag(see function-doc)
		$courses = $this->_add_displayflag($courses);

		// create empty structure of timetable
		$timetable = $this->_create_timetable_array();

		// sort courses into timetable-array-structure
		$stundenplan = $this->_courses_into_timetable($courses, $timetable);

		// assemble the return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by numbers, their actual date
		$days = $this->_create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by numbers (not actually required )
		$times = $this->_create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by numbers, ordered by day and hour
		array_push($return, $courses);
		
		return $return;
	}

    /**
     * Returns the combined timetable with events of all roles the currently authenticated user
     * is assigned to.
     * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
     * then an Array for all courses in this hour.
     * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
     *
     * @access public
     * @return array 3-dimensional array with the timetable of all roles the user is assigned to.
     */
    public function get_stundenplan_for_all_roles(){

        // get the student courses and prepare them
        $student_courses = $this->_get_courses_student();
        // control active-flag of courses, change if necessary(see function-doc)
        $student_courses = $this->_set_active($student_courses);
        // add display flag (see function-doc)
        $student_courses = $this->_add_displayflag($student_courses);

        // get the tutor courses and prepare them
        $tutor_courses = $this->_get_courses_tutor();
        // add display flag(see function-doc)
        $tutor_courses = $this->_add_displayflag_dozent_advisor_tutor($tutor_courses);

        // get the advisor courses and prepare them
        $advisor_courses = $this->_get_courses_advisor();
        // add display flag(see function-doc)
        $advisor_courses = $this->_add_displayflag_dozent_advisor_tutor($advisor_courses);

        // get the dozent courses and prepare them
        $dozent_courses = $this->_get_courses_dozent();
        // add display flag(see function-doc)
        $dozent_courses = $this->_add_displayflag_dozent_advisor_tutor($dozent_courses);

        // create empty structure of timetable
        $stundenplan = $this->_create_timetable_array();

        // sort the different courses into the timetable-array-structure
        $stundenplan = $this->_courses_into_timetable($student_courses, $stundenplan);
        $stundenplan = $this->_courses_into_timetable($tutor_courses, $stundenplan);
        $stundenplan = $this->_courses_into_timetable($advisor_courses, $stundenplan);
        $stundenplan = $this->_courses_into_timetable($dozent_courses, $stundenplan);

        // free unused varaibles
        unset($student_courses);
        unset($tutor_courses);
        unset($advisor_courses);
        //unset($dozent_courses);

        // assemble the return-array
        $return = array();

        // [0] : The actual timetable
        array_push($return, $stundenplan);
        unset($stundenplan);

        // [1] : The days, indexed by numbers, their actual date
        $days = $this->_create_days_array();
        array_push($return, $days);
        unset($days);

        //[2] : The times, indexed by numbers (Not requiered actually)
        $times = $this->_create_times_array();
        array_push($return, $times);
        unset($times);

        //[3] : The courses in a list, indexed by numbers, ordered by day and hour
        array_push($return, $dozent_courses);
        //unset($combined_courses);

        return $return;
    }

	/**
	 * Returns the students timetable in an 3-dimensional array.
     * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
     * then an Array for all courses in this hour.
     * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
     * @access public
	 * @return array 3-dimensional array with the student's timetable.
	 */
	public function get_stundenplan_student(){
		return $this->get_stundenplan();
	}

	/**
	 * Returns the dozents timetable for the currently authenticated user.
	 * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
     * then an Array for all courses in this hour.
     * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
     * @access public
     * @return array multi-dimensional array with dozent's stundenplan.
	 */
	public function get_stundenplan_dozent(){
		// query all courses from the database
		$courses = $this->_get_courses_dozent();

		// add display flag(see function-doc)
		$courses = $this->_add_displayflag_dozent_advisor_tutor($courses);

		// create empty structure of timetable
		$timetable = $this->_create_timetable_array();

		// sort courses into timetable-array-structure
		$stundenplan = $this->_courses_into_timetable($courses, $timetable);

		// assemble the return-array
		$return = array();

		// [0] : The actual timetable
		array_push($return, $stundenplan);

		// [1] : The days, indexed by numbers, their actual date
		$days = $this->_create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by numbers (Not requiered actually)
		$times = $this->_create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by numbers, ordered by day and hour
		array_push($return, $courses);

		return $return;
	}

	/**
	 * Returns the tutor's timetable for the currently authenticated user (if the user is got an tutor).
     * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
     * then an Array for all courses in this hour.
     * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
     * @access public
     * @return array multi-dimensional array with a student's timetable.
	 */
	public function get_stundenplan_tutor(){
		// query all courses from the database
		$courses = $this->_get_courses_tutor();

		// add display flag(see function-doc)
		$courses = $this->_add_displayflag_dozent_advisor_tutor($courses);

		// create empty structure of timetable
		$timetable = $this->_create_timetable_array();

		// sort courses into timetable-array-structure
		$stundenplan = $this->_courses_into_timetable($courses, $timetable);

		// assemble return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by Numbers, their actual date
		$days = $this->_create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by Numbers (Not actually required)
		$times = $this->_create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by Numbers, ordered by day and hour
		array_push($return, $courses);

		return $return;
	}

    /**
     * Returns the advisor's timetable for the currently authenticated user (if the user is got an advisor).
     * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
     * then an Array for all courses in this hour.
     * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
     *
     * @access public
     * @return array multi-dimensional array with a advisor's timetable
     */
    public function get_stundenplan_advisor(){

        // query all courses from the database
        $courses = $this->_get_courses_advisor();

        // add display flag(see function-doc)
        $courses = $this->_add_displayflag_dozent_advisor_tutor($courses);

        // create empty structure of timetable
        $timetable = $this->_create_timetable_array();

        // sort courses into timetable-array-structure
        $stundenplan = $this->_courses_into_timetable($courses, $timetable);

        // assemble return-array
        $return = array();

        //[0] : The actual timetable
        array_push($return, $stundenplan);

        //[1] : The days, indexed by Numbers, their actual date
        $days = $this->_create_days_array();
        array_push($return, $days);

        //[2] : The times, indexed by Numbers (Not actually required)
        $times = $this->_create_times_array();
        array_push($return, $times);

        //[3] : The courses in a list, indexed by Numbers, ordered by day and hour
        array_push($return, $courses);

        return $return;

    }
}
/* End of file stundenplan_model.php */
/* Location: ./application/models/stundenplan_model.php */