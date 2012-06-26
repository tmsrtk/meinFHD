
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

	public function get_courseinfo($user_id, $course_id)
	{	
		//wie sollte das array aussehen?
		//Konzeption: In das Array müssen alle Kurse, welche diese übergebene ID haben.
		//Dies sind dann verschiedene Veranstaltungsformen. Sortiert nach eventueller Vorlesung, Praktika, Übung,
		//Seminar, Seminaristischer Unterricht

		 //(Select Aktiv FROM benutzerKurs b WHERE sp.KursID = b.KursID AND sp.SPKursID = b. SPKursID AND b.BenutzerID = ".$user_id.") AS "Angemeldet",
		$courseinfo = array();

		//Collect the basic Info to all Parts of the Course
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
			stunde s_beginn, stunde s.ende
		WHERE 
			sp.kursID = ".$course_id." AND
			sp.IsWPF = 0 AND
			sg.KursID = ".$course_id." AND
			sp.VeranstaltungsformID = v.VeranstaltungsformID AND
			sp.TagID = t.TagID

		");

		
		$result = $query->result_array();

		$this->krumo->dump($result);

		return $result;


	}


}
