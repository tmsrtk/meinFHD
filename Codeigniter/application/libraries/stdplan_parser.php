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
//    private $sameday = false;
    // Studiengang-Semester des eingelesenen Stundenplans
    private $stdg_semester = "";
    // Index f�r Tage im array
//    private $index = 1;
    // Index f�r Stunden im array
//    private $stunde_index = 1;
    // Index f�r Veranstaltungen im array
//    private$run = 0;
    //======================== Variablen-Deklaration ENDE
    
    
    function parse_stdplan($file_data){
	
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
	
//	// globalisiere Variablen
//	global $index, $stunde_index, $sameday, $run, $array_fachtext, $array_veranstaltungen, $stdg_short, $stdg_pov, $stdg_semester;
		
	$index = 1;
	$stunde_index = 1;
	$run = 0;
	$sameday = false;
	
	
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
		$this->array_fachtext[$index][0] = $attrs["fach"];		// Fach-Abk�rzung
		$this->array_fachtext[$index][1] = $attrs["dozname"];		// Dozentname
		$this->array_fachtext[$index][2] = $attrs["lang"];		// Fachname
		$this->array_fachtext[$index][3] = $attrs["farbeRGB"];	// Fachname

		// z�hle index weiter
		$index++;					
	}

	// wenn Knoten vom typ 'termin' ist.
	if( $knoten_element_name == "termin" ) {

		// gebe $index den richtigen Wert
		switch( $attrs["tag"] ) {
			case 0: $index = 0; break;
			case 1: $index = 1; break;
			case 2: $index = 2; break;
			case 3: $index = 3; break;
			case 4: $index = 4; break;
			case 5: $index = 5; break;
			case 6: $index = 6; break;
		}
		// gebe $stunde_index den richtigen Wert
		switch( $attrs["stunde"] ) {
			case 0: $stunde_index = 0; break;
			case 1: $stunde_index = 1; break;
			case 2: $stunde_index = 2; break;
			case 3: $stunde_index = 3; break;
			case 4: $stunde_index = 4; break;
			case 5: $stunde_index = 5; break;
			case 6: $stunde_index = 6; break;
			case 7: $stunde_index = 7; break;
			case 8: $stunde_index = 8; break;
			case 9: $stunde_index = 9; break;
			case 10: $stunde_index = 10; break;
			case 11: $stunde_index = 11; break;
			case 12: $stunde_index = 12; break;
			case 13: $stunde_index = 13; break;
		}
		// $array_veranstaltungen[tag][stunde][veranstaltung][4] = Tag
		// $array_veranstaltungen[tag][stunde][veranstaltung][5] = Stunde
		$this->array_veranstaltungen[$index][$stunde_index][$run][4] = $attrs["tag"];
		$this->array_veranstaltungen[$index][$stunde_index][$run][5] = $attrs["stunde"];

		// wir k�nnen zum n�chsten Tag
		$sameday = false;
	}

	// wenn es noch der gleiche Tag ist
	if( $sameday ) {
		// $array_veranstaltungen[tag][stunde][veranstaltung][4] = Tag
		// $array_veranstaltungen[tag][stunde][veranstaltung][5] = Stunde
		$this->array_veranstaltungen[$index][$stunde_index][$run][4] = $index;
		$this->array_veranstaltungen[$index][$stunde_index][$run][5] = $stunde_index;
	}

	// pr�fe ob Knoten vom typ 'veranstaltung' ist.
	// wenn ja ...
	if( $knoten_element_name == "veranstaltung" ) {

		// $array_veranstaltungen[tag][stunde][veranstaltung][0] = fach
		// $array_veranstaltungen[tag][stunde][veranstaltung][1] = form
		// $array_veranstaltungen[tag][stunde][veranstaltung][2] = raum
		// $array_veranstaltungen[tag][stunde][veranstaltung][3] = dozent
		// $array_veranstaltungen[tag][stunde][veranstaltung][6] = farbeRGB
		$this->array_veranstaltungen[$index][$stunde_index][$run][0] 	= $attrs["fach"];
		$this->array_veranstaltungen[$index][$stunde_index][$run][1] 	= $attrs["form"];
		$this->array_veranstaltungen[$index][$stunde_index][$run][2] 	= $attrs["raum"];
		$this->array_veranstaltungen[$index][$stunde_index][$run][3] 	= $attrs["dozname"];
		$this->array_veranstaltungen[$index][$stunde_index][$run][6] 	= $attrs["farbeRGB"];

		// Betrachte n�chste Veranstaltung
		$run++;
		// es ist noch immer der gleiche Tag
		$sameday = true;
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



    function write_data_to_db(){
//	echo '<pre>';
//	print_r($this->array_veranstaltungen);
//	echo '<pre>';
    }

}
?>
