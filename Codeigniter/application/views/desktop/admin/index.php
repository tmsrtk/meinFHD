<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Admin<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
			<div class="well well-small admin">
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
			</div>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>