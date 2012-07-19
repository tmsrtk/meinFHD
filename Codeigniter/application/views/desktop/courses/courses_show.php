<?php 

    // prepare array with ids for json
    $course_ids_jq = array();
    // associative arrays can be handled easier in jquery
    $course_ids = array_keys($course_details);
    foreach ($course_ids as $id) {
	$course_ids_jq['KursID'.$id] = $id;
    }
    
?>
<div class="well">
    
    <ul class="nav nav-tabs" id="course-details-navi">
	<?php 
	    // print navigation depending on courses this user has
	    foreach ($course_names_ids as $key=> $value) {
		echo '<li id="course-tab-'.$key.'">';
		echo '<a href="#'.$value.'-'.$key.'" data-toggle="tab">'.$value.'</a>';
		echo '</li>';
	    }
	?>
    </ul>
    
    
	
    
    <div class="tab-content">
	<?php 
	    // print div for each course
	    foreach($course_names_ids as $c_id => $c_name){
		// print email-checkbox
		
		// checkbox data - has to be generate each time because of course_id!
		$cb_data = array(
		    'name' => '',
		    'class' => 'email-checkbox',
		    'id' => 'email-checkbox-all-id-'.$c_id,
		    'value' => '',
		    'checked' => 'checked',
		);
		
		echo '<div id="staff-send-email">';
		echo form_open(); 
		echo '<div class="span1">';
		echo form_checkbox($cb_data);
		echo '</div>';
		echo form_submit('', 'Email senden');
		echo form_close();
		echo '</div>';
		echo '</div>';

		echo '<div class="tab-pane" id="'.$c_name.'-'.$c_id.'"> ';
		// $course_details contains mapped details on course_ids
		foreach ($course_details[$c_id] as $c_details) {
		    // necessary because pr, übung, sem come withing nested array
		    if(!is_array($c_details)){
			print($c_details);
		    } else {
			foreach($c_details as $v){
			    print($v);
			}
		    }
		}
		echo '</div>';
	    }
	?>    
    </div>
    
</div>


<script>

(function() {
    
    // initialize active tab
    $('.tab-content div:first-child').addClass("active");
    $('#course-details-navi li:first-child').addClass("active");
    
    var courseIdsInView = <?php echo json_encode($course_ids_jq); ?>;
    
    // get first checkbox
    var mailCheckboxAll = 'email-checkbox-all-id-';
    var mailCheckboxStaffIds = 'email-checkbox-staff-id-';
    
    // get all staff-checkboxes
    var mailCheckboxStaffElements = $('.email-checkbox-staff');
    // get all course-checkboxes
    var mailCheckboxCourse = $('#email-checkbox-course');
    
    console.log(mailCheckboxStaffElements);
    
    // run through all ids and assign function
    $.each(courseIdsInView, function(indexAll, courseId){
	$('#'+mailCheckboxAll+courseId).change(function(){
	    var cbAll = $(this);
	    var cbsToChange = mailCheckboxStaffIds+courseId;
	    // run through all
	    $.each(mailCheckboxStaffElements, function(indexCb, valueCb){
		var cbStaff = $(this);
		var idStaffCb = cbStaff.attr('id');
		// get only checkboxes for that course
		if(idStaffCb == cbsToChange){
		    // set un/checked
		    if(cbAll.is(':checked')){
			console.log(idStaffCb);
			$('#'+idStaffCb).attr('checked', true);
		    } else {
			$('#'+idStaffCb).attr('checked', false);
		    }
		}
	    });
	    
	});
    });
    
    // run through both and combine with first checkbox
//    mailCheckboxStaff.
//	if(mailCheckboxAll.is(":checked")){
//	    
//	}
//    });

})();

	
</script>
