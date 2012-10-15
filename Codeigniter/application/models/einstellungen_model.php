<?php

class Einstellungen_model extends CI_Model {

	public function query_userdata($userid)
	{
		$this->db->select('b.*, sg.*')
				->from('benutzer b')
				->join('studiengang sg', 'b.StudiengangID = sg.StudiengangID')
				->where('b.BenutzerID', $userid)
				;

		return $this->db->get()->row_array();
	}

	public function save_edits()
	{
		$formdata = $this->input->post();

		$update = array(
			'LoginName'				=> $formdata['loginname'],
			'Passwort'				=> md5($formdata['password']),
			'Vorname'				=> $formdata['forename'],
			'Nachname'				=> $formdata['lastname'],
			'Email'					=> $formdata['email'],
			'StudienbeginnSemestertyp'	=> $formdata['semesteranfang'],
			'StudienbeginnJahr'		=> $formdata['startjahr']
			);

		$this->db->where('BenutzerID', $this->user_model->get_userid());
		$this->db->update('benutzer', $update);
	}

}