<?php

	/**
	 * Provides a panel with all staff which has the role (labing=3 or tutor=4)
	 * For tutor-panel there is a button added which provides the possibility to
	 * 'create' a new tutor. >> Dialog with search-function.
	 * 
	 * IMPORTANT:
	 * Partial is used to build labing and tut-panel.
	 * Therefore the variables has to be assigned dependant on flag (print_tuts)
	 * 
	 * Panel is only built when user is not an tutor!
	 * 
	 */

    // general form setup

	// prepare button-data
    $add_tutor_button_data = array(
		'name' => 'add-tutor-button',
		'id' => 'add-tutor-button-'.$course_id,
		'data-course-id' => $course_id,
		'class' => 'span12 btn',
		'content' => 'Tutor hinzuf&uuml;gen'
    );

    // init needed variables
    $panel_id_prefix = 'labings-panel-';
    $form_id = 'course-labings-save-button-'. $course_id;
    $possible_staff = $possible_labings;
    $current_staff = $current_labings;
    $label_id_prefix = 'labing-label-';
    $save_data = 'kursverwaltung/save_labings_for_course';
    $tut_button = '';

    // override if it is a tut-view
    if($print_tuts){
		$panel_id_prefix = 'tuts-panel-';
		$form_id = 'course-tut-save-button-' . $course_id;
		$possible_staff = $possible_tuts;
		$current_staff = $current_tuts;
		$label_id_prefix = 'tut-label-';
		$save_data = 'kursverwaltung/save_tuts_for_course';
		$tut_button = '<div id="tuts-panel-button-'.$course_id.'">'.form_button($add_tutor_button_data).'</div>';
    }
	
    $form_attributes = array('id' => $form_id);
    $submit_attributes = 'id="course-staff-save-button-' . $course_id . '" class="btn-warning"';
    
?>

<div class="well well-small clearfix staff-panel" id="<?php echo $panel_id_prefix.$course_id; ?>" style="display:none;">

    <?php
	
	// build checkbox panel with all possible staff for a course if the user is not an tutor
	if(!$is_tutor){
        echo '<p>Bitte w&auml;hlen Sie die Person(en) aus, die als Betreuer f&uuml;r diesen Kurs vorgesehen sind ';
        echo 'und klicken Sie anschlie&szlig;end auf Speichern.</p>';
        echo '<hr/>';
		echo form_open($save_data, $form_attributes);
		echo form_submit($course_id, 'Speichern', $submit_attributes);

	    /**
		 * Staff is displayed in 3 columns.
		 * Therefore:
		 * - counter for creating 3 columns
		 * - get all staff for that category
		 * - calculate one third of that >> 3 columns
		 * 
		 * >> Then run over all staff and build panel.
		 */
	    $counter = 0;
	    $number_staff = count($possible_staff);
	    $third_staff = ceil($number_staff / 3);
	    
	    foreach($possible_staff as $p_staff){

			// building three columns
			if($counter % $third_staff == 0){
				if($counter == 0){
					// first div
					echo '<div class="span3" style="float:left;">';
				}
                else {
					// second, third div
					echo '</div><div class="span3" style="float:left;">';
				}
			}

			// provide correct state of checkbox
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

			// one field (cb plus label)
			echo '<p>';
				// prepare checkbox-data
				$cb_name = $p_staff->BenutzerID;
				$cb_id = $course_id.'-'.$p_staff->BenutzerID;

				// define checkbox attrs-array
				$cb_data = array(
					'name' => $cb_name,
					'id' => $cb_id,
					'value' => $course_id,
					'checked' => $checked,
				);
				echo form_checkbox($cb_data);

				// init
				$label_id = '';
				$label_text = '';
				
				// prepare label-data
				$label_id = $label_id_prefix.$course_id.'-'.$p_staff->BenutzerID;
				$label_text = $p_staff->Vorname.' '.$p_staff->Nachname;
				
				// check if staff stored a name
				if($label_text == ' '){
					$label_text = 'keine Angabe';
				}
				
				// define label attrs-array
				$label_attrs = array(
					'id' => $label_id,
					'style' => 'display:inline'
				);
                echo '&nbsp;&nbsp;';
				echo form_label($label_text, '', $label_attrs);
			echo '</p>';

			$counter++;
	    } // endforeach
	    echo '</div>';
	    echo form_close();

		// print add-tut-button
		echo $tut_button;
	} // endif
    ?>
</div>