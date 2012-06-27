<h2>Benutzer anlegen</h2>


<?php
	// needet vars
	$data_formopen = array('class' => 'well', 'id' => 'create_user');
	$data_roles = 'class="role"';
	$data_loginname = array(
			'class' => 'span3',
			'name' => 'loginname',
			'placeholder' => 'Login Name',
			'value' => set_value('loginname')
		);
	$data_email = array(
			'class' => 'span3',
			'name' => 'email',
			'placeholder' => 'E-Mail',
			'value' => set_value('email')
		);
	$data_forename = array(
				'class' => 'span3',
				'name' => 'forename',
				'placeholder' => 'Vorname',
				'value' => set_value('forename')
		);
	$data_lastname = array(
			'class' => 'span3',
			'name' => 'lastname',
			'placeholder' => 'Nachname',
			'value' => set_value('lastname')
		);
	$data_matrikelnummer = array(
			'class' => 'span2',
			'name' => 'matrikelnummer',
			'placeholder' => 'Matrikelnummer',
			'value' => set_value('matrikelnummer')
		);
	$data_startjahr = array(
			'class' => 'span2',
			'name' => 'startjahr',
			'placeholder' => 'Startjahr',
			'value' => set_value('startjahr')
		);
	$data_studiengang = 'class="studiengang_dd"';
	$submit_data = array(
			'name'			=> 'submit',
			'class'			=> 'btn btn-danger'
		);
	//--------------------------------------------------------------------------
?>


<?php 
	// form validation
	// http://codeigniter.com/user_guide/libraries/form_validation.html

	// validation errors or empty string otherwise
	echo validation_errors();
?>

<?php
	// main inputs for all users
	echo form_open('admin/validate_create_user_form/', $data_formopen);
	echo form_dropdown('role', $userdata['roles'], /*standard value*/'0', $data_roles);
	echo form_input($data_loginname);
	echo form_input($data_email);
	echo form_input($data_forename);
	echo form_input($data_lastname);
?>
	<div id="studentdata">
<?php
	// student specific input fields, only visible if 'student' is selected from dropdown
	// matrnr, startjahr+sem, studiengang
	echo form_input($data_matrikelnummer);
	echo form_input($data_startjahr);
	echo form_radio('semesteranfang', 'WS', TRUE);
	echo 'WS';
	echo form_radio('semesteranfang', 'SS', FALSE);
	echo 'SS'; echo '<br />';
	echo form_dropdown('studiengang_dd', $studiengaenge, /*standard value*/'', $data_studiengang);
?>
	</div>
<?php
	echo form_submit($submit_data, 'Neuen Benutzer anlegen');
	echo form_close();
?>





<script>



(function() {

	var Studentsdata = {
		init : function( config ) {
			this.config = config;
			this.bindEvents();
			this.toggleStudentsdata();
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
			(dropdown_value === '4') ? studentdata_container.fadeIn() : studentdata_container.fadeOut();
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

})();







	
</script>