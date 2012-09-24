<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Helper class for admin stuff
* 
* @author Konstantin Voth
*/
class Adminhelper {

	/**
	 *
	 */
	public function __construct()
	{

	}

	/**
	* Creates a random pw with a length of 10 chars - jochens function
	*
	* @return random password
	*/
	function passwort_generator() 
	{
		$laenge = 10;
		$string = md5((string)mt_rand() . $_SERVER["REMOTE_ADDR"] . time());
		  
		$start = rand(0,strlen($string)-$laenge);
		 
		$password = substr($string, $start, $laenge);
		 
		return md5($password);
	}

	// modular form validation rules
	/**/
	function get_formvalidation_role()
	{
		return array(
			'field' => 'role',
			'label' => 'Rolle',
			'rules' => 'required|integer'
			);
	}

	/**/
	function get_formvalidation_loginname()
	{
		return array(
			'field' => 'loginname',
			'label' => 'Benutzername',
			'rules' => 'required|alpha_dash|min_length[4]|max_length[20]|is_unique[benutzer.LoginName]'
			);
	}

	/**/
	function get_formvalidation_forename()
	{
		return array(
			'field' => 'forename',
			'label' => 'Vorname',
			'rules' => 'alpha|max_length[50]'
			);
	}

	/**/
	function get_formvalidation_lastname()
	{
		return array(
			'field' => 'lastname',
			'label' => 'Nachname',
			'rules' => 'alpha|max_length[50]'
			);
	}

	/**/
	function get_formvalidation_email()
	{
		return array(
			'field' => 'email',
			'label' => 'E-Mail',
			'rules' => 'required|valid_email|is_unique[benutzer.Email]'
			);
	}

	/**/
	function get_formvalidation_matrikelnummer()
	{
		return array(
			'field' => 'matrikelnummer',
			'label' => 'Matrikelnummer',
			'rules' => 'integer|exact_length[6]|is_unique[benutzer.Matrikelnummer]'
			);
	}

	/**/
	function get_formvalidation_startjahr()
	{
		return array(
			'field' => 'startjahr',
			'label' => 'Startjahr',
			'rules' => 'integer|exact_length[4]'
			);
	}

	/**/
	function get_formvalidation_semesteranfang()
	{
		return array(
			'field' => 'semesteranfang',
			'label' => 'Semesteranfang',
			'rules' => 'alpha'
			);
	}

	/**/
	function get_formvalidation_studiengang()
	{
		return array(
			'field' => 'studiengang',
			'label' => 'Studiengang',
			'rules' => 'required|integer'
			);
	}

	/***/
	function get_formvalidation_searchbox()
	{
		return array(
			'field' => 'search_user',
			'label' => 'search_user',
			'rules' => ''
			);
	}

	function get_formvalidation_erstsemestler()
	{
		return array(
			'field' => 'erstsemestler',
			'label' => 'erstsemestler',
			'rules' => ''
			);
	}

	/************************************************************************
	 *	printSemesterInteger()												*
	 *																		*
	 *	Gibt anhand der übergebenen persönlichen Daten das aktuelle 		*
	 *	Semester des Studenten zurück.										*
	 *																		*
	 *	@param	$semestertyp	zweistelliger String des Semestertyps 		*
	 *							(WS oder SS) des Startjahres.				*
	 *	@param	$studienbehinn	vierstellige Jahreszahl des Studienbeginns.	*
	 *																		*
	 *	@return	$semester 		Aktuelles Semester als String.				*
	 ************************************************************************/
	function getSemester( $semestertyp, $studienbeginn ) 
	{
		// definiere R�ckgabewert
		$semester = "";
		
		// ermittel semestertyp
		$errechneter_semestertyp = $this->getSemesterTyp();
		
		// stimmt aktueller Semestertyp mit Studienbeginn-Semestertyp �berein?
		$gleicher_semestertyp = ($errechneter_semestertyp == $semestertyp) ? true : false;
		
		// Errechne aktuelles Semester
		$semester = (($gleicher_semestertyp) ? 1 : 0) + 2 * ((($gleicher_semestertyp && date("n") < 3) ? date("Y")-1 : date("Y")) - $studienbeginn + ((date("n")>2) ? 1 : 0));
		
		/* 
		======= Version vor dem 16.02.2012
		$semester = (($gleicher_semestertyp) ? 1 : 0) + 2 * ((($gleicher_semestertyp && date("n") < 3) ? date("Y")-1 : date("Y")) - $studienbeginn + ((!$gleicher_semestertyp) ? 1 : 0));
		
		======= Version vor dem 12.01.2011
		$semester = ((((date("n")>=3 && date("n")<=8) ? "SS" : "WS") == $benutzer_daten["StudienbeginnSemestertyp"]) ? 1 : 0) + 2*(date("Y") - $benutzer_daten["StudienbeginnJahr"]);
		*/
		
		// Gebe String zur�ck
		return $semester;
	}

	/************************************************************************
	 *	getSemesterTyp()													*
	 *																		*
	 *	Gibt den aktuellen Semestertyp zur�ck.								*
	 *																		*
	 *	@return	 	Aktueller Semestertyp als String.						*
	 ************************************************************************/
	function getSemesterTyp() 
	{
		// Errechne aktuellen Semestertyp
		return (date("n") >= 3 && date("n") <= 8) ? "SS" : "WS";		
	}

}









/* End of file Adminhelper.php */
/* Location: ./application/libraries/Adminhelper.php */