<thead>
	<tr class="row">
		<th>Name</th>
		<th>Überprüfung</th>
		<?php 
			// MUST BE BUILT DYNAMIC:
			// >> number of events $event_dates[$sp_course_id][22]
			// >> additional events $event_dates[$sp_course_id][20] and [21]
			// >> simple index of other events 1-20
			for($i = 0; $i < $event_dates[$sp_course_id][22]; $i++){
				// init dates on today
				$picker_date = date('d-m-yy');
				
				// if there's already a date stored >> generate dates for picker and header
				if($event_dates[$sp_course_id][$i]){
					$picker_date = date('d-m-yy', strtotime($event_dates[$sp_course_id][$i]));
					$show_date = date('d.m.', strtotime($event_dates[$sp_course_id][$i]));
				} 
				
				echo '<th><div class="thead-labmgt event-date-'.$sp_course_id.'" data-date="'.$picker_date.'" data-date-format="dd-mm-yyyy data-eventid='.$i.' data-spcourseid='.$sp_course_id.'">';
				
				// print header for each tab depending on stored data in db
				if($event_dates[$sp_course_id][$i]){
					echo $show_date;
				} else {
					echo 'T'.($i+1);
				}
				echo '</div></th>';
			}
			if($event_dates[$sp_course_id][20] != 0){
				echo '<th><div class="event-additional-'.$sp_course_id.'">20</div>';
			}
			if($event_dates[$sp_course_id][21] != 0){
				echo '<th><div class="event-additional-'.$sp_course_id.'">21</div></th>';
			}
		?>
		<th>Finales Testat</th>
		<th>Notizen</th>
		<th>Teilnehmer deaktivieren</th>
	</tr>
</thead>