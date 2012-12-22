<?php

/**
 * meinFHD WebApp
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Jan Eichler(JE), <jan.eichler@fh-duesseldorf.de>
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class / Model Einstellungen_model
 *
 * Implements all necessary database operations for the custom user settings.
 */
class Einstellungen_model extends CI_Model {

    /**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
    public function __construct(){

        parent::__construct();
    }

    /**
     * Selects the students user data from the database for the given user-id
     * and returns it as an array.
     * @param $userid Integer ID of the user, where the data should be selected for.
     * @return mixed An array will be returned if there are information for the given user id, otherwise NULL will be returned.
     */
	public function query_userdata_student($userid)
	{
        // select the student specific user information and return it
		$this->db->select('b.*, sg.*')
				->from('benutzer b')
				->join('studiengang sg', 'b.StudiengangID = sg.StudiengangID')
				->where('b.BenutzerID', $userid)
				;

		return $this->db->get()->row_array();
	}

    /**
     * Selects the user data from the database for the given user id and returns it as an
     * array.
     *
     * @param $userid Integer ID of the user, where the data should be selected for.
     * @return mixed An array will be returned if there are information for the given user id, otherwise NULL will be returned.
     */
	public function query_userdata($userid)
	{
        // select the whole user information and return it
		$this->db->select('b.*')
				->from('benutzer b')
				->where('b.BenutzerID', $userid)
				;

		return $this->db->get()->row_array();
	}

    /**
     * Saves the edits of the personal information for the currently authenticated user
     * in the database.
     *
     * @access public
     * @param $form_data array The array with the user input from the form
     * @return void
     */
	public function save_edits($form_data)
	{
        // construct the array with the basic user information
		$update = array(
			'LoginName' => $form_data['loginname'],
			'Vorname' => $form_data['forename'],
			'Nachname' => $form_data['lastname'],
			'Email' => $form_data['email'],
		);

        // extract the students specific information and add them to the update-array, if the user is an student
		if ( in_array(Roles::STUDENT, $this->user_model->get_all_roles())){
			$update['StudienbeginnSemestertyp'] = $form_data['semesteranfang'];
			$update['StudienbeginnJahr'] = 	$form_data['startjahr'];
            $update['EmailDarfGezeigtWerden'] = $form_data['EmailDarfGezeigtWerden'];
		}

        // extract the dozent specific information and add them to the update-array, if the user is an student
		if ( in_array(Roles::DOZENT, $this->user_model->get_all_roles()) ||  in_array(Roles::BETREUER, $this->user_model->get_all_roles())){
			$update['Raum'] = $form_data['room'];
			$update['Titel'] = $form_data['title'];
		}

        // extract the new password and add it to the update-array, if it is set in the form_data-array
		if ( ! empty($form_data['password'])){
            $update['Passwort'] = md5($form_data['password']);
        }

        // update the database with the update array
		$this->db->where('BenutzerID', $this->user_model->get_userid());
		$this->db->update('benutzer', $update);

    }

}
/* End of file einstellungen_model.php */
/* Location: ./application/models/einstellungen_model.php */