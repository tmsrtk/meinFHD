<div id="stdgng-courses" class="span12">
    
	
    <?php 
	$new_course_form_attributes = array('id' => 'stdgng-new-course-save-button');
	print form_open('admin/validate_new_degree_program_course', $new_course_form_attributes); //save_stdgng_course_changes
    ?>
	<table class="table" id="degree-program-table">
	    <thead id="dp-table-head">
		<?php echo $course_tablehead; ?>
	    </thead>
	    
	    <tbody id="dp-table-body-first">
		<!-- first row as own table to insert new course -->
		<?php echo $new_course; ?>
	    </tbody>
	    
	</table>
    
    <?php
	// close form
	print form_close();
    ?>
	
    <?php 
	$change_data_form_attributes = array('id' => 'stdgng-save-button');
	print form_open('admin/validate_degree_program_course_changes', $change_data_form_attributes); //save_stdgng_course_changes
    ?>
	
<!--	<table class="table">-->
	<?php // echo $course_tablehead; ?>
		<table class="table" id="degree-program-table">
			<tbody id="dp-table-body-second">
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

<script>

(function() {

//    var table = $('#degree-program-table');
//    var thead = $('#dp-table-head');
//    var tbodyFirst = $('#dp-table-body-first');
////    console.log('offset: '+table.offset().top);
////    console.log('pos: '+table.position().top);
//    
//    $(window).scroll(function(){
//	var windowTop = $(window).scrollTop();
//	var tableTop = $(table).offset().top;
////	console.log(windowTop);
////	console.log(tableTop);
//	if(windowTop > tableTop){
//	    $(thead).css('position', 'absolute').css('top', windowTop);
//	} else {
//	    $(thead).css('position', 'absolute').css('top', windowTop);
//	}
//	
//    });
    
})(); 
    
</script>