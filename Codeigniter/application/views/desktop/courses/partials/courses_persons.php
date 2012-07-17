<!-- overview over relevant persons for this course -->
<h3>Personen:</h3>
<table class="table table-striped table-bordered table-condensed">
    <tbody>
	<tr>
	    <td class="span1 ">
		<span class="label label-info">Dozent</span>
	    </td>
	    <td>
		<?php echo $prof; ?>
	    </td>
	</tr>
	<tr>
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
		    
		    <div id="labings-panel-<?php echo $course_id; ?>" style="display:none;">
			<hr />

			<?php
			    // building checkbox panel with all possible labings for a course
			    if(!$is_tutor){
				$form_attributes = array('id' => 'course-labings-save-button');
				print form_open('kursverwaltung/save_labings_for_course', $form_attributes);
				echo form_submit('save_labings_for_course', 'Speichern');

				// counter for creating 3 collumns
				$counter = 0;
				$number_labings = count($possible_labings);
				$third_labings = ceil($number_labings / 3);
				foreach($possible_labings as $labing){

				    // building three columns
				    if($counter % $third_labings == 0){
					if($counter == 0){
					    echo '<div style="float:left; width:300px;">';
					} else {
					    echo '</div><div style="float:left; width:300px;">';
					}
				    }

				    $checked = FALSE; // init

				    // only if there are labings in variable
				    if(in_array($course_id, array_keys($current_labings))){
					// check if labing is one of the current labings
					foreach($current_labings[$course_id] as $labings){
					    if($labings['BenutzerID'] == $labing->BenutzerID){
						$checked = TRUE;
					    }
					}
				    }

				    echo '<p>';
					// print checkbox
					$cb_name = 'labing-cb-name'.$labing->BenutzerID;
					$cb_id = $course_id.'-'.$labing->BenutzerID;

					// checkbox data
					$cb_data = array(
					    'name' => $cb_name,
					    'id' => $cb_id,
					    'checked' => $checked,
					);
					echo form_checkbox($cb_data);

					// print label
					$label_id = 'labing-label-'.$course_id.'-'.$labing->BenutzerID;
					$label_text = $labing->Vorname.' '.$labing->Nachname;
					$label_attrs = array(
					    'id' => $label_id,
					    'style' => 'display:inline'
					);
					echo form_label($label_text, '', $label_attrs);
				    echo '</p>';

				    if($counter == $third_labings*3){
					echo '</div>';
				    }

				    $counter++;
				} // endforeach
			    } // endif
			?>
		    <?php echo form_close(); ?>
		</div>
	    </td>
	</tr>
	<tr>
	    <td>
		<span class="label label-info">Tutor(en)</span>
	    </td>
	    <td>
		<?php if(!$is_tutor){
		    echo '<a class="btn btn-mini" id="tuts-slider-'.$course_id.'" href="#">+</a>';
		}
		// if there are already - print
		foreach($current_tuts as $tuts){
		    foreach($tuts as $t){
			echo '<span
			    id="added-labings-'.$course_id.'-'.$t['BenutzerID'].'"> '.$t['Vorname'].' '.$t['Nachname'].', </span>';
		    }
		}
		?>
	    </td>
	</tr>
    </tbody>
</table>



<script>

(function() {
    
    var labingsForCourse = {
	
    };
    
    var courseId = "<?php echo $course_id; ?>";
    
    // ids of dom-elements
    var buttonId = '#labings-slider-'+courseId;
    var panelId = '#labings-panel-'+courseId;
    
    // show labings in table when clicked - NOT saved yet!
    $(panelId + ' input').change(function () {
	var self = $(this);
	var id = self.attr("id");
	console.log(self);
	if(self.is(":checked")) {
	    $('<span></span>', {
		text: $('#labing-label-'+id).text()+', ',
		id: 'added-labings-'+id
	    }).appendTo('#current-labings-'+courseId);
	};
	if(!self.is(":checked")){
	    $('#added-labings-'+id).remove();
	};
    });
    
    // labings slide-toggle
    $(buttonId).click(function() { 
	$(panelId).slideToggle('slow', function () {
	    // TODO ??
	});
    });
    
    // converting plus into minus-buttons and back again
    $(buttonId).toggle(
	function() { 
	    $(this).text('-');
	},
	
	function() { 
	    $(this).text('+');
	}
    
    );
    
	
})();



	
</script>