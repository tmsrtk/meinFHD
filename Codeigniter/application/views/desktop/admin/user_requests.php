<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Einladungsverwaltung<?php endblock(); ?>
<?php startblock('content'); # additional markup before content ?>
<div class="row-fluid">
	<h2>Übersicht aller Einladungsanforderungen</h2>
    <p>Hier werden alle noch ausstehenden Einladungs-Anforderungen aufgelistet.</p>
</div>
<hr>
<div class="row-fluid">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>
					<div class="span2">Anfragende globale Benutzer-ID auf der Blacklist?</div>
					<div class="span2"><strong>Nachname</strong></div>
					<div class="span2"><strong>Vorname</strong></div>
					<div class="span2"><strong>E-Mail</strong></div>
					<div class="span2"><strong>Funktion</strong></div>
					<div class="span2"><strong>Los</strong></div>
				</th>
			</tr>
		</thead>
		<tbody id="content_invitations">
			<?php foreach ($user_invitations as $key => $value) : ?>
                <?php # form setup
                $data_formopen2 = array('id' => 'accept_invitation' . $value['AnfrageID']);
                $data_dropdown = array('Erstellen', 'Löschen');
                $attrs_dropdown = 'id="user_function' . $value['AnfrageID'] . '" class="input-xxlarge"';
                $submit_data = array(
                    'id' 			=> 'save' . $value['AnfrageID'],
                    'name'			=> 'los',
                    'class'			=> 'btn btn-mini btn-danger'
                );
                ?>
			<tr>
				<td>
					<?php echo form_open('admin/create_user_from_invitation_requests/', $data_formopen2); ?>
					<?php echo form_hidden('request_id', $value['AnfrageID']); ?>

					<div class="span2"><?php echo ($value['FHD_IdP_UID']) ? 'Ja' : 'Nein'; ?></div>
					<div class="span2"><?php echo $value['Nachname']; ?></div>
					<div class="span2"><?php echo $value['Vorname']; ?></div>
					<div class="span2"><?php echo $value['Emailadresse']; ?></div>

					<?php echo "<div class=\"span2\">".form_dropdown('user_function', $data_dropdown, '0', $attrs_dropdown)."</div>"; ?>
					<?php echo "<div class=\"span1\">".form_submit($submit_data, 'LOS!')."</div>"; ?>
					<div class="clearfix"></div>
					<?php echo form_close(); ?>
				</td>
			</tr>
			<?php endforeach ?>
				
		</tbody>
	</table>
</div>

<div id="modalcontent"></div>

<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

	$("#content_invitations").on("click", "input#save", function() {
		// determine which function was selected from the dropdown
		// 0 = erstellen, 1 = löschen
        var user_function =  $(this).parents("form[id^=accept_invitation]").find("[id^=user_function]").val();

		if (user_function === '0') {
			$(this).attr("data-clicked", "true");
			_showModal('User erstellen', 'Soll der User wirklich erstellt werden?', true);
		} else if (user_function === '1') {
			$(this).attr("data-clicked", "true");
			_showModal('Einladung löschen', 'Soll die Einladung wirklch gelöscht werden?', true);
		} else {

		}

		// prevent default submit behaviour
		return false;
	});

<?php endblock(); ?>

<?php end_extend(); ?>