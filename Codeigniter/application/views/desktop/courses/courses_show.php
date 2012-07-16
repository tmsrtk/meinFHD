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
	    foreach($course_names_ids as $key => $value){
		echo '<div class="tab-pane" id="'.$value.'-'.$key.'"> ';
		// $course_details contains mapped details on course_ids
		foreach ($course_details[$key] as $c_details) {
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
    
    // update stdgnge-view according to chosen field in dropdown
    $('li').click(function() {
	$('#course-details').html('Daten werden geladen.');
	
	var self = $(this);
	var id = self.attr('id');

	// get data from db
	$.ajax({
	    type: "POST",
	    url: "<?php echo site_url();?>kursverwaltung/ajax_show_course_details/",
	    dataType: 'html',
	    data : {course_id : id},
	    success: function (data){
		$('#course-details').html(data);
	    }
	});
    });


    // initialize active tab
    $('.tab-content div:first-child').addClass("active");
    $('#course-details-navi li:first-child').addClass("active");
    

//	$("#stdgng-list").html('suche...');
//	// ajax
//	if($(this).val() != 0) {
//	    $.get(
//		"<?php // echo site_url();?>admin/ajax_show_courses_of_stdgng/",
//		'stdgng_id='+$(this).val(),
//		function(response) {
//		    // returns view into div
//		    $('#stdgng-list').html(response);
//		});
//	} else {
//		$("#stdgng-list").html('');
//	}

    
	
})();



	
</script>
