<!--  first row in stdgng-list-table  -->
<tr id="coursetable-new-course">
    <!-- id - just for orientation -->
    <?php	    
	$id_attrs = array(
	    'id' => 'stdgng-new-course-head',
	);
    ?>
    <td><?php print form_label('+', $id_attrs); ?> </td>

    <!-- Kursname -->
    <?php 
	$kursnameData = array(
	    'name' => 'Kursname',
	    'id' => 'kursname',
	    'value' => ''
	);
    ?>
    <td><?php print form_input($kursnameData); ?></td>

    <!-- Abk. -->
    <?php 
	$kursnameKurzData = array(
	    'name' => 'kurs_kurz',
	    'id' => 'kursname-kurz',
	    'value' => '',
	    'class' => 'span'
	);
    ?>
    <td><?php print form_input($kursnameKurzData); ?></td>

    <!-- CP -->
    <?php 
	$creditpointsData = array(
	    'name' => 'Creditpoints',
	    'id' => 'cp',
	    'value' => '',
	    'class' => 'span'
	);
    ?>
    <td><?php print form_input($creditpointsData); ?></td>

    <!-- SWS -->
    <?php
	// run through all 6 SWS-types and generate data-array for usage with input-field
	// get data for Vorlesung
	$swsDataVorl = array(
	    'name' => 'SWS_Vorlesung',
	    'id' => 'sws-vorl',
	    'value' => '',
	    'class' => 'span'
	);

	// get data for Uebung
	$swsDataUeb = array(
	    'name' => 'SWS_Uebung',
	    'id' => 'sws-ueb',
	    'value' => '',
	    'class' => 'span'
	);

	// get data for Praktikum
	$swsDataPrakt = array(
	    'name' => 'SWS_Praktikum',
	    'id' => 'sws-prakt',
	    'value' => '',
	    'class' => 'span'
	);

	// get data for Projekt
	$swsDataPro = array(
	    'name' => 'SWS_Projekt',
	    'id' => 'sws-pro',
	    'value' => '',
	    'class' => 'span'
	);

	// get data for Seminar
	$swsDataSem = array(
	    'name' => 'SWS_Seminar',
	    'id' => 'sws-seminar',
	    'value' => '',
	    'class' => 'span'
	);

	// get data for Seminarunterricht - ?? // TODO check if this field is still needed / in use?
	$swsDataSemU = array(
	    'name' => 'SWS_SeminarUnterricht',
	    'id' => 'sws-seminar-u',
	    'value' => '',
	    'class' => 'span'
	);
    ?>			
    <td>
<!--	<table>
	    <tbody>
		<tr>-->
		    <div class="span2"><?php print form_input($swsDataVorl); ?></div>
		    <div class="span2"><?php print form_input($swsDataUeb); ?></div>
		    <div class="span2"><?php print form_input($swsDataPrakt); ?></div>
		    <div class="span2"><?php print form_input($swsDataPro); ?></div>
		    <div class="span2"><?php print form_input($swsDataSem); ?></div>
		    <div class="span2"><?php print form_input($swsDataSemU); ?></div>
<!--		</tr>
	    </tbody>
	</table>-->
    </td>

    <!-- Semester -->
    <?php 
	$dropdown_attributes = 'class = "span"';
    ?>
    <td>
	<?php 
	    print form_dropdown('Semester', $semester_dropdown, 0, $dropdown_attributes);
	?>
    </td>

    <!-- Prüfungstypen -->
    <?php
	// exam-type 1
	$data_dropdown_ext1 = array(
	    'name' => 'ext_1',
	    'value' => '1',
	    'id' => 'ext-1'
	);
	// exam-type 2
	$data_dropdown_ext2 = array(
	    'name' => 'ext_2',
	    'value' => '1',
	    'id' => 'ext-2'
	);
	// exam-type 3
	$data_dropdown_ext3 = array(
	    'name' => 'ext_3',
	    'value' => '1',
	    'id' => 'ext-3'
	);
	// exam-type 4
	$data_dropdown_ext4 = array(
	    'name' => 'ext_4',
	    'value' => '1',
	    'id' => 'ext-4'
	);
	// exam-type 5
	$data_dropdown_ext5 = array(
	    'name' => 'ext_5',
	    'value' => '1',
	    'id' => 'ext-5'
	);
	// exam-type 6
	$data_dropdown_ext6 = array(
	    'name' => 'ext_6',
	    'value' => '1',
	    'id' => 'ext-6'
	);
	// exam-type 7
	$data_dropdown_ext7 = array(
	    'name' => 'ext_7',
	    'value' => '1',
	    'id' => 'ext-7'
	);
	// exam-type 8
	$data_dropdown_ext8 = array(
	    'name' => 'ext_8',
	    'value' => '1',
	    'id' => 'ext-8'
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
	    'name' => 'Beschreibung',
	    'id' => 'beschreibung',
	    'value' => '',
	    'rows' => 3,
	    'class' => 'span'
	);
    ?>
    <td><?php print form_textarea($textareaData); ?></td>

    <!-- add-course-button -->
    <?php 
	$submit_attributes = 'id=#stdgng-course-create-new class="btn-warning"';
    ?>
    <td>
	<?php
	    print form_submit('save_new_course', 'Kurs hinzufügen', $submit_attributes);
	    echo form_hidden('StudiengangID', $stdgng_id);
	?>
    </td>

</tr>