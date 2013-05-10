<?php
    // general form setup before content
    $data_kursname = array(
        'name' => $KursID . 'Kursname',
        'id' => $KursID . '_kursname',
        'value' => $Kursname
    );

    $data_kursname_kurz = array(
        'name' => $KursID . 'kurs_kurz',
        'id' => $KursID . '_kursname_kurz',
        'value' => $kurs_kurz,
        'class' => 'span'
    );

    $data_creditpoints = array(
        'name' => $KursID . 'Creditpoints',
        'id' => $KursID . '_ceditpoints',
        'value' => $Creditpoints,
        'class' => 'span'
    );

    // data for the different lesson types

    // prepare data for vorlesung
    if($SWS_Vorlesung === '0'){
        $sd_SWS_Vorlesung = '';
    } else {
        $sd_SWS_Vorlesung = $SWS_Vorlesung;
    }

    $data_vorlesung_sws = array(
        'name' => $KursID . 'SWS_Vorlesung',
        'id' => $KursID . '_sws_vorlesung',
        'value' => $sd_SWS_Vorlesung,
        'class' => 'span',
        'placeholder' => 'V'
    );

    // prepare data for uebung
    if($SWS_Uebung === '0'){
        $sd_SWS_Uebung = '';
    } else {
        $sd_SWS_Uebung = $SWS_Uebung;
    }

    $data_uebung_sws= array(
        'name' => $KursID . 'SWS_Uebung',
        'id' => $KursID . '_sws_uebung',
        'value' => $sd_SWS_Uebung,
        'class' => 'span',
        'placeholder' => 'UE'
    );

    // prepare data for praktikum
    if($SWS_Praktikum === '0'){
        $sd_SWS_Praktikum = '';
    } else {
        $sd_SWS_Praktikum = $SWS_Praktikum;
    }

    $data_praktikum_sws = array(
        'name' => $KursID . 'SWS_Praktikum',
        'id' => $KursID . '_sws_praktikum',
        'value' => $sd_SWS_Praktikum,
        'class' => 'span',
        'placeholder' => 'P'
    );

    // prepare data for projekt
    if($SWS_Projekt === '0'){
        $sd_SWS_Projekt = '';
    } else {
        $sd_SWS_Projekt = $SWS_Projekt;
    }

    $data_projekt_sws = array(
        'name' => $KursID . 'SWS_Projekt',
        'id' => $KursID . '_sws_projekt',
        'value' => $sd_SWS_Projekt,
        'class' => 'span',
        'placeholder' => 'Pr',
    );

    // prepare data for seminar
    if($SWS_Seminar === '0'){
        $sd_SWS_Seminar = '';
    } else {
        $sd_SWS_Seminar = $SWS_Seminar;
    }

    $data_seminar_sws = array(
        'name' => $KursID . 'SWS_Seminar',
        'id' => $KursID . '_sws_seminar',
        'value' => $sd_SWS_Seminar,
        'class' => 'span',
        'placeholder' => 'S'
    );

    // prepare data for seminarunterricht
    if($SWS_SeminarUnterricht === '0'){
        $sd_SWS_SeminarUnterricht = '';
    } else {
        $sd_SWS_SeminarUnterricht = $SWS_SeminarUnterricht;
    }

    $data_seminar_unterricht_sws = array(
        'name' => $KursID . 'SWS_SeminarUnterricht',
        'id' => $KursID . '_sws_seminar_unterricht',
        'value' => $sd_SWS_SeminarUnterricht,
        'placeholder' => 'SU',
        'class' => 'span'
    );

    $dropdown_attributes = 'class="span" id ="' . $KursID . '_semester"';

    // prepare data for the different exam types

    // exam-type 1
    $data_examtype_1 = array(
        'name' => $KursID . 'ext_1',
        'value' => '1',
        'id' => $KursID . '_ext_1',
        'checked' => $pruefungstyp_1,
    );

    // exam-type 2
    $data_examtype_2 = array(
        'name' => $KursID . 'ext_2',
        'value' => '1',
        'id' => $KursID . '_ext_2',
        'checked' => $pruefungstyp_2,
    );

    // exam-type 3
    $data_examtype_3 = array(
        'name' => $KursID . 'ext_3',
        'value' => '1',
        'id' => $KursID . '_ext_3',
        'checked' => $pruefungstyp_3,
    );

    // exam-type 4
    $data_examtype_4 = array(
        'name' => $KursID . 'ext_4',
        'value' => '1',
        'id' => $KursID . '_ext_4',
        'checked' => $pruefungstyp_4,
    );

    // exam-type 5
    $data_examtype_5 = array(
        'name' => $KursID . 'ext_5',
        'value' => '1',
        'id' => $KursID . '_ext_5',
        'checked' => $pruefungstyp_5,
    );

    // exam-type 6
    $data_examtype_6 = array(
        'name' => $KursID . 'ext_6',
        'value' => '1',
        'id' => $KursID . '_ext_6',
        'checked' => $pruefungstyp_6,
    );

    // exam-type 7
    $data_examtype_7 = array(
        'name' => $KursID . 'ext_7',
        'value' => '1',
        'id' => $KursID . '_ext_7',
        'checked' => $pruefungstyp_7,
    );

    // exam-type 8
    $data_examtype_8 = array(
        'name' => $KursID . 'ext_8',
        'value' => '1',
        'id' => $KursID . '_ext_8',
        'checked' => $pruefungstyp_8,
    );

    $data_description = array(
        'name' => $KursID . 'Beschreibung',
        'id' => $KursID . '_beschreibung',
        'value' => $Beschreibung,
        'rows' => 3,
        'class' => 'span'
    );

    $data_btn_delete = array(
        'name' => $Kursname,
        'class' => 'btn btn-danger span',
        'data-id' => $KursID . '_' . $dp_id,
        'value' => true,
        'content' => 'L&ouml;schen',
        'id' => 'delete_degree_program_course_btn_' . $KursID
    );

?>
<!--  single row in degree-program-list-table  -->
<tr style="clear:both">
    <td>
        <?php echo $KursID; ?>
    </td>
    <td>
        <?php echo form_input($data_kursname); ?>
    </td>
    <td>
        <?php echo form_input($data_kursname_kurz); ?>
    </td>
    <td>
        <?php echo form_input($data_creditpoints); ?>
    </td>
    <td>
		<div class="span2"><?php echo form_input($data_vorlesung_sws); ?></div>
		<div class="span2"><?php echo form_input($data_uebung_sws); ?></div>
		<div class="span2"><?php echo form_input($data_praktikum_sws); ?></div>
		<div class="span2"><?php echo form_input($data_projekt_sws); ?></div>
		<div class="span2"><?php echo form_input($data_seminar_sws); ?></div>
		<div class="span2"><?php echo form_input($data_seminar_unterricht_sws); ?></div>
    </td>

    <td>
	<?php 
	    if($Semester !== '0'){
			echo form_dropdown($KursID . '_semester', $SemesterDropdown, ($Semester-1), $dropdown_attributes);
	    } else {
			echo form_dropdown($KursID . '_semester', $SemesterDropdown, 0, $dropdown_attributes);
	    }
	?>
    </td>
    <td style="white-space: nowrap;">
	<?php echo form_checkbox($data_examtype_1); ?>
	<?php echo form_checkbox($data_examtype_2); ?>
	<?php echo form_checkbox($data_examtype_3); ?>
	<?php echo form_checkbox($data_examtype_4); ?>
	<?php echo form_checkbox($data_examtype_5); ?>
	<?php echo form_checkbox($data_examtype_6); ?>
	<?php echo form_checkbox($data_examtype_7); ?>
	<?php echo form_checkbox($data_examtype_8); ?>
    </td>
    <td>
        <?php echo form_textarea($data_description); ?>
    </td>
    <td>
        <?php print form_button($data_btn_delete); ?>
    </td>
</tr>