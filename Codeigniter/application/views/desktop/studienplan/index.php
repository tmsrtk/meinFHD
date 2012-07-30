<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title'); # extend the sites title ?><?php get_extended_block(); ?> - Studienplan<?php endblock();?>

<?php startblock('content'); # content for this view ?>

<?php
	$data_formopen = array('id' => 'studienplan-form');
?>

<div class="well well-small admin">

	<div class="row-fluid">
		<div class="span4">
			<h1>Studienplan</h1>
		</div>
		<div class="span4">
			<p>Studium abgeschlossen zu </p>
			<div class="progress progress-success">
				<div class="bar" style="width: <?php echo $percentage ?>%;"><?php echo $percentage ?>%</div>
			</div>
		</div>
		<div class="span2">
			<p>Durchschnittsnote</p>
			<span class="badge"><?php echo round($averageMark) ?></span>
		</div>
		<div class="span2">
			<div id="studienplan-einstellungen" class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					Einstellungen
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li class="sp-info"><a href="#">Info</a></li>
					<li class="divider"></li>
					<li class="sp-addsem"><a href="#">Weiteres Semester anlegen</a></li>
					<li class="sp-remsem"><a href="#">Letztes Semester löschen</a></li>
					<li class="divider"></li>
					<li class="sp-reset"><a href="#">Studienplan resetten</a></li>
				</ul>
			</div>
		</div>
	</div>
	<hr>

	<div class="row-fluid">
		<div id="studienplan" class="span12">
			<?php echo form_open('', $data_formopen); ?>
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
						<?php foreach($studienplan as $semester): ?>
							<?php $i = 0; // semester nr ?>
							<?php foreach($semester as $modul): ?>
								<?php if($i != 0) : # Anerkennungssemester ?> 
									<th style="background-color: #eee;">
										<h3 style="font-weight: normal;">Semester <?php echo $i ?></h3>
										<p style="font-size: 10px; color: #bbb;">WiSe <?php echo $userdata['studienbeginn_jahr']+$i ?></p>
									</th>
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
										<td <?php if($i==$userdata['act_semester']) echo 'style="border-top:4px solid #acd704";' ?> >
											<ul id="<?php echo $i ?>" class="unstyled semesterplanspalte">
												<?php foreach($modul as $data): ?>
													<?php if ($data['KursID'] != NULL): ?>
														<li id="module_<?php echo $data['KursID']; ?>">
															<div class="semestermodul dropup" data-kursid="<?php echo $data['KursID']; ?>">
																<i class="arrw icon-align-justify" data-toggle="dropdown" style="height: 10px; width: 3px;"></i>
																<a class="b_hoeren" href="">H</a>
																<a class="b_pruefen" href="">P</a>
																<ul class="dropdown-menu">
																      <li class="kursinfo"><a href="#">Info</a></li>
																      <li class="divider"></li>
																      <li class="reset-kurs"><a href="#">Resetten</a></li>
																</ul>
	
																<span class="modulfach"><?php echo $data['Kurzname'] ?></span>
																<input class="modulnote input-mini" name="modulnote[]" type="text" value="<?php echo $data['Notenpunkte'] ?>">
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
						<tr>
							<?php foreach($studienplan as $semester): ?>
								<?php $i = 0; // semester nr ?>
								<?php foreach($semester as $modul): ?>
									<?php if($i != 0) : # Anerkennungssemester ?>
										<td>
											<p>SWS: <span class="badge badge-success pull-right"><?php echo $swsCp[$i]['SWS_Summe'] ?></span></p>
											<hr>
											<p>CP: <span class="badge badge-info pull-right"><?php echo $swsCp[$i]['CP_Summe']?></span></p>
										</td>
									<?php endif; ?>
									<?php $i++ ?>
								<?php endforeach; // $semester ?>
							<?php endforeach; // $studienplan ?>
						</tr>
					</tbody>
				</table>
			<!-- Test für speichern der Modulreihenfolge -->
			<?php $fs_attrs = array(
				'id'	=>	'sendButton',
				'name'	=>	'sendButton',
				'class' =>	'btn btn-success'
				); ?>
			<?php echo form_submit($fs_attrs, 'Los'); ?>
			<?php echo form_close(); ?>
		</div>
	</div>

</div>

<!-- <button name="resetStudienPlan" id="sB" class="btn btn-warning" >Reset</button> -->

<!-- <button name="resetStudienPlan" id="resetStudienPlan" class="btn btn-warning" >Reset</button> -->

<div id="modalcontent"></div>

<?php endblock();?>

<?php startblock('customFooterJQueryCode');?>

	// save php vars in js vars
	// <?php $test = "test"; ?>
	// var testjs = "<?php echo $test; ?>";
	// console.log( testjs );


	// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	// $(document).ready(function() {
	//   $(window).keydown(function(event){
	//     if( (event.keyCode == 13) && (validationFunction() == false) ) {
	//       event.preventDefault();
	//       return false;
	//     }
	//   });
	// });


	// disable "Enter"-key functionality 
	$(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });

	// initialize the Studienplan var
	var Studienplan = {
			init: function( config ) {
				this.config = config;
				// this.config.changedModulesHistory = [];

				this.setupAjax();

				this.initJQUIsortable();

				this.initModules();
				this.initHoerenButtons();
				this.initPruefenButtons();
				this.initModulNotenInputs();
				this.initInfoModalButtons();
				this.initSemesterplanEdit();
				
				this.initSendButton();
				// this.initResetButton();
			},

			setupAjax : function() {
				$.ajaxSetup({
					type : 'GET'
				});
			},
			
			initJQUIsortable: function() {
				var self = this;

				this.config.semesterplanspalten.sortable({
					connectWith: self.config.connectWithColumns,
					cursor: 'pointer',
					opacity: '0.6',
					placeholder: 'semestermodul_placeholder',
					tolerance: 'pointer',
					revert : 'true',
	
					// for each change of modules order, save the serialized get url
					update: function(event, ui) {
						// serialize the module order
						// module_serialisiert = $(this).sortable("serialize");
	
						// additionally add the semester number to the get url
						// semester = $(this).attr('id');
						// module_serialisiert+='&semester='+semester;

						// save each order change, in the global "history" variable
						// self.config.changedModulesHistory.push(module_serialisiert);

					},

					stop : function(event, ui) {
						kursId = 'kursid='+ui.item.find('.semestermodul').attr('data-kursid');
						var semester = ui.item.parent().attr('id');
						$selfhoerenButtons = ui.item.find('.b_hoeren');

						self._getModuleRegSemester(kursId).done(function(result) {
							if ( self._checkSemesterEquality(semester, result) === true ) {
								// console.log("ist im richtigen Semester");
							} else {
								// console.log("ist im falschen Semester");

								if ( $selfhoerenButtons.hasClass('b_active') ) {
									$selfhoerenButtons.removeClass('b_active');
								}
							}

							// ich muss wissen ob das reguläre semester gerade oder ungerade ist
							// wenns es gerade ist, schaltet sich beim modul auf ungeraden semestern das T aus

							// if ( semester == result  ) {
								
							// 	console.log("ist im richtigen Semester");

							// } else {
							// 	console.log("ist im falschen Semester");

								// if ( $selfhoerenButtons.hasClass('b_active') ) {
								// 	$selfhoerenButtons.removeClass('b_active');

								// 	$.ajax({
								// 	  url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
								// 	  data: kursId,
								// 	  success: function(data, textStatus, xhr) {
								// 	    console.log("deactivated");
								// 	  }
								// 	});
								// }
							// }
						});
					}
				});
			},

			initModules : function() {
				var self = this;

				// for each semester
				this.config.semesterplanspalten.each(function(index, semester) {
					// console.log($(semester));

					$semester = $(semester);

					// modules per semester
					semestermodule = $semester.find('.semestermodul');

					// for each module in a semester
					(semestermodule).each(function(index, module) {
						// cache jquery object of each module
						$module = $(module);

						// get mark
						// mark = $module.find('.modulnote').val();
						// get kursid
						// kursId = $module.attr('data-kursid');

						// check mark, add styles
						self._addModuleMarkColor($module);
						// $module.addClass(self._getModuleMarkColorClass(self._getModuleMark($module)));


						// check mark, if avaliable, hide hoeren/pruefen
						hoerenAndPruefen = $module.find('a.b_hoeren, a.b_pruefen');
						if (mark) {
							hoerenAndPruefen.hide();
						}

						// check hoeren/pruefen, add styles
						var hoeren = self._getModuleHoerenButton($module);
						var pruefen = self._getModulePruefenButton($module);
						// var hoeren = $module.find('a.b_hoeren');
						// var pruefen = $module.find('a.b_pruefen');

						self._getModuleHoerenPruefenStatus(self._getModuleKursId($module), 'hoeren').done(function(status) {
							if (status == '1') {
								hoeren.addClass('b_active');
								// self._activateModuleHoerenButton($module);
							}
						});

						self._getModuleHoerenPruefenStatus(self._getModuleKursId($module), 'pruefen').done(function(status) {
							if (status == '1') {
								pruefen.addClass('b_active');
							}
						});
					});
				});
			},

			initModulNotenInputs : function() {
				var self = this;

				// validate function & .blur() -> add classes
				this.config.modulNotenInputs.blur(function() {
					// get input
					$this = $(this);

					// cache appropriate module
					$module = $this.parents('.semestermodul ');
					// get module
					// module = $this.parent();

					// get mark
					mark = self._getModuleMark($module);
					// mark = $this.val();

					// hoeren&pruefen
					var hoeren = self._getModuleHoerenButton($module);
					var pruefen = self._getModulePruefenButton($module);
					// hoerenAndPruefen = module.find('a.b_hoeren, a.b_pruefen');

					colors = ['sm_green', 'sm_yellow-green', 'sm_yellow', 'sm_orange', 'sm_red'];
					// if there is a mark and the mark is validated, remove old colors
					if ( mark && self._validateUserInput(mark) == true ) {
						self._removeModuleColorClass($module);
						// hide hoeren/pruefen
						hoeren.hide();
						pruefen.hide();

					} else if ( mark && self._validateUserInput(mark) == false ) {
						$this.val('');

						hoeren.show();
						pruefen.show();

						self._removeModuleColorClass($module);

						// create and show modal
						mm = self.createModalDialog('Falscher Wert!', 'Tragen Sie bitte in das Feld einen Wert zwischen 1 - 5 ein.');
						self.config.modalWrapper.html(mm);

						$('#myModal').modal({
							keyboard: false
						}).on('hide', function () {
							// console.log("hidden");
						}).modal('show');
					} else {
						hoeren.show();
						pruefen.show();

						self._removeModuleColorClass($module);
					}

					// get color for the mark
					self._addModuleMarkColor($module);
				});

			},

			

			initSendButton : function() {
				var self = this;

				this.config.sendButton.click(function() {
					// semester run var
					var i = 1;

					self.config.semesterplanspalten.sortable( "refreshPositions" );
					// for each semester
					(self.config.semesterplanspalten).each(function(index, semester) {
						var hoerenPruefen = '';
						var mark = '';

						// get in each semester for each module values, if the h/t buttons are klicked or not
						($(semester).find('.semestermodul')).each(function(index, module) {
							// console.log('kursid='+$(module).attr('data-kursid')+'&status='+$(module).find('a.b_hoeren').hasClass('b_active'));

							hoerenModus = 0;
							pruefenModus = 0;
							if ( $(module).find('a.b_hoeren').hasClass('b_active') ) hoerenModus = 1;
							if ( $(module).find('a.b_pruefen').hasClass('b_active') ) pruefenModus = 1;

							mark+='&mark[]='+self._getModuleMark($(module));

							hoerenPruefen+='&hoeren[]='+hoerenModus;
							hoerenPruefen+='&pruefen[]='+pruefenModus;
						});
						// serialize module order values+hoeren/pruefen+semester
						modules = $(semester).sortable("serialize");
						semester = '&semester='+i;

						value = modules+hoerenPruefen+mark+semester;

						// console.log(modules+hoerenPruefen+semester);
						// console.log("-------------------------------------------------------");

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

						i++;
					});

					return false;

					// *************************************** testing **************************************************************************************************************************

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

					self.config.semesterplanspalten.sortable( "cancel" );
					self.config.changedModulesHistory = [];

					return false;
				});
			},

			initPruefenButtons : function() {
				// // get the status for this button | on / off ?
				// $(this.config.pruefenButtons).each(function(index, elem) {

				// 	// cache the button
				// 	var self = this;

				// 	// get the kursid of the module	
				// 	value = 'kursid='+$(elem).parent().attr('data-kursid');

				// 	// ajax request, to get the status for this button | on / off ?
				// 	$.ajax({
				// 	  url: "<?php echo site_url();?>ajax/check_status_pruefung/",
				// 	  data: value,
				// 	  complete: function(xhr, textStatus) {
				// 	    //called when complete
				// 	  },
				// 	  success: function(data, textStatus, xhr) {
				// 	    (data == '1') ? $(self).addClass('b_active') : console.log("false");
				// 	  },
				// 	  error: function(xhr, textStatus, errorThrown) {
				// 	    //called when there is an error
				// 	  }
				// 	});
				// });

				// click function to turn on or off
				$(this.config.pruefenButtons).click(function() {
					// cache appropriate module
					$this = $(this);
					$module = $this.parents('.semestermodul');

					// value = 'kursid='+$(this).parent().attr('data-kursid');

					// if actual button has the b_active class, turn in off
					if ( $this.hasClass('b_active') ) {
						$this.removeClass('b_active');

						// $.ajax({
						//   url: "<?php echo site_url();?>ajax/deactivate_status_pruefung/",
						//   data: value,
						//   success: function(data, textStatus, xhr) {
						//     console.log("deactivated");
						//   }
						// });
					} else { // otherwise, turn it on
						$this.addClass('b_active');

						// $.ajax({
						//   url: "<?php echo site_url();?>ajax/activate_status_pruefung/",
						//   data: value,
						//   success: function(data, textStatus, xhr) {
						//     console.log("activated");
						//   }
						// });
					}
					return false;
				});
			},

			initHoerenButtons : function() {
				var self = this;

				// // get the status for this button | on / off ?
				// $(this.config.hoerenButtons).each(function(index, elem) {

				// 	// cache the button
				// 	var self = this;

				// 	// get the kursid of the module	
				// 	value = 'kursid='+$(elem).parent().attr('data-kursid');

				// 	// ajax request, to get the status for this button | on / off ?
				// 	$.ajax({
				// 	  url: "<?php echo site_url();?>ajax/check_status_hoeren/",
				// 	  data: value,
				// 	  complete: function(xhr, textStatus) {
				// 	    //called when complete
				// 	  },
				// 	  success: function(data, textStatus, xhr) {
				// 	    (data == '1') ? $(self).addClass('b_active') : console.log("false");
				// 	  },
				// 	  error: function(xhr, textStatus, errorThrown) {
				// 	    //called when there is an error
				// 	  }
				// 	});
				// });

				// click function to turn on or off
				this.config.hoerenButtons.click(function() {
					// cache actual hoeren button
					var $this = $(this);
					// cache appropriate module
					$module = $this.parents('.semestermodul');

					// get kursid
					kursId = 'kursid='+self._getModuleKursId($module);

					// in which semester is our module at the moment?
					var actSemester = self._getModuleActSemester($module);
					console.log(actSemester);

					// check if this module has vl for this semester
					self._getModuleRegSemester(kursId).done(function(regSem) {
						if ( self._checkSemesterEquality(actSemester, regSem) === true ) {
							if ( $this.hasClass('b_active') ) {
								$this.removeClass('b_active');
							} else { // otherwise, turn it on
								$this.addClass('b_active');
							}
						} else {
							// no way to turn it on again
							mm = self.createModalDialog('Keine VL!', 'Für dieses Modul wird keine Vorlesung in diesem Semester angeboten!');
							self.config.modalWrapper.html(mm);

							$('#myModal').modal({
								keyboard: false
							}).on('hide', function () {
								// console.log("hidden");
							}).modal('show');
						}

						// if ( actSemester == semester  ) {
						// 	// console.log("ist im richtigen Semester");

						// 	// if actual button has the b_active class, turn it off
						// 	if ( $this.hasClass('b_active') ) {
						// 		$this.removeClass('b_active');

						// 		// $.ajax({
						// 		//   url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
						// 		//   data: value,
						// 		//   success: function(data, textStatus, xhr) {
						// 		//     console.log("deactivated");
						// 		//   }
						// 		// });
						// 	} else { // otherwise, turn it on
						// 		$this.addClass('b_active');

						// 		// $.ajax({
						// 		//   url: "<?php echo site_url();?>ajax/activate_status_hoeren/",
						// 		//   data: value,
						// 		//   success: function(data, textStatus, xhr) {
						// 		//     console.log("activated");
						// 		//   }
						// 		// });
						// 	}
						// } else {
						// 	// console.log("ist im falschen Semester");
						// }
					});

					
					return false;
				});
			},

			

			initInfoModalButtons : function() {
				// cache main object
				var self = this;

				// module-context menu, "Info"
				this.config.infoButtonsWrapper.on('click', 'li.kursinfo', function() {
					$this = $(this);
					$module = $this.parents('.semestermodul');

					kursId = self._getModuleKursId($module);

					// create modal with appropriate module infos
					var mm = self.createModalDialog(
						self.getTitleForModule(kursId).done(function(result) {
							self.setModuleTitle(result);
					}), 
						self.getTextForModule(kursId).done(function(result) {
							self.setModuleText(result);
						}));

					self.config.modalWrapper.html(mm);

					$('#myModal').modal({ // self.config.modalContent.modal({ ... not working, why??  s. u. Kommentar!!!
						keyboard: false
					}).on('hide', function () {
						// console.log("hidden");
					}).modal('show');

				});

				// module-context menu, "Resetten"
				this.config.infoButtonsWrapper.on('click', 'li.reset-kurs', function() {
					// cache
					$this = $(this);
					$module = $this.parents('.semestermodul');

					// reset the module
					self._resetModule($module);

				});
			},

			initSemesterplanEdit : function() {
				var self = this;

				info = this.config.semesterplanEditCtxMenu.find('li.sp-info');
				addsem = this.config.semesterplanEditCtxMenu.find('li.sp-addsem');
				remsem = this.config.semesterplanEditCtxMenu.find('li.sp-remsem');
				reset = this.config.semesterplanEditCtxMenu.find('li.sp-reset');

				reset.click(function() {
					self.config.semesterplanspalten.each(function(index, semester) {
						// cache semester
						$semester = $(semester);

						// modules per semester
						$semestermodule = $semester.find('.semestermodul');

						// for each module in a semester
						($semestermodule).each(function(index, module) {
							// cache jquery object of each module
							$module = $(module);

							self._resetModule($module);
						});
					});
				});
			},

			

			// helper methods --------------------------------------------------------------------------

			// modals ---------------------------------------
			createModalDialog : function(title, text) {
				myModalDialog = 
					$('<div class="modal hide" id="myModal"></div>')
					.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
					.append('<div class="modal-body"><p>'+text+'</p></div>')
					.append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Schließen</a>');
					// <a href="" class="btn btn-primary" data-accept="modal">OK</a></div>
				return myModalDialog;
			},

			getTitleForModule : function(moduleId) {
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

			getTextForModule : function(moduleId) {
				ajaxData = 'moduleid='+moduleId;

				// ajax request to get the full text from server
				return $.ajax({
					url: "<?php echo site_url();?>ajax/get_module_text/",
					data: ajaxData,
				}).promise();
			},

			setModuleText : function(response) {
				$('div.modal-body p').html(response);
			},

			// modules ---------------------------------------

			_validateUserInput : function(mark) {
				if ( mark == '1' || mark == '1-' || mark == '2+'
					|| mark == '2' || mark == '2-' || mark == '3+'
					|| mark == '3' || mark == '3-' || mark == '4+'
					|| mark == '4' || mark == '4-' || mark == '5' ) { 
				return true;
			}
				else return false;
			},

			_checkSemesterEquality : function(actSem, regSem) {
				regEven = false;
				actEven = false;

				(regSem%2 == 0) ? regEven = true : regEven = false;
				(actSem%2 == 0) ? actEven = true : actEven = false;

				if ( regEven === actEven ) return true; else return false;

			},

			_resetModule : function(module) {
				// reset input field
				module.find('input.modulnote').val('');
				// reset hoeren/pruefen
				hoerenAndPruefen = module.find('a.b_hoeren, a.b_pruefen');
				hoerenAndPruefen.each(function(index, elem) {
					$elem = $(elem);
					if ( $elem.hasClass('b_active') == false ) $elem.addClass('b_active');
					$elem.show();
				});
				// remove colors
				colors = ['sm_green', 'sm_yellow-green', 'sm_yellow', 'sm_orange', 'sm_red'];
				$.each(colors, function(index, val) {
					if (module.hasClass(val)) { module.removeClass(val) };
				});
				kursId = 'kursid='+this._getModuleKursId(module);
				var actSem = this._getModuleActSemester(module);
				// actSem = $this.parents('.semesterplanspalte ').attr('id');
				// determine in which semester this this modulee should be regulary
				this._getModuleRegSemester(kursId).done(function( regSem ) {
					// if different from actual semester, move baby
					if ( actSem !== regSem ) {
						module.parent().appendTo($('.semesterplanspalte#'+regSem));
					}
				});
			},

			_getModuleKursId : function(module) {
				kursId = module.attr('data-kursid');
				return kursId;
			},
			_getModuleRegSemester : function(moduleId) {
				return $.ajax({
					url: "<?php echo site_url();?>ajax/check_status_hoeren_vl/",
					data: moduleId
				}).promise();
			},
			_getModuleActSemester : function(module) {
				regSem = module.parents('.semesterplanspalte').attr('id');
				return regSem;
			},
			_getModuleMark : function(module) {
				mark = module.find('.modulnote').val();
				return mark;
			},
			_addModuleMarkColor : function(module) {
				module.addClass( this._getModuleMarkColorClass(this._getModuleMark(module)) );
			},
			_getModuleHoerenButton : function(module) {
				return module.find('a.b_hoeren');
			},
			_getModulePruefenButton : function(module) {
				return module.find('a.b_pruefen');
			},
			// _activateModuleHoerenButton : function(module) {
			// 	hoeren = this._getModuleHoerenButton(module);
			// 	console.log(hoeren);
			// 	hoeren.addClass('b_active');
			// },

			_getModuleHoerenPruefenStatus : function(kursId, what) {
				value = 'kursid='+kursId;

				return $.ajax({
					url: "<?php echo site_url();?>ajax/check_status_"+what,
					data: value
				}).promise();
			},

			_getModuleMarkColorClass : function( mark ) {
				if ( mark == '1' || mark == '1-' ) {
					return 'sm_green';
				} else if ( mark == '2+' || mark == '2' || mark == '2-' ) {
					return 'sm_yellow-green';
				} else if ( mark == '3+' || mark == '3' || mark == '3-' ) {
					return 'sm_yellow'
				} else if ( mark == '4+' || mark == '4' || mark == '4-' ) {
					return 'sm_orange';
				} else if ( mark == '5') {
					return 'sm_red';
				} else {
					return '';
				}
			},
			_removeModuleColorClass : function(module) {
				// possible classes
				colors = ['sm_green', 'sm_yellow-green', 'sm_yellow', 'sm_orange', 'sm_red'];

				$.each(colors, function(index, val) {
					if (module.hasClass(val)) { module.removeClass(val) };
				});
			}
		};


		
		Studienplan.init({
			semesterplanspalten: $(".semesterplanspalte"),
			connectWithColumns: '.semesterplanspalte',
			sendButton : $('#sendButton'),
			resetButton : $('#resetStudienPlan'),
			pruefenButtons : $('a.b_pruefen'),
			hoerenButtons : $('a.b_hoeren'),
			modalContent : $('#myModal'), //existiert an dieser stelle noch nicht!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! deswegen funzt oben nicht
			modalWrapper : $('#modalcontent'),
			infoButtonsWrapper : $('ul.dropdown-menu'),
			modulNotenInputs : $('.semestermodul input.modulnote'),
			semesterplanEditCtxMenu : $('#studienplan-einstellungen')

		});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>