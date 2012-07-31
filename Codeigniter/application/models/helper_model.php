<?php

/**
 * Provides data that is needed in several views
 * i.e. dropdown data for times, days, are there any more??
 * @author frank gottwald
 * 
 */

class Helper_model extends CI_Model {
    
    public function __construct() {
		parent::__construct();
    }
    
    /**
     * Returns array holding data for form_dropdown(...)
	 * Called from Timetable-Mgt. $ Course-Mgt.
     * @param String $type - so far 'starttimes', 'endtimes' or 'days'
     * @return array holding all OPTIONS for drowpdown - can be used directly in 
     * >> form_dropdown('name', $OPTIONS, $val, $attrs);
     * @return string
     */
    public function get_dropdown_options($type){
		$data = '';
		$name = '';
		switch ($type) {
			case 'starttimes' : 
			$name = 'Beginn';
			$data = $this->get_dropdown_data($name, 'stunde');
			break;
			case 'endtimes' : 
			$name = 'Ende';
			$data = $this->get_dropdown_data($name, 'stunde');
			break;
			case 'days' : 
			$name = 'TagName';
			$data = $this->get_dropdown_data($name, 'tag');
			break;
		}
		// run through data and build options-array
		for($i = 0; $i < count($data); $i++){
//			if($i != 0){
			$options[$i] = $data[$i]->$name;
//			} else {
//			$options[$i] = '';
//			}
		}
		return $options;
    }
    
    /**
     * 
     * Returns all start and end times for dropdown - depending on 
     * @return type
     */
    private function get_dropdown_data($type, $table){
		$this->db->select($type);
		$q = $this->db->get($table);

//		$data[] = null;

		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
			$data[] = $row;
			}
			return $data;
		}
    }
    
    
    public function get_course_name($course_id){
		$this->db->select('Kursname');
		$q = $this->db->get_where('studiengangkurs', $course_id);

		if($q->num_rows() == 0){
			foreach ($q->result() as $row){
			return $row;
			}
		}
	
    }
}

?>
