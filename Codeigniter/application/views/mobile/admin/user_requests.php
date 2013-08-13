<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Einladungsverwaltung<?php endblock(); ?>
<?php startblock('content'); # additional markup before content ?>
<div class="row-fluid">
	<h2>Übersicht aller Einladungsanforderungen</h2>
    <p>Hier werden alle noch ausstehenden Einladungs-Anforderungen aufgelistet.</p>
</div>
<hr>
<div class="row-fluid">
    <?php if($user_invitations): # display the table with the invitations, only if there are at least on existing request?>
	<table class="table">
		<thead>
            <tr>
                <th class="span1">Anfragende globale Benutzer-ID auf der Blacklist?</th>
                <th class="span2">Nachname</th>
                <th class="span2">Vorname</th>
                <th class="span4">E-Mail</th>
                <th class="span2">Funktion</th>
                <th class="span1">Los</th>
            </tr>
		</thead>
    </table>
    <?php foreach ($user_invitations as $key => $value) : # for each invitation render a separate table -> only one form per table is w3c valid!?>
    <?php # form setup
        $data_formopen2 = array('id' => 'accept_invitation' . $value['AnfrageID']);
        $data_dropdown = array('Erstellen', 'L&ouml;schen');
        $attrs_dropdown = 'id="user_function' . $value['AnfrageID'] . '" class="input-xlarge"';
        $submit_data = array(
            'id' 			=> 'save' . $value['AnfrageID'],
            'name'			=> 'los',
            'class'			=> 'btn btn-mini btn-danger'
        );
    ?>
    <?php echo form_open('admin/create_user_from_invitation_requests/', $data_formopen2); ?>
    <?php echo form_hidden('request_id', $value['AnfrageID']); ?>
    <table class="table table-striped-custom">
		<tbody>
            <tr>
                <td class="span1">
                    <?php echo ($value['FHD_IdP_UID']) ? 'Ja' : 'Nein'; ?>
                </td>
                <td class="span2">
                    <?php echo $value['Nachname']; ?>
                </td>
                <td class="span2">
                    <p><?php echo $value['Vorname']; ?></p>
                </td>
                <td class="span4">
                    <?php echo $value['Emailadresse']; ?>
                </td>
                <td class="span2">
                    <?php echo form_dropdown('user_function', $data_dropdown, '0', $attrs_dropdown);?>
                </td>
                <td class="span1">
                    <?php echo form_submit($submit_data, 'Los'); ?>
                </td>
            </tr>
		</tbody>
	</table>
    <?php echo form_close(); ?>
    <?php endforeach ?>
    <?php else: ?>
    <p>
        Es liegen keine offenen Benutzeranfragen vor.
    </p>
    <?php endif; ?>
</div>

<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

<?php endblock(); ?>

<?php end_extend(); ?>