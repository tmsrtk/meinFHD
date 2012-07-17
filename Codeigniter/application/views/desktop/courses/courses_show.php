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
    
    // initialize active tab
    $('.tab-content div:first-child').addClass("active");
    $('#course-details-navi li:first-child').addClass("active");

})();

	
</script>
