<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of einstellungen
 *
 * @author jan
 */
class einstellungen extends FHD_Controller{
    
    //private $userid;
    //var $data;
    
    public function __construct()
		{
			error_reporting(E_ERROR);
		  parent::__construct();	
		  $this->load->model('persDaten_model');
                  $this->load->model('studienplan_model');
		  
		  //$this->userid = 1383;
		  //$this->userid = $this->authentication->user_id();
                  //$this->data = array();
		}
	
	
	function index()
	{
	    
		//initial database-query to get als required information of the user
		$data['info'] = $this->persDaten_model->getUserInfo();
		$data['stgng'] = $this->persDaten_model->getStudiengang();

                //$this->krumo->dump($data);
		//setting up the rules, to which the user-input of the corresponding form-fields must comply:
		//Note: the form_validation-class is automagically loaded in the config/autoload.php, so there's no need to load it here.
		$this->form_validation->set_rules('login', 'Loginname', 'callback_validateLoginname['.$data['info']['LoginName'].']');
		$this->form_validation->set_rules('pw2', 'Passwort', 'callback_validatePassword');
		$this->form_validation->set_rules('email', 'Email', 'callback_validateEmail');

		//$this->krumo->dump($data);
		//$this->krumo->dump($_POST);
		//print_r($this->authentication->user_id());
		//$this->load->view('einstellungen', $data);
                
                //Form-Validation works like this:
                //  1. is there POST-data to check? if not, it fails -> no database updating
                //  2. if there is POST-data, it checks every rule above for validation ->if something is wrong -> no update
                //  3. the checked fields (login, pw, email) are either valid in empty state (pw) OR get filled with the current data (login,email), if the user doesn't change anything
                //      Because of that, every POST-data always gets to the db-update, even if there are no rules set up. (like firstname/lastname etc.)
		if ($this->form_validation->run() == FALSE)
		{
                    echo 'NICHTS PASSIERT';
			//echo 'fehler';
			$this->load->view('einstellungen', $data);
			//$this->persDaten_model->update();
		}
		else
		{		
			//array of all input-fields
			$fieldarray = array(
			    'LoginName' => $_POST['login'],
			    'Email' => $_POST['email'],
			    'Titel' => $_POST['title'],
			    'Raum' => $_POST['room'],
			    'Vorname' => $_POST['firstname'],
			    'Nachname' => $_POST['lastname'],
			    'StudienbeginnJahr' => $_POST['year'],
			    'StudienbeginnSemestertyp' => $_POST['semester'],
			    'StudiengangID' => $_POST['stgid'] 
                            );
			
			//set emailflag. required, because a not checked checkbox results in no $_POST-entry
			$fieldarray['EmailDarfGezeigtWerden'] = isset($_POST['emailflag']) ? 1 : 0;
			
			if ($this->hasPasswordChanged())
			{
			    echo 'Password wurde geändert';
			    //ToDO: Email versenden!
			    
			    //add the encrypted passwort
			    $fieldarray['Passwort'] = md5($_POST['pw2']);
			}
                        
                        
			
			//update database
			$this->persDaten_model->update($fieldarray);
                        
                        if ($this->hasStudycourseChanged($data['info']['StudiengangID']))
			{
			    echo 'Studiengang wurde geändert';
			    
                            //delete old semesterplan
                            //$this->studienplan_model->deleteAll();
                            
                            //and create a new one
                            //$this->studienplan_model->createStudyplan();
                            
			    //add the studycourse
			    //$fieldarray['StudiengangID'] = $_POST['stgid'];
			}
			//create log
			$this->persDaten_model->log($data['info']['TypID'], $data['info']['FachbereichID']);
                        
                        //If the studycourse got updated, delete all old references and db-entries
                        if ($this->hasStudycourseChanged($data['info']['StudiengangID']))
			{
			    echo 'Studiengang wurde geändert';
			    
                            //delete old semesterplan
                            //$this->studienplan_model->deleteAll();
                            
                            //and create a new one
                            //$this->studienplan_model->createStudyplan();
                            //$this->studienplan_model->createTimetableCourses();
                            
			}

			$data['info'] = $this->persDaten_model->getUserInfo();
                        $data['stgng'] = $this->persDaten_model->getStudiengang();

			$this->load->view('einstellungen', $data);
	  }
	}
	
        function studiengangWechseln()
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
            $this->load->view('einstellungen_studiengangWechseln', $data);
        }
	
	
        function hasStudycourseChanged($old_id)
        {
            return (isset($_POST['stgid']) && $_POST['stgid'] != $old_id) ? TRUE : FALSE;
        }
        
	/**
	 * checks, if the CONFIRMATION-password-field actually got input. If this field has been left empty, the user won't try to update his old password.
         * WAS formerly checking the first password-field, but some Browsers (Firefox e.g) fill the field automatically, so that this method always returned TRUE without any input by the user
	 * @return TRUE if password has been altered. FALSE if nothing got submitted or field is empty
	 */
	function hasPasswordChanged()
	{
	    //returns true, if there is input
	    //returns false, if the pw-field ist empty
	    return (isset($_POST['pw2']) && $_POST['pw2'] != "") ? TRUE : FALSE;
	    
	}

	/**
	 * Checks, if the entered password meets the requirements:
	 * - minimum/maximum length
	 * - entered same string in both pw-fields
	 * @param string	password
	 * @return boolean	TRUE, if password is valid or empty
	 */
	function validatePassword($pw2) {
	    
	    // min/max length values for the passwort
	    $min = 6;
	    $max = 15;
	    
	    //no error if there's no password submitted
	    if ($this->hasPasswordChanged())
	    {
		//echo $pw;
		//the should actually be checked for whitespaces or other unwanted characters! But its not yet implemented
//		//check if it contains any suspiscious or probably unwanted characters like spaces, slashes etc.
//		if (!$this->form_validation->min_length($pw, $min)){
//		    //echo 'too short';
//		    //if yes, return "not valid" and set the error-msg
//		    $this->form_validation->set_message('validatePassword', 'Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen');
//		    return false;
//		}
//		
		
		//does it have less than the minimum length?
		if (!$this->form_validation->min_length($pw2, $min)){
		    //echo 'too short';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validatePassword', 'Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen');
		    $this->message->set('Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen', 'error');
		    return false;
		}
		//or more than the maximum length?
		if (!$this->form_validation->max_length($pw2, $max)){
		    //echo 'too long';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validatePassword', 'Passwort zu lang. Benötigt höchstens '.$max.' Zeichen');
		    $this->message->set('Passwort zu lang. Benötigt höchstens '.$max.' Zeichen', 'error');
		    return false;
		}
		
		//if the length seems valid, check if the pw got doublechecked
		//echo $_POST['pw2'];
		if (!$this->form_validation->matches($pw2, 'pw')){
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validatePassword', 'Passwort stimmt nicht mit Wiederholung über ein.');
		    $this->message->set('Passwort stimmt nicht mit Wiederholung über ein.', 'error');
		    return false;
		}
		
	    }
	    
	    //if all of the above doesnt trigger, the pasword is either valid or not entered
	    return TRUE;
	}
	
	
	/**
	 * Checks the entered LoginName for uniqueness and wordlength 
	 * @param string    $login
	 * @param string    $old_login
	 * @return boolean  TRUE, if Loginname is valid
	 */
	function validateLoginname($login, $old_login)
	{
//	    echo $login;
//	    echo $old_login;
	    
	    // min/max length values for the input
	    $min = 4;
	    $max = 200;
//	    print_r($login);
//	    print_r($old_login);
//	    
	    //if the Loginname was changed:
	    if ($login != $old_login)
	    {
		//does it have less than the minimum length?
		if (!$this->form_validation->min_length($login, $min)){
		    //echo 'too short';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validateLoginname', 'Loginname zu kurz. Benötigt mindestens 6 Zeichen');
		    $this->message->set('Loginname zu kurz. Benötigt mindestens 6 Zeichen', 'error');
		    return false;
		}
		//or more than the maximum length?
		if (!$this->form_validation->max_length($login, $max)){
		    //echo 'too long';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validateLoginname', 'Loginname zu lang. Benötigt höchstens 200 Zeichen');
		    $this->message->set('Loginname zu lang. Benötigt höchstens 200 Zeichen', 'error');
		    return false;
		}
		
		//if the unthinkable did happen and the user hasn't fucked up yet, check for a unique Loginname
		if (!$this->form_validation->is_unique($login, 'benutzer.LoginName'))
		{
		    $this->form_validation->set_message('validateLoginname', 'Loginname schon vorhanden. Bitte einen anderen eingeben.');
		    $this->message->set('Loginname schon vorhanden. Bitte einen anderen eingeben.', 'error');
		    return false;
		}
		
	    }
	    
	    //if all of the above doesnt trigger, the Loginname is either valid or the old one
	    return TRUE;
	}
	
	/**
	 * Just the CodeIgniter "valid_email"-Rule, only with a custom-Errormessage
	 * Checks, if the email-address has the correct format
	 * @param string    entered emailaddress
	 * @return Boolean  TRUE if mail is correct, FALSE if not
	 */
	function validateEmail($mail)
	{
	    if (!$this->form_validation->valid_email($mail)){
		$this->form_validation->set_message('validateEmail', 'Keine korrekte Emailadresse. Überprüfen sie ihre Eingabe');
		$this->message->set('Keine korrekte Emailadresse. Überprüfen sie ihre Eingabe', 'error');
		return FALSE;
		
	    }
	    return TRUE;
	}
}

?>
