<!-- overview over relevant persons for this course -->
<h3>Personen:</h3>
<table class="table table-striped table-bordered table-condensed">
    <tbody>
	<tr>
	    <td class="span1 ">
		<span class="label label-info">Dozent</span>
	    </td>
	    <td>
		<?php // if($is_tutor == '0'){
//		    echo '<a class="btn btn-mini" href="#">+</a>';
		    // TODO while adding labings to courses (at the moment only whole
		    // course is possible (>> all spkursids for that courseid)
		    // write in labing-table to save data - same with tuts

//		}
		?>
		<?php // print out all profs ?>
		Hier stehen die Dozenten - und zwar ziemlich fix einer - aus Stundenplankurs ^^
	    </td>
	</tr>
	<tr>
	    <td>
		<span class="label label-info">Betreuer</span>
	    </td>
		<td>
		    <div id="current-labings">
			<?php if(!$is_tutor){
			    echo '<a class="btn btn-mini" id="labings-slider" href="#">+</a>';
			}
			?>
		    </div>
		    
			<div id="labings-panel" style="display:none;">
			    <hr />

			    <?php 
				echo form_open();
				echo form_submit('', 'Speichern');

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
			    ?>

			    <p><input 
				    type="checkbox" 
				    name="labing-cb-name<?php echo $labing->BenutzerID; ?>"
				    id="<?php echo $labing->BenutzerID; ?>" />
				<label
				    id="labing-label-<?php echo $labing->BenutzerID; ?>"
				    style="display:inline" />
				    <?php echo $labing->Vorname.' '.$labing->Nachname; ?>
				</label>
			    </p>

			    <?php
    //				$checkbox_attrs = array(
    //				    'id' => 'choose_cb'.$labing->BenutzerID,
    //				    'class' => 'inline'
    //				);
    //				
    //				echo form_checkbox($checkbox_attrs);
    //				$label_text = $labing->Vorname.' '.$labing->Nachname;
    //				echo form_label($label_text, 'choose_cb'.$labing->BenutzerID);?>
				<?php
				    if($counter == $third_labings*3){
					echo '</div>';
				    }

				    $counter++;
				}
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
		    echo '<a class="btn btn-mini" href="#">+</a>';
		}
		?>
		<?php // print out all tuts ?>
		Hier stehen die Tutoren
	    </td>
	</tr>
    </tbody>
</table>



<script>



(function() {
    
    var labingsForCourse = {
	
    };
    
    // show labings in table when clicked - NOT saved yet!
    $('#labings-panel input').change(function () {
	var self = $(this);
	var id = self.attr("id");
	console.log(self);
	if(self.is(":checked")) {
	    $('<span></span>', {
		text: $('#labing-label-'+id).text(),
		id: 'added-labings-'+id
	    }).appendTo('#current-labings');
	};
	if(!self.is(":checked")){
	    $('#added-labings-'+id).remove();
	};
    });
    
    // labings slide-toggle
    $('#labings-slider').click(function() { 
	$('#labings-panel').slideToggle('slow', function () {
	    // TODO ??
	});
    });
    
    // converting plus into minus-buttons and back again
    $('#labings-slider').toggle(
	function() { 
	    $(this).text('-');
	},
	
	function() { 
	    $(this).text('+');
	}
    
    );
    
	
})();



	
</script>