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
	// get courses for that prof
	$this->course_ids = $this->user_model->get_user_course_ids();

    }
    
    
    function show_coursemgt(){
	
	// dropdown data
	$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
	$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
	$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');
	
	// role ? static views of forms
	$role['role_tutor'] = 0;
	$subview_to_load = '';
	
	// switch if user is tutor or not
	// TODO? hard coded ints at the moment - perhaps better via function
	if(!in_array(2, $this->roleIds) && !in_array(3, $this->roleIds)){
	    $role['role_tutor'] = '1';
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture_tut';
	} else {
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture';
	}


	// if user has courses - run through all of them
	if($this->course_ids){
	    // get data foreach course
	    foreach($this->course_ids as $id){
		$headlines = array(); // init / clean
		
		//get person-overview view
		// TODO get people and hand them into view
		$course_data[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $role, TRUE);
		
		// get view for each eventtype
		$eventtypes = $this->kursverwaltung_model->get_eventtypes_for_course($id);	
		foreach($eventtypes as $e){
		    // must be an array because view runs data in foreach loop 
		    $course_data[] = $this->get_course_event_view($id, $e, $subview_data, $subview_to_load);
		}
		$this->data->add('course_data', $course_data);
		$this->data->add('offset', 0);
//		echo '<pre>';
//		print_r($headlines);
//	        echo '</pre>';
	    }
	    
	    // show view
	    $siteinfo = array(
		'title' => 'Kursverwaltung',
		'main_content' => 'kursverwaltung/kursverwaltung_uebersicht'
	    );
	    $this->data->add('siteinfo', $siteinfo);
	    $this->load->view('includes/template', $this->data->load());
	    
	} else {
	    // no courses assigned view
	    $siteinfo = array(
		'title' => 'Kursverwaltung',
		'main_content' => 'kursverwaltung/kursverwaltung_no_courses'
	    );
	    $this->data->add('siteinfo', $siteinfo);
	    $this->load->view('includes/template', $this->data->load());
	}
    }
    
    
    function get_course_event_view($course_id, $eventtype, $subview_data, $subview_to_load){
	
	// init
	$subview_data['lab'] = '';
	
	switch($eventtype) {
	    case Kursverwaltung::LECTURE :
		// data for subviews
		$subview_data['lab'] = '0';
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
	foreach($lab_details as $l){
	    $subview_data['lecture_details'] = $l;
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
       
    
}


?>
