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
						<table id="user_overview" class="table table-striped ">
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
						</table>
					</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>
				var UsersEditAjax = {
					init : function( config ) {
						this.config = config;
						this.bindEvents();
						this.requestBySearch();
					},
					
					bindEvents : function() {
						var self = this;
						this.config.roleDropdown.on( 'change', function() {
							self.requestByStdGang($(this));
						});
						this.config.searchInput.on( 'keyup', function() {
							self.requestBySearch($(this));
						});
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
					dataContent : $('table#user_overview tbody')
				});
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>