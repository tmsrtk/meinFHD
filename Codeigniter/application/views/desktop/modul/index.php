<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Modul<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
				<!-- CONTENT -->
				<h1>Modul View (desktop)</h1>
<?php endblock(); ?>
<?php end_extend(); ?>
