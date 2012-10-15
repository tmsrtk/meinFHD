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
		// $this->db->get_compiled_select(); 

		$this->db->select('*')
				->from('benutzer')
				->where('BenutzerID', $dozent_id)
				;
		// FB::log($this->db->last_query());
		$q = $this->db->get();


		return $q->result_array();

		// $return = array();

		// $query = $this->db->query("
		// SELECT
		// *
		// FROM 
		// 	benutzer b
		// WHERE 
		// 	b.BenutzerID = ".$dozent_id." 
		// ");
		
		// $result = $query->result_array();

		// return $result;
	}


}