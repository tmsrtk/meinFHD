<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Simon vom Eyser (SVE), <simon.vomeyser@fh-duesseldorf.de>
 */

/**
 * Stundenplan Model
 */
class Stundenplan_Model extends CI_Model {

	// private $user;//Debug

	
	public function __construct()
	{

	}

	/**
	 * Defines global record of variables, in case additional information about the User is needed.
	 *
	 * Example: $this->user['VorName'];
	 *
	 * @param integer // ID of a User
	 * @return row_array // Information about User
	 */	
	// private function define_user($id)
	// {
	
	// 	$query = $this->db->query("Select * From benutzer Where BenutzerID Like '".$id."' LIMIT 1");
	// 	$this->user = $query->row_array();
	// }

	/**
	 * Function create_times_array
	 *
	 * Constructs and returns an empty array which will contain the times
	 *
	 * @param type name // nicht vorhanden
	 * @return array // structure of times_table
	 */	
	private function create_times_array()
	{
		$query_times = $this->db->query("SELECT Beginn FROM stunde");
		$times = $query_times->result_array();

		return $times;
	}

	/**
	 * Constructs and returns an empty array which will contain the days and their date
	 *
	 * The Date is depending on the actual day and calcualted for Monday in this week.
	 *
	 * @param type name // nicht vorhanden
	 * @return type // Days in a array with their date
	 */	
	private function create_days_array()
	{
		//Create row array containing the names of the days
		$query_days = $this->db->query("SELECT TagName FROM tag");
		$days = $query_days->result_array();

		//Clear Saturday and Sunday
		unset( $days[5]);
		unset( $days[6]);


		//Add the belonging date to the specific day 

		//Contains 1 if it's Monday, 2 if Tuesday..
		$actual_day = date('w');


		//For every day past since monday, substract the time for 24 hours (86400 Sek)
		$time_since_monday = 0;

		while ($actual_day > 1) {
			$time_since_monday = $time_since_monday + 86400;
			$actual_day--;
		}
		
		//Bugfix, if it's Sunday, it's the day before Monday, so the past time is negative 
		if ($actual_day == 0)
			$time_since_monday = -86400;

		$date_monday = date('d.m.Y', time() - $time_since_monday);

		//Add to the row array the specific date counting from Monday to Friday
		$actual_date = $date_monday;
		
		// We need to exclude Saturday and Sunday.
		// PHP week numbers start at 0 for Sunday, so 6 is for Saturday
		$valid_days = array(1, 2, 3, 4, 5);
		
		//Reset Variable for actual day(Needed to Markup the day in Array)
		$day_number = date('w');
		$actual_day = (in_array($day_number, $valid_days)) ? $day_number : 1;

		$day_in_loop = 1;
		
		foreach ($days as $key => $value)
		{			
			$days[$key]["Datum"] = $actual_date;
			$time_since_monday = $time_since_monday - 86400;

			$actual_date = date('d.m.Y', time() - $time_since_monday);

			$days[$key]["IstHeute"] = 0;

			if ($day_in_loop == $actual_day)
			{
				$days[$key]["IstHeute"] = 1;
			}

			$day_in_loop++;

		}

		//Bugifx, If its Weekend show the next Monday
		if (($actual_day == 0) or ($actual_day == 6)) {
			$days[0]["IstHeute"] = 1;
		}

		return $days;
	}

	/**
	 * Constructs and returns an empty 2D array which will contain the timetable
	 *
	 * @param type name // nicht vorhanden
	 * @return type // empty 2D array which will contain the timetable
	 */
	private function create_timetable_array()
	{
		$query_days = $this->db->query("SELECT TagName FROM tag");
		$days = $query_days->result_array();

		$query_stunden = $this->db->query("SELECT StundeID FROM stunde");
		$stunden = $query_stunden->result_array();

		$stundenplan = array();

		foreach ($days as $tag) 
		{
			foreach ($stunden as $stunde) {
			$stundenplan[$tag['TagName']][$stunde['StundeID']] = array();
			}

		}

		//Erase Saturday and Sunday
		unset( $stundenplan['Samstag']);
		unset( $stundenplan['Sonntag']);

		return $stundenplan;
	}

	/**
	 * Returns all courses in complex query.
	 *	
	 * (For documentation of query look inside of function)
	 *
	 * @param Integer // ID of User
	 * @return row-array // List of all courses in this semster linked to the USER-ID
	 */	
	private function get_courses_student() 
	{
		$id = $this->user_model->get_userid();

		//benutzerkurs: Enthält die aktiven Module
		//studiengangkurs: Die Namen im Klartext (Nur deswegen in Query)
		//stundenplankurs: Genauere Informationen zu Kursen(Startzeit, Typ)
		//veranstaltungsform: Die Namen der Veranstaltungsformen im Klartext
		//Tag: Um TagIds von timetablekurs in Klartext darzustellen, wichtig 
		//Stunde: Doppelt verwendet für Startzeitpunkt und Endzeitpunkt des Kurses
		//Dozent: Für DozentID im Klartext benutzer noch einmal
		//Gruppe: Enthält maximale Anzahl, ob Anmeldung freigeschaltet ist
		//Gruppenteilnehmer: mit count(*) als Subquery 
		//Sollen kein WPF sein! (Feature not supported yet)
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

	private function __get_courses_dozent()
	{
		$id = $this->user_model->get_userid();
		$semestertyp = $this->adminhelper->getSemesterTyp();

		$this->db
					->select('
						sgk.Kursname, sgk.kurs_kurz, sgk.KursID, 
						spk.SPKursID, spk.VeranstaltungsformAlternative, spk.DozentID, spk.StartID, spk.EndeID, (spk.EndeID-spk.StartID)+1 AS Dauer, spk.GruppeID, spk.Farbe,
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

		if ($semestertyp == 'WS')
		{
			$where_in = array('1', '3', '5', '7', '9', '11', '13', '15', '17', '19');
		}
		else
		{
			$where_in = array('2', '4', '6', '8', '10', '12', '14', '16', '18', '20');
		}

		$this->db->where_in('sgk.Semester', $where_in);

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}

	private function __get_courses_tutor()
	{
		$id = $this->user_model->get_userid();
		$semestertyp = $this->adminhelper->getSemesterTyp();

		$this->db
					->select('
						sgk.Kursname, sgk.kurs_kurz, sgk.KursID, 
						spk.SPKursID, spk.VeranstaltungsformAlternative, spk.DozentID, spk.StartID, spk.EndeID, (spk.EndeID-spk.StartID)+1 AS Dauer, spk.GruppeID, spk.Farbe,
						vf.VeranstaltungsformName, vf.VeranstaltungsformID,
						ben.Vorname AS TutorVorname, ben.Nachname AS TutorNachname, ben.Email as TutorEmail,
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
						AND spk.VeranstaltungsformID = 4
						AND spk.IsWPF = 0
						')
					;

		$where_in = array();

		if ($semestertyp == 'WS')
		{
			$where_in = array('1', '3', '5', '7', '9', '11', '13', '15', '17', '19');
		}
		else
		{
			$where_in = array('2', '4', '6', '8', '10', '12', '14', '16', '18', '20');
		}

		$this->db->where_in('sgk.Semester', $where_in);

		$query = $this->db->get();

		$result = $query->result_array();

		return $result;
	}

	/**
	 * Function sets the courses in the Timtable active if they schould be displayed.
	 *
	 * The "aktiv"-Flag in the Database needs to be manually set in certain courses:
	 * 
	 * 1. If there is no alternativ course
	 *
	 * @param type name // Record of courses
	 * @return type // Record of courses, all courses which schould be displayed flagged active
	 */	
	private function set_active($courses)
	{

		foreach ($courses as $index =>$course) {
			if (!$course['VeranstaltungsformAlternative']) {
				$courses[$index]['Aktiv'] = "1";
			}
		}

		return $courses;
	}

	private function set_active_dozent($courses)
	{

		foreach ($courses as $index =>$course) {
			$courses[$index]['Aktiv'] = "1";
		}

		return $courses;
	}


	/**
	 * Important Function for display in week-view.
	 *
	 * Depending on their alternative and if the user is aready enrolled, a flag named "Anzeigen" is added.
	 *
	 */
	private function add_displayflag($courses)
	{
		//Run throug array
		foreach ($courses as $key => $course) {

			// Konstantin Voth
			// if KursHoeren was deactivated in the "Studienplan" and course is a Vorlesung, do not show
			if ($course["KursHoeren"] == 0 )
				// && $course['VeranstaltungsformID'] == 1) // do not show any kind of events of this course // Dahms choise
			{
				$courses[$key]["Anzeigen"] = 0;
			}

			//when there is no alterntive
			elseif ($course["VeranstaltungsformAlternative"] == "") 
			{
				$courses[$key]["Anzeigen"] = 1;
			}  
			//when there is a alternative
			else
			{
				if ($course["Aktiv"] == 1) 
				{
					$courses[$key]["Anzeigen"] = 1;
				}
				else //Aktiv == 0
				{

					//Flag to be altered when there is a active one
					$user_enrolled = false;

					foreach ($courses as $ikey => $icourse) {
						//Run through array again, if there is an course with Aktiv flag and same
						//vernastaltungsformID and same KursID set user_enrolled = true
						if ($icourse["VeranstaltungsformID"] == $course["VeranstaltungsformID"]  and
							$icourse["KursID"] == $course["KursID"]  and
							$icourse["Aktiv"] == 1) 
						{
							$user_enrolled = true;
						}	

						//if user is somwhere else enrolled, the actual curse should not be displayed
						if ($user_enrolled)
							$courses[$key]["Anzeigen"] = 0;
						else	
							$courses[$key]["Anzeigen"] = 1;


					}//End inner foreach

				}
				
			}//End else there is alternative

		}//End outer foreach 

		return $courses;
	}


	private function __add_displayflag_dozent($courses)
	{
		//Run throug array
		foreach ($courses as $key => $course) {

			$courses[$key]["Anzeigen"] = 1;

		}//End outer foreach 

		return $courses;
	}

	/**
	 * Sort courses into timetable-array-structure
	 *
	 * @param type name // Record of courses(already set active!!), empty timetabe array
	 * @return type // filled timetable
	 */
	private function courses_into_timetable($courses, $timetable)
	{

		//insert courses in the empty timetable array via for-each
		foreach ($timetable as $TagName => $TagInhalt) {
			foreach ($TagInhalt as $StundeID => $StundeInhalt) {
				
				//For every course in $courses check, if it is at that specific day/hour
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
	 * Central function, returns various arrays, the most important one is the "stundenplan"-Array (found under index [0])
	 * The Array is 3-Demensional.
	 * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
	 * then an Array for all courses in this hour. 
	 * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
	 * @param integer // The ID of a User
	 * @return array // Various arrays
	 */	
	public function get_stundenplan()
	{
		//Set global variable in this class for informtion concering the User.
		// $this->define_user($id);
		

		//Query all courses from Database
		$courses = $this->get_courses_student();

		//Control active-flag of courses, change if necsassary(see function-doc)
		$courses = $this->set_active($courses);

		//Add display flag(see function-doc)
		$courses = $this->add_displayflag($courses);


		//Create empty structure of timetable
		$timetable = $this->create_timetable_array();

		//Sort courses into timetable-array-structure
		$stundenplan = $this->courses_into_timetable($courses, $timetable);


		//Assemble return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by Numbers, their actual date
		$days = $this->create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by Numbers (Not requiered actually)
		$times = $this->create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by Numbers, ordered by day and hour
		array_push($return, $courses);

		
		
		return $return;
	}


	/**
	 * Returns students Stundenplan
	 *
	 * @return mixed multi-dim array with student's stundenplan.
	 */
	public function get_stundenplan_student()
	{
		return $this->get_stundenplan();
	}

	/**
	 * Returns dozents Stundenplan.
	 *
	 * @return mixed multi-dim array with dozent's stundenplan.
	 */
	public function get_stundenplan_dozent()
	{
		//Query all courses from Database
		$courses = $this->__get_courses_dozent();

		// FB::log($courses); return;

		//Control active-flag of courses, change if necsassary(see function-doc)
		// $courses = $this->set_active_dozent($courses);

		// //Add display flag(see function-doc)
		$courses = $this->__add_displayflag_dozent($courses);


		//Create empty structure of timetable
		$timetable = $this->create_timetable_array();

		//Sort courses into timetable-array-structure
		$stundenplan = $this->courses_into_timetable($courses, $timetable);


		//Assemble return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by Numbers, their actual date
		$days = $this->create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by Numbers (Not requiered actually)
		$times = $this->create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by Numbers, ordered by day and hour
		array_push($return, $courses);

		
		
		return $return;
	}

	/**
	 * Returns a tutor's Stundenplan.
	 *
	 * @return mixed multi-dim array with a student's stundenplan.
	 */
	public function get_stundenplan_tutor()
	{
		//Query all courses from Database
		$courses = $this->__get_courses_tutor();

		// FB::log($courses); return;

		//Control active-flag of courses, change if necsassary(see function-doc)
		// $courses = $this->set_active_dozent($courses);

		// //Add display flag(see function-doc)
		$courses = $this->__add_displayflag_dozent($courses);


		//Create empty structure of timetable
		$timetable = $this->create_timetable_array();

		//Sort courses into timetable-array-structure
		$stundenplan = $this->courses_into_timetable($courses, $timetable);


		//Assemble return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by Numbers, their actual date
		$days = $this->create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by Numbers (Not requiered actually)
		$times = $this->create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by Numbers, ordered by day and hour
		array_push($return, $courses);

		
		
		return $return;
	}
	
}
