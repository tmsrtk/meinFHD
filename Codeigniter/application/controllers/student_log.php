<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Peter Jaraczewski (PJ), <peter.jaraczewski@fh-duesseldorf.de>
 * @author Jan Eichler (JE), <jan.eichler@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class Student_Log
 *
 * Description...
 */
class Student_log extends FHD_Controller {

    public function __construct() {
        parent::__construct();

        // --- EDIT BY Christian Kundruss (CK) for sso authentication ---
        // check if the user is logged in, if he is not logged in he can`t access the requested site
        if(!$this->authentication->is_logged_in()) { // the user is not logged in -> redirect him to the login page
            redirect('app/login');
        }
        // --- END EDIT --
    }

    /**
     * Index
     *
     * .../app
     * .../app/index
     */
    public function index()
    {
        $this->load->view('student_log_list_courses', $this->data->load());
    }
}
