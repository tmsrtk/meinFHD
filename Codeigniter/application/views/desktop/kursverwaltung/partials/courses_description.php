<h3>Beschreibung </h3>
<p>Hier k&ouml;nnen Sie eine (kurze) Beschreibung zu dem Kurs und seinen Inhalten hinterlegen. Um Links zu
    Dokumenten oder Websites aufzuf&uuml;hren k&ouml;nnen HTML-Tags verwendet werden.
</p>
<div>
	<?php
		/**
		 * If tutor-flag is set, a static description is printed.
		 * Otherwise: a simple textarea with the saved description will be displayed
		 */
	
		// prepare textarea
		$course_description_textarea_data = array(
			'name' => $course_id.'_description',
			'id' => 'input-course-description-' . $course_id,
			'class' => 'input-xlarge span',
			'value' => $course_description,
			'rows' => 7,
			'cols' => 40,
		);

        $course_description_textarea_readonly_data = array(
            'name' => $course_id.'_description',
            'id' => 'input-course-description-' . $course_id,
            'class' => 'input-xlarge span',
            'value' => $course_description,
            'rows' => 7,
            'cols' => 40,
            'readonly' => 'readonly'
        );

        $submit_data_save_description = array(
            'name' => $course_id,
            'value' => 'Beschreibung speichern',
            'id' => 'save-description'.$course_id,
            'class' => 'btn btn-warning',
        );
		
		if(!$is_tutor){
			echo form_textarea($course_description_textarea_data);
            echo '<div style="text-align: right">';
            echo form_submit($submit_data_save_description);
            echo '</div>';

        }
        else {
            echo form_textarea($course_description_textarea_readonly_data);
        }
    ?>
</div>