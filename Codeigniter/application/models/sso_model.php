<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK) <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * SSO Model
 * The SSO model deals with the all necessary db operations for the Single-Sign-On-process.
 *
 * @author Christian Kundruss (CK) <christian.kundruss@fh-duesseldorf.de>
 */
class SSO_model extends CI_Model {

    /**
     * Default constructor. Used for initialization purposes.
     * @access public
     */
    public function __construct() {
        parent::__construct();

    }

    /**
     * Method scans the user table for an account that is linked to the given uid of the idp.
     * If there is already an linked account the information about the local identity will be
     * returned.
     *
     * @access public
     * @param integer $idp_uid uid of the idp, that asks for authentication
     * @return mixed '0' if there is more than one, or none linked account, otherwise information
     *              of the linked user will be returned as an array
     */
    public function get_linked_user($idp_uid) {
        // query the user table and scan for the idp uid in the appropriate coloumn
        $this->db->select('BenutzerID, LoginName, Passwort, Email, Vorname, Nachname, FHD_IdP_UID');
        $this->db->from('benutzer');
        $this->db->where('FHD_Idp_UID', $idp_uid);

        $query = $this->db->get();

        // check if there is only one result row... usally there can only be one linked account; multi accounting is not available..
        // one id can be linked to one idp uid. (1:1)
        if ($query->num_rows() == 1) {
            // construct the array with the user information
            $linked_user = array (
                'BenutzerID' => $query->row()->BenutzerID,
                'LoginName' => $query->row()->LoginName,
                'Passwort' => $query->row()->Passwort,
                'Vorname' =>$query->row()->Vorname,
                'Nachname' => $query->row()->Nachname,
                'FHD_IdP_UID' => $query->row()->FHD_IdP_UID,
                'Email' => $query->row()->Email
            );
            // return the selected row
            return $linked_user;
        }

        return NULL;
    }

    /**
     * Links an account with the given local uid to the global uid.
     *
     * @access public
     * @param integer $local_uid UID of the local account that should be linked
     * @param integer $idp_uid global UID where the local account should be linked to
     * @return bool returns TRUE if update is okay, otherwise FALSE will be returned
     */
    public function link_account($local_uid, $idp_uid) {
        $update_data = array (
            'FHD_IdP_UID'   => $idp_uid
        );

        // only the identity with the local_uid should be updated
        $this->db->where('BenutzerID', $local_uid);

        // check if the update was successfull
        if (!$this->db->update('benutzer', $update_data)) {
            return FALSE; // update was not successful
        }

        return TRUE;
    }

    /**
     * Checks in the shibbolethblacklist table, if the given global uid is on the blacklist or not.
     *
     * @access public
     * @param integer $idp_uid the global user id
     * @return bool TRUE if the uid is blacklisted, otherwise FALSE
     */
    public function is_blacklisted($idp_uid) {
        $this->db->select('*');
        $this->db->from('shibbolethblacklist');
        $this->db->where('FHD_IdP_UID', $idp_uid);

        $query = $this->db->get();

        if ($query->num_rows >= 1) { // user is on the blacklist
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Returns all configured departments from the database.
     *
     * @access public
     * @return array The array with the departments
     */
    public function get_all_departments() {
        $this->db->select('*');
        $this->db->from('fachbereich');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Saves a new user in the database and links him to his global shibboleth uid
     *
     * @access public
     * @param Array $form_data Array with the provided user input
     * @return void
     */
    public function save_new_user($form_data) {
        // prepare data for insert
        $input_data = array (
            'LoginName' 				=> $form_data['email'],
            'Email' 					=> $form_data['email'],
            'Vorname'					=> $form_data['forename'],
            'Nachname' 					=> $form_data['lastname'],
            'Matrikelnummer' 			=> $form_data['matrikelnummer'],
            'StudienbeginnJahr' 		=> $form_data['startjahr'],
            'StudienbeginnSemestertyp' 	=> $form_data['semesteranfang'],
            'StudiengangID' 			=> $form_data['studiengang'],
            'Passwort' 					=> md5($form_data['password']),
            'FHD_IdP_UID'               => $form_data['FHD_IdP_UID'],
            'FachbereichID'             => $form_data['department'],
            'TypID'                     => $form_data['TypID']
        );

        // insert the user into the user table
        $this->db->insert('benutzer', $input_data);

        // get the user_id of the created user
        $last_id = mysql_insert_id();
        echo $last_id;

        // insert the user with his roles into benutzer_mm_rolle
        $input_data = array (
            'BenutzerID'    => $last_id,
            'RolleID'       => $form_data['role']
        );

        $this->db->insert('benutzer_mm_rolle', $input_data);
    }

    /**
     * Writes an invitation request with the global uid into the database.
     *
     * @access public
     * @param $form_data Array with the user input
     * @return void
     */
    public function save_user_invitation($form_data) {
        // prepare data for insert
        $input_data = array(
            'Vorname'					=> $form_data['forename'],
            'Nachname' 					=> $form_data['lastname'],
            'Startjahr'			 		=> $form_data['startjahr'],
            'Matrikelnummer' 			=> $form_data['matrikelnummer'],
            'Emailadresse' 				=> $form_data['email'],
            'Semester'				 	=> $form_data['semesteranfang'],
            'Studiengang' 				=> $form_data['studiengang'],
            'TypID'						=> $form_data['role'],
            'FHD_IdP_UID'               => $form_data['FHD_IdP_UID']
        );

        // insert the invitation to the database
        $this->db->insert('anfrage', $input_data);
    }

    /**
     * Checks if there is already an useraccount for the given matrikelnummer
     *
     * @access public
     * @param integer $matr_nr matrikelnummer to check
     * @return bool TRUE if the given matrikelnummer has got an account, otherwise FALSE
     */
    public function check_matrikelnummer_has_account($matr_nr) {
        $this->db->select('*');
        $this->db->from('benutzer');
        $this->db->where('Matrikelnummer',$matr_nr);

        $q = $this->db->get();

        if ($q->num_rows >= 1) { // matrikelnummer has got an account
            return TRUE;
        }

        return FALSE;
    }
}
/* End of file sso_model.php */
/* Location: ./application/models/sso_model.php */