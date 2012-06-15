<div>
    <!--  single row in stdgng-list-table  -->
    <tr>
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
		'id' => 'Kursname',
		'value' => $Kursname
	    );
	?>
	<td><?php print form_input($kursnameData); ?></td>

	<!-- Abk. -->
	<?php 
	    $kursnameKurzData = array(
		'name' => $KursID.'kurs_kurz',
		'id' => 'KursnameKurz',
		'value' => $kurs_kurz,
		'class' => 'span1'
	    );
	?>
	<td><?php print form_input($kursnameKurzData); ?></td>

	<!-- CP -->
	<?php 
	    $creditpointsData = array(
		'name' => $KursID.'Creditpoints',
		'id' => 'CP',
		'value' => $Creditpoints,
		'class' => 'span1'
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
		'id' => 'SWS_Vorl',
		'value' => $sd_SWS_Vorlesung,
		'class' => 'span1'
	    );

	    // get data for Uebung
	    if($SWS_Uebung === '0'){
		    $sd_SWS_Uebung = ' ';
	    } else {
		    $sd_SWS_Uebung = $SWS_Uebung;
	    }
	    $swsDataUeb = array(
		'name' => $KursID.'SWS_Uebung',
		'id' => 'SWS_Ueb',
		'value' => $sd_SWS_Uebung,
		'class' => 'span1'
	    );

	    // get data for Praktikum
	    if($SWS_Praktikum === '0'){
		    $sd_SWS_Praktikum = ' ';
	    } else {
		    $sd_SWS_Praktikum = $SWS_Praktikum;
	    }
	    $swsDataPrakt = array(
		'name' => $KursID.'SWS_Praktikum',
		'id' => 'SWS_Prakt',
		'value' => $sd_SWS_Praktikum,
		'class' => 'span1'
	    );

	    // get data for Projekt
	    if($SWS_Projekt === '0'){
		    $sd_SWS_Projekt = ' ';
	    } else {
		    $sd_SWS_Projekt = $SWS_Projekt;
	    }
	    $swsDataPro = array(
		'name' => $KursID.'SWS_Projekt',
		'id' => 'SWS_Pro',
		'value' => $sd_SWS_Projekt,
		'class' => 'span1'
	    );

	    // get data for Seminar
	    if($SWS_Seminar === '0'){
		    $sd_SWS_Seminar = ' ';
	    } else {
		    $sd_SWS_Seminar = $SWS_Seminar;
	    }
	    $swsDataSem = array(
		'name' => $KursID.'SWS_Seminar',
		'id' => 'SWS_Sem',
		'value' => $sd_SWS_Seminar,
		'class' => 'span1'
	    );

	    // get data for Seminarunterricht - ?? // TODO check if this field is still needed / in use?
	    if($SWS_SeminarUnterricht === '0'){
		    $sd_SWS_SeminarUnterricht = ' ';
	    } else {
		    $sd_SWS_SeminarUnterricht = $SWS_SeminarUnterricht;
	    }
	    $swsDataSemU = array(
		'name' => $KursID.'SWS_SeminarUnterricht',
		'id' => 'SWS_SemU',
		'value' => $sd_SWS_SeminarUnterricht,
		'class' => 'span1'
	    );
	?>			
	<td>
	    <table>
		<tbody>
		    <tr>
			<td><?php print form_input($swsDataVorl); ?></td>
			<td><?php print form_input($swsDataUeb); ?></td>
			<td><?php print form_input($swsDataPrakt); ?></td>
			<td><?php print form_input($swsDataPro); ?></td>
			<td><?php print form_input($swsDataSem); ?></td>
			<td><?php print form_input($swsDataSemU); ?></td>
		    </tr>
		</tbody>
	    </table>
	</td>

	<!-- Semester -->
	<?php 
	    $dropdown_attributes = 'class = "span1"';
	?>
	<td><?php print form_dropdown($KursID.'Semester', $SemesterDropdown,
		$Semester, $dropdown_attributes)?></td>

	<!-- Beschreibung -->
	<?php 
	    $textareaData = array(
		'name' => $KursID.'Beschreibung',
		'id' => 'Beschreibung',
		'value' => $Beschreibung,
		'rows' => 3,
		'cols' => 5
	    );
	?>
	<td><?php print form_textarea($textareaData); ?></td>

	<!-- Aktion - Button -->
	<?php 
	    if($KursID == 0){ 
		$buttonData = array(
		    'name' => $KursID.'createCourse',
		    'id' => 'create_btn_stdgng',
		    'value' => true,
		    'content' => 'Hinzufügen'
		);
	    } else {
		$buttonData = array(
		    'name' => $KursID.'deleteCourse',
		    'id' => 'delete_btn_stdgng',
		    'data-id' => $KursID,
		    'value' => true,
		    'content' => 'Löschen'
		);
	    }
	?>
	<!-- TODO event for button-click - id vergeben und über AJAX -->
	<td><?php print form_button($buttonData); ?></td>

    </tr>
</div>