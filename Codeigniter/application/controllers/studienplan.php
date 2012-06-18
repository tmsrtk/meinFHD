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
    /**
     * Index-Method, which loads the Studienplan
     */
    public function index()
    {
        // load model
        $this->load->model('Studienplan_Model');
        $plan = $this->Studienplan_Model->queryStudyplan();
        
        // add the resultset/array to the data-object
        $this->data->add('studienplan', $plan);
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Create a studyplan
     */
    public function studienplanErstellen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->createStudyplan();
    }
    
    
    
    /**
     * Add a new coloumn to semesterplan
     */
    public function spalteEinfuegen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->$this->Studienplan_Model->createNewSemesterColoumn();
    }
    
    
    
    /**
     * Update new position of a module
     */
    public function modulVerschieben()
    {
        // ID's werden hier benötigt
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->$this->Studienplan_Model->shiftModule($module_id, $semester);
    }
    
    
    
    /**
     * Calculate the average mark 
     */
    public function durchschnittsnoteBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $average = $this->Studienplan_Model->$this->Studienplan_Model->calculateAverageMark();
        
        $this->data->add('averageMark', $average);
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Calculate the percentage of current study-status 
     */
    public function prozentsatzBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $percent = $this->Studienplan_Model->$this->Studienplan_Model->calculatePercentageOfStudy();
        
        $this->data->add('percentage', $percent);
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Accept a mark
     */
    public function noteAkzeptieren()
    {
        // Hier wird noch die Modul-ID benötigt
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->acceptMarks($module_id);
    }
    
    
    
    /**
     * Reset the whole studyplan 
     */
    public function studienplanZuruecksetzen()
    {
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->reset();
    }
    
    
    
    /**
     *Calculate the SWS and the Creditpoints 
     */
    public function swsUndCpBerechnen()
    {
        $this->load->model('Studienplan_Model');
        $swsCp = $this->Studienplan_Model->calculateSwsAndCp();
        
        $this->data->add('swsCp', $swsCp);
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Raise the try of a module
     */
    public function versuchEinesModulsErhoehen()
    {
        // Hier wird noch die Modul-ID benötigt
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
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Show all participated groups
     */
    public function gruppenAnzeigen()
    {
        $this->load->model('Studienplan_Model');
        $groups = $this->Studienplan_Model->groups();
        
        $this->data->add('groups', $groups);
        $this->load->view('studienplan', $this->data->load());
    }
    
    
    
    /**
     * Calculate a mark 
     */
    public function noteBerechnen()
    {
        // Note muss  übegeben werden
        $this->load->model('Studienplan_Model');
        $this->Studienplan_Model->calculateMark($mark);
    }
}

/* End of file studienplan.php */
/* Location: ./application/controllers/studienplan.php */