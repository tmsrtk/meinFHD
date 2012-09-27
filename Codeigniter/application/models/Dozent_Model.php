<?php   if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dozent_Model extends CI_Model {

	private $user;//Debug

	
	public function __construct()
	{

	}


	/**
	 * Collects the basic Info to the docent a query
	 *
	 *
	 * @param type $dozent_id // ID of the docent
	 * @return result_array // Array of docentinfo
	 */	
	public function get_dozentinfo($dozent_id)
	{	

		$return = array();

		$query = $this->db->query("
		SELECT 
		*
		FROM 
			benutzer b
		WHERE 
			b.BenutzerID = ".$dozent_id." 
		");
		
		$result = $query->result_array();

		return $result;
	}


}