<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
    /**
     * meinFHD WebApp
     *
     * @version 0.0.1
     * @copyright Fachhochschule Duesseldorf, 2012
     * @link http://www.fh-duesseldorf.de
     * @author Christian Kundruß (CK), <christian.kundruss@fh-duesseldorf.de>
     */

    /**
     * Logbuch_Analysis-Controller
     * The Logbuch_Analysis-Controller provides the different analysis,
     * which are part of the logbook functionality.
     * @author Christisan Kundruss, <christian.kundruss@fh-duesseldorf.de>
     */
class Logbuch_Analysis extends FHD_Controller {

    /**
     * Default constructor, used for initialization.
     * @access public
     * @return void
     */
    public function __construct(){
        parent::__construct();

        // load the logbuch model
        $this->load->model('logbuch_model');

        // security check / protection to prevent access for unauthorized users
        // check if user is authenticated, otherwise he will be redirected to the login page
        // $this->authentication->check_for_authentication(); TODO remove comment when sso login is merged
    }


}
