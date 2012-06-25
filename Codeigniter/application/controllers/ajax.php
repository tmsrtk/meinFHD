<?php

/*
 * meinFHD 2.0
 * http://www..de/
 *
 * Konstantin Voth
 * Copyright 2012
 *
 */

/* 
 * Ajax Controller
 * Kümmert sich um die asynchronen requests/responses 
 * vom Client zu Server und zurück
 */

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ajax_model');
	}

	// public function index()
	// {
	// 	$data['title'] = 'Ajax';
	// 	$data['main_content'] = 'ajax_uebersicht';

	// 	// aktuelle Reihenfolge der Module laden
	// 	// $anzahl = $this->ajax_model->get_semesteranzahl();
	// 	$anzahl = $this->ajax_model->get_semesteranzahl(83);
	// 	$data['semesteranzahl'] = $anzahl;
		
	// 	// speichere für j-s Semester dynamisch in die Datenvariable die Module
	// 	$counter = 1;
	// 	foreach ($anzahl as $key => $value) {
	// 		$data['module_reihenfolge'.$counter] = $this->ajax_model->get_reihenfolge($counter);
	// 		$counter++;
	// 	}

	// 	$this->load->view('includes/template', $data);
	// }

	public function index()
	{
		$data['title'] = 'Ajax';
		$data['main_content'] = 'ajax_uebersicht';

		// aktuelle Reihenfolge der Module laden
		// $anzahl = $this->ajax_model->get_semesteranzahl();
		// $anzahl = $this->ajax_model->get_semesteranzahl(4);
		$anzahl = array(
			'semesteranzahl' => '6'
		);
		$data['semesteranzahl'] = $anzahl;
		
		// speichere für j-s Semester dynamisch in die Datenvariable die Module aus dem Semester
		$laufvar = $anzahl['semesteranzahl'];
		for ($i=1; $i<=$laufvar; $i++) {
			$data['module_reihenfolge'.$i] = $this->ajax_model->get_reihenfolge($i);
		}

		$this->load->view('includes/template', $data);
	}

	public function schreibe_reihenfolge_in_db()
	{
		// frage übergebene Daten ab (veränderte Reihenfolge der Module)
		// serialisiert
		$neue_reihenfolge = $this->input->get('module');
		$semesternr = $this->input->get('semester');

		

		// speichere die neue Reihenfolge in die Datenbank
		$this->ajax_model->set_reihenfolge($neue_reihenfolge, $semesternr);
	}

}