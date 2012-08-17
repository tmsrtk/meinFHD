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
 * SAML Authentication Library
 *
 * This library is a wrapper to integrate simpleSAMLphp into the CI-Framework.
 * Moreover it supplies methods to realize the authentication via an identity provider.
 */

class Samlauthentication {

    private $authSource;

    /**
     * default constructor: loads the library and initializes the authentication source
     */
    public function __construct() {
        // load the simplesamlphp library if it exists
        @require_once('simplesamlphp/lib/_autoload.php');

        // select and create the authentication source
        $this->authSource = new SimpleSAML_Auth_Simple('default-sp');
    }

    /**
     * Method requires the authentication with the choosen authentication source,
     * if no authentication exists.
     */
    public function require_authentication () {
        // ask for authentication at the idp
        $this->authSource->requireAuth();
    }

    /**
     * Checks in the existing simplesaml-object if an authentication exists
     * @return TRUE if an authentication exists, otherwise FALSE
     */
    public function is_authenticated () {

       return $this->authSource->isAuthenticated();
    }

    /**
     * Method asks the idp for the attributes of the current authenticated user
     * @return returns the attributes of the user, if someone is authenticated, otherwise false is returned
     */
    public function get_attributes() {
        // if someone is authenticated return the attributes
        if($this->authSource->isAuthenticated()) {
            return $this->authSource->getAttributes();
        }

        // no authentication exists
        return FALSE;
    }

    /**
     * Method provides the basic login functionality.
     * Each call of the function starts an new authentication process.
     */
     public function login () {
        $this->authSource->login();
     }

    /**
     * Method logs the user out
     */
    public function logout() {

        $this->authSource->logout();
    }


    /**
     * Function checks if the global authenticated user has got an already linked local identity
     * @return bool TRUE if the user has an linked account, otherwise FALSE
     */
    public function has_linked_account () {
        $CI = & get_instance(); // get the ci-instance to access other elements of the application
        $CI->load->model('SSO_model');

        if ($this->is_authenticated()) {
            // get the global uid of the authentication source
            $idp_attributes = $this->get_attributes();
            // save the uid provided by the idp for further access -> the array needs to be splitted to hold only the uid in the variable
            $idp_auth_uid = $idp_attributes['uid']['0'];

            // check with the sso_model if the user is linked
            if($CI->SSO_model->get_linked_user($idp_auth_uid)) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
/* End of file Samlauthentication.php */