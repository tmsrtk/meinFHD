
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
		*
		FROM 
			stundenplankurs sp,
			benutzerkurs b,
			studiengangkurs sg,
		WHERE 
			sp.kursID = ".$course_id." AND
			b.BenutzerID = ".$id." AND 
			b.kursID = ".$course_id." AND
			sp.IsWPF = 0

		");


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
		
		$result = $query->result_array();

		return $result;


	}


}
