<h2>Benutzer bearbeiten</h2>

<?php
	
	// validation errors or empty string otherwise
	echo validation_errors();
	
	$attrs = array('class' => 'well form-search', 'id' => 'edit_user');
	echo form_open('', $attrs);

	//*
	$class_dd = 'class="user_change_rolle_dd" id="user_cr_role"';

	$dropdown_data = array();
	$dropdown_data = $global_data['userdata']['roles'];
	// add as first element
	array_unshift($dropdown_data, 'Bitte auswaehlen');

	echo form_dropdown('user_change_rolle_dd', $dropdown_data, '0', $class_dd);

	$data = array(
			'id' => 'user_cr_search',
			'class' => 'search-query',
			'name' => 'search_user',
			'placeholder' => 'Benutzer suchen'
		);
	echo form_input($data);

	echo form_close();
?>

<table id="user_overview" class="table table-striped table-bordered table-condensed">
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

<script>
	(function() {

		//// edit_user

		// hide_all_submit_buttons();

		// request for all users
		ajax_request_user_by_search();

		$('#user_cr_role').change(function() {
			ajax_request_user_by_stdgang($(this));
		});

		$('#user_cr_search').keyup(function() {
			ajax_request_user_by_search($(this));
		});

		// $("table tbody").on("submit", 'input[name="action_to_perform"]', function(e) {
		// 	e.preventDefault();
		// 	alert("asdf");
		// });

		// $("table tbody").on("submit", "tr", function(e) {
		// 	e.preventDefault();
		// 	console.log(this);
		// 	alert($('input[type="submit"]:focus'));
		// 	// $('input[name="action_to_perform"]').val()
		// });

	})(); // self envoked anonymous function

	function ajax_request_user_by_stdgang(parent) {
		$("table#user_overview tbody").html("<img src=\"<?php echo base_url();?>assets/pics/loadinganim.gif\" />");
		$.get(
			"<?php echo site_url();?>admin/ajax_show_user/",
			'role_id='+parent.val(),
		function(response) {
			// 
			$('table#user_overview tbody').html(response);
			// hide the submit buttons again
			// hide_all_submit_buttons();
		});
	}

	function ajax_request_user_by_search(parent) {
		var url = "<?php echo site_url();?>admin/ajax_show_user/";
		var data = "";

		// for calling this method without parameters
		if (!parent) {
			data = 'role_id='+$('#user_cr_role option:selected').val();

			$.get(
			url,
			data,
			function(response) {
				// 
				$('table#user_overview tbody').html(response);
				// hide the submit buttons again
				// hide_all_submit_buttons();
			});
		} else {
			if (parent.val().length == 0) { // load all users of selected std
				data = 'role_id='+$('#user_cr_role option:selected').val();

				$.get(
				url,
				data,
				function(response) {
					// 
					$('table#user_overview tbody').html(response);
					// hide the submit buttons again
					// hide_all_submit_buttons();
				});
			} else if (parent.val().length >= 2) { // start to search when when two letters were entered
				data = 'searchletter='+parent.val()+'&role_id='+$('#user_cr_role option:selected').val();

				$.get(
				url,
				data,
				function(response) {
					// 
					$('table#user_overview tbody').html(response);
					// hide the submit buttons again
					// hide_all_submit_buttons();
				});
			}
		}
	}

	function hide_all_submit_buttons() {
		$("input#save, input#pw_reset").hide();
	}
	function show_save_button(c) {
		c.find("#save, #pw_reset").show();
	}
	function hide_save_button(c) {
		c.find("#save, #pw_reset").hide();
	}

</script>

