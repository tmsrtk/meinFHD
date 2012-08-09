<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * SSO Model
 * The sso model deals with the all necessary db operations for the Single-Sign-On-process
 */
class SSO_model extends CI_Model {

    // the default constructor
    public function __construct() {
        parent::__construct();

    }

    /**
     * Method scans the user table for an account that is linked to the uid of the idp.
     * Information about the linked account are then returned.
     * @param $idp_uid uid of the idp, that asks for authentication
     * @return '0' if there is more than one, or none linked account, otherwise informations
     * of the linked user will be returned as an array
     */
    public function get_linked_user($idp_uid) {
        // query the user table and scan for the idp uid in the appropriate coloumn
        $this->db->select('BenutzerID, LoginName, Passwort, Vorname, Nachname, FHD_IdP_UID');
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
                'FHD_IdP_UID' => $query->row()->FHD_IdP_UID
            );
            print_r($linked_user);
            // return the selected row
            return $linked_user;
        }

        return NULL;
    }
}
/* End of file sso_model.php */
/* Location: ./application/models/sso_model.php */