<?php 
    // checkbox data
    $cb_data = array(
	'class' => 'email-checkbox-'.$course_id,
//	'class' => 'email-checkbox-staff email-checkbox-staff-courseid-'.$course_id,
	'value' => '',
	'checked' => 'checked',
    );
?>
<!-- overview over relevant staff for this course -->
<h3>Personen:</h3>
<table class="table table-striped table-bordered table-condensed">
    <tbody>
	<tr>
	    <td class="span1">
		<?php
		    $cb_data['name'] = 'email-checkbox-'.$course_id.'-1';
		    $cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-1';
		    echo form_open();
		    echo form_checkbox($cb_data);
		    echo form_close();
		?>
	    </td>
	    <td class="span1 ">
		<span class="label label-info">Dozent</span>
	    </td>
	    <td>
		<?php echo $prof; ?>
	    </td>
	</tr>
	<tr>
	    <td>
		<?php
		    $cb_data['name'] = 'email-checkbox-'.$course_id.'-2';
		    $cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-2';
		    echo form_open();
		    echo form_checkbox($cb_data);
		    echo form_close();
		?>
	    </td>
	    <td>
		<span class="label label-info">Betreuer</span>
	    </td>
	    <td>
		<div id="current-labings-<?php echo $course_id; ?>">
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
		</div>
		<?php echo $labing_panel; ?>
	    </td>
	</tr>
	<tr>
	    <td class="span1">
		<?php
		    $cb_data['name'] = 'email-checkbox-'.$course_id.'-3';
		    $cb_data['id'] = 'email-checkbox-staff-id-'.$course_id.'-3';
		    echo form_open();
		    echo form_checkbox($cb_data);
		    echo form_close();
		?>
	    </td>
	    <td>
		<span class="label label-info">Tutor(en)</span>
	    </td>
	    <td>
		<div id="current-tuts-<?php echo $course_id; ?>">
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
		</div>
		<?php echo $tut_panel; ?>
	    </td>
	</tr>
    </tbody>
</table>



<script>

(function() {
    
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