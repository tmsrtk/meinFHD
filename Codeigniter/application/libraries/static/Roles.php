<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Roles
 * Holds all static role properties.
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundurss@fh-duesseldorf.de>
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