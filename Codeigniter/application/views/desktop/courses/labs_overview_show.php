<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Praktikumsverwaltung - Überblick<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
	//general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>
		<!-- TODO: run through array of courses and groups and print as table -->
		
		<div>
			<pre>
				<?php print_r($sp_course_details); ?>
			</pre>
		</div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
<?php endblock(); ?>

<?php end_extend(); ?>