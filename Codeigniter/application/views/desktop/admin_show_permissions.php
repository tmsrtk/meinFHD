<h2>Benutzerrechte anzeigen</h2>

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

<script>

(function() {

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

})();

</script>