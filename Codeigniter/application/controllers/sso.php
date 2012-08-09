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
        $this->samlauthentication->require_authentication();

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
     * Function is called from the login form / startpage if an global session already exists and the global uid is linked
     * to an local account, so start a local session with the linked account.
     * It checks if the global authenticated uid is linked to an local account.
     * If not return the user to the login page, otherwise establish a local session.
     * This function is called when the user opens the startpage and an global session exists
     */
    public function authenticate_linked_account() {
        // get and save the information for the linked user in the appropriate class variable
        if ($this->linked_user) { // there is an linked account
            // establish a local session...
            $this->establish_local_session();
        }

        // there is no linked account for the actual global user-id
        else {
          // return to the login page
          redirect('app/login');
        }
    }
}
/* End of file sso.php */
/* Location: ./application/controllers/sso.php */