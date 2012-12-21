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
        $this->load->model('persDaten_model');
        $this->load->model('studienplan_model');
        $this->load->model('einstellungen_model');
    }
	
	/**
     * Loads the initial data and shows the custom user settings view with the user specific
     * information / preferences.
     * Check`s the user input against defined rules and saves the changed user specific information
     * in the database.
     *
     * @access public
     * @return void
     */
	public function index()
	{
        // initial database-query to get als required information of the user
        $data['info'] = $this->persDaten_model->getUserInfo();
        $data['stgng'] = $this->persDaten_model->getStudiengang();

        /*
         * setting up the rules, to which the user-input of the corresponding form-fields must comply:
         * Note: the form_validation-class is automagically loaded in the config/autoload.php, so there's no need to load it here.
         */
        $this->form_validation->set_rules('login', 'Loginname', 'callback_validate_loginname['.$data['info']['LoginName'].']');
        $this->form_validation->set_rules('pw2', 'Passwort', 'callback_validate_password');
        $this->form_validation->set_rules('email', 'Email', 'callback_validate_email');
        $this->form_validation->set_rules('matrikel', 'Matrikelnummer', 'callback_validate_matrikelnummer');

        /*
         * Form-Validation works like this:
         *  1. is there POST-data to check? if not, it fails -> no database updating
         *  2. if there is POST-data, it checks every rule above for validation ->if something is wrong -> no update
         *  3. the checked fields (login, pw, email) are either valid in empty state (pw) OR get filled with the current data (login,email), if the user doesn't change anything
         *  Because of that, every POST-data always gets to the db-update, even if there are no rules set up. (like firstname/lastname etc.)
         */
        if ($this->form_validation->run() != FALSE)
        {
            // array of all input-fields
            $fieldarray = array(
                'LoginName' => $_POST['login'],
                'Email' => $_POST['email'],
                'Titel' => $_POST['title'],
                'Raum' => $_POST['room'],
                'Vorname' => $_POST['firstname'],
                'Nachname' => $_POST['lastname'],
                'StudienbeginnJahr' => $_POST['startjahr'],
                'StudienbeginnSemestertyp' => $_POST['semesteranfang'],
                'StudiengangID' => $_POST['stgid']
            );

            // set emailflag. required, because a not checked checkbox results in no $_POST-entry
            $fieldarray['EmailDarfGezeigtWerden'] = isset($_POST['emailflag']) ? 1 : 0;

            if ($this->has_password_changed())
            {
                // add the encrypted passwort
                $fieldarray['Passwort'] = md5($_POST['pw2']);
            }

            // if there is no matrikelnummer yet and the POSTfield is set
            if (($data['info']['MatrikelnummerFlag'] == 0) && isset($_POST['matrikel']))
            {
                // add to the to-be-updated field
                $fieldarray['Matrikelnummer'] = $_POST['matrikel'];
                $fieldarray['MatrikelnummerFlag'] = 1;
            }

            // update the database with the input data
            $this->persDaten_model->update($fieldarray);
            // create log
            $this->persDaten_model->log($data['info']['TypID'], $data['info']['FachbereichID']);

            // the user has changed his degree programm -> so create a new study-plan
            if ($this->hasStudycourseChanged($data['info']['StudiengangID']))
            {
                //echo 'Studiengang wurde geändert';

                //delete old semesterplan
                //$this->studienplan_model->deleteAll();

                //and create a new one
                //$this->studienplan_model->createStudyplan();

                //add the studycourse
                //$fieldarray['StudiengangID'] = $_POST['stgid'];
            }

            // get user and degree program - information
            $data['info'] = $this->persDaten_model->getUserInfo();
            $data['stgng'] = $this->persDaten_model->getStudiengang();
        }

        // load the view
        $this->load->view('einstellungen/index', $data);
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
            var_dump($new_form_values);
        }

        // --- validation rules end ---

		// set the validation rules
		$this->form_validation->set_rules($rules);

		// check for incorrectness
		if($this->form_validation->run() == FALSE)
		{
            // inputs are not valid -> return to the index page and display errors
			$this->desktop_index();
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
	 * 
	 * Loads the "Persönliche Einstellungen" view for desktop version.
	 *
     * @access public
     * @return void
	 * @category einstellungen/index.php
	 * @author Konstantin Voth
	 */
	public function desktop_index()
	{
		// query all needed userinfo
		if ( in_array(Roles::STUDENT, $this->user_model->get_all_roles()))
		{
			$data = $this->einstellungen_model->query_userdata_student($this->user_model->get_userid());
		}
		else
		{
			$data = $this->einstellungen_model->query_userdata($this->user_model->get_userid());
		}
		
		$this->data->add('formdata', $data);

		$this->load->view('einstellungen/index', $this->data->load());
	}

    /**
     * Method for changing the degree program. Actually not working!
     *
     * @access public
     * @return void
     *
     *
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
		
	/**
	 * Checks, if the CONFIRMATION-password-field actually got input. If the field has been left empty, the user won't try to update his old password.
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
	 * Checks, if the entered password meets the requirements:
	 * - minimum/maximum length
	 * - entered same string in both pw-fields
     *
     * @access public
	 * @param string Password that should be checked.
	 * @return bool	TRUE, if the given password is valid or empty
     * TODO Check for deprecated stuff
	 */
	public function validate_password($pw2) {
		
		// min/max length values for the password
		$min = 6;
		$max = 15;
		
		//no error if there's no password submitted
		if ($this->has_password_changed())
		{
		//echo $pw;
		// the should actually be checked for whitespaces or other unwanted characters! But its not yet implemented
//		//check if it contains any suspiscious or probably unwanted characters like spaces, slashes etc.
//		if (!$this->form_validation->min_length($pw, $min)){
//		    //echo 'too short';
//		    //if yes, return "not valid" and set the error-msg
//		    $this->form_validation->set_message('validatePassword', 'Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen');
//		    return false;
//        }
		
		//does it have less than the minimum length?
		if (!$this->form_validation->min_length($pw2, $min)){
			//echo 'too short';
			//if yes, return "not valid" and set the error-msg
			$this->form_validation->set_message('validate_password', 'Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen');
			$this->message->set('Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen', 'error');
			return false;
		}
		//or more than the maximum length?
		if (!$this->form_validation->max_length($pw2, $max)){
			//echo 'too long';
			//if yes, return "not valid" and set the error-msg
			$this->form_validation->set_message('validate_password', 'Passwort zu lang. Benötigt höchstens '.$max.' Zeichen');
			$this->message->set('Passwort zu lang. Benötigt höchstens '.$max.' Zeichen', 'error');
			return false;
		}
		
		//if the length seems valid, check if the pw got doublechecked
		//echo $_POST['pw2'];
		if (!$this->form_validation->matches($pw2, 'pw')){
			//if yes, return "not valid" and set the error-msg
			$this->form_validation->set_message('validate_password', 'Passwort stimmt nicht mit Wiederholung über ein.');
			$this->message->set('Passwort stimmt nicht mit Wiederholung über ein.', 'error');
			return false;
		}
		
		}
		//if all of the above doesnt trigger, the pasword is either valid or not entered
		return TRUE;
	}
	
	
	/**
	 * Checks the entered LoginName for uniqueness and wordlength.
     *
     * @access public
	 * @param string    $login The new loginname as an string.
	 * @param string    $old_login The old loginname as an string.
	 * @return bool TRUE if Loginname is valid, FALSE otherwise
	 */
	public function validate_loginname($login, $old_login)
	{
		// min/max length values for the input
		$min = 4;
		$max = 200;

		// if the Loginname has changed
		if ($login != $old_login)
		{
            // does it have less than the minimum length?
            if (!$this->form_validation->min_length($login, $min)){
                // if yes, return "not valid" and set the error-msg
                $this->form_validation->set_message('validate_loginname', 'Loginname zu kurz. Es werden mindestens 6 Zeichen benötigt.');
                $this->message->set('Loginname zu kurz. Benötigt mindestens 6 Zeichen', 'error');
                return false;
            }

            // more characters than the maximum length?
            if (!$this->form_validation->max_length($login, $max)){
                //echo 'too long';
                //if yes, return "not valid" and set the error-msg
                $this->form_validation->set_message('validate_loginname', 'Loginname zu lang. Benötigt höchstens 200 Zeichen');
                $this->message->set('Loginname zu lang. Benötigt höchstens 200 Zeichen', 'error');
                return false;
            }

            // if the unthinkable did happen and the user hasn't fucked up yet, check for a unique Loginname
            if (!$this->form_validation->is_unique($login, 'benutzer.LoginName'))
            {
                $this->form_validation->set_message('validate_loginname', 'Loginname schon vorhanden. Bitte einen anderen Loginnamen eingeben.');
                $this->message->set('Loginname schon vorhanden. Bitte einen anderen Loginnamen eingeben.', 'error');
                return false;
            }
		
		}

		// if all of the above doesnt trigger, the Loginname is either valid or the old one
		return TRUE;
	}
	
	/**
	 * Validates the given email address. It`s just the CodeIgniter "valid_email"-Rule with an custom error message.
	 * Checks, if the email-address has the correct format
     *
     * @access public
	 * @param string The entered emailaddress as an string
	 * @return bool TRUE if the entered/given email is correct, FALSE if it is not correct
	 */
	public function validate_email($mail)
	{
        // the entered email address is invalid
		if (!$this->form_validation->valid_email($mail)){

            // set the custom error message
            $this->form_validation->set_message('validate_email', 'Keine korrekte Emailadresse. Überprüfen Sie ihre Eingabe!');
		    $this->message->set('Keine korrekte Emailadresse. Überprüfen Sie ihre Eingabe!', 'error');

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
            if (!$this->form_validation->is_natural($_POST['matrikel']))
            {
                $this->message->set('Keine korrekte Matrikelnummer. Überprüfen sie ihre Eingabe', 'error');
                return FALSE;
            }

            // is it already in use by another student?
            if (!$this->form_validation->is_unique($_POST['matrikel'], 'benutzer.Matrikelnummer'))
            {
                $this->message->set('Matrikelnummer wird schon verwendet. Überprüfen sie ihre Eingabe oder wenden sie sich an den Administrator', 'error');
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
     * @return bool TRUE if the email-flag is set, otherwise FALSE
     */
    public function validate_email_flag(){

        // pseudo validation was successful
        return TRUE;
    }
}

/* End of file einstellungen.php */
/* Location: ./application/controllers/einstellungen.php */