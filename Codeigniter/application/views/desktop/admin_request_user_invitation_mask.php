<h2>Einladungsaufforderungen</h2>


<h3>Einladung auffordern</h3>
<p>Sie haben noch keinen Zugang? Dann können Sie hier eine Einladung anfordern:</p>

<?php echo validation_errors() ?>

<?php
	$attrs = array('class' => 'well', 'id' => 'request_invitation');
	echo form_open('admin/validate_request_user_invitation_form/', $attrs);
?>

<p><?php echo form_radio('role', '4', TRUE) ?> Ich bin ein Stundent</p>
<p><?php echo form_radio('role', '2', FALSE) ?> Ich bin ein Dozent</p>

<p>Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist. 
	Die Emailadresse wird für die Kommunikation mit meinFHD, den Dozenten und Studierenden verwendet.</p>

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
?>
<p>Vor &amp; Nachname: <?php echo form_input($data_forename); ?> <?php echo form_input($data_lastname); ?></p>

<?php
	$data_jahr = array(
			'class' => 'span2',
			'name' => 'startjahr',
			'placeholder' => 'Startjahr',
			'value' => set_value('startjahr')
		);
	$class_dd = 'class="studiengang_dd"';
?>
<?php
	$data_email = array(
		'class' => 'span3',
		'name' => 'email',
		'placeholder' => 'E-Mail',
		'value' => set_value('email')
	);
?>
<p>E-Mail: <?php echo form_input($data_email); ?></p>

<div id="studentendaten">
<hr />

	<p>Ich bin Erstsemestler! <?php echo form_checkbox('erstsemestler', 'accept', FALSE) ?> </p>

	<div id="erstsemestler">
		<p>Jahr, Semester &amp; Studiengang: <?php echo form_input($data_jahr); ?> WS: <?php echo form_radio('semesteranfang', 'WS', TRUE); ?> SS: <?php echo form_radio('semesteranfang', 'SS', FALSE); ?></p>
	</div>

		<p><?php echo form_dropdown('studiengang_dd', $studiengaenge, '0', $class_dd); ?></p>
		<?php
			$data_matrikelnummer = array(
				'class' => 'span2',
				'name' => 'matrikelnummer',
				'placeholder' => 'Matrikelnummer',
				'value' => set_value('matrikelnummer')
			);
		?>
		<p>Matrikelnummer: <?php echo form_input($data_matrikelnummer) ?></p>

</div>


<?php
$submit_data = array(
		'name'			=> 'submit',
		'class'			=> 'btn btn-danger'
	);
?>

<hr />

<?php echo form_submit($submit_data, 'Einladung auffordern'); ?>



<?php echo form_close(); ?>



<h3>---------------------------------------------------------------------------</h3>
<h3>Uebersicht aller Einladungsaufforderungen</h3>

<?php

$class_dd = 'id="user_function" class="span2"';
$dropdown_data = array('Erstellen', 'Loeschen');



$submit_data = array(
		'id' 			=> 'save',
		'name'			=> 'submit',
		'class'			=> 'btn btn-mini btn-danger'
	);

?>

<table id="user_overview" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>Nachname</th>
			<th>Vorname</th>
			<th>Email</th>
			<th>Funktion</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($user_invitations as $key => $value) { ?>
		<tr>
			<?php echo form_open('admin/create_user_from_invitation/', $attrs); ?>
			<?php echo form_hidden('request_id', $value['AnfrageID']); ?>

			<?php
				
				$data['name'] = 'lastname';
				$data['value'] = $value['Nachname'];
				echo $this->load->view('admin-subviews/user_td', $data, TRUE);

				$data['name'] = 'forename';
				$data['value'] = $value['Vorname'];
				echo $this->load->view('admin-subviews/user_td', $data, TRUE);

				$data['name'] = 'email';
				$data['value'] = $value['Emailadresse'];
				echo $this->load->view('admin-subviews/user_td', $data, TRUE);

			?>

			<?php echo "<td>".form_dropdown('user_function', $dropdown_data, '0', $class_dd)."</td>"; ?>

			<?php echo "<td>".form_submit($submit_data, 'LOS!')."</td>"; ?>

			<?php echo form_close(); ?>
		</tr>
		<?php } ?>
	</tbody>
</table>


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

})();

function toggle_more_info(c) {
	var additional_student_data = $("div#studentendaten");

	// c jQuery object of toggle button
	if (c.val() === '4') {
		// show additional student da
		additional_student_data.fadeIn('slow');
		console.log(c.val());
	}
	else {
		additional_student_data.fadeOut('slow');
		console.log(c.val());
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

	console.log(c.attr('checked'));
}

</script>