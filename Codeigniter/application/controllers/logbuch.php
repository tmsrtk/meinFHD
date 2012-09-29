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

        // collect all logbooks from the database and add them to the view
        $this->data->add('logbooks',$this->logbuch_model->get_all_logbooks($this->authentication->user_id()));
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
            // if there is no logbook for the selected course create it, otherwise return a message
            if (!$this->logbuch_model->check_logbook_course_existence_for_user($selected_course, $this->authentication->user_id())){
                $this->create_logbook($selected_course);
            }
            else {
                $this->message->set(sprintf('Sorry, zu dem gewählten Kurs exisitiert bereits ein Logbuch. Bitte schau in deine Logbuchbibliothek.'));
                redirect('logbuch/add_logbook');
            }
        }
    }

    /**
     * Creates an new logbook for the given combination of course and user id.
     * @access public
     * @param $course_id Id of the course where the logbook should be created for
     * @param $user_id Id of the user, who should own the logbook
     */
    public function create_logbook($course_id){
        // if there is no logbook for the selected course create it, otherwise return a message
        if (!$this->logbuch_model->check_logbook_course_existence_for_user($course_id, $this->authentication->user_id())){
            // add a new logbook to the database (selected course, and id of the current user)
            $logbook_id = $this->logbuch_model->insert_new_logbook($course_id, $this->authentication->user_id());
            $this->copy_all_base_topics_for_course($course_id, $logbook_id);

            // load the logbook content view
            $this->show_logbook_content($logbook_id);
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

    /**
     * Deletes the logbook with the given id, and reloads the logbook library afterwards.
     * Function is usually called from the logbook library
     * @access public
     * @param $logbook_id id of the logbook that should be deleted
     */
    public function delete_logbook($logbook_id) {
        // delete the specified logbook from the database
        $this->logbuch_model->delete_logbook($logbook_id);

        // reload the logbook library view
        $this->show_logbooks();
    }

    /**
     * Opens the logbook content view with the different topic entries for
     * the given logbook id.
     * @param $logbook_id ID of the selected logbook
     */
    public function show_logbook_content($logbook_id) {
        // get the attendance count and add it to the view
        // query out the course id that corresponds to the logbook id
        $course_id = $this->logbuch_model->get_course_id_for_logbook($logbook_id);
        $this->data->add('course_id', $course_id); // add the course_id to the view
        $this->data->add('attendance_count', $this->logbuch_model->get_attendance_count_for_course_and_act_semester($course_id, $this->authentication->user_id()));
        // add the logbook_id to the view
        $this->data->add('logbook_id', $logbook_id);
        // get all logbook entries and add them to the view
        $this->data->add('logbook_entries',$this->logbuch_model->get_all_entries_for_logbook($logbook_id));
        // get the logbook (average) rating and add it to the view
        $this->data->add('logbook_rating', $this->logbuch_model->get_avg_rating_for_logbook($logbook_id));
        // load the logbook content view
        $this->load->view('logbuch/logbook_entries', $this->data->load());
    }

    /**
     * Displays the input mask to create a new entry for the currently viewed logbook.
     * @access public
     * @param $logbook_id INTEGER id of the logbook where the entry should be saved in
     */
    public function create_entry_mask($logbook_id) {

        // get the coursename and the logbook id for the viewed logbook and add it to the view
        $this->data->add('kursname',$this->logbuch_model->get_course_name_for_logbook($logbook_id));
        $this->data->add('LogbuchID', $logbook_id);
        $this->load->view('logbuch/logbook_add_entry', $this->data->load());
    }

    /**
     * Validates the user input for a new topic. If everything is alright, the new
     * logbook entry will be created.
     * @access public
     */
    public function validate_create_entry_form() {
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">','</div>');

        // set custom error messages
        $this->form_validation->set_message('required','Das Feld %s muss einen Wert enthalten.');
        // define the validation rules
        $this->form_validation->set_rules('topic','Thema','required|max_length[255]');

        // get the input data
        $form_data = $this->input->post();

        // run the validation, if everything is okay -> create the entry
        if($this->form_validation->run() == FALSE){
            // repopulate the view
            $this->create_entry_mask($form_data['LogbuchID']);
        }
        else { // validation was okay
            // clean up the topic rating -> remove the % sign
            $rating = str_replace('%','',$form_data['topic_rating']);

            // save new entry in the logbook (database)
            $this->logbuch_model->save_new_logbook_entry($form_data['LogbuchID'], $form_data['topic'], $form_data['input-topic-annotation'], $rating);
            // jump back to the logbook content overview
            $this->show_logbook_content($form_data['LogbuchID']);
        }
    }

    /**
     * Displays the edit mask for an selected logbook entry.
     * @access public
     * @param $lb_entry_id ID of the logbook entry, that should be edited
     */
    public function edit_entry_mask($lb_entry_id) {
        $logbook_entry = $this->logbuch_model->get_single_logbook_entry($lb_entry_id);

        // get the needed data for the single entry and add it to the view
        $this->data->add('logbook_entry', $logbook_entry);
        $this->data->add('course_name', $this->logbuch_model->get_course_name_for_logbook($logbook_entry['LogbuchID']));

        // load the edit entry view
        $this->load->view('logbuch/logbook_edit_entry', $this->data->load());
    }

    /**
     * Validates the user edits for the given entry. If everything is alright,
     * the edits will be saved.
     * @access public
     */
    public function validate_edit_entry_form() {
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">','</div>');

        // set custom error messages
        $this->form_validation->set_message('required','Das Feld %s muss einen Wert enthalten.');
        // define the validation rules
        $this->form_validation->set_rules('topic','Thema','required|max_length[255]');

        $form_data = $this->input->post();
        // run the validation, if everything is okay -> create the entry

        if($this->form_validation->run() == FALSE){
            // repopulate the view
            $this->edit_entry_mask($form_data['LogbucheintragID']);
        }
        else { // validation was okay
            // clean up the topic rating -> remove the % sign
            $rating = str_replace('%','',$form_data['topic_rating']);
            // save new entry in the logbook (database)
            $this->logbuch_model->update_logbook_entry($form_data['LogbucheintragID'], $form_data['topic'], $form_data['input-topic-annotation'], $rating);
            // jump back to the logbook content overview
            $this->show_logbook_content($form_data['LogbuchID']);
        }
    }

    /**
     * Deletes an single logbook entry by his id and reloads the logbook entry view afterwards.
     * @access public
     * @param $lb_entry_id The id of the entry, that should be deleted.
     * @param $logbook_id ID of the logbook, that corresponds to the entry.
     */
    public function delete_single_logbook_entry($lb_entry_id, $logbook_id){
        $this->logbuch_model->delete_logbook_entry($lb_entry_id); // delete the entry
        $this->show_logbook_content($logbook_id); // reload the topic overview
    }

    /**
     * Copys all base topics for the given course id to the given logbook_id. If there are no topics, it looks for the newest 'studiengangkurs'(PO) to copy the base topics.
     * If there are also no base topics, the logbook stays empty.
     * @param $ourse_id ID of the course that corresponds to the logbook
     * @param $logbook_id ID of the logbook, where the topics should be inserted.
     */
    public function copy_all_base_topics_for_course($course_id, $logbook_id) {

        // get all base topics for the given course id
        $base_topics = $this->logbuch_model->get_all_base_topics_for_course($course_id);
        // if there are no topics, look for the newest course id
        if (count($base_topics) == 0){
            $newest_course_id = $this->logbuch_model->get_newest_course_id($course_id);

            // get all base topics of the given course id
            $topics_new_course = $this->logbuch_model->get_all_base_topics_for_course($newest_course_id);
            // look if there are any topics for the newest course
            if (count($topics_new_course) > 0) {
                $base_topics = $topics_new_course;
            }

            // if there are topics -> copy them to the logbook
            $this->logbuch_model->insert_base_topics_into_logbook($logbook_id, $base_topics);
            // otherwise the logbook stays empty
        }
    }

    /**
     * Selects and opens the course logbook for the given course and user id.
     * @access public
     * @param $course_id Id of the course that corresponds to the logbook
     * @param $user_id Id of the logbook owner
     */
    public function open_logbook_for_course_and_user($course_id, $user_id){
        // get the id of the logbook that should be opened
        $logbook_id = $this->logbuch_model->get_logbook_id($course_id, $user_id);

        // open up the logbook
        $this->show_logbook_content($logbook_id);
    }

}