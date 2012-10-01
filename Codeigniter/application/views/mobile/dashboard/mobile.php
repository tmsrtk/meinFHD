<?php extend('base/template.php'); # extend main template ?>

	<?php startblock('title'); # extend the site's title ?>
		<?php get_extended_block(); ?> - Übersicht
	<?php endblock();?>
	
	<?php startblock('content'); # content for this view ?>
		
		<div class="span4 well">
			<a href="<?php print base_url('stundenplan'); ?>" class="mfhd-btn-block-primary">Stundenplan</a>
			<a href="<?php print base_url('studienplan'); ?>" class="mfhd-btn-block-primary">Studienplan</a>
			<a href="<?php print base_url('dashboard'); ?>" class="mfhd-btn-block-primary">Dashboard</a>
			<a href="<?php print base_url('veranstaltungen'); ?>" class="mfhd-btn-block-primary">Meine Kurse</a>
			<a href="<?php print base_url('logout'); ?>" class="mfhd-btn-block-inverse">Abmelden</a>
		</div>
					
	<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>