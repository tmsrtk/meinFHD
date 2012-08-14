<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modul_Model extends CI_Model {

	private $user;//Debug

	
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
	private function define_user($id)
	{
		$query = $this->db->query("SELECT * FROM benutzer WHERE BenutzerID LIKE '".$id."' LIMIT 1");
		$this->user = $query->row_array();
	}

	/**
	 * 
	 *
	 * @param type name // nicht vorhanden
	 * @return type // nicht vorhanden
	 */	
	private function create_courseinfo_array()
	{
		$courseinfo = array();

		//Things like "Dozent", Name etc.
		$courseinfo['Modulinfo'] = array();
		$courseinfo['Modulinfo']['LangName'] = '';
		$courseinfo['Modulinfo']['KurzName'] = '';
		$courseinfo['Modulinfo']['Dozent'] = '';

		//Get possible kinds of courses from database

		$query = $this->db->query("SELECT * FROM veranstaltungsform");

		$result = $query->result_array();

		//Insert in Array
		foreach ($result as $key => $kind_of_course) {
			$courseinfo[$kind_of_course['VeranstaltungsformName']] = array();
		}


		return $courseinfo;

	}


	/**
	 * Adds important, User-specific inforamtion to the courselist. A flags "Aktiv" and "Button" are added
	 * 1. In a "Praktikum" etc. must be added, if the User is part of the group("Aktiv" is 1 in that case)
	 * 2. Other courses like "Vorlesung" dont need that, the flag is set in a loop automatically
	 * 3. If there is no alternative to that course, the aktive flag is also set by default
	 *
	 * Courses set active will not have the possibility to enroll for the user(In the mobile view).
	 *
	 * @param type name // nicht vorhanden
	 * @return type // nicht vorhanden
	 */	
	private function courselist_add_userinfo($courselist, $user_id)
	{

		foreach ($courselist as $key => $course) {

			//If Vorlesung, Tutorium or there is no alternative 
			if ($course['VeranstaltungsformID'] == 1 OR $course['VeranstaltungsformID'] == 6 OR $course['VeranstaltungsformAlternative'] == "")
			{
				$courselist[$key]['Aktiv'] = 1;
				$courselist[$key]['Button'] = 0;
			} 
			//Otherwise, it must be checked, if already set active by user
			else 
			{

				//The course has a button to enroll
				$courselist[$key]['Button'] = 1;

				$query_benutzer = $this->db->query("
					SELECT 
						* 
					FROM 
						benutzerkurs b 
					WHERE 
						b.BenutzerID = ". $user_id . " AND 
						b.SPKursID = " .$course['SPKursID']. " AND 
						b.KursID =" .$course['KursID'] ."
					");

				$result_benutzer = $query_benutzer->result_array();

				if ($result_benutzer[0]['aktiv'] == 0) 
					$courselist[$key]['Aktiv'] = 0;
				else 
					$courselist[$key]['Aktiv'] = 1;
				
			}

		}//End ForEach

		return $courselist;
	}


	/**
	 * The collected Information in the courselist is added to the Array "courseinfo".
	 * In this way, the needed Information is easier to access in the view.
	 * (Optimized for the mobile View! Maybe)
	 *
	 *
	 * @param type name // nicht vorhanden
	 * @return type // nicht vorhanden
	 */	
	private function courselist_in_courseinfo()
	{
	}

	/**
	 * Collects the basic Info to all courses belonging to the "Modul" in a query, depending on the ID of the "Modul"
	 *
	 *
	 * @param type name // ID of the "Modul", $course_id
	 * @return result_array // Array of all courses belonging to Modul
	 */	
	public function get_courselist($course_id)
	{

		$query = $this->db->query("
		SELECT 
			sg.Kursname, sg.kurs_kurz,
			v.VeranstaltungsformName,sp.VeranstaltungsformAlternative, sp.VeranstaltungsformID, sp.KursID, sp.SPKursID,
			sp.DozentID, sp.StartID, sp.EndeID, (sp.EndeID-sp.StartID)+1 AS 'Dauer', sp.GruppeID,
			d.Vorname AS 'DozentVorname', d.Nachname AS 'DozentNachname', d.Email AS 'DozentEmail',
			t.TagName,t.TagID,
			s_beginn.Beginn, s_ende.Ende,
			g.TeilnehmerMax, g.Anmeldung_zulassen,
			(SELECT COUNT(*) FROM gruppenteilnehmer gt WHERE gt.GruppeID = sp.GruppeID) AS 'Anzahl Teilnehmer'
		FROM 
			stundenplankurs sp,
			studiengangkurs sg,
			veranstaltungsform v,
			tag t,
			stunde s_beginn,
			stunde s_ende,
			benutzer d,
			gruppe g
		WHERE 
			sp.kursID = ".$course_id." AND
			sp.IsWPF = 0 AND
			sg.KursID = ".$course_id." AND
			sp.VeranstaltungsformID = v.VeranstaltungsformID AND
			s_beginn.StundeID = sp.StartID AND
			s_ende.StundeID = sp.EndeID AND
			sp.TagID = t.TagID AND 
			sp.GruppeID = g.GruppeID AND
			d.BenutzerID = sp.DozentID
		");
		
		$result = $query->result_array();

		return $result;
	}

	public function get_courseinfo($user_id, $course_id)
	{	

		$courselist = $this->get_courselist($course_id);

		$courselist = $this->courselist_add_userinfo($courselist, $user_id);

		$this->krumo->dump($courselist);

		return $courselist;
	}


}
