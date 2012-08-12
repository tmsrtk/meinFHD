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