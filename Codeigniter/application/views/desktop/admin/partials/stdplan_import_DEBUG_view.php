<hr>
<hr>
<hr>
<h1>Details zum geparsten Stundenplan</h1>
<p>Wenn du das hier liest, dann wurde das Skript zumindest ohne Fehler gelesen.</p>
<p>Wichtig: Auf Verarbeitungsfehler prüfen:</p>

<form id="submit-edit" accept-charset="utf-8" method="post" action="http://localhost/meinFHD/Codeigniter/admin/stdplan_edit">
	<input type="hidden" value="<?php echo $ids[0].'_'.$ids[2].'_'.$ids[1]; ?>" name="stdplan_id">
	<input class="btn btn-warning" type="submit" value="<?php echo $ids[0].'-'.$ids[1].'-'.$ids[2] ?> überprüfen!" name="savestdplanchanges">
</form>

<!--<p>Bei Fehlern löschen und xml überprüfen => </p>-->

<!--<form id="submit-delete" accept-charset="utf-8" method="post" action="http://localhost/meinFHD/Codeigniter/admin/delete_stdplan">
	<input id="<?php // echo $ids[0].'-'.$ids[1].'-'.$ids[2] ?>" class="btn-danger delete-stdplan" type="submit" value="Stundenplan loeschen" name="delete_sdtplan">
	<input type="hidden" value="<?php // echo $ids[0]; ?>" name="stdplan_abk">
	<input type="hidden" value="<?php // echo $ids[1]; ?>" name="stdplan_po">
	<input type="hidden" value="<?php // echo $ids[2]; ?>" name="stdplan_semester">
</form>-->

