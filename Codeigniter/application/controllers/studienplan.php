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
        //$plan = $this->Studienplan_Model->createStudyplan();
        //$plan = $this->Studienplan_Model->createNewSemesterColoumn();
        //$this->Studienplan_Model->updateSemesterColoumn(342, 7);
        //var_dump($this->Studienplan_Model->queryAllModules());
        //var_dump($this->Studienplan_Model->calculateAverageMark());
        //var_dump($this->Studienplan_Model->calculatePercentageOfStudy());
        //$this->Studienplan_Model->acceptMarks(342);
        //$this->Studienplan_Model->reset();
        //var_dump($this->Studienplan_Model->calculateSwsAndCp());
        
        // add the resultset/array to the data-object
        $this->data->add('studienplan', $plan);
        $this->load->view('studienplan', $this->data->load());
    }
}

/* End of file studienplan.php */
/* Location: ./application/controllers/studienplan.php */