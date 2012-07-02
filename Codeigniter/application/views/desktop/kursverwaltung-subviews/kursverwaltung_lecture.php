<?php
    // attributes
    $course_name_attrs = array(
	'name' => 'room', // TODO $KursID.'kurs_kurz',
	'id' => 'kursverwaltung-raum',
	'value' => 'Raum', // TODO $kurs_kurz,
	'class' => 'span2'
    );
    
    $submit_button_attrs = 'id = #stdgng-course-details-save-button class = "btn-warning"';
    
    $dropdown_attrs = 'class = "span2"';
    
    if($lab == '1'){
	$lab_participants_attrs = array(
	    'name' => 'teilnehmer', // TODO $KursID.'kurs_kurz',
	    'id' => 'kursverwaltung-tn',
	    'value' => $lecture_details->TeilnehmerMax, // TODO $kurs_kurz,
	    'class' => 'span1'
	);
    }
    
    $label_attrs = array(
	'name' => 'group_label',
	'id' => 'kursverwaltung-tn',
	'class' => 'label label-info span2',
	'for' => 'kursverwaltung-raum'
    );
    
//    echo '<pre>';
//    print_r($lecture_details);
//    echo '</pre>';
    
?>
<div class="clearfix">
    <?php echo form_open(); ?>
    <div>
	<?php
	    if($lab == '1'){
		// group-label for better overview
	//	echo '<span class="label label-info">1-4</span>';
		echo form_label('Gruppe 1-4', '', $label_attrs);
	    } else {
		echo form_label('Kursname', '', $label_attrs);
	    }
	?>
    </div>

    <!--building table >> content-->
    <div class="span2"><?php echo form_input($course_name_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown('starttime', $starttime_options, $lecture_details->StartID, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown('endtime', $endtime_options, $lecture_details->EndeID, $dropdown_attrs); ?></div>
    <div class="span2"><?php echo form_dropdown('day', $day_options, $lecture_details->TagID, $dropdown_attrs); ?></div>
    <div class="span2">
	<?php
	    // add another field for number of possible particitpants - for labs view
	    if($lab == '1'){
		// max participants - only relevant for labs
		echo form_input($lab_participants_attrs);
	    }
	?>
    </div>
    <div class="span2"><?php echo form_submit('save_course_details', 'Speichern', $submit_button_attrs); ?></div>
    <?php echo form_close(); ?>
</div>