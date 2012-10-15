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
		
		<div class="row-fluid">
			<h2>Meine Praktikumsgruppen</h2>
			<p><a class="label label-info" href="<?php echo base_url(); ?>/kursverwaltung/show_labmgt">Zurück zur Übersicht</a></p>
		</div>
		
		<div>
			<ul class="nav nav-tabs" id="lab-details-navi">
				<?php 
					// printing tab for each group - key= SPKursID !
					foreach($sp_course_details as $details){
						if($details){
							$index = 1;
							foreach($details as $d){
								echo '<li id="lab-tab-'.$d->SPKursID.'">';
								echo '<a href="#tab-panel-'.$d->SPKursID.'" data-toggle="tab">'.$d->kurs_kurz.' - Gruppe '.$index.'</a>';
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
						echo '<div class="tab-pane" id="tab-panel-'.$sp_course_id.'"> ';

						// button for adding dates - must be generated with unique id!
						// therefore here within code
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
								// prepare cb-data
								$cb_data_presence = array(
									'name' => 'presence-uid-'.$i.'-'.$one_participant->BenutzerID,
									'id' => 'presence-uid-'.$i.'-'.$one_participant->BenutzerID,
									'data-uid' => $one_participant->BenutzerID,
									'data-eid' => $i,
									'class' => 'lab-cb',
									'value' => 'accept',
									'checked' => 'TRUE'
								);
								$cb_data_testat = array(
									'name' => 'testat-uid-'.$i.'-'.$one_participant->BenutzerID,
									'id' => 'testat-uid-'.$i.'-'.$one_participant->BenutzerID,
									'data-uid' => $one_participant->BenutzerID,
									'data-eid' => $i,
									'class' => 'lab-cb',
									'value' => 'accept',
									'checked' => 'TRUE'
								);
								
								// print checkboxes
								if(substr($one_participant->anwesenheit, $i, 1) == '1'){
									// check
									echo form_checkbox($cb_data_presence);
								} else {
									// uncheck
									$cb_data_presence['checked'] = FALSE;
									echo form_checkbox($cb_data_presence);
								}
								echo '<br>';

								if(substr($one_participant->testat, $i, 1) == '1'){
									// check
									echo form_checkbox($cb_data_testat);
								} else {
									// uncheck
									$cb_data_testat['checked'] = FALSE;
									echo form_checkbox($cb_data_testat);
								}
								echo '</td><td>';
							}

							// prepare final-testat checkbox
							$cb_data_final = array(
								'name' => 'final-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'final-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 0,
								'class' => 'lab-cb',
								'value' => 'accept',
								'checked' => ($one_participant->gesamttestat ? TRUE:FALSE)
							);
							// final testat
							echo form_checkbox($cb_data_final);
							echo '</td><td>';

							// print  notes
							echo form_textarea($notes_attr);
							echo '</td><td>';

							// prepare disable checkbox
							$cb_data_disable = array(
								'name' => 'disable-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'disable-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 0,
								'class' => 'lab-cb',
								'value' => 'accept',
								'checked' => ($one_participant->ende ? TRUE:FALSE)
							);
							// print disable participant
							echo form_checkbox($cb_data_disable);
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

<!--<script>-->

	// getting tab-status and id of active-tab from controller
	var activeTabId = <?php echo $active_group; ?>;

	// initialize active tab
	if(activeTabId == 0){
		$('.tab-content div:first-child').addClass("active");
		$('#course-details-navi li:first-child').addClass("active");
	} else {
		$('#tab-panel-'+activeTabId).addClass("active");
		$('#lab-tab-'+activeTabId).addClass("active");
	}
	
	
	// saving EVERY change in checkbox-checked-status
	$('.lab-cb').change(function (){
		// getting cb-data
		var elementName = $(this).attr('name');
		var cbStatus = $(this).attr('checked');
		var userId = $(this).data('uid');
		var eventId = $(this).data('eid');
		var dataToSave = [elementName, cbStatus, userId, eventId];
		
		// save checkbox-status for user and event
		$.ajax({
			type: "POST",
			url: "<?php echo site_url();?>kursverwaltung/ajax_save_lab_checkboxes/",
			dataType: 'html',
			data : {lab_cb_data : dataToSave},
			success: function (data){
				// TODO show modal ??
			}
		});
	});
	
	
<?php endblock(); ?>

<?php end_extend(); ?>