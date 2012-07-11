<div>
    
    <?php 
	foreach ($course_data as $key => $value) :
    ?>
	
	<div class="well">
	    <!-- Print Headline -->
	    <?php 
//		echo $headlines[$offset];
//		echo 'Ueberschrift';
	    ?>
	
	    <!-- Print course-data -->
	    <?php
		if(!is_array($value)){
		    print($value);
		} else {
		    foreach($value as $v){
			print($v);
		    }
		}
	    ?>
	    
	</div>
	
    <?php 
	endforeach;
    ?>


</div>
