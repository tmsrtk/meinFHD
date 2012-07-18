<?php

class Kursverwaltung extends FHD_Controller {

    private $permissions;
    private $roles;
    private $roleIds;
    private $course_ids;
    
    // eventtype_ids
    const LECTURE = 1;
    const LAB_SEM = 2;
    const LAB_UEB = 3;
    const LAB_PRA = 4;
    const TUT = 6;
    
    // tables to switch between data
    const LABING = 'laboringenieur';
    const TUTOR = 'tutor';
    

    function __construct(){
	parent::__construct();
	$this->load->model('kursverwaltung_model');
	
	// get all roles the user has
	$this->roleIds = $this->user_model->get_all_roles();
	// get course ids for that user
	$this->course_ids = $this->user_model->get_user_course_ids();
	
    }
    
    
    function show_coursemgt(){

	// getting course_ids
	$course_ids = $this->course_ids;
	
	// getting short-names labeling
	foreach ($course_ids as $cid => $role) {
	    $course_names_ids[$cid] = $this->kursverwaltung_model->get_lecture_name($cid)->kurs_kurz;
	}
	
	// add course_names to view
	$this->data->add('course_names_ids', $course_names_ids);
	
	// dropdown data
	$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
	$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
	$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');
	
	// init variables
	$staff_view_data['is_tutor'] = false;
	$subview_lecture_to_load = '';
	
	// switch view - depends on if user is tutor or not
	// TODO? hard coded ints at the moment - perhaps better via function?
	if(in_array(2, $this->roleIds) || in_array(3, $this->roleIds)){
	    $subview_lecture_to_load = 'courses/partials/courses_lecture';
	} else {
	    $staff_view_data['is_tutor'] = true;
	    $subview_lecture_to_load = 'courses/partials/courses_lecture_tut';
	}


	// if user has courses - run through all of them
	if($this->course_ids){

	    // get data for each course
	    foreach($this->course_ids as $id => $role){
		$staff_view_data['course_id'] = $id;
		// get staff-overview view
		
		// get active staff
		$name = $this->kursverwaltung_model->get_profname_for_course($id);
		$staff_view_data['prof'] = $name[0].' '.$name[1].' '.$name[2];
		$staff_view_data['current_labings'] = 
		    $this->kursverwaltung_model->get_current_labings_tuts_for_course($id, Kursverwaltung::LABING);
		$staff_view_data['possible_labings'] = 
		    $this->kursverwaltung_model->get_all_possible_labings();
		$staff_view_data['current_tuts'] = 
		    $this->kursverwaltung_model->get_current_labings_tuts_for_course($id, Kursverwaltung::TUTOR);
		$staff_view_data['possible_tuts'] = 
		    $this->kursverwaltung_model->get_all_tuts();
		
		// get labing/tut partials for printing checkbox-panels
		$staff_view_data['print_tuts'] = false;
		$staff_view_data['labing_panel'] =
		    $this->load->view('courses/partials/courses_staff_cb_panel', $staff_view_data, TRUE);
		$staff_view_data['print_tuts'] = true;
		$staff_view_data['tut_panel'] =
		    $this->load->view('courses/partials/courses_staff_cb_panel', $staff_view_data, TRUE);
		
		// get staff-view
		$course_data[$id][] = $this->load->view('courses/partials/courses_staff', $staff_view_data, TRUE);
		
		// get view for each eventtype
		$eventtypes = $this->kursverwaltung_model->get_eventtypes_for_course($id);	
		foreach($eventtypes as $e){
		    // must be an array because view runs data in foreach loop 
		    $course_data[$id][] = $this->get_course_event_view($id, $e, $subview_data, $subview_lecture_to_load);
		}
		
//		echo '<pre>';
//		print_r($staff_view_data['current_labings']);
//		echo '</pre>';

		$this->data->add('course_details', $course_data);
		$this->data->add('offset', 0);
	    }
	    
	    $siteinfo = array(
		'title' => 'Kursverwaltung',
		'main_content' => 'courses/courses_show'
	    );
	    $this->data->add('siteinfo', $siteinfo);

	    $this->load->view('includes/template', $this->data->load());
	    
	} else {
	    // no courses assigned view
	    $siteinfo = array(
		'title' => 'Kursverwaltung',
		'main_content' => 'courses/courses_no'
	    );
	    $this->data->add('siteinfo', $siteinfo);
	    $this->load->view('includes/template', $this->data->load());
	}
    }
    
    
    function get_course_event_view($course_id, $eventtype, $subview_data, $subview_to_load){
	
	// init
	$subview_data['lab'] = '';
	$subview_data['lecture_name'] = $this->kursverwaltung_model->get_lecture_name($course_id);
	$subview_data['course_id'] = $course_id;
	
	switch($eventtype) {
	    case Kursverwaltung::LECTURE :
		// data for subviews
		$subview_data['lab'] = '0';
		$subview_data['tut'] = '0';
		$subview_data['headline'] = 'Vorlesung';

		//get lecture-data - only changable for NO-tuts
		$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
		$lecture = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
		$lecture .= $this->load->view($subview_to_load, $subview_data, TRUE);
		return $lecture;
	    case Kursverwaltung::LAB_SEM :
		$subview_data['headline'] = 'Seminar';
		return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
	    case Kursverwaltung::LAB_UEB :
		$subview_data['headline'] = 'Übung';
		return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
	    case Kursverwaltung::LAB_PRA :
		$subview_data['headline'] = 'Praktikum';
		return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
	    case Kursverwaltung::TUT :
		$subview_data['tut'] = '1';
		$subview_data['headline'] = 'Tutorium';
		// get tut data view - always visible to every role
		$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
		$tut = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
		$tut .= $this->load->view('courses/partials/courses_lecture', $subview_data, TRUE);
		return $tut;
	}
	
    }
    
    /**
     * Returns lab-part of course-view - necessary because of headlines
     * @param int $course_id
     * @param int $eventtype
     */
    function get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data){
	$lab = array(); // init/clean
	// data for subviews
	$subview_data['lab'] = '1';
	//get lab-data-view (
	// get data from db - array containing lab-data
	$lab_details = $this->kursverwaltung_model->get_lab_details($course_id, $eventtype);
	$lab[] = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
	foreach($lab_details as $details){
//	    echo '<pre>';
//	    print_r($l);
//	    echo '</pre>';
	    $subview_data['lecture_details'] = $details;
	    $lab[] = $this->load->view($subview_to_load, $subview_data, TRUE);
	}
	return $lab;
    }
    
    
    function get_headlines_for_view($eventtype){
	switch($eventtype){
	    case Kursverwaltung::LECTURE : 
		return 'Vorlesung';
	    case Kursverwaltung::LAB_SEM : 
		return 'Seminar';
	    case Kursverwaltung::LAB_UEB : 
		return 'Uebung';
	    case Kursverwaltung::LAB_PRA : 
		return 'Praktikum';
	    case Kursverwaltung::TUT : 
		return 'Tutorium';
		break;
	}
    }
    
    
    
//    ############################################################## SAVING DATA
    
    /**
     * 
     */
    function save_course_details(){
	
	// TODO update database with new data >> number of participants has to be store in gruppe
	$input_data = $this->input->post();
	
	$save_course_details_to_db = array(); // init
	$save_group_details_to_db = array(); // init
	$sp_course_id = ''; // init
	$t = '';
	
	// run through input
	foreach ($input_data as $key => $value) {
	    // get key and field-name
	    $split_key = explode('_', $key);
	    // save spkursid
	    $sp_course_id = $split_key[0];
	    if($split_key[1] != 'TeilnehmerMax'){
		$save_course_details_to_db[$split_key[1]] = $value;
	    } else {
		$save_group_details_to_db['TeilnehmerMax'] = $value;
	    }
	}
	
	// save that data
	$this->kursverwaltung_model->save_course_details(
		$sp_course_id, $save_course_details_to_db, $save_group_details_to_db);
	
	
	$this->show_coursemgt();
	
    }
    
    
    
    function save_labings_for_course(){
	// get incoming data
	$staff_to_save = $this->input->post();
	$this->save_staff_to_db('laboringenieur', $staff_to_save);
//	echo '<pre>';
//	echo '<div>staff to save</div>';
//	print_r($staff_to_save);
//	echo '</pre>';
    }
    
    
    function save_tuts_for_course(){
	// get incoming data
	$staff_to_save = $this->input->post();
	$this->save_staff_to_db('tutor', $staff_to_save);
    }

    /**
     * Passes data to db to save it.
     * @param String $table indicates table to which staff should be saved
     */
    function save_staff_to_db($table, $staff){
	$current_labings_tuts = array(); // init
	$course_id = ''; // init
	
	// if theres only one item - all labings/tuts has been removed
	if(count($staff) === 1){
//	    echo 'if'; // DEBUG
	    // get course_id i.e. first element key
	    reset($staff); // setting pointer to first element - necessary?!?!
	    $course_id = key($staff);
	    // delete staff for that course_id
	    // >> model - passed array is empty
	} else {
//	    echo 'else'; // DEBUG
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
	
	
//	echo '<pre>';
//	echo '<div>current_labings</div>';
//	print_r($current_labings);
//	echo '</pre>';
	
	// back to view
	$this->show_coursemgt();
    }
    
    
    
    /**
     * Used to populate benutzer_mm_rolle-table
     */
//    function update_benutzermmrolle(){
//	$this->kursverwaltung_model->update_benutzermmrolle();
//    }
       
    
}


?>
