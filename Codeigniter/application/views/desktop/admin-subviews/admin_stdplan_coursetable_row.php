<?php 
    // common dropdown attrs
    $dropdown_attributes = 'class = "span2"';
?>
	    

<tr>

    <!-- Kursname -->
    <?php	    
		// !! important: to save changed data correctly, name has to consist of SPKursID and the collumn-name in database
		$course_name_attrs = array(
			'id' => 'stdplan-list-coursename',
		);
    ?>
    <td>
		<?php
			if(!$first_row){
				print form_label($kursname, $course_name_attrs);
			} else {
				$new_course_courses_dropdown_attrs = $dropdown_attributes.' id="new-course-courses-dropdown"';
				print form_dropdown(
					'NEW_KursID',
					$courses_dropdown_options,
					0, 
					$new_course_courses_dropdown_attrs
				);
			}
		?>
	</td>

    <!-- dropdown for event-types-->
    <td>
		<?php 
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_VeranstaltungsformID',
					$eventtype_dropdown_options,
					$veranstaltungsform_id-1, // !! ARRAY - minus 1
					$dropdown_attributes);
			} else {
				$new_course_event_dropdown_attrs = $dropdown_attributes.' id = "new-course-eventtype-dropdown"';
				print form_dropdown(
					'NEW_VeranstaltungsformID',
					$eventtype_dropdown_options,
					0,
					$new_course_event_dropdown_attrs);
			}
		?>
    </td>

    <!-- input-field for alternatives-->
    <?php
		if(!$first_row){
			$eventy_alt_data = array(
				'name' => $spkurs_id.'_VeranstaltungsformAlternative',
				'id' => 'stdplan-list-alternative',
				'value' => $alternative,
				'class' => 'span1'
			);
		} else {
			$eventy_alt_data = array(
				'name' => 'NEW_VeranstaltungsformAlternative',
				'id' => 'new-course-stdplan-list-alternative',
				'value' => '',
				'class' => 'span1'
			);
		}
    ?>
    <td><?php print form_input($eventy_alt_data); ?></td>

    <!-- room-->
    <?php
		if(!$first_row){
			$room_data = array(
				'name' => $spkurs_id.'_Raum',
				'id' => 'stdplan-list-room',
				'value' => $raum,
				'class' => 'span2'
			);
		} else {
			$room_data = array(
				'name' => 'NEW_Raum',
				'id' => 'new-course-stdplan-list-room',
				'value' => '',
				'class' => 'span2'
			);
		}
    ?>
    <td><?php print form_input($room_data); ?></td>

    <!-- dropdown for profs-->
    <td>
		<?php 
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_DozentID', 
					$profs_dropdown_options,
					$dozent_id,
					$dropdown_attributes
				);
			} else {
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

    <!-- dropdown for starttime-->
    <td>
		<?php
			if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_StartID',
					$starttimes_dropdown_options,
					$beginn_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			} else {
				$new_course_starttime_dropdown_attrs = $dropdown_attributes.' id="new-course-starttime-dropdown"';
				print form_dropdown(
					'NEW_StartID',
					$starttimes_dropdown_options,
					0,
					$new_course_starttime_dropdown_attrs
				);
			}
		?>
    </td>

    <!-- dropdown for endtime-->
    <td>
		<?php
		 	if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_EndeID',
					$endtimes_dropdown_options,
					$ende_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			} else {
				$new_course_endtime_dropdown_attrs = $dropdown_attributes.' id="new-course-endtime-dropdown"';
				print form_dropdown(
					'NEW_EndeID',
					$endtimes_dropdown_options,
					0, 
					$new_course_endtime_dropdown_attrs
				);
			}
		?>
    </td>

    <!-- dropdown for day-->
    <td>
		<?php
		 	if(!$first_row){
				print form_dropdown(
					$spkurs_id.'_TagID',
					$days_dropdown_options,
					$tag_id-1, // !! ARRAY - minus 1
					$dropdown_attributes
				);
			} else {
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

    <!-- checkbox for wpf-->
    <?php
		if(!$first_row){
			$wpf_cb_data = array(
				'name' => $spkurs_id.'_isWPF',
				'id' => 'stdplan-list-wpfcheckbox',
				'value' => 'accept',
				'checked' => ($wpf_flag === '1') ? true : false
			);
		} else {
			$wpf_cb_data = array(
				'name' => 'NEW_isWPF',
				'id' => 'new-course-stdplan-list-wpfcheckbox',
				'value' => '',
				'checked' => false
			);
		}
    ?>
    <td><?php print form_checkbox($wpf_cb_data); ?></td>

    <!-- inputfield for wpf-name-->
    <?php 
		if(!$first_row){
			$wpf_data = array(
				'name' => $spkurs_id.'_WPFName',
				'id' => 'stdplan-list-wpfname',
				'value' => $wpf_name,
				'class' => 'span2'
			);
		} else {
			$wpf_data = array(
				'name' => 'NEW_WPFName',
				'id' => 'new-course-stdplan-list-wpfname',
				'value' => '',
				'class' => 'span2'
			);
		}
    ?>
    <td><?php print form_input($wpf_data); ?></td>

    <!-- dropdown for color - at first: find out key-->
    <td>
		<?php
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
			} else {
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

    <!-- delete/add button-->
    <?php
		if(!$first_row){ 
			$buttonData = array(
				'name' => $kurs_ids_split[0].'_'.$kurs_ids_split[1].'_'.$kurs_ids_split[2].'_'.$spkurs_id,
				'id' => $spkurs_id.'delete-btn',
				'class' => 'btn btn-danger span2 delete-stdpln-btn',
				'data-id' => $spkurs_id,
				'value' => true,
				'content' => 'Löschen'
			);
		} else {
			$buttonData = array(
				'name' => $kurs_ids_split[0].'_'.$kurs_ids_split[1].'_'.$kurs_ids_split[2],
				'id' => 'create-btn-stdpln',
				'class' => 'btn btn-warning span2',
				'value' => true,
				'content' => 'Hinzufügen'
			);
		}
    ?>
    <td><?php print form_button($buttonData); ?></td>
</tr>