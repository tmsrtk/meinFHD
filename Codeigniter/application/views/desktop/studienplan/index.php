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
									<ul id="<?php echo $i ?>" class="unstyled semesterplanspalte">
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

<!-- Test für speichern der Modulreihenfolge -->
<?php $fs_attrs = array(
	'id'	=>	'sendButton',
	'name'	=>	'sendButton',
	'class' =>	'btn btn-success'
	); ?>
<?php echo form_submit($fs_attrs, 'Los'); ?>

<button name="resetStudienPlan" id="resetStudienPlan" class="btn btn-warning" >Reset</button>

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
				this.config.changedModulesHistory = new Array();
				this.initJQUIsortable();
				this.initSendButton();
				this.initResetButton();
				this.initBtnPruefen();
			},
			
			initJQUIsortable: function() {

				var self = this;
				this.config.sortableColumns.sortable({
					connectWith: self.config.connectWithColumns,
					cursor: 'pointer',
					opacity: '0.6',
					placeholder: 'semestermodul_placeholder',
					dropOnEmpty: true,
					tolerance: 'pointer',
					revert : 'true',
	
					// hier findet das Schreiben in die Datenbank statt
					// jedes Mal wenn das Draggen aufgehört hat UND es eine Veränderung
					// in der Reihenfolge gibt
					update: function(event, ui) {


						// Färbe das Modul mit einem roten Rahmen ein um zu zeigen
						// das ein Request ausgeführt wird
						// $(ui.item).children(".semestermodul").toggleClass("highlight");
	
						// serialisiere die Modulreihenfolge
						module_serialisiert = $(this).sortable("serialize");
	
						// hänge auch die semesternr an die url
						semester = $(this).attr('id');
						module_serialisiert+='&semester='+semester;

						// array mit allen befehlen zwischenspeichern, beim klicken auf submit, wird dieses
						// array als post übertragen? und auf der serverseite für jeden array eintrag 
						// die db ausgeführt
						
						

						self.config.changedModulesHistory.push(module_serialisiert);


						// DEBUG:
						// console.log(changedModulesHistory);

						// kein ajax!
						// return;

						//---------------------------------------------------------------------------------


	
						// ajax request to save the new module orders
						// $.ajax({
						// 	type: 'GET',
						// 	url: "<?php echo site_url();?>ajax/schreibe_reihenfolge_in_db/", 
						// 	data: module_serialisiert, 
						// 	success: function(response) {
						// 		// entferne wieder den roten Rahmen wenn request erfolgreich
						// 		$(ui.item).children(".semestermodul").toggleClass("highlight");
						// 	}
						// });
					}
				});
			},

			initSendButton : function() {
				var self = this;

				this.config.sendButton.click(function() {

					// create an array of the saved Object where the history of the changes is stored
					test = $.makeArray(self.config.changedModulesHistory);

					// for each element, fire an ajax request to save the new orders
					$.each(test , function(index, value) {
						console.log(index+" "+value);

						$.ajax({
						  url: "<?php echo site_url();?>ajax/schreibe_reihenfolge_in_db/",
						  type: 'GET',
						  data: value,
						  complete: function(xhr, textStatus) {
						    //called when complete
						  },
						  success: function(data, textStatus, xhr) {
						    console.log("success");
						  },
						  error: function(xhr, textStatus, errorThrown) {
						    //called when there is an error
						  }
						});
						
					} );
					
				});


				// this.config.sendButton.click(function() {
				// 	$.ajax({
				// 			type: 'GET',
				// 			url: "<?php echo site_url();?>ajax/save_changes/", 
				// 			data: self.config.changedModulesHistory, 
				// 			success: function(response) {
				// 				console.log(response);
				// 			}
				// 		});
				// });
				return false;
			},

			initResetButton : function() {
				var self = this;

				this.config.resetButton.click(function() {

					self.config.sortableColumns.sortable( "cancel" );
					self.config.changedModulesHistory = new Array();

					return false;
				});
			},

			initBtnPruefen : function() {

				// console.log(this.config.btnPruefen);

				$(this.config.btnPruefen).each(function(index, elem) {

					var self = this;

					// console.log($(elem).parent().attr('data-kursid'));

					value = 'kursid='+$(elem).parent().attr('data-kursid');

					$.ajax({
					  url: "<?php echo site_url();?>ajax/check_status_pruefung/",
					  type: 'GET',
					  data: value,
					  complete: function(xhr, textStatus) {
					    //called when complete
					  },
					  success: function(data, textStatus, xhr) {
					    // (data == '1') ? console.log("true") : console.log("false");
					    (data == '1') ? $(self).addClass('b_active') : console.log("false");
					    // console.log(data);
					  },
					  error: function(xhr, textStatus, errorThrown) {
					    //called when there is an error
					  }
					});
				});

				$(this.config.btnPruefen).click(function() {

					value = 'kursid='+$(this).parent().attr('data-kursid');

					if ( $(this).hasClass('b_active') ) {
						$(this).removeClass('b_active');

						$.ajax({
						  url: "<?php echo site_url();?>ajax/deactivate_status_pruefung/",
						  type: 'GET',
						  data: value,
						  success: function(data, textStatus, xhr) {
						    // (data == '1') ? console.log("true") : console.log("false");
						    // (data == '1') ? $(self).addClass('b_active') : console.log("false");
						    console.log("deactivated");
						  }
						});

					} else {
						$(this).addClass('b_active');

						$.ajax({
						  url: "<?php echo site_url();?>ajax/activate_status_pruefung/",
						  type: 'GET',
						  data: value,
						  success: function(data, textStatus, xhr) {
						    // (data == '1') ? console.log("true") : console.log("false");
						    // (data == '1') ? $(self).addClass('b_active') : console.log("false");
						    console.log("activated");
						  }
						});
					}

					return false;
				});
			}
		};
		
		Studienplan.init({
			sortableColumns: $(".semesterplanspalte"),
			connectWithColumns: '.semesterplanspalte',
			sendButton : $('#sendButton'),
			resetButton : $('#resetStudienPlan'),
			btnPruefen : $('a.b_pruefen')
		});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>