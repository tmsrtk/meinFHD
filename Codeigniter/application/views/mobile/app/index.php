<?php extend('base/template.php'); # extend main template ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
			<div class="span4"></div>
			<div class="span4">
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
			</div><!-- /.span4-->
			<div class="span4"></div>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>