<h3>Semesterplan</h3>








<?php /*
<?php for ($i=1; $i<=$count_semester; $i++ ) { ?>
	<ul id="semesterplansemester_<?php echo $i ?>" class="unstyled semesterplanspalte">
		<?php $varname = 'modules_order_sem_'.$i; ?>
		<?php foreach(${$varname} as $modul): ?>
			<li id="module_<?php echo $modul['KursID'] ?>">
				<div class="semestermodul btn btn-success btn-large">
					<span class="modulfach"><?php echo $modul['kurs_kurz'] ?></span>
					<span class="modulfachnote">NP:</span>
					<input id="modulnote" class="input-small" name="modulnote" type="text" value="55" size="3">
				</div>
			</li>
		<?php endforeach ?>
	</ul>
<?php } ?>
*/ ?>



<?php //FB::log($global_data['studienplan']); ?>


<?php foreach($global_data['studienplan'] as $semester): ?>
	<?php $i = 0; ?>
    <?php foreach($semester as $modul): ?>
    
		<ul id="<?php echo $i ?>" class="unstyled semesterplanspalte">
	        <?php foreach($modul as $data): ?>
	        <?php if ($data['Kurzname'] != NULL): ?>
	    	<li id="module_<?php echo $data['KursID']; ?>">
	    		<div class="semestermodul btn btn-success btn-large">
					<span class="modulfach"><?php echo $data['Kurzname'] ?></span>
					<span class="modulfachnote">NP:</span>
					<input id="modulnote" class="input-small" name="modulnote" type="text" value="<?php echo $data['Notenpunkte'] ?>" size="3">
				</div>
	    	</li>
	    	<?php endif; ?>
	        <?php endforeach; ?>
	    </ul>
	
    <?php $i++ ?>
    <?php endforeach; ?>
<?php endforeach; ?>










<script>
	(function() {

		$(".semesterplanspalte").sortable({
			connectWith: '.semesterplanspalte',
			cursor: 'pointer',
			opacity: '0.6',
			placeholder: 'semestermodul btn btn-warning btn-large',
			dropOnEmpty: true,

			// hier findet das Schreiben in die Datenbank statt
			// jedes Mal wenn das Draggen aufgehört hat UND es eine Veränderung
			// in der Reihenfolge gibt
			update: function(event, ui) {

				
				// Färbe das Modul mit einem roten Rahmen ein um zu zeigen
				// das ein Request ausgeführt wird
				$(ui.item).children(".semestermodul").toggleClass("highlight");

				// serialisiere die Modulreihenfolge
				var module_serialisiert = '';
				module_serialisiert = $(this).sortable("serialize");


				// hänge auch die semesternr an die url
				var semester = $(this).attr('id'); // hier die klasse oder id oder iwas vom spaltenelement rausfinden
				module_serialisiert+='&semester='+semester;

				console.log(module_serialisiert);

				$.get("<?php echo site_url();?>studienplan/modulVerschieben", 
					module_serialisiert, function(response) {
						// entferne wieder den roten Rahmen wenn request erfolgreich
						$(ui.item).children(".semestermodul").toggleClass("highlight");
				});
			},

			// beim Draggen UND JEDER Veränderung
			change: function(event, ui) {
				// console.log($(ui.item).find(".modulfachnote").html(ui.position.left));
				// var module_serialisiert = '';
				// module_serialisiert = $(this).sortable("serialize");
				// console.log(module_serialisiert);
			},

			// beim Start des Draggens
			start: function(event, ui) {
				// $(ui.item).toggleClass("highlight");
			},

			// beim Stop des Draggends
			stop: function(event, ui) {
				
			}
		});
		// wird für das Sortable benötigt
		$(".spalte").disableSelection();
		
		
		
		
		
		// // Animate headline
		// $("body").bind('show', function() {
		// 	$("body").find("h2").each(function(n) {
		// 		var $this = $(this);
		// 		$this.css({
		// 			'marginLeft' : (n==0?-50:-10),
		// 			'opacity' : 0
		// 		});
		// 		setTimeout(function(){
		// 			$this.animate({
		// 				'marginLeft' : 0,
		// 				'opacity' : 1
		// 			}, {
		// 				easing : 'easeOutQuint',
		// 				duration : 1500
		// 			})}, 400*n);
		// 	});
		// }).bind('hide', function() {
		// 	$("body").find("h2").stop(true, true).fadeTo(0,0);
		// });


		// $(".semestermodul").each(function(index, el) {
		// 	setTimeout(function() {
		// 		$(el).fadeOut(3000);
		// 	}, 2000);
		// 	console.log($(el).queue());
		// });

		////Animation - Module
		// (function animateModule() {
		// 	setTimeout(function() {
		// 		animate();
		// 	}, 2000);
		// })();

		// var test = $(".modulfach");
		// console.log(test);
		// (function animate() {
		// 	$.each(test, function(index, modul) {
		// 		setTimeout(function() {
		// 			$(modul).fadeOut(3000);
		// 			//animate();
		// 		}, 2000);
		// 	console.log(modul);
		// 	});
		// })();

		//// Beispielskript
		// $homepage.bind('show',function(){
		// 	$homepage.find('.body-copy,.services').each(function(n){
		// 		var $this=$(this);
		// 		$this.css({
		// 			'marginLeft':(n==0?-50:-10),
		// 			'opacity':0
		// 		});
		// 		setTimeout(function(){
		// 			$this.animate({
		// 				'marginLeft':0,
		// 				'opacity':1
		// 			},{
		// 				easing:'easeOutQuint',
		// 				duration:1500
		// 			})},400*n);
		// 	});
		// }).bind('hide',function(){
		// 	$homepage.find('.body-copy,.services').stop(true,true).fadeTo(0,0);
		// });

		// $homepage.trigger('hide');

	})();
</script>