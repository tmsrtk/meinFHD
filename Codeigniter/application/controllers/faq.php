<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Fabian Martinovic (FM), <fabian.martinovic@fh-duesseldorf.de>
 */

/**
 * Hilfe-Controller
 */
class FAQ extends FHD_Controller {

    /**
     * Index
     */
    public function index() {
        $this->load->view('faq/index', $this->data->load());
    }

}

/* End of file App.php */
/* Location: ./application/controllers/faq.php */