<h2>Benutzer anlegen</h2>

<?php
	// form validation
	// http://codeigniter.com/user_guide/libraries/form_validation.html

	// validation errors or empty string otherwise
	echo validation_errors();

	// prepare attrs for the form
	$attrs = array('class' => 'well', 'id' => 'create_user');
	echo form_open('admin/validate_create_user_form/', $attrs);

	$class_dd = 'class="rolle_dd" onchange="toggle_studentendaten($(this).val())"';      ///////// I THINK ITS BETTER TO DO IT THIS WAY, THAN IN THE FOOTER!!!!!!!!!!!!!!!!!!!!
	echo form_dropdown('rolle_dd', $global_data['userdata']['roles'], /*standard value*/'', $class_dd);

	$data = array(
			'class' => 'span3',
			'name' => 'username',
			'placeholder' => 'Login Name',
			'value' => set_value('username')
		);
	echo form_input($data);

	$data = array(
			'class' => 'span3',
			'name' => 'email',
			'placeholder' => 'E-Mail',
			'value' => set_value('email')
		);
	echo form_input($data);

	$data = array(
			'class' => 'span3',
			'name' => 'forename',
			'placeholder' => 'Vorname',
			'value' => set_value('forename')
		);
	echo form_input($data);

	$data = array(
			'class' => 'span3',
			'name' => 'lastname',
			'placeholder' => 'Nachname',
			'value' => set_value('lastname')
		);
	echo form_input($data);

	// student specific input fields, only visible if 'student' is selected
	// matrnr, startjahr+sem, studiengang
?>
	<div id="studentendaten">
<?php
	$data = array(
			'class' => 'span2',
			'name' => 'matrikelnummer',
			'placeholder' => 'Matrikelnummer',
			'value' => set_value('matrikelnummer')
		);
	echo form_input($data);

	$data = array(
			'class' => 'span2',
			'name' => 'startjahr',
			'placeholder' => 'Startjahr',
			'value' => set_value('startjahr')
		);
	echo form_input($data);

	echo form_radio('semester_def', 'WS', TRUE);
	echo 'WS';
	echo form_radio('semester_def', 'SS', FALSE);
	echo 'SS'; echo '<br />';

	$class_dd = 'class="studiengang_dd"';
	echo form_dropdown('studiengang_dd', $studiengaenge, /*standard value*/'', $class_dd);
?>
	</div>
<?php
	$submit_data = array(
			'name'			=> 'submit',
			'class'			=> 'btn btn-danger'
		);
	echo form_submit($submit_data, 'Neuen Benutzer anlegen');

	echo form_close();

?>




<script>
	(function() {

		/* user management */
		//// create_user

		// hide studentendaten
		toggle_studentendaten();

		// dropdown value change								/// LOOK AT THE TOP OF THE DOC, THERE IS AN OTHER VARIANT!!!!!!!!!!!!!!!!!!!!!!!!
		// $(".rolle_dd").change(function() {
		// 	toggle_studentendaten($("select.rolle_dd option:selected").val());
		// });


	})();

	// checks if studentendaten have to be shown
	function toggle_studentendaten(c) {
		// if 'student' was selected show more inputs
		if(c === '4') {
			$("#studentendaten").show();
		} else {
			$("#studentendaten").hide();
		}
	}



	
</script>