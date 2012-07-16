<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Einladungsverwaltung<?php endblock(); ?>

<?php
	# general form setup
	$data_formopen = array('class' => 'form-horizontal', 'id' => 'request_invitation');
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
	$data_labelattrs = array(
		'class' => 'control-label'
	);
?>

<?php startblock('content'); # additional markup before content ?>
				<div class="row-fluid">
					<h2>Verwaltung der Einladungsanforderungen</h2>
				</div>
				<hr>
				<?php echo validation_errors() ?>
				<h3>Einladung anfordern</h3>
				<p>Sie haben noch keinen Zugang? Dann können Sie hier eine Einladung anfordern:</p>



<?php
	echo form_open('admin/validate_request_user_invitation_form/', $data_formopen);
?>

<div class="control-group">
	<?php echo form_label('Ich bin ein', 'role', $data_labelattrs); ?>
	<div class="controls">
		<label class="radio">
			<?php echo form_radio('role', '5', TRUE) ?>
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
	<?php echo form_label('Vor &amp; Nachname', 'forename', $data_labelattrs); ?>
	<div class="controls docs-input-sizes">
		<?php echo form_input($data_forename); ?>
		<?php echo form_input($data_lastname); ?>
	</div>
</div>

<div class="control-group">
	<?php echo form_label('E-Mail', 'email', $data_labelattrs); ?>
	<div class="controls docs-input-sizes">
		<?php echo form_input($data_email); ?>
	</div>
</div>


<div id="studentendaten">
<hr />


	<div class="control-group">
		<?php echo form_label('Ich bin Erstsemestler!', 'erstsemestler', $data_labelattrs); ?>
		<div class="controls docs-input-sizes">
			<?php echo form_checkbox('erstsemestler', 'accept', FALSE) ?>
		</div>
	</div>

	<div id="erstsemestler">

		<div class="control-group">
			<?php echo form_label('Jahr', 'startjahr', $data_labelattrs); ?>
			<div class="controls docs-input-sizes">
				<?php echo form_input($data_jahr); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo form_label('Semesteranfang', 'semesteranfang', $data_labelattrs); ?>
			<div class="controls">
				<label class="radio">
					<?php echo form_radio('semesteranfang', 'WiSe', TRUE); ?>
					WiSe
				</label>
			</div>
			<div class="controls">
				<label class="radio">
					<?php echo form_radio('semesteranfang', 'SoSe', FALSE); ?>
					SoSe
				</label>
			</div>
		</div>

	</div>

	<div class="control-group">
		<?php echo form_label('Studiengang', 'studiengang', $data_labelattrs); ?>
		<div class="controls docs-input-sizes">
			<?php echo form_dropdown('studiengang', $studiengaenge, '', $class_dd); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo form_label('Matrikelnummer', 'matrikelnummer', $data_labelattrs); ?>
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
	$data_formopen2 = array('class' => 'form-horizontal', 'id' => 'accept_invitation');
	$data_dropdown = array('Erstellen', 'Loeschen');
	$attrs_dropdown = 'id="user_function" class="span2"';
	$submit_data = array(
			'id' 			=> 'save',
			'name'			=> 'los',
			'class'			=> 'btn btn-mini btn-danger'
		);
?>


<div class="row">
	<div class="span2">Test</div>
	<div class="span2"><strong>Nachname</strong></div>
	<div class="span2"><strong>Vorname</strong></div>
	<div class="span2"><strong>E-Mail</strong></div>
	<div class="span2"><strong>Funktion</strong></div>
	<div class="span2"><strong>Los</strong></div>
</div>

<div class="row" id="content_invitations">
<?php foreach ($user_invitations as $key => $value) : ?>
	<?php echo form_open('admin/create_user_from_invitation/', $data_formopen2); ?>
	<?php echo form_hidden('request_id', $value['AnfrageID']); ?>

	<div class="span2">leer</div>
	<div class="span2"><?php echo $value['Nachname']; ?></div>
	<div class="span2"><?php echo $value['Vorname']; ?></div>
	<div class="span2"><?php echo $value['Emailadresse']; ?></div>

	<?php echo "<div class=\"span2\">".form_dropdown('user_function', $data_dropdown, '0', $attrs_dropdown)."</div>"; ?>
	<?php echo "<div class=\"span2\">".form_submit($submit_data, 'LOS!')."</div>"; ?>
	<div class="clearfix"></div>
	<?php echo form_close(); ?>
<?php endforeach ?>
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

	// onchange for radiobuttons 
	$("input[name='role']").change(function() {
		toggle_studentdata($(this));
	});

	// onchange for erstsemestler
	$("input[name='erstsemestler']").change(function() {
		toggle_erstsemestlerdata($(this));
	});

	// prompt dialogs
	/**
	 * 
	 */
	function createDialog(title, text) {
		var $mydialog = $('<div id="dialog-confirm" title="'+title+'"></div>')
					.html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text+'</p>')
					.dialog({
						autoOpen: false,
						resizable: false,
						height: 200,
						modal: true,
						buttons: {
							OK: function() {
								$("input[type=submit][clicked=true]").parents("form#accept_invitation").submit();
								$("#content_invitations input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							},
							Abbrechen: function() {
								$("#content_invitations input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							}
						}
					});
		return $mydialog;
	}

	$("#content_invitations").on("click", "input#save", function() {
		// e.preventDefault();
		// determine which function was selected from the dropdown
		// 0 = erstellen, 1 = löschen
		var user_function =  $(this).parents("form#accept_invitation").find("#user_function").val();

		// console.log(user_function);
		// return false;

		if (user_function === '0') {
			$(this).attr("clicked", "true");
			createDialog('User erstellen', 'Sollen der User wirklich erstellt werden?').dialog("open");
		} else if (user_function === '1') {
			$(this).attr("clicked", "true");
			createDialog('Einladung löschen', 'Soll die Einladung wirklch gelöscht werden?').dialog("open");
		} else {

		}

		// prevent default submit behaviour
		return false;
	});


/* toggles additional student data when user selected 'student' from the dropdown */
function toggle_studentdata(c) {
	var additional_student_data = $("div#studentendaten");

	// c jQuery object of toggle button
	if (c.val() === '5') {
		// show additional student da
		additional_student_data.slideDown('slow');
	}
	else {
		additional_student_data.slideUp('slow');
	}
}

/* toggles additional semester data when user checked 'Erstsemester' */
function toggle_erstsemestlerdata(c) {
	var erstsemestler_data = $("div#erstsemestler");

	if (c.attr('checked')) {
		erstsemestler_data.slideUp('slow');
	}
	else {
		erstsemestler_data.slideDown('slow');
	}
}
<?php endblock(); ?>

<?php end_extend(); ?>