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
        // $session_userid = $this->authentication->user_id();

        // $loginname = $this->admin_model->get_loginname($session_userid);
        // $user_permissions = $this->admin_model->get_all_userpermissions($session_userid);
        // $roles = $this->admin_model->get_all_roles();
        
        // $userdata = array(
        //         'userid' => $session_userid,
        //         'loginname' => $loginname['LoginName'],
        //         'userpermissions' => $user_permissions,
        //         'roles' => $roles
        //     );

        // $this->data->add('userdata', $userdata);
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
        //$this->pruefenTeilnehmenHolen();
        
        
        // add the resultset/array to the data-object
        $this->data->add('studienplan', $plan);
        $this->load->view('studienplan/index', $this->data->load());
        
        // uncomment for desktop-view
        //$this->load->view('semesterplan_show', $this->data->load()); // leave commented
//        $data['main_content'] = 'semesterplan_show';
//        $data['global_data'] = $this->data->load();
//        $this->load->view('includes/template', $data);
    }


    

     /**
     * Show Studienplan, Testmethod!!!
     *
     * @author Konstantin Voth
     */
    public function studienplan_show()
    {
        $this->load->model('Studienplan_Model');

        $siteinfo = array(
            'title'         => 'Semesterplan',
            'main_content'  => 'semesterplan_show'
            );
        $this->data->add('siteinfo', $siteinfo);

        $this->data->add('studienplan', $this->Studienplan_Model->queryStudyplan());

        $this->load->view('includes/template', $this->data->load());
    }
    
    
    
    
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

        header('Location: /meinFHD/Codeigniter/studienplan/');
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
    private function durchschnittsnoteBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $average = $this->Studienplan_Model->calculateAverageMark();
        
        $this->data->add('averageMark', $average);
    }
    
    
    
    
    /**
     * Calculate the percentage of current study-status 
     */
    private function prozentsatzBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $percent = $this->Studienplan_Model->calculatePercentageOfStudy();
        
        $this->data->add('percentage', $percent);
    }
    
    
    
    
    /**
     * Accept a mark
     */
    public function noteAkzeptieren()
    {
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
    private function swsUndCpBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $swsCp = $this->Studienplan_Model->calculateSwsAndCp();
        
        $this->data->add('swsCp', $swsCp);
    }
    
    
    
    
    /**
     * Raise the try of a module
     */
    private function versuchEinesModulsErhoehen($module_id)
    {
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
     * Show all participated groups
     */
    public function gruppenAnzeigen()
    {
        $this->load->model('Studienplan_Model');
        $groups = $this->Studienplan_Model->groups();
        
        $this->data->add('groups', $groups);
    }
    
    
    
    
    /**
     * Get an array with information about the status of Prüfen & Teilnehmen of 
     * each module
     */
    /*public function pruefenTeilnehmenHolen()
    {
        $this->load->model('Studienplan_Model');
        $pruefenTeilnehmen = $this->Studienplan_Model->getPruefenTeilnehmen();
        
        $this->data->add('pruefenTeilnehmen', $pruefenTeilnehmen);
    }*/
    
    
    
    
    /**
     * Save the status of a module 
     */
    public function pruefenTeilnehmenSpeichern()
    {
        //locale variables
        $module_id = 0;
        $teilnehmen = 0;
        $pruefen = 0;
        
        // get post-data
        $post = $this->input->post();

        // process post data
        foreach($post as $key => $value)
        {
            // explode the array by underscore
            $tempArray = explode('_', $key);
            
            // get data for teilnehmen
            if(preg_match('/hoeren_/', $key))
            {
                $module_id = $tempArray[1];
                $teilnehmen = $value;
            }
            // get data for pruefen
            elseif(preg_match('/schreiben_/', $key))
            {
                $module_id = $tempArray[1];
                $pruefen = $value;
            }
        }
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->savePruefenTeilnehmen($module_id, $pruefen, $teilnehmen);
    }
    
    
    
    
    /**
     * Save a mark in the DB
     */
    public function noteSpeichern()
    {
        // locale variables
        $module_id = 0;
        $mark = 0;
        
        // get post data
        $post= $this->input->post();

        // process post data
        foreach($post as $key => $value)
        {
            if(preg_match('/note_/', $key))
            {
                $tempArray = explode('_', $key);
                $module_id = $tempArray[1];
                $mark = $value;
            }
        }
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->saveMark($module_id, $mark);
    }
    
    
    
    
    /**
     * Save a changed semester in the DB
     */
    public function semesterSpeichern()
    {
        $module_id = 0;
        $semester = 0;
        
        $post = $this->input->post();

        foreach($post as $key => $value)
        {
            if(preg_match('/semester_/', $key))
            {
                $tempArray = explode('_', $key);
                $module_id = $tempArray[1];
                $semester = $value;
            }
        }
        
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->saveSemester($module_id, $semester);
    }
    
    
    

    /**
     * Executes all saving methods 
     */
    public function speichern()
    {
        if($this->input->post() != null)
        {
            $this->noteSpeichern();
            $this->semesterSpeichern();
            $this->pruefenTeilnehmenSpeichern();

            header('Location: /meinFHD/Codeigniter/studienplan/');
        }
    }
}

/* End of file studienplan.php */
/* Location: ./application/controllers/studienplan.php */