<?php
    $submit_button_attrs = 'id = #stdgng-course-details-save-button class = "btn-warning"';
    
    $dropdown_attrs = 'class = "span1"';
    
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
	'class' => 'label label-info',
	'for' => 'kursverwaltung-raum'
    );
    
//    echo '<pre>';
//    print_r($lecture_details);
//    echo '</pre>';
    
?>

<?php 
    // if($this->authentication->role == $is_prof || $this->authentication->role == $is_betreuer)
    echo form_open();
    ?>
<div class="clearfix">
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
    <div class="span1">
	<?php echo $lecture_details->Raum; ?>
    </div>
    <div class="span1">
	<?php   
	//    echo form_input($course_name_attrs);
	    // starttime
	    echo $starttime_options[$lecture_details->StartID];
	?>
    </div>
    <div class="span1">
	<?php
	    // endtime
	    echo $endtime_options[$lecture_details->EndeID];
	?>
    </div>
    <div class="span2">
	<?php
	    // day
	    echo $day_options[$lecture_details->TagID];
	?>
    </div>
    <div class="span1">
	<?php

	    // add another field for number of possible particitpants - for labs view
	    if($lab == '1'){
		// max participants - only relevant for labs
		echo $lecture_details->TeilnehmerMax;
	    } else {
		echo 'kein Limit';
	    }

	    // submit-button
	//    echo form_submit('save_course_details', 'Speichern', $submit_button_attrs);

	    echo form_close();

	    // else print labels/text only
	?>
    </div>
    <div class="span2">
    </div>
</div>