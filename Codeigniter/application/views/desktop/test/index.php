<?php extend('base/template.php'); # extend main template ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
			<div class="well well-small">
				<?php FB::log($this); ?>
				<pre>krumo($this):</pre>
				<?php krumo($this); ?>
			</div>
			<div class="row-fluid">
				<div class="span4"></div>
				<div class="span8">
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
				</div><!-- /.span8-->
			</div>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>
