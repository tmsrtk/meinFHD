<h3>Beschreibung </h3>
<div>
	<?php
		/**
		 * If tutor-flag is set, a static description is printed.
		 * Otherwise: simple textarea with description from db
		 */
	
		// prepare textarea
		$course_description_textarea_data = array(
			'name' => $course_id.'_description',
			'id' => 'input-course-description',
			'class' => 'input-xlarge span',
			'value' => $course_description,
			'rows' => 7,
			'cols' => 40
		);
		
		// print
		if(!$is_tutor){
			echo form_textarea($course_description_textarea_data); 
		} else {
			if($course_description){
				echo $course_description;
			} else {
				echo 'Keine Beschreibung vorhanden.';
			}
		}
	?>
</div>