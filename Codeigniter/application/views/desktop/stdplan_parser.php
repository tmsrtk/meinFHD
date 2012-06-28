<?php

class Stdplan_parser {
    
    
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
		    
		    // search array_fachtext for kurs[0](='kursname')
		    for( $i=1 ; $i < count($this->array_fachtext); $i++) {
			// if there is a kurs then save it to seperate variable $course_name
			if($this->array_fachtext[$i][0] == $course[0]){
			    $course_name = $this->array_fachtext[$i][2];
			}
		    }
		    
		    echo '<pre>';	
		    // get dozentID from database
		    echo $this->admin_model->get_dozentid_for_name($course[3]);
		    echo '</pre>';	
		    
		    
		} // end foreach hours
	    }// end foreach days
	} // end foreach
    }
    

    function write_data_to_db(){
	echo '<pre>';
	print_r($this->array_fachtext);
	print_r($this->array_veranstaltungen);
	echo '<pre>';
	
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

}
?>
