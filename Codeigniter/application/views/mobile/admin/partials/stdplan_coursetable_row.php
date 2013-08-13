<?php 
    // common dropdown attributes
    $dropdown_attributes = 'class = "span"';

?>

<tr>
    <!-- course name -->
    <?php	    
		// !! important: to save changed data correctly, name has to consist of SPKursID and the column-name in database
		$course_name_attrs = array(
			'id' => 'stdplan-list-coursename',
			'class' => 'label label-info'
		);
    ?>
    <td>
		<?php

            // if the first row variable is not set fill it with the passed course-name
			if(!$first_row){
				print '<p class="label label-info">'.$kursname.'</p>';
				print '<br />';
			}
            // the viewed row is the first row, so prepare the dropdown attributes
            else {
				$new_course_courses_dropdown_attrs = $dropdown_attributes.' id="new-course-courses-dropdown"';
				print form_dropdown(
					'NEW_KursID',
					$courses_dropdown_options,
					0, 
					$new_course_courses_dropdown_attrs
				);
			}
		?>

     <!-- dropdown for profs -->
		<?php
            // if it is not the first row, read out the passed prof data and print out the dropdown
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_DozentID', 
					$profs_dropdown_options,
					$dozent_id,
					$dropdown_attributes
				);
			}
            // the viewed row is the first row, so prepare the dropdown with all profs
            else {
				$new_course_profs_dropdown_attrs = $dropdown_attributes.' id="new-course-profs-dropdown"';
				print form_dropdown(
					'NEW_DozentID', 
					$profs_dropdown_options,
					0,
					$new_course_profs_dropdown_attrs
				);
			}
		?>
    </td>

    <!-- dropdown for event-types-->
    <td>
		<?php
            // if the viewed row is not the first row read out the event-type of the viewed course
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_VeranstaltungsformID',
					$eventtype_dropdown_options,
					$veranstaltungsform_id-1, // !! ARRAY - minus 1
					$dropdown_attributes);
			}
            // the viewed row is the first row, so prepare the dropdown without pre-selection of an event-type
            else {
				$new_course_event_dropdown_attrs = $dropdown_attributes.' id = "new-course-eventtype-dropdown"';
				print form_dropdown(
					'NEW_VeranstaltungsformID',
					$eventtype_dropdown_options,
					0,
					$new_course_event_dropdown_attrs);
			}
		?>

        <!-- input-field for alternatives-->
        <?php
            // if the viewed row is not the first row add the alternative attribute information
            if(!$first_row){
                $eventy_alt_data = array(
                    'name' => $spkurs_id.'_VeranstaltungsformAlternative',
                    'id' => 'stdplan-list-alternative',
                    'value' => $alternative,
                    'class' => 'span',
                    'placeholder' => 'Alternative'
                );
            }
            // if the viewed row is the first row add the new_alternative-attribute information
            else {
                $eventy_alt_data = array(
                    'name' => 'NEW_VeranstaltungsformAlternative',
                    'id' => 'new-course-stdplan-list-alternative',
                    'value' => '',
                    'class' => 'span',
                    'placeholder' => 'Alternative'
                );
            }

            // print out the input form
		    print form_input($eventy_alt_data);
        ?>

        <!-- room-->
        <?php
            // if the viewed row ist not the first row add all rooms to the dropdown and preselect the room
            if(!$first_row){
                $room_data = array(
                    'name' => $spkurs_id.'_Raum',
                    'id' => 'stdplan-list-room',
                    'value' => $raum,
                    'class' => 'span',
                    'placeholder' => 'Raum'
                );
            }
            // if the viewed row is the first row add the rooms to the dropdown and preselect none room
            else {
                $room_data = array(
                    'name' => 'NEW_Raum',
                    'id' => 'new-course-stdplan-list-room',
                    'value' => '',
                    'class' => 'span',
                    'placeholder' => 'Raum'
                );
            }

            print form_input($room_data);
        ?>
	</td>


    <td>
        <!-- dropdown for starttime-->
        <?php
            // if the viewed row is not the first row preselect the starttime, that is passed from the controller
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_StartID',
					$starttimes_dropdown_options,
					$beginn_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			}
            // if the viewed row is the first row add the starttimes to the dropdown and preselect an empty value
            else {
				$new_course_starttime_dropdown_attrs = $dropdown_attributes.' id="new-course-starttime-dropdown"';
				print form_dropdown(
					'NEW_StartID',
					$starttimes_dropdown_options,
					0,
					$new_course_starttime_dropdown_attrs
				);
			}
		?>

        <!-- dropdown for endtime -->
		<?php
            // if the viewed row is not the firs row add all endtimes and preselect the passed endtime value
		 	if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_EndeID',
					$endtimes_dropdown_options,
					$ende_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			}
            // if the viewed row is the first row add all endtimes and do not preselect any value
            else {
				$new_course_endtime_dropdown_attrs = $dropdown_attributes.' id="new-course-endtime-dropdown"';
				print form_dropdown(
					'NEW_EndeID',
					$endtimes_dropdown_options,
					0, 
					$new_course_endtime_dropdown_attrs
				);
			}
		?>

        <!-- dropdown for day -->
		<?php
            // if the viewed row is not the first row add all possible days to the dropdown and preselect the passed day
		 	if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_TagID',
					$days_dropdown_options,
					$tag_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			}
            // if the viewed row is the first row add all possible days to the dropdown and do not preselect any value
            else {
				$new_course_days_dropdown_attrs = $dropdown_attributes.' id="new-course-days-dropdown"';
				print form_dropdown(
					'NEW_TagID',
					$days_dropdown_options,
					0,
					$new_course_days_dropdown_attrs
				);
			}
		?>
    </td>

    <td>
    <!-- checkbox for wpf-->
    <?php
        // if the viewed row is not the first row check or uncheck the wpf checkbox depending on the passed course type
		if(!$first_row){
			$wpf_cb_data = array(
				'name' => $spkurs_id.'_isWPF',
				'id' => $spkurs_id.'-wpfcheckbox',
				'class' => 'stdplan-edit-wpfcheckbox',
				'value' => 'accept',
				'data-spcid' => $spkurs_id,
				'checked' => ($wpf_flag === '1') ? true : false
			);
		}
        // if the viewed row is the first row do not check or uncheck the wpf checkbox
        else {
			$wpf_cb_data = array(
				'name' => 'NEW_isWPF',
				'id' => 'new-course-stdplan-list-wpfcheckbox',
				'class' => 'stdplan-edit-wpfcheckbox',
				'value' => '',
				'data-spcid' => 'new-course-stdplan-list',
				'checked' => false
			);
		}

        print '<p>'.form_checkbox($wpf_cb_data).'</p><br />';
        ?>

    <!-- inputfield for wpf-name-->
    <?php
        // if the viewed row is not the first row add the value for the wpf-name, that is passed by the controller
		if(!$first_row){
			$wpf_data = array(
				'name' => $spkurs_id.'_WPFName',
				'id' => $spkurs_id.'-wpfname',
				'value' => $wpf_name,
				'data-spcid' => $spkurs_id,
				'placeholder' => 'WPF Name',
				'class' => 'span stdplan-edit-wpfname'
			);
		}
        // if the viewed row is the first row do not add any value to the input field
        else {
			$wpf_data = array(
				'name' => 'NEW_WPFName',
				'id' => 'new-course-stdplan-list-wpfname',
				'value' => '',
				'data-spcid' => 'new-course-stdplan-list',
				'placeholder' => 'WPF Name',
				'class' => 'span stdplan-edit-wpfname'
			);
		}
    ?>
		<?php print form_input($wpf_data); ?>
	</td>

    <!-- dropdown for color - at first: find out key-->
    <td>
		<?php
            // if the viewed row is the first row add all possible color values to the dropdown and preselect the passed value
			if(!$first_row){
				// find out key for color-dropdown
				$ck = '';
				foreach ($colors_dropdown_options as $key => $value){
					if($value == $farbe) {
						$ck = $key;
					}
				}
				// print color-dropdown
				print form_dropdown(
					$spkurs_id.'_'.'Farbe',
					$colors_dropdown_options,
					$ck,
					$dropdown_attributes
				);
			}
            else {
                // if the viewed row is the first row add all possible color values to the dropdown and do not preselct any value
				$new_course_color_dropdown_attrs = $dropdown_attributes.' id="new-course-color-dropdown"';
				print form_dropdown(
					'NEW_Farbe',
					$colors_dropdown_options,
					0,
					$new_course_color_dropdown_attrs
				);
			}
		?>
    </td>

    <td>
    <!-- delete/add button-->
    <?php
        // if the viewed row is not the first row add the delete course button to the view
		if(!$first_row){ 
			$buttonData = array(
				'name' => $kurs_ids_split[0].'_'.$kurs_ids_split[1].'_'.$kurs_ids_split[2].'_'.$spkurs_id,
				'id' => $spkurs_id.'delete-btn',
				'class' => 'btn btn-danger span delete-stdpln-btn',
				'data-id' => $spkurs_id,
				'value' => true,
				'content' => 'L&ouml;schen'
			);
		}
        // if the viewed row is the first row add the create new course button to the view
        else {
			$buttonData = array(
				'name' => $kurs_ids_split[0].'_'.$kurs_ids_split[1].'_'.$kurs_ids_split[2],
				'id' => 'create-btn-stdpln',
				'class' => 'btn btn-warning span',
				'value' => true,
				'content' => 'Hinzuf&uuml;gen'
			);
		}

        print form_button($buttonData); ?>
    </td>
</tr>