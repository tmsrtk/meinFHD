<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Stundenplan
 *
 * The Stundenplan-Controller provides all features and function, that are afforded
 * for the timetable and the different views (day and week view).
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2013
 * @link http://www.fh-duesseldorf.de
 * @author Simon vom Eyser (SVE), <simon.vomeyser@fh-duesseldorf.de>
 * @author Christian Kundruss (CK), <christian.kundruss@fh-duesseldorf.de>
 */
class Stundenplan extends FHD_Controller {

    /**
     * Default constructor. Used for initialisation purposes.
     * Loads all necessary models.
     *
     * @access public
     * @return void
     */
	public function __construct(){
		parent::__construct();
		$this->load->model('stundenplan_model');
	}
	
    /**
     * The index function is the standard function of the timetable controller and
     * will be called if no function is passed.
     * The index method provides / loads the timetable day view.
     * It will be executed when the following url-schema is used:
     * ../stundenplan
     * ../stundenplan/index
     * 
     * @access public
     * @return void
     */
	public function index(){
		// get the necessary data and load the view
        $stundenplan = $this->stundenplan_model->get_stundenplan($this->authentication->user_id());
		
		$this->data->add('stundenplan', $stundenplan[0]); 
		$this->data->add('tage', $stundenplan[1]);
		$this->data->add('zeiten', $stundenplan[2]);
		$this->data->add('aktivekurse', $stundenplan[3]);
		
		$this->load->view('stundenplan/day', $this->data->load());
    }
	
	/**
	 * Controller for week view
	 *
     * ../stundenplan/woche
     *
     * normaler week view ohne rollenunterscheidung
     * @access public
     * @return void
	 */
	public function woche(){
		// Load all necessary data for "Stundenplan" :)
		//$plan = $this->stundenplan_model->get_stundenplan();
        $plan = $this->stundenplan_model->get_stundenplan_for_all_roles();

		// Save the days in a separate variable
		$days = $plan[0];

		// Load helper classes
		include(APPPATH . 'libraries/events/Event.php');
		include(APPPATH . 'libraries/events/EventSort.php');

		foreach ($days as $dayname => $day){
			// Events get stored here
			$events = array();
			// Create an event object for every event
			foreach ($day as $row){
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
     * Function loads the timetable week view version, that was especially made for the desktop
     * version of meinfhd (prefix desktop). The function will be automatically called, if the method woche()
     * is called.
     * It loads the week view of timetable depending on the roles the user has got. For every different role
     * the user has got, an separate tab with the role specific timetable will be displayed.
     *
     * @access public
     * @return void
	 */
    public function desktop_woche(){
		$stundenplaene = array(); // array that holds the different timetables

		$userid = $this->user_model->get_userid();

		// 1. query all different roles the currently authenticated user has got
		$roles = $this->user_model->get_all_roles();

        // 2. load for each role the timetable data
		if ( in_array(Roles::DOZENT, $roles) ){
			$stundenplaene[Roles::DOZENT] = $this->stundenplan_model->get_stundenplan_dozent();

			$this->data->add('tage', $stundenplaene[Roles::DOZENT][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::DOZENT][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::DOZENT][3]);
		}

		if ( in_array(Roles::TUTOR, $roles) ){
			$stundenplaene[Roles::TUTOR] = $this->stundenplan_model->get_stundenplan_tutor();

			$this->data->add('tage', $stundenplaene[Roles::TUTOR][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::TUTOR][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::TUTOR][3]);
		}
        
		if ( in_array(Roles::STUDENT, $roles) ){
			$stundenplaene[Roles::STUDENT] = $this->stundenplan_model->get_stundenplan_student();

			$this->data->add('tage', $stundenplaene[Roles::STUDENT][1]);
			$this->data->add('zeiten', $stundenplaene[Roles::STUDENT][2]);
			$this->data->add('aktivekurse', $stundenplaene[Roles::STUDENT][3]);
		}

        if (in_array(Roles::BETREUER, $roles)){

            $stundenplaene[Roles::BETREUER] = $this->stundenplan_model->get_stundenplan_advisor();

            $this->data->add('tage', $stundenplaene[Roles::BETREUER][1]);
            $this->data->add('zeiten', $stundenplaene[Roles::BETREUER][2]);
            $this->data->add('aktivekurse', $stundenplaene[Roles::BETREUER][3]);
        }

		// load helper classes
		include(APPPATH . 'libraries/events/Event.php');
		include(APPPATH . 'libraries/events/EventSort.php');

        // events and sorting for each timetable
		foreach ($stundenplaene as $role => $plan){
			// Save the days in a separate variable
			$days = $plan[0];

			foreach ($days as $dayname => $day){
				// array to store the timetable events
				$events = array();
				// Create an event object for every event
				foreach ($day as $row){
					foreach ($row as $event){
						// only show and calculate events that should be displayed / flag "anzeigen"
						if ((bool) $event['Anzeigen']){
							// to calculate the correct display data, we need the start, duration and color.
							$start = (int) $event['StartID'];
							$duration = (int) $event['Dauer'];
							$color = $event['Farbe'];

							// create an object for the current event.
							$events[] = new Event($start, $duration, $color, $event);
						}
					}
				}

				// create a sortable list of events
				$sort = new EventSort($events);
				// Optimize the display data for the events
				$days[$dayname] = $sort->optimize();
				$wochenplaene[$role][$dayname] = $days[$dayname];
			}
		}

		// add data to global array and load the view
		$this->data->add('stundenplaene', $wochenplaene);
		$this->load->view('stundenplan/week', $this->data->load());
	}
}
/* End of file stundenplan.php */
/* Location: ./application/controllers/stundenplan.php */