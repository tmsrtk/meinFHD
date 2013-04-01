<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplanimport - Fehler beim Parsen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>

	<h1>Fehler beim Parsen</h1>
    <p>W&auml;hrend des Einlesens der ausgew&auml;hlten Stundenplan-Datei sind Fehler aufgetreten, die dazu gef&uuml;hrt haben, dass das XML-Dokument nicht
        eingelesen werden konnte. Diese werden im Folgenden aufgeflistet. Bitte pr&uuml;fe das XML-Dokument und starte den Parsing-Prozess dann erneut.
        Das XML-Dokument wurde vom Server gel&ouml;scht und die &Auml;nderungen wurden nicht in die Datenbank &uuml;bernommen.
    </p>
    <hr/>
	<ul>
		<?php 
			foreach($errors as $e){
				if($e != 'errors'){
					echo '<li>'.$e.'</li>';
				}
			}
		?>
	</ul>
    <hr/>
    <a href="<?php print base_url('admin/show_timetable_import'); ?>" class="btn btn-info">Zur&uuml;ck zum Stundenplan-Import</a>
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>

<?php end_extend(); ?>