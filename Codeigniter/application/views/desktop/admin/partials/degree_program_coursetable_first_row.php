<!--  first row in degree-program-list-table  -->
<?php
    // general form setup before content

    $data_coursename = array(
        'name' => 'new-course-coursename',
        'id' => 'new-course-coursename',
        'value' => set_value('new-course-coursename'),
        'placeholder' => 'Kursname'
    );

    $data_course_abbreviation = array(
        'name' => 'new-course-abbreviation',
        'id' => 'new-course-abbreviation',
        'value' => set_value('new-course-abbreviation'),
        'class' => 'span',
        'placeholder' => 'Abk.'
    );

    $data_creditpoints = array(
        'name' => 'new-course-cp',
        'id' => 'new-course-cp',
        'value' => set_value('new-course-cp'),
        'class' => 'span',
        'placeholder' => 'CP'
    );

    // form setup for the different lesson types
    $data_vorlesung = array(
        'name' => 'new-course-sws-vorl',
        'id' => 'new-course-sws-vorl',
        'value' => set_value('new-course-sws-vorl'),
        'class' => 'span',
        'placeholder' => 'V'
    );

    $data_uebung = array(
        'name' => 'new-course-sws-ueb',
        'id' => 'new-course-sws-ueb',
        'value' => set_value('new-course-sws-ueb'),
        'class' => 'span',
        'placeholder' => 'UE'
    );

    $data_praktikum = array(
        'name' => 'new-course-sws-prakt',
        'id' => 'new-course-sws-prakt',
        'value' => set_value('new-course-sws-prakt'),
        'class' => 'span',
        'placeholder' => 'P'
    );

    $data_projekt = array(
        'name' => 'new-course-sws-pro',
        'id' => 'new-course-sws-pro',
        'value' => set_value('new-course-sws-pro'),
        'class' => 'span',
        'placeholder' => 'Pr'
    );

    $data_seminar = array(
        'name' => 'new-course-sws-seminar',
        'id' => 'new-course-sws-seminar',
        'value' => set_value('new-course-sws-seminar'),
        'class' => 'span',
        'placeholder' => 'S'
    );

    $data_seminar_lesson = array(
        'name' => 'new-course-sws-seminar-u',
        'id' => 'new-course-sws-seminar-u',
        'value' => set_value('new-course-sws-seminar-u'),
        'class' => 'span',
        'placeholder' => 'SU'
    );

    $dropdown_attributes = 'id="new-course-semester" class = "span"';

    // form data for the different exam types
    // exam-type 1
    $data_examtype_1 = array(
        'name' => 'new-course-ext-1',
        'value' => '1',
        'id' => 'new-course-ext-1'
    );
    // exam-type 2
    $data_examtype_2 = array(
        'name' => 'new-course-ext-2',
        'value' => '1',
        'id' => 'new-course-ext-2'
    );
    // exam-type 3
    $data_examtype_3 = array(
        'name' => 'new-course-ext-3',
        'value' => '1',
        'id' => 'new-course-ext-3'
    );
    // exam-type 4
    $data_examtype_4 = array(
        'name' => 'new-course-ext-4',
        'value' => '1',
        'id' => 'new-course-ext-4'
    );
    // exam-type 5
    $data_examtype_5 = array(
        'name' => 'new-course-ext-5',
        'value' => '1',
        'id' => 'new-course-ext-5'
    );
    // exam-type 6
    $data_examtype_6 = array(
        'name' => 'new-course-ext-6',
        'value' => '1',
        'id' => 'new-course-ext-6'
    );
    // exam-type 7
    $data_examtype_7 = array(
        'name' => 'new-course-ext-7',
        'value' => '1',
        'id' => 'new-course-ext-7'
    );
    // exam-type 8
    $data_examtype_8 = array(
        'name' => 'new-course-ext-8',
        'value' => '1',
        'id' => 'new-course-ext-8'
    );

    $data_description = array(
        'name' => 'new-course-description',
        'id' => 'new-course-description',
        'value' => set_value('new-course-description'),
        'rows' => 3,
        'class' => 'span'
    );

    $data_add_course_btn = array(
        'class' => 'btn btn-warning span',
        'name' => 'create-new-course',
        'id' => 'create-new-course',
        'data-id' => $dp_id,
        'content' => 'Kurs hinzuf&uuml;gen'
    );

?>
<tr>
    <td>+</td>
    <td>
        <?php print form_input($data_coursename); ?>
    </td>
    <td>
        <?php print form_input($data_course_abbreviation); ?>
    </td>
     <td>
         <?php print form_input($data_creditpoints); ?>
     </td>

    <td>
		<div class="span2">
            <?php print form_input($data_vorlesung); ?>
        </div>
		<div class="span2">
            <?php print form_input($data_uebung); ?>
        </div>
		<div class="span2">
            <?php print form_input($data_praktikum); ?>
        </div>
		<div class="span2">
            <?php print form_input($data_projekt); ?>
        </div>
		<div class="span2">
            <?php print form_input($data_seminar); ?>
        </div>
		<div class="span2">
            <?php print form_input($data_seminar_lesson); ?>
        </div>
    </td>
    <td>
	    <?php print form_dropdown('new-course-semester', $semester_dropdown, 0, $dropdown_attributes); ?>
    </td>
    <td style="white-space: nowrap;">
        <?php print form_checkbox($data_examtype_1); ?>
        <?php print form_checkbox($data_examtype_2); ?>
        <?php print form_checkbox($data_examtype_3); ?>
        <?php print form_checkbox($data_examtype_4); ?>
        <?php print form_checkbox($data_examtype_5); ?>
        <?php print form_checkbox($data_examtype_6); ?>
        <?php print form_checkbox($data_examtype_7); ?>
        <?php print form_checkbox($data_examtype_8); ?>
    </td>
    <td>
        <?php print form_textarea($data_description); ?>
    </td>
    <td>
        <?php print form_button($data_add_course_btn); ?>
    </td>
</tr>