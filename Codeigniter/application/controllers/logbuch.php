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
 * Logbuch-Controller
 *
 */
class Logbuch extends FHD_Controller {

    /**
     * Default constructor, used for initialization
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        // security check / protection to prevent access for unauthorized users
        // check if user is authenticated, otherwise he will be redirected to the login page
        // $this->authentication->check_for_authentication(); TODO remove comment when sso login is merged
    }

    /**
     * Shows the logbuch index page (main menue)
     * @access public
     * @return void
     */
    public function index() {

        // load the logbuch index view
        $this->load->view('logbuch/index.php', $this->data->load());
    }

}