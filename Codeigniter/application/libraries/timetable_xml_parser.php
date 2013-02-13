<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Timetable XML-Parser
 *
 * The Timetable-XML-Parser - Library provides all functions to parse uploaded timetable xml-files.
 * Main logic is from the timetable-parsing-script form meinFHD 1.0. Written by Jochen Sauer.
 * @author Christian Kundruss <CK>
 */
class Timetable_xml_parser {

    /**
     * Holds the instance of the currently running ci, after the timetable xml parser has been initialized.
     * @var CI CI-Instance
     * @access private
     */
    private $CI;

    /**
     * Holds the uid of the editor(user), that executes the timetable xml parsing. The default value is 0.
     * @var int
     * @access private
     */
    private $editor_id = 0;

    // === variable declaration for the parsed xml information ===

    /**
     * Indicates whether the parsed course is an wpf or not. Default Value is false.
     * @var bool Default value: false.
     * @access private
     */
    private $is_wpf = false;

    /**
     * Array for saving the 'fachtext'-xml-node.
     * @var array
     * @access private
     */
    private $array_fachtext = array();

    /**
     * Array for saving die 'Veranstatungen'-xml-node.
     * @var array
     * @access private
     */
    private $array_veranstaltungen = array();

    /**
     * Variable to store the short name of the parsed degree program. The default value is an empty string.
     * @var string
     * @access private
     */
    private $dp_short = "";

    /**
     * Variable to store the version of the degree program, where the timetable file is parsed for. Default value is an empty string
     * @var string
     * @access private
     */
    private $dp_version = "";

    /**
     * Helper variable to be able to detect, if the parser is still at the same day node. Default value is an false. True, if the
     * parser is still viewing the same day.
     * @var bool
     * @access private
     */
    private $sameday = false;

    /**
     * Semester of the parsed timetable xml file. Default value is an empty string.
     * @var string
     * @access private
     */
    private $dp_semester = "";

    /**
     * Variable for the index of the day array. Default value is 1.
     * @var int
     * @access private
     */
    private $day_index = 1;

    /**
     * Variable for the index in the hour array. Default value is 1.
     * @var int
     * @access private
     */
    private $hour_index = 1;

    /**
     * Variable for the index in the event array. Default value is 0.
     * @var int
     * @access private
     */
    private $event_index = 0;
    // == end variable declaration ===

    /**
     * Constructor. Used for initialization while loading the helper class.
     * @access protected
     */
    function __construct(){

        $this->CI =& get_instance(); // get and save the ci instance

        $this->editor_id = $this->CI->user_model->get_userid(); // get the uid of the authenticated user and save it as the editor
        $this->CI->load->model('timetable_parsing_model');
    }

    /**
     * Starts the process to parse an timetable xml file.
     * @param $xml_file string Name and path of the uploaded xml-file, that should be parsed.
     */
    public function parse_timetable_xml($xml_file){

        $xml_data = array(); // array for saving the content of the xml file

        // create a new XML-Parser
        $xml_parser = xml_parser_create("ISO-8859-1");

        // set the options for the xml parser
        xml_parser_set_option( $xml_parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1" ); // encoding type
        xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 ); // no case-folding
        xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 ); // escape whitespaces at the beginning and the end of the file

        // set and configure the element handler for searching elements
        xml_set_element_handler($xml_parser, array($this, '_search_element'), array($this, '_search_end_element'));

        // if the xml-file is readable open the stream and
        if(!is_readable($xml_file['file_name'])){

            $file_stream = fopen('./resources/uploads/stundenplaene/'.$xml_file['file_name'], "r");

            // read out the content of the xml file and save it into the xml_data-array
            while($data = fread($file_stream, 4096)){

                // if the actual line is not readable
                if(!xml_parse($xml_parser, $data, feof($file_stream))){

                    $error_messages = array(
                        'errors'
                    );


                    //-> exit with an detailed error message, where the unexpected element is
                    $error_messages[] = sprintf("XML error: %s at line %d",
                                                xml_error_string( xml_get_error_code($xml_parser)),
                                                xml_get_current_line_number($xml_parser));

                    return $error_messages;
                }
            }
        }

        // the xml-file is not readable
        else {

            // exit with an error message
            $error_messages = array(
                'errors'
            );

            $error_messages[] = "Die XML-Datei konnte nicht ausgelesen werden!";

            return $error_messages;
        }

        // free the xml-parser
        xml_parser_free($xml_parser);

        // check the parsed data for errors
        $error_messages = $this->_search_parsing_errors();
        // if there are errors return them
        if (count($error_messages) > 1){
            return $error_messages;
        }

        // the parsed data is valid

        // prepare array to return a unique combination
        $ids = array(
            $this->dp_short,
            $this->dp_semester,
            $this->dp_version
        );

        // and add them to return-array
        $return_parsing_info = array(
            $ids
        );

        // find out how many degree-programs with that PO-Abk-Kombi there are
        $count_dp = 0;
        $count_dp = $this->CI->timetable_parsing_model->count_degree_programs($this->dp_short, $this->dp_version);

        // only if there is exactly one degree-program with that PO-Abk-Kombi start working on the data
        if($count_dp === 1){
            // prepare data and return it to the view
            $return_parsing_info[] = $this->_prepare_parsed_timetable_data();

            return $return_parsing_info;
        }

        // there is more than one degree-program with that PO-Abk-Kombi -> give an error message
        else {

            // delete file from the server
			unlink('./resources/uploads/stundenplaene/'.$xml_file['file_name']);

            // add an error message and return it
            $error_messages = array(
                'errors'
            );

            $error_messages[] = "Der in der XML-Datei angegebene Studiengang ist nicht vorhanden oder fehlerhaft. Bitte &uuml;berpr&uuml;fe".
                                " die XML-Datei und starte den Import dann erneut. Die Datei wurde vom Server gel&ouml;scht.";


            return $error_messages;
        }
    }

    /**
     * Finds an start tag in the timetable xml file. Also processes the node element data.
     *
     * @param $parser xml_parser The regarding xml-parser.
     * @param $node_element_name string The name of the node element too lok for.
     * @param $attributes string The associative element in the attribute array
     * @return void
     * @access private
     */
    private function _search_element($parser, $node_element_name, $attributes){

        // if note type is 'studiengang'
        if($node_element_name == "studiengang"){

            $this->dp_short = $attributes["stdg"]; // save the short name
            $this->dp_version = $attributes["poversion"]; // save the degree program version
            $this->dp_semester = $attributes["semester"]; // save the semester
        }

        // if the node type is 'fachtext'
        if($node_element_name == "fachtext"){

            $this->array_fachtext[$this->day_index][0] = $attributes["fach"]; // Fach-abbreviation
            $this->array_fachtext[$this->day_index][1] = $attributes["dozname"]; // Dozentname
            $this->array_fachtext[$this->day_index][2] = $attributes["lang"]; // Fachname
            $this->array_fachtext[$this->day_index][3] = $attributes["farbeRGB"]; // Fachname

            // increment the day index (+1)
            $this->day_index++;
        }

        // if the node type is 'termin'.
        if($node_element_name == "termin") {

            // give $day_index the right value
            switch( $attributes["tag"] ) {
                case 0: $this->day_index = 0; break;
                case 1: $this->day_index = 1; break;
                case 2: $this->day_index = 2; break;
                case 3: $this->day_index = 3; break;
                case 4: $this->day_index= 4; break;
                case 5: $this->day_index = 5; break;
                case 6: $this->day_index= 6; break;
            }

            // give the $hour_index the right value
            switch( $attributes["stunde"] ) {
                case 0: $this->hour_index = 0; break;
                case 1: $this->hour_index = 1; break;
                case 2: $this->hour_index = 2; break;
                case 3: $this->hour_index = 3; break;
                case 4: $this->hour_index = 4; break;
                case 5: $this->hour_index = 5; break;
                case 6: $this->hour_index = 6; break;
                case 7: $this->hour_index = 7; break;
                case 8: $this->hour_index = 8; break;
                case 9: $this->hour_index = 9; break;
                case 10: $this->hour_index = 10; break;
                case 11: $this->hour_index= 11; break;
                case 12: $this->hour_index = 12; break;
                case 13: $this->hour_index = 13; break;
            }

            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][4] = $attributes["tag"];
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][5] = $attributes["stunde"];

            // we can now go to the next day
            $this->sameday = false;
        }

        // if it is still the same day
        if( $this->sameday ) {
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][4] = $this->event_index;
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][5] = $this->hour_index;
        }

        // if the node is from the type 'veranstaltung'
        if($node_element_name == "veranstaltung"){

            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][0] = $attributes["fach"];
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][1] = $attributes["form"];
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][2] = $attributes["raum"];
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][3] = $attributes["dozname"];
            $this->array_veranstaltungen[$this->day_index][$this->hour_index][$this->event_index][6] = $attributes["farbeRGB"];

            // go to the next event
            $this->event_index++;

            // it is still the same day
            $this->sameday = true;
        }
    }

    /**
     * Finds an end tag in the timetble xml-file.
     * Function will not provide any function.
     *
     * @param $parser xml_parser The regarding xml-parser.
     * @param $node_element_name string The name of the node element too lok for.
     * @access private
     * @return void
     */
    private function _search_end_element($parser, $node_element_name){

    }

    /**
     * Prepares the parsed timetable data for inserting it into the database.
     *
     * @return array The array that can be inserted into the database.
     * @access private
     */
    private function _prepare_parsed_timetable_data(){

        $duplicate_event = array(); // array for saving duplicated events

        $debugging = array(); // array to to check parsing more comfortably

        // run through the parsed data

        // run through each day
        foreach($this->array_veranstaltungen as $days){
            // run through every hour of the day

            foreach ($days as $hours) {

                // run through every course
                foreach ($hours as $course) {

                    // extend the script running time
                    set_time_limit(30);

                    /*
                     * the xml-files contains one entry for every hour!
                     * -> same course over more than one hour exists more than one time
                     * check if combination already has been saved to the $duplicate_event-array
                     */

                    // if it has not been saved in the duplicated array
                    if(! in_array($course[0].'_'.$course[1].'_'.$course[3], $duplicate_event)){

                        // add combination to array
                        $duplicate_event[] = $course[0].'_'.$course[1].'_'.$course[3];

                        // search array_fachtext for kurs[0](='coursename')
                        $course_name = '';

                        for( $i=1 ; $i <= count($this->array_fachtext); $i++) {

                            // if there is a course then save it to separate variable $course_name
                            if($this->array_fachtext[$i][0] == $course[0]){

                                /*
                                 * note:
                                 * course_name is needed to identify a course as an wpf and extract the wpf-name
                                 * and to create an correct entry in studiengangkurs if it doesn't already exist
                                 */
                                $course_name = $this->array_fachtext[$i][2];
                            }
                        }

                        // get the dozent_id and save it into an temp array
                        $dozent_tmp = array();
                        $dozent_tmp = $this->CI->timetable_parsing_model->get_dozentid_for_name($course[3]);

                        // if there is a known dozent
                        if($dozent_tmp){
                            $dozent_id = $dozent_tmp->BenutzerID; // save the appropriate dozent id
                        }
                        // there is no know dozent
                        else {
                            $dozent_id = 0;
                        }

                        // check if the "fachname" contains "WPF", than the course is an WPF
                        $is_wpf = false;
                        $is_wpf = (strpos($course[0], "WPF") !== false) ? true : false;

                        // get rid of "WPF" from course_name
                        $course_name = str_replace("WPF-", "", $course_name);

                        // remove everything from the opening-bracket (module-number) - if there is one
                        if(strpos($course_name, "(") > 0 ){
                            $course_name = substr($course_name, 0, strpos($course_name, "(")-1);
                        }

                        // if the course is a wpf
                        if($is_wpf) {

                            // remove WPF from the course name and save it in an separate variable
                            $wpfname = substr($course_name, strpos($course_name," - ")+((strpos($course_name," - ")!==false) ? 3 : 0));

                            // remove the wpf-mark from the course name and save it into an separate variable
                            $course_name = substr($course_name,0,strpos($course_name," - "));

                            // get the abbreviation for the wpf
                            $wpf_kurz = str_replace("WPF-","",$course[0]);
                        }

                        $stdgng_tmp = array();

                        // get the degree program id
                        $stdgng_id = '';
                        $stdgng_tmp = $this->CI->helper_model->get_stdgng_id($this->dp_version, $this->dp_short);

                        // if there is a degree program id
                        if($stdgng_tmp){
                            $stdgng_id = $stdgng_tmp->StudiengangID;
                        }

                        // get the course_id
                        $course_id_tmp = '';
                        $course_id = '';

                        // if the course is a wpf
                        if($is_wpf){

                            // if course is wpf - wpf_kurz has to be cut
                            $course_id_tmp = $this->CI->timetable_parsing_model->get_course_id($wpf_kurz, $stdgng_id);
                        }
                        // the course is not an wpf
                        else {
                            // otherwise use the parsed course-name
                            $course_id_tmp = $this->CI->timetable_parsing_model->get_course_id($course[0], $stdgng_id);
                        }

                        // if there is a course_id
                        if($course_id_tmp){
                            $course_id = $course_id_tmp->KursID;
                        }
                        else {
                            // get the short-name of the course
                            $short_name = ($is_wpf) ? $wpf_kurz : $course[0];

                            // otherwise empty entry has to be created in studiengangkurs
                            $this->CI->timetable_parsing_model->create_new_course_in_stdgng($course_name, $short_name, $this->dp_semester, $stdgng_id);

                            // and the new course_id has to be saved to $course_id
                            $new_course_id = $this->CI->timetable_parsing_model->get_max_course_id_from_studiengangkurs();
                            $course_id = $new_course_id->KursID;
                        }

                        // get the 'VeranstaltungsformID' / event_type_id
                        $event_type_id = '';

                        switch( mb_substr($course[1],0,1, "ISO-8859-1") ) {
                            case "V": $event_type_id = 1; break;
                            case "Ü": $event_type_id = 2; break;
                            case "S": $event_type_id = 3; break;
                            case "P": $event_type_id = 4; break;
                            case "L": $event_type_id = 5; break;
                            case "T": $event_type_id = 6; break;
                        }

                        /*
                         * get the course_duration
                         * Calculate with the help of the 'Kurstyp-Zahl' the duration of a course.
                         * event_type in xml contains a number that represents the duration of a course
                         */
                        $course_duration = '';

                        if( strpos($course[1], "1") !== false )
                        {
                            $course_duration = 1;
                        }
                        else if( strpos($course[1], "2") !== false )
                        {
                            $course_duration = 2;
                        }
                        else if( strpos($course[1], "3") !== false )
                        {
                            $course_duration = 3;
                        }

                        // update semester of that course
                        $this->CI->timetable_parsing_model->update_semester_of_course($course_id, $this->dp_semester);

                        // create a new group in the 'gruppe' database table
                        $this->CI->helper_model->create_new_group();

                        // use the id of the newly created group(get highest group_id from gruppe)
                        $group_tmp = array();
                        $group_id = '';
                        $group_tmp = $this->CI->helper_model->get_max_group_id_from_gruppe();

                        // separate the group id
                        if($group_tmp){

                            $group_id = $group_tmp->GruppeID;
                        }

                        // construct data for the debugging array
                        $data = '';
                        $data .= '<div>********************naechster Datensatz********************<br />';
                        $data .= 'KursID:'.$course_id.'<br />';
                        $data .= 'Kursname:'.$course_name.'<br />';
                        $data .= 'VeranstaltungsformID:'.$event_type_id.'<br />';
                        $data .= 'VeranstaltungsformAlternative:'.substr($course[1], 2).'<br />';
                        $data .= 'WPFName:'.($is_wpf ? $wpfname : '').'<br />';
                        $data .= 'Raum:'.$course[2].'<br />';
                        $data .= 'DozentID:'.$dozent_id.'<br />';
                        $data .= 'StartID:'.($course[5] + 1).'<br />';
                        $data .= 'EndeID:'.($course[5] + $course_duration).'<br />';
                        $data .= 'TagID:'.($course[4] + 1).'<br />';
                        $data .= 'isWPF:'.($is_wpf ? '1' : '0').'<br />';
                        $data .= 'Farbe:'.$course[6].'<br />';
                        $data .= 'GruppeID:'.$group_id.'<br />';
                        $data .= 'Editor:'.$this->editor_id.'<br />';
                        $data .= '*****************************************************************</div>';
                        $debugging[] = $data;

                        // save the parsed and prepared data to the database
                        $this->CI->timetable_parsing_model->write_stdplan_data(
                            $course_id,
                            $event_type_id,
                            (substr($course[1], 2) != '') ? substr($course[1],2) : '',
                            ($is_wpf? $wpfname : ''),
                            $course[2],
                            $dozent_id,
                            $course[5] + 1,
                            $course[5] + $course_duration,
                            ($is_wpf ? '1' : '0'),
                            $course[4] + 1,
                            $group_id,
                            $course[6],
                            $this->editor_id
                        );

                        // get the max spkurs_id / id of the latest inserted course
                        $spcourse_tmp = array();
                        $spcourse_tmp = $this->CI->helper_model->get_max_spkurs_id();

                        // only extract the spcourse_id from the array, that has been returned
                        if($spcourse_tmp){
                            $spcourse_id = $spcourse_tmp->SPKursID;
                        }

                        // update the 'benutzerkurs'-table for each student
                        $this->CI->helper_model->update_benutzerkurs($this->editor_id, $event_type_id, $course_id, $spcourse_id, $stdgng_id);

                    } // endif duplicate entry

                    // save course content to the debugging array
                    $debugging [] = $course;

                } // end foreach hours

            }// end foreach days

        } // end foreach

        // return the debugging array
        return $debugging;
    }

    /**
     * Looks for parsing errors in the $this->array_veranstaltungen-array
     * If there are some errors construct an error message and add them to an array.
     * Afterwards the array with all found error-messages will be returned.
     *
     * @access private
     * @return array Array with all found errors during parsing
     */
    private function _search_parsing_errors(){

        /*
         * Search for errors and add some messages to the error_messages-array.
         * Runt through the parsed data.
         */
        $error_messages = array(
            'errors'
        );

        // run through every event
        foreach($this->array_veranstaltungen as $days){

            // run through every single hour
            foreach ($days as $hours) {

                // run through every course
                foreach ($hours as $course) {

                    // look if the viewed tag is empty
                    if(count($course) < 3){
                        // the tag is empty, construct and add the error message to the error_messages-array
                        $error_messages[] = 'Empty tag @ Tag-'.$course[4].' Stunde-'.$course[5];
                    }
                }
            }
        }

        return $error_messages;
    }
}

/* End of file Timetable_xml_parser.php */
/* Location: ./application/libraries/Timetable_xml_parser.php */