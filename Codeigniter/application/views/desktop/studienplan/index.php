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
														<input class="modulnote input-mini" name="modulnote" type="text" value="<?php echo $data['Notenpunkte'] ?>">
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

<!-- <button name="resetStudienPlan" id="resetStudienPlan" class="btn btn-warning" >Reset</button> -->

<div id="modalcontent"></div>

<?php endblock();?>

<?php startblock('customFooterJQueryCode');?>

	// save php vars in js vars
	// <?php $test = "test"; ?>
	// var testjs = "<?php echo $test; ?>";
	// console.log( testjs );


	// -------------------------------------------------------------------------


	

	// -------------------------------------------------------------------------

	var Studienplan = {
			init: function( config ) {
				this.config = config;
				this.config.changedModulesHistory = [];

				this.setupAjax();
				this.initJQUIsortable();
				this.initSendButton();
				// this.initResetButton();
				this.initBtnPruefen();
				this.initBtnHoeren();
				this.initInfoButtons();
			},

			setupAjax : function() {
				$.ajaxSetup({
					type : 'GET'
				});
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
	
					// for each change of modules order, save the serialized get url
					update: function(event, ui) {
						// serialize the module order
						module_serialisiert = $(this).sortable("serialize");
	
						// additionally add the semester number to the get url
						semester = $(this).attr('id');
						module_serialisiert+='&semester='+semester;

						// save each order change, in the global "history" variable
						self.config.changedModulesHistory.push(module_serialisiert);

					},

					stop : function(event, ui) {
						value = 'kursid='+ui.item.find('.semestermodul').attr('data-kursid');
						// semester = $(this).attr('id');
						semester = ui.item.parent().attr('id');
						$selfBtnHoeren = ui.item.find('.b_hoeren');

						// console.log(value);
						// console.log(semester);
						// return;

						self.checkIfModuleHasVl(value).done(function(result) {

							console.log(result);
							console.log(semester);

							if ( semester == result  ) {
								
								console.log("ist im richtigen Semester");

								// if actual button has the b_active class, turn it off
								// if ( $selfBtnHoeren.hasClass('b_active') ) {
								// 	$selfBtnHoeren.removeClass('b_active');

								// 	$.ajax({
								// 	  url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
								// 	  data: value,
								// 	  success: function(data, textStatus, xhr) {
								// 	    console.log("deactivated");
								// 	  }
								// 	});
								// } else { // otherwise, turn it on
								// 	$selfBtnHoeren.addClass('b_active');

								// 	$.ajax({
								// 	  url: "<?php echo site_url();?>ajax/activate_status_hoeren/",
								// 	  data: value,
								// 	  success: function(data, textStatus, xhr) {
								// 	    console.log("activated");
								// 	  }
								// 	});
								// }
							} else {
								console.log("ist im falschen Semester");

								if ( $selfBtnHoeren.hasClass('b_active') ) {
									$selfBtnHoeren.removeClass('b_active');

									$.ajax({
									  url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
									  data: value,
									  success: function(data, textStatus, xhr) {
									    console.log("deactivated");
									  }
									});
								}
							}
						});
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
						
					});

					// reset the history array
					self.config.changedModulesHistory = [];

					//self.initResetButton();	// TODO: funzt noch nicht, soll nach einem speichern, das reseten nicht mehr erlauben, bzw auch nen ajax
											// request ausführen, der das dann entsprechen zurücksetzt
					
				});
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
				// get the status for this button | on / off ?
				$(this.config.btnPruefen).each(function(index, elem) {

					// cache the button
					var self = this;

					// get the kursid of the module	
					value = 'kursid='+$(elem).parent().attr('data-kursid');

					// ajax request, to get the status for this button | on / off ?
					$.ajax({
					  url: "<?php echo site_url();?>ajax/check_status_pruefung/",
					  data: value,
					  complete: function(xhr, textStatus) {
					    //called when complete
					  },
					  success: function(data, textStatus, xhr) {
					    (data == '1') ? $(self).addClass('b_active') : console.log("false");
					  },
					  error: function(xhr, textStatus, errorThrown) {
					    //called when there is an error
					  }
					});
				});

				// click function to turn on or off
				$(this.config.btnPruefen).click(function() {

					value = 'kursid='+$(this).parent().attr('data-kursid');

					// if actual button has the b_active class, turn in off
					if ( $(this).hasClass('b_active') ) {
						$(this).removeClass('b_active');

						$.ajax({
						  url: "<?php echo site_url();?>ajax/deactivate_status_pruefung/",
						  data: value,
						  success: function(data, textStatus, xhr) {
						    console.log("deactivated");
						  }
						});
					} else { // otherwise, turn it on
						$(this).addClass('b_active');

						$.ajax({
						  url: "<?php echo site_url();?>ajax/activate_status_pruefung/",
						  data: value,
						  success: function(data, textStatus, xhr) {
						    console.log("activated");
						  }
						});
					}
					return false;
				});
			},

			initBtnHoeren : function() {
				var self = this;

				// get the status for this button | on / off ?
				$(this.config.btnHoeren).each(function(index, elem) {

					// cache the button
					var self = this;

					// get the kursid of the module	
					value = 'kursid='+$(elem).parent().attr('data-kursid');

					// ajax request, to get the status for this button | on / off ?
					$.ajax({
					  url: "<?php echo site_url();?>ajax/check_status_hoeren/",
					  data: value,
					  complete: function(xhr, textStatus) {
					    //called when complete
					  },
					  success: function(data, textStatus, xhr) {
					    (data == '1') ? $(self).addClass('b_active') : console.log("false");
					  },
					  error: function(xhr, textStatus, errorThrown) {
					    //called when there is an error
					  }
					});
				});

				// click function to turn on or off
				$(this.config.btnHoeren).click(function() {
					var $selfBtnHoeren = $(this);

					value = 'kursid='+$(this).parent().attr('data-kursid');

					// in which semester is our module at the moment?
					semester = $(this).parent().parent().parent().attr('id');

					// check if this module has vl for this semester
					self.checkIfModuleHasVl(value).done(function(result) {
						if ( semester == result  ) {
							console.log("ist im richtigen Semester");

							// if actual button has the b_active class, turn it off
							if ( $selfBtnHoeren.hasClass('b_active') ) {
								$selfBtnHoeren.removeClass('b_active');

								$.ajax({
								  url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
								  data: value,
								  success: function(data, textStatus, xhr) {
								    console.log("deactivated");
								  }
								});
							} else { // otherwise, turn it on
								$selfBtnHoeren.addClass('b_active');

								$.ajax({
								  url: "<?php echo site_url();?>ajax/activate_status_hoeren/",
								  data: value,
								  success: function(data, textStatus, xhr) {
								    console.log("activated");
								  }
								});
							}
						} else {
							console.log("ist im falschen Semester");
						}
					});

					
					return false;
				});
			},

			checkIfModuleHasVl : function(moduleId) {
				return $.ajax({
					url: "<?php echo site_url();?>ajax/check_status_hoeren_vl/",
					data: moduleId
				}).promise();
			},

			initInfoButtons : function() {
				var self = this;

				this.config.infoButtonsWrapper.on('click', 'li.kursinfo', function() {

					kursId = $(this).parent().parent().attr('data-kursid');

					var mm = self.createModalDialog(
						self.getTitleFromModule(kursId).done(function(result) {
							self.setModuleTitle(result);
					}), 
						self.getTextFromModule(kursId).done(function(result) {
							self.setModuleText(result);
						}));

					self.config.moduleModalWrapper.html(mm);

					$('#myModal').modal({ // self.config.modalContent.modal({ ... not working, why??  s. u. Kommentar!!!
						keyboard: false
					}).on('hide', function () {

					}).modal('show');

				});
			},

			createModalDialog : function(title, text) {
				myModalDialog = 
					$('<div class="modal hide" id="myModal"></div>')
					.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
					.append('<div class="modal-body"><p>'+text+'</p></div>')
					.append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Abbrechen</a><a href="" class="btn btn-primary" data-accept="modal">OK</a></div>');
				return myModalDialog;
			},

			getTitleFromModule : function(moduleId) {
				ajaxData = 'moduleid='+moduleId;

				// ajax request to get the full title from server
				return $.ajax({
					url: "<?php echo site_url();?>ajax/get_module_title/", 
					data: ajaxData 
				}).promise();

			},

			setModuleTitle : function(response) {
				$('div.modal-header h3').html(response);
			},

			getTextFromModule : function(moduleId) {
				ajaxData = 'moduleid='+moduleId;

				// ajax request to get the full text from server
				return $.ajax({
					url: "<?php echo site_url();?>ajax/get_module_text/",
					data: ajaxData,
				}).promise();
			},

			setModuleText : function(response) {
				$('div.modal-body p').html(response);
			}


		};


		
		Studienplan.init({
			sortableColumns: $(".semesterplanspalte"),
			connectWithColumns: '.semesterplanspalte',
			sendButton : $('#sendButton'),
			resetButton : $('#resetStudienPlan'),
			btnPruefen : $('a.b_pruefen'),
			btnHoeren : $('a.b_hoeren'),
			modalContent : $('#myModal'), //existiert an dieser stelle noch nicht!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! deswegen funzt nicht oben
			moduleModalWrapper : $('#modalcontent'),
			infoButtonsWrapper : $('ul.dropdown-menu')
		});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>