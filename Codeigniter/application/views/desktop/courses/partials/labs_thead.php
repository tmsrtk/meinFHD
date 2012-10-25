<thead>
	<tr class="row">
		<th>Name</th>
		<th>Überprüfung</th>
		<?php 
			$number_of_events = 0;
			// MUST BE BUILT DYNAMIC:
			// >> number of events $event_dates[$sp_course_id][22]
			// >> additional events $event_dates[$sp_course_id][20] and [21]
			// >> simple index of other events 1-20
			for($i = 0; $i < $event_dates[$sp_course_id][22]; $i++){
				// init dates on today
				$picker_date = date('d-m-Y');
				
				// if there's already a date stored >> generate dates for picker and header
				if($event_dates[$sp_course_id][$i]){
					$picker_date = date('d-m-Y', strtotime($event_dates[$sp_course_id][$i]));
					$show_date = date('d.m.', strtotime($event_dates[$sp_course_id][$i]));
				} 
				
				echo '<th><div class="thead-labmgt event-date-'.$sp_course_id.'" id=event-'.$sp_course_id.'-'.$i.' data-date="'.$picker_date.'" data-date-format="dd-mm-yyyy" data-eventid="'.$i.'" data-enabled="0">';
				
				// print header for each tab depending on stored data in db
				if($event_dates[$sp_course_id][$i]){
					echo $show_date;
				} else {
					echo 'T'.($i+1);
					$number_of_events = $i+1;
				}
				echo '</div></th>';
			}
			// if there is a string stored in db >> print thead for that collumn
			// addtional data containing:
			// - number of events - only added at first collumn
			// - current-text
			// - event-id
			// - status (default: disabled)
			if(strlen($event_dates[$sp_course_id][20]) > 0){
				echo '<th><div class="event-additional-'.$sp_course_id.'" id="event-additional-1-'.$sp_course_id.'" data-numberofevents="'.$number_of_events.'" data-enabled="0" data-eventid="x1" data-text="'.$event_dates[$sp_course_id][20].'">'.$event_dates[$sp_course_id][20].'</div>';
			}
			if(strlen($event_dates[$sp_course_id][21]) > 0){
				echo '<th><div class="event-additional-'.$sp_course_id.'" id="event-additional-2-'.$sp_course_id.'" data-enabled="0" data-eventid="x2" data-text="'.$event_dates[$sp_course_id][21].'">'.$event_dates[$sp_course_id][21].'</div></th>';
			}
		?>
		<th>
			<div class="event-final-<?php echo $sp_course_id?>" data-eventid="final" data-enabled="0">Finales Testat</div>
		</th>
		<th>Notizen</th>
		<th>
			<div class="participant-disable-<?php echo $sp_course_id?>" data-eventid="disable" data-enabled="0">Teilnehmer deaktivieren</div>
		</th>
	</tr>
</thead>