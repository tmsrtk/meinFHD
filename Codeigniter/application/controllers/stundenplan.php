<?php   if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Simon vom Eyser (SVE), <simon.vomeyser@fh-duesseldorf.de>
 */

/**
 * Stundenplan Controller 
 */
class Stundenplan extends FHD_Controller {

	/**
	 * Constructor Method
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->data->add('title', 'Stundenplan');
		$this->load->model('Stundenplan_Model');
	}
	
	
	/**
	 * Controller for day view
	 *
	 * ../stundenplan
	 * ../stundenplan/index
	 * 
	 * @access public
	 * @return void
	 */
	public function index()
	{
		//krumo($this->data->load());
		$stundenplan = $this->Stundenplan_Model->get_stundenplan($this->authentication->user_id());
		
		$this->data->add('stundenplan', $stundenplan[0]); 
		$this->data->add('tage', $stundenplan[1]);
		$this->data->add('zeiten', $stundenplan[2]);
		$this->data->add('aktivekurse', $stundenplan[3]);
		
		//$this->krumo->dump($this->data);
		$this->load->view('stundenplan/index', $this->data->load());
	}
	
	/**
	 * Controller for week view
	 *
	 * ../stundenplan/woche
	 * 
	 * @access public
	 * @return void
	 */
	public function week()
	{
		// Load all necessary data for "Stundenplan" :)
		$plan = $this->Stundenplan_Model->get_stundenplan($this->authentication->user_id());
		
		// Save the days in a seperate variable
		$days = $plan[0];
		
		// Load helper classes
		include(APPPATH . 'libraries/events/Event.php');
		include(APPPATH . 'libraries/events/EventSort.php');
		
		foreach ($days as $dayname => $day)
		{
			// Events get stored here
			$events = array();

			// Create an event object for every event
			foreach ($day as $row)
			{
				foreach ($row as $event) {
					// To calculate the correct display data, we need
					// the start, duration and color.
					$start = (int) $event['StartID'];
					$duration = (int) $event['Dauer'];
					$color = $event['Farbe'];
					
					// Create an object for the current event.
					$events[] = new Event($start, $duration, $color, $event);
				}
			}

			// Create a sortable list of events
			$sort = new EventSort($events);
			// Optimize the display data for the events
			$days[$dayname] = $sort->optimize();
		}
		
		$this->data->add('stundenplan', $days); 
		$this->data->add('tage', $plan[1]);
		$this->data->add('zeiten', $plan[2]);
		$this->data->add('aktivekurse', $plan[3]);

		$this->load->view('stundenplan/week', $this->data->load());
	}

}