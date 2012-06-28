<h3>Semesterplan</h3>




<?php //FB::log($global_data['studienplan']); ?>

<div id="studienplan">

<table>

<tbody>
	<tr>
		<?php foreach($studienplan as $semester): ?>
			<?php $i = 0; // semester value ?>

			<?php //TODO: Zero semester ausblenden!! ?>
		    <?php foreach($semester as $modul): ?>
		    <td>
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
			</td>
		    <?php $i++ ?>
		    <?php endforeach; ?>
		<?php endforeach; ?>
	</tr>
</tbody>

</table>



</div>



<script>

(function() {

	var Studienplan = {
		init: function( config ) {
			this.config = config; 
			this.initJQUIsortable();
		},

		initJQUIsortable: function() {
			var self = this;
			this.config.sortableColumns.sortable({
				connectWith: self.config.connectWithColumns,
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
					var module_serialisiert = $(this).sortable("serialize");

					// hänge auch die semesternr an die url
					var semester = $(this).attr('id');
					module_serialisiert+='&semester='+semester;

					// DEBUG:
					console.log(module_serialisiert);

					// ajax request to save the new module orders
					$.ajax({
						type: 'GET',
						url: "<?php echo site_url();?>ajax/schreibe_reihenfolge_in_db/", 
						data: module_serialisiert, 
						success: function(response) {
							// entferne wieder den roten Rahmen wenn request erfolgreich
							$(ui.item).children(".semestermodul").toggleClass("highlight");
						}
					});
				},

				// beim Draggen UND JEDER Veränderung
				change: function(event, ui) {

				},

				// beim Start des Draggens
				start: function(event, ui) {

				},

				// beim Stop des Draggends
				stop: function(event, ui) {
					
				}
			});
		}

	};

	Studienplan.init({
		sortableColumns: $(".semesterplanspalte"),
		connectWithColumns: '.semesterplanspalte'
	});

})();




</script>




