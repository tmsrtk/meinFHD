<?php

    $roles = array(
	'4' => 'Tutor', // role_id => role_name
	'5' => 'Student'
    ); // TODO: get from controller
    
    $role_timetables = array(
	'4' => '<div>Tutor-timetable-data</div>', // role_id => timetable for tutors
	'5' => '<div>Student-timetable-data</div>' // timetable for students
    ); // TODO: get from controller
    
?>

<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<div class="well">
    
    <!-- tab-navigation -->
    <ul class="nav nav-tabs" id="tt-tab-navi">
	<?php 
	    // print navigation depending on roles this user has
	    foreach ($roles as $r_id => $r_name) {
		echo '<li id="tt-tab-'.$r_name.'">';
		echo '<a href="#'.$r_name.'-tt" data-toggle="tab">'.$r_name.'</a>';
		echo '</li>';
	    }
	?>
    </ul>
    
    
    <!-- tab-content -->
    <div class="tab-content">
	<?php 
	    // print div for each timetable
	    foreach($roles as $r_id => $r_name) {
		echo '<div class="tab-pane" id="'.$r_name.'-tt"> ';

	        // print role-specific timetable
		print $role_timetables[$r_id];
		
		// implementation-detail
		// 
		// if there are : 
		// role-specific buttons or submit-forms can be build directly inside this loop
		// otherwise (passed via controller) they got to be stuffed with 
		// the right role-id to provide role-specific function
		

		
		
		echo '</div>'; // closing tab-pane
	    } // endforeach
	?>    
    </div>
</div>
<?php endblock(); ?>
<?php end_extend(); ?>
