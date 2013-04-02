<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * Class / Controller Einstellungen
 *
 * Controller implements all necessary functions for the custom user settings.
 */
class Einstellungen extends FHD_Controller {

	/**
     * Default constructor. Used for initialization.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // load all needed models
        $this->load->model('studienplan_model');
        $this->load->model('einstellungen_model');
        $this->load->model('user_model');
    }

    /**
     * Loads the 'Persoenliche Einstellungen' View.
     * Selects therefore the necessary data from the database.
     *
     * @access public
     * @return void
     */
    public function index(){

        // query all needed user information from the database

        // the authenticated user is a student -> select the student specific data
        if ( in_array(Roles::STUDENT, $this->user_model->get_all_roles()))
        {
            $data = $this->einstellungen_model->query_userdata_student($this->user_model->get_userid());
        }
        else // authenticated user is not a student -> load the normal userdata
        {
            $data = $this->einstellungen_model->query_userdata($this->user_model->get_userid());
        }

        // add the data to the view
        $this->data->add('formdata', $data);
        $this->data->add('userroles', $this->user_model->get_all_roles()); // load all possible userroles

        // load the index view / page
        $this->load->view('einstellungen/index', $this->data->load());
    }

	/**
	 * Form validation for the personal preferences mask.
     * The method is going to be called from the authenticated user while pressing
     * 'Aenderungen speichern'.
     *
     * @access public
     * @return void
	 */
	public function validate_edits()
	{
		// set the custom delimiter for validation errors
		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

        // array for the validation rules
		$rules = array();

		// read the values, from actual form
		$new_form_values = $this->input->post();

		// get current user data from the database
		$current_user_data = $this->einstellungen_model->query_userdata($this->user_model->get_userid());

        // set the emailflag in the post_form_values. required, because a not checked checkbox results in no $_POST-entry
        $new_form_values['EmailDarfGezeigtWerden'] = isset($new_form_values['EmailDarfGezeigtWerden']) ? 1 : 0;

        // --- set the different form validation rules ---

        // validation rule for the loginname
		if ( array_key_exists('loginname', $new_form_values))
		{
			// check if current value is different from the value in db
			if ($current_user_data['LoginName'] != $new_form_values['loginname']) 
			{
                $this->form_validation->set_rules('login', 'Loginname', 'callback_validate_loginname['.$new_form_values['loginname'].']');
			}
		}

        // validation rule for the persons title
		if ( array_key_exists('title', $new_form_values))
		{
			if ($current_user_data['Titel'] != $new_form_values['title'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_title();
			}
		}

        // validation rule for the forename
		if ( array_key_exists('forename', $new_form_values))
		{
			if ($current_user_data['Vorname'] != $new_form_values['forename'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_forename();
			}
		}

        // validation rule for the lastname
		if ( array_key_exists('lastname', $new_form_values))
		{
			if ($current_user_data['Nachname'] != $new_form_values['lastname'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_lastname();
			}
		}

        // validation rule for the email address
		if ( array_key_exists('email', $new_form_values))
		{
			if ($current_user_data['Email'] != $new_form_values['email']) 
			{
                $this->form_validation->set_rules('email', 'Email', 'callback_validate_email');
            }
		}

        // validation rule for the persons room / office
		if ( array_key_exists('room', $new_form_values))
		{
			if ($current_user_data['Raum'] != $new_form_values['room'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_room();
			}
		}

        // validation rule for the students begin year
		if ( array_key_exists('startjahr', $new_form_values))
		{
			if ($current_user_data['StudienbeginnJahr'] != $new_form_values['startjahr'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_startjahr();
			}
		}

        // validation rule for the password
		if ( array_key_exists('password', $new_form_values))
		{
			if ( ! empty($new_form_values['password']))
			{
				$rules[] = $this->adminhelper->get_formvalidation_password();
				$rules[] = $this->adminhelper->get_formvalidation_password_confirm();
			}
		}

        // validation rule for the startsemester type
		if ( array_key_exists('semesteranfang', $new_form_values))
		{
			if ($current_user_data['StudienbeginnSemestertyp'] != $new_form_values['semesteranfang'])
			{
				$rules[] = $this->adminhelper->get_formvalidation_semesteranfang();
			}
		}

        // validation rule for the show email flag & callback validation method for a checkbox
        if (array_key_exists('EmailDarfGezeigtWerden', $new_form_values)){

            // the new user (form input) differs from the currently saved user data
            if ($current_user_data['EmailDarfGezeigtWerden'] != $new_form_values['EmailDarfGezeigtWerden']){
                $this->form_validation->set_rules('show_email_flag', 'Erreichbarkeit mit der Emailadresse', 'callback_validate_email_flag');
            }
        }

        // --- validation rules end ---

		// set the validation rules
		$this->form_validation->set_rules($rules);

		// check for incorrectness
		if($this->form_validation->run() == FALSE)
		{
            // inputs are not valid -> return to the index page and display errors
			$this->index();
		}
		else // validation was correct
		{
			// save changes in the database
			$this->einstellungen_model->save_edits($new_form_values);
            // fire an logging event
            Log::new_log_entry(Log::PERSOENLICHE_DATEN_BEARBEITEN, 7);

            // set a message and redirect to the preferences index page
			$this->message->set('&Auml;nderungen erfolgreich &uuml;bernommen!', 'success');
			redirect(site_url('einstellungen/index'));

		}
    }

	/**
	 * Checks, if the CONFIRMATION-password-field actually got input. If the field has been left empty,
     * the user won't try to update his old password.
     *
     * @access public
	 * @return bool TRUE If an password has been altered. FALSE if nothing has been submitted or the field has been left empty.
	 */
	public function has_password_changed()
	{
		// returns true, if there is input
		// returns false, if the pw-field ist empty
		return (isset($_POST['pw2']) && $_POST['pw2'] != "") ? TRUE : FALSE;
	}

	/**
	 * Checks the entered(given) LoginName for uniqueness and wordlength.
     *
     * @access public
	 * @param string $login The new loginname as an string.
	 * @param string $old_login The old loginname as an string.
	 * @return bool TRUE if Loginname is valid, FALSE otherwise
	 */
	public function validate_loginname($login, $old_login)
	{
		// define min/max length values for the input
		$min = 4;
		$max = 200;

		// check if the loginname has changed
		if ($login != $old_login)
		{
            // does it have less than the minimum length?
            if (!$this->form_validation->min_length($login, $min)){

                // if yes, return "not valid" and set the error-msg
                $this->form_validation->set_message('validate_loginname', 'Der Loginname ist zu kurz. Es werden mindestens 6 Zeichen ben&ouml;tigt.');

                return false;
            }

            // more characters than the maximum length?
            if (!$this->form_validation->max_length($login, $max)){

                $this->form_validation->set_message('validate_loginname', 'Der Loginname ist zu lang. Es d&uuml;rfen h&ouml;chstens 200 Zeichen verwendet werden.');

                return false;
            }

            // the unthinkable did happen. The user hasn't got an (unique) loginname so far. Check for a unique Loginname.
            if (!$this->form_validation->is_unique($login, 'benutzer.LoginName')){

                $this->form_validation->set_message('validate_loginname', 'Loginname schon vorhanden. Bitte w&auml;hle einen anderen Loginnamen.');

                return false;
            }
		
		}

		// if all of the above does not trigger, the Loginname is either valid or the old one -> return true
		return TRUE;
	}
	
	/**
	 * Validates the given email address. It`s just the CodeIgniter "valid_email"-Rule with an custom error message.
	 * Checks, if the email-address has the correct format.
     *
     * @access public
	 * @param string The entered email address as an string
	 * @return bool TRUE if the entered/given email is correct, FALSE if it is not correct
	 */
	public function validate_email($mail)
	{
        // the entered email address is invalid
		if (!$this->form_validation->valid_email($mail)){

            // set the custom error message
            $this->form_validation->set_message('validate_email', 'Keine korrekte Emailadresse. &Uuml;berpr&uuml;fe deine Eingabe!');

            return FALSE;
		}

        // the entered email address is valid
		return TRUE;
	}

    /**
     * Validates the email flag.
     * Workaround: Everytime the function is called it will return TRUE, because the validation rule will only be set if
     * the email flag was changed by the user. Regular validation of an checkbox is not possible, because an unset checkbox
     * will result in no entry in the $POST-Array.
     *
     * @access public
     * @return bool TRUE if the function is called
     */
    public function validate_email_flag(){

        // pseudo validation was successful
        return TRUE;
    }

    /**
     * Loads the change degree program view as an string and echos the view out. Function
     * is designed for ajax-requests.
     *
     * @access public
     * @return void / echos a string with the change degree program view
     */
    public function ajax_load_change_degree_program_view(){

        // load the needed data for changing the degree program and add id to the data array
        $this->data->add('degree_programs', $this->einstellungen_model->get_all_degree_programs());
        // get the student specific userdata (includes degree program name etc.)
        $user_data = $this->einstellungen_model->query_userdata_student($this->user_model->get_userid());
        $this->data->add('student_data', $user_data); // add the user data to the data array

        // --- create the csv file with the old degree program data for export ---
        // add the full qualified name of the student
        $csv_data = "Name:;" . $user_data['Vorname'] . " " . $user_data['Nachname'] . ";\r\r";
        // add the degree program
        $csv_data .= "Studiengang:;" . $user_data['StudiengangName'] . ";\r";
        // add the po
        $csv_data .= "Prüfungsordnung:;" . $user_data['Pruefungsordnung'] . ";\r\r";
        // add table headlines
        $csv_data .= "Fach;Notenpunkte;\r\r";

        // add all degree program courses and the corresponding grades to the file
        $courses_grades = $this->einstellungen_model->get_courses_and_grades($this->user_model->get_userid());

        foreach($courses_grades as $single_course){
            $csv_data .= utf8_decode($single_course['Kursname'] . ";" . (($single_course["Notenpunkte"]==101) ? "-/-" : $single_course["Notenpunkte"]) . ";\r");
        }

        // create and upload the .csv-file
        $path_to_csv_file = $this->einstellungen_model->create_csv_file('semplan_',$csv_data);

        $this->data->add('csv_filepath', $path_to_csv_file);
        // -- end csv file creation ---

        // load the view as an string and return it
        $change_degree_program_view = $this->load->view('einstellungen/change_degree_program', $this->data->load(), TRUE);

        echo $change_degree_program_view;
    }

    /**
     * Form validation for the degree program change. Method is called if the user submits an degree program change.
     * If all necessary inputs are correct the degree program will be changed and an success message will be displayed,
     * otherwise an error message will be displayed.
     *
     * @access public
     * @return void
     */
    public function validate_change_degree_program(){

        // set the custom delimiter for validation errors
        $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');

        // read the values, from actual form
        $new_form_values = $this->input->post();

        // get the current user data from the database
        $current_user_data = $this->einstellungen_model->query_userdata($this->user_model->get_userid());

        // -- set the different form validation rules --
        $this->form_validation->set_rules('degree_program_change_to_id', 'Studiengang', 'callback_validate_selected_degree_program'); // rule for the degree program selection
        $this->form_validation->set_rules('startjahr_change', 'Startjahr', 'required|integer|exact_length[4]'); // rule for the begin year -> is required, and only an 4 character integer value
        $this->form_validation->set_rules('semesteranfang_change', 'Semesteranfang', 'required|alpha'); // rule for the begin semester type -> is required
        // -- end setting form validation rules --

        // only validate the inputs, if there was another degree program selected
        if($new_form_values['degree_program_change_to_id'] != $current_user_data['StudiengangID']){ // another degree program was selected

            if($this->form_validation->run()){ // validation was correct -> save changes

                // change the degree program in the user table
                $this->einstellungen_model->change_degree_program($new_form_values['degree_program_change_to_id']);

                // update the degree program id and the studyplan class variables of the studienplan_model, because the degree program id has changed
                $this->studienplan_model->queryStudycourseId();
                $this->studienplan_model->queryStudyplanId();

                $this->studienplan_model->deleteAll(); // delete the old study plan

                // create a new study plan and an new timetable
                $this->studienplan_model->createStudyplan();

                // add logging
                Log::new_log_entry(Log::PERSOENLICHE_DATEN_BEARBEITEN,7);

                // set a message and redirect to the preferences index page
                $this->message->set('Studiengang wurde erfolgreich gewechselt', 'success');
                redirect(site_url('einstellungen/index'));
            }
            else { // validation was incorrect -> display error message and reload the preferences view

                // set a message and redirect to the preferences index page
                $this->message->set('Beim Wechsel des Studiengangs ist ein Fehler aufgetreten. M&ouml;glicherweise waren nicht alle Felder des Formulars gef&uuml;llt.', 'error');
                redirect(site_url('einstellungen/index'));
            }
        }
        else{ // if there were no changes in the degree program -> return to the personal preferences index page
            $this->index();
        }
    }

    /**
     * Validation for the selected degree program. If the id of the degree program is not 0 the validation returns
     * true.
     *
     * @access public
     * @return bool TRUE if the id of the selected degree program is not 0, otherwise FALSE will be returned
     */
    public function validate_selected_degree_program(){
        // get the form inputs
        $form_input = $this->input->post();

        if($form_input['degree_program_change_to_id'] != 0){
            return TRUE;
        }

        return FALSE;
    }
}
/* End of file einstellungen.php */
/* Location: ./application/controllers/einstellungen.php */