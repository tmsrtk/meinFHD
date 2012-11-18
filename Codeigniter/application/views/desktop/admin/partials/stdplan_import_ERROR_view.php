<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplanimport - Fehler beim Parsen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>
	
<?php
	// general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>

	<h1>Fehler beim Parsen</h1>
	<p>Nachfolgend eine Auflistung der Fehler die dazu führen, dass das xml-Dokument nicht geparst werden kann.</p>

	<ul>
		<?php 
			foreach($errors as $e){
				if($e != 'errors'){
					echo '<li>'.$e.'</li>';
				}
			}
		?>
	</ul>


	<form id="submit-import" accept-charset="utf-8" method="post" action="http://localhost/meinFHD/Codeigniter/admin/stdplan_import">
		<input class="btn btn-info" type="submit" value="Zurück zum Stundenplan-Import" name="importstdplan">
	</form>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>

<?php end_extend(); ?>