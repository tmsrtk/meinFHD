<?php 
    
?>

<div>
    
    <div class="well">
	<pre>
	    <?php print_r($global_data); ?>
	</pre>
	
    </div>
 
    
    <div class="well">
	<!-- Show relevant persons for this course -->
	<?php echo $persons; ?>
    </div>


    <!-- View that shows the course-details + possiblity to changes data
    - only visible for profs -->
    <div class="well">
	<h3>Kursdetails:</h3>
	<!-- Show course details -->
	<?php echo $lecture; ?>
    </div>


    <!-- View that shows lab-details - possiblity to change data depends on role
    >> (prof & betreuer may change; tutor must not) -->
    <div class="well">
	<h3>Praktikum / Übung / Seminar:</h3>
	
	<?php 
	    foreach($lab as $l){
		echo $l;
	    }
	
	?>
	
    </div>
    
    
    <!-- View that shows tut-details + possibility to change data
    - possibility to change tutor depends on role -->
    <div class="well">
	<h3>Tutorium:</h3>
	
	<?php echo $tut; ?>
	
    </div>

    
</div>
