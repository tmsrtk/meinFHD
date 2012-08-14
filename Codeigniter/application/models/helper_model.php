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
	
	
	/**
	 * Converts Excel- to rgb-color
	 * @param String $col excel-id of color
	 * @param boolean $light
	 * @return string
	 */
	public function excel_color( $col, $light = false ) 
	{		
		// hellere Farbe
		if( $light ) 
		{		
			// Fallunterscheidung f�r Excel-Farb-IDs
			switch( $col ) 
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// wei�
				case 255: 		$ret = "ffaaaa"; break;	// rot
				case 65280: 	$ret = "aaffaa"; break;	// gr�n
				case 16711680: 	$ret = "aaaaff"; break;	// blau
				case 65535: 	$ret = "ffffaa"; break;	// gelb
				case 16711935: 	$ret = "ffaaff"; break;	// magenta
				case 16776960: 	$ret = "ccffff"; break;	// cyan
				case 128: 		$ret = "baaaaa"; break;
				case 32768: 	$ret = "aabaaa"; break;
				case 8388608: 	$ret = "aaaaba"; break;
				case 32896: 	$ret = "babaaa"; break;
				case 8388736: 	$ret = "baaaba"; break;
				case 8421376: 	$ret = "aababa"; break;
				case 12632256: 	$ret = "cacaca"; break;
				case 8421504: 	$ret = "8a8a8a"; break;
				case 16751001: 	$ret = "ccccff"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 13434879: 	$ret = "fffff0"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 6684774: 	$ret = "99aa99"; break;
				case 8421631: 	$ret = "ffbaba"; break;
				case 13395456: 	$ret = "aa99ff"; break;
				case 16764108: 	$ret = "f0f0ff"; break;
				case 16763904: 	$ret = "aaf0ff"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 13434828: 	$ret = "f0fff0"; break;
				case 10092543: 	$ret = "ffff00"; break;
				case 16764057: 	$ret = "ccf0ff"; break;
				case 13408767: 	$ret = "ffccff"; break;
				case 16751052: 	$ret = "f0ccff"; break;
				case 10079487: 	$ret = "fff5cc"; break;
				case 16737843: 	$ret = "6699ff"; break;
				case 13421619: 	$ret = "66f0f0"; break;
				case 52377: 	$ret = "ccf0aa"; break;
				case 52479: 	$ret = "fff0aa"; break;
				case 39423: 	$ret = "ffcc33"; break;
				case 26367: 	$ret = "ffcc66"; break;
				case 10053222: 	$ret = "9999cc"; break;
				case 9868950: 	$ret = "c9c9c9"; break;
				case 6697728: 	$ret = "aa6699"; break;
				case 6723891: 	$ret = "66cc99"; break;
				case 13209: 	$ret = "cc66aa"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 10040115: 	$ret = "6666cc"; break;
			}
			
		// normale Farbe
		} 
		else 
		{		
			// Fallunterscheidung f�r Excel-Farb-IDs
			switch( $col ) 
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// wei�
				case 255: 		$ret = "ff0000"; break;	// rot
				case 65280: 	$ret = "00ff00"; break;	// gr�n
				case 16711680: 	$ret = "0000ff"; break;	// blau
				case 65535: 	$ret = "ffff00"; break;	// gelb
				case 16711935: 	$ret = "ff00ff"; break;	// magenta
				case 16776960: 	$ret = "00ffff"; break;	// cyan
				case 128: 		$ret = "800000"; break;
				case 32768: 	$ret = "008000"; break;
				case 8388608: 	$ret = "000080"; break;
				case 32896: 	$ret = "808000"; break;
				case 8388736: 	$ret = "800080"; break;
				case 8421376: 	$ret = "008080"; break;
				case 12632256: 	$ret = "c0c0c0"; break;
				case 8421504: 	$ret = "808080"; break;
				case 16751001: 	$ret = "9999ff"; break;
				case 6697881: 	$ret = "993366"; break;
				case 13434879: 	$ret = "ffffcc"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 6684774: 	$ret = "660066"; break;
				case 8421631: 	$ret = "ff8080"; break;
				case 13395456: 	$ret = "0066cc"; break;
				case 16764108: 	$ret = "ccccff"; break;
				case 16763904: 	$ret = "00ccff"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 13434828: 	$ret = "ccffcc"; break;
				case 10092543: 	$ret = "ffff99"; break;
				case 16764057: 	$ret = "99ccff"; break;
				case 13408767: 	$ret = "ff99cc"; break;
				case 16751052: 	$ret = "cc99ff"; break;
				case 10079487: 	$ret = "ffcc99"; break;
				case 16737843: 	$ret = "3366ff"; break;
				case 13421619: 	$ret = "33cccc"; break;
				case 52377: 	$ret = "99cc00"; break;
				case 52479: 	$ret = "ffcc00"; break;
				case 39423: 	$ret = "ff9900"; break;
				case 26367: 	$ret = "ff6600"; break;
				case 10053222: 	$ret = "666699"; break;
				case 9868950: 	$ret = "969696"; break;
				case 6697728: 	$ret = "003366"; break;
				case 6723891: 	$ret = "339966"; break;
				case 13209: 	$ret = "993300"; break;
				case 6697881: 	$ret = "993366"; break;
				case 10040115: 	$ret = "333399"; break;
			}		
		}
		
		// Gebe Farbwert zur�ck
		return $ret; 
	}
}

?>
