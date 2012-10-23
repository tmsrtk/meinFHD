<?php

class Einstellungen_model extends CI_Model {

	public function query_userdata_student($userid)
	{
		$this->db->select('b.*, sg.*')
				->from('benutzer b')
				->join('studiengang sg', 'b.StudiengangID = sg.StudiengangID')
				->where('b.BenutzerID', $userid)
				;

		return $this->db->get()->row_array();
	}

	public function query_userdata($userid)
	{
		$this->db->select('b.*')
				->from('benutzer b')
				->where('b.BenutzerID', $userid)
				;

		return $this->db->get()->row_array();
	}

	public function save_edits()
	{
		$formdata = $this->input->post();

		$update = array(
			'LoginName'				=> $formdata['loginname'],
			'Vorname'				=> $formdata['forename'],
			'Nachname'				=> $formdata['lastname'],
			'Email'					=> $formdata['email']
			);

		if ( in_array(Roles::STUDENT, $userroles))
		{
			$update['StudienbeginnSemestertyp'] = 	$formdata['semesteranfang'];
			$update['StudienbeginnJahr'] = 			$formdata['startjahr'];
		}

		if ( in_array(Roles::DOZENT, $userroles))
		{
			$update['Raum'] = 						$formdata['room'];
			$update['Titel'] = 					$formdata['title'];
		}

		if ( ! empty($formdata['password'])) $update['Passwort'] = md5($formdata['password']);

		// FB::log($update);

		$this->db->where('BenutzerID', $this->user_model->get_userid());
		$this->db->update('benutzer', $update);
	}

}