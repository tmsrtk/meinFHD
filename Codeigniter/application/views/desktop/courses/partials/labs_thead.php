<thead>
	<tr class="row">
		<th>Vorname</th>
		<th>Nachname</th>
		<?php 
			for($i = 0; $i < $number_of_events; $i++){
//				echo '<th class="thead-labmgt">';
				echo '<th>';
				
				// print header for each tab depending on stored data in db
				if($event_dates[$sp_course_id][$i]){
					echo date('d.m.', strtotime($event_dates[$sp_course_id][$i]));
				} else {
					echo 'T'.($i+1);
				}
				echo '</th>';
			}
		?>
		<th>Finales Testat</th>
		<th>Notizen</th>
		<th>Ans Ende stellen</th>
	</tr>
</thead>