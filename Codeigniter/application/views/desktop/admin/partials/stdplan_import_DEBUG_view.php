<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplanimport - Fehler beim Parsen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span2"></div>
	<div class="span8 well well-small">
<?php endblock(); ?>
	
<?php
	// general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>

	<h1>Details zum geparsten Stundenplan</h1>
	<p>Wenn du das hier liest, dann wurde das Skript zumindest ohne Fehler gelesen.</p>
	<p>Wichtig: Auf Verarbeitungsfehler prüfen:</p>

	<form id="submit-edit" accept-charset="utf-8" method="post" action="http://localhost/meinFHD/Codeigniter/admin/stdplan_edit">
		<input type="hidden" value="<?php echo $ids[0].'_'.$ids[2].'_'.$ids[1]; ?>" name="stdplan_id">
		<input class="btn btn-warning" type="submit" value="<?php echo $ids[0].'_'.$ids[1].'_'.$ids[2] ?> überprüfen!" name="savestdplanchanges">
	</form>
	
	<?php 
		// print parsed data
		foreach($data as $details){
			foreach($details as $d){
				// if there's an array inside 
				if(is_array($d)){
					echo '<pre>';
					print_r($d);
					echo '</pre>';
				} else {
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

