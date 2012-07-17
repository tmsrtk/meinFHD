<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzer löschen<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

<?php
	// needet vars
	$data_formopen = array('id' => 'delete_user_row');
	$data_submit = array(
		'id'			=> 'delete_user_btn',
		'name'			=> 'delete_user_btn',
		'class'			=> 'btn btn-mini btn-danger delete_user_btn'
	);
	//--------------------------------------------------------------------------
?>
<div class="row-fluid">
	<h2>Benutzer löschen</h2>
</div>
<hr>
<div class="row-fluid">
	<table id="user_overview" class="table table-striped">
		<thead>
			<tr>
				<th>
					<div class="span2">Loginname</div>
					<div class="span2">Nachname</div>
					<div class="span2">Vorname</div>
					<div class="span2">E-Mail</div>
					<div class="span2">Los</div>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($user as $zeile) : ?>
			<tr>
				<td id="content_userrow">
					<?php echo form_open('admin/delete_user/', $data_formopen); ?>
					<?php echo form_hidden('user_id', $zeile['BenutzerID']); ?>

					<div class="span2"><?php echo $zeile['LoginName']; ?></div>
					<div class="span2"><?php echo $zeile['Nachname']; ?></div>
					<div class="span2"><?php echo $zeile['Vorname']; ?></div>
					<div class="span2"><?php echo $zeile['Email']; ?></div>

					<?php echo "<div class=\"span2\">".form_submit($data_submit, 'Löschen')."</div>"; ?>
					<div class="clearfix"></div>
					<?php echo form_close(); ?>
				</td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>



	<a id="modaltest_btn" class="btn" href="">Modal Test</a>
	<div id="modalcontent"></div>

</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

	// prompt dialogs
	$("td#content_userrow").on("click", "input#delete_user_btn", function() {
		$(this).attr("data-clicked", "true");

		var mm = createModalDialog('User löschen', 'Soll der User wirklich gelöscht werden?');
		$("#modalcontent").html(mm);

		$('#myModal').modal('show');
		return false;
	});

	function createModalDialog(title, text) {
		var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
					.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
					.append('<div class="modal-body"><p>'+text+'</p></div>')
					.append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Abbrechen</a><a href="" class="btn btn-primary" data-accept="modal">OK</a></div>');
		return $myModalDialog;
	}

	$("#modalcontent").on( 'click', 'a', function() {
		if ( $(this).attr("data-accept") === 'modal' ) {
			console.log("accept");

			$("input[type=submit][data-clicked=true]").parents("form#delete_user_row").submit();
			$("#content_userrow input#delete_user_btn").removeAttr("data-clicked");
		} else {
			console.log("cancel");
			$("#content_userrow input#delete_user_btn").removeAttr("data-clicked");
		}

		return false;
	});


	/* helper functions */
	function hide_all_submit_buttons() {
		$("input#delete_user_btn").hide();
	}

	function show_delete_button(c) {
			c.find("#delete_user_btn").show();
	}
		function hide_delete_button(c) {
			c.find("#delete_user_btn").hide();
	}
<?php endblock(); ?>
<?php end_extend(); # end extend main template ?>