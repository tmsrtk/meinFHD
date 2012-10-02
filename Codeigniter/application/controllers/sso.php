<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 *
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Christian Kundruss (CK) <christian.kundruss@fh-duesseldorf.de>
 */

/**
 * Class SSO
 *
 * This class deals with the authentication process via the configured Single-Sign-On IdP, if the users chooses the
 * SSO login method on the login page.
 *
 * @author Christian Kundruss (CK) <christian.kundruss@fh-duesseldorf.de>
 * @todo E-Mail function needs to be configured with the productive configuration.
 */
class SSO extends FHD_Controller {

    // --- definition of class variables begin ---
    /**
     * @var String Stores the User ID of the global authenticated user.
     */
    private $idp_auth_uid;
    /**
     * @var Array Stores the information of the currently authenticated user in an array.
     *            Structure -> BenutzerID, LoginName, Passwort, Email, Vorname, Nachname, FHD_IdP_UID
     */
    private $linked_user;
    // --- definition of class variables end ---

    /**
     * Default constructor. Used for initialization.
     * Is called if an object of this class is created.
     * Loads information about the configured IdP, the authenticated user and the linked user (If there is an linked user)
     * @access public
     * @return void
     * */
    public function __construct() {
        parent::__construct();

        // load the sso model
        $this->load->model('SSO_model');
        $this->load->model('admin_model');

        // at first get the attributes of the authenticated user
        $idp_attributes = $this->samlauthentication->get_attributes();
        // save the uid provided by the idp for further access -> the array needs to be splitted to hold only the uid in the variable
        $this->idp_auth_uid = $idp_attributes['uid']['0'];
        // get all information of the linked user and save them in the appropriate class variable
        $this->linked_user = $this->get_linked_user();
    }

    /**
     * Method starts the authentication process with the configured idp.
     *
     * @access public
     * @return bool FALSE if an connection to the configured IdP can not be established.
     */
    public function authenticate() {

        // ask for authentication at the sso idp -> redirect to the idp`s login page automatically if no global session exists
        if(!$this->samlauthentication->is_authenticated()) {
            $this->samlauthentication->require_authentication(); // ask for global authentication
        }

        // check if the authentication was successful (and if there is an linked account) -> then establish a local session. otherwise not....
        if ($this->samlauthentication->is_authenticated()) {

            // if the user has got an linked account -> perform login with the linked local user account
            // if the global uid is linked to an local account establish a local session
            if ($this->linked_user) {
                $this->establish_local_session();
            }

            // the user has no linked account and is not blacklisted -> give him the possibility to link or create an account
            // otherwise request for linking: 2 alternatives -> user has got an account and links; -> user requests an account that is automatically linked
            else {
                redirect('sso/link_account');
            }
        }

        return FALSE;
    }

    /**
     * Establishes an local session with the account, that is linked to the global authenticated user.
     *
     * @access public
     * @return void
     */
    public function establish_local_session () {

        // if the returned array is not empty-> perform login.. -> establish a local session via the authentication library... (DOKU makes use of "third party")
        // establish a local session with the information in the linked_user array, use therefore the provided authentication library
        if($this->authentication->sso_login($this->linked_user['LoginName'], $this->linked_user['Passwort'])) {
            // establish local session
            // user is logged in -> show message and redirect to the dashboard
            $message_body = 'Hallo ' . $this->authentication->get_name() . ' (User-ID: ' . $this->authentication->user_id() . '), du hast dich erfolgreich eingeloggt!';
            $this->message->set(sprintf($message_body));
            redirect('dashboard/index');
        }
    }

    /**
     * Function returns the information for the linked user. If the user is not linked false will be returned
     * Wrapper function for multiple calls of the same operation.
     *
     * @access public
     * @return mixed Returns information about the linked local user account in an array, or FALSE if there isn`t an linked account.
     */
    public function get_linked_user() {
        // check in the database if the authenticated user has got an local linked user account
        // -> call the sso model to do this.... because it needs to talk with the database
        $linked_user_information = $this->SSO_model->get_linked_user($this->idp_auth_uid);

        // if an linked user exists return the provided user information, otherwise false
        if ($linked_user_information){
            return $linked_user_information;
        }

        return FALSE;
    }

    /**
     * Links an global authenticated account with an supplied local identity . The local identity, that should be linked is submitted / inputted by the user.
     * If the account was successfully created an email is going to be sent to the user.
     *
     * @access public
     * @return void
     * @todo Final configuration of E-Mail function. Messages are already implemented.
     */
    public function link_account() {

        // --- protect the link account function for calls without having a global session --
        if (!$this->samlauthentication->is_authenticated() || $this->samlauthentication->has_linked_account()) {
            redirect('app/login');
        }

        // read the post parameters
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // check if we have values for the username and password
        if ($username && $password) {

            // check if the global account is on the blacklist
            if($this->SSO_model->is_blacklisted($this->idp_auth_uid)){
                // show message and redirect back to the link account page
                $message_body = '<p>Beim Verknüpfen Deines lokalen Accounts mit dem zentralen Account ist ein Fehler aufgetreten. Dein zentraler'.
                    ' Account ist für meinFHD gesperrt. Bitte kontaktiere den Support unter <a href="mailto:meinfhd.medien@fh-duesseldorf.de">meinfhd.medien@fh-duesseldorf.de</></p>';
                $this->message->set(sprintf($message_body));
                redirect('app/login'); // reload controller for displaying the error message
            }

            // query the user table to check if the given identity is correct and not linked
            $this->db->select('BenutzerID, LoginName, Passwort, Vorname, Nachname');
            $this->db->from('benutzer');
            $this->db->where('LoginName', $username);
            $this->db->where('Passwort', MD5($password));
            $this->db->where('FHD_IdP_UID',NULL); // account is not linked

            $query = $this->db->get();
            // there should be only 1 row in the db
            if($query->num_rows() == 1) {
                // get the inputted userid
                $local_uid = $query->row()->BenutzerID;

                // link account
                if($this->SSO_model->link_account($local_uid, $this->idp_auth_uid)) { // link was successful
                    // update data of the linked user
                    $this->linked_user = $this->get_linked_user();

                    // TODO: send the user a mail, that his local account is linked
                    $email_reciever = $this->linked_user['Email'];

                    $email_subject = '[meinFHD] Account erfolgreich verkn&uuml;pft';

                    $email_message_body = '<p>Dein lokaler Account wurde erfolgreich mit dem globalen Benutzeraccount' . $this->linked_user['FHD_IdP_UID'] .
                        ' verkn&uuml;pft.</p><p>Du kannst dich jeder Zeit &uuml;ber das Shibboleth-Verfahren anmelden!</p>';

                    // call the send mail method

                    // establish local session / login
                    $this->establish_local_session();
                }
            }

            else { // link was not successful
                // show message and redirect back to the link account page
                $message_body = '<p>Beim Verknüpfen Deines lokalen Accounts mit dem zentralen Account ist ein Fehler aufgetreten. Bitte überprüfe die von dir '.
                                'eingegebene Kombination aus Benutzername und Passwort.</p> <p>Sollte der Fehler weiterhin auftreten kontaktiere bitte den Support unter' .
                                ' <a href="mailto:meinfhd.medien@fh-duesseldorf.de">meinfhd.medien@fh-duesseldorf.de</a></p>';
                $this->message->set(sprintf($message_body),'error');
                redirect('sso/link_account'); // reload controller for displaying the error message
            }
        }


        // add all needed data to the view
        $this->data->add('all_departments', $this->SSO_model->get_all_departments()); // add all departments
        $this->data->add('all_stdgnge', $this->admin_model->get_all_degree_programs()); // add all degree programs

        // load the link account view
        $this->load->view('sso/link_account', $this->data->load());
    }

    /**
     * Validates if all user inputs in the create account form are in the correct format.
     * If they are in the correct format the new account is going to be created, otherwise the form
     * will be repopulated.
     * While creating the account the method checks if the asking global UID is blacklisted so far. If the uid is
     * on the blacklist an invitation is going to be saved and an email will be send to the user and support.
     * If the global uid is not blacklisted the account will be created and an welcome e-mail will be sent to the user.
     *
     * @access public
     * @return void
     */
    public function validate_create_account_form() {
        // set custom delimiter for validation errors
        $this->form_validation->set_error_delimiters('<div id="createAccountErrors" class="alert alert-error">', '</div>');

        // prepare the validation rules array
        $rules = array();

        $rules[] = $this->adminhelper->get_formvalidation_role();
        $rules[] = $this->adminhelper->get_formvalidation_email();
        $rules[] = $this->adminhelper->get_formvalidation_forename();
        // set the validation rules
        $this->form_validation->set_rules($rules);

        // get the role that was selected
        $role = $this->input->post('role');

        // different validation rules if the user wants to create a student or dozent account

        // student validation
        if ($role === '5'/*student*/)
        {
            // prepare the student validation rules
            $rules = array();

            $rules[] = $this->adminhelper->get_formvalidation_studiengang();
            //$rules[] = $this->adminhelper->get_formvalidation_matrikelnummer();

            // set the student validation rules
            $this->form_validation->set_rules($rules);

            // query if erstsemestler checkbox was checked or not
            if ( empty($form_values['erstsemestler']) )
            {
                // if not checked, -> invitation for non erstsemestler, -> more inputs to fill out
                $rules[] = $this->adminhelper->get_formvalidation_startjahr();
                $rules[] = $this->adminhelper->get_formvalidation_semesteranfang();

                $this->form_validation->set_rules($rules);
            }
        }

        // run the validation and check if everything is alright
        if ($this->form_validation->run() == FALSE) {
            // something is wrong -> call the mask again
            $this->link_account();
        }
        else { // the inputs are correct

            // get the user inputs
            $form_data = $this->input->post();

            // check if the given matrikelnummer has an account or not
            if($this->SSO_model->check_matrikelnummer_has_account($form_data['matrikelnummer'])){ // the inputted matrikelnummer has already an account
                // do nothing, but show the user a message
                $message_body = '<p>Für die von dir angegebene Matrikelnummer existiert bereits ein lokaler Account. Solltest du dein Passwort vergessen haben,' .
                    ' so kannst du dir auf der <a href=" <?php print base_url()?>'.'/app/login">Startseite</a> ein neues anfordern.</p>
                                <p>Andernfalls nehme bitte Kontakt mit dem Support unter <a href="mailto:meinfhd.medien@fh-duesseldorf.de">meinfhd.medien@fh-duesseldorf.de</a> auf.</p>';
                $this->message->set(sprintf($message_body),'error');
                // load the form again
                redirect('sso/validate_create_account_form');
            }
            // is global uid blacklisted?
            else if($this->SSO_model->is_blacklisted($this->idp_auth_uid)) {
                // save an user invitation
                $this->_save_user_request($form_data);

                // TODO: send email to admin, that a new request has been saved

                $email_reciever = 'meinfhd.medien@fh-duesseldorf.de'; // where to get the email address from?!

                $email_subject = '[meinFHD] Einladungs-Anforderung einer geblacklisteten Benutzer-ID';


                $email_message_body = '<h2>Einladungs-Anfoderung</h2><p>Es existiert eine Anfrage f&uuml;r Benutzereintragung mit der globalen Benutzer-ID: ' .
                                       $this->idp_auth_uid . ', die auf der Blacklist steht.</p><p>Bitte &uuml;berpr&uuml;fe die vorliegende Anfrage.</p>';

                // TODO: send mail to user, that he has to wait

                // use the same variables
                $email_reciever = $form_data['email']; // the user email address

                $email_subject = '[meinFHD] Deine Anfrage wurde gespeichert';

                $email_message_body = '<p>Deine Anfrage wurde als Einladung gespeichert. Bitte habe Verst&auml;ndnis, dass die Freischaltung nicht sofort erfolgen kann, ' .
                    'sondern erst durch einen Administrator pers&ouml;nlich freigeschaltet werden muss. In der Regel dauert das nicht l&auml;nger als einen Tag.</p>';

               // call the sendmail method


                // show a message -> contact admin and redirect back to the login page
                $message_body = '<p>Beim Anlegen des lokalen Accounts ist ein Fehler aufgetreten. Deine Informationen wurden als Einladung gespeichert und der Support'.
                    ' darüber informiert. Solltest du keine Rückmeldung erhalten kontaktiere bitte den Support unter <a href="mailto:meinfhd.medien@fh-duesseldorf.de">meinfhd.medien@fh-duesseldorf.de</a></p>';
                $this->message->set(sprintf($message_body),'error');
                // redirect to login-form
                redirect('app/login');
            }
            else { // user is not blacklisted -> create his account
                $this->_create_user($form_data);

                // TODO: send email to user that the account was successfully created

                $email_reciever = $form_data['email'];

                $email_subject = '[meinFHD] Herzlich Willkommen';

                $email_message_body = '<h2>Herzlich Willkommen auf meinFHD - Deiner Studienplanungs-Plattform!</h2><br/>' .
                                      '<p>Du hast dich erfolgreich bei meinFHD angemeldet. Der erstellte lokale Account wurde mit deinem zentralen Account ' .
                                      'erfolgreich verkn&uuml;pft. Du kannst dich jederzeit problemlos &uuml;ber das Shibboleth-Verfahren bei meinFHD einloggen.</p>' .
                                      '<p>Solltest du dich lokal anmelden wollen, so musst du dir ein Passwort auf <a href="<?php print base_url(); ?>">meinFHD</a> '.
                                      'anfordern. Dein lokaler Account hat folgenden Benutzernamen: ' . $form_data['email'] .'</p>' .
                                      '<p>F&uuml;r den schnellen Einstieg findest Du unter FAQ und Hilfe Antworten auf h&auml;fig auftretende Fragen und eine '.
                                      '&Uuml;bersicht &uuml;ber Deine M&ouml:glichkeiten in meinFHD. Bis bald, bei meinFHD!</p>'.
                                      '<p>Dein meinFHD-Team</p>';

                // call the send mail method

                // load the user welcome view
                $this->load->view('sso/user_welcome', $this->data->load());
            }
        }
    }

    /**
     * Creates a new user and links him to the global user id.
     *
     * @access private
     * @param Array $form_data The user form input from the create account mask.
     */
    private function _create_user ($form_data) {
        // generate a custom password
        $password = $this->adminhelper->passwort_generator();
        $form_data['password'] = $password;
        $form_data['FHD_IdP_UID'] = $this->idp_auth_uid;

        // add the right 'benutzertyp' to the array
        if ($form_data['role'] == '2') { // user is a dozent
            $form_data['TypID'] = '5';
        }
        else if($form_data['role'] == '5') { // user is a student
            $form_data['TypID'] = '7';
        }

        // insert the user into the database and link his account
        $this->SSO_model->save_new_user($form_data);
    }

    /**
     * Saves a new user request(invitation) with the global UID to be able to link the account after submitting
     * the invitation.
     *
     * @access private
     * @param Array $form_data The user form input from the create account mask.
     */
    private function _save_user_request ($form_data) {
        // add the global uid to the user input
        $form_data['FHD_IdP_UID'] = $this->idp_auth_uid;
        $this->SSO_model->save_user_invitation($form_data);
    }
}
/* End of file sso.php */
/* Location: ./application/controllers/sso.php */