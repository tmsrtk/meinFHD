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
	   Bitte pr&uuml;fe die eingelesenen Daten auf Verarbeitungsfehler und passe diese ggf. manuell an.</p>

    <form id="submit-edit" accept-charset="utf-8" method="get" action="<?php print base_url();?>admin/stdplan_edit/" class="edittimetable">
		<input type="hidden" value="<?php echo $ids[0].'_'.$ids[1].'_'.$ids[2]; ?>" name="timetable_id" />
		<input class="btn btn-warning" type="submit" value="<?php echo $ids[0].' '.$ids[1].' - '.$ids[2] ?> bearbeiten" name="savestdplanchanges" />
	</form>
    <a href="<?php print base_url('/admin/show_timetable_import'); ?>" class="btn btn-info">Eingelesener Stundenplan ist okay</a>
    <hr/>

    <?php if($created_courses): # if there were added some more courses during the parsing process show the result?>
    <h2>Hinzugef&uuml;gte Kurse</h2>
    <p>
        Die im Folgenden aufgelisteten Kurse wurden w&auml;hrend des Parsing-Prozesses angelegt (in die Tabelle 'studiengangkurs' eingef&uuml;gt), da sie noch nicht in der Datenbank
        vorhanden waren bzw. kein zu den im XML-File beschriebenen Kursen passender Kurs in der Datenbank gefunden wurde. Bitte &uuml;berpr&uuml;fe die Kurse und erg&auml;nze
        die fehlenden Informationen.
    </p>
    <br/>
    <?php
        // print out the added courses
        foreach($created_courses as $detail){
            echo utf8_encode($detail); // encode the string in utf8 to display umlaut´s properly
            echo '<br/>';
        }
    ?>
    <hr/>
    <?php endif; ?>

    <h2>Datens&auml;tze des eingelesenen Stundenplans</h2>
    <p>
        Die im Folgenden aufgelisteten Datens&auml;tze wurden als Stundenplan in der Datenbnak gespeichert.
    </p>
    <br/>
	<?php
        // print out the parsed data
        foreach($parsed_data as $detail){

            // if there is an nested array -> print it out
            if(is_array($detail)){
                echo '<pre>';
                print_r($detail);
                echo '</pre>';
            }
            // print out the detail of the currently view dataset
            else{
                echo utf8_encode($detail); // encode the string in utf8 to display umlaut´s properly
            }
        }
	?>
    <hr/>
    <form id="submit-edit2" accept-charset="utf-8" method="get" action="<?php print base_url();?>admin/stdplan_edit/" class="edittimetable">
        <input type="hidden" value="<?php echo $ids[0].'_'.$ids[1].'_'.$ids[2]; ?>" name="timetable_id" />
        <input class="btn btn-warning" type="submit" value="<?php echo $ids[0].' '.$ids[1].' - '.$ids[2] ?> bearbeiten" name="savestdplanchanges" />
    </form>
    <a href="<?php print base_url('/admin/show_timetable_import'); ?>" class="btn btn-info">Eingelesener Stundenplan ist okay</a>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span2"></div>
<?php endblock(); ?>

<?php end_extend(); ?>

