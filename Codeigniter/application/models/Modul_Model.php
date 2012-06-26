
<?php   if (!defined('BASEPATH')) exit('No direct script access allowed');

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

		$this->krumo->dump($courseinfo);

	}


	/**
	 * Adds important, User-specific inforamtion to the courselist.
	 * 1. In a "Praktikum" etc. must be added, if the User is part of the group
	 * 2. Other courses like "Vorlesung"
	 *
	 * @param type name // nicht vorhanden
	 * @return type // nicht vorhanden
	 */	
	private function userinfo_to_courselist($courselist, $user_id)
	{

	}


	/**
	 * 
	 *
	 * @param type name // nicht vorhanden
	 * @return type // nicht vorhanden
	 */	
	private function courselist_in_courseinfo()
	{

	}

	/**
	 * Collects the basic Info to all courses of the "Modul" in a query, depending on the ID of the "Modul"
	 *
	 *
	 * @param type name // ID of the "Modul", $course_id
	 * @return result_array // Array of all courses to Modul
	 */	
	public function get_courselist($course_id)
	{

		$query = $this->db->query("
		SELECT 
			sg.Kursname, sg.kurs_kurz,
			v.VeranstaltungsformName,sp.VeranstaltungsformAlternative,
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

		$this->create_courseinfo_array();


		return $this->get_courselist($course_id);

	}


}
