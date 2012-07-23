<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title'); # extend the sites title ?><?php get_extended_block(); ?> - Studienplan<?php endblock();?>

<?php startblock('content'); # content for this view ?>

<?php
	$data_formopen = array('id' => 'studienplan-form');
?>

<div class="row-fluid">
	<h2>Studienplan</h2>
</div>
<hr>

<div id="studienplan" class="well">
	<?php echo form_open('studienplan/save_changes', $data_formopen); ?>
		<table>
			<thead>
				<tr>
				<?php foreach($studienplan as $semester): ?>
					<?php $i = 0; // semester nr ?>
					<?php foreach($semester as $modul): ?>
						<?php if($i != 0) : # Anerkennungssemester ?> 
							<th>Semester <?php echo $i ?></th>
						<?php endif; ?>
						<?php $i++ ?>
					<?php endforeach // $semester ?>
				<?php endforeach // $studienplan ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($studienplan as $semester): ?>
						<?php $i = 0; // semester nr ?>
						<?php foreach($semester as $modul): ?>
							<?php if($i != 0) : # Anerkennungssemester ?>
								<td>
									<ul id="semester_<?php echo $i ?>" class="unstyled semesterplanspalte">
										<?php foreach($modul as $data): ?>
											<?php if ($data['KursID'] != NULL): ?>
												<li id="module_<?php echo $data['KursID']; ?>">
													<div class="semestermodul dropup" data-kursid="<?php echo $data['KursID']; ?>">
														<i class="arrw icon-chevron-up" data-toggle="dropdown"></i>
														<a class="b_pruefen" href="">P</a>
														<a class="b_hoeren" href="">H</a>
														<ul class="dropdown-menu">
														      <li class="kursinfo"><a href="#">Info</a></li>
														      <li><a href="#"><?php echo $data['KursID']; ?></a></li>
														      <li><a href="#">Something else here</a></li>
														      <li class="divider"></li>
														      <li><a href="#">Separated link</a></li>
														</ul>

														<span class="modulfach"><?php echo $data['Kurzname'] ?></span>
														<input class="modulnote input-mini" name="modulnote" type="text" value="<?php echo $data['Notenpunkte'] ?>" size="3">
													</div>
												</li>
											<?php endif; ?>
										<?php endforeach; // $modul ?>
									</ul>
								</td>
							<?php endif; ?>
							<?php $i++ ?>
						<?php endforeach; // $semester ?>
					<?php endforeach; // $studienplan ?>
				</tr>
			</tbody>
		</table>
	<?php echo form_close(); ?>
</div>

<div id="modalcontent"></div>

<?php endblock();?>

<?php startblock('customFooterJQueryCode');?>

	// save php vars in js vars
	<?php $test = "test"; ?>
	var testjs = "<?php echo $test; ?>";
	console.log( testjs );


	// -------------------------------------------------------------------------

	// prompt dialogs
	$("ul.dropdown-menu").on("click", "li.kursinfo", function() {

		// console.log();
		// return;

		kursId = $(this).parent().parent().attr('data-kursid');

		var mm = createModalDialog(getTitleFromModule(kursId), getTextFromModule(kursId));
		$("#modalcontent").html(mm);

		$('#myModal').modal({
			keyboard: false
		}).on('hide', function () {

		}).modal('show');
		
	});

	/**  */
	function createModalDialog(title, text) {
		var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
					.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
					.append('<div class="modal-body"><p>'+text+'</p></div>')
					.append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Abbrechen</a><a href="" class="btn btn-primary" data-accept="modal">OK</a></div>');
		return $myModalDialog;
	}

	/** */
	function getTitleFromModule(moduleId) {
		// console.log(moduleId);

		ajaxData = 'moduleid='+moduleId;

		// ajax request to get the full title from server
		$.ajax({
			type: 'GET',
			url: "<?php echo site_url();?>ajax/get_module_title/", 
			data: ajaxData, 
			success: function(response) {
				setModuleTitle(response);
			}
		});

		// while the ajax request is responsing, show some placeholder text
		return 'Wird geladen...';
	}

	/** */
	function setModuleTitle(response) {
		$('div.modal-header h3').html(response);
	}

	/** */
	function getTextFromModule(moduleId) {
		ajaxData = 'moduleid='+moduleId;

		// ajax request to get the full text from server
		$.ajax({
			type: 'GET',
			url: "<?php echo site_url();?>ajax/get_module_text/", 
			data: ajaxData, 
			success: function(response) {
				setModuleText(response);
			}
		});

		// while the ajax request is responsing, show some placeholder text
		return 'Wird geladen...';
	}

	/** */
	function setModuleText(response) {
		$('div.modal-body p').html(response);
	}


	/**  */
	$("#modalcontent").on( 'click', 'button, a', function(event) {

	});

	// -------------------------------------------------------------------------

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
					placeholder: 'semestermodul_placeholder',
					dropOnEmpty: true,
	
					// hier findet das Schreiben in die Datenbank statt
					// jedes Mal wenn das Draggen aufgehört hat UND es eine Veränderung
					// in der Reihenfolge gibt
					update: function(event, ui) {

						// kein ajax!
						return;

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

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>