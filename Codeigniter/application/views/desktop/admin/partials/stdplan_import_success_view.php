<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplanimport - Einlesen der XML-Datei war erfolgreich<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span2"></div>
	<div class="span8 well well-small">
<?php endblock(); ?>
<?php startblock('content'); # additional markup before content ?>

	<h1>Stundenplan wurde erfolgreich importiert</h1>
	<br/>
    <p>Das Einlesen des Stundenplans war erfolgreich. Folgender Stundenplan wurden eingelesen und in der Datenbank gespeichert:<br/>
        <strong>Studiengang:&nbsp;</strong><?php echo $ids[0]; ?><br/>
        <strong>Semester:&nbsp;</strong><?php echo $ids[1]; ?><br/>
        <strong>PO-Version:&nbsp;</strong><?php echo $ids[2]; ?><br/>
	   Bitte pr&uuml;fe die eingelesenen Daten auf Verarbeitungsfehler und passe diese bei Fehlern ggf. manuell an.</p>

    <form id="submit-edit" accept-charset="utf-8" method="get" action="<?php print base_url();?>admin/stdplan_edit/" class="edittimetable">
		<input type="hidden" value="<?php echo $ids[0].'_'.$ids[1].'_'.$ids[2]; ?>" name="timetable_id" />
		<input class="btn btn-warning" type="submit" value="<?php echo $ids[0].' '.$ids[1].' - '.$ids[2] ?> bearbeiten" name="savestdplanchanges" />
	</form>
    <a href="<?php print base_url('/admin/show_timetable_import'); ?>" class="btn btn-info">Eingelesener Stundenplan ist okay</a>
    <hr/>
    <h2>Datens&auml;tze des eingelesenen Stundenplans</h2>
    <br/>
	<?php 
		// print out the parsed data
		foreach($data as $details){

            foreach($details as $d){

				// if there's an nested array -> print it out
				if(is_array($d)){
					echo '<pre>';
					print_r($d);
					echo '</pre>';
				}
                // print out the detail of the currently viewed dataset
                else {
					echo $d;
				}
			}
		}
	?>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span2"></div>
<?php endblock(); ?>

<?php end_extend(); ?>

