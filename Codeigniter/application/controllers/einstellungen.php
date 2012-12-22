<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.2
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Jan Eichler(JE), <jan.eichler@fh-duesseldorf.de>
 * @author Christian Kundruß(CK), <christian.kundruss@fh-duesseldorf.de>
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
     * Loads the 'Persönliche Einstellungen' View.
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
     * 'Änderungen speichern'.
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
                $this->form_validation->set_rules('login', 'Loginname', 'callback_validate_loginname['.$data['info']['LoginName'].']');
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

            // set a message and redirect to the index page
			$this->message->set('Änderungen erfolgreich übernommen', 'success');
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
                $this->form_validation->set_message('validate_loginname', 'Loginname zu kurz. Es werden mindestens 6 Zeichen benötigt.');
                $this->message->set('Loginname zu kurz. Es werden mindestens 6 Zeichen benötigt.', 'error');

                return false;
            }

            // more characters than the maximum length?
            if (!$this->form_validation->max_length($login, $max)){

                $this->form_validation->set_message('validate_loginname', 'Loginname zu lang. Es dürfen höchstens 200 Zeichen verwendet werden.');
                $this->message->set('Loginname zu lang. Es drüfen höchstens 200 Zeichen verwendet werden.', 'error');

                return false;
            }

            // the unthinkable did happen. The user hasn't got an (unique) loginname so far. Check for a unique Loginname.
            if (!$this->form_validation->is_unique($login, 'benutzer.LoginName')){

                $this->form_validation->set_message('validate_loginname', 'Loginname schon vorhanden. Bitte wähle einen anderen Loginnamen.');
                $this->message->set('Loginname schon vorhanden. Bitte wähle einen anderen Loginnamen.', 'error');

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
            $this->form_validation->set_message('validate_email', 'Keine korrekte Emailadresse. Überprüfe deine Eingabe!');
		    $this->message->set('Keine korrekte Emailadresse. Überprüfe deine Eingabe!', 'error');

            return FALSE;
		}

        // the entered email address is valid
		return TRUE;
	}

    /**
     * Validates the entered matrikelnummer for correctness.
     *
     * @access public
     * @return bool TRUE if the entered matrikelnummer is correct, FALSE otherwise
     */
    public function validate_matrikelnummer()
    {
        if (isset($_POST['matrikel'])){

            // does it contain any letters or other non-numbery characters?
            if (!$this->form_validation->is_natural($_POST['matrikel'])){

                $this->message->set('Keine korrekte Matrikelnummer. Überprüfe deine Eingabe!', 'error');
                return FALSE;
            }

            // is it already in use by another student?
            if (!$this->form_validation->is_unique($_POST['matrikel'], 'benutzer.Matrikelnummer')){

                $this->message->set('Matrikelnummer wird schon verwendet. Überprüfe deine Eingabe oder wende dich an den Administrator!', 'error');
                return FALSE;
            }
        }

        // if none of the above triggers, the entered number seems to be valid
        return TRUE;
    }

    /**
     * Validates the email flag.
     * Workaround: Everytime the function is called it will return TRUE, because the validation rule will only be set if
     * the email flag was changed by the user. Regular validation of an checkbox is not possible, because an unset checkbox
     * will result in no entry in the $POST-Array.
     *
     * @return bool TRUE if the function is called
     */
    public function validate_email_flag(){

        // pseudo validation was successful
        return TRUE;
    }

    /**
     * Method for changing the degree program. Actually not working!
     *
     * @access public
     * @return void
     * TODO Degree programm change is not working so far.
     */
    public function studiengangWechseln()
    {

        $data['stgng'] = $this->persDaten_model->getStudiengang();
        $data['info'] = $this->persDaten_model->getUserInfo();

        // ----------- Not working --------------- //
        //because the way the form_validation back in index() works, it does not recognize the StudiengangID (stgid) in the POST-Array, if it got submitted by the View of this function
        //to fix this, we simply add the required POST-fields like they would be in the index()-function:
        //$_POST['login'] = $data['info']['LoginName'];       //login = old login -> no error during form_validation
        //$_POST['email'] = $data['info']['Email'];           //same
        // ---------------------------------------------

        //create a String in csv.-encoding
        //first add the full name of the student
        $table = "Name:;".$data['info']['Vorname']." ".$data['info']['Nachname'].";\r\r";
        //then add the studycourse and PO
        $table .= "Studiengang:;".$data['info']["StudiengangName"].";\rPrüfungsordnung:;".$data['info']["Pruefungsordnung"].";\r\r";
        //then add all courses and the corresponding grades:
        $result = $this->persDaten_model->getCoursesAndGrades();
        foreach ($result as $row)
        {
            $table .= utf8_decode($row["Kursname"]).";".(($row["Notenpunkte"]==101) ? "-/-" : $row["Notenpunkte"]).";\r";
        }

        //create *.csv
        $data['filepath'] = $this->persDaten_model->createCsv($table);


        //View
        $this->load->view('einstellungen/studiengangWechseln', $data);
    }

    /**
     * Recognizes if the degree program was changed by the authenticated user
     *
     * @access public
     * @param $old_id
     * @return bool
     */
    public function hasStudycourseChanged($old_id)
    {
        return (isset($_POST['stgid']) && $_POST['stgid'] != $old_id) ? TRUE : FALSE;
    }
}

/* End of file einstellungen.php */
/* Location: ./application/controllers/einstellungen.php */