<?php 
	/**
	 * Partial that provides an overview over relevant staff for a course and the possibility to assign new
     * labings/tuts. Furthermore there are checkboxes which belong to the email-'system' of the page
	 * 
	 * View consists of 3 lines:
	 * 1. profs - static: only cb, label and name of prof 
	 * 2. labings - dynamic for non-tuts:
	 * - profs & labings see staff PLUS button to assign new staff to course
	 * - tuts only see static view
	 * 3. tutors - dynamic for non-tuts:
	 * - same as labings
	 * 
	 * After 2. and 3. line there is a panel added, which contains all potential staff for that role.
	 * Panel only consists no data, if view is build for tutor!
	 */

    // form / checkbox setup
    $cb_data = array(
		'class' => 'email-checkbox-staff-'.$course_id.' email-checkbox-'.$course_id,
		'value' => '',
		'checked' => 'checked',
    );
?>
<div class="span1"></div>
<div class="span2"><h3>Personen:</h3></div>
<div class="span9"></div>

<div class="clearfix">
    <div class="span1 bold">Email an</div>
    <div class="span10"></div>
    <div class="span1"></div>
</div>

<div>
    <div class="clearfix">
		<div class="span1" style="text-align: center;">
			<?php
				$cb_data['name'] = $course_id.'-1';
				$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-1';
				echo form_open('');
				echo form_checkbox($cb_data);
				echo form_close();
			?>
		</div>
		<div class="span2">
			<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-1'; ?>">Dozent</label>
		</div>
		<div class="span9">
			<?php echo $prof; ?>
		</div> 
    </div> <!-- FIRST line ends here -->
	
    <div class="clearfix">
		<div class="span1" style="text-align: center;">
			<?php
				$cb_data['name'] = $course_id.'-2';
				$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-2';
				echo form_open('');
				echo form_checkbox($cb_data);
				echo form_close();
			?>
		</div>
		<div class="span2">
			<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-2'; ?>">Betreuer</label>
		</div>
	    <div class="span9" id="current-labings-<?php echo $course_id; ?>"><!-- staff SECOND line -->
		<?php 
		    // print button - only for non-tutors
		    if(!$is_tutor){
				echo '<a class="btn btn-mini" id="labings-slider-'.$course_id.'" href="#"><i class="icon-pencil"></i></a>&nbsp;';
		    }
		    // print all current labings
		    foreach($current_labings as $labings){
				foreach($labings as $l){
					echo '<span id="added-labings-'.$course_id.'-'.$l['BenutzerID'].'"> '.$l['Vorname'].' '.$l['Nachname'].', </span>';
				}
		    }
		?>
	    </div>
    </div> <!-- SECOND line ends here -->
    
    <div class="clearfix"><?php echo $labing_panel; ?></div>
    
    <div class="clearfix">
		<div class="span1" style="text-align: center">
			<?php
				$cb_data['name'] = $course_id.'-3';
				$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-3';
				echo form_open('');
				echo form_checkbox($cb_data);
				echo form_close();
			?>
		</div>
		<div class="span2">
			<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-3'; ?>">Tutor(en)</label>
		</div>
	    <div class="span9" id="current-tuts-<?php echo $course_id; ?>">
		<?php if(!$is_tutor){
		    echo '<a class="btn btn-mini" id="tuts-slider-'.$course_id.'" href="#"><i class="icon-pencil"></i></a>&nbsp;';
			}
			// print all current tutors
			foreach($current_tuts as $tuts){
				foreach($tuts as $t){
					echo '<span id="added-tuts-'.$course_id.'-'.$t['BenutzerID'].'"> '.$t['Vorname'].' '.$t['Nachname'].', </span>';
				}
			}
		?>
	    </div>
    </div> <!-- THIRD line ends here -->
    <div class="clearfix"><?php echo $tut_panel; ?></div>
</div> <!-- staff part ends here -->