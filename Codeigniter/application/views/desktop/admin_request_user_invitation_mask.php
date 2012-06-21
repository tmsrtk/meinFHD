<?php
	$data_forename = array(
			'class' => 'span2',
			'name' => 'forename',
			'placeholder' => 'Vorname',
			'value' => set_value('forename')
		);
	$data_lastname = array(
			'class' => 'span2',
			'name' => 'lastname',
			'placeholder' => 'Nachname',
			'value' => set_value('lastname')
		);


	$data_jahr = array(
			'class' => 'span2',
			'name' => 'startjahr',
			'placeholder' => 'Startjahr',
			'value' => set_value('startjahr')
		);
	$class_dd = 'class="studiengang_dd"';


	$data_email = array(
		'class' => 'span3',
		'name' => 'email',
		'placeholder' => 'E-Mail',
		'value' => set_value('email')
	);



	$data_matrikelnummer = array(
		'class' => 'span2',
		'name' => 'matrikelnummer',
		'placeholder' => 'Matrikelnummer',
		'value' => set_value('matrikelnummer')
	);



	$submit_data = array(
			'name'			=> 'los',
			'class'			=> 'btn btn-danger'
		);



	$attributes = array(
	    'class' => 'control-label'
	);

?>

<h2>Verwaltung der Einladungsanforderungen</h2>


<h3>Einladung anfordern</h3>
<p>Sie haben noch keinen Zugang? Dann können Sie hier eine Einladung anfordern:</p>

<?php echo validation_errors() ?>

<?php
	$attrs = array('class' => 'well form-horizontal', 'id' => 'request_invitation');
	echo form_open('admin/validate_request_user_invitation_form/', $attrs);
?>

<div class="control-group">
	<?php echo form_label('Ich bin ein', 'role', $attributes); ?>
	<div class="controls">
		<label class="radio">
			<?php echo form_radio('role', '4', TRUE) ?>
			Student
		</label>
		<label class="radio">
			<?php echo form_radio('role', '2', FALSE) ?>
			Dozent
		</label>
	</div>
</div>

<div class="alert">
	Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist. 
	Die Emailadresse wird für die Kommunikation mit meinFHD, den Dozenten und Studierenden verwendet.
</div>


<div class="control-group">
	<?php echo form_label('Vor &amp; Nachname', 'forename', $attributes); ?>
	<div class="controls docs-input-sizes">
		<?php echo form_input($data_forename); ?>
		<?php echo form_input($data_lastname); ?>
	</div>
</div>

<div class="control-group error">
	<?php echo form_label('E-Mail', 'email', $attributes); ?>
	<div class="controls docs-input-sizes">
		<?php echo form_input($data_email); ?>
	</div>
</div>


<div id="studentendaten">
<hr />


	<div class="control-group">
		<?php echo form_label('Ich bin Erstsemestler!', 'erstsemestler', $attributes); ?>
		<div class="controls docs-input-sizes">
			<?php echo form_checkbox('erstsemestler', 'accept', FALSE) ?>
		</div>
	</div>

	<div id="erstsemestler">

		<div class="control-group">
			<?php echo form_label('Jahr', 'startjahr', $attributes); ?>
			<div class="controls docs-input-sizes">
				<?php echo form_input($data_jahr); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Semesteranfang', 'semesteranfang', $attributes); ?>
			<div class="controls">
				<label class="radio">
					<?php echo form_radio('semesteranfang', 'WS', TRUE); ?>
					WS
				</label>
			</div>
			<div class="controls">
				<label class="radio">
					<?php echo form_radio('semesteranfang', 'SS', FALSE); ?>
					SS
				</label>
			</div>
		</div>

	</div>

	<div class="control-group">
		<?php echo form_label('Studiengang', 'semesteranfang', $attributes); ?>
		<div class="controls docs-input-sizes">
			<?php echo form_dropdown('studiengang_dd', $studiengaenge, '0', $class_dd); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo form_label('Matrikelnummer', 'matrikelnummer', $attributes); ?>
		<div class="controls docs-input-sizes">
			<?php echo form_input($data_matrikelnummer) ?>
		</div>
	</div>

</div>

<div class="form-actions">
<?php echo form_submit($submit_data, 'Einladung anfordern'); ?>
</div>

<?php echo form_close(); ?>



<h3>Uebersicht aller Einladungsaufforderungen</h3>

<?php

$class_dd = 'id="user_function" class="span2"';
$dropdown_data = array('Erstellen', 'Loeschen');



$submit_data = array(
		'id' 			=> 'save',
		'name'			=> 'los',
		'class'			=> 'btn btn-mini btn-danger'
	);

?>


<div class="row">
	<div class="span2">Test</div>
	<div class="span2">Test</div>
	<div class="span2">Test</div>
	<div class="span2">Test</div>
	<div class="span2">Test</div>
	<div class="span2">Test</div>
</div>



<?php $attrs2 = array('class' => 'form-horizontal', 'id' => 'accept_invitation'); ?>
	<?php foreach ($user_invitations as $key => $value) { ?>
	<?php FB::log($value); ?>

<div class="row">
	<?php echo form_open('admin/create_user_from_invitation/', $attrs2); ?>
	<?php echo form_hidden('request_id', $value['AnfrageID']); ?>

	<div class="span2">leer</div>
	<div class="span2"><?php echo $value['Nachname']; ?></div>
	<div class="span2"><?php echo $value['Vorname']; ?></div>
	<div class="span2"><?php echo $value['Emailadresse']; ?></div>

	<?php echo "<div class=\"span2\">".form_dropdown('user_function', $dropdown_data, '0', $class_dd)."</div>"; ?>

	<?php echo "<div class=\"span2\">".form_submit($submit_data, 'LOS!')."</div>"; ?>

	<?php echo form_close(); ?>
</div>
<?php } ?>


<script>

(function() {

	// onchange to radiobuttons 
	// input[name='radio-button-gruppe']
	$("input[name='role']").change(function() {
		toggle_more_info($(this));
	});

	$("input[name='erstsemestler']").change(function() {
		toggle_erstsemestler($(this));
	});





	var $mydialog = $('<div id="dialog-confirm" title="Empty the recycle bin?"></div>')
					.html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>These items will be permanently deleted and cannot be recovered. Are you sure?</p>')
					.dialog({
						autoOpen: false,
						resizable: false,
						height: 200,
						modal: true,
						buttons: {
							"Ja, löschen": function() {
								$("input[type=submit][clicked=true]").parents("form").submit();
								$("#accept_invitation input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							},
							Abbrechen: function() {
								$("#accept_invitation input#save").removeAttr("clicked");
								$mydialog.dialog( "close" );
							}
						}
					});


	$("#accept_invitation input#save").click(function(e) {
		e.preventDefault();

		// add custom attribute to determine later
		$(this).attr("clicked", "true");

		$mydialog.dialog("open");
	});



	// confirm_modal();


	// $("#dialog-confirm").click(function() {
	// 	$mydialog.dialog('open');
	// 	return false;
	// });


		// $( "#dialog-confirm" ).dialog({
		// 	autoOpen: false,
		// 	resizable: false,
		// 	height:140,
		// 	modal: true,
		// 	buttons: {
		// 		"Ja, löschen": function() {
		// 			$("input[type=submit][clicked=true]").parents("form").submit();
		// 			that.removeAttr("clicked");
		// 			$( this ).dialog( "close" );
		// 		},
		// 		Abbrechen: function() {
		// 			that.removeAttr("clicked");
		// 			$( this ).dialog( "close" );
		// 		}
		// 	}
		// });


})();

function toggle_more_info(c) {
	var additional_student_data = $("div#studentendaten");

	// c jQuery object of toggle button
	if (c.val() === '4') {
		// show additional student da
		additional_student_data.fadeIn('slow');
	}
	else {
		additional_student_data.fadeOut('slow');
	}
}

function toggle_erstsemestler(c) {
	var erstsemestler_data = $("div#erstsemestler");

	if (c.attr('checked')) {
		erstsemestler_data.fadeOut('slow');
	}
	else {
		erstsemestler_data.fadeIn('slow');
	}
}

// function confirm_modal() {

// 	$("#accept_invitation input#save").click(function(e) {
// 		e.preventDefault();

// 		var that = $(this);
// 		// add custom attribute to determine later
// 		$(this).attr("clicked", "true");


// 		$mydialog.dialog("open");
// 		// $("#dialog-confirm").dialog('open');

// 	});

// }

</script>