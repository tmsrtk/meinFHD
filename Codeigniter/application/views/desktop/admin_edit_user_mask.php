<?php
	// needet vars
	$data_formopen = array('class' => 'well form-search', 'id' => 'edit_user');
	$data_role = array();
	$data_role = $all_roles;
	// add as first element
	array_unshift($data_role, 'Bitte auswaehlen');
	$data_role_ext = 'class="user_change_rolle_dd" id="user_cr_role"';
	$data_search = array(
		'id' => 'user_cr_search',
		'class' => 'search-query',
		'name' => 'search_user',
		'placeholder' => 'Benutzer suchen'
	);
	//--------------------------------------------------------------------------
?>

<h2>Benutzer bearbeiten</h2>

<?php
	// validation errors or empty string otherwise
	echo validation_errors();
?>

<?php
	echo form_open('', $data_formopen);
	echo form_dropdown('user_change_rolle_dd', $data_role, '0', $data_role_ext);
	echo form_input($data_search);
	echo form_close();
?>

<!-- <table id="user_overview" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>Benutzername</th>
			<th>Nachname</th>
			<th>Vorname</th>
			<th>Email</th>
			<th>Funktion</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table> -->





<div class="row">
	<div class="span2"><strong>Benutzername</strong></div>
	<div class="span2"><strong>Nachname</strong></div>
	<div class="span2"><strong>Vorname</strong></div>
	<div class="span2"><strong>E-Mail</strong></div>
	<div class="span2"><strong>Funktion</strong></div>
	<div class="span2"><strong>Los</strong></div>
</div>

<div class="row" id="content_user">
	<!-- Userdata -->

</div>


























<script>

(function() {
	var UsersEditAjax = {
		init : function( config ) {
			this.config = config;
			this.bindEvents();
			this.requestBySearch();
		},

		bindEvents : function() {
			var self = this;
			this.config.roleDropdown.on( 'change', function() {
				self.clearSearchbox(self.config.searchInput);
				self.requestByStdGang($(this));
			});
			this.config.searchInput.on( 'keyup', function() {
				self.requestBySearch($(this));
			});
		},

		clearSearchbox : function(sb) {
			console.log(sb);
		},

		requestByStdGang : function( studienganginput ) {
			var self = this;
			this.config.dataContent.html("lade Daten...");
			$.get(
				"<?php echo site_url();?>admin/ajax_show_user/",
				'role_id='+studienganginput.val(),
			function(response) {
				self.config.dataContent.html(response);
			});
		},

		requestBySearch : function( searchinput ) {
			var self = this;
			var url = "<?php echo site_url();?>admin/ajax_show_user/";
			var data = '';

			// this.config.dataContent.html("lade Daten...");
			// console.log( this.config.roleDropdown.val() ); return;

			// for calling this method without parameter
			if (!searchinput) {
				data = 'role_id='+this.config.roleDropdown.val();

				$.get(
				url,
				data,
				function(response) {
					self.config.dataContent.html(response);
				});
			} else {
				clearTimeout( self.timer );

				if (searchinput.val().length == 0) { // load all users of selected std
					data = 'role_id='+this.config.roleDropdown.val();

					$.get(
					url,
					data,
					function(response) {
						self.config.dataContent.html(response);
					});
				} else if (searchinput.val().length >= 2) { // start to search when when two letters were entered
					self.timer = setTimeout(function() {
						data = 'searchletter='+searchinput.val()+'&role_id='+self.config.roleDropdown.val();

						$.get(
						url,
						data,
						function(response) {
							self.config.dataContent.html(response);
						});
						
					}, 400);
				}
			}
		}
	};

	// UsersEditAjax.init({
	// 	roleDropdown : $('#user_cr_role'),
	// 	searchInput : $('#user_cr_search'),
	// 	dataContent : $('table#user_overview tbody')
	// });

	UsersEditAjax.init({
		roleDropdown : $('#user_cr_role'),
		searchInput : $('#user_cr_search'),
		dataContent : $('div#content_user')
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
								$("input[type=submit][clicked=true]").parents("form#edit_user_row").submit();
								$("#content_user input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							},
							Abbrechen: function() {
								$("#content_user input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							}
						}
					});
		return $mydialog;
	}




	// live click listener (because of ajax an new content) to override default submit button function
	// to open the prompt dialog
	$("#content_user").on("click", "input#save", function() {
		// determine which function was selected from the dropdown
		// 0 = speichern, 1 = pw resetten, 2 = Stundenplan resetten, 3 = Als..anmelden
		var user_function =  $(this).parents("form#edit_user_row").find("#user_function").val();

		if (user_function === '0') {
			$(this).attr("clicked", "true");
			createDialog('Änderungen speichern', 'Sollen die Änderungen wirklich gespeichert werden?').dialog("open");
		} else if (user_function === '1') {
			$(this).attr("clicked", "true");
			createDialog('Passwort resetten', 'Möchten Sie das Passwort für diesen Benutzer wirklich zurücksetzen?').dialog("open");
		} else if (user_function === '2') {
			$(this).attr("clicked", "true");
			createDialog('Stundenplan resetten', 'Möchten Sie den Stundenplan für diesen Benutzer wirklich zurücksetzen?').dialog("open");
		} else if (user_function === '3') {
			$(this).attr("clicked", "true");
			createDialog('Anmelden als...', 'Möchten Sie sich wirklich als dieser Benutzer anmelden?').dialog("open");
		} else {

		}

		// prevent default submit behaviour
		return false;
	});
















})();

</script>




</script>

