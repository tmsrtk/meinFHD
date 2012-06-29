<?php

class Kursverwaltung extends FHD_Controller {

    

    function __construct(){
	parent::__construct();
	
	$this->load->model('kursverwaltung_model');
	$this->load->model('admin_model'); // TODO: daten müssen aus session kommen
	
	//// data
	// userdata
	$session_userid = 1357;

	$loginname = $this->admin_model->get_loginname($session_userid); 				///////////////////////////////
	$user_permissions = $this->admin_model->get_all_userpermissions($session_userid);
	$roles = $this->admin_model->get_all_roles();

	$userdata = array(
			'userid' => $session_userid,
			'loginname' => $loginname['LoginName'],
			'userpermissions' => $user_permissions,
			'roles' => $roles
		);

	$this->data->add('userdata', $userdata);

    }
    
    
    function show_kursmgt(){
	// get data to show in view
	// course-details 
	// time array for dropdown
	// day array for dropdown
	// array with all possible 
	// times

	$subview_data['starttime_options'] = $this->helper_model->get_dropdown_options('starttimes');
	$subview_data['endtime_options'] = $this->helper_model->get_dropdown_options('endtimes');
	$subview_data['day_options'] = $this->helper_model->get_dropdown_options('days');
	
	// add views to data corresponding to user_role
//	if(in_array('dozent', $user_model->get_all_roles()) || in_array('betreuer', $user_model->get_all_roles())){
//	    $person_data['tutor'] = '0';
//	    $data['persons'] = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $person_data, TRUE);
//
//	    // data for subviews
//	    $subview_data['lab'] = '0';
//	    //get person-overview view
//	    $data['lecture'] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//	    $data['tut'] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//
//	    // data for subviews
//	    $subview_data['lab'] = '1';
//	    // foreach lab - save indexed to data
//	    $data['lab'][] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//	    $data['lab'][] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//	    $data['lab'][] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//	    $data['lab'][] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);
//	} else {
	    // user is tutor
	    $person_data['tutor'] = '1';
	    $persons = $this->load->view('kursverwaltung-subviews/kursverwaltung_persons', $person_data, TRUE);

	    // data for subviews
	    $subview_data['lab'] = '0';
	    //get person-overview view
	    $lecture = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture_tut', $subview_data, TRUE);
	    $tut = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture', $subview_data, TRUE);

	    // data for subviews
	    $subview_data['lab'] = '1';
	    // foreach lab - save indexed to data
	    $lab[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture_tut', $subview_data, TRUE);
	    $lab[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture_tut', $subview_data, TRUE);
	    $lab[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture_tut', $subview_data, TRUE);
	    $lab[] = $this->load->view('kursverwaltung-subviews/kursverwaltung_lecture_tut', $subview_data, TRUE);
//	}	

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
