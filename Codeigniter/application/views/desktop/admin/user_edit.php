<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer bearbeiten<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
<?php
	// needet vars
	$data_formopen = array('class' => 'form-search', 'id' => 'edit_user');
	$data_role = array();
	$data_role = $all_roles;
	// add as first element
	array_unshift($data_role, 'Bitte wählen');
	$data_role_ext = 'class="user_change_rolle_dd" id="user_cr_role"';
	$data_search = array(
		'id' => 'user_cr_search',
		'class' => 'search-query',
		'name' => 'search_user',
		'placeholder' => 'Benutzer suchen'
	);
	//--------------------------------------------------------------------------
?>
	<div class="row-fluid">
		<?php echo form_open('', $data_formopen); ?>
		<div class="span4"><h2>Benutzer bearbeiten</h2></div>
		<div class="span4"><h5>Filter</h5><?php echo form_dropdown('user_change_rolle_dd', $data_role, '0', $data_role_ext); ?></div>
		<div class="span4"><h5>Suche</h5><?php echo form_input($data_search); ?></div>
		<?php echo form_close(); ?>
	</div>
	<hr>

	<?php echo validation_errors(); // validation errors or empty string otherwise ?>

	<div class="row-fluid">
		<table id="user_overview" class="table table-striped">
			<thead>
				<tr>
					<th>
						<div class="span2">Loginname</div>
						<div class="span2">Nachname</div>
						<div class="span2">Vorname</div>
						<div class="span2">E-Mail</div>
						<div class="span2">Funktion</div>
						<div class="span2">Los</div>
					</th>
				</tr>
			</thead>

			<tbody id="user_content">
				<!-- Userdata -->
			</tbody>
		</table>
	</div>

<?php endblock(); ?>



<?php startblock('customFooterJQueryCode');?>

	var UsersEditAjax = {
		init : function( config ) {
			this.config = config;
			this.bindEvents();
			// this.requestBySearch();
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
							}
						);
						
					}, 400);
				}
			}
		}
	};

	UsersEditAjax.init({
		roleDropdown : $('#user_cr_role'),
		searchInput : $('#user_cr_search'),
		dataContent : $('tbody#user_content')
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
								$("td.user_content_row input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							},
							Abbrechen: function() {
								$("td.user_content_row input#save").removeAttr("clicked");
								$( this ).dialog( "close" );
							}
						}
					});
		return $mydialog;
	}

	// live click listener (because of ajax and new content) to override default submit button function
	// to open the prompt dialog 
	$("#user_content").on("click", "input#save", function() {
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

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>