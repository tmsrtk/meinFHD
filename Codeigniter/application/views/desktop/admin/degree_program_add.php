<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	    <div class="span2"></div>
	    <div class="span8">
		    <div class="well well-small">
<?php endblock(); ?>

<?php
// general form setup

#textarea
$stdgng_details_textarea_data = array(
	'name' => 'Beschreibung',
	'id' => 'input-stdgng-beschreibung',
	'class' => 'input-xxxlarge',
	'value' => '',
	'rows' => 7,
	'cols' => 40
);
# submit button
$btn_attributes = 'class = "btn-warning input-xxxlarge"';

?>
<?php startblock('content'); # additional markup before content ?>
	    <div class="row-fluid">
		    <h2>Studiengang anlegen</h2>
	    </div>
	    <hr>
	    <?php echo validation_errors(); ?>
	    <div class="row-fluid">
	    <?php echo form_open('admin/validate_new_created_stdgng'); ?>
		    <div id="stdgng-details">
			    <div id="stdgng-details-1" class="span6">
				    <?php 
					    foreach ($all_degree_programs[0] as $key => $value){
						    if( $key == 'StudiengangName' || 
							    $key == 'StudiengangAbkuerzung' || 
							    $key == 'Pruefungsordnung' || 
							    $key == 'Regelsemester' || 
							    $key == 'Creditpoints') {

								    // create empty fields - new course will be created
								    $inputFieldData = array(
									    'name' => $key,
									    'id' => $key,
									    'class' => 'input-xxxlarge',
									    'value' => set_value($key, ''),
									    'placeholder' => $key
								    );

								    // print input field
								    echo form_input($inputFieldData);
							    }
						    }

						    // put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
						    $static_data = array(
							    'CreditpointsMin' => '0',
							    'FachbereichID' => '5'
					    );

					    echo form_hidden($static_data);
				    ?>

			    </div>
			    <div id="stdgng-details-2" class="span6">
				    <?php echo form_textarea($stdgng_details_textarea_data); ?>
				    <?php echo form_submit('save_stdgng_detail_changes', 'Neuen Studiengang speichern', $btn_attributes); ?>
			    </div>
		    </div>
	    <?php echo form_close(); ?>
	    </div><!-- /.row-fluid -->
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		    </div>
	    </div>
	    <div class="span2"></div>
<?php endblock(); ?>
<?php end_extend(); ?>