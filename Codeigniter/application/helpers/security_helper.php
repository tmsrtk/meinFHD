<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Security Helper
 *
 * Helper that provides some functions for security checks etc. that are used in some controllers.
 */

/**
 * Functions checks for authentication. If not authentication exists it redirects the user to the login page.
 * Function is usually used to protect views, that should not be accessed from guest users.
 *
 */
if ( ! function_exists('check_for_authentication'))
{
    function check_for_authenticaton () {

        $CI = & get_instance(); // get the ci-instance to access other elements of the application

        // check if the user is logged in, if he is not logged in he can`t access the requested site
        if(!$CI->authentication->is_logged_in()) { // the user is not logged in -> redirect him to the login page
            redirect('app/login');
        }
    }
}

/**
 * Function checks if the global authenticated user has got an local linked identity
 * @return bool TRUE if the user is linked, otherwise FALSE
 */
if ( ! function_exists('has_linked_account'))
{
    function has_linked_account () {
        $CI = & get_instance(); // get the ci-instance to access other elements of the application
        $CI->load->model('SSO_model');

        // get the global uid of the authentication source
        $idp_attributes = $CI->samlauthentication->get_attributes();
        // save the uid provided by the idp for further access -> the array needs to be splitted to hold only the uid in the variable
        $idp_auth_uid = $idp_attributes['uid']['0'];

        // check with the sso_model if the user is linked
        if($CI->SSO_model->get_linked_user($idp_auth_uid)) {
            return TRUE;
        }

        return FALSE;

    }
}