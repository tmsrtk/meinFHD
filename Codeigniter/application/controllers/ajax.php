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
		$this->load->model('Modul_Model');
	}

	public function index()
	{
		// $data['title'] = 'Ajax';
		// $data['main_content'] = 'ajax_uebersicht';

		// // aktuelle Reihenfolge der Module laden
		// // $anzahl = $this->ajax_model->get_semesteranzahl();
		// // $anzahl = $this->ajax_model->get_semesteranzahl(4);
		// $anzahl = array(
		// 	'semesteranzahl' => '6'
		// );
		// $data['semesteranzahl'] = $anzahl;
		
		// // speichere für j-s Semester dynamisch in die Datenvariable die Module aus dem Semester
		// $laufvar = $anzahl['semesteranzahl'];
		// for ($i=1; $i<=$laufvar; $i++) {
		// 	$data['module_reihenfolge'.$i] = $this->ajax_model->get_reihenfolge($i);
		// }

		// $this->load->view('includes/template', $data);
	}

	/**
	 * Save new values for a Semester.
	 */
	public function schreibe_reihenfolge_in_db()
	{
		// form data
		$modules = $this->input->get('module');
		$hoeren = $this->input->get('hoeren');
		$pruefen = $this->input->get('pruefen');
		$mark = $this->input->get('mark');
		$semesternr = $this->input->get('semester');


		// 3. setze alle veranstaltungen außer VL - "aktiv = 0"
		// $this->load->model('Module_Model');

		// gib alle veranstaltungen außer VL aus dem übergebenen module
		// melde für die übriggebliebenen veranstaltungen üall aus

		foreach ($modules as $key => $kurs_id)
		{
			log_message('error', "Key: {$key} | Value: {$kurs_id}");

			$withdrawable_events = $this->ajax_model->get_withdrawable_events($kurs_id);

			foreach ($withdrawable_events as $key => $value)
			{
				// log_message('error', 
				// 		"{$value['SemesterID']} | {$semesternr}");

				if ($value['SemesterID'] !== $semesternr)
				{
					log_message('error', 
						"war im {$value['SemesterID']}, jetzt im {$semesternr}");

					$this->Modul_Model->withdraw_from_course(
											$this->user_model->get_userid(),
											$kurs_id,
											$value['SPKursID'],
											$value['GruppeID']
											);
				}
			}
		}


		// 1. update semesterkurs
		$this->ajax_model->set_reihenfolge($modules, $semesternr, $hoeren, $pruefen, $mark);
		// 2. update benutzerkurs
		$this->ajax_model->set_reihenfolge_benutzerkurs($modules, $semesternr);
		

		


		// 4. melde den user aus allen gruppen ab




	}


	public function get_module_title()
	{
		$mid = $this->input->get('moduleid');
		$res = $this->ajax_model->query_module_title($mid);
		echo $res['Kursname'];
	}

	public function get_module_text()
	{
		$mid = $this->input->get('moduleid');
		$res = $this->ajax_model->query_module_text($mid);

		// edit array for html output
		$output = '';
		$output.= $this->_generate_row_for_modaltext('Kursname', $res['Kursname']);
		$output.= $this->_generate_row_for_modaltext('Kurzname', $res['kurs_kurz']);
		$output.= $this->_generate_row_for_modaltext('Creditpoints', $res['Creditpoints']);

		// $output.= $this->_generate_row_for_modaltext('SWS Vorlesung', $res['SWS_Vorlesung']);
		// $output.= $this->_generate_row_for_modaltext('SWS Übung', $res['SWS_Uebung']);
		// $output.= $this->_generate_row_for_modaltext('SWS Praktikum', $res['SWS_Praktikum']);
		// $output.= $this->_generate_row_for_modaltext('SWS Praktikum', $res['SWS_Praktikum']);

		echo $output;
	}

	private function _generate_row_for_modaltext($name = '', $text = 'kein Eintrag')
	{
		return '<p><strong>'.$name.': '.'</strong>'.$text.'</p>';
	}

	public function save_changes()
	{
		$data = $this->input->post();
		FB::log($data);
	}


	public function check_status_pruefen()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->query_status_pruefung($kurs_id);
	}
	public function activate_status_pruefung()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->write_activate_status_pruefung($kurs_id);
	}
	public function deactivate_status_pruefung()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->write_deactivate_status_pruefung($kurs_id);
	}



	public function check_status_hoeren()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->query_status_hoeren($kurs_id);
	}
	public function activate_status_hoeren()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->write_activate_status_hoeren($kurs_id);
	}
	public function deactivate_status_hoeren()
	{
		$kurs_id = $this->input->get('kursid');
		$this->ajax_model->write_deactivate_status_hoeren($kurs_id);
	}





	public function check_status_hoeren_vl()
	{
		$kurs_id = $this->input->get('kursid');
		$res = $this->ajax_model->query_status_hoeren_vl($kurs_id);

		echo $res['Semester'];

	}

}