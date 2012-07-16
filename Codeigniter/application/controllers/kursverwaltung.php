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
	$person_view_data['is_tutor'] = false;
	$subview_to_load = '';
	
	// switch view - depends on if user is tutor or not
	// TODO? hard coded ints at the moment - perhaps better via function?
	if(in_array(2, $this->roleIds) || in_array(3, $this->roleIds)){
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture';
	} else {
	    $person_view_data['is_tutor'] = true;
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture_tut';
	}


	// if user has courses - run through all of them
	if($this->course_ids){

	    // get data for each course
	    foreach($this->course_ids as $id => $role){
		$person_view_data['course_id'] = $id;
		// get person-overview view
		// get active persons
		$name = $this->kursverwaltung_model->get_profname_for_course($id);
		$person_view_data['prof'] = $name[0].' '.$name[1].' '.$name[2];
		$person_view_data['current_labings'] = $this->kursverwaltung_model->get_current_labings_for_course($id);
		$person_view_data['possible_labings'] = $this->kursverwaltung_model->get_all_possible_labings();
		$course_data[$id][] = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $person_view_data, TRUE);
		
		// get view for each eventtype
		$eventtypes = $this->kursverwaltung_model->get_eventtypes_for_course($id);	
		foreach($eventtypes as $e){
		    // must be an array because view runs data in foreach loop 
		    $course_data[$id][] = $this->get_course_event_view($id, $e, $subview_data, $subview_to_load);
		}
		
//		echo '<pre>';
//		print_r($person_view_data['current_labings']);
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
	
	switch($eventtype) {
	    case Kursverwaltung::LECTURE :
		// data for subviews
		$subview_data['lab'] = '0';
		$subview_data['tut'] = '0';
		$subview_data['headline'] = 'Vorlesung';

		//get lecture-data - only changable for NO-tuts
		$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
		$lecture = $this->load->view('kursverwaltung-subviews/kursverwaltung_tablehead', $subview_data, TRUE);
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
		$tut = $this->load->view('kursverwaltung-subviews/kursverwaltung_tablehead', $subview_data, TRUE);
		$tut .= $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
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
	$lab[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_tablehead', $subview_data, TRUE);
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
    
    
    
    
    
    /**
     * Used to populate benutzer_mm_rolle-table
     */
//    function update_benutzermmrolle(){
//	$this->kursverwaltung_model->update_benutzermmrolle();
//    }
       
    
}


?>
