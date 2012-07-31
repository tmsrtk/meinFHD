<!--  single row in stdgng-list-table  -->
<tr style="clear:both">
    <!-- id - just for orientation -->
    <?php	    
	$id_attrs = array(
	    'id' => 'stdgng-table-id',
	);
    ?>
    <td><?php print form_label($KursID, $id_attrs); ?> </td>

    <!-- Kursname -->
    <!-- get data and store in associative array to use code-igniters form_input-->
    <?php 
	$kursnameData = array(
	    'name' => $KursID.'Kursname',
	    'id' => 'kursname',
	    'value' => $Kursname
	);
    ?>
    <td><?php print form_input($kursnameData); ?></td>

    <!-- Abk. -->
    <?php 
	$kursnameKurzData = array(
	    'name' => $KursID.'kurs_kurz',
	    'id' => 'kursname-kurz',
	    'value' => $kurs_kurz,
	    'class' => 'span'
	);
    ?>
    <td><?php print form_input($kursnameKurzData); ?></td>

    <!-- CP -->
    <?php 
	$creditpointsData = array(
	    'name' => $KursID.'Creditpoints',
	    'id' => 'cp',
	    'value' => $Creditpoints,
	    'class' => 'span'
	);
    ?>
    <td><?php print form_input($creditpointsData); ?></td>

    <!-- SWS -->
    <?php
	// run through all 6 SWS-types and generate data-array for usage with input-field
	// get data for Vorlesung
	if($SWS_Vorlesung === '0'){
		$sd_SWS_Vorlesung = ' ';
	} else {
		$sd_SWS_Vorlesung = $SWS_Vorlesung;
	}
	$swsDataVorl = array(
	    'name' => $KursID.'SWS_Vorlesung',
	    'id' => 'sws-vorl',
	    'value' => $sd_SWS_Vorlesung,
	    'class' => 'span'
	);

	// get data for Uebung
	if($SWS_Uebung === '0'){
		$sd_SWS_Uebung = ' ';
	} else {
		$sd_SWS_Uebung = $SWS_Uebung;
	}
	$swsDataUeb = array(
	    'name' => $KursID.'SWS_Uebung',
	    'id' => 'sws-ueb',
	    'value' => $sd_SWS_Uebung,
	    'class' => 'span'
	);

	// get data for Praktikum
	if($SWS_Praktikum === '0'){
		$sd_SWS_Praktikum = ' ';
	} else {
		$sd_SWS_Praktikum = $SWS_Praktikum;
	}
	$swsDataPrakt = array(
	    'name' => $KursID.'SWS_Praktikum',
	    'id' => 'sws-prakt',
	    'value' => $sd_SWS_Praktikum,
	    'class' => 'span'
	);

	// get data for Projekt
	if($SWS_Projekt === '0'){
		$sd_SWS_Projekt = ' ';
	} else {
		$sd_SWS_Projekt = $SWS_Projekt;
	}
	$swsDataPro = array(
	    'name' => $KursID.'SWS_Projekt',
	    'id' => 'sws-pro',
	    'value' => $sd_SWS_Projekt,
	    'class' => 'span'
	);

	// get data for Seminar
	if($SWS_Seminar === '0'){
		$sd_SWS_Seminar = ' ';
	} else {
		$sd_SWS_Seminar = $SWS_Seminar;
	}
	$swsDataSem = array(
	    'name' => $KursID.'SWS_Seminar',
	    'id' => 'sws-seminar',
	    'value' => $sd_SWS_Seminar,
	    'class' => 'span'
	);

	// get data for Seminarunterricht - ?? // TODO check if this field is still needed / in use?
	if($SWS_SeminarUnterricht === '0'){
		$sd_SWS_SeminarUnterricht = ' ';
	} else {
		$sd_SWS_SeminarUnterricht = $SWS_SeminarUnterricht;
	}
	$swsDataSemU = array(
	    'name' => $KursID.'SWS_SeminarUnterricht',
	    'id' => 'sws-seminar-u',
	    'value' => $sd_SWS_SeminarUnterricht,
	    'class' => 'span'
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
	$dropdown_attributes = 'class = "span"';
    ?>
    <td>
	<?php 
	    if($Semester !== '0'){
		print form_dropdown($KursID.'Semester', $SemesterDropdown, ($Semester-1), $dropdown_attributes);
	    } else {
		print form_dropdown($KursID.'Semester', $SemesterDropdown, 0, $dropdown_attributes);
	    }
	?>
    </td>

    <!-- Prüfungstypen -->
    <?php
	// exam-type 1
	$data_dropdown_ext1 = array(
	    'name' => $KursID.'ext_1',
	    'value' => '1',
	    'id' => 'ext-1',
	    'checked' => $pruefungstyp_1,
	);
	// exam-type 2
	$data_dropdown_ext2 = array(
	    'name' => $KursID.'ext_2',
	    'value' => '1',
	    'id' => 'ext-2',
	    'checked' => $pruefungstyp_2,
	);
	// exam-type 3
	$data_dropdown_ext3 = array(
	    'name' => $KursID.'ext_3',
	    'value' => '1',
	    'id' => 'ext-3',
	    'checked' => $pruefungstyp_3,
	);
	// exam-type 4
	$data_dropdown_ext4 = array(
	    'name' => $KursID.'ext_4',
	    'value' => '1',
	    'id' => 'ext-4',
	    'checked' => $pruefungstyp_4,
	);
	// exam-type 5
	$data_dropdown_ext5 = array(
	    'name' => $KursID.'ext_5',
	    'value' => '1',
	    'id' => 'ext-5',
	    'checked' => $pruefungstyp_5,
	);
	// exam-type 6
	$data_dropdown_ext6 = array(
	    'name' => $KursID.'ext_6',
	    'value' => '1',
	    'id' => 'ext-6',
	    'checked' => $pruefungstyp_6,
	);
	// exam-type 7
	$data_dropdown_ext7 = array(
	    'name' => $KursID.'ext_7',
	    'value' => '1',
	    'id' => 'ext-7',
	    'checked' => $pruefungstyp_7,
	);
	// exam-type 8
	$data_dropdown_ext8 = array(
	    'name' => $KursID.'ext_8',
	    'value' => '1',
	    'id' => 'ext-8',
	    'checked' => $pruefungstyp_8,
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
	    'name' => $KursID.'Beschreibung',
	    'id' => 'beschreibung',
	    'value' => $Beschreibung,
	    'rows' => 3,
	    'class' => 'span'
	);
    ?>
    <td><?php print form_textarea($textareaData); ?></td>

    <!-- Aktion - Button -->
    <?php 
	if($KursID == 0){ 
	    $buttonData = array(
		'name' => $KursID.'createCourse',
		'id' => 'create-stdgng-btn',
		'value' => true,
		'content' => 'Hinzufügen'
	    );
	} else {
	    $buttonData = array(
		'name' => $KursID.'_'.$stdgng_id,
		'id' => 'delete-stdgng-btn',
		'data-id' => $KursID,
		'value' => true,
		'content' => 'Löschen'
	    );
	}
    ?>
    <td><?php print form_button($buttonData); ?></td>

</tr>