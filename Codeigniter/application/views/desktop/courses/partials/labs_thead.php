<thead>
	<tr class="row">
		<th>Name</th>
		<th>Überprüfung</th>
		<?php 
			// TODO must be dynamic - number of events
			for($i = 0; $i < $number_of_events; $i++){
				echo '<th><div class="thead-labmgt label">';
				
				// print header for each tab depending on stored data in db
				if($event_dates[$sp_course_id][$i]){
					echo date('d.m.', strtotime($event_dates[$sp_course_id][$i]));
				} else {
					echo 'T'.($i+1);
				}
				echo '</div></th>';
			}
		?>
		<th>Finales Testat</th>
		<th>Notizen</th>
		<th>Ans Ende stellen</th>
	</tr>
</thead>