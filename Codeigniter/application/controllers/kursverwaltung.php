<?php

class Kursverwaltung extends FHD_Controller {

    private $permissions;
    private $roles;
    private $roleIds;

    function __construct(){
	parent::__construct();
	$this->load->model('kursverwaltung_model');
	
	// Daten holen - Alle Rollen mit Bezeichnung, Alle Berechtigungen mit Bezeichnung, gesondert die RoleIds
	$this->roleIds = $this->user_model->get_all_roles();

    }
    
//	    echo '<pre>';
//	    print_r($subview_data['lecture_details']);
//	    echo '</pre>';
    
    function show_coursemgt(){
	// get data to show in view
	// course-details 
	// time array for dropdown
	// day array for dropdown
	// array with all possible 
	// times

	$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
	$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
	$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');
	
	$role['role_tutor'] = 0;
	$subview_to_load = '';
	
	// switch if user is tutor or not
	if(!in_array(2, $this->roleIds) && !in_array(3, $this->roleIds)){
	    $role['role_tutor'] = '1';
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture_tut';
	} else {
	    $subview_to_load = 'kursverwaltung-subviews/kursverwaltung_lecture';
	}

	//get person-overview view
	$persons = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $role, TRUE);

	// data for subviews
	$subview_data['lab'] = '0';

	//get lecture-data - only visible to NO-tuts
	$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details(302, 1);
	$lecture = $this->load->view($subview_to_load, $subview_data, TRUE);

	// get tut data view - always visible to every role
	$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details(302, 6);
	$tut = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);

	// data for subviews
	$subview_data['lab'] = '1';
	//get lab-data-view
	// get data from db - array containing more than one 
	// TODO what if there are labs with only one date ?!?!?!
	$lab_details = $this->kursverwaltung_model->get_lab_details(302, 4);
	foreach($lab_details as $l){
	    $subview_data['lecture_details'] = $l;
	    $lab[] = $this->load->view($subview_to_load, $subview_data, TRUE);
	}

	// add data to view
	$this->data->add('persons', $persons);
	$this->data->add('lecture', $lecture);
	$this->data->add('tut', $tut);
	$this->data->add('lab', $lab);
	
	$siteinfo = array(
	    'title' => 'Kursverwaltung',
	    'main_content' => 'kursverwaltung/kursverwaltung_uebersicht'
	);
	$this->data->add('siteinfo', $siteinfo);

	$this->load->view('includes/template', $this->data->load());
    }    
    
    
}


?>
