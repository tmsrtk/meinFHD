
<?php   if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stundenplan_Model extends CI_Model {

	private $user;//Debug

	
	public function __construct()
	{
		$this->load->database();
		
	}


	/**
	 * Defines global record of variables, in case additional information about the User is needed.
	 *
	 * Example: $this->user['VorName'];
	 *
	 * @param integer // ID of a User
	 * @return row_array // Information about User
	 */	
	private function define_user($id)
	{
	
		$query = $this->db->query("Select * From benutzer Where BenutzerID Like '".$id."' LIMIT 1");
		$this->user = $query->row_array();
	}

	public function enroll_in_course($user_id, $course_id) {

		$this->db->select('*');
		$this->db->from('gruppenteilnehmer');
		$this->db->where('BenutzerID', $user_id);
		$this->db->where('GruppeID', $course_id);
		
		$students_in_course = $this->db->count_all_results();

		echo $students_in_course;

		if (false) {
			# code...
		}

	}
	
	/**
	 * get_Semester_Typ()													
	 *																		
	 * wirtten by Jochen Sauer, copyed function								
	 *																		
	 * @return Actual Typ of Semester (Winter or Summer)						
	 */
	private function get_Semester_Typ() 
	{		
		// Errechne aktuellen Semestertyp
		return (date("n") >= 3 && date("n") <= 8) ? "SS" : "WS";		
	}
	 
	/**
	 *	printSemesterInteger()												
	 *							
	 *  wirtten by Jochen Sauer, copyed function	
	 *											
	 *	Gibt anhand der übergebenen persönlichen Daten das aktuelle 		
	 *	Semester des Studenten zurück.										
	 *																		
	 *	@param	$semestertyp	 (WS or SS)	
	 *											
	 *	@param	$studienbeginn	Four-digit-number, year the study started	
	 *																		
	 *	@return	$semester 		Actual Semester as String.				
	 */
	private function get_Semester( $semestertyp, $studienbeginn ) 
	{
		
		// definiere Rückgabewert
		$semester = "";
		
		// ermittel semestertyp
		$errechneter_semestertyp = $this->get_Semester_Typ();
		
		// stimmt aktueller Semestertyp mit Studienbeginn-Semestertyp überein?
		$gleicher_semestertyp = ($errechneter_semestertyp == $semestertyp) ? true : false;
		
		// Errechne aktuelles Semester
		$semester = (($gleicher_semestertyp) ? 1 : 0) + 2 * ((($gleicher_semestertyp && date("n") < 3) ? date("Y")-1 : date("Y")) - $studienbeginn);
		
		// Gebe String zurück
		return $semester;
	}	


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
		$query_times = $this->db->query("SELECT Beginn FROM Stunde");
		$times = $query_times->result_array();

		return $times;
	}

	//Constructs and returns an empty array which will contain the days
	private function create_days_array()
	{
		$query_days = $this->db->query("SELECT TagName FROM Tag");
		$days = $query_days->result_array();

		return $days;
	}

	//Constructs and returns an empty 2D array which will contain the timetable
	private function create_timetable_array()
	{
		$query_days = $this->db->query("SELECT TagName FROM Tag");
		$days = $query_days->result_array();

		$query_stunden = $this->db->query("SELECT StundeID FROM Stunde");
		$stunden = $query_stunden->result_array();

		$stundenplan = array();

		foreach ($days as $tag) 
		{
			foreach ($stunden as $stunde) {
			$stundenplan[$tag['TagName']][$stunde['StundeID']] = array();
			}

		}


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
	private function get_courses($id) 
	{
		
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
			v.VeranstaltungsformName,sp.VeranstaltungsformAlternative,
			sp.DozentID, sp.StartID, sp.EndeID, (sp.EndeID-sp.StartID)+1 AS 'Dauer', sp.GruppeID,
			d.Vorname AS 'DozentVorname', d.Nachname AS 'DozentNachname', d.Email AS 'DozentEmail',
			t.TagName,t.TagID,
			s_beginn.Beginn, s_ende.Ende,
			b.Aktiv,
			b.KursID,b.SPKursID,
			g.TeilnehmerMax, g.Anmeldung_zulassen,
			(SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.BenutzerID = ".$id." AND gt.GruppeID = sp.GruppeID) AS 'Anzahl Teilnehmer'
		FROM 
			benutzerkurs b,
			studiengangkurs sg,
			stundenplankurs sp,
			veranstaltungsform v,
			tag t,
			stunde s_beginn, stunde s_ende,
			benutzer d,
			gruppe g
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
			b.SemesterID = ".$this->get_Semester($this->user['StudienbeginnSemestertyp'] , $this->user['StudienbeginnJahr'])." AND
			sp.GruppeID = g.GruppeID AND
			sp.IsWPF = 0
		ORDER BY 
			sp.tagID, sp.StartID
		");
		
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
	 * The Array is 3-Demnsional.
	 * The first Array in there is indexed by Days, for example $stundenplan['Montag']]), then hours,
	 * then an Array for all courses in this hour. 
	 * You would get the name of the first course at hour 1 on a Monday by $stundenplan['Montag'][0][0]['Kursname']])
	 *
	 * @param integer // The ID of a User
	 * @return array // Various arrays
	 */	
	public function get_stundenplan($id)
	{
		//Set global variable in this class for informtion concering the User.
		$this->define_user($id);
		

		//Query all courses from Database
		$courses = $this->get_courses($id);

		//Control active-flag of courses, change if necsassary(see function-doc)
		$courses = $this->set_active($courses);

		//Create empty structure of timetable
		$timetable = $this->create_timetable_array();

		//Sort courses into timetable-array-structure
		$stundenplan = $this->courses_into_timetable($courses, $timetable);


		//Assemble return-array
		$return = array();

		//[0] : The actual timetable
		array_push($return, $stundenplan);

		//[1] : The days, indexed by Numbers (Not requiered actually)
		$days = $this->create_days_array();
		array_push($return, $days);

		//[2] : The times, indexed by Numbers (Not requiered actually)
		$times = $this->create_times_array();
		array_push($return, $times);

		//[3] : The courses in a list, indexed by Numbers, ordered by day and hour(Not requiered actually)
		array_push($return, $courses);

		$this->krumo->dump($return);
		
		return $return;
	}
	
}
