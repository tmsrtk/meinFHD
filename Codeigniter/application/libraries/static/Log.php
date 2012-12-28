<?php

/**
 * meinFHD WebApp
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class Log
 *
 * Hold`s static information about the different Logtypes in meinFHD 2.0 and provides methods
 * for submitting the log actions.
 */
class Log{

    /**
     * Constant to map the action "Email über Stundenplan" to the corresponding LogtypID
     */
    CONST EMAIL_UEBER_STDPLAN = 1;

    /**
     * Constant to map the action "Semesterplan bearbeiten" to the corresponding LogtypID
     */
    CONST SEMESTERPLAN_BEARBEITEN = 2;

    /**
     * Constant to map the action "Stundenplan bearbeiten" to the corresponding LogtypID
     */
    CONST STUNDENPLAN_BEARBEITEN = 3;

    /**
     * Constant to map the action "Persoenliche Daten bearbeiten" to the corresponding LogtypID
     */
    CONST PERSOENLICHE_DATEN_BEARBEITEN = 4;


    /**
     * Constant to map the action "Kursdaten geaendert" to the corresponding LogtypID
     */
    CONST KURSDATEN_GEAENDERT = 5;


    /**
     * Constant to map the action "Tutor zugewiesen" to the corresponding LogtypID
     */
    CONST TUTOR_ZUGEWIESEN = 6;

    /**
     * Submits a new log entry in the database. Therefore there must be the two parameters logtype and benutzertype_id
     * be submitted. If the user doesn`t pass any value for the parameter department_id, the default departmend_id
     * (5 - FB Medien) will be used.
     *
     * @param int $logtype Logtype / Event that should be logged
     * @param int $usertype_id Usertype of the event initiator
     * @param int $department_id ID of the department where the action comes from. If no value is passed the department
     *                           5 will be used.
     */
    public static function new_log_entry($logtype, $usertype_id, $department_id = 5){
        // construct the input array for the table
        $data = array(
            'LogtypID' => $logtype,
            'BenutzertypID' => $usertype_id,
            'Fachbereich' => $department_id,
        );

        // save the ci instance in an variable
        $CI = & get_instance();
        // insert the data into the table
        $CI->db->insert('logging', $data);
    }
}


/* End of file Logtype.php */
/* Location: ./application/libraries/static/Logtype.php */