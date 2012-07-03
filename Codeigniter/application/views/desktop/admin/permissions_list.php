<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Benutzerrechte anzeigen<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
<div class="row-fluid">
	<h2>Benutzerrechte anzeigen</h2>
</div>
<hr>
<h3><?php echo $userdata['loginname']; ?></h3>

<?php
foreach ($userdata['userpermissions'] as $zeile) {
	echo $zeile;
	echo br();
}
?>

<div id ="menuefooter">
	<div id="menuefooter_button">
		<div id="toggle_button" class="up">Menue</div>
	</div>

	<div id="menuefooter_content">
		<div id="mf_content">
			<h2>Praktika</h2>
			<button class="btn btn-large btn-success">OOP1</button>
			<button class="btn btn-large btn-success">DBS1</button>
			<button class="btn btn-large btn-success">MedGest1</button>
		</div>
	</div>
</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>


	$("div#toggle_button").click(function() {
		var menue_button = "div#menuefooter_content";

		if ( $(menue_button).hasClass("open") ) {
			$(menue_button).removeClass("open")
			.animate({
				height: 0
			}, 500, function() {
				// alert("animation ready");
				$(menue_button).hide();
			});
		} else {
			$(menue_button).show();
			$(menue_button).animate({
				height: 200
			}, 500, function() {
				// alert("animation ready");
			})
			.addClass("open");
		}
	});
<?php endblock(); ?>

<?php end_extend(); ?>