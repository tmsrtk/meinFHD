<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * meinFHD WebApp
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
* Class Roles
* Holds static role properties.
*/
class Roles{

    /**
     * Constant that maps the admin role id to an named constant. Easier access.
     */
	CONST ADMIN = 1;

    /**
     * Constant that maps the dozent role id to an named constant. Easier access.
     */
    CONST DOZENT = 2;

    /**
     * Constant that maps the betreuer role id to an named constant. Easier access.
     */
    CONST BETREUER = 3;

    /**
     * Constant that maps the tutor role id to an named constant. Easier access.
     */
    CONST TUTOR = 4;

    /**
     * Constant that maps the student role id to an named constant. Easier access.
     */
    CONST STUDENT = 5;
}
/* End of file Roles.php */
/* Location: ./application/libraries/static/Roles.php */