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
//	$number_of_events = ;

	// storing group-ids to array - needed in jq-part to run through all tabs
	$sp_course_ids = array();
	foreach($event_dates as $spc_id => $array_with_group_headers){
		$sp_course_ids_for_jq['spk_id'.$spc_id] = $spc_id;
	}
	
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
//						foreach($theads as $head){
//							print_r($head);
//						}
						
				?>
			</pre>-->
			
				
		<div class="tab-content">
			<?
				// run through all sp-course-details
				// $key ($course_type_id) build of course_id-eventtype e.g. [302-4]
				// $value arrays containing all participants
				foreach($sp_course_participants_details as $course_type_id => $group_details){
					
					// index for counting groups
					// only used to append a number to the tab-text
					$index_groups = 1;
					
					// run through all groups
					// $key contains sp_course_id e.g. [2464]
					// $value simple array containing all participants of that group
					foreach($group_details as $sp_course_id => $participants){
						
						// print tab-pane - one for each group
						echo '<div class="tab-pane" id="tab-panel-'.$sp_course_id.'"> ';
						
						// print coure-details - use key from outer loop
						// day, starttime, endtime, group
						// therefore run through sp_course_details for that group
						// if $value[SPKursID] == $sp_course_id: print details
						foreach($sp_course_details[$course_type_id] as $value){
							if($value->SPKursID == $sp_course_id){
								echo '<div class="well well-small">';
								echo '<h4>'.$value->Kursname.'</h4>';
								echo $value->TagName.', '.$value->Beginn.' - '.$value->Ende.' Uhr, ' .$value->Raum;
								echo '<br /><br />';
								echo 'Spaltenüberschriften anklicken, um Anwesenheit/Testat abzuhaken.';
								echo '</div>';
							}
						}
						
						// button for adding dates - must be generated with unique id!
						// therefore here within code
						echo '<div class="change-group-dates" id="change-group-dates-'.$sp_course_id.'">';
							$header_button_data = array(
								'name' => 'change-dates-button',
								'id' => 'change-dates-button-'.$sp_course_id,
								'class' => 'btn btn-info change-dates-button',
								'data-courseid' => $sp_course_id,
								'data-editing' => 'inactive',
								'value' => 'true',
								'content' => 'Termine bearbeiten'
							);
							echo form_button($header_button_data);
						echo '</div>';

						echo '<table class="table lab-tab">';
						echo $theads[$sp_course_id];
						echo '<tbody>';
						foreach($participants as $index => $one_participant){
							// ## preparing some data
							// notes area - to be generated for each element therefore inside loop
							$notes_attr = array(
								'name' => 'user-notes-'.$one_participant->BenutzerID,
								'id' => 'user-notes-'.$one_participant->BenutzerID,
								'class' => 'user-notes',
								'rows' => 1,
								'value' => $one_participant->notizen
							);

							// build row from data
							echo '<tr class="row"><td>';

							// print labels - name
							echo form_label($one_participant->Vorname, 'first_name'.$one_participant->BenutzerID);
							echo form_label($one_participant->Nachname, 'last_name'.$one_participant->BenutzerID);
							echo '</td><td>';
							echo form_label('Anwesenheit', '5');
							echo form_label('Testat', '5');
							echo '</td><td>';

							// print two lines of checkboxes (1. presence, 2. testat)
							for($i = 0; $i < $number_of_events[$sp_course_id]; $i++){
								// prepare cb-data
								$cb_data_presence = array(
									'name' => 'presence-uid-'.$i.'-'.$one_participant->BenutzerID,
									'id' => 'presence-uid-'.$i.'-'.$one_participant->BenutzerID,
									'data-uid' => $one_participant->BenutzerID,
									'data-eid' => $i,
									'class' => 'lab-cb lab-cb-'.$sp_course_id.'-'.$i,
									'value' => 'accept',
									'checked' => 'TRUE'
								);
								$cb_data_testat = array(
									'name' => 'testat-uid-'.$i.'-'.$one_participant->BenutzerID,
									'id' => 'testat-uid-'.$i.'-'.$one_participant->BenutzerID,
									'data-uid' => $one_participant->BenutzerID,
									'data-eid' => $i,
									'class' => 'lab-cb lab-cb-'.$sp_course_id.'-'.$i,
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
							
							// depending on data print checkboxes for pre-final-testat1
							$cb_data_pre_testat1 = array(
								'name' => 'pretestat1-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'pretestat1-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 'x1',
								'class' => 'lab-cb lab-cb-'.$sp_course_id.'-x1',
								'value' => 'accept',
								'checked' => ($one_participant->zwischentestat1 ? TRUE:FALSE)
							);
							if(strlen($zwtestat1[$sp_course_id]) > 0){
								echo form_checkbox($cb_data_pre_testat1);
								echo '</td><td>';
							}
							
							// depending on data print checkboxes for pre-final-testat2
							$cb_data_pre_testat2 = array(
								'name' => 'pretestat2-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'pretestat2-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 'x2',
								'class' => 'lab-cb lab-cb-'.$sp_course_id.'-x2',
								'value' => 'accept',
								'checked' => ($one_participant->zwischentestat1 ? TRUE:FALSE)
							);
							if(strlen($zwtestat2[$sp_course_id]) > 0){
								echo form_checkbox($cb_data_pre_testat2);
								echo '</td><td>';
							}

							// prepare final-testat checkbox
							$cb_data_final = array(
								'name' => 'final-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'final-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 'final',
								'class' => 'lab-cb  lab-cb-'.$sp_course_id.'-final',
								'value' => 'accept',
								'checked' => ($one_participant->gesamttestat ? TRUE:FALSE)
							);
							// final testat
							echo form_checkbox($cb_data_final);
							echo '</td><td>';

							// print  notes
							echo form_textarea($notes_attr);
							echo '<div data-id="'.$one_participant->BenutzerID.'" class="saving-notes" id="saving-notes-'.$one_participant->BenutzerID.'"></div>';
							echo '</td><td>';

							// prepare disable checkbox
							$cb_data_disable = array(
								'name' => 'disable-uid-'.$i.'-'.$one_participant->BenutzerID,
								'id' => 'disable-uid-'.$i.'-'.$one_participant->BenutzerID,
								'data-uid' => $one_participant->BenutzerID,
								'data-eid' => 'disable',
								'class' => 'lab-cb lab-cb-'.$sp_course_id.'-disable',
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
					
						echo '<div id="edit-xtra-event-modal-'.$sp_course_id.'"></div>';
					}
				}
			?>
		</div> <!-- end of tabcontainer -->
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>

<!--<script>-->

	// getting tab-status and id of active-tab from controller
	var activeTabId = <?php echo (($active_group != -1) ? $active_group : 0); ?>;
	
	// initialize active tab
	if(activeTabId == 0){
		$('.tab-content div:first-child').addClass("active");
		$('#lab-details-navi li:first-child').addClass("active");
	} else {
		$('#tab-panel-'+activeTabId).addClass("active");
		$('#lab-tab-'+activeTabId).addClass("active");
	}
	
	// create variable that contains all sp_course_ids in that view
	// to run through the tabs
    var spCourseIdsInView = <?php echo json_encode($sp_course_ids_for_jq); ?>;
	
	// get all input-elements
	//var inputs = $('input')
	
	
	// disable checkboxes - default-status
	$('.lab-cb').attr('disabled', true);
	
	// behaviour of checkboxes, when table-head is clicked
	$.each(spCourseIdsInView, function(index, spCourseId){
		// standard-events 1-20
		$('.event-date-'+spCourseId).click(function(){
			enableCbForEvent($(this));
		});
		
		// extra-events
		$('.event-additional-'+spCourseId).click(function(index, val){
			enableCbForEvent($(this));
		});
		
		// final testat
		$('.event-final-'+spCourseId).click(function(index, val){
			enableCbForEvent($(this));
		});
		
		// disable participants
		$('.participant-disable-'+spCourseId).click(function(index, val){
			enableCbForEvent($(this));
		});
	});
	
	
	/**
	 * Helper to enable 
	 */
	function enableCbForEvent(self){
		// if checkboxes are enabled
		// >> get id of current element and run over all inputfields and check for eid
		// >> enable and set class
		if(self.data('enabled') == 0){
			var id = self.data('eventid');
			$.each($('input'), function(index, val){
				if($(val).data('eid') == id){
					console.log($(val).data('eid'));
					$(val).removeAttr('disabled');
				}
			});
			self.data('enabled', 1);
			self.addClass('text-success');
		// otherwise same but disable and remove class
		} else {
			var id = self.data('eventid');
			$.each($('input'), function(index, val){
				if($(val).data('eid') == id){
					$(val).attr('disabled', true);
				}
			});
			self.data('enabled', 0);
			self.removeClass('text-success');
		}
	}
	
	
	// saving EVERY change in checkbox-checked-status
	$('.lab-cb').change(function (){
		var cbId = $(this).attr('id');
		// getting cb-data
		var elementName = $(this).attr('name');
		var cbStatus = $('#'+cbId).attr('checked') ? 1 : 0;
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
		
		return true;
	});
	
	// getting ids for each save-notes-div
	var saveNotes = $('.saving-notes');
	$.each(saveNotes, function(indexAll, buttonSpace){
		var buttonDiv = '#'+$(this).attr('id');
		var participantId = $(this).data('id');
		var pNotes = new Array();
		
		// showing save/cancel-buttons when textarea is focussed
		$('#user-notes-'+participantId).focus(function(){
			var areaId = $(this).attr('id');
			$(buttonDiv).html(getSaveNotesButtons(participantId));
			//console.log(buttonDiv);
		}).blur(function(){
			// get current input
			var currentText = $('#user-notes-'+participantId).val();
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url();?>kursverwaltung/ajax_get_former_participant_notes/",
				dataType: 'html',
				data : {participant_id : participantId},
				success: function (data){
					console.log(currentText);
					// when textarea blurred, check if content changed
					if(data != currentText){
						// if changed do nothing
						//console.log('diff: '+data);
					} else {
						// else hide button if field is left and nothing changed
//						console.log('hide '+data);
						$(buttonDiv).html('');
					}
				}
			});
			$.ajax({
				type: "POST",
				url: "<?php echo site_url();?>kursverwaltung/ajax_get_former_participant_notes/",
				dataType: 'html',
				data : {participant_id : participantId},
				success: function (data){
					console.log(currentText);
					// when textarea blurred, check if content changed
					if(data != currentText){
						// if changed do nothing
						//console.log('diff: '+data);
					} else {
						// else hide button if field is left and nothing changed
//						console.log('hide '+data);
						$(buttonDiv).html('');
					}
				}
			});
			
		});
		
//		console.log(currentText);
		
		// handle lab-note-save-button
		$('td').on('click', '#save-notes-button-'+participantId, function(){
			var id = $(this).attr('id');
			var newNotes = $('#user-notes-'+participantId).val();
			var pNotes = new Array(
				participantId,
				newNotes
			);
			
			console.log(id);
			// disable button
			$('#'+id).attr('disabled', true);
			$.ajax({
				type: "POST",
				url: "<?php echo site_url();?>kursverwaltung/ajax_save_lab_notes/",
				dataType: 'html',
				data : {participant_notes : pNotes},
				success: function (data){
					// hide button
					$(buttonDiv).html('');
				}
			});
		});
		
		// handle lab-note-cancel-button
		$('td').on('click', '#cancel-notes-button-'+participantId, function(){
			var id = $(this).attr('id');
			$.ajax({
				type: "POST",
				url: "<?php echo site_url();?>kursverwaltung/ajax_get_former_participant_notes/",
				dataType: 'html',
				data : {participant_id : participantId},
				success: function (data){
					// reload stored value
					$('#user-notes-'+participantId).val(data)
					console.log('#user-notes-'+participantId);
					
					// hide button
					$(buttonDiv).html('');
				}
			});
		});
		
	});
	
	/**
	 * Helper method to build two buttons (save/cancel) for notes-input-field
	 */
	function getSaveNotesButtons(pId){
		var noteButtons = 
			$('<div class="span save-notes-container"></div>')
			.html('<input type="submit" class="btn btn-success save-notes-button" id="save-notes-button-'+pId+'" data-pid="'+pId+'" value="Speichern">')
			.append('<input type="submit" class="pull-right btn btn-warning cancel-save-notes-button" id="cancel-notes-button-'+pId+'" data-pid="'+pId+'" value="Abbrechen">');

		return noteButtons;
	};
	
	

	<!--<script>-->
	
	// handle all group-details-buttons in view
	// each tab (i.e. sp_course_id) has it's own button
	$.each(spCourseIdsInView, function(indexAll, spCourseId){
		// get ids
		var changeDetailsButtonId = $('#change-dates-button-'+spCourseId);
		
		$(changeDetailsButtonId).click(function(){
			
			// getting edit-status of button and act depending on it
			var editStatus = $(this).data('editing');

			
			if(editStatus == 'inactive'){
				// if inactive then:
				// - activate date-picker, 
				// - change button color, text, status and
				// - add edit-button and activate it
				
				// date-picker
				$('.event-date-'+spCourseId).datepicker().
					on('changeDate', function(ev){
						// getting element-id and some other data
						var textId = $(this).attr('id');
						var eventId = $(this).data('eventid');
						
						//console.log(ev.date);
						var d = ev.date.getDate();
						var m = ev.date.getMonth()+1;
						var y = ev.date.getFullYear();
						$('#'+textId).html(d+'.'+m+'.'+ getDateIcon());
						
						var saveData = new Array(
							spCourseId,
							eventId,
							d,
							m,
							y
						);
						
						// saving new date to db
						$.ajax({
							type: "POST",
							url: "<?php echo site_url();?>kursverwaltung/ajax_save_new_date_for_event/",
							dataType: 'html',
							data : {save_event_data : saveData},
							success: function (data){
								// nothing to do - date has been saved
							}
						});
						
					});
				$('.event-date-'+spCourseId).append(getDateIcon());
				
				// button
				var xtraText1 = $('#event-additional-1-'+spCourseId).data('text');
				var xtraText2 = $('#event-additional-2-'+spCourseId).data('text');
				var numberEvents = $('#event-additional-1-'+spCourseId).data('numberofevents');
				
				console.log(xtraText1);
				console.log(xtraText2);
				console.log(numberEvents);
				
				$('#change-group-dates-'+spCourseId).html(getSubmitReloadButton(spCourseId, xtraText1, xtraText2, numberEvents));
				
//				// edit-button
//				//$('.event-additional-'+spCourseId).append(getWrenchIcon());
//				$('.event-additional-'+spCourseId).click(function(){
//					var eventId = $(this).data('eventid');
//					var currentText = $(this).data('text');
//					var dialog = getXtraEventModal(eventId, spCourseId, currentText, 'Beschriftung wählen', 'Nutzen Sie Zusatztermine um zum Beispiel Zwischenabgaben zu verwalten.');
//					$('#edit-xtra-event-modal-'+spCourseId).html(dialog);
//					
//					// function of dialog
//					$('#change-xtra-text').modal({
//						keyboard: false,
//						backdrop: 'static'
//					}).on('show', function(){
//						//$('#add-tutor-dialog-confirm').data('id', courseId);
//					// on hide hide
//					}).on('hide', function(){
//						// 
//					}).modal('show');
//
//					return false;
//				});
//				
//				// behaviour when search started
//				$('#edit-xtra-event-modal-'+spCourseId).on('click', '#change-event-text-save', function(){
//					// creating array to pass spCourseId and new text
//					var self = $(this);
//					var newData = new Array(
//						spCourseId,
//						$('#event-text-'+spCourseId).attr('value'),
//						$('#event-text-'+spCourseId).data('eventid')
//					);
//
//					$('.modal-body').html('Text wird geändert.');
//					$('.modal-header button').hide();
//					$('.modal-footer').hide();
//					$.ajax({
//						   type: "POST",
//						   url: "<?php echo site_url();?>kursverwaltung/ajax_save_xtra_event/",
//						   dataType: 'html',
//						   data : {new_data : newData},
//						   success: function (data){
//							   location.reload();
//							   // nothing to do
//						   }
//						});
//
//				});
				
				
			} else {
				// else nothing to do
				// reload triggered 
			}
			
		});
		
		
	});
	
	
//	function getXtraEventModal(xtraEventId, spCourseId, currentText, title, text){
//		var myDialog = 
//			$('<div class="modal hide" id="change-xtra-text"></div>')
//			.html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
//			.append('<div class="modal-body" id="modal-body"><p>'+text+'</p>\n\
//			<p>Title eingeben: <input type="text" id="event-text-//'+spCourseId+'" data-eventid="'+xtraEventId+'" name="description" value="'+currentText+'" >\n\
//			<input type="submit" class="btn-info" id="change-event-text-save" value="Speichern"></div>//')
//			.append('<div class="modal-footer"><a href="#" class="btn" id="change-event-text-cancel" data-dismiss="modal">Abbrechen</a>\n\
//			</div>//');
//
//		return myDialog;
//    };
	
	
	/**
	 * Helper function to generate calendar-icon for changing dates
	 */
	function getDateIcon(){
		return '<div class="change-date-icon label label-info" stlye="text-align:center"><i class="icon-calendar icon-white"></i></div>';
	};
	
	
	/**
	 * Helper function to generate wrench-icon for changing 'free' events
	 */
	function getWrenchIcon(){
		return '<div class="change-free-icon label label-info" stlye="text-align:center"><i class="icon-wrench icon-white"></i></div>';
	};
	
	<!--<script>-->
	/**
	 * Helper function to return form with submit-button
	 * and checkboxes for each extra-event
	 * Used to trigger submit-action (+passing id) and redirect to show_labmgt_group
	 */
	function getSubmitReloadButton(spCourseId, text1, text2, numberEvents){
		var button = 
			$('<div></div>')
			.append('<form accept-charset="utf-8" method="post" action="<?php echo base_url(); ?>kursverwaltung/save_and_reload_lab_mgt_group/">\n\
			<div><input type="hidden" value="'+spCourseId+'" name="sp_course_id">\n\
			<input type="submit" id="submit-reload-'+spCourseId+'" class="btn btn-warning" value="Bearbeiten beenden" name="'+spCourseId+'-reload"></div>\n\
			<div class="well well-small clearfix" id="xtra-event-panel-'+spCourseId+'">\n\
			<div class="span4"><label>Beschriftung Zusatztermin (+) 1</label>\n\
			<input type="text" class="" value="'+text1+'" name="xtra_event_1"></div>\n\
			<div class="span4"><label>Beschriftung Zusatztermin (+) 2</label>\n\
			<input type="text" class="" value="'+text2+'" name="xtra_event_2"></div>\n\
			<div class="span4"><label>Anzahl Termine (max. 20)</label>\n\
			<input type="text" class="" value="'+numberEvents+'" name="number_of_events"></div>\n\
			</div></form>');
		return button;
	};
	
	/*
			<div class="span4"><label class="checkbox"><input type="checkbox" name="xtra-event-1-cb" id="xtra-event-1-cb-'+spCourseId+'"/>Beschriftung Zusatztermin 1</label>\n\
			<input type="text" class="" value="" placeholder="Beschriftung" name="xtra-event-1-'+spCourseId+'"></div>\n\
			<div class="span4"><label class="checkbox"><input type="checkbox" name="xtra-event-2-cb" id="xtra-event-2-cb-'+spCourseId+'"/>Beschriftung Zusatztermin 2</label>\n\
			<input type="text" class="" value="" placeholder="Beschriftung" name="xtra-event-2-'+spCourseId+'"></div>\n\
			<div class="span4"><label class="checkbox"><input type="checkbox" name="number-event-2-cb" id="xtra-event-2-cb-'+spCourseId+'"/>Anzahl Termine</label>\n\
			<input type="text" class="" value="" placeholder="Beschriftung" name="xtra-event-2-'+spCourseId+'"></div>\n\
	*/
	
	
<?php endblock(); ?>

<?php end_extend(); ?>