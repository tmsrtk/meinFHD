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
    <td><?php print form_label($kursname, $course_name_attrs); ?> </td>

    <!-- dropdown for event-types-->
    <td><?php print form_dropdown(
	    $spkurs_id.'_VeranstaltungsformID',
	    $eventtype_dropdown_options,
	    $veranstaltungsform_id-1, // !! ARRAY - minus 1
	    $dropdown_attributes); ?>
    </td>

    <!-- input-field for alternatives-->
    <?php
	$eventy_alt_data = array(
	    'name' => $spkurs_id.'_VeranstaltungsformAlternative',
	    'id' => 'stdplan-list-alternative',
	    'value' => $alternative,
	    'class' => 'span1'
	);
    ?>
    <td><?php print form_input($eventy_alt_data); ?></td>

    <!-- room-->
    <?php
	$room_data = array(
	    'name' => $spkurs_id.'_Raum',
	    'id' => 'stdplan-list-room',
	    'value' => $raum,
	    'class' => 'span2'
	);
    ?>
    <td><?php print form_input($room_data); ?></td>

    <!-- dropdown for profs-->
    <td><?php print form_dropdown(
	    $spkurs_id.'_DozentID', 
	    $profs_dropdown_options,
	    $dozent_id,
	    $dropdown_attributes); ?>
    </td>

    <!-- dropdown for starttime-->
    <td><?php print form_dropdown(
	    $spkurs_id.'_StartID',
	    $starttimes_dropdown_options,
	    $beginn_id-1, // !! ARRAY - minus 1
	    $dropdown_attributes); ?>
    </td>

    <!-- dropdown for endtime-->
    <td><?php print form_dropdown(
	    $spkurs_id.'_EndeID',
	    $endtimes_dropdown_options,
	    $ende_id-1, // !! ARRAY - minus 1
	    $dropdown_attributes); ?>
    </td>

    <!-- dropdown for day-->
    <td><?php print form_dropdown(
	    $spkurs_id.'_TagID',
	    $days_dropdown_options,
	    $tag_id-1, // !! ARRAY - minus 1
	    $dropdown_attributes); ?>
    </td>

    <!-- checkbox for wpf-->
    <?php
	$wpf_cb_data = array(
	    'name' => $spkurs_id.'_isWPF',
	    'id' => 'stdplan-list-wpfcheckbox',
	    'value' => 'accept',
	    'checked' => ($wpf_flag === '1') ? true : false
	);
    ?>
    <td><?php print form_checkbox($wpf_cb_data); ?></td>

    <!-- inputfield for wpf-name-->
    <?php 
	$wpf_data = array(
	    'name' => $spkurs_id.'_WPFName',
	    'id' => 'stdplan-list-wpfname',
	    'value' => $wpf_name,
	    'class' => 'span2'
	);
    ?>
    <td><?php print form_input($wpf_data); ?></td>

    <!-- dropdown for color - at first: find out key-->
    <?php 
	$ck = '';
	foreach ($colors_dropdown_options as $key => $value){
	    if($value == $farbe) {
		$ck = $key;
	    }
	}
    ?>
    <td><?php print form_dropdown(
	    $spkurs_id.'_'.'Farbe',
	    $colors_dropdown_options,
	    $ck,
	    $dropdown_attributes); ?>
    </td>

    <!-- delete/add button-->
    <?php
	if($spkurs_id == 0){ 
		$buttonData = array(
		    'name' => $spkurs_id.'createCourse',
		    'id' => 'create_btn_stdpln',
		    'value' => true,
		    'content' => 'Hinzufügen'
		);
	} else {
		$buttonData = array(
		    'name' => $spkurs_id.'deleteCourse',
		    'id' => 'delete_btn_stdpln',
		    'data-id' => $spkurs_id,
		    'value' => true,
		    'content' => 'Löschen'
		);
	}
    ?>

    <!-- TODO event for button-click - id vergeben und über AJAX-->
    <td><?php print form_button($buttonData); ?></td>
</tr>