<thead>
	<tr class="row">
		<th>Name</th>
		<th>Überprüfung</th>
		<?php 
			// TODO must be dynamic - number of events
			for($i = 0; $i < $event_dates[$sp_course_id][22]; $i++){
				// init dates on today
				$picker_date = date('d-m-yy');
				
				// if there's already a date stored >> generate dates for picker and header
				if($event_dates[$sp_course_id][$i]){
					$picker_date = date('d-m-yy', strtotime($event_dates[$sp_course_id][$i]));
					$show_date = date('d.m.', strtotime($event_dates[$sp_course_id][$i]));
				} 
				
				echo '<th><div class="thead-labmgt event-date" data-date="'.$picker_date.'" data-date-format="dd-mm-yyyy">';
				
				// print header for each tab depending on stored data in db
				if($event_dates[$sp_course_id][$i]){
					echo $show_date;
				} else {
					echo 'T'.($i+1);
				}
				echo '</div></th>';
			}
			if($event_dates[$sp_course_id][20] != 0){
				echo '<div class="event-additional"><th>20</th></div>';
			}
			if($event_dates[$sp_course_id][21] != 0){
				echo '<div class="event-additional"><th>21</th></div>';
			}
		?>
		<th>Finales Testat</th>
		<th>Notizen</th>
		<th>Bearbeitung deaktivieren</th>
	</tr>
</thead>