<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @18.June.2012
 * @Coder Rico Xu, <rednoya@live.com>
 *
 */


/**
 * The Controlle for 'Hile und FAQ'
 */
class Hilfe extends FHD_Controller
{
    /**
     * the default function of 'CI'
     */
    public function index()
    {
        // load the GUI from view of 'CI'
        $this->load->view('hilfe');
    }
}

/* End of file Hilfe.php */
/* Location: ./application/controllers/Hilfe.php */