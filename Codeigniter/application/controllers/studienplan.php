<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Fabian Martinovic (FM), <fabian.martinovic@fh-duesseldorf.de>
 */


/**
 * Studienplan Controller
 */
class Studienplan extends FHD_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');

        // userdata
        $session_userid = $this->authentication->user_id();

        $loginname = $this->admin_model->get_loginname($session_userid);
        $user_permissions = $this->admin_model->get_all_userpermissions($session_userid);
        $roles = $this->admin_model->get_all_roles();
        
        $userdata = array(
                'userid' => $session_userid,
                'loginname' => $loginname['LoginName'],
                'userpermissions' => $user_permissions,
                'roles' => $roles
            );

        $this->data->add('userdata', $userdata);
    }


    

    /**
     * Index-Method, which loads the Studienplan
     */
    public function index()
    {
        // load model
        $this->load->model('Studienplan_Model');
        $plan = $this->Studienplan_Model->queryStudyplan();
        
        // Calculate needed options
        $this->swsUndCpBerechnen();
        $this->durchschnittsnoteBerechnen();
        $this->modulinfo();
        $this->prozentsatzBerechnen();
        $this->pruefenTeilnehmenHolen();
        
        $data['main_content'] = 'semesterplan_show';
        
        // add the resultset/array to the data-object
        $this->data->add('studienplan', $plan);
        //$this->load->view('studienplan', $this->data->load());
        //$this->load->view('semesterplan_show', $this->data->load());
        
        $data['global_data'] = $this->data->load();
        $this->load->view('includes/template', $data);
    }


    

     /**
     * Show Studienplan, Testmethod!!!
     *
     * @author Konstantin Voth
     */
    public function studienplan_show()
    {
        $this->load->model('Studienplan_Model');

        $data['title'] = 'Semesterplan';
        $data['main_content'] =  'semesterplan_show';

        //----------------------------------------------------------------------
        $plan = $this->Studienplan_Model->queryStudyplan();
        $this->data->add('studienplan', $plan);
        $data['semesteranzahl'] = 7;         // TODO: !!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //----------------------------------------------------------------------
        $data['global_data'] = $this->data->load();

        $this->load->view('includes/template', $data);
    }
    
    
    
    
    /**
     * Mobile Index-Method, which loads the Studienplan
     */
    /*public function mobile_index()
    {
        // load model
        $this->load->model('Studienplan_Model');
        $plan = $this->Studienplan_Model->queryStudyplan();
        $this->swsUndCpBerechnen();
        
        // add the resultset/array to the data-object
        $this->data->add('studienplan', $plan);
        $this->load->view('studienplan', $this->data->load());
    }*/
    
    
    
    
    /**
     * Create a studyplan
     */
    public function studienplanErstellen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->createStudyplan();
        
        $this->message->set(sprintf('Der Studienplan wurde erfolgreich erstellt.'));
    }
    
    
    
    
    /**
     * Add a new coloumn to semesterplan
     */
    public function spalteEinfuegen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->createNewSemesterColoumn();
    }
    
    
    
    
    /**
     * Mobile Update new position of a module
     */
    public function mobile_modulVerschieben()
    {
        // TODO: Felder verlinken
        $module_id = $this->input->post();
        $semester = $this->input->post();

        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->shiftModuleMobile($module_id, $semester);
    }
    
    
    
    
    /**
     * Desktop: Change position of module 
     */
    public function modulVerschieben()
    {
        // frage übergebene Daten ab (veränderte Reihenfolge der Module)
        // serialisiert
        $neue_reihenfolge = $this->input->get('module');
        $semesternr = $this->input->get('semester');
        
        // speichere die neue Reihenfolge in die Datenbank
        $this->Studienplan_Model->shiftModuleDesktop($neue_reihenfolge, $semesternr);
    }
    
    
    
    
    /**
     * Calculate the average mark 
     */
    public function durchschnittsnoteBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $average = $this->Studienplan_Model->calculateAverageMark();
        
        $this->data->add('averageMark', $average);
    }
    
    
    
    
    /**
     * Mobile: Calculate the average mark 
     */
    /*public function mobile_durchschnittsnoteBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $average = $this->Studienplan_Model->$this->Studienplan_Model->calculateAverageMark();
        
        $this->data->add('averageMark', $average);
    }*/
    
    
    
    
    /**
     * Calculate the percentage of current study-status 
     */
    public function prozentsatzBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $percent = $this->Studienplan_Model->calculatePercentageOfStudy();
        
        $this->data->add('percentage', $percent);
    }
    
    
    
    
    /**
     * Mobile: Calculate the percentage of current study-status 
     */
    /*public function mobile_prozentsatzBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $percent = $this->Studienplan_Model->calculatePercentageOfStudy();
        
        $this->data->add('percentage', $percent);
        $this->load->view('studienplan', $this->data->load());
    }*/
    
    
    
    
    /**
     * Accept a mark
     */
    public function noteAkzeptieren()
    {
        $module_id = $this->input->post();

        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->acceptMarks($module_id);
    }
    
    
    
    
    /**
     * Reset the whole studyplan 
     */
    public function studienplanZuruecksetzen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->resetSemestercourses();
        
        $this->message->set(sprintf('Der Studienplan wurde erfolgreich zurükgesetzt.'));
    }
    
    
    
    
    /**
     * Calculate the SWS and the Creditpoints 
     */
    public function swsUndCpBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $swsCp = $this->Studienplan_Model->calculateSwsAndCp();
        
        $this->data->add('swsCp', $swsCp);
    }
    
    
    
    
    /**
     * Raise the try of a module
     */
    public function versuchEinesModulsErhoehen()
    {
        $module_id = $this->input->post();
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->increaseTry($module_id);
    }

    
    
    
    /**
     * Get information about all modules 
     */
    public function modulinfo()
    {
        $this->load->model('Studienplan_Model');
        $info = $this->Studienplan_Model->moduleInfo();
        
        $this->data->add('moduleinfo', $info);
    }
    
    
    
    
    /**
     * Mobile: Get information about all modules 
     */
    /*public function mobile_modulinfo()
    {
        $this->load->model('Studienplan_Model');
        $info = $this->Studienplan_Model->moduleInfo();
        
        $this->data->add('moduleinfo', $info);
        $this->load->view('studienplan', $this->data->load());
    }*/
    
    
    
    
    /**
     * Show all participated groups
     */
    public function gruppenAnzeigen()
    {
        $this->load->model('Studienplan_Model');
        $groups = $this->Studienplan_Model->groups();
        
        $this->data->add('groups', $groups);
    }
    
    
    
    
    /**
     * Mobile: Show all participated groups
     */
    /*public function mobile_gruppenAnzeigen()
    {
        $this->load->model('Studienplan_Model');
        $groups = $this->Studienplan_Model->groups();
        
        $this->data->add('groups', $groups);
        $this->load->view('studienplan', $this->data->load());
    }*/
    
    
    
    
    /**
     * Get an array with information about the status of Prüfen & Teilnehmen of 
     * each module
     */
    public function pruefenTeilnehmenHolen()
    {
        $this->load->model('Studienplan_Model');
        $pruefenTeilnehmen = $this->Studienplan_Model->getPruefenTeilnehmen();
        
        $this->data->add('pruefenTeilnehmen', $pruefenTeilnehmen);
    }
    
    
    
    
    /**
     * Save the status of a module 
     */
    public function pruefenTeilnehmenSpeichern()
    {
        $module_id = $this->input->post();
        $pruefen = $this->input->post();
        $teilnehmen = $this->input->post();
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->savePruefenTeilnehmen($module_id, $pruefen, $teilnehmen);
    }
    
    
    
    
    /**
     * Save a mark in the DB
     */
    public function noteSpeichern()
    {
        $moduleID = $this->input->post();
        $mark = $this->input->post();
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->saveMark($moduleID, $mark);
    }
    
    
    
    
    /**
     * Save a changed semester in the DB
     */
    public function semesterSpeichern()
    {
        $module_id = $this->input->post();
        $semester = $this->input->post();
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->shiftModuleMobile($module_id, $semester);
    }
    
    
    
    
    /**
     * Get the Context text
     * 
     * @deprecated because this context will be static
     */
    /*public function kontextFuerSelectboxHolen()
    {
        $this->load->model('Studienplan_Model');
        $context = $this->Studienplan_Model->getContextForSemesterSelectBox;
        
        $this->data->add('context', $context);
        $this->load->view('studienplan', $this->data->load());
    }*/
}

/* End of file studienplan.php */
/* Location: ./application/controllers/studienplan.php */