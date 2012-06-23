<?php

class Admin_model_parsing extends CI_Model {

    
    //======================== Variablen-Deklaration
    // Gibt an, ob es sich um ein WPF handelt oder nicht.
    private $isWPF = false;	
    // array zum speichern der Fachtext-Knoten-Elemente
    private $array_fachtext = array();
    // array zum speichern der Veranstaltungen-Knoten-Elemente
    private $array_veranstaltungen = array();
    // Studiengang-Abk�rzung des eingelesenen Stundenplans
    private $stdg_short = "";
    // Studiengang-Pr�fungsordnungsversion des eingelesenen Stundenplans
    private $stdg_pov = "";
    // Hilfsvariable zur Unterscheidung ob sich der
    // Parser noch im gleichen Tag-Knoten befindet.
    private $sameday = false;
    // Studiengang-Semester des eingelesenen Stundenplans
    private $stdg_semester = "";
    // Index f�r Tage im array
    private $index = 1;
    // Index f�r Stunden im array
    private $stunde_index = 1;
    // Index f�r Veranstaltungen im array
    private$run = 0;
    //======================== Variablen-Deklaration ENDE
    
    private $admin_model;
    
    function parse_stdplan($file_data){
	
//	$this->CI =& get_instance();
//	$admin_model = $this->load->model('admin_model');
	
	// array zum pr�fen ob ein Eintrag bereits darin enthalten ist.
	$array_check[0][0] = ""; 
	$array_check[0][1] = ""; 
	$array_check[0][2] = "";

	// Erzeuge neuen XML-Parser
	$xml_parser = xml_parser_create();
	
	// Setze Optionen:
	xml_parser_set_option( $xml_parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1" ); 	// Encoding-Typ ISO-8859-1
	xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 ); 					// kein Case-Folding
	xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 ); 					// �berspringe whitespaces an Anfang und Ende

	// Setze Elementhandler: searchElement und searchEndElement
	xml_set_element_handler( $xml_parser, array($this, 'searchElement'), array($this, 'searchEndElement') );

	// Wenn die Datei gelesen werden kann, �ffne stream
	if(!is_readable($file_data['file_name'])){
	    $fp = fopen('./resources/uploads/'.$file_data['file_name'], "r");
	}
	// anderenfalls gebe Fehlermeldung aus
	else die("<p class=\"eintrag_fehlerhaft\">XML-Datei konnte nicht ausgelesen werden!</p>");

	// Lese Daten aus xml-file und speichere sie in $data
	while($data = fread($fp, 4096)){
	    if(!xml_parse($xml_parser, $data, feof($fp))){
		die(sprintf(
			"XML error: %s at line %d",
			xml_error_string( xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser)));
	    }
	}

	// Entlasse XML-Parser
	xml_parser_free($xml_parser);
	
	// get data from parser and prepare for db-queries
	$this->prepare_parsed_stdplan_data();
	$this->write_data_to_db();
    }
    
    
    /*********************************************************
	**		searchElement()
	**	 	Wird beim Finden eines Start-Tags im XML-file aufgerufen.
	**		Verarbeitet die Daten des Knoten-Elements.
	**
	**		@param	$parser	Der betreffende Parser.
	**		@param	$name	Der Name des Knoten-Elements.
	**		@param	$attrs	Das assoziative Attribut-Array.
	*********************************************************/
    function searchElement( $parser, $knoten_element_name, $attrs ) {
	
	// wenn Knoten vom typ 'studiengang' ist
	if( $knoten_element_name == "studiengang" ) {
		$this->stdg_short = $attrs["stdg"];
		$this->stdg_pov = $attrs["poversion"];
		$this->stdg_semester = $attrs["semester"];
	}

	// wenn Knoten vom typ 'fachtext' ist.
	if( $knoten_element_name == "fachtext" ) {

		// $array_fachtext[index] 		erstes fachtext-objekt
		// $array_fachtext[index][0] 	fach-element des ersten fachtext-objekts
		// $array_fachtext[index][1] 	dozname-element des ersten fachtext-objekts
		// $array_fachtext[index][2] 	lang-element des ersten fachtext-objekts
		$this->array_fachtext[$this->index][0] = $attrs["fach"];		// Fach-Abk�rzung
		$this->array_fachtext[$this->index][1] = $attrs["dozname"];		// Dozentname
		$this->array_fachtext[$this->index][2] = $attrs["lang"];		// Fachname
		$this->array_fachtext[$this->index][3] = $attrs["farbeRGB"];	// Fachname

		// z�hle index weiter
		$this->index++;					
	}

	// wenn Knoten vom typ 'termin' ist.
	if( $knoten_element_name == "termin" ) {

		// gebe $index den richtigen Wert
		switch( $attrs["tag"] ) {
			case 0: $this->index = 0; break;
			case 1: $this->index = 1; break;
			case 2: $this->index = 2; break;
			case 3: $this->index = 3; break;
			case 4: $this->index = 4; break;
			case 5: $this->index = 5; break;
			case 6: $this->index = 6; break;
		}
		// gebe $stunde_index den richtigen Wert
		switch( $attrs["stunde"] ) {
			case 0: $this->stunde_index = 0; break;
			case 1: $this->stunde_index = 1; break;
			case 2: $this->stunde_index = 2; break;
			case 3: $this->stunde_index = 3; break;
			case 4: $this->stunde_index = 4; break;
			case 5: $this->stunde_index = 5; break;
			case 6: $this->stunde_index = 6; break;
			case 7: $this->stunde_index = 7; break;
			case 8: $this->stunde_index = 8; break;
			case 9: $this->stunde_index = 9; break;
			case 10: $this->stunde_index = 10; break;
			case 11: $this->stunde_index = 11; break;
			case 12: $this->stunde_index = 12; break;
			case 13: $this->stunde_index = 13; break;
		}
		// $array_veranstaltungen[tag][stunde][veranstaltung][4] = Tag
		// $array_veranstaltungen[tag][stunde][veranstaltung][5] = Stunde
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][4] = $attrs["tag"];
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][5] = $attrs["stunde"];

		// wir k�nnen zum n�chsten Tag
		$this->sameday = false;
	}

	// wenn es noch der gleiche Tag ist
	if( $this->sameday ) {
		// $array_veranstaltungen[tag][stunde][veranstaltung][4] = Tag
		// $array_veranstaltungen[tag][stunde][veranstaltung][5] = Stunde
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][4] = $this->index;
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][5] = $this->stunde_index;
	}

	// pr�fe ob Knoten vom typ 'veranstaltung' ist.
	// wenn ja ...
	if( $knoten_element_name == "veranstaltung" ) {
	    
		// $array_veranstaltungen[tag][stunde][veranstaltung][0] = fach
		// $array_veranstaltungen[tag][stunde][veranstaltung][1] = form
		// $array_veranstaltungen[tag][stunde][veranstaltung][2] = raum
		// $array_veranstaltungen[tag][stunde][veranstaltung][3] = dozent
		// $array_veranstaltungen[tag][stunde][veranstaltung][6] = farbeRGB
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][0] 	= $attrs["fach"];
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][1] 	= $attrs["form"];
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][2] 	= $attrs["raum"];
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][3] 	= $attrs["dozname"];
		$this->array_veranstaltungen[$this->index][$this->stunde_index][$this->run][6] 	= $attrs["farbeRGB"];

		// Betrachte n�chste Veranstaltung
		$this->run++;
		// es ist noch immer der gleiche Tag
		$this->sameday = true;
	}

    }
    
    
    /*********************************************************
    **		searchEndElement()
    **	 	Wird beim Finden eines End-Tags im XML-file aufgerufen.
    **		WIRD NICHT VERWENDET!
    **
    **		@param	$parser	Der betreffende Parser.
    **		@param	$name	Der Name des Knoten-Elements.
    *********************************************************/
    function searchEndElement( $parser, $name ) { }   

    
    /************************************************************************
    * 	blank()																*
    *																		*
    * 	Pr�ft eine Variable auf Leerheit.									*
    *	Die im Array definierten Werte d�rfen alleine nicht im Array stehen.*
    ************************************************************************/	
    function blank( $string ) {
	// definiere verbotene einzeln-zeichen
	$replace = array( ' ', '&nbsp;', '?', '!', '"',); 

	// k�rze leere string-stellen weg
	$string = trim( $string ); 

	// l�sche verbotene stellen aus string
	$string = str_replace( $replace, '', $string ); 

	// Sicherheitsvorkehrung: 
	// Da ein 0-String von empty() als leer angesehen wird, wird 0 in x replaced
	$string = str_replace( '0', 'x', $string );

	// pr�fe mit empty auf rest-leere und gebe ergebnis zur�ck
	return empty( $string ); 
    }


    
    function prepare_parsed_stdplan_data(){
	// >> run through parsed data
	//run through days
	foreach($this->array_veranstaltungen as $days){
	    // run through hours
	    foreach ($days as $hours) {
		// run through courses
		foreach ($hours as $course) {
		    
//		    echo '<pre>';	
//		    print_r($course);
//		    echo '</pre>';
		    
		    // search array_fachtext for kurs[0](='kursname')
		    for( $i=1 ; $i < count($this->array_fachtext); $i++) {
			// if there is a kurs then save it to seperate variable $course_name
			if($this->array_fachtext[$i][0] == $course[0]){
			    $course_name = $this->array_fachtext[$i][2];
			}
		    }
		    
		    // get dozent_id
		    $dozent_tmp = $this->get_dozentid_for_name($course[3]);
		    // only if there is a known dozent
		    if($dozent_tmp){
			$dozent_id = $dozent_tmp->BenutzerID;
		    } else {
			$dozent_id = 999;
		    }
		    
		    // if "fachname" contains "WPF" this course is a WPF
		    $isWPF = (strpos($course[0], "WPF") !== false) ? true : false;
		    
		    // get rid of "WPF" from course_name
		    $course_name = str_replace("WPF-", "", $course_name);

		    // remove brackets with module-number - if there is one 
		    if(strpos($course_name, "(") > 0 ){
			$course_name = substr($course_name, 0, strpos($course_name, "(")-1);
		    }
		    
		    // wenn es sich um ein WPF handelt,
		    if( $isWPF ) {
			// entnehme WPF-Bezeichnung aus Kursname und speichere sie separat in $wpfname
			$wpfname = substr($course_name, strpos($course_name," - ")+((strpos($course_name," - ")!==false) ? 3 : 0));

			// entferne anschließend die WPF-Bezeichnung aus dem Kursnamen und speicher ihn separat ab
			$course_name = substr($course_name,0,strpos($course_name," - "));

			// ermittel fachabkürzung für wpf
			$wpf_kurz = str_replace("WPF-","",$course[0]);
		    }
		    
		    // get stdgng_id
		    $stdgng_tmp = $this->get_stdgng_id($this->stdg_pov, $this->stdg_short);
		    $stdgng_id = $stdgng_tmp->StudiengangID;
		    
		    
		    // get course_id
		    $course_tmp = null;
		    if($isWPF){
			$course_tmp = $this->get_course_id($wpf_kurz, $stdgng_id);
		    } else {
			$course_tmp = $this->get_course_id($course_name, $stdgng_id);
		    }
		    // only if there is a course_id
		    if($course_tmp){
			$course_id = $course_tmp->KursID;
		    } else {
			$course_id = 999;
		    }
		    
		    // eventtype_id
		    $event_type_id = "";		
		    // Ermittel anhand von Fallunterscheidung die VeranstaltungsformID
		    // should be done by querying data from db
		    // if types should change we get a problem here!!
		    switch( substr($course[1],0,1) ) {
			case "V": $event_type_id = 1; break;
			case "Ü": $event_type_id = 2; break;
			case "S": $event_type_id = 3; break;
			case "P": $event_type_id = 4; break;
			case "L": $event_type_id = 5; break;
			case "T": $event_type_id = 6; break;
		    }
		    
		    // get course_duration
		    // Ermittel anhand der Kurstyp-Zahl die Dauer des Kurses
		    // event_type in xml contains a number that represents the duration of a course
		    $course_duration = "";
		    if( strpos($course[1], "1") !== false ) $course_duration = 1;
		    if( strpos($course[1], "2") !== false ) $course_duration = 2;
		    if( strpos($course[1], "3") !== false ) $course_duration = 3;
		    
	
//		    // create a new groupe in gruppe and then
//		    $this->create_new_group();
		    
		    // use this id (get highest group_id from gruppe)
		    $group_tmp = $this->get_max_group_id_from_gruppe();
		    $group_id = $group_tmp->GruppeID;

		    // TODO - save data
		    // get max spkurs_id
		    $spcourse_tmp = $this->get_max_spkurs_id();
		    $spcourse_id = $spcourse_tmp->SPKursID;
		    
		    
		    // ########## update USERS >> benutzerkurs
		    // get all students who 
		    $students = $this->get_student_ids($stdgng_id);
		    
		    
		    
		    // run through students and generate benutzerkurse
		    foreach ($students as $s){
			// mark courses as active if they are 'vorlesung' = 1 or 'tutorium' = 6
			if($event_type_id == 1 || $event_type_id == 6){
			    $isActive = true;
			} else {
			    // all other courses are inactive
			    $isActive = false;
			}
			// get semester that should be added to benutzerkurs
			$semester_tmp = $this->get_user_course_semester($s->BenutzerID, $course_id);
			$semester = '';
			if($semester_tmp){
			    $semester = $semester_tmp->Semester;
			}
			
			$this->pre($semester);
			
			// proceed only if there is a course_name
			// otherwise this part of array is empty (i.e. no courses at this time)
			if($semester){
			    $this->save_data_to_benutzerkurs(
				    $s->BenutzerID,
				    $course_id,
				    $spcourse_id,
				    $semester,
				    (($isActive) ? 1 : 0),
				    'stdplan_parsing',
				    99 // TODO get session_id from admin
			    );
			}
		    }
		    
		    
		    
		    
		} // end foreach hours
	    }// end foreach days
	} // end foreach
    }
    

    function write_data_to_db(){
	echo '<pre>';
//	print_r($this->array_fachtext);
//	print_r($this->array_veranstaltungen);
	echo '<pre>';
	
	// DEBUGGING: 
//	$this->create_new_group();
	
	$stdplan_record = array(
//	    'KursID' => ,
//	    'VeranstaltungsformID' => ,
//	    'VeranstaltungsformAlternative' => ,
//	    'WPFName' => ,
//	    'Raum' => ,
//	    'DozentID' => ,
//	    'StartID' => ,
//	    'EndeID' => ,
//	    'isWPF' => ,
//	    'TagID' => ,
//	    'GruppeID' => 
//	    'Farbe' => 
//	    'Editor' => 
	    
	);
	
    }
    
    
    
    
    
    
    
    
    // ################################################################# QUERIES

    // get all users with role dozent
    function get_dozentid_for_name($name){
	$this->db->distinct();
	$this->db->select('a.BenutzerID, a.Nachname');
	$this->db->from('benutzer as a');
	$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID and b.RolleID = 2');
	$this->db->like('a.Nachname', $name);

	$q = $this->db->get();

	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
    }
    
    
    // get all users with role dozent
    function get_student_ids($stdgng_id){
	$this->db->distinct();
	$this->db->select('a.BenutzerID');
	$this->db->from('benutzer as a');
	$this->db->join('benutzer_mm_rolle as b', 'a.BenutzerID = b.BenutzerID and b.RolleID = 4');
	$this->db->where('StudiengangID', $stdgng_id);

	$q = $this->db->get();

	if($q->num_rows() >= 1){
	    foreach ($q->result() as $row){
		    $data[] = $row;
	    }
	    return $data;
	}
    }
    
    function get_stdgng_id($po, $stdgng_short){
	$this->db->select('StudiengangID');
	$this->db->where('Pruefungsordnung', $po);
	$this->db->where('StudiengangAbkuerzung', $stdgng_short);
	$q = $this->db->get('studiengang');
	
	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
    }
    
    
    function get_course_id($course_short, $stdgng_id){
	$this->db->select('KursID');
	$this->db->where('kurs_kurz', $course_short);
	$this->db->where('StudiengangID', $stdgng_id);
	$q = $this->db->get('studiengangkurs');
	
	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
    }
    
    
    function create_new_group(){
	$a = array(
	    'TeilnehmerMax' => 0,
	    'TeilnehmerWartelisteMax' => 0,
	    'Anmeldung_zulassen' => 0
	);
	$this->db->insert('gruppe', $a);
    }
    
    
    function get_max_group_id_from_gruppe(){
	$this->db->select_max('GruppeID');
	$q = $this->db->get('gruppe');
	
	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
    }
    
    
    function get_max_spkurs_id(){
	$this->db->select_max('SPKursID');
	$q = $this->db->get('stundenplankurs');
	
	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
    }
    
    /**
     * Returns semester in which a given user put the course
     */
    function get_user_course_semester($user_id, $course_id){
	$this->db->select('b.Semester');
	$this->db->from('semesterplan as a');
	$this->db->join('semesterkurs as b', 'a.SemesterplanID = b.SemesterplanID');
	$this->db->where('b.KursID = '.$course_id . ' and a.BenutzerID = '. $user_id);
	
	$q = $this->db->get();
	
	if($q->num_rows() == 1){
	    foreach ($q->result() as $row){
		    $data = $row;
	    }
	    return $data;
	}
	
//	select b.`Semester`
//	from semesterplan as a
//	inner join semesterkurs as b
//	on a.`SemesterplanID` = b.`SemesterplanID`
//	where b.`KursID` = 1 and a.`BenutzerID` = 1383;
    }
    
    
    function save_data_to_benutzerkurs($user_id, $course_id, $spcourse_id, $semester, $active_flag, $comment, $edit_id){
	$benutzerkurs_record = array(
	    'BenutzerID' => $user_id,
	    'KursID' => $course_id,
	    'SPKursID' => $spcourse_id,
	    'SemesterID' => $semester,
	    'aktiv' => $active_flag,
	    'changed_at' => $comment,
	    'edited_by' => $edit_id
	);
	
	$this->db->insert('benutzerkurs', $benutzerkurs_record);
    } 
    
    
    ///////////// DEBUG
    function pre($var){
	$result = '';
	
	$result += '<pre>';
	$result += print_r($var);
	$result += '</pre>';
	
	return $result;
    }
}

?>
