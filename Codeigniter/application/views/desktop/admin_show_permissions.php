<h2>Benutzerrechte anzeigen</h2>

<h3><?php echo $global_data['userdata']['username']; ?></h3>

<?php
foreach ($global_data['userdata']['userpermissions'] as $zeile) {
	echo $zeile;
	echo br();
}
?>

<div id ="menuefooter">
	<div id="menuefooter_button">
		<div id="toggle_button" class="up">O</div>
	</div>

	<div id="menuefooter_content">
		<p>
		Challenges
		I’ve had the pleasure to consult and collaborate with large multidisciplinary teams, on projects ranging from brand strategy and communication, interaction design, web design, motion and visual communication. Think you might have a challenge for me? Then I would love to hear from you.
		</p>
		<p>
		Challenges
		I’ve had the pleasure to consult and collaborate with large multidisciplinary teams, on projects ranging from brand strategy and communication, interaction design, web design, motion and visual communication. Think you might have a challenge for me? Then I would love to hear from you.
		</p>
		<p>
		Challenges
		I’ve had the pleasure to consult and collaborate with large multidisciplinary teams, on projects ranging from brand strategy and communication, interaction design, web design, motion and visual communication. Think you might have a challenge for me? Then I would love to hear from you.
		</p>
	</div>
</div>

<script>

(function() {

	$("div#toggle_button").click(function() {
		var menue = "div#menuefooter";
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
				height: 500
			}, 500, function() {
				// alert("animation ready");
			})
			.addClass("open");
		}
	});

})();

</script>