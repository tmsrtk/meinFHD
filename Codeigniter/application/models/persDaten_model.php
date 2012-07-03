<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of persDaten_model
 *
 * @author jan
 */
class persDaten_model extends CI_Model{
    
    
    private $userId;
    
    public function __construct()
    {
        parent::__construct();
	
        $this->userId = $this->authentication->user_id();
    }
    
    
    public function getUserInfoDoz($userid)
    {
//	Select LoginName, Email, Titel, Vorname, Nachname, Raum, farbschema
//	From benutzer
//	Where benutzerid = $userid
	$this->db->select('LoginName, Titel, Email, Vorname, Nachname, Raum, farbschema')
		->from('benutzer')
		->where('BenutzerID', $userid );
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
    public function getUserInfo($userid)
    {
	//Select all columns from "benutzer"
	$query = $this->db->get_where('benutzer', array('benutzerid' => $userid));
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
    
    public function getStudiengang()
    {
	$this->db->select('StudiengangID, Pruefungsordnung, StudiengangAbkuerzung, StudiengangName')
		->from('studiengang')
		->order_by('StudiengangAbkuerzung','asc')
		->order_by('Pruefungsordnung','desc');
	$query = $this->db->get();
	return $query->result_array();
    }
    
//    Dinge, die die jeweiligen Userrollen ändern dürfen
//    
//    Dozent/Mitarbeiter:
//	LoginName,
//	Email,
//	Titel,
//	Vorname,
//	Nachname,
//	Raum,
//	farbschema
//	
//    Student
//	LoginName
//	Email
//	EmailDarfGezeigtWerden
//	Vorname
//	Nachname
//	(Matrikelnummer)	nur Anzeige
//	(MatrikelnummerFlag)	??? Was ist das
//	(StudiengangID)		nur Anzeige -> Referenz auf Studiengang-Tabelle bei Anzeige!
//	(Semester)		nur Anzeige
//	StudienbeginnJahr
//	StudienbeginnSemestertyp
//	farbschema
//	
//    Tutor
//      LoginName
//	Email
//	EmailDarfGezeigtWerden
//	Vorname
//	Nachname
//	farbschema
    
    
    /**
     * Same as update(), but without a new the password being set.
     * @param array  
     */
    function update($userid, $fielddata)
    {
	
	$this->db->where('BenutzerID', $userid)
		->update('benutzer', $fielddata);
	
    }
    
    /**
     * Kann man dies nicht auslagern? In der Theorie müsste es diese Funktion auch in 3 weiteren Models geben.
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
	    'BenutzertypID' => $typid,  //Benutzertyp = Rolle? benutzer.TypID
	    'Fachbereich' => $fbid
	    );

	$this->db->insert('logging', $log_array);
	//echo 'debug: Insert Log';
	
    }
}

?>