<?php

    $add_tutor_button_data = array(
		'name' => 'add-tutor-button',
		'id' => 'add-tutor-button-'.$course_id,
		'data-course-id' => $course_id,
		'class' => 'span12 btn',
		'content' => 'Tutor hinzufügen'
    );

    // init needed variables
    $panel_id_prefix = 'labings-panel-';
    $form_id = 'course-labings-save-button';
    $possible_staff = $possible_labings;
    $current_staff = $current_labings;
    $label_id_prefix = 'labing-label-';
    $save_data = 'kursverwaltung/save_labings_for_course';
    $tut_button = '';

    // switch if it is tut-view
    if($print_tuts){
		$panel_id_prefix = 'tuts-panel-';
		$form_id = 'course-tut-save-button';
		$possible_staff = $possible_tuts;
		$current_staff = $current_tuts;
		$label_id_prefix = 'tut-label-';
		$save_data = 'kursverwaltung/save_tuts_for_course';
		$tut_button = '<div id="tuts-panel-button-'.$course_id.'">'.form_button($add_tutor_button_data).'</div>';
    }
    
    $form_attributes = array('id' => $form_id);
    $submit_attributes = 'id=course-staff-save-button class="btn-warning"';
    
    
?>

<div class="well well-small clearfix staff-panel" id="<?php echo $panel_id_prefix.$course_id; ?>" style="display:none;">
<!--    <hr />-->

    <?php
	// building checkbox panel with all possible staff for a course
	if(!$is_tutor){
	    print form_open($save_data, $form_attributes);
	    echo form_submit($course_id, 'Speichern', $submit_attributes);

	    // counter for creating 3 collumns
	    $counter = 0;
	    $number_staff = count($possible_staff);
	    $third_staff = ceil($number_staff / 3);
	    
	    foreach($possible_staff as $p_staff){

			// building three columns
			if($counter % $third_staff == 0){
				if($counter == 0){
					echo '<div class="span3" style="float:left;">';
				} else {
					echo '</div><div class="span3" style="float:left;">';
				}
			}

			$checked = FALSE; // init

			// only if there are labings in variable
			if(in_array($course_id, array_keys($current_staff))){
				// check if staff is in current staff
				foreach($current_staff[$course_id] as $c_staff){
					if($c_staff['BenutzerID'] == $p_staff->BenutzerID){
						$checked = TRUE;
					}
				}
			}

			echo '<p>';
				// print checkbox
				$cb_name = $p_staff->BenutzerID;
				$cb_id = $course_id.'-'.$p_staff->BenutzerID;

				// checkbox data
				$cb_data = array(
					'name' => $cb_name,
					'id' => $cb_id,
					'value' => $course_id,
					'checked' => $checked,
				);
				echo form_checkbox($cb_data);

				// print label
				$label_id = $label_id_prefix.$course_id.'-'.$p_staff->BenutzerID;
				$label_text = $p_staff->Vorname.' '.$p_staff->Nachname;
				$label_attrs = array(
					'id' => $label_id,
					'style' => 'display:inline'
				);
				echo form_label($label_text, '', $label_attrs);
			echo '</p>';

	//		if($counter == $third_staff*3){
	//		    echo '</div>';
	//		}

			$counter++;
	    } // endforeach
	    echo '</div>';
	    echo form_close();

		// print add-tut-button
		echo $tut_button;
	} // endif
    ?>
</div>