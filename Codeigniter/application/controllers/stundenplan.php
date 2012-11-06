<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
		$this->data->add('titel', 'Stundenplan');
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
		$stundenplan = $this->Stundenplan_Model->get_stundenplan($this->authentication->user_id());
		
		$this->data->add('stundenplan', $stundenplan[0]); 
		$this->data->add('tage', $stundenplan[1]);
		$this->data->add('zeiten', $stundenplan[2]);
		$this->data->add('aktivekurse', $stundenplan[3]);
		
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
	public function woche()
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
					// Only show and calculate events that should be displayed
					if ((bool) $event['Anzeigen']) {
						// To calculate the correct display data, we need
						// the start, duration and color.
						$start = (int) $event['StartID'];
						$duration = (int) $event['Dauer'];
						$color = $event['Farbe'];
	
						// Create an object for the current event.
						$events[] = new Event($start, $duration, $color, $event);
					}
					
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

	/**
	 * Loads the week view. Decides, which roles the user has and which Stundenpläne he needs to query.
	 * 
	 * @author Konstantin Voth <konstantin.voth@fh-duesseldorf.de>
	 */
	public function desktop_woche()
	{
		// 2. load for each role the Stundenplan data

		$stundenplaene = array();
		$userid = $this->user_model->get_userid();

		// 1. query users roles

		$roles = $this->user_model->get_all_roles();

		if ( in_array(Roles::DOZENT, $roles) )
		{
			$stundenplaene[Roles::DOZENT] = $this->Stundenplan_Model->get_stundenplan_dozent();

			$this->data->add('tage', $stundenplaene[Roles::DOZENT][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::DOZENT][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::DOZENT][3]);
		}

		if ( in_array(Roles::TUTOR, $roles) )
		{
			$stundenplaene[Roles::TUTOR] = $this->Stundenplan_Model->get_stundenplan_tutor();

			$this->data->add('tage', $stundenplaene[Roles::TUTOR][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::TUTOR][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::TUTOR][3]);
		}

		if ( in_array(Roles::STUDENT, $roles) )
		{
			$stundenplaene[Roles::STUDENT] = $this->Stundenplan_Model->get_stundenplan_student();

			$this->data->add('tage', $stundenplaene[Roles::STUDENT][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::STUDENT][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::STUDENT][3]);
		}

		// FB::log($stundenplaene); return;



		// events and sorting for each stundenplan
		
		// Load helper classes
		include(APPPATH . 'libraries/events/Event.php');
		include(APPPATH . 'libraries/events/EventSort.php');


		foreach ($stundenplaene as $role => $plan)
		{
			// Save the days in a seperate variable
			$days = $plan[0];

			foreach ($days as $dayname => $day)
			{
				// Events get stored here
				$events = array();
				// Create an event object for every event
				foreach ($day as $row)
				{
					foreach ($row as $event)
					{
						// Only show and calculate events that should be displayed
						if ((bool) $event['Anzeigen'])
						{
							// To calculate the correct display data, we need
							// the start, duration and color.
							$start = (int) $event['StartID'];
							$duration = (int) $event['Dauer'];
							$color = $event['Farbe'];
			
							// Create an object for the current event.
							$events[] = new Event($start, $duration, $color, $event);
						}
					}
				}
				// Create a sortable list of events
				$sort = new EventSort($events);
				// Optimize the display data for the events
				$days[$dayname] = $sort->optimize();
				$wochenplaene[$role][$dayname] = $days[$dayname];
			}
		}

		// FB::log($wochenplaene); return;





		// 3. load view with needed data

		$this->data->add('stundenplaene', $wochenplaene); 
		

		// $this->data->add('stundenplan', $days); 
		// $this->data->add('tage', $plan[1]);
		// $this->data->add('zeiten', $plan[2]);
		// $this->data->add('aktivekurse', $plan[3]);

		$this->load->view('stundenplan/week', $this->data->load());








	}

}