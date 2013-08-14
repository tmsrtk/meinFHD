<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Kursverwaltung
 *
 * The course administration / course administration controller provides all course management features for
 * meinFHD.
 *
 * @version 0.0.1
 * @package meinFHD\controllers
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Frank Gottwald (FG), <frank.gottwald@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Kursverwaltung extends FHD_Controller {

    /**
     * @var array Array, that holds all permissions.
     */
    private $permissions;

    /**
     * @var array Array, that holds all role ids the user has got.
     */

    private $role_ids;

    /**
     * @var array Array, that holds all course ids.
     */
    private $course_ids;

    /**
     * @var int User ID auf the currently authenticated user.
     */
    private $user_id;

    // definition of constants for the different eventtypes
    const LECTURE = 1;
    const LAB_SEM = 3;
    const LAB_UEB = 2;
    const LAB_PRA = 4;
    const TUT = 6;

    /*
     * definition of constants for the different user types, that are
     * needed in the course administration. The constants are used in
     * the kursverwaltung_model.
     */
    const LABING = 'kursbetreuer';
    const TUTOR = 'kurstutor';
    

	/**
	 * Default constructor. Used for initialization.
	 * Loads the 'kursverwaltung_model', loads necessary data from the user-model.
	 * - all roles the user has got
	 * - course ids for that user [course-id] => [role-id]
	 * - user_id - mainly for logging-reasons
     * and saves them in the corresponding instance variables
     *
     * @access public
     * @return void
	 */
    public function __construct(){
		parent::__construct();

        // load all necessary models
		$this->load->model('kursverwaltung_model');
        $this->load->model('logbuch_model');

        // query all roles, that are assigned to the authenticated user
		$this->role_ids = $this->user_model->get_all_roles();
        // query all courses, that are assigned to the authenticated user
		$this->course_ids = $this->user_model->get_user_course_ids();
        // query and save the user id of the authenticated user
		$this->user_id = $this->user_model->get_userid();
    }

    /*
     * ==================================================================================
     *                                   Course administration start
     * ==================================================================================
     */

	/**
	 * Main function for the course management.
     * Function shows the initial course management view, if it is requested by the user from
     * the main menu.
     *
	 * Depending on the course_ids that are stored for the user, this function
	 * calls a single/multi-tab-view with course-details or a 'no-courses-assigned' view.
	 * 
	 * IMPORTANT:
	 * The same view is used for all roles (Dozent, Betreuer, Tutor).
	 * Therefore the data has to be prepared depending on all courses the user is assigned to
	 * and the (potentially) different roles the user has in each course.
	 *
     * @access public
     * @return void
	 */
    public function show_coursemgt(){

		// get flash-data - necessaray to reload correct tab (class="active")
		$reload_course = $this->session->flashdata('reload_course');

		if($reload_course){
			$this->data->add('active_course', $reload_course);
		}
        else {
			$this->data->add('active_course', 0);
		}

        // if the user has courses - run through all of them and display them
		if($this->course_ids){

			$course_names_ids = array(); // init the array for all course-names and their correspoonding ids
			/*
			 * Preparing data to print tabs in view:
			 *  - run through all courses and get the short-name of each course to be able to display the course names
			 *    for the different tabs
			 *  - add the short name of every course to the course_names_ids - array. The array has got the following
			 *    structure: key is the course id and the value is the short name of the course
			 */
			foreach ($this->course_ids as $cid => $value) {
                $course_names_ids[$cid] = $this->kursverwaltung_model->get_lecture_name($cid);
			}

			// add the array with the course names and course ids to the global data array (it is added to the view afterwards)
			$this->data->add('course_names_ids', $course_names_ids);

			// preparing dropdown data
			// get options of all dropdowns - starttime, endtime, days
			$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
			$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
			$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');

			/**
			 * The main view is build of several partials.
			 * The basic structure of these partials is the same, therefore they are
			 * reused for every role. Due to this re-use, there are some flags needed,
			 * which indicate, for which role the view is build.
			 *
			 * The following partials (+path) are used - from top to bottom in view:
			 * (More detailed comments on the views within the views)
			 * - staff (/partials/courses_staff):
			 * >> lists staff (prof, labings, tuts) for course
			 * - staff checkbox panel (/partials/courses_staff_cb_panel):
			 * >> list of users (labings, profs, tuts) who can be assigned to course
			 * - course description (/partials/courses_description):
			 * >> textarea with desc from db or static text (tutor)
			 * - tablehead for courses (/partials/courses_tablehead):
			 * >> as name says -- the tablehead for each course
			 * - course lecture (1 row) (/partials/course_lecture):
			 * >> 1 row showing all changeable details for the lecture
			 * - course lecture for tutor-view (1 row) (/partials/course_lecture_tut):
			 * >> 1 row showing all details for the tutor-event -
			 * >> own partial because changable for everyone! (role-independant)
			 *
			 * Depending on role the details provided in these views are changeable.
			 * - Tutors (RolleID = 4) may change only the details of tuts
			 * - Profs & Labings (RolleID = 2 & 3) may change everything
			 * >> More detailed comments on the views within the views
			 */

			// init helper-variables used in partials
			$staff_view_data['is_tutor'] = false; // tutor-flag for staff-partial
			$subview_data['is_tut'] = false; // tutor-flag for all other partials
			$subview_lecture_to_load = '';

			/**
			 * Switch the views to use, depending on the user-role.
			 * If PROF(2)or LABING(3): simple lecture-partial is used
			 * otherwise: setting flags and use tutor-partial.
			 */
			if(in_array(2, $this->role_ids) || in_array(3, $this->role_ids)){
				$subview_lecture_to_load = 'kursverwaltung/partials/courses_lecture';
			}
            else {
				$staff_view_data['is_tutor'] = true;
				$subview_data['is_tut'] = true;
				$subview_lecture_to_load = 'kursverwaltung/partials/courses_lecture_tut';
			}

			/**
			 * Building data for each course
			 * Each tab in the view contains data for a single course.
			 * Therefore the array passed to the view is structured by the course-id.
			 */
            foreach($this->course_ids as $id => $role){
				$name = array(); // init

				// save course_id and course_description for staff-partial
				$staff_view_data['course_id'] = $id;
				$staff_view_data['course_description'] = $course_names_ids[$id]->Beschreibung;

                // edit by CK: get all course topics for the current course and pass them to the view
                $staff_view_data['course_topics'] = implode("\n", $this->logbuch_model->get_all_base_topics($id));


				// getting prof-name from db otherwise only text
				$name = $this->kursverwaltung_model->get_profname_for_course($id);
				if($name){
					$staff_view_data['prof'] = $name[0].' '.$name[1].' '.$name[2];
				}
                else {
					$staff_view_data['prof'] = 'keine Angabe';
				}

				/**
				 * Getting staff from DB
				 * Current labings/tuts for that course and possible labings/tuts
				 * has to be treated separately.
				 * Data is the base for the checkboxes (possible) in view and
				 * active status (current).
				 */
				$staff_view_data['current_labings'] = $this->kursverwaltung_model->get_current_labings_tuts_for_course($id, Kursverwaltung::LABING);
				$staff_view_data['possible_labings'] = $this->kursverwaltung_model->get_all_possible_labings();
				$staff_view_data['current_tuts'] = $this->kursverwaltung_model->get_current_labings_tuts_for_course($id, Kursverwaltung::TUTOR);
                $staff_view_data['possible_tuts'] = $this->kursverwaltung_model->get_all_tuts();

				/**
				 * Build checkbox-views for labings and tuts:
				 * Same partial is used, therefore print_tuts flag toggles.
				 */
				$staff_view_data['print_tuts'] = false;
				$staff_view_data['labing_panel'] = $this->load->view('kursverwaltung/partials/courses_staff_cb_panel', $staff_view_data, TRUE);
				$staff_view_data['print_tuts'] = true;
				$staff_view_data['tut_panel'] = $this->load->view('kursverwaltung/partials/courses_tutor_cb_panel', $staff_view_data, TRUE);

				/**
				 * Build final staff-view:
				 * The partials build above are assembled in one more partial.
				 */
				// get staff-view
				$staff[$id] = $this->load->view('kursverwaltung/partials/courses_staff', $staff_view_data, TRUE);

				// fetching eventtypes for that course_id
				$eventtypes = $this->kursverwaltung_model->get_eventtypes_for_course($id);

				/**
				 * Building activate-application-row
				 * >> eventtypes 2,3,4
				 */
				$act_app_data[] = array(); // init

				// get application-status for that course
				// 1 = application
				// returns -1 if there are no courses, that can be activated
				$application_status = $this->kursverwaltung_model->get_application_status($id);

				// if there are courses where the application could be activated or deactivated for
				if($application_status != -1){
					$act_app_data['course_id'] = $id;
					$act_app_data['headline'] = 'Gruppenanmeldung:';

					// switch labels depending on status
					if($application_status == 1){
						$act_app_data['status_label'] = 'Anmeldung ist aktiviert';
						$act_app_data['status_css'] = 'enabled';
						$act_app_data['btn_class'] = 'btn btn-warning';
						$act_app_data['button_label'] = 'Anmeldung deaktivieren';
					}
                    else {
						$act_app_data['status_label'] = 'Anmeldung ist deaktiviert';
						$act_app_data['status_css'] = 'disabled';
						$act_app_data['btn_class'] = 'btn btn-success';
						$act_app_data['button_label'] = 'Anmeldung aktivieren';
					}

					// if user is not an tutor >> get data and save for view
					if(in_array(2, $eventtypes) || in_array(3, $eventtypes) || in_array(4, $eventtypes) && !$subview_data['is_tut']){
						$activate_application[$id] = $this->load->view('kursverwaltung/partials/courses_activate_application', $act_app_data, TRUE);
					}
                    else {
						$activate_application[$id] = '';
					}
				}

				/**
				 * Building the detail-view:
				 * Get all eventtypes for the course, get view for each of the types
				 * and store to structured array (mentioned above))
				 */
				foreach($eventtypes as $e){
				    // must be an array because (final) view (/index.php) runs data in foreach loop
					$course_data[$id][] = $this->_get_course_event_view($id, $e, $subview_data, $subview_lecture_to_load);
				}

				/**
				 * Add information if the save-button should be shown depending on
				 * role (profs & labings see buttons always) and existence of tut-event.
				 */
				if(in_array(2, $this->role_ids) || in_array(3, $this->role_ids)){
					$show_save_button[$id] = true;
				}
                else {
					if(in_array('6', $eventtypes)){
						$show_save_button[$id] = true;
					}
                    else {
						$show_save_button[$id] = false;
					}
				}
				$this->data->add('show_save_button', $show_save_button);

				// getting description depending on role
				$description_field[$id] = $this->load->view('kursverwaltung/partials/courses_description', $staff_view_data, TRUE);

                // load the course_topic-view to show the course topics depending on roles
                $topic_field[$id] = $this->load->view('kursverwaltung/partials/courses_topics', $staff_view_data, TRUE);

				// adding all data to view
				$this->data->add('staff', $staff);
				$this->data->add('activate_application', $activate_application);
				$this->data->add('course_details', $course_data);
				$this->data->add('description', $description_field);
                // add topic data
                $this->data->add('topics', $topic_field);
				$this->data->add('offset', 0);

            }
            // load course-view
            $this->load->view('kursverwaltung/index', $this->data->load());
        }
        else {
			// load no-courses-assigned-view
			$this->load->view('kursverwaltung/no_courses', $this->data->load());
        }
    }

    /**
     * Helper function to be able to call the course management from another view. The necessary data needs
     * to be passed via flashdata (course id that should be reloaded): 'reload_course'.
     *
     * @access public
     * @return void
     */
    public function call_coursemgt_from_view($c_id){

        // redirecting to show-coursemgt-view
        $this->session->set_flashdata('reload_course', $c_id);
        redirect('kursverwaltung/show_coursemgt');
    }

    /**
	 * Returns one part of the view for each event type.
     * ONLY ONE part of the view (lecture or lab or tut) depending on the passed event type.
	 *
     * @access private
     * @param int $course_id the course id
     * @param int $eventtype eventtype of that event
     * @param array $subview_data containing dropdown-data and flags
     * @param string $subview_to_load defining the subview to load (changable or static - role-dependant)
     * @return string containing the part of the course-view (eventtype-dependant)
     */
    private function _get_course_event_view($course_id, $eventtype, $subview_data, $subview_to_load){
		// initing some variables needed in the partials
		$subview_data['is_lab'] = FALSE; // flag for lab
		$subview_data['lecture_name'] = $this->kursverwaltung_model->get_lecture_name($course_id); // for labels - line
		$subview_data['course_id'] = $course_id; // adding course_id for view
		$subview_data['current_participants'] = ''; // variable needed for participant-buttons

		/**
		 * In this switch-case the views are put together - depending on the event type that is passed.
		 * 1. setting flag (LAB or not)
		 * >> lab: print 'Gruppe #' as label and a number of participants-limit
		 * 2. setting headline
		 * 3. putting view together
		 * >> LECTURE & TUT can get directly put together (with tablehead)
		 * >> other types need some special handling because of the table headeronly once for (potentially) multiple rows
		 *    >> _get_lab_view()
		 * 
		 */
		switch($eventtype) {

			case Kursverwaltung::LECTURE :
				$subview_data['headline'] = 'Vorlesung';
				//get lecture-data - only changeable for NON-tuts
				$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
				$subview_data['current_participants'] = $this->kursverwaltung_model->count_participants_for_course($subview_data['lecture_details']->SPKursID, FALSE);
				$lecture = $this->load->view('kursverwaltung/partials/courses_tablehead', $subview_data, TRUE);
				$lecture .= $this->load->view($subview_to_load, $subview_data, TRUE);
				return $lecture;
			case Kursverwaltung::LAB_SEM :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = 'Seminar';
				return $this->_get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::LAB_UEB :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = '&Uuml;bung';
				return $this->_get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::LAB_PRA :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = 'Praktikum';
				return $this->_get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::TUT :
				$subview_data['is_tut'] = TRUE;
				$subview_data['headline'] = 'Tutorium';
				// get tut data view - always visible to AND changeable for any role
				$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
				$tut = $this->load->view('kursverwaltung/partials/courses_tablehead', $subview_data, TRUE);
				// !! view has to be courses_lecture because tut should be able to save changes in tut
				$tut .= $this->load->view('kursverwaltung/partials/courses_lecture', $subview_data, TRUE);
				return $tut;
		}
    }
    
    /**
	 * Returns the lab-part of course-view as an string
	 *
     * @access private
	 * @param int $course_id the course-id
	 * @param int $eventtype the event type
     * @param string $subview_to_load defining the subview to load (changeable or static - role-dependant)
     * @param array $subview_data containing dropdown-data and flags + additional data
     * @return string containing the part of the course-view (eventtype-dependant)
	 */
    private function _get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data){
		$lab = array(); // init/clean
		
		// data for subviews
		$subview_data['lab'] = '1';
		$subview_data['current_participants'] = '';
		$subview_data['download_file'] = '';
		
		/**
		 * Get lab-data-view:
		 * At first fetch all the course-details for that course from db
		 * 
		 * Then initially the tablehead is loaded into an empty array (lab)
		 * after that a row for each group is added.
		 * >> getting number of participants and participants themselves
		 * >> and create a location a file can be stored
		 */
		
		// get data from db - array containing lab-data
		$lab_details = $this->kursverwaltung_model->get_lab_details($course_id, $eventtype);
		
		// IMPORTANT:
		// method get_lab_details returns the details for ALL labs
		// to have one unique id (not the course-id) for each table-header
		// only the first entry in array is passed into view
		// means: when reading data, groups has to be generated by knowing one of them
		$subview_data['lab_details'] = $lab_details[0];
		
		// init lab with tablehead
		$lab[] = $this->load->view('kursverwaltung/partials/courses_tablehead', $subview_data, TRUE);
		
		foreach($lab_details as $details){
			// getting all participants and count them to show current participants
			$count_participants = 0;
			$count_participants = $this->kursverwaltung_model->count_participants_for_course($details->SPKursID, TRUE);
			
			// create location of potential downloaded file
			$dl_file = '';
			$dl_file = './resources/downloads/participants/gruppe-'.md5($details->SPKursID);
			
			// pass data into view
			$subview_data['download_file'] = $dl_file;
			$subview_data['lecture_details'] = $details;
			$subview_data['current_participants'] = $count_participants;
			
			// save new row to view
			$lab[] = $this->load->view($subview_to_load, $subview_data, TRUE);
		}
		
		// returning final (sub)view
		return $lab;
    }


    /**
	 * Saves the details of the currently selected course tab in the view.
	 * Saves the course-changes made by a user and submitted.
	 * Data in post contain ALL details from the form.
	 * Every field which is not empty will be saved to db.
	 * 
	 * Data is structured as described below:
	 * - course-details: [SPKursID_Spaltenname-DB] => value (empty if no value)
	 * - description: [KursID_description] => value (empty if no value)
	 *
     * @acess public
     * @return void
     */
    public function save_course_details_all_at_once(){

		// get data from POST
		$input_data = $this->input->post();

		// init
		$input_data_filtered = array();
		$sp_course_id = 0; // init to detect changes
		$sp_course_id_temp = 0;
		$save_course_details_to_db = array();

		$save_group_details_to_db = array();
		$desc_split = array();
		$course_id = '';
		$description = '';

        // filter -> save course topics and remove them from the input array
        $input_data = $this->_save_course_topics($input_data);

		// first filter
		// - remove empty fields from email-checkboxes
		// - get description

		/**
		 * 1. Run through passed data and filter.
		 * - Empty fields will be removed here.
		 * - Last field containing the description will be saved separately.
		 * - Other data stored to new array.
		 * 
		 */
		foreach ($input_data as $key => $value) {
			// only empty fields
			if($value !== ''){
				// description
				if(!strstr($key, 'description')){
					$input_data_filtered[$key] = $value;
				}
                else {
					$desc_split = explode('_', $key);
					$course_id = $desc_split[0];
					$description = $value;
				}
			// description, room and number of participants has to be stored!!
			// >> override the number will an "empty string"
			}
            else {
				// handle room and number of participants
				if(strstr($key, 'Raum') || strstr($key, 'Teilnehmer')){
					$input_data_filtered[$key] = $value;
				}
				// handle description
				if(strstr($key, 'description')){
					$desc_split = explode('_', $key);
					$course_id = $desc_split[0];
					$description = $value;
				}
				
			}
		}

		// run through the filtered input
		foreach ($input_data_filtered as $key => $value) {
			// get key and field-name
			$split_key = explode('_', $key);
			// save spkursid
			$sp_course_id = $split_key[0];

			// if sp_course_id changed >> build arrays to save in db
			if($sp_course_id !== $sp_course_id_temp){
				// if old spkursid is not initial value 0 - there are data to save
				if($sp_course_id_temp !== 0){

					// save that data each time course_id changes
					$this->kursverwaltung_model->save_course_details($sp_course_id_temp, $save_course_details_to_db, $save_group_details_to_db);

					// clean arrays
					$save_course_details_to_db = array();
					$save_group_details_to_db = array();
					
				}
				$sp_course_id_temp = $sp_course_id;
			}

            // avoid array index overflow exception because of the button, that does not provide any key following the id
            if(array_key_exists(1,$split_key)){
                // prepare data for saving - starttime, endtime and day has to be mapped from array-index to ID (+1)
                switch ($split_key[1]) {
                    case 'TeilnehmerMax' : $save_group_details_to_db[$split_key[1]] = $value; break;
                    case 'Raum' : $save_course_details_to_db[$split_key[1]] = $value; break;
                    default : $save_course_details_to_db[$split_key[1]] = $value+1; break;
                }
            }
		}

		// save the course description
		$this->kursverwaltung_model->save_course_description($course_id, $description);
		
		// log the activities at once - data has been changed
		$this->helper_model->log_activities(5, $this->user_id);

		// redirecting to show-coursemgt-view
		$this->session->set_flashdata('reload_course', $course_id);
		redirect('kursverwaltung/show_coursemgt');
    }
    
    
    /**
     * Switch method for saving the selected labings for the desired courses.
     *
     * @access public
     * @return void
     */
    public function save_labings_for_course(){
		// get incoming data
		$staff_to_save = $this->input->post();
        // save the desired labings for the desired courses
		$this->_save_staff_to_db(Kursverwaltung::LABING, $staff_to_save);
		
		// redirecting to show-coursemgt-view again (reload)
		$this->session->set_flashdata('reload_course', key($staff_to_save));
		redirect('kursverwaltung/show_coursemgt');
    }
    
    
    /**
     * Switch method for saving the selected tuts for the desired courses.
     *
     * @access public
     * @return void
     */
    public function save_tuts_for_course(){
		// get incoming data
		$staff_to_save = $this->input->post();
		$this->_save_staff_to_db(Kursverwaltung::TUTOR, $staff_to_save);
		
		// redirecting to show-coursemgt-view
		$this->session->set_flashdata('reload_course', key($staff_to_save));
		redirect('kursverwaltung/show_coursemgt');
    }

    /**
     * Saves the passed staff information in the passed database table.
     *
     * @access private
     * @param string $table Database table to which the staff should be saved
     * @param array $staff Array with the staff, that should be saved in the database
     */
    private function _save_staff_to_db($table, $staff){
		$current_labings_tuts = array(); // init
		$course_id = ''; // init

		// if theres only one item - all labings/tuts has been removed
		if(count($staff) === 1){
			// get course_id i.e. first element key
			reset($staff); // setting pointer to first element - necessary?!?!
			$course_id = key($staff);
			// delete staff for that course_id
			// >> model - passed array is empty
		} else {
			// run through all incoming data
			foreach ($staff as $key => $value) {
				// ignore first element
				if($value != 'Speichern'){
					$course_id = $value;
					$current_labings_tuts[] = $key;
				}
			}
		}	

		$this->kursverwaltung_model->save_staff_to_db($course_id, $current_labings_tuts, $table);

		// back to view
		$this->show_coursemgt();
    }

    /**
     * Generates an file with all registered course participants for
     * the desired time table course. Therefore the timetable course
     * id is passed via $POST. After generating the file an download
     * link will be echoed.
     * The method is designed for being called via ajax.
     * For only generating the file and getting an link use the method
     * _generate_course_participants_list().
     *
     * @access public
     * @return void
     */
    public function ajax_download_course_participants_list(){

        $course_id = $this->input->post('sp_course_id');
        $is_spcourse = $this->input->post('is_spcourse');

        // convert the is_spcourse flag that is passed via post to an boolean value, because during the ajax
        // operation it is passed as an simple string
        if($is_spcourse == 'true'){
            $is_spcourse = true;
        }
        else {
            $is_spcourse = false;
        }

        // generate the list and echo the download link
        $local_path = $this->_generate_course_participants_list($course_id, $is_spcourse);
        $global_path = site_url($local_path);

        echo $global_path;
    }

    /**
     * Depending on the flag '$is_spcourse' the method generates an .csv-file with all
     * timetable course or regular course participants and returns an link to the download
     * location of the file.
     *
     * @access private
     * @param $course_id int ID of the course
     * @param $is_spcourse boolean Flag if the course is an timetable course(TRUE) or not (FALSE)
     * @return string String with the full path, where the participants list could be downloaded.
     */
    private function _generate_course_participants_list($course_id, $is_spcourse){

        // get data for file
        $data = $this->kursverwaltung_model->get_data_for_course_participants_list($course_id, $is_spcourse);

        $file = '';
        $file_name = 'Teilnehmerliste-gruppe-'.md5($course_id).'.csv';
        $file_path = './resources/userfiles/downloads/'.$file_name;

        // if there is already an file with the same name -> delete it
        if(file_exists($file_path)){
            unlink($file_path);
        }

        // create the file (open it for writing)
        $file = fopen($file_path, 'a+');

        // write information into the file
        if(is_writable($file_path)){
            fwrite($file, $data);
        }

        // close the file
        fclose($file);
        // set the correct unix permissions
        chmod($file_path, 0640);

        // return the local path to the file
        return $file_path;
    }

	/**
	 * Updates the application-status for an whole course.
     * Therefore the id of the timetable course is passed
     * via post. The Method will not return / echo any result.
	 * Echo just obligatory >> success changes button-appearance
     *
     * @access public
     * @return void
	 */
	public function ajax_toggle_activation_of_sp_course(){
		$data = '';
        // get the course id and the actual application status in one array
		$data = $this->input->post('course_id_status');

        // if the course is enable -> disable application
		if($data[1] == 'enabled'){
			// means before it was ENABLED >> DISABLE registration
			$this->kursverwaltung_model->update_benutzerkurs_application($data[0], FALSE);
		}
        else { // the course was disabled -> enable application
			// means before it was DISABLED >> ENABLE registration
			$this->kursverwaltung_model->update_benutzerkurs_application($data[0], TRUE);
		}

		echo '1';
	}
	
	
	/**
	 * Returns the modal-content for search-user-modal to the view
	 * Method is called from within view (hitting the search button in user-search-modal)
	 * Data passed from view contains array:
	 * - [0] => matrikelno to search for
	 * - [1] => courseId (needed to generate unique dom-element - necessary because of tab-view)
	 * 
	 * The only validation is to check for numeric value.
	 * In any other case a message is returned that user, could not be found + new input-field 
	 *
     * @access public
	 * @echo string final dom-element depending on search-result
     * @return void
	 */
	public function ajax_search_student_by_matrno(){
		$return = ''; // init
		
		// fetching data from post
		$data_from_post = $this->input->post('server_data');

		// check if passed data is numeric
		if(is_numeric($data_from_post[0])){
			$return = $this->kursverwaltung_model->search_student_by_matrno($data_from_post[0]);
		}
		
		// build element to be shown in modal and echo
		// either student-details + button to assign role
		if($return){
			echo $this->_build_studentsearch_modal_content($data_from_post[1], $return);
		// or error-message, that there is no student with this matr-no and input-field to repeat search
		}
        else {
			echo $this->_build_studentsearch_modal_content($data_from_post[1]);
		}
	}
	
	
	/**
	 * Builds the modal-content for search-user-modal
	 * Returns a final-dom element which is put into the modal
	 * Depending on the search-result (student found or not) the content differs:
	 * - student found: button to assign role to student
	 * - student already tutor: message to have a look at the list
	 * - not found: message plus inputfield to search again - same id as defined in view >> reusable functions
	 *
     * @access private
	 * @param int $course_id the course the student should be added as tutor
	 * @param array $student_detail some student-details (atm: first, last name and matrno)
	 * @return string final dom-element that could be put into the modal.
	 */
	private function _build_studentsearch_modal_content($course_id, $student_detail = ''){
		$element = '<div>'; // init

		// if student was found
		if($student_detail){
			// check ifthe user already has the tutor-role
			if($student_detail != -1){
				$value = $student_detail[0]->Vorname.' '.$student_detail[0]->Nachname.' ('.$student_detail[0]->Matrikelnummer.')';
				$element .= '<input type="submit" value="'.$value.' zum Tutor machen" id="add-tutor-dialog-assign-'.$course_id.'" data-matrno="'.$student_detail[0]->Matrikelnummer.'" class="span12 btn-danger">';
			// otherwise return error-message				
			} else {
				$element .= '<div class="span12">Der Student ist bereits Tutor. Wählen Sie ihn aus der Liste der Tutoren aus.</div>';
			}
		// otherwise
		} else {
			$element .= '<div class="span12">Unter dieser Matrikelnummer wurde im System kein Student gefunden.</div>';
			$element .= '<div class="span12">Neue Suche:';
			$element .= '<input type="text" placeholder="Matrikelnummer" name="matrnr" id="matrnr-input">';
			$element .= '<input type="submit" value="Suchen" id="add-tutor-dialog-search" class="btn-info"></div>';
		}
		
		$element .= '</div>';
		return $element;
	}

	/**
	 * Function to assign tutor-role to a student and make him tutor for a course.
	 * Therefore following data is passed via POST - array:
	 * - [0] => matrikelno to search for
	 * - [1] => courseId (needed to generate unique dom-element
	 * 
	 * After assignment return success-message as string or
	 * error-message (shouldn't happen).
     * Function is made to be called via ajax
     *
     * @access public
     * @return void
	 */
	public function ajax_add_student_as_tutor(){
		// fetching data from post
		$student_data = $this->input->post('student_data');
		
		// assign tut-role to student and for passed course_id
		if($this->kursverwaltung_model->assign_tut_role_to_student($student_data, $this->user_model->get_userid())){
			echo 'Der Student ist nun Tutor des Kurses und kann Tutorien verwalten.';
		}
        else {
			echo 'Fehler bei der Verarbeitung. Kontaktieren Sie einen Administrator.';
		}
	}

    /**
     * Removes course topics from the input array, returns the array and saves the topics in the database.
     *
     * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
     * @access private
     * @param $input_data the whole form input
     * @return array the input array without the course topics
     */
    private function _save_course_topics($input_data){

        // init
        $course_id = 0;
        $topics = '';

        // extract course id and topics from form input
        foreach ($input_data as $key => $value) { // run through the array
            if(strstr($key, 'topics')) { // search for the topic key
                // split the key to extract the course_id
                $splitted_key = explode('_', $key);
                $course_id = $splitted_key[0];

                // extract the topics
                $topics = $value;

                // remove them from the array
                unset($input_data[$key]);
                break;
            }
        }

        // save topics for the appropriate course

        // first of all delete all existing elements for the course_id, if there are some...
        $this->logbuch_model->delete_all_base_topics($course_id);
        // if the string is not empty insert the topics, otherwise there are no base topics for the students (anylonger)
        if ($topics != '') {
           // construct the array (1 line is 1 entry) -> every line is one topic
            $topic_array = explode("\n", $topics);
           // insert the topics
            $this->logbuch_model->save_all_base_topics($course_id, $topic_array);
        }
        // return the modified input array
        return $input_data;
    }

    /**
     * Sends an Email to the course and selected course participants, groups, tutor, and/or adviser.
     * Therefore the method expects the id of the course and the single groups and/or advisors
     * via the POST-Array. The method is designed for being called via ajax.
     * The following $POST-Parameters are afforded:
     * - staff_recipients -> staff to who the email should be sent
     * - course_recipients -> the groups to which the email should be sent
     * - email_subject -> the subject of the email
     * - email_message -> the message(body) of the email
     *
     * If the email was successfully sent to the recipients, true as an json-object will be returned,
     *
     * @access public
     * @return void
     */
    public function ajax_send_email_to_course(){

        // extract the needed elements from the post array
        $staff_recipients = $this->input->post('staff_recipients');
        $course_group_recipients = $this->input->post('course_recipients');
        $email_subject = $this->input->post('email_subject');
        $email_message = $this->input->post('email_message');

        // get the email of all chosen recipients
        $course_recipients_array = array();

        // if there are some selected course groups
        if (count($course_group_recipients) > 0){
            // get the email addresses of the desired users
            foreach($course_group_recipients as $single_sp_course){

                // get the eventtype of the course
                $spcourse_eventtype = $this->kursverwaltung_model->get_eventtype_for_spcourse($single_sp_course);
                // depending on the eventtype get all participants and email addresses
                if ($spcourse_eventtype == 1){ // if eventtype 1 the course is not an spcourse
                    $course_recipients_array[] = $this->kursverwaltung_model->get_participants_for_single_sp_course($single_sp_course, false);
                }
                else{ // all other eventtypes
                    $course_recipients_array[] = $this->kursverwaltung_model->get_participants_for_single_sp_course($single_sp_course, true);
                }
            }

        }

        // init of variables
        $staff_recipients_array = array();

        // if there are some staff roles selected get the desired email addresses
        if (count($staff_recipients) > 0){

            // get the email address for each selected recipient type
            foreach($staff_recipients as $single_staff_recipient){

                // cut the string off, because it is constructed like this 'course_id-recipent type'
                $string_cut_off = explode('-', $single_staff_recipient);

                $course_id = $string_cut_off[0];
                $recipient_type = $string_cut_off[1];
                unset($string_cut_off); // delete the variable

                // switch-case for the different recipient types
                switch ($recipient_type){
                    case'1': // dozent
                        $staff_recipients_array[] = $this->kursverwaltung_model->get_assigned_adviser_information_for_single_course($course_id, 'dozent');
                        break;
                    case '2': // advisor
                        $staff_recipients_array[] = $this->kursverwaltung_model->get_assigned_adviser_information_for_single_course($course_id, 'advisor');
                        break;
                    case'3': // tutor
                        $staff_recipients_array[] = $this->kursverwaltung_model->get_assigned_adviser_information_for_single_course($course_id, 'tutor');
                        break;
                }
            }
        }

        // generate an array that contains all email addresses (only distinct ones)
        $email_addresses = array();

        // for each course recipients
        foreach($course_recipients_array as $single_recipient => $value){
            foreach ($value as $nested_entry){
               // if the address is not already in the array -> add it
                if (!in_array($nested_entry->Email, $email_addresses)){
                    $email_addresses[] = $nested_entry->Email;
                }
            }
        }

        // for each staff recipients
        foreach($staff_recipients_array as $single_recipient_type => $value){
            foreach($value as $nested_entry){
                // if the address is not already in the array -> add it
                if (!in_array($nested_entry['Email'], $email_addresses)){
                    $email_addresses[] = $nested_entry['Email'];
                }
            }
        }

        // at least add the email address of the currently authenticated user to the array (if it is not in it so far)
        $current_user_email = $this->user_model->get_email_address();

        if(!in_array($current_user_email, $email_addresses)){
            $email_addresses[] = $current_user_email;
        }

        // get some information about the lecture (longname, shortname, description)
        $lecture_information = $this->kursverwaltung_model->get_lecture_name($course_id);

        // extend the email subject with the course short name at the beginning
        $email_subject = $lecture_information->kurs_kurz . ': ' . $email_subject;

        // send the email to all selected recipients
        $this->mailhelper->send_meinfhd_to_multiple_recipients($email_addresses, $email_subject, $email_message);

        echo json_encode(true);
    }

    /*
     * ==================================================================================
     *                                   Course administration end
     * ==================================================================================
     */
	
	
	
	/* ************************************************************************
	 * 
	 * ******************************** Praktikumsverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 */
	
	

	/**
	 * MAIN-FUNCTION FOR LAB-MGT
	 * Provides two different views with one single function called from menue
	 * and from within view. The views that are shown depend on:
	 * 
	 * 1. data passed via POST
	 * If there is data passed via POST, then view has been called from within
	 * overview view. That means the user already chose one group and
	 * the view to be shown is a detail-view of a lab with one defined active tab.
	 * 
	 * 2. courseIds for current user
	 * If the current user has more than one course_id to manage AND there is NO data passed
	 * he is directed to an overview view, that provides the possibility to chose from
	 * all groups he manages.
	 */
	public function show_labmgt(){
		// init course_id with -1
		$course_id_to_show = -1;
		
		// if there is only one course_id >> save that one to variable
		// TODO check if correct !!!!!!!!!!!!!
		// !!!! doesn't work, because user-model returns array with ALL course-ids mapped to role for this course
		// only working if staff has only one role for that course << which is the default case
		// either prevent from assigning two different roles for one person and course (+ modals)
		// or catch 
		if(count($this->course_ids) === 1){
			$course_id_to_show = key($this->course_ids);
		}
		
//		echo '<div>';
//		print_r($this->course_ids);
//		echo '<div>';
		 
		// DEBUG
//		echo 'test'.$this->input->post();
		
		// if data is passed >> save to variable
		if($this->input->post('sp_course_id')){
			$sp_course_id_to_show = $this->input->post('sp_course_id');
			$course_id_to_show = $this->input->post('course_id');
		}
		
		// if course_ids is NO array and NOT initialized
		// means: there is more than one course_id stored ???????
		if(count($this->course_ids) > 1 && $course_id_to_show == -1){
			// goto overview-view
			$this->show_labmgt_overview();
		} else {
			// go directly to group-view
//			$this->show_labmgt_group();
			
			// pass new id via flashdata
			$this->session->set_flashdata('sp_course_id', $sp_course_id_to_show);
			$this->session->set_flashdata('course_id', $course_id_to_show);

			// goto view
			redirect('kursverwaltung/show_labmgt_group');
			
		}
	}
	
	/**
	 * Pass course_ids and sp_course_ids into view and call view
	 */
	private function show_labmgt_overview(){
		// get sp_course_details for course_id - all labs? disable seminar?
		$sp_course_details = '';
		$eventtypes_to_fetch = array(2,3,4);
		foreach($this->course_ids as $c_id => $role){
			foreach($eventtypes_to_fetch as $e){
				$sp_course_details[$c_id.'-'.$e] = $this->kursverwaltung_model->get_course_details($c_id, $e);
			}
		}

		// store for view
		$this->data->add('sp_course_details', $sp_course_details);
	    $this->load->view('kursverwaltung/labs_overview_show', $this->data->load());
	}
	
	/**
	 * Prepare data and pass into view
	 * !! little more work to do.
	 */
	public function show_labmgt_group(){
		// variables to get to correct group (with active group; depending on sp_course_id)
		$load_sp_course_id = $this->session->flashdata('sp_course_id');
		$load_course_id = $this->session->flashdata('course_id');
		
//		echo $load_course_id;
//		echo $load_sp_course_id;
		
		// if there is NO data passed
		if(!$load_sp_course_id){
			$load_sp_course_id = -1;
			$load_course_id = -1;
		}
		
		// ## TODO 
		// ## fetch all lab-details of each group and pass to view
		// ## get sp_course_details for course_id - all labs? disable seminar?
		// init
		$sp_course_details = '';
		$lab_theads = '';
		
		
		// defining all eventtypes to fetch data for
		$eventtypes_to_fetch = array(2,3,4);
		foreach($eventtypes_to_fetch as $e){
			// either data has been passed via flashdata >> user chose group (and (implicit) course) to manage
			// active group in view
			if($load_sp_course_id != -1){
				$course_id = $load_course_id;
			} else {
				// or there is only one course
				// >> no active group
				$course_id = key($this->course_ids);
			}
			
			// fetch data
			if(count($this->course_ids)){
				$sp_course_details[$course_id.'-'.$e] = $this->kursverwaltung_model->get_course_details($course_id, $e);
			}

		}
		
		// preparing group-table-head data
		$thead_data['event_dates'] = $this->get_dates_for_all_labs($sp_course_details);
		
		// running through array to pass SPKursID to fetch dates for
		// and the number of groups to be shown
		foreach($sp_course_details as $details){
			foreach($details as $d){
				if($d){
					
					// zwischentestat1 - index 20!
					// zwischentestat2 - index 21!
					// number of groups to be shown stored with dates - index 22!
					$thead_data['zwtestat1'][$d->SPKursID] = $thead_data['event_dates'][$d->SPKursID][20];
					$thead_data['zwtestat2'][$d->SPKursID] = $thead_data['event_dates'][$d->SPKursID][21];
					$thead_data['number_of_events'][$d->SPKursID] = $thead_data['event_dates'][$d->SPKursID][22];
					
					// save ids for partial
					$thead_data['sp_course_id'] = $d->SPKursID;
					// storing 
					$lab_theads[$d->SPKursID] = $this->load->view('kursverwaltung/partials/labs_thead', $thead_data, TRUE);
				}
			}
		}
		
		// save data and active group to 
		$this->data->add('theads', $lab_theads);
		$this->data->add('sp_course_details', $sp_course_details);
		$this->data->add('sp_course_participants_details', $this->get_details_for_all_labs($sp_course_details));
		$this->data->add('event_dates', $this->get_dates_for_all_labs($sp_course_details));
		$this->data->add('active_group', $load_sp_course_id);
	    $this->load->view('kursverwaltung/labs_group_show', $this->data->load());
	}
	
	
	/**
	 * Helper method taking array with all eventtypes (AND groups) for a single course
	 * Peparing data for view
	 *  
	 * @param array $sp_course_details containing all course-labs-details sorted by eventtypes
	 * @return array $group_participants containing all participants sorted by groups
	 */
	private function get_details_for_all_labs($sp_course_details){
		// variable to be returned
		$lab_participants_plus_notes = array();
		
		// running through all course-event-combinations and get participants
		// 1. check if there are courses with details to fetch
		foreach($sp_course_details as $key => $groups){
			// if there are courses for this course-event-combination
			if($groups){
				// 2. get participants for each course and save to array
				foreach($groups as $index => $sp_course_object){
					$lab_participants_plus_notes[$key][$sp_course_object->SPKursID] = $this->kursverwaltung_model->get_lab_notes($sp_course_object->GruppeID, $sp_course_object->SPKursID);
				}
			}
		}
		
		return $lab_participants_plus_notes;
	}
	
	
	
	/**
	 * Helper method taking array with all eventtypes (AND groups) for a single course
	 * Peparing data for view
	 * 
	 * @param array $sp_course_details containing all course-labs-details sorted by eventtypes
	 * @return array $event_dates containing all dates if stored before
	 */
	private function get_dates_for_all_labs($sp_course_details){
		// variable to be returned
		$event_dates = array();
		
		// running through all course-event-combinations and get participants
		// 1. check if there are courses with details to fetch
		foreach($sp_course_details as $key => $groups){
			// if there are courses for this course-event-combination
			if($groups){
				// 2. get dates for each course and save to array
				foreach($groups as $index => $sp_course_object){
					$event_dates[$sp_course_object->SPKursID] = $this->kursverwaltung_model->get_lab_dates($sp_course_object->GruppeID);
				}
			}
		}
		
		return $event_dates;
	}
	
	
	/**
	 * Saving data from lab-notes-view - CHECKBOXES ONLY
	 * Array with bunch of data passed via POST. Includes the following:
	 * array[0] element_name
	 * array[1] cb_status
	 * array[2] user_id
	 * array[3] event_id starts with 0 runs to number set by user for this lab - xtra-events = x1, x2
	 * Depending on the checkbox that has been clicked db is updated
	 * 
	 */
	public function ajax_save_lab_checkboxes(){
		$cb_data= ''; // init
		$cb_data = $this->input->post('lab_cb_data');
		
		$cb_name = $cb_data[0];
		
		// calling method and pass table-collumn to save data in
		if(strstr($cb_name, 'presence')){
			$this->kursverwaltung_model->update_group_cbs($cb_data[1], $cb_data[2], 'anwesenheit', $cb_data[3]);
		} else if(strstr($cb_name, 'testat')){
			$this->kursverwaltung_model->update_group_cbs($cb_data[1], $cb_data[2], 'testat', $cb_data[3]);
		} else if(strstr($cb_name, 'final')){
			$this->kursverwaltung_model->update_group_cbs($cb_data[1], $cb_data[2], 'gesamttestat');
		} else if(strstr($cb_name, 'disable')){
			$this->kursverwaltung_model->update_group_cbs($cb_data[1], $cb_data[2], 'ende');
		}
		
	}
	

	/**
	 * Helper function to get the notes stored for a single lab-participant.
	 */
	public function ajax_get_former_participant_notes(){
		$participant_id = $this->input->post('participant_id');
		echo $this->kursverwaltung_model->get_participant_notes($participant_id);
	}
	
	
	/**
	 * Getting user-notes from view and save to db.
	 * Array passed:
	 * - array[0]: user_id
	 * - array[1]: user_notes
	 * 
	 */
	public function ajax_save_lab_notes(){
		$user_notes = $this->input->post('participant_notes');
		$this->kursverwaltung_model->update_group_notes($user_notes[0], $user_notes[1]);
		// only return something - method-detects change
		echo 'done';
	}
	
	
	/**
	 * Helper function to reload the view with the correct tab activated.
	 * Course-id fetched from db, because more complicated to get inside view at that point.
	 * 
	 */
	public function save_and_reload_lab_mgt_group(){
		$sp_course_id = $this->input->post('sp_course_id');
		$text_xtra_1 = $this->input->post('xtra_event_1');
		$text_xtra_2 = $this->input->post('xtra_event_2');
		$number_of_events = $this->input->post('number_of_events');
		
		// save passed data
		$this->kursverwaltung_model->update_xtra_event($sp_course_id, $text_xtra_1, $text_xtra_2, $number_of_events);
			
		$course_id_to_show = $this->kursverwaltung_model->get_course_id_for_spkursid($sp_course_id);

		$this->session->set_flashdata('sp_course_id', $sp_course_id);
		$this->session->set_flashdata('course_id', $course_id_to_show->KursID);
		
		// goto view
		redirect('kursverwaltung/show_labmgt_group');
	}
	
	
	/**
	 * Saving new date for one event.
	 * Data passed via POST contains array
	 * array[0] - sp_course_id
	 * array[1] - event_id !! +1 because of array-index
	 * array[2] - day
	 * array[3] - month
	 * array[4] - year
	 * 
	 */
	public function ajax_save_new_date_for_event(){
		$save_data = $this->input->post('save_event_data');
		
		// building date to be stored in db
		$store_date = '';
		$store_date = $save_data[4].'-'.$save_data[3].'-'.$save_data[2];
		
		$this->kursverwaltung_model->update_eventdate($save_data[0], $save_data[1]+1, $store_date);
	}


//	/**
//	 * Saving new text for an extra-event.
//	 * Data passed via POST contains array
//	 * array[0] - sp_course_id
//	 * array[1] - text - new text to store
//	 * array[2] - event - 1 or 2
//	 *
//	 */
//	public function ajax_save_xtra_event(){
//		$sp_course_id = '';
//		$text = '';
//
//		$data = $this->input->post('new_data');
//		$sp_course_id = $data[0];
//		$text = $data[1];
//		$event = $data[2];
//
//		// save to db
//		$this->kursverwaltung_model->update_xtra_event($sp_course_id, $text, $event);
//
//		// reload page - otherwise there are cache-problems with input-fields
//		$this->reload_lab_mgt_group($sp_course_id);
//	}
	

	/* 
	 * 
	 * ******************************** Praktikumsverwaltung
	 * ************************************** Frank Gottwald
	 * 
	 * ***********************************************************************/
	
	
	
}


?>
