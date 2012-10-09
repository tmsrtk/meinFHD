<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Praktikumsverwaltung - Gruppen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
	// general form setup
	// TODO
	// to change number of shown labs >> there must be an additional field in db to store this value
	// at first static in this view
	$number_of_events = 15;
	
?>

<?php startblock('content'); # additional markup before content ?>
		
		<div>
			<ul class="nav nav-tabs" id="lab-details-navi">
				<?php 
					// printing tab for each group - key= SPKursID !
					foreach($sp_course_details as $details){
						if($details){
							$index = 1;
							foreach($details as $d){
								echo '<li id="lab-tab-'.$d->SPKursID.'">';
								echo '<a href="#'.$d->SPKursID.'" data-toggle="tab">'.$d->kurs_kurz.' - Gruppe '.$index.'</a>';
								echo '</li>';
								$index++;
							}
						}
					}

				?>
			</ul>
			
		</div>
		
<!--			<pre>
				<?
//					print_r($event_dates);
//					print_r($active_group);
//					print_r($sp_course_details);
//					print_r($sp_course_participants_details);
//					foreach($theads as $head){
//						print_r($head);
//					}
						
				?>
			</pre>-->
			
				
		<div class="tab-content">
			<?
				foreach($sp_course_participants_details as $group_details){
					// index for counting groups
					$index_groups = 1;
					foreach($group_details as $sp_course_id => $participants){
						echo '<div class="tab-pane" id="'.$sp_course_id.'"> ';

						// button for adding dates - must be generated with unique id!
						$header_button_data = array(
							'name' => 'change-dates-button',
							'id' => 'change-dates-button'.$sp_course_id,
							'class' => 'btn btn-info',
							'value' => 'true',
							'content' => 'Termine der Gruppe '.$index_groups.' anpassen'
						);
						echo form_button($header_button_data);

						echo '<table class="table lab-tab">';
						echo $theads[$sp_course_id];
						echo '<tbody>';
						foreach($participants as $index => $one_participant){
							// ## preparing some data
							// notes area - to be generated for each element therefore inside loop
							$notes_attr = array(
								'name' => 'user-notes'.$one_participant->BenutzerID,
								'id' => 'user-notes'.$one_participant->BenutzerID,
								'class' => 'user-notes',
								'rows' => 1,
								'value' => $one_participant->notizen
							);

							// build row from data
							echo '<tr class="row"><td>';

							// print labels - name
							echo form_label($one_participant->Vorname, 'first_name'.$one_participant->BenutzerID);
							echo '</td><td>';
							echo form_label($one_participant->Nachname, 'last_name'.$one_participant->BenutzerID);
							echo '</td><td>';

							// print two lines of checkboxes (1. presence, 2. testat)
							for($i = 0; $i < $number_of_events; $i++){
								if(substr($one_participant->anwesenheit, $i, 1) == '1'){
									// check
									echo form_checkbox('presence'.$i, 'accept', TRUE);
								} else {
									// uncheck
									echo form_checkbox('presence'.$i, 'accept', FALSE);
								}
								echo '<br>';

								if(substr($one_participant->testat, $i, 1) == '1'){
									// check
									echo form_checkbox('testat'.$i, 'accept', TRUE);
								} else {
									// uncheck
									echo form_checkbox('testat'.$i, 'accept', FALSE);
								}
								echo '</td><td>';
							}

							// final testat
							echo form_checkbox('final_testat', 'accept', ($one_participant->gesamttestat ? TRUE:FALSE));
							echo '</td><td>';

							// print  notes
							echo form_textarea($notes_attr);
							echo '</td><td>';

							// print disable participant
							echo form_checkbox('final_testat', 'accept', ($one_participant->ende ? TRUE:FALSE));
							echo '</td></tr>';
						}


						echo '</tbody>';
						echo '</table>';
						echo '</div>';
						$index_groups++;
					}
				}
			?>
		</div> <!-- end of tabcontainer -->
		
		<div class="hidden update-group-dates-modal"></div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>

	// initialize active tab
    $('.tab-content div:first-child').addClass("active");
    $('#lab-details-navi li:first-child').addClass("active");
	
<?php endblock(); ?>

<?php end_extend(); ?>