<?php
    // attributes
    $course_room_attrs = array(
	'name' => 'room', // TODO $KursID.'kurs_kurz',
	'id' => 'kursverwaltung-raum',
	'class' => 'span1',
	'value' => $lecture_details->Raum, // TODO $kurs_kurz,
    );
    
    $submit_button_attrs = 'id = #stdgng-course-details-save-button class = "btn-warning"';
    
    $dropdown_attrs = 'class = "span1"';
    $dropdown_attrs2 = 'class = "span2"';
    
    if($lab == '1'){
	$lab_participants_attrs = array(
	    'name' => 'teilnehmer', // TODO $KursID.'kurs_kurz',
	    'id' => 'kursverwaltung-tn',
	    'class' => 'span1',
	    'value' => $lecture_details->TeilnehmerMax, // TODO $kurs_kurz,
	);
    }
    
    $label_attrs = array(
	'name' => 'group_label',
	'id' => 'kursverwaltung-tn',
	'class' => 'label label-info',
	'for' => 'kursverwaltung-raum'
    );
    
//    echo '<pre>';
//    print_r($lecture_details);
//    echo '</pre>';
    
?>
<div class="clearfix">
    <?php echo form_open(); ?>
    <div class="span2">
	<?php
	    if($lab == '1'){
		// group-label for better overview
	//	echo '<span class="label label-info">1-4</span>';
		echo form_label('Gruppe '.$lecture_details->VeranstaltungsformAlternative, '', $label_attrs);
	    } else {
		echo form_label($lecture_name->kurs_kurz, '', $label_attrs);
	    }
	?>
    </div>

    <!--building table >> content-->
    <div class="span1"><?php echo form_input($course_room_attrs); ?></div>
    <div class="span1"><?php echo form_dropdown('starttime', $starttime_options, $lecture_details->StartID, $dropdown_attrs); ?></div>
    <div class="span1"><?php echo form_dropdown('endtime', $endtime_options, $lecture_details->EndeID, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown('day', $day_options, $lecture_details->TagID, $dropdown_attrs2); ?></div>
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
    <div class="span2"><?php echo form_submit('save_course_details', 'Speichern', $submit_button_attrs); ?></div>
    <?php echo form_close(); ?>
</div>