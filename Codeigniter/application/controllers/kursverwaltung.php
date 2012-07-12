<?php

class Kursverwaltung extends FHD_Controller {

    private $permissions;
    private $roles;
    private $roleIds;
    private $course_ids;
    private $course_ids_labing;
    private $course_ids_tut;
    
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
	// TODO what if that prof is prof for one course and labing for another?!?!?
	// >> both course-ids has to be stored separatly
	// or does the system do not pretend a possiblity to be prof and labing at one time?!?!!?!?!?!?!!?!?!?!
	// if not - no problem - user is either prof or labing or tut - first implementation like said before
	// REALLY IMPORTANT - AT THE MOMENT A PROF, LABING, TUT CAN ONLY HAVE ONE COURSE-RELEVANT ROLE!!!!
//	$this->course_ids = $this->user_model->get_user_course_ids();
//	$this->user_model->get_user_course_ids();
	$this->course_ids = array(301);
	
//	echo '<pre>';
//	print_r($this->course_ids);
//	echo '</pre>';

    }
    
    
    function show_coursemgt(){
	
	// dropdown data
	$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
	$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
	$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');
	
	// additional data
//	$subview_data['course_name'] = $this->helper_model->get_
	
	// role ? static views of forms
	$person_view_data['role_tutor'] = 0;
	$subview_to_load = '';
	
	// switch if user is tutor or not
	// TODO? hard coded ints at the moment - perhaps better via function
	if(!in_array(2, $this->roleIds) && !in_array(3, $this->roleIds)){
	    $person_view_data['role_tutor'] = '1';
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
		$person_view_data['possible_labings'] = $this->kursverwaltung_model->get_all_possible_labings();
		$course_data[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $person_view_data, TRUE);
		
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
