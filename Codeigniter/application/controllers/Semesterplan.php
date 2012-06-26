<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @19.June.2012
 * @Coder Rico Xu, <rednoya@live.com>
 *
 */


/**
 * The Controlle for 'Semesterplan'
 */
class Semesterplan extends FHD_Controller
{
    /**
     * the default function of 'CI'
     */
    public function index()
    {
        // load the GUI from view of 'CI'
        $this->load->view('semesterplan');
    }
}

/* End of file Semesterplan.php */
/* Location: ./application/controllers/Semesterplan.php */