<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
				<div class="span4"></div>
				<div class="span4 well well-small">
<?php endblock(); ?>

<?php
	// general form setup
	$data_formopen = array('class' => '', 'id' => 'create_user');
	$data_roles = 'class="role input-xxxlarge"';
	$data_loginname = array(
			'class' => 'input-xxxlarge',
			'name' => 'loginname',
			'placeholder' => 'Login Name',
			'value' => set_value('loginname')
		);
	$data_email = array(
			'class' => 'input-xxxlarge',
			'name' => 'email',
			'placeholder' => 'E-Mail',
			'value' => set_value('email')
		);
	$data_forename = array(
				'class' => 'input-xxxlarge',
				'name' => 'forename',
				'placeholder' => 'Vorname',
				'value' => set_value('forename')
		);
	$data_lastname = array(
			'class' => 'input-xxxlarge',
			'name' => 'lastname',
			'placeholder' => 'Nachname',
			'value' => set_value('lastname')
		);
	$data_matrikelnummer = array(
			'class' => 'input-xxxlarge',
			'name' => 'matrikelnummer',
			'placeholder' => 'Matrikelnummer',
			'value' => set_value('matrikelnummer')
		);
	$data_startjahr = array(
			'class' => 'input-xxxlarge',
			'name' => 'startjahr',
			'placeholder' => 'Startjahr',
			'value' => set_value('startjahr')
		);
	$data_studiengang = 'class="studiengang"';
	$submit_data = array(
			'name'			=> 'submit',
			'class'			=> 'btn btn-danger'
		);
?>

<?php startblock('content'); # additional markup before content ?>
					<div class="row-fluid">
						<h2>Benutzer anlegen</h2>
						<?php echo validation_errors(); // validation errors or empty string otherwise ?>
					</div>
					<hr>
					<?php echo form_open('admin/validate_create_user_form/', $data_formopen); // main inputs for all users ?>
						<fieldset id="user-info">
							<?php echo form_dropdown('role', $all_roles, /*standard value*/set_value('role'), $data_roles); ?>
							<?php echo form_input($data_loginname); ?>
							<?php echo form_input($data_email); ?>
							<?php echo form_input($data_forename); ?>
							<?php echo form_input($data_lastname); ?>
						</fieldet>
						<fieldset id="studentdata">
							
							<?php
								// student specific input fields, only visible if 'student' is selected from dropdown
								// matrnr, startjahr+sem, studiengang
								echo form_input($data_matrikelnummer);
								echo form_input($data_startjahr);
								// echo form_radio('semesteranfang', 'WS', TRUE);
								// creating the radio manually, to use the set_radio() method
								// echo '<input type="radio" name="semesteranfang" value="WiSe"'.set_radio('semesteranfang', 'WiSe', TRUE).' />';
								// echo 'WiSe';
								// // echo form_radio('semesteranfang', 'SS', FALSE);
								// echo '<input type="radio" name="semesteranfang" value="SoSe"'.set_radio('semesteranfang', 'SoSe', FALSE).' />';
								// echo 'SoSe'; echo '<br />';

								echo '<input type="radio" name="semesteranfang" value="WS"'.set_radio('semesteranfang', 'WS', TRUE).' />';
								echo 'WS';
								// echo form_radio('semesteranfang', 'SS', FALSE);
								echo '<input type="radio" name="semesteranfang" value="SS"'.set_radio('semesteranfang', 'SS', FALSE).' />';
								echo 'SS'; echo '<br />';
							?>
							<?php echo form_dropdown('studiengang', $studiengaenge, /*standard value*/'', $data_studiengang); ?>
						</fieldset>
						<hr>
						<?php echo form_submit($submit_data, 'Neuen Benutzer anlegen'); ?>
					<?php echo form_close(); ?>
					
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
					</div>
					<div class="span4"></div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
	var Studentsdata = {
		init : function( config ) {
			this.config = config;
			this.bindEvents();
			this.toggleStudentsdata(this.config.roleDropdown);
		},
		
		bindEvents : function() {
			var self = this;
			this.config.roleDropdown.on( 'change', function() {
				self.toggleStudentsdata($(this));
			} );
		},
		
		toggleStudentsdata : function(selectbox) {
			var studentdata_container = this.config.studentdataField;
			var dropdown_value = (selectbox)
				? dropdown_value = selectbox.val()
				: '0';
			(dropdown_value === '5') ? studentdata_container.slideDown() : studentdata_container.slideUp();
		}
	};
	
	// in case of more than one object
	// var sd1 = Object.create( Studentsdata );
	// sd1.init({
	// 	studentdataField : $('#studentdata'),
	// 	roleDropdown : $('select.role')
	// });
	
	Studentsdata.init({
		studentdataField : $('#studentdata'),
		roleDropdown : $('select.role')
	});
<?php endblock(); ?>

<?php end_extend(); ?>