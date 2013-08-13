<?php
	/**
	 * Partial that provides a single row in course-table - FOR TUTORS
	 * 
	 * The tutor-view is much more static than the view for non-tutors.
	 * Therefore this part has an own partial where the rows are build.
	 * 
	 * As in standard course_lecture.php-partial all fields, that should provide
	 * the possibility to save data has to be bound to a specific SPKursID (name, id - whatever is used)
	 * 
	 * As in the other courses_lecture-partial the rows are part of one big form
	 * which is opened and closed in the main view (courses_show.php).
	 * To provide the possibility to save only one row, deactivate comments.
	 * 
	 * Furthermore same structure as in mentioned file (courses_lecture.php)
	 * For more detailed comments have a look at that file.
	 * 
	 */
/*
    $label_attrs = array(
		'name' => 'group_label',
		'id' => 'course-mgt-label-'.$lecture_details->SPKursID,
		'class' => 'label label-info',
		'for' => 'kursverwaltung-raum'
    );*/

    $label_attrs = array(
        'id' => 'course-mgt-label-'.$lecture_details->SPKursID,
        'class' => 'label label-info',
    );

    $course_room_attrs = array(
        'name'  => $lecture_details->SPKursID.'_Raum',
        'id'    => 'kursverwaltung-raum-' . $lecture_details->SPKursID,
        'class' => 'span',
        'value' => $lecture_details->Raum,
        'readonly' => 'readonly'
    );
    
    // checkbox data
    $cb_data = array(
		'name' => $lecture_details->SPKursID,
		'class' => 'email-checkbox-courses-'.$course_id.' email-checkbox-'.$course_id,
		'id' => 'email-checkbox-course-id-'.$lecture_details->SPKursID,
		'value' => '',
		'checked' => 'checked',
    );

    $dropdown_attrs = 'class="span" disabled="disabled"';
    
?>
<div class="clearfix">
    <div class="span1">
	<?php echo form_checkbox($cb_data); ?>
    </div>
    
    <div class="span2">
	<?php
	    if($is_lab){
		// group-label for better overview
			echo form_label('Gruppe '.$lecture_details->VeranstaltungsformAlternative, '', $label_attrs);
	    }
        else {
			echo form_label($lecture_name->kurs_kurz, '', $label_attrs);
	    }
	?>
    </div>
    <div class="span1">
        <?php echo form_input($course_room_attrs); ?>
    </div>
    <div class="span2">
		<?php   
			// starttime
            echo form_dropdown($lecture_details->SPKursID.'_StartID', $starttime_options, $lecture_details->StartID-1, $dropdown_attrs);
		?>
    </div>
    <div class="span2">
		<?php
			// endtime
            echo form_dropdown($lecture_details->SPKursID.'_EndeID', $endtime_options, $lecture_details->EndeID-1, $dropdown_attrs);
		?>
    </div>
    <div class="span2">
		<?php
			// day
            echo form_dropdown($lecture_details->SPKursID.'_TagID', $day_options, $lecture_details->TagID-1, $dropdown_attrs);
		?>
    </div>
    <div class="span1">
		<?php
			// add another field for number of possible participants - for labs view
			if($is_lab){
				// max participants - only relevant for labs
				echo $lecture_details->TeilnehmerMax;
			}
            else {
				echo '-';
			}
		?>
    </div>
    <!-- placeholder for submitbutton - not in tut-view -->
    <div class="span1">-</div>
</div>