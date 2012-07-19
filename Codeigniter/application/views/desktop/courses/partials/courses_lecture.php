<?php
    // attributes
    $course_room_attrs = array(
	'name' => $lecture_details->SPKursID.'_Raum', 
	'id' => 'kursverwaltung-raum',
	'class' => 'span1',
	'value' => $lecture_details->Raum, 
    );
    
    $submit_button_attrs = 'id=lecture-details-save-button class ="btn-warning"';
    
    $dropdown_attrs = 'class = "span1"';
    $dropdown_attrs2 = 'class = "span2"';
    
    if($lab == '1'){
	$lab_participants_attrs = array(
	    'name' => $lecture_details->SPKursID.'_TeilnehmerMax', 
	    'id' => 'kursverwaltung-tn',
	    'class' => 'span1',
	    'value' => $lecture_details->TeilnehmerMax, 
	);
    }
    
    $label_attrs = array(
	'name' => 'group_label',
	'id' => 'kursverwaltung-tn',
	'class' => 'label label-info',
	'for' => 'kursverwaltung-raum'
    );
    
    // checkbox data
    $cb_data = array(
	'name' => $lecture_details->SPKursID,
	'id' => 'email-checkbox email-checkbox-course',
	'value' => '',
	'checked' => 'checked',
    );
?>

<div class="clearfix">
    <div class="span1">
	<?php 
	    echo form_open(''); 
	    echo form_checkbox($cb_data); 
	    echo form_close(''); 
	?>
    </div>
    <?php
	$form_attributes = array('id' => 'course-details-save-button');
	echo form_open('kursverwaltung/save_course_details', $form_attributes); 
    ?>
    <div class="span2">
	<?php
	    if($lab == '1'){
		// group-label for better overview
		echo form_label('Gruppe '.$lecture_details->VeranstaltungsformAlternative, '', $label_attrs);
	    } else {
		echo form_label($lecture_name->kurs_kurz, '', $label_attrs);
	    }
	?>
    </div>
    <!-- building table >> content-->
    <div class="span1"><?php echo form_input($course_room_attrs); ?></div>
    <div class="span1"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_StartID', $starttime_options, $lecture_details->StartID, $dropdown_attrs); ?></div>
    <div class="span1"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_EndeID', $endtime_options, $lecture_details->EndeID, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown(
	    $lecture_details->SPKursID.'_TagID', $day_options, $lecture_details->TagID, $dropdown_attrs2); ?></div>
    <div class="span1">
	<?php
	    // add another field for number of possible particitpants - for labs view
	    if($lab == '1'){
		// max participants - only relevant for labs
		echo form_input($lab_participants_attrs);
	    } else {
		echo 'kein Limit';
	    }
	?>
    </div>
    <div class="span2"><?php echo form_submit('', 'Speichern', $submit_button_attrs); ?></div>
    <?php echo form_close(); ?>
</div>