<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * @19.June.2012
 * @Coder Rico Xu, <rednoya@live.com>
 *
 */


/**
 * The Controlle for the detail page of 'Hile'
 */
class Informationseite extends FHD_Controller
{
    /**
     * the default function of 'CI'
     */
    public function index()
    {
        // load the GUI from view of 'CI'
        $this->load->view('Informationseite');
    }
}

/* End of file Informationseite.php */
/* Location: ./application/controllers/Informationseite.php */