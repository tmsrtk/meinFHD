<div id="messages" class="alert alert-<?php print $state; ?> multiple">
	<a class="close" data-dismiss="alert" href="#">&times;</a>
	<ul>
	<?php foreach ($messages as $message) : ?>
		<li><?php $message['message']; ?></li>
	<?php endforeach; ?>
	</ul>
</div>