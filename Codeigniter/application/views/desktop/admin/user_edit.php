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

	$searchbox_content = '';
	(!empty($_POST['email']))?$searchbox_content=$_POST['email']:'';

	$data_search = array(
		'id' => 'user_cr_search',
		'class' => 'search-query',
		'name' => 'search_user',
		'placeholder' => 'Benutzer suchen',
		'value' => $searchbox_content
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

	<div id="modalcontent"></div>

	<?php FB::log($_POST);FB::log($_GET) ?>

<?php endblock(); ?>



<?php startblock('customFooterJQueryCode');?>

	var xhr; 

	var UsersEditAjax = {
		init : function( config ) {
			this.config = config;
			this.bindEvents();
			this.requestSearch(this.config.roleDropdown, this.config.searchInput);
		},
		
		bindEvents : function() {
			var self = this;
			this.config.roleDropdown.on( 'change', function() {
				// self.requestByStdGang($(this));
				self.requestSearch( $(this), self.config.searchInput );
			});
			this.config.searchInput.on( 'keyup', function() {
				// self.requestBySearch($(this));
				self.requestSearch( self.config.roleDropdown, $(this) );
			});
		},

		requestSearch : function( filter, searchbox ) {

			var self = this;

			// console.log(filter);
			// console.log(searchbox);

			clearTimeout( self.timer );

			// fire the command after 400 ms, so when the user types a name in the searchbox
			// not for every letter a ajax request will be fired, but for the last chain
			self.timer = setTimeout(function() {

				self.config.dataContent.html("lade Daten...");

				var data = '';

				// if filter not 0 = "Bitte auswählen" -> no role_id var
				( filter.val() !== '0' ) ? data+='role_id='+filter.val()+'&' : data+='role_id=&';
				// more than two letters, typed in the searchbox
				( searchbox.val().length > 2 ) ? data+='searchletter='+searchbox.val() : data+='searchletter=';
					
				// if the request was already sent, check if its still running
				// if so, abort it to prevent inserting the requested content, after
				// another request was sent and responsed/inserted
				if ( xhr && xhr.readyState != 4 ) {
					xhr.abort();
				}

				xhr = $.get(
					"<?php echo site_url();?>admin/ajax_show_user/",
					data,
					function(response) {
						self.config.dataContent.html(response);
					});
				
			}, 400);


		},
		
		requestByStdGang : function( studienganginput ) {
			var self = this;
			
			if ( studienganginput.val() !== '0' ) {
				this.config.dataContent.html("lade Daten...");

				// if the request was already sent, check if its still running
				// if so, abort it to prevent inserting the requested content, after
				// another request was sent and responsed/inserted
				if ( xhr && xhr.readyState != 4 ) {
					xhr.abort();
				}

				xhr = $.get(
				"<?php echo site_url();?>admin/ajax_show_user/",
				'role_id='+studienganginput.val(),
				function(response) {
					self.config.dataContent.html(response);
				});
			} else {
				console.log(studienganginput.val());
			}
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
				
				if ( xhr && xhr.readyState != 4 ) {
					xhr.abort();
				}

				xhr = $.get(
				url,
				data,
				function(response) {
					self.config.dataContent.html(response);
				});
			} else {
				clearTimeout( self.timer );
				
				if (searchinput.val().length == 0) { // load all users of selected std
					data = 'role_id='+this.config.roleDropdown.val();
					
					if ( xhr && xhr.readyState != 4 ) {
						xhr.abort();
					}

					xhr = $.get(
					url,
					data,
					function(response) {
						self.config.dataContent.html(response);
					});
				} else if (searchinput.val().length >= 2) { // start to search when when two letters were entered
					self.timer = setTimeout(function() {
						data = 'searchletter='+searchinput.val()+'&role_id='+self.config.roleDropdown.val();
						
						if ( xhr && xhr.readyState != 4 ) {
							xhr.abort();
						}

						xhr = $.get(
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


	// live click listener (because of ajax and new content) to override default submit button function
	// to open the prompt dialog 
	$("#user_content").on("click", "input#save", function() {
		// determine which function was selected from the dropdown
		// 0 = speichern, 1 = pw resetten, 2 = Studienplan resetten, 3 = Als..anmelden
		var user_function =  $(this).parents("form#edit_user_row").find("#user_function").val();

		if (user_function === '0') {
			$(this).attr("data-clicked", "true");
			// createDialog('Änderungen speichern', 'Sollen die Änderungen wirklich gespeichert werden?').dialog("open");
			_showModal('Änderungen speichern', 'Sollen die Änderungen wirklich gespeichert werden?', true);
		} else if (user_function === '1') {
			$(this).attr("data-clicked", "true");
			_showModal('Passwort resetten', 'Möchten Sie das Passwort für diesen Benutzer wirklich zurücksetzen?', true);
		} else if (user_function === '2') {
			$(this).attr("data-clicked", "true");
			_showModal('Studienplan resetten', 'Möchten Sie den Studienplan für diesen Benutzer wirklich zurücksetzen?', true);
		} else if (user_function === '3') {
			$(this).attr("data-clicked", "true");

   		   	// Edits by Christian Kundruss
            // for the login as function the model is unneccessary in my opinion...
            //createDialog('Anmelden als...', 'Möchten Sie sich wirklich als dieser Benutzer anmelden?').dialog("open");

             // if we do not use the modal box pass the information of the choosen user to the controller
             $("input[type=submit][data-clicked=true]").parents("form#edit_user_row").submit();
             $("td.user_content_row input#save").removeAttr("data-clicked");
		} else {

		}

		// prevent default submit behaviour
		return false;
	});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>