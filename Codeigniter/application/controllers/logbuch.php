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

        // load the logbuch model
        $this->load->model('logbuch_model');

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

    /**
     * Opens the logbook library and shows all logbooks for the current user
     * @access public
     */
    public function show_logbooks() {
        // collect all necessary data, that is needed to display the logbooks
        // add the data to the view
        // load the view
        $this->load->view('logbuch/logbook_library', $this->data->load());
    }

    /**
     * Opens the add logbook view with the necessary data
     * @access public
     */
    public function add_logbook() {

        // get the id of the current (asking user) and call
        $current_userid = $this->authentication->user_id();

        // collect the courses that should be displayed
        $courses = $this->logbuch_model->get_all_possible_courses($current_userid);

        // prepare data for dropdown
        $courses_to_display['default_value'] = "Bitte wählen"; // set the default value

        foreach($courses as $key => $value){
            $courses_to_display[$value['KursID']] = $value['kurs_kurz']." (".$value['Kursname'].")";
        }

        // collect the courses that sould be display and add them to the view
        $this->data->add('possible_courses',$courses_to_display);
        // load the view
        $this->load->view('logbuch/logbook_add', $this->data->load());
    }

    /**
     * Validates the selected course in the add logbook form.
     * If everything is alright a new logbook will be created.
     * @access public
     */
    public function validate_add_logbook_form() {

        // set delimiter for validation errors
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">','</div>');

        // set the validation rules
        $this->form_validation->set_rules('kurs','Kurs','callback_check_selected_course');

        // get the selected course (input parameter)
        $selected_course = $this->input->post('kurs');

        // run the validation
        if ($this->form_validation->run() == FALSE) {
            // validation was not successful -> call the mask again
            $this->add_logbook();
        }
        else {
            // add a new logbook to the database (seleected course, and id of the current user)
            $logbook_id = $this->logbuch_model->insert_new_logbook($selected_course, $this->authentication->user_id());

            // TODO
            // copy the base topics

            // load the logbook content view
        }
    }

    /**
     * Checks if the value of the selected course is not the default
     * value (Bitte wählen). Callback function for validate_add_logbook_from() - validation
     * @access public
     * @param $str Value to check
     * @return bool FALSE if the default value is selected, otherwise TRUE
     */
    public function check_selected_course($str) {

        if ($str == 'default_value'){
            $this->form_validation->set_message('check_selected_course', 'Bitte einen Kurs auswählen');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
}