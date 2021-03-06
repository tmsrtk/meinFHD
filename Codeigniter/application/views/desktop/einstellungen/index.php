<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Persönliche Einstellungen<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php 
$userroles = $this->user_model->get_all_roles();
?>

<!-- New -->
<div class="well well-small">

	<!-- Header -->
	<div class="row-fluid">
		<div class="span12">
			<div class="span8">
				<h1>Persönliche Einstellungen</h1>
			</div>
			<div class="span4 pull-right">
				<p style="text-align: right; font-weight: bold; font-size: 18px;"><?php if ( in_array(Roles::DOZENT, $userroles)) print $formdata['Titel'].' ' ?><?php print $formdata['Vorname'].' '.$formdata['Nachname'] ?></p>
				<?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
				<p style="text-align: right"><?php print $userdata['act_semester'].'tes Semetser'?></p>
				<?php endif ?>
			</div>
		</div>
	</div>
	<div class="row-fluid"><hr /></div>


	<!-- Content -->
	<div class="row-fluid">
		<?php echo validation_errors() ?>

		<?php
		  $formopen = array(
			'class' => 'form-horizontal'
			);
		?>
		<?php echo form_open(base_url('einstellungen/validate'), $formopen) ?>

			<div class="control-group">
				<label class="control-label" for="loginname">Loginname</label>
				<div class="controls">
					<input type="text" name="loginname" placeholder="Loginname" value="<?php echo set_value('loginname', $formdata['LoginName']) ?>">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="password">Passwort</label>
				<div class="controls">
					<input type="password" name="password" placeholder="Passwort">
					<input type="password" name="password2" placeholder="Passwort bestätigen">
				</div>
			</div>

			<hr />

			<?php if ( in_array(Roles::DOZENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="forename">Titel</label>
				<div class="controls">
					<input type="text" name="title" placeholder="Titel" value="<?php echo set_value('title', $formdata['Titel']) ?>">
				</div>
			</div>
			<?php endif ?>

			<div class="control-group">
				<label class="control-label" for="forename">Vor&Nachname</label>
				<div class="controls">
					<input type="text" name="forename" placeholder="Vorname" value="<?php echo set_value('forename', $formdata['Vorname']) ?>">
					<input type="text" name="lastname" placeholder="Nachname" value="<?php echo set_value('lastname', $formdata['Nachname']) ?>">
				</div>
			</div>

			<hr />

			<div class="control-group">
				<label class="control-label" for="email">E-Mail Adresse</label>
				<div class="controls">
					<input type="text" name="email" placeholder="E-Mail" value="<?php echo set_value('email', $formdata['Email']) ?>">
				</div>
			</div>

			<?php if ( in_array(Roles::DOZENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="room">Raum</label>
				<div class="controls">
					<input type="text" name="room" placeholder="Raum" value="<?php echo set_value('room', $formdata['Raum']) ?>">
				</div>
			</div>
			<hr />
			<?php endif ?>

			<?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="studiengang">Studiengang</label>
				<div class="controls">
					<input type="text" name="studiengang" placeholder="Studiengang" value="<?php print $formdata['StudiengangName'] . ' ' . $formdata['Pruefungsordnung'] ?>" disabled>
					<a href="<?php echo base_url('einstellungen/studiengang_wechseln') ?>" class="btn btn-warning">Studiengang wechseln</a>
				</div>
			</div>
			<?php endif ?>

			<?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="matrikelnummer">Matrikelnummer</label>
				<div class="controls">
					<input type="text" name="matrikelnummer" placeholder="Matrikelnummer" value="<?php print $formdata['Matrikelnummer'] ?>" disabled>
				</div>
			</div>
			<?php endif ?>

			<?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="semesteranfang">Semesteranfang</label>
				<div class="controls">
					<div class="radio">
						<?php
						$checkedWS = FALSE;
						$checkedSS = FALSE;
						($formdata['StudienbeginnSemestertyp'] == 'WS') ? $checkedWS=TRUE : $checkedSS=TRUE;

						// FB::log($formdata['StudienbeginnSemestertyp']);

						?>

						<input type="radio" name="semesteranfang" value="WS" <?php echo set_radio('semesteranfang', 'WS', $checkedWS) ?>>WS
					</div>
				</div>
				<div class="controls">
					<div class="radio">
						<input type="radio" name="semesteranfang" value="SS" <?php echo set_radio('semesteranfang', 'SS', $checkedSS) ?>>SS
					</div>
				</div>
			</div>
			<?php endif ?>

			<?php if ( in_array(Roles::STUDENT, $userroles)) : ?>
			<div class="control-group">
				<label class="control-label" for="startjahr">Startjahr</label>
				<div class="controls">
					<input class="span1" type="text" name="startjahr" value="<?php echo set_value('startjahr', $formdata['StudienbeginnJahr']) ?>">
				</div>
			</div>
			<?php endif ?>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary span3">Änderungen Speichern</button>
			</div>

		<?php echo form_close() ?>
	</div>
</div><!-- END well well-small -->

<!-- ######################################################################################################################## -->

<?php
/*

<!-- Old -->
<div class="container-fluid">
  <form id="user-details" class="form-vertical" method="post" action="<?php print base_url(); ?>einstellungen">
	<div class="row-fluid">
	  <div class="span12" id="basisinfos">
		<div class="alert alert-block alert-info">
		  <h3>Basisinfos</h3>

	<?php 

	if($info['MatrikelnummerFlag'] != 1) {

	?>
		<p>Bitte geben Sie zunächst ihre Matrikelnummer an. Diese Eingabe ist einmalig und wichtig für Ihre Zuordnung zu Kursen.</p>

		<div class="form-elements" style="margin: 1em 0;">
		  <label for="matrikelnummer">Matrikelnummer</label>
		  <input type="number" class="input-xxxlarge input-block-level" id="matrikelnummer" name="matrikel" placeholder="Matrikelnummer" />
		</div>
		<ul style="list-style: none; margin:0; padding 0;">    
	<?php 

	} else {

	?>
		  <ul style="list-style: none; margin:0; padding 0;">
			<li>Matrikelnummer: <?php echo $info['Matrikelnummer'] ?></li>
	<?php 

	}

	?>
			<li>Studiengang: <?php echo $info['StudiengangName'] ?> <a href="<?php print base_url(); ?>einstellungen/studiengangWechseln">(ändern)</a></li>
			<li>Studienbeginn: <?php echo $info['StudienbeginnSemestertyp'] . " " . $info['StudienbeginnJahr']; ?></li>
			<li>Fachsemester: <?php echo $info['Semester'] ?></li>
		  </ul>
		</div>
	  </div>
	</div>
	<div class="row-fluid">
	  <fieldset class="span6" id="login-details">
		<div class="well well-small">
		  <h3>Login-Details</h3>
		  <div class="form-elements">
			  <label for="login-name">Loginname</label>
			  <input type="text" class="input-xxxlarge input-block-level" id="login-name" name="login" placeholder="Loginname"<?php if(isset($info['LoginName'])) echo ' value="' . $info['LoginName'] . '"' ?> />
			  <label for="password">Passwort</label>
			  <input type="password" class="input-xxxlarge input-block-level" id="password" name="pw" placeholder="Passwort" />
			  <label for="confirm-password">Passwort bestätigen</label>
			  <input type="password" class="input-xxxlarge input-block-level" id="confirm-password" name="pw2" placeholder="Passwort bestätigen" />
		  </div>
		</div>
	  </fieldset>
	  <fieldset class="span6" id="contact-details">
		<div class="well well-small">
		  <h3>Kontaktinformationen</h3>
		  <div class="form-elements">
			<label for="first-name">Vorname</label>
			<input type="text" class="input-xxxlarge input-block-level" id="first-name" name="firstname" placeholder="Vorname"<?php if ( isset($info['Vorname']) ) echo ' value="' . $info['Vorname'] . '"' ?> />
			<label for="last-name">Nachname</label>
			<input type="text" class="input-xxxlarge input-block-level" id="last-name" name="lastname" placeholder="Nachname"<?php if ( isset($info['Nachname']) ) echo ' value="' . $info['Nachname'] . '"' ?> />
			<label for="email">E-Mail-Adresse</label>
			<input type="email" class="input-xxxlarge input-block-level" id="email" name="email" placeholder="E-Mail-Adresse"<?php if ( isset($info['Email']) ) echo ' value="' . $info['Email'] . '"' ?> />
			<label for="private-correspondence" class="checkbox"><input type="checkbox" id="private-correspondence" name="emailflag"<?php if ( isset($info['EmailDarfGezeigtWerden']) && $info['EmailDarfGezeigtWerden'] == 1 ) echo ' checked' ?> /> Dozenten dürfen mich unter dieser Adresse auch persönlich erreichen.</label>
		  </div>
		</div>
	  </fieldset>
	</div>

	<div class="row-fluid">
	  <div class="alert alert-info clearfix">
		<button type="submit" class="btn btn-large" value="save"><i class="icon-hdd"></i> Speichern</button>
	  </div>
	</div>
  </form>
</div>

 */
?>
<?php endblock(); ?>



<?php end_extend(); ?>