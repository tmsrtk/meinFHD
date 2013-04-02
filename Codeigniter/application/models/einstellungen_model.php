<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Jan Eichler(JE), <jan.eichler@fh-duesseldorf.de>
 * @author Christian Kundruss(CK), <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class / Model Einstellungen_model
 *
 * Implements all necessary database operations for the custom user settings.
 */
class Einstellungen_model extends CI_Model {

    /**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
    public function __construct(){

        parent::__construct();
    }

    /**
     * Selects the students user data from the database for the given user-id
     * and returns it as an array.
     *
     * @param $userid Integer ID of the user, where the data should be selected for.
     * @access public
     * @return mixed An array will be returned if there are information for the given user id, otherwise NULL will be returned.
     */
	public function query_userdata_student($userid)
	{
        // select the student specific user information and return it
		$this->db->select('b.*, sg.*')
				->from('benutzer b')
				->join('studiengang sg', 'b.StudiengangID = sg.StudiengangID')
				->where('b.BenutzerID', $userid);

		return $this->db->get()->row_array();
	}

    /**
     * Selects the user data from the database for the given user id and returns it as an
     * array.
     *
     * @param $userid Integer ID of the user, where the data should be selected for.
     * @access public
     * @return mixed An array will be returned if there are information for the given user id, otherwise NULL will be returned.
     */
	public function query_userdata($userid)
	{
        // select the whole user information and return it
		$this->db->select('b.*')
				->from('benutzer b')
				->where('b.BenutzerID', $userid);

		return $this->db->get()->row_array();
	}

    /**
     * Saves the edits of the personal information for the currently authenticated user
     * in the database.
     *
     * @access public
     * @param $form_data array The array with the user input from the form
     * @return void
     */
	public function save_edits($form_data)
	{
        // construct the array with the basic user information
		$update = array(
			'LoginName' => $form_data['loginname'],
			'Vorname' => $form_data['forename'],
			'Nachname' => $form_data['lastname'],
			'Email' => $form_data['email'],
		);

        // extract the students specific information and add them to the update-array, if the user is an student
		if ( in_array(Roles::STUDENT, $this->user_model->get_all_roles())){
			$update['StudienbeginnSemestertyp'] = $form_data['semesteranfang'];
			$update['StudienbeginnJahr'] = 	$form_data['startjahr'];
            $update['EmailDarfGezeigtWerden'] = $form_data['EmailDarfGezeigtWerden'];
		}

        // extract the dozent specific information and add them to the update-array, if the user is an student
		if ( in_array(Roles::DOZENT, $this->user_model->get_all_roles()) ||  in_array(Roles::BETREUER, $this->user_model->get_all_roles())){
			$update['Raum'] = $form_data['room'];
			$update['Titel'] = $form_data['title'];
		}

        // extract the new password and add it to the update-array, if it is set in the form_data-array
		if ( ! empty($form_data['password'])){
            $update['Passwort'] = md5($form_data['password']);
        }

        // update the database with the update array
		$this->db->where('BenutzerID', $this->user_model->get_userid());
		$this->db->update('benutzer', $update);

    }

    /**
     * Selects information of all degree program courses and returns them as an array.
     *
     * @access public
     * @return array The array with all different degree programs. Each row represents one degree program
     *               -> StudiengangID, Pruefungsordnung, StudiengangAbkuerzung, StudiengangName
     */
    public function get_all_degree_programs()
    {
        // select all degree programs from the database
        $this->db->select('StudiengangID, Pruefungsordnung, StudiengangAbkuerzung, StudiengangName')
            ->from('studiengang')
            ->order_by('StudiengangAbkuerzung','asc')
            ->order_by('Pruefungsordnung','asc');

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Selects all courses and the corresponding grades of the actual degree program plan for the
     * authenticated user.
     *
     * @access public
     * @return array Associative array with all courses and grades. Each row represents one courses with
     *               the following keys: Kursname, Notenpunkte.
     */
    public function get_courses_and_grades($userid)
    {
        $this->db->select('studiengangkurs.Kursname, semesterkurs.Notenpunkte')
            ->from('semesterplan')
            ->join('semesterkurs','semesterplan.SemesterplanID = semesterkurs.SemesterplanID')
            ->join('studiengangkurs','semesterkurs.KursID = studiengangkurs.KursID')
            ->where('BenutzerID', $userid);

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Creates an csv-file with the provided data and uploads it to the standard /upload - folder of the
     * application.
     *
     * @access public
     * @param $filename_prefix string Prefix of the filename
     * @param $content string Content, that should be written into the file
     * @return string The full qualified path and name of the uploaded file, if everything went fine. Otherwise an
     *                error message ('unable to write file') will be returned.
     */
    public function create_csv_file($filename_prefix, $content){

        $filename = $filename_prefix . md5($this->user_model->get_loginname()) . '.csv'; // construct the filename
        $filepath = './resources/uploads/studienplaene/' . $filename; // specify the full storage path

        // if there already exists a file -> delete it‚
        if(file_exists($filepath))
            unlink($filepath);

        // create the file with the specified content and save it
        if(write_file($filepath,$content,'a+')){
            chmod($filepath, 0640); // set the correct permissions
            $filepath = substr($filepath, 1); // remove the first sign (.) from the filepath to avoid problems
            return $filepath;
        }
        return 'unable to write file';
    }

    /**
     * All db operations for changing the degree program of the current authenticated user to the specified
     * degree program id (parameter). Creates also a new semesterplan and all students courses for the new
     * degree program.
     *
     * @access public
     * @param $new_deg_prog_id integer ID of the new degree program
     * @return void
     */
    public function change_degree_program($new_deg_prog_id){

        // update / change the degree program in the user table
        $user_table_update_data = array(
            'StudiengangID' => $new_deg_prog_id,
        );

        $this->db->where('BenutzerID',$this->user_model->get_userid());
        $this->db->update('benutzer',$user_table_update_data);
    }
}
/* End of file einstellungen_model.php */
/* Location: ./application/models/einstellungen_model.php */