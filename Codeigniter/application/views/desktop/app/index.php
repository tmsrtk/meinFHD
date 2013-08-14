<?php extend('base/template.php'); # extend main template ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
			<div class="span3"></div>
			<div class="span6">
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
			</div><!-- /.span6-->
			<div class="span3"></div>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>