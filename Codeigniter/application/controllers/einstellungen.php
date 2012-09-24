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
    
    private $userid;
    var $data;
    
    function __construct()
		{
			error_reporting(E_ERROR);
		  parent::__construct();	
		  $this->load->model('persDaten_model');
		  
		  //$this->userid = 1383;
		  $this->userid = $this->authentication->user_id();
                  $this->data = array();
		}
	
	
	function index()
	{
	    
		//initial database-query to get als required information of the user
		$this->data['info'] = $this->persDaten_model->getUserInfo($this->userid);
		$this->data['stgng'] = $this->persDaten_model->getStudiengang();

		//setting up the rules, to which the user-input of the corresponding form-fields must comply:
		//Note: the form_validation-class is automagically loaded in the config/autoload.php, so there's no need to load it here.
		$this->form_validation->set_rules('login', 'Loginname', 'callback_validateLoginname['.$this->data['info']['LoginName'].']');
		$this->form_validation->set_rules('pw', 'Passwort', 'callback_validatePassword');
		$this->form_validation->set_rules('email', 'Email', 'callback_validateEmail');

		//$this->krumo->dump($data);
		//$this->krumo->dump($_POST);
		//print_r($this->authentication->user_id());
		//$this->load->view('einstellungen', $data);
		if ($this->form_validation->run() == FALSE)
		{
			//echo 'fehler';
			$this->load->view('einstellungen', $this->data);
			//$this->persDaten_model->update();
		}
		else
		{		
			//array of all input-fields
			$fieldarray = array(
			    'Loginname' => $_POST['login'],
			    'Email' => $_POST['email'],
			    'Titel' => $_POST['title'],
			    'Raum' => $_POST['room'],
			    'Vorname' => $_POST['firstname'],
			    'Nachname' => $_POST['lastname'],
			    'StudienbeginnJahr' => $_POST['year'],
			    'StudienbeginnSemestertyp' => $_POST['semester'],
			    //'StudiengangID' => $_POST['stgid'] 
                            );
			
			//set emailflag. required, because a not checked checkbox results in no $_POST-entry
			$fieldarray['EmailDarfGezeigtWerden'] = isset($_POST['emailflag']) ? 1 : 0;
			
			if ($this->hasPasswordChanged())
			{
			    echo 'Password wurde geändert';
			    //ToDO: Email versenden!
			    
			    //add the encrypted passwort
			    $fieldarray['Passwort'] = md5($_POST['pw']);
			}
                        
                        if ($this->hasStudycourseChanged())
			{
			    echo 'Studiengang wurde geändert';
			    
			    //add the encrypted passwort
			    $fieldarray['StudiengangID'] = $_POST['stgid'];
			}
			
			//update database
			$this->persDaten_model->update($this->userid, $fieldarray);
			//create log
			$this->persDaten_model->log($this->data['info']['TypID'], $this->data['info']['FachbereichID']);

			$this->data['info'] = $this->persDaten_model->getUserInfo($this->userid);
                        $this->data['stgng'] = $this->persDaten_model->getStudiengang();

			$this->load->view('einstellungen', $this->data);
	  }
	}
	
	function testlog()
	{
	    $this->persDaten_model->log(array());
	}
	
	function update()
	{
	    $data['stuff'] = 'bab';
	    $this->load->view('einstellungen_update', $data);
	}
	
         function hasStudycourseChanged()
        {
            echo (isset($_POST['stgid']) && $_POST['stgid'] != $this->data['info']['StudiengangID']) ? 'TRUE' : 'FALSE';
            return (isset($_POST['stgid']) && $_POST['stgid'] != $this->data['info']['StudiengangID']) ? TRUE : FALSE;
        }
        
	/**
	 * checks, if the pw-field actually got input.
	 * @return TRUE if password has been altered. FALSE if nothing got submitted or field is empty
	 */
	function hasPasswordChanged()
	{
	    //returns true, if there is input
	    //returns false, if the pw-field ist empty
	    return (isset($_POST['pw']) && $_POST['pw'] != "") ? TRUE : FALSE;
	    
	}

	/**
	 * Checks, if the entered password meets the requirements:
	 * - minimum/maximum length
	 * - entered same string in both pw-fields
	 * @param string	password
	 * @return boolean	TRUE, if password is valid or empty
	 */
	function validatePassword($pw) {
	    
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
		if (!$this->form_validation->min_length($pw, $min)){
		    //echo 'too short';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validatePassword', 'Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen');
		    $this->message->set('Passwort zu kurz. Benötigt mindestens '.$min.' Zeichen', 'error');
		    return false;
		}
		//or more than the maximum length?
		if (!$this->form_validation->max_length($pw, $max)){
		    //echo 'too long';
		    //if yes, return "not valid" and set the error-msg
		    $this->form_validation->set_message('validatePassword', 'Passwort zu lang. Benötigt höchstens '.$max.' Zeichen');
		    $this->message->set('Passwort zu lang. Benötigt höchstens '.$max.' Zeichen', 'error');
		    return false;
		}
		
		//if the length seems valid, check if the pw got doublechecked
		//echo $_POST['pw2'];
		if (!$this->form_validation->matches($pw, 'pw2')){
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
