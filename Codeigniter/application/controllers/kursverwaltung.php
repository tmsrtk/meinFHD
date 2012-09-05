<?php

class Kursverwaltung extends FHD_Controller {

    private $permissions;
    private $roles;
    private $roleIds;
    private $course_ids;
    
    // eventtype_ids
    const LECTURE = 1;
    const LAB_SEM = 3;
    const LAB_UEB = 2;
    const LAB_PRA = 4;
    const TUT = 6;
    
    // tables to switch between data
    const LABING = 'laboringenieur';
    const TUTOR = 'tutor';
    

    function __construct(){
		parent::__construct();
		$this->load->model('kursverwaltung_model');
        $this->load->model('logbuch_model'); // edit by CK load the logbuch model

		// get all roles the user has
		$this->roleIds = $this->user_model->get_all_roles();
		// get course ids for that user
		$this->course_ids = $this->user_model->get_user_course_ids();

    }
    
    
    function show_coursemgt(){
		// if user has courses - run through all of them
		if($this->course_ids){

			$course_names_ids = array();

			// getting course_ids
			$course_ids = $this->course_ids;


			// getting short-names labeling
			foreach ($course_ids as $cid => $role) {
				// comes from studiengangkurs
		//		$course_names_ids[$cid] = $this->kursverwaltung_model->get_lecture_name($cid)->kurs_kurz;
				$course_names_ids[$cid] = $this->kursverwaltung_model->get_lecture_name($cid);
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

			// get data for each course
			foreach($this->course_ids as $id => $role){
				$name = array(); // init

				// save course_id and course_description for partial view
				$staff_view_data['course_id'] = $id;
				$staff_view_data['course_description'] = $course_names_ids[$id]->Beschreibung;

                // edit by CK: get all course topics for the current course and pass them to the view
                $staff_view_data['course_topics'] = implode("\n", $this->logbuch_model->get_all_base_topics($id));

				// get staff-overview view
				// get active staff
				$name = $this->kursverwaltung_model->get_profname_for_course($id);
				if($name){
					$staff_view_data['prof'] = $name[0].' '.$name[1].' '.$name[2];
				} else {
					$staff_view_data['prof'] = 'keine Angabe';
				}
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
		//		$course_data[$id][] = $this->load->view('courses/partials/courses_staff', $staff_view_data, TRUE); // old version - bound to array
				$staff[$id] = $this->load->view('courses/partials/courses_staff', $staff_view_data, TRUE);
		//		$staff[$id] = $this->load->view('courses/partials/courses_staff_dummy', '', TRUE); // DEBUG VIEW

				// get view for each eventtype
				$eventtypes = $this->kursverwaltung_model->get_eventtypes_for_course($id);
				foreach($eventtypes as $e){
					// must be an array because (final) view (courses_show.php) runs data in foreach loop 
					$course_data[$id][] = $this->get_course_event_view($id, $e, $subview_data, $subview_lecture_to_load);
				}
				
				// getting description depending on role
				$description_field[$id] = $this->load->view('courses/partials/courses_description', $staff_view_data, TRUE);

                // edit by CK: load de coursse_topic-view to show the course topics depending on roles
                $topic_field[$id] = $this->load->view('courses/partials/courses_topics', $staff_view_data, TRUE);

				$this->data->add('staff', $staff);
				$this->data->add('course_details', $course_data);
				$this->data->add('description', $description_field);
                // edit by CK: add topic data
                $this->data->add('topics', $topic_field);
				$this->data->add('offset', 0);
			}

			$this->load->view('courses/courses_show', $this->data->load());

		} else {
			// no courses assigned view
			$this->load->view('courses/courses_no', $this->data->load());
		}
    }
    
    
    /**
     * Returns part of the course view for ONE lecture, lab, tut
     * Depending on eventtype that was passed.
     * @param type $course_id
     * @param type $eventtype
     * @param type $subview_data
     * @param type $subview_to_load
     * @return type
     */
    private function get_course_event_view($course_id, $eventtype, $subview_data, $subview_to_load){
	
		// init
		$subview_data['is_lab'] = FALSE;
		$subview_data['is_tut'] = FALSE;
		$subview_data['lecture_name'] = $this->kursverwaltung_model->get_lecture_name($course_id);
		$subview_data['course_id'] = $course_id;
		$subview_data['current_participants'] = '';

		switch($eventtype) {
			case Kursverwaltung::LECTURE :
				// data for subviews
				$subview_data['headline'] = 'Vorlesung';

				//get lecture-data - only changable for NO-tuts
				$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
				$subview_data['current_participants'] = $this->kursverwaltung_model->count_participants_for_course($subview_data['lecture_details']->SPKursID, FALSE);
				$lecture = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
				$lecture .= $this->load->view($subview_to_load, $subview_data, TRUE);
				return $lecture;
			case Kursverwaltung::LAB_SEM :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = 'Seminar';
				return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::LAB_UEB :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = 'Übung';
				return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::LAB_PRA :
				$subview_data['is_lab'] = TRUE;
				$subview_data['headline'] = 'Praktikum';
				return $this->get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data);
			case Kursverwaltung::TUT :
				$subview_data['is_tut'] = TRUE;
				$subview_data['headline'] = 'Tutorium';
				// get tut data view - always visible to every role
				$subview_data['lecture_details'] = $this->kursverwaltung_model->get_lecture_details($course_id, $eventtype);
				$tut = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
				// !! view has to be courses_lecture because tut should be able to save changes in tut
				$tut .= $this->load->view('courses/partials/courses_lecture', $subview_data, TRUE);
				return $tut;
		}
	
    }
    
    /**
     * Returns lab-part of course-view
     * Necessary because of headlines >> tablehead
     * @param int $course_id
     * @param int $eventtype
     */
    private function get_lab_view($course_id, $eventtype, $subview_to_load, $subview_data){
		$lab = array(); // init/clean
		
		// data for subviews
		$subview_data['lab'] = '1';
		$subview_data['current_participants'] = '';
		$subview_data['download_file'] = '';
		
		//get lab-data-view (
		// get data from db - array containing lab-data
		$lab_details = $this->kursverwaltung_model->get_lab_details($course_id, $eventtype);
		$lab[] = $this->load->view('courses/partials/courses_tablehead', $subview_data, TRUE);
		foreach($lab_details as $details){
	//	    echo '<pre>';
	//	    print_r($l);
	//	    echo '</pre>';
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
			$lab[] = $this->load->view($subview_to_load, $subview_data, TRUE);
		}
		return $lab;
    }
    
//    
//    private function get_headlines_for_view($eventtype){
//	switch($eventtype){
//	    case Kursverwaltung::LECTURE : 
//		return 'Vorlesung';
//	    case Kursverwaltung::LAB_SEM : 
//		return 'Seminar';
//	    case Kursverwaltung::LAB_UEB : 
//		return 'Uebung';
//	    case Kursverwaltung::LAB_PRA : 
//		return 'Praktikum';
//	    case Kursverwaltung::TUT : 
//		return 'Tutorium';
//		break;
//	}
//    }
//    
//    
    
//    ############################################################## SAVING DATA
    
//    /**
//     * outdated / needed, if just one course should be saved
//     */
//    public function save_course_details(){
//
//		// update database with new data
//		// !! >> number of participants has to be store in gruppe
//		$input_data = $this->input->post();
//		echo '<pre>';
//		echo '<div>POST</div>';
//		print_r($input_data);
//		echo '</pre>';
//
//		$save_course_details_to_db = array(); // init
//		$save_group_details_to_db = array(); // init
//		$sp_course_id = ''; // init
//		$t = '';
//
//		// run through input
//		foreach ($input_data as $key => $value) {
//			// get key and field-name
//			$split_key = explode('_', $key);
//			// save spkursid
//			$sp_course_id = $split_key[0];
//			
//			// prepare data for saving - starttime, endtime and day has to be mapped from array-index to ID (+1)
//			switch ($split_key[1]) {
//				case 'TeilnehmerMax' : $save_group_details_to_db['TeilnehmerMax'] = $value; break;
//				case 'Raum' : $save_course_details_to_db[$split_key[1]] = $value; break;
//				default : $save_course_details_to_db[$split_key[1]] = $value+1; break;
//			}
//		}
//
//		// save that data
//		$this->kursverwaltung_model->save_course_details(
//			$sp_course_id, $save_course_details_to_db, $save_group_details_to_db);
//
//
//		$this->show_coursemgt();
//	
//    }
    
    /**
     * Alternative function to save all details at once.
     * 
     */
    public function save_course_details_all_at_once(){
		$input_data = $this->input->post();
//		echo '<pre>';
//		echo '<div>POST</div>';
//		print_r($input_data);
//		echo '</pre>';

		// init
		$input_data_filtered = array();
		$sp_course_id = 0; // init to detect changes
		$sp_course_id_temp = 0;
		$save_course_details_to_db = array();

		$save_group_details_to_db = array();
		$desc_split = array();
		$course_id = '';
		$description = '';

        // edit by CK
        // filter -> save course topics and remove them from the input array
        $input_data = $this->_save_course_topics($input_data);

		// first filter
		// - remove empty fields from email-checkboxes
		// - get description
		foreach ($input_data as $key => $value) {
			// empty fields
			if($value !== ''){
				// description
				if(!strstr($key, 'description')){
					$input_data_filtered[$key] = $value;
				}else {
					$desc_split = explode('_', $key);
					$course_id = $desc_split[0];
                    print_r($desc_split);
					$description = $value;
				}
			}
		}

		// run through input
		foreach ($input_data_filtered as $key => $value) {
			// get key and field-name
			$split_key = explode('_', $key);
			// save spkursid
			$sp_course_id = $split_key[0];

			// if sp_course_id changed >> buidl arrays to save in db
			if($sp_course_id !== $sp_course_id_temp){
				// if old spkursid is not initial value 0 - there are data to save
				if($sp_course_id_temp !== 0){

					// save that data each time course_id changes
					$this->kursverwaltung_model->save_course_details(
						$sp_course_id_temp, $save_course_details_to_db, $save_group_details_to_db);
//						echo '<pre>';
//						echo '<div>course</div>';
//						print_r($save_course_details_to_db);
//						echo '<div>group</div>';
//						print_r($save_group_details_to_db);
//						echo '</pre>';

					$save_course_details_to_db = array();
					$save_group_details_to_db = array();

				}
				$sp_course_id_temp = $sp_course_id;
			}
			// prepare data for saving - starttime, endtime and day has to be mapped from array-index to ID (+1)
			switch ($split_key[1]) {
				case 'TeilnehmerMax' : $save_group_details_to_db['TeilnehmerMax'] = $value; break;
				case 'Raum' : $save_course_details_to_db[$split_key[1]] = $value; break;
				default : $save_course_details_to_db[$split_key[1]] = $value+1; break;

			}
		}

		// save that data - a last time
		// >> because last data won't be detected by change of course_id (there is no new course-id)
		$this->kursverwaltung_model->save_course_details(
			$sp_course_id_temp, $save_course_details_to_db, $save_group_details_to_db);

		$this->kursverwaltung_model->save_course_description($course_id, $description);
		
//		echo '<pre>';
//		echo '<div>course</div>';
//		print_r($save_course_details_to_db);
//		echo '<div>group</div>';
//		print_r($save_group_details_to_db);
//		echo '<div>id+desc</div>';
//		print_r($course_id.' '.$description);
//		echo '</pre>';


	//	echo '<pre>';
	//	echo '<div>POST</div>';
	//	print_r($input_data_filtered);
	//	echo '</pre>';

//		$this->show_coursemgt();
		redirect('kursverwaltung/show_coursemgt');
    }
    
    
    /**
     * Switch: calls correct method to save to LABORINGENIEUR-table
     */
    public function save_labings_for_course(){
		// get incoming data
		$staff_to_save = $this->input->post();
		$this->save_staff_to_db('laboringenieur', $staff_to_save);
    }
    
    
    /**
     * Switch: calls correct method to save to TUTOR-table
     */
    public function save_tuts_for_course(){
		// get incoming data
		$staff_to_save = $this->input->post();
		$this->save_staff_to_db('tutor', $staff_to_save);
    }

    /**
     * Passes data to db to save it.
     * Passes data to db to save it.
     * @param String $table indicates table to which staff should be saved
     */
    private function save_staff_to_db($table, $staff){
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


		// back to view
		$this->show_coursemgt();
    }
    
    


	/**
	 * TODO - not working at the moment
	 * Returns participants of a sp_course group!! (via POST) to show in any dom-element.
	 */
	public function ajax_get_participants_of_sp_course(){
		$sp_course_id = $this->input->post('sp_course_id');
		
		echo $sp_course_id;
		
	}
	
	
	
	/**
	 * Create file with participants for SP!! Course
	 */
	public function ajax_create_participants_file_sp_course(){
		$id = $this->input->post('sp_course_id');
		echo $this->download_course_participants($id, TRUE);
	}
	
	/**
	 * Create file with participants for Course
	 */
	public function ajax_create_participants_file_course(){
		$id = $this->input->post('sp_course_id');
		echo $this->download_course_participants($id, FALSE);
	}
	
	
	/**
	 * Depending on the method the call comes from a file with sp_course/ course-participants will be created
	 * @param type $id
	 * @param type $is_spcourse
	 */
	private function download_course_participants($id, $is_spcourse){
		
		// create file
		$data = $this->kursverwaltung_model->create_file_with_participants_for_course($id, $is_spcourse);
		
		$file = '';
		$file_name = 'gruppe-'.md5($id).'.csv';
		$file_path = './resources/userfiles/downloads/'.$file_name;
		
		if(file_exists($file_path)){
			unlink($file_path);
		}
		
		// create file
		$file = fopen($file_path, 'a+');
		
		// and fill
		if(is_writable($file_path)){
			fwrite($file, $data);
		}
		
		// close
		fclose($file);
		chmod($file_path, 0640);

//		// prompt user to download file
//		header('Content-type: application/csv');
//		header('Content-Disposition: attachment; filename="download.csv"');
//		readfile($file_path);

		shell_exec('start '.$file_path);
	}
	
	
	public function ajax_toggle_activation_of_spcourse(){
		// TODO
		$data = '';
		$data = $this->input->post('course_id_status');
		// sp_course_id = $data[0]; status (enabled/disabled) = $data[1]
		
		
		// update benutzerkurs
		if($data[1] == 'enabled'){
			// means before it was ENABLED >> DISABLE registration
			$this->kursverwaltung_model->update_benutzerkurs_activation($data[0], FALSE);
		} else {
			// means before it was DISABLED >> ENABLE registration
			$this->kursverwaltung_model->update_benutzerkurs_activation($data[0], TRUE);
		}
		
	}

    /**
     * Removes course topics from input array, returns the array and saves the topics in the database.
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
     * Used to populate benutzer_mm_rolle-table
     */
//    function update_benutzermmrolle(){
//	$this->kursverwaltung_model->update_benutzermmrolle();
//    }
       
    
}


?>
