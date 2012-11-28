<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Einladungsverwaltung<?php endblock(); ?>
<?php startblock('content'); # additional markup before content ?>
<div class="row-fluid">
	<h2>Übersicht aller Einladungsanforderungen</h2>
    <p>Hier werden alle noch ausstehenden Einladungs-Anforderungen aufgelistet.</p>
</div>
<hr>
<?php
	$data_formopen2 = array('id' => 'accept_invitation');
	$data_dropdown = array('Erstellen', 'Loeschen');
	$attrs_dropdown = 'id="user_function" class="input-xxlarge"';
	$submit_data = array(
			'id' 			=> 'save',
			'name'			=> 'los',
			'class'			=> 'btn btn-mini btn-danger'
		);
?>
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

	/**
	 * Object which handles the UI functions.
	 * @type {Object}
	 */
	var AdditionalInfo = {
		init : function(config) {
			this.config = config;
			this.bindEvents();
			this.build();

			this.changeToStudent();
		},
		bindEvents : function() {
			context = this;
			this.config.roleInput.change(function() {
				context.toggle_studentdata($(this));
			});
			this.config.erstsemestler.change(function() {
				context.toggle_erstsemestlerdata($(this));
			});
		},
		build : function() {
			// console.log();
			this.toggle_studentdata($("label[for='role']").parent().find("input:checked"));
			this.toggle_erstsemestlerdata($("label[for='erstsemestler']").parent().find("input"));
		},
		toggle_studentdata : function(c) {
			additional_student_data = $("div#studentendaten");

			// c jQuery object of toggle button
			if (c.val() === '5') {
				// show additional student da
				additional_student_data.slideDown('slow');
				this.changeToStudent();
			}
			else {
				additional_student_data.slideUp('slow');
				this.changeToDozent();
			}
		},
		toggle_erstsemestlerdata : function(c) {
			var erstsemestler_data = $("div#erstsemestler");

			if (c.attr('checked')) {
				erstsemestler_data.slideUp('slow');
			}
			else {
				erstsemestler_data.slideDown('slow');
			}
		},
		changeToStudent : function() {
			// this.config.additionalInfoContent = this.studentenInfo;
			this.config.additionalInfoContent.html(this.studentenInfo);
		},
		changeToDozent : function() {
			this.config.additionalInfoContent.html(this.dozentenInfo);
		},
		studentenInfo : "Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist. Die Emailadresse wird für die Kommunikation mit meinFHD, den Dozenten und Studierenden verwendet.",
		dozentenInfo : "Geben Sie bitte hier Ihren vollen Namen an, da dieser für die Kommunikation mit den Studierenden gebraucht wird. Die Emailadresse wird für die Kommunikation mit meinFHD und den Studierenden verwendet. "
	};

	// initialise the object
	AdditionalInfo.init({
		additionalInfoContent : $("div#additional-info"),
		roleInput : $("input[name='role']"),
		erstsemestler : $("input[name='erstsemestler']")
	});


	$("#content_invitations").on("click", "input#save", function() {
		// e.preventDefault();
		// determine which function was selected from the dropdown
		// 0 = erstellen, 1 = löschen
		var user_function =  $(this).parents("form#accept_invitation").find("#user_function").val();

		// console.log(user_function);
		// return false;

		if (user_function === '0') {
			$(this).attr("data-clicked", "true");
			// createDialog('User erstellen', 'Sollen der User wirklich erstellt werden?').dialog("open");
			_showModal('User erstellen', 'Soll der User wirklich erstellt werden?', true);
		} else if (user_function === '1') {
			$(this).attr("data-clicked", "true");
			// createDialog('Einladung löschen', 'Soll die Einladung wirklch gelöscht werden?').dialog("open");
			_showModal('Einladung löschen', 'Soll die Einladung wirklch gelöscht werden?', true);
		} else {

		}

		// prevent default submit behaviour
		return false;
	});



<?php endblock(); ?>

<?php end_extend(); ?>