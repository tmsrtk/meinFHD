<?php 
    // checkbox data
    $cb_data = array(
	'class' => 'email-checkbox-staff-'.$course_id.' email-checkbox-'.$course_id,
	'value' => '',
	'checked' => 'checked',
    );
?>
<!-- overview over relevant staff for this course -->
<h3>Personen:</h3>
<div> <!-- staff part starts here -->
    <div class="clearfix"> <!-- FIRST line -->
	<div class="span1"> <!-- checkbox FIRST line -->
	    <?php
		$cb_data['name'] = $course_id.'-1';
		$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-1';
		echo form_open('');
		echo form_checkbox($cb_data);
		echo form_close();
	    ?>
	</div> <!-- checkbox FIRST line ends here -->
	<div class="span2"> <!-- label FIRST line -->
		<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-1'; ?>">Dozent</label>
	</div> <!-- label ends here -->
	<div class="span6"> <!-- staff FIRST line -->
		<?php echo $prof; ?>
	</div> <!-- staff FIRST line ends here -->
    </div> <!-- FIRST line ends here -->
    <div class="clearfix"> <!-- SECOND line -->
	<div class="span1"> <!-- checkbox SECOND line -->
	    <?php
		$cb_data['name'] = $course_id.'-2';
		$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-2';
		echo form_open('');
		echo form_checkbox($cb_data);
		echo form_close();
	    ?>
	</div> <!-- checkbox SECOND line ends here -->
	<div class="span2"> <!-- label SECOND line -->
		<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-2'; ?>">Betreuer</label>
	</div> <!-- label ends here -->
	    <div class="span6" id="current-labings-<?php echo $course_id; ?>"><!-- staff SECOND line -->
		<?php 
		    // print button
		    if(!$is_tutor){
			echo '<a class="btn btn-mini" id="labings-slider-'.$course_id.'" href="#">+</a>';
		    }
		    // if there are already - print
		    foreach($current_labings as $labings){
			foreach($labings as $l){
			    echo '<span
				id="added-labings-'.$course_id.'-'.$l['BenutzerID'].'"> '.$l['Vorname'].' '.$l['Nachname'].', </span>';
			}
		    }
		?>
	    </div> <!-- staff SECOND line ends here -->
    </div> <!-- SECOND line ends here -->
    
    <div class="clearfix"><?php echo $labing_panel; ?></div>
    
    <div class="clearfix"> <!-- THIRD line -->
	<div class="span1"> <!-- checkbox THIRD line -->
	    <?php
		$cb_data['name'] = $course_id.'-3';
		$cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-3';
		echo form_open('');
		echo form_checkbox($cb_data);
		echo form_close();
	    ?>
	</div> <!-- checkbox ends here -->
	<div class="span2"> <!-- label THIRD line -->
		<label class="label label-info" id="course-mgt-label-<?php echo $course_id.'-3'; ?>">Tutor(en)</label>
	</div><!-- label ends here -->
	    <div class="span6" id="current-tuts-<?php echo $course_id; ?>"><!-- staff THIRD line -->
		<?php if(!$is_tutor){
		    echo '<a class="btn btn-mini" id="tuts-slider-'.$course_id.'" href="#">+</a>';
		}
		// if there are already - print
		foreach($current_tuts as $tuts){
		    foreach($tuts as $t){
			echo '<span
			    id="added-tuts-'.$course_id.'-'.$t['BenutzerID'].'"> '.$t['Vorname'].' '.$t['Nachname'].', </span>';
		    }
		}
		?>
	    </div><!-- staff ends here -->
    </div> <!-- THIRD line ends here -->
    
    <div class="clearfix"><?php echo $tut_panel; ?></div>

</div> <!-- staff part ends here -->

<script>

(function(){
   
   // get courseId once
    var courseId = "<?php echo $course_id; ?>";
    
    // ids of sliders
    var buttonId = ['#labings-slider-'+courseId, '#tuts-slider-'+courseId];
    var panelId = ['#labings-panel-'+courseId, '#tuts-panel-'+courseId];
    
    // ids/texts of name-spans and cells
    var spanText = ['#labing-label-', '#tut-label-'];
    var spanIdText = ['added-labings-', 'added-tuts-'];
    var spanId = ['#added-labings-', '#added-tuts-'];
    var cellId = ['#current-labings-', '#current-tuts-'];
    
    // saving checkboxes into var
    var cb = $('#labings-panel-'+courseId).children('input');
    
    console.log(cb);

    // activate each panel
    $.each(panelId, function(index, value){
	// show labings in table when clicked - NOT saved yet!
	$(value + ' input').change(function () {
	    var self = $(this);
	    var id = self.attr("id");
	    console.log(self);
	    if(self.is(":checked")) {
		$('<span></span>', {
		    text: $(spanText[index] + id).text()+', ',
		    id: spanIdText[index] + id
		}).appendTo(cellId[index] + courseId);
	    };
	    if(!self.is(":checked")){
		$(spanId[index] + id).remove();
		console.log(spanId[index]+id);
	    };
	});
    });

    
    // activate buttons for both - labings and tuts
    $.each(buttonId, function(index, value){
	// slide-toggle
	$(value).click(function() { 
	    console.log($(this).attr('id'));
	    // !!usage of index: first buttonId >> first Panel || second buttonId >> second Panel
	    $(panelId[index]).slideToggle('slow', function () {
		// 
	    });
	});

	// converting plus into minus-buttons and back again
	$(value).toggle(
	    function() { 
		$(this).text('-');
	    },

	    function() { 
		$(this).text('+');
	    }

	);
    });
   
})();


</script>
