<?php
    $submit_button_attrs = 'id = #stdgng-course-details-save-button class = "btn-warning"';
    
    $dropdown_attrs = 'class = "span1"';
    
    if($lab == '1'){
	$lab_participants_attrs = array(
	    'name' => 'teilnehmer', 
	    'id' => 'kursverwaltung-tn',
	    'value' => $lecture_details->TeilnehmerMax, 
	    'class' => 'span1'
	);
    }
    
    $label_attrs = array(
	'name' => 'group_label',
	'id' => 'kursverwaltung-tn',
	'class' => 'label label-info',
	'for' => 'kursverwaltung-raum'
    );
    
?>

<div class="clearfix">
    <div class="span1">
	<?php echo 'cb'; ?>
    </div>
    
    <?php // echo form_open(); ?>
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
    <div class="span1">
	<?php echo $lecture_details->Raum; ?>
    </div>
    <div class="span1">
	<?php   
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
	    // add another field for number of possible participants - for labs view
	    if($lab == '1'){
		// max participants - only relevant for labs
		echo $lecture_details->TeilnehmerMax;
	    } else {
		echo 'kein Limit';
	    }
//	    echo form_close();
	?>
    </div>
    <!-- placeholder for submitbutton-->
    <div class="span2">
    </div>
</div>