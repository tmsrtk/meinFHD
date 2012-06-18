<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Adminhelper {

	/**
	 *
	 */
	public function __construct()
	{

	}

	/* creates a random pw with a length of 10 chars - jochens function */
	function passwort_generator() 
	{
		$laenge = 10;
		$string = md5((string)mt_rand() . $_SERVER["REMOTE_ADDR"] . time());
		  
		$start = rand(0,strlen($string)-$laenge);
		 
		$password = substr($string, $start, $laenge);
		 
		return md5($password);
	}

	// get standard user from rules
	function get_standard_userform_rules()
	{
		$rules = array(
			$this->get_formvalidation_forename(),
			$this->get_formvalidation_lastname(),
			$this->get_formvalidation_email()
		);

		return $rules;
	}

	// modular form validation rules



	/**/
	function get_formvalidation_role()
	{
		return array(
			'field' => 'role',
			'label' => 'Rolle',
			'rules' => 'integer'
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
			'field' => 'semesteranfang',  // should be named: semesteranfang
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
			'rules' => 'integer'
			);
	}

}









/* End of file Adminhelper.php */
/* Location: ./application/libraries/Adminhelper.php */