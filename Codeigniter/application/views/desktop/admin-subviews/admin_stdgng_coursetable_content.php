<div id="stdgng-courses" class="span12">
    
	
    <?php 
	$new_course_form_attributes = array('id' => 'stdgng-new-course-save-button');
	print form_open('admin/validate_new_stdgng_course', $new_course_form_attributes); //save_stdgng_course_changes
    ?>
	<table class="table">
	    <?php echo $course_tablehead; ?>
	    
	    <!-- first row as own table to insert new course -->
	    <?php echo $new_course; ?>
	</table>
    
    <?php
	// close form
	print form_close();
    ?>
	
    <?php 
	$change_data_form_attributes = array('id' => 'stdgng-save-button');
	print form_open('admin/validate_stdgng_course_changes', $change_data_form_attributes); //save_stdgng_course_changes
    ?>
	
	<table class="table">
	<?php echo $course_tablehead; ?>
	    <tbody>
		<?php 
		    if($stdgng_course_rows){
			foreach($stdgng_course_rows as $row){
			    echo '<tr>'.$row.'</tr>';
			}
		    }
		?>

	    </tbody>
	</table>

    <?php
	// hidden field to transmit the stdgng-id
	print form_hidden('stdgng_id', $stdgng_id);

	// save-button
	$btn_attributes = 'id = #stdgng-course-details-save-button class = "btn-warning"';
	print form_submit('save_stdgng_course_changes', 'Änderungen speichern', $btn_attributes);
	// close form
	print form_close();

    ?>

</div>