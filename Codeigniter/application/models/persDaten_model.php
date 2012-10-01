<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The corresponding Model to the "Persönlichen Einstellungen".
 * Retrieves all sort of informations about the user and his studycourse
 * 
 * Important: 
 * At this state, it considers all needed informations for STUDENTS only,
 * ergo: academics/instructors and tutors are handled as students and can't change their office/room e.g.
 *
 * @author jan
 */
class persDaten_model extends CI_Model{
    
    
    private $userid;
    
    public function __construct()
    {
        parent::__construct();
	
        $this->userid = $this->authentication->user_id();
    }
    
    /**
     * Not used
     * @return type
     */
    private function getUserInfoDoz()
    {
//	Select LoginName, Email, Titel, Vorname, Nachname, Raum, farbschema
//	From benutzer
//	Where benutzerid = $userid
	$this->db->select('LoginName, Titel, Email, Vorname, Nachname, Raum, farbschema')
		->from('benutzer')
		->where('BenutzerID', $this->userid );
	$query = $this->db->get();
	
	return $query->result_array();
    }
    
    /**
     * Returns information about students from the "benutzer"-table. Needs to join the "studiengang"-table too
     * @param type $userid
     * @return type
     */
    public function getUserInfo_old($userid)
    {
	//Select all rows from "benutzer" and the corresponding Pruefungsordnung/Studiengang, if available
	$this->db->select('benutzer.*, studiengang.Pruefungsordnung, studiengang.StudiengangAbkuerzung, studiengang.StudiengangName')
		->from('benutzer')
		->join('studiengang', 'benutzer.studiengangid = studiengang.studiengangid OR ')
		->where('Benutzerid', $userid);
	$query = $this->db->get();
	
	//if the userid has no Studiengang selected, the query above results in an empty join (because there's no benutzer.studiengangid)
	//if that's the case, just select the benutzer-table without the studiengang and insert a placeholder.
	//could possibly be better implemented
	echo $query->num_rows();
	if ($query->num_rows() == 0){
	    $this->db->select('benutzer.*, studiengang.Pruefungsordnung, studiengang.StudiengangAbkuerzung, studiengang.StudiengangName')
		->from('benutzer')
		->join('studiengang', 'benutzer.studiengangid = studiengang.studiengangid OR ')
		->where('Benutzerid', $userid);
	$query = $this->db->get();
	    
	}
	return $result = $query->row_array();
    }
    
    /**
     * Returns all possible information about the user of $userid of the "benutzer"-table, including information about the Studiengang/PO of the "studiengang"-table 
     * @param type $userid
     * @return array of rows
     */
    public function getUserInfo()
    {
	//Select all columns from "benutzer"
	$query = $this->db->get_where('benutzer', array('benutzerid' => $this->userid));
	$result = $query->row_array();
	
	
	//and add the corresponding studiengang, if available
	//echo $query->num_rows();
	
	$this->db->select('Pruefungsordnung, StudiengangAbkuerzung, StudiengangName')
	    ->from('studiengang')
	    ->where('StudiengangID', $result['StudiengangID']);
	$query2 = $this->db->get();
	
	//if result is not empty, merge the additional studiengang-informations, otherwise insert some default-stuff.
	if ($query2->num_rows() > 0){
	    $result2 = $query2->row_array();
	}
	else{
	    $result2 =  array(
		'Pruefungsordnung' => '',
		'StudiengangAbkuerzung' => '',
		'StudiengangName' => ''
	    );
	}   
	return array_merge($result, $result2);
    }
    
    /**
     * Retrieves an Array with informations of all Studycourses
     * @return array of rows -> StudiengangID, Pruefungsordnung, StudiengangAbkuerzung, StudiengangName
     */
    public function getStudiengang()
    {
	$this->db->select('StudiengangID, Pruefungsordnung, StudiengangAbkuerzung, StudiengangName')
		->from('studiengang')
		->order_by('StudiengangAbkuerzung','asc')
		->order_by('Pruefungsordnung','desc');
	$query = $this->db->get();
	return $query->result_array();
    }
    
    /**
     * Retrieves all Coursenames and the corresponding grades of the semesterplan of the student
     * @return array of rows -> Kursname, Notenpunkte
     */
    public function getCoursesAndGrades()
    {
        
        //original SQL-Statement:
//      SELECT c.`Kursname`, b.`Notenpunkte` 
//	  FROM semesterplan AS a, semesterkurs AS b, studiengangkurs AS c
//	  WHERE a.`BenutzerID` = '".$benutzerid."' 
//	   AND a.`SemesterplanID` = b.`SemesterplanID` 
//	   AND b.`KursID` = c.`KursID`");
        
        $this->db->select('studiengangkurs.Kursname, semesterkurs.Notenpunkte')
                ->from('semesterplan')
                ->join('semesterkurs','semesterplan.SemesterplanID = semesterkurs.SemesterplanID')
                ->join('studiengangkurs','semesterkurs.KursID = studiengangkurs.KursID')
                ->where('BenutzerID', $this->userid);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Creates the actual csv-file and uploads it to a given filepath
     * @param data      $content to be written in the file
     * @return string   filepath , or an error message (not ideal)
     */
    public function createCsv($content)
    {
        $filename = "semplan_".md5($_SESSION["loginname"]).".csv";
        $filepath = 'upload/'.$filename;       //atm located in the base-directory of CodeIgniter
				
        //if there's an old file already, delete it
        if(file_exists($filepath))
        {
            unlink($filepath);
        }
        //and create a new file
        //File Permissions based on the original Code
        if ( write_file($filepath, $content, 'a+'))
        {
            chmod($filepath, 0640);
            return $filepath;
        }
        
        return 'unable to write file';
                
        
    }
    
    
    /**
     * Updates the database with the content from the array 
     * @param array  
     */
    public function update($fielddata)
    {
	
	$this->db->where('BenutzerID', $this->userid)
		->update('benutzer', $fielddata);
	
    }
    
    /**
     * Creates a log-entry in the database.
     * @param type $array
     */
    public function log($typid, $fbid)
    {
//	INSERT 
//	INTO logging
//	( 
//	    `LogtypID` , 
//	    `BenutzertypID` , 
//	    `Fachbereich` 
//	) 
//	VALUES 
//	( 
//	    '4' , 
//	    '".$_SESSION["usertypeid"]."' , 
//	    '".$fachbereich."' 
//	);
	$log_array = array(
	    'LogtypID' => 4,
	    'BenutzertypID' => $typid,
	    'Fachbereich' => $fbid
	    );

	$this->db->insert('logging', $log_array);
	
    }
}

?>