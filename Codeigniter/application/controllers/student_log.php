<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Peter Jaraczewski (PJ), <peter.jaraczewski@fh-duesseldorf.de>
 * @author Jan Eichler (JE), <jan.eichler@fh-duesseldorf.de>
 */

/**
 * Class Student_Log
 *
 * Description...
 */
class Student_log extends FHD_Controller {

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
