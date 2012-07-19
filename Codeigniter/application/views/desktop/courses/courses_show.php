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
		echo '<div class="tab-pane" id="'.$c_name.'-'.$c_id.'"> ';

		// print email-checkbox
		
		// checkbox data - has to be generate each time because of course_id!
		$cb_data = array(
		    'name' => '',
		    'class' => 'email-checkbox',
		    'id' => 'email-checkbox-all-id-'.$c_id,
		    'value' => '',
		    'checked' => 'checked',
		);
		
		$submit_data = array(
		    'name' => $c_id,
		    'value' => 'Email senden',
		    'id' => 'send-email-to-cb-'.$c_id
		);
		
		echo '<div id="staff-send-email">';
		echo form_open(); 
		echo '<div class="span1">';
		echo form_checkbox($cb_data);
		echo '</div>';
		echo form_submit($submit_data);
		echo form_close();
		echo '</div>';
		
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
    
    // run through all ids and assign functions
    // - un/check all boxes if overall cb changes
    // - uncheck overall cb if ONE or more of the single cb is NOT checked
    // - check overall cb if all single cb are checked
    // - click on email-button
    $.each(courseIdsInView, function(indexAll, courseId){

	// save checkboxes for that course to array
	var checkboxesOnSite = $('.email-checkbox-'+courseId);
	var overallCbId = '#email-checkbox-all-id-'+courseId;
	// save id for email-button
	var sendEmailButtonId = '#send-email-to-cb-'+courseId;
	
	// find out how many checkboxes there are on course-site
	var numberCbs = 0;
	$.each(checkboxesOnSite, function(index, value){
	    numberCbs++;
	});
	
	// change of overall cb - uncheck >> uncheck all | check >> check all
	$(overallCbId).change(function(){
	    var cbAll = $(this);
	    // run through all elements and set un/checked
	    $.each(checkboxesOnSite, function(i, v){
		var cbSelf = $(this);
		var cbId = cbSelf.attr('id');
		if(cbAll.is(':checked')){
		    $('#'+cbId).attr('checked', true);
		} else {
		    $('#'+cbId).attr('checked', false);
		}
		console.log(cbId);
	    });
	});
	
	// change of any of the single checkboxes - uncheck one >> uncheck overall | check all >> check overall
	$.each(checkboxesOnSite, function(index, value){
	    // init counter to detect if there are un/checked checkboxes
	    var self = $(this);
	    var cbId = self.attr('id');

	    // if checkbox changes
	    $('#'+cbId).change(function(){
		var counter = 0;
		
		// count unchecked checkboxes
		$.each(checkboxesOnSite, function(i, v){
		    if($(this).is(':checked')){
			counter++;
		    }
		});
		// if all checkboxes are checked >> check overall checkbox
		if(counter == numberCbs){
		    $(overallCbId).attr('checked', true);
		// otherwise uncheck overall checkbox
		} else {
		    $(overallCbId).attr('checked', false);
		}
	    }); // end checkbox-change
	}); // end run through checkboxes
	
	// get staff and course checkboxes separatly
	// and put into array to run through easier
	var staffCbElements = $('.email-checkbox-staff-'+courseId);
	var courseCbElements = $('.email-checkbox-courses-'+courseId);
	var bothCbElements = [staffCbElements, courseCbElements];
	
	// init arrays to save recipients
	var staffRecipients = new Array();
	var courseRecipients = new Array();
	
	// click on email-button
	$(sendEmailButtonId).click(function(){
	    // detect chosen checkboxes - 
	    $.each(bothCbElements, function(index, checkboxes){
		$.each(checkboxes, function(i, v){
		    var self = $(this);
		    var cbName = self.attr('name');
		    if(self.is(':checked')){
			// differ between staff and courses
			if(index == 0){
			    staffRecipients.push(cbName);
			} else if(index == 1) {
			    courseRecipients.push(cbName);
			}
		    }
		});
	    });
	    alert(
		'TODO \n\
		Emailversand an Personen: ' + staffRecipients + '\n\
		Emailversand an Teilnehmer: ' + courseRecipients
	    );
	});
	
	
    }); // end 

})();

	
</script>
