<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Praktikumsverwaltung - Überblick<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
	//general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>
		
<!--		<div>
			<pre>
				<?php print_r($sp_course_details); ?>
			</pre>
		</div>-->
		
		<div class="row-fluid">
			<h2>Meine Praktikumsgruppen</h2>
		</div>

		<div>
			<?php
				// helper vars to save the course before and the lab-type
				// used to start new well and print headline
				$course_before = -1;
				$lab_before = -1;
				
				// run through all details and print them + button
				foreach($sp_course_details as $key => $details){
					$group = 1;
					// if there are details for that course-eventtype-combination print them
					if($details){
						foreach($details as $d){
							// print headline if the course changes
							if($course_before === -1){
								echo '<h3>'.$d->Kursname.' - '.$d->VeranstaltungsformName.'</h3>';
								echo '<table class="table table-hover">
									<thead>
										<tr>
											<th>Bezeichnung</th>
											<th>Tag</th>
											<th>Beginn</th>
											<th>Ende</th>
											<th>Bearbeiten</th>
										</tr>
									</thead>
									<tbody>';
							} else if($course_before != substr($key, 0, 3)){
								echo '</tbody></table><h3>'.$d->Kursname.' - '.$d->VeranstaltungsformName.'</h3>';
								echo '<table class="table table-hover">
									<thead>
										<tr>
											<th>Bezeichnung</th>
											<th>Tag</th>
											<th>Beginn</th>
											<th>Ende</th>
											<th>Bearbeiten</th>
										</tr>
									</thead>
									<tbody>';
							} else if($course_before == substr($key, 0, 3) && $lab_before != substr($key, 4, 1)){
								echo '</tbody></table><h3>'.$d->Kursname.' - '.$d->VeranstaltungsformName.'</h3>';
								echo '<table class="table table-hover">
									<thead>
										<tr>
											<th>Bezeichnung</th>
											<th>Tag</th>
											<th>Beginn</th>
											<th>Ende</th>
											<th>Bearbeiten</th>
										</tr>
									</thead>
									<tbody>';
							}
							// print lab-groups + buttons
							
							echo '<tr>';
							// print course-data in table
							echo '<td>Gruppe '.$group.'</td>';
							echo '<td>'.$d->TagName.'</td>';
							echo '<td>'.$d->Beginn.'</td>';
							echo '<td>'.$d->Ende.'</td>';
							
							// print last table-cell with button
							echo '<td>';
								echo form_open('kursverwaltung/show_labmgt');
								echo form_hidden('sp_course_id', $d->SPKursID);
								echo form_hidden('course_id', substr($key, 0, 3));
								echo form_submit(array('class' => 'span btn btn-info', 'name' => 'show_group'), 'Bearbeiten');
								echo form_close();
							echo '</td>';
							echo '</tr>';
							
							$group++;
							
							// save the course before and the lab-type
							$course_before = substr($key, 0, 3);
							$lab_before = substr($key, 4, 1);
						}
					}
				}
				echo '</tbody></table>';
			
			?>
		</div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
<?php endblock(); ?>

<?php end_extend(); ?>