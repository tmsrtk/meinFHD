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
 * Class SSO
 *
 * This class deals with the authentication process via the configured Single-Sign-On IdP, if the users chooses the
 * SSO login method on the login page.
 */
class SSO extends FHD_Controller {

    // --- definition of class variables begin ---
    private $idp_auth_uid;
    private $linked_user;
    // --- definition of class variables end ---

    /**
     * Default constructor to prepare the controller. Constructor is called, when any of the sso-controller functions is called from
     * any view or any other controller
     * */
    public function __construct() {
        parent::__construct();

        // load the sso model
        $this->load->model('SSO_model');

        // at first get the attributes of the authenticated user
        $idp_attributes = $this->samlauthentication->get_attributes();
        // save the uid provided by the idp for further access -> the array needs to be splitted to hold only the uid in the variable
        $this->idp_auth_uid = $idp_attributes['uid']['0'];
        // get all information of the linked user and save them in the appropriate class variable
        $this->linked_user = $this->get_linked_user();
    }

    /**
     * Method starts the authentication process with the in the samlauthentication-library
     * configured idp
     */
    public function authenticate() {

        // ask for authentication at the sso idp -> redirect to the idp`s login page automatically if no global session exists
        if(!$this->samlauthentication->is_authenticated()) {
            $this->samlauthentication->require_authentication(); // ask for global authentication
        }

        // check if the authentication was successful (and if there is an linked account) -> then establish a local session. otherwise not....
        if ($this->samlauthentication->is_authenticated()) {

            // if the user has got an linked account -> perform login with the linked local user account
            // if the global uid is linked to an local account establish a local session
            if ($this->linked_user) {
                $this->establish_local_session();
            }

            // the user has no linked account -> give him the possibility to link or create an account
            // otherwise request for linking: 2 alternatives -> user has got an account and links; -> user requests an account that is automatically linked
            else {
                redirect('sso/link_account');
            }
        }

        return FALSE;
    }

    /**
     * Function establishes an local session with the account to which the global uid is linked
     */
    public function establish_local_session () {

        // if the returned array is not empty-> perform login.. -> establish a local session via the authentication library... (DOKU makes use of "third party")
        // establish a local session with the information in the linked_user array, use therefore the provided authentication library
        if($this->authentication->sso_login($this->linked_user['LoginName'], $this->linked_user['Passwort'])) {
            // establish local session
            // user is logged in -> show message and redirect to the dashboard
            $message_body = 'Hallo ' . $this->authentication->get_name() . ' (User-ID: ' . $this->authentication->user_id() . '), du hast dich erfolgreich eingeloggt!';
            $this->message->set(sprintf($message_body));
            redirect('dashboard/index');
        }
    }

    /**
     * Function returns the information for the linked user. If the user is not linked false will be returned
     * Wrapper function for multiple calls of the same operation
     */
    public function get_linked_user() {
        // check in the database if the authenticated user has got an local linked user account
        // -> call the sso model to do this.... because it needs to talk with the database
        $linked_user_information = $this->SSO_model->get_linked_user($this->idp_auth_uid);

        // if an linked user exists return the provided user information, otherwise false
        if ($linked_user_information){
            return $linked_user_information;
        }

        return FALSE;
    }

    /**
     * Links an global authenticated account with an local identity in. The local identity that should be linked is submitted
     * by the user.
     */
    public function link_account() {

        // --- protect the link account for calls without having a global session --
        if (!$this->samlauthentication->is_authenticated() || $this->samlauthentication->has_linked_account()) {
            redirect('app/login');
        }
        // read the post parameters
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // check if we have values for the username and password
        if ($username && $password) {

            // query the user table to check if the given identity is correct and not linked
            $this->db->select('BenutzerID, LoginName, Passwort, Vorname, Nachname');
            $this->db->from('benutzer');
            $this->db->where('LoginName', $username);
            $this->db->where('Passwort', MD5($password));
            $this->db->where('FHD_IdP_UID',''); // account is not linked

            $query = $this->db->get();

            // there should be only 1 row in the db
            if($query->num_rows() == 1) {
                // get the inputted userid
                $local_uid = $query->row()->BenutzerID;

                // link account
                if($this->SSO_model->link_account($local_uid, $this->idp_auth_uid)) { // link was successful
                    // update data of the linked user
                    $this->linked_user = $this->get_linked_user();
                    // establish local session / login
                    $this->establish_local_session();
                }
            }

            else { // link was not successful
                // show message and redirect back to the link account page
                $message_body = 'Beim Verknüpfen der angegebenen Identität ist ein Fehler aufgetreten. Vermutlich ist die eingegebene Kombination aus Benutzername' .
                                ' und Passwort falsch, oder die angegebene Identität wurde bereits verknüpft.' .' Bitte überprüfe deine Eingaben und starte den Prozess' .
                                ' erneut. Sollte der Fehler weiterhin auftreten kontaktiere bitte den Support.';
                $this->message->set(sprintf($message_body));
                redirect('sso/link_account'); // reload controller for displaying the error message
            }
        }
        // load the link account view
        $this->load->view('sso/link_account', $this->data->load());
    }
}
/* End of file sso.php */
/* Location: ./application/controllers/sso.php */