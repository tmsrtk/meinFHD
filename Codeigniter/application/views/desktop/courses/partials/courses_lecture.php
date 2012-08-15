<?php
    // attributes
    $course_room_attrs = array(
		'name' => $lecture_details->SPKursID.'_Raum', 
		'id' => 'kursverwaltung-raum',
		'class' => 'span',
		'value' => $lecture_details->Raum, 
    );
    
    $submit_button_attrs = 'id=lecture-details-save-button class ="btn-warning"';
    
    $dropdown_attrs = 'class = "span"';
    $dropdown_attrs2 = 'class = "span"';
    
    if($is_lab){
		$lab_participants_attrs = array(
			'name' => $lecture_details->SPKursID.'_TeilnehmerMax', 
			'id' => 'kursverwaltung-tn',
			'class' => 'span',
			'value' => $lecture_details->TeilnehmerMax, 
		);
    }
    
    $label_attrs = array(
		'name' => 'group_label',
		'id' => 'course-mgt-label-'.$lecture_details->SPKursID,
		'class' => 'label label-info',
		'for' => 'kursverwaltung-raum'
    );
    
    // checkbox data
    $cb_data = array(
		'name' => $lecture_details->SPKursID,
		'class' => 'email-checkbox-courses-'.$course_id.' email-checkbox-'.$course_id,
		'id' => 'email-checkbox-course-id-'.$lecture_details->SPKursID,
		'value' => '',
		'checked' => 'checked',
    );
?>

<div class="clearfix">
    <div class="span1">
	<!-- email-checkbox-->
		<?php echo form_checkbox($cb_data); ?>
		</div>
		<?php
		// to save each course seperately enable this and disable form in course_show.php
	//	$form_attributes = array('id' => 'course-details-save-button');
	//	echo form_open('kursverwaltung/save_course_details', $form_attributes); 
		?>
    <div class="span2">
		<?php
			if($is_lab){
			// group-label for better overview
				echo form_label('Gruppe '.$lecture_details->VeranstaltungsformAlternative, '', $label_attrs);
			} else {
				echo form_label($lecture_name->kurs_kurz, '', $label_attrs);
			}
		?>
    </div>
    <!-- building table >> content-->
    <div class="span1"><?php echo form_input($course_room_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_StartID', $starttime_options, $lecture_details->StartID-1, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_EndeID', $endtime_options, $lecture_details->EndeID-1, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_TagID', $day_options, $lecture_details->TagID-1, $dropdown_attrs2); ?></div>
    <div class="span1">
		<?php
			// add another field for number of possible particitpants - for labs view
			if($is_lab){
			// max participants - only relevant for labs
				echo form_input($lab_participants_attrs);
				
			} else {
				echo '-';
			}
		?>
    </div>
	<div class="span1">
		<?php 
			// only visible for non-tuts
			if(!$is_tut){
				// download-button labe depends on eventtype
				$download_button_label = '<i class="icon-download"></i> ';
				if($is_lab){
					// labs show number of participants and max. possible participants
					$download_button_label .= $current_participants.'/'.$lecture_details->TeilnehmerMax;
					$anchor_attrs = array(
						'class' => 'btn btn-mini btn-success download-tn-button-'.$course_id,
						'data-id' => $lecture_details->SPKursID
					);
				} else {
					// lectures show current attendees
					$download_button_label .= $current_participants;
					$anchor_attrs = array(
						'class' => 'btn btn-mini btn-success download-tn-button-course-'.$course_id,
						'data-id' => $lecture_details->SPKursID
					);
				}
				echo anchor('kursverwaltung/show_coursemgt#', $download_button_label, $anchor_attrs);
//				echo '<a class="btn btn-mini download-tn-button" id="'.$lecture_details->SPKursID.'-tn-download" href="#"><i class="icon-hdd"></i></a>'; 
			} else {
				echo '-';
			}
		?>
	</div>
<!--    <div class="span2"><?php // echo form_submit('', 'Speichern', $submit_button_attrs); ?></div>-->
    <?php // echo form_close(); ?>
</div>