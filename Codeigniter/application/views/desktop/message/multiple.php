<div id="alertMessages" class="span3">
	<div class="alert alert-<?php print $state; ?> fade in multiple">
		<a class="close" data-dismiss="alert" href="#">&times;</a>
		<ul>
		<?php foreach ($messages as $message) : ?>
			<li><?php $message['message']; ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>