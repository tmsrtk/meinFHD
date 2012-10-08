<!--  first row in degree-program-list-table  -->
<tr>
    <!-- id - just for orientation -->
    <?php	    
		$id_attrs = array(
			'id' => 'degree-program-new-course-head',
		);
    ?>
    <td><?php print form_label('+', $id_attrs); ?> </td>

    <!-- Kursname -->
    <?php 
		$kursnameData = array(
			'name' => 'NEW_Kursname',
			'id' => 'new-course-coursename',
			'value' => '',
			'placeholder' => 'Kursname'
		);
    ?>
    <td><?php print form_input($kursnameData); ?></td>

    <!-- Abk. -->
    <?php 
		$kursnameKurzData = array(
			'name' => 'NEW_kurs_kurz',
			'id' => 'new-course-coursename-short',
			'value' => '',
			'class' => 'span',
			'placeholder' => 'Abk.'
		);
    ?>
    <td><?php print form_input($kursnameKurzData); ?></td>

    <!-- CP -->
    <?php 
	$creditpointsData = array(
	    'name' => 'NEW_Creditpoints',
	    'id' => 'new-course-cp',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'CP'
	);
    ?>
    <td><?php print form_input($creditpointsData); ?></td>

    <!-- SWS -->
    <?php
	// run through all 6 SWS-types and generate data-array for usage with input-field
	// get data for Vorlesung
	$swsDataVorl = array(
	    'name' => 'NEW_SWS_Vorlesung',
	    'id' => 'new-course-sws-vorl',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'V'
	);

	// get data for Uebung
	$swsDataUeb = array(
	    'name' => 'NEW_SWS_Uebung',
	    'id' => 'new-course-sws-ueb',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'UE'
	);

	// get data for Praktikum
	$swsDataPrakt = array(
	    'name' => 'NEW_SWS_Praktikum',
	    'id' => 'new-course-sws-prakt',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'P'
	);

	// get data for Projekt
	$swsDataPro = array(
	    'name' => 'NEW_SWS_Projekt',
	    'id' => 'new-course-sws-pro',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'Pr'
	);

	// get data for Seminar
	$swsDataSem = array(
	    'name' => 'NEW_SWS_Seminar',
	    'id' => 'new-course-sws-seminar',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'S'
	);

	// get data for Seminarunterricht - ?? // TODO check if this field is still needed / in use?
	$swsDataSemU = array(
	    'name' => 'NEW_SWS_SeminarUnterricht',
	    'id' => 'new-course-sws-seminar-u',
	    'value' => '',
	    'class' => 'span',
	    'placeholder' => 'SU'
	);
    ?>			
    <td>
		<div class="span2"><?php print form_input($swsDataVorl); ?></div>
		<div class="span2"><?php print form_input($swsDataUeb); ?></div>
		<div class="span2"><?php print form_input($swsDataPrakt); ?></div>
		<div class="span2"><?php print form_input($swsDataPro); ?></div>
		<div class="span2"><?php print form_input($swsDataSem); ?></div>
		<div class="span2"><?php print form_input($swsDataSemU); ?></div>
    </td>

    <!-- Semester -->
    <?php 
		$dropdown_attributes = 'id="new-course-semester" class = "span"';
    ?>
    <td>
	<?php 
	    print form_dropdown('NEW_Semester', $semester_dropdown, 0, $dropdown_attributes);
	?>
    </td>

    <!-- Prüfungstypen -->
    <?php
	// exam-type 1
	$data_dropdown_ext1 = array(
	    'name' => 'NEW_ext_1',
	    'value' => '1',
	    'id' => 'new-course-ext-1'
	);
	// exam-type 2
	$data_dropdown_ext2 = array(
	    'name' => 'NEW_ext_2',
	    'value' => '1',
	    'id' => 'new-course-ext-2'
	);
	// exam-type 3
	$data_dropdown_ext3 = array(
	    'name' => 'NEW_ext_3',
	    'value' => '1',
	    'id' => 'new-course-ext-3'
	);
	// exam-type 4
	$data_dropdown_ext4 = array(
	    'name' => 'NEW_ext_4',
	    'value' => '1',
	    'id' => 'new-course-ext-4'
	);
	// exam-type 5
	$data_dropdown_ext5 = array(
	    'name' => 'NEW_ext_5',
	    'value' => '1',
	    'id' => 'new-course-ext-5'
	);
	// exam-type 6
	$data_dropdown_ext6 = array(
	    'name' => 'NEW_ext_6',
	    'value' => '1',
	    'id' => 'new-course-ext-6'
	);
	// exam-type 7
	$data_dropdown_ext7 = array(
	    'name' => 'NEW_ext_7',
	    'value' => '1',
	    'id' => 'new-course-ext-7'
	);
	// exam-type 8
	$data_dropdown_ext8 = array(
	    'name' => 'NEW_ext_8',
	    'value' => '1',
	    'id' => 'new-course-ext-8'
	);
    ?>
    <td nowrap>
	<?php print form_checkbox($data_dropdown_ext1); ?>
	<?php print form_checkbox($data_dropdown_ext2); ?>
	<?php print form_checkbox($data_dropdown_ext3); ?>
	<?php print form_checkbox($data_dropdown_ext4); ?>
	<?php print form_checkbox($data_dropdown_ext5); ?>
	<?php print form_checkbox($data_dropdown_ext6); ?>
	<?php print form_checkbox($data_dropdown_ext7); ?>
	<?php print form_checkbox($data_dropdown_ext8); ?>
    </td>

    <!-- Beschreibung -->
    <?php 
	$textareaData = array(
	    'name' => 'NEW_Beschreibung',
	    'id' => 'new-course-description',
	    'value' => '',
	    'rows' => 3,
	    'class' => 'span'
	);
    ?>
    <td><?php print form_textarea($textareaData); ?></td>

    <!-- add-course-button -->
	<?php 
		$buttonData = array(
			'name' => $dp_id,
			'class' => 'btn btn-warning span',
			'id' => 'degree-program-course-create',
			'value' => true,
			'content' => 'Kurs hinzufügen'
		);
    ?>
    <td><?php print form_button($buttonData); ?></td>

</tr>