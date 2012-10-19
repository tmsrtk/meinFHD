<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title'); # extend the sites title ?><?php get_extended_block(); ?> - Studienplan<?php endblock();?>

<?php startblock('content'); # content for this view ?>

<?php
	$data_formopen = array('id' => 'studienplan-form');

	$fs_attrs = array(
		'id'	=>	'sendButton',
		'name'	=>	'sendButton',
		'class' =>	'btn btn-success pull-right'
		);
?>

<div class="well well-small admin">

	<?php 
	// if there is no semesterplan, show create possibility
	if ( ! isset($userdata['semesterplan_id'])) : ?>

	<?php echo form_open('/studienplan/studienplanErstellen/') ?>
	<p>Du hast noch keinen Studienplan. Vergewissere Dich, dass du die korrekten Daten eingetragen hast und klicke auf "Studienplan erstellen".</p>
	<?php echo form_submit('create_sp', 'Studienplan erstellen'); ?>
	<?php echo form_close() ?>
	
	<?php else : ?>

	<div class="row-fluid">
		<div class="span4">
			<h1 class="headline">Studienplan</h1>
		</div>
		<div class="span4">
			<p>Studium abgeschlossen zu </p>
			<div class="progress progress-success">
				<div id="study-percent" class="bar" style="width: 0%">0%</div>
			</div>
		</div>
		<div class="span2">
			<p>Durchschnittsnote</p>
			<span id="average-mark" class="badge">0</span>
		</div>
		<div class="span2">
			<div id="studienplan-einstellungen" class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					Einstellungen
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li class="sp-info"><a href="#"><i class="icon-info-sign"></i> Info</a></li>
					<li class="divider"></li>
					<li class="sp-addsem"><a href="#"><i class="icon-plus"></i> Weiteres Semester anlegen</a></li>
					<li class="sp-remsem"><a href="#"><i class="icon-minus"></i> Letztes Semester löschen</a></li>
					<li class="divider"></li>
					<li class="sp-approvesem" data-hasapprovesem="<?php echo $has_approve_sem['HatAnerkennungsSemester'] ?>"><a href="#"><i class="<?php (empty($has_approve_sem['HatAnerkennungsSemester'])) ? print "icon-plus" : print "icon-minus"; ?>"></i> <?php (empty($has_approve_sem['HatAnerkennungsSemester'])) ? print "Anerkennungssemester anlegen" : print "Anerkennungssemester löschen"; ?></a></li>
					<li class="divider"></li>
					<li class="sp-reset"><a href="#"><i class="icon-retweet"></i> Studienplan zurücksetzen</a></li>
				</ul>
			</div>
		</div>
	</div>
	<hr>

	<?php FB::log($studienplan) ?>

	<div class="row-fluid">
		<div id="studienplan" class="span12">
			<?php echo form_open('', $data_formopen); ?>
				<table class="table table-bordered">
					<thead>
						<!-- Head -->
						<tr>
						<?php foreach($studienplan as $semester): ?>
							<?php $i = $laufvar; // semester nr ?>
							<?php $sem_typ = $userdata['studienbeginn_semestertyp'] ?>
							<?php $sem_jahr = $userdata['studienbeginn_jahr'] ?>
							<?php foreach($semester as $modul): ?>
								<?php #if($i != 0) : # Anerkennungssemester ?> 
									<th <?php ($i==$userdata['act_semester']) ? print 'style="background-color: #dee4c5"' : print 'style="background-color: #eee"'; ?> >
										<h4><?php ($i==0)?print 'Anerkennungs Semester':print 'Semester '.$i ?></h4>
										<p style="font-size: 10px; color: #bbb;"><?php ($i==0)?'':print $sem_typ.' '.$sem_jahr ?></p>
									</th>
									<?php 
										(($i+1)%2 == 0) ? $sem_typ = 'SoSe' : $sem_typ = 'WiSe';		// TODO: look for a better algo
										(($i+1)%2 == 0) ? $sem_jahr++ : $sem_jahr;
									?>
								<?php #endif; ?>
								<?php $i++ ?>
							<?php endforeach // $semester ?>
						<?php endforeach // $studienplan ?>
						</tr>
					</thead>
					<tbody>
						<!-- Module -->	
						<tr>
							<?php foreach($studienplan as $semester): ?>
								<?php $i = $laufvar; // semester nr ?>
								<?php foreach($semester as $modul): ?>
									<?php #if($i != 0) : # Anerkennungssemester ?>
										<td <?php if($i==$userdata['act_semester']) echo 'style="background-color: #dee4c5";' ?> >
											<ul id="<?php echo $i ?>" class="unstyled semesterplanspalte">
												<?php foreach($modul as $data): ?>
													<?php if ($data['KursID'] != NULL): ?>
														<li id="module_<?php echo $data['KursID']; ?>">
															<div class="semestermodul dropup" data-kursid="<?php echo $data['KursID']; ?>" data-cp="<?php echo $data['Creditpoints'] ?>" data-sws="<?php echo $data['KursSWSSumme'] ?>">
																<i class="arrw icon-align-justify" data-toggle="dropdown" style="height: 10px; width: 4px;"></i>
																<a class="b_hoeren" href="">T</a>
																<a class="b_pruefen" href="">P</a>
																<ul class="dropdown-menu">
																      <li class="kursinfo"><a href="#">Info</a></li>
																      <li class="divider"></li>
																      <li class="reset-kurs"><a href="#">Resetten</a></li>
																</ul>
	
																<span class="modulfach"><?php echo $data['Kurzname'] ?></span>
																<!-- <span class="modulfach-lang"><?php echo $data['Kursname'] ?></span> -->
																<input class="modulnote input-mini" name="modulnote[]" type="text" value="<?php echo $data['Notenpunkte'] ?>">
															</div>
														</li>
													<?php endif; ?>
												<?php endforeach; // $modul ?>
											</ul>
										</td>
									<?php #endif; ?>
									<?php $i++ ?>
								<?php endforeach; // $semester ?>
							<?php endforeach; // $studienplan ?>
						</tr>
						<!-- SWS/CP -->
						<tr>
							<?php foreach($studienplan as $semester): ?>
								<?php $i = $laufvar; // semester nr ?>
								<?php foreach($semester as $modul): ?>
									<?php #if($i != 0) : # Anerkennungssemester ?>
										<td <?php if($i==$userdata['act_semester']) echo 'style="background-color: #dee4c5";' ?> >
											<p>SWS: <span class="badge badge-success pull-right sws-badge"></span></p>
											<p>CP: <span class="badge badge-info pull-right cp-badge"></span></p>
										</td>
									<?php #endif; ?>
									<?php $i++ ?>
								<?php endforeach; // $semester ?>
							<?php endforeach; // $studienplan ?>
						</tr>
					</tbody>
				</table>

				<hr />

			<?php echo form_submit($fs_attrs, 'Änderungen speichern'); ?>
			<?php echo form_close(); ?>
		</div>
	</div>
	<?php endif ; ?>

</div>

<div id="modalcontent"></div>

<?php #FB::log($userdata['studiengang_data']) ?>

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

	$('h1.headline').ajaxStart(function() {
		$(this).html('Lade...');
	});

	$('h1.headline').ajaxStop(function() {
		$(this).html('Studienplan');
	});

	// initialize the Studienplan var
	var Studienplan = {
			init: function( config ) {
				var self = this;

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
						self._progressChanged();
					}
				});
			},

			initModules : function() {
				var self = this;

				// for each semester
				this.config.semesterplanspalten.each(function(index, semester) {
					// console.log($(semester));

					$semester = $(semester);

					// check if its the zero semester to give some special style
					if ($semester.attr('id') == 0) {
						$semester.parents('tbody').find('tr:first-child td:first-child').addClass('zero-sem-bg');
						$semester.parents('table').find('tr:first-child th:first-child').addClass('zero-sem-bg');
						$semester.parents('tbody').find('tr:last-child td:first-child').addClass('zero-sem-bg');
					}	

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
							if (status == '1') { hoeren.addClass('b_active'); }
						});

						self._getModuleHoerenPruefenStatus(self._getModuleKursId($module), 'pruefen').done(function(status) {
							if (status == '1') { pruefen.addClass('b_active'); }
						});
					});
				});
				this._progressChanged();
			},

			initModulNotenInputs : function() {
				var self = this;

				// trigger .blur on enter functionality 
				this.config.modulNotenInputs.keydown(function(event) {
				    if(event.keyCode == 13) {
				      event.preventDefault();
				      $(this).blur();
				    }
				});

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
					hoeren = self._getModuleHoerenButton($module);
					pruefen = self._getModulePruefenButton($module);
					// hoerenAndPruefen = module.find('a.b_hoeren, a.b_pruefen');

					// if there is a mark and the mark is validated, remove old colors
					if ( mark && self._validateUserInput(mark) == true ) {
						self._removeModuleColorClass($module);
						// hide hoeren/pruefen
						hoeren.hide();
						pruefen.hide();

						// animate progress bar
						self._progressChanged();

					} else if ( mark && self._validateUserInput(mark) == false ) {
						$this.val('');

						hoeren.show();
						pruefen.show();

						self._removeModuleColorClass($module);

						// create and show modal
						_showModal('Falscher Wert!', 'Tragen Sie bitte in das Feld einen Wert zwischen 0 - 100 ein.', false);
						self._progressChanged();
					} else {
						hoeren.show();
						pruefen.show();

						self._removeModuleColorClass($module);
						self._progressChanged();
					}

					// get color for the mark
					self._addModuleMarkColor($module);


				});

			},

			

			initSendButton : function() {
				var self = this;

				console.log();

				this.config.sendButton.click(function() {

					$(this).attr("data-clicked", "true");
					_showModal('Änderungen speichern', "<?php echo Messages::SAVE_STUDIENPLAN ?>", true, true, self);

					// self._saveSemesterplan().done(function() {
					// 	location.reload();
					// });
					return false;
				});
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
							_showModal('Keine VL!', 'Für dieses Modul wird keine Vorlesung in diesem Semester angeboten!');
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
					_showModal(
							self.getTitleForModule(kursId).done(function(result) {
								self.setModuleTitle(result);
						}), 
							self.getTextForModule(kursId).done(function(result) {
								self.setModuleText(result);
							}));
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
				addSem = this.config.semesterplanEditCtxMenu.find('li.sp-addsem');
				remSem = this.config.semesterplanEditCtxMenu.find('li.sp-remsem');
				approveSem = this.config.semesterplanEditCtxMenu.find('li.sp-approvesem');
				reset = this.config.semesterplanEditCtxMenu.find('li.sp-reset');

				info.click(function() {
					title = "<?php echo $userdata['studiengang_data']['StudiengangName'] ?>";
					text = '<p>Regelsemester: '+"<?php echo  $userdata['studiengang_data']['Regelsemester'] ?>"+'</p>...';
					_showModal(
						title,
						text
						);
				});

				// add new coloumn
				addSem.click(function() {
					return $.ajax({
					  url: "<?php echo site_url();?>studienplan/spalteEinfuegen"
					}).promise().done(function() {
						self._saveSemesterplan().done(function() {
							// reload page
							location.reload();
						});
					});
				});


				// delete the last semester
				remSem.click(function() {
					// before delete
					// - check if actual semester sum is equal to regular
					// if so, no deleting possible

					regelSemester = "<?php echo $userdata['studiengang_data']['Regelsemester'] ?>";
					// console.log(regelSemester);
					// return;

					if ( regelSemester != self.config.semesterplanspalten.length ) {
						// - check if there are any modules in the last semester
						// get last semester, and appropriate modules
						lastSem = self.config.semesterplanspalten.last().find('.semestermodul');
						var modulesInLastSemester = false;
						lastSem.each(function(index, module) {
							// if there are modules, no way to delete the last sem 
							// -> show modal with a hint to clear the last semester from modules
							if (module) {
								modulesInLastSemester = true;
								return;
							}
						});
						// if there were any modules, no decreasing possible
						if ( modulesInLastSemester === true ) {
							_showModal(
							'Letztes Semester nicht leer', 
							'Im letzten Semester befinden sich noch Module, bitte entfernen Sie diese!');
						} else {
							// decrease semesteranzahl in db
							return $.ajax({
							  url: "<?php echo site_url();?>studienplan/spalteLoeschen"
							}).promise().done(function() {
								self._saveSemesterplan().done(function() {
									// reload page
									location.reload();
								});
							});
						}
					} else {
						_showModal('Nicht möglich', 'Die Regelstudienzeit für diesen Studiengang beträgt '+regelSemester+' Semester. Keine weitere Verkürzung möglich.')
					}

					



					// // hook in document, to wait until all requests are finished
					// $(window).ajaxStop(function() {
					// 	// location.reload();
					// 	console.log("ready reset");
					// 	self._saveSemesterplan(); // endlosschleife hier, weil ajaxStop jedes mal nach _saveSemesterplan() neu aufgerufen wird
					// });

					


					// location.reload();
					
					// if so, reset them

					// otherwise, ajax request to decrease the semester sum from "semesterplan"

					// reload

				});

				// add the approve sem
				approveSem.click(function() {

					if ($(this).data("hasapprovesem") == 1) {

						apprSem = self.config.semesterplanspalten.first().find('.semestermodul');
						var modulesInApproveSem = false;
						apprSem.each(function(index, module) {
							// if there are modules, no way to delete the last sem 
							// -> show modal with a hint to clear the last semester from modules
							if (module) {
								modulesInApproveSem = true;
								return;
							}
						});
						// if there were any modules, no decreasing possible
						if ( modulesInApproveSem === true ) {
							_showModal(
							'Anerkennungs - Semester nicht leer!', 
							'Im Anerkennungs - Semester befinden sich noch Module, bitte verschieben Sie diese!');
						} else {


							return $.ajax({
							  url: "<?php echo site_url();?>studienplan/delete_approve_sem"
							}).promise().done(function() {
								self._saveSemesterplan().done(function() {
									// reload page
									location.reload();
								});
							});
						}
						
					} else {
						return $.ajax({
						  url: "<?php echo site_url();?>studienplan/create_approve_sem"
						}).promise().done(function() {
							self._saveSemesterplan().done(function() {
								// reload page
								location.reload();
							});
						});
					}

					
				});

				// reset the studienplan
				reset.click(function() {

					// modal, yes or no
					_showModal('Studienplan resetten', 'Alle Kurse werden zurückgesetzt und Sie werden aus allen Gruppen ausgetragen! Sicher?', true);

					// if there are any click listener, remove them
					$('#modalcontent').off('click');
					// add new
					$("#modalcontent").on( 'click', 'button, a', function(event) {
						event.preventDefault();

						if ( $(this).attr("data-accept") === 'modal' ) {
							console.log("accept");

							$(event.target).parent().parent().find("div.modal-body").html("Bitte warten, der Befehl wird ausgeführt");
							$(event.target).parent().parent().find("div.modal-footer").hide();

							// complete reset with all dependencies
							return $.ajax({
							  url: "<?php echo site_url();?>studienplan/studienplanRekonstruieren"
							}).promise().done(function() {
								// reload page
								location.reload();
							});

						} else {
							console.log("cancel");
						}

					});


					// complete reset with all dependencies
					// return $.ajax({
					//   url: "<?php echo site_url();?>studienplan/studienplanRekonstruieren"
					// }).promise().done(function() {
					// 	// reload page
					// 	location.reload();
					// });

					// // old version, where the studienplan is resetting live, but not everything in the db
					// self.config.semesterplanspalten.each(function(index, semester) {
					// 	// cache semester
					// 	$semester = $(semester);

					// 	// modules per semester
					// 	$semestermodule = $semester.find('.semestermodul');

					// 	// for each module in a semester
					// 	($semestermodule).each(function(index, module) {
					// 		// cache jquery object of each module
					// 		$module = $(module);
					// 		// reset the actual module
					// 		self._resetModule($module);
					// 	});
					// });
				});
			},

			

			// helper methods --------------------------------------------------------------------------

			_progressChanged : function () {
				var self = this

    				//////////////////
				// PROGRESS BAR //
    				//////////////////

				// calculate new % value
					// query all modules
					// count them
					// count modules in studienplan which have inputs and >= 50
					// calculate

				allModules = $('.semestermodul')
					modulesSum = allModules.length
					counter = 0

					markSum = 0
					counter2 = 0

				allModules.each(function (index, el) {
					// percent of study stuff
					if (self._getModuleMark($(el)) >= 50) 
						counter++

					// average mark stuff
					markSum += self._getModuleMark($(el))
					if (self._getModuleMark($(el)) != 'NULL' ) { counter2 += 100 };
					// console.log(markSum)

					// sws/cp stuff
					
				})

				percent = counter/modulesSum*100
				percent = Math.round(percent)

				// console.log(percent)

				// add new % value to progress bar
				self.config.progressBar.animate({width: percent+'%'}, 300)
				self.config.progressBar.text(percent+'%')

   				//////////////////
				// AVERAGE MARK //
    				//////////////////

    				averageMarkT = markSum/counter2*100
    				self.config.averageMark.fadeOut(600).text(Math.round(averageMarkT)).fadeIn(600)

    				//////////////////
				//   SWS / CP   //
    				//////////////////

    				var swsArray = []
    				var cpArray = []
    				this.config.semesterplanspalten.each(function (i, e) {
    					var swsSum = 0
    					var cpSum = 0
    					// run through each e
    					$(e).find('.semestermodul').each(function (i, e) {
    						// count sws of every module
    						swsSum += $(e).data('sws')
    						cpSum += $(e).data('cp')
    						// console.log(e)
    						
    					})
					swsArray.push(swsSum)
					cpArray.push(cpSum)
    				})

				// add to swsBadges
				// console.log(this.config.swsBadges)
				this.config.swsBadges.each(function (i, e) {
					$(e).fadeOut(600).text(swsArray[i]).fadeIn(600)
				})
				this.config.cpBadges.each(function (i, e) {
					$(e).fadeOut(600).text(cpArray[i]).fadeIn(600)
				})

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
				if ( mark <= 100 && mark >= 0 ) {
				return true;
			}
				else return false;
			},

			// _validateUserInput : function(mark) {
			// 	if ( mark == '1' || mark == '1-' || mark == '2+'
			// 		|| mark == '2' || mark == '2-' || mark == '3+'
			// 		|| mark == '3' || mark == '3-' || mark == '4+'
			// 		|| mark == '4' || mark == '4-' || mark == '5' ) { 
			// 	return true;
			// }
			// 	else return false;
			// },

			_checkSemesterEquality : function(actSem, regSem) {
				regEven = false;
				actEven = false;

				(regSem%2 == 0) ? regEven = true : regEven = false;
				(actSem%2 == 0) ? actEven = true : actEven = false;

				if ( regEven === actEven ) return true; else return false;

			},

			_resetModule : function(module) {
				// reset input field
				// module.find('input.modulnote').val('');
				this._setModuleMark(module, '');

				// reset hoeren/pruefen
				hoerenAndPruefen = module.find('a.b_hoeren, a.b_pruefen');
				hoerenAndPruefen.each(function(index, elem) {
					$elem = $(elem);
					if ( $elem.hasClass('b_active') == false ) $elem.addClass('b_active');
					$elem.show();
				});
				// remove colors
				this._removeModuleColorClass(module);

				kursId = 'kursid='+this._getModuleKursId(module);
				var actSem = this._getModuleActSemester(module);
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
				// return (isNaN(parseInt(mark, 10))?0:parseInt(mark, 10));
				return mark;
			},
			_setModuleMark : function(module, mark) {
				module.find('.modulnote').val(mark);
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
				if ( mark <= 100 && mark >= 90 ) {
					return 'sm_green';
				} else if ( mark < 90 && mark >= 75) {
					return 'sm_yellow-green';
				} else if ( mark < 75 && mark >= 60) {
					return 'sm_yellow'
				} else if ( mark < 60 && mark >= 50 ) {
					return 'sm_orange';
				} else if ( mark && mark < 50) {
					return 'sm_red';
				} else {
					return '';
				}
			},

			// _getModuleMarkColorClass : function( mark ) {
			// 	if ( mark == '1' || mark == '1-' ) {
			// 		return 'sm_green';
			// 	} else if ( mark == '2+' || mark == '2' || mark == '2-' ) {
			// 		return 'sm_yellow-green';
			// 	} else if ( mark == '3+' || mark == '3' || mark == '3-' ) {
			// 		return 'sm_yellow'
			// 	} else if ( mark == '4+' || mark == '4' || mark == '4-' ) {
			// 		return 'sm_orange';
			// 	} else if ( mark == '5') {
			// 		return 'sm_red';
			// 	} else {
			// 		return '';
			// 	}
			// },
			_removeModuleColorClass : function(module) {
				// possible classes
				colors = ['sm_green', 'sm_yellow-green', 'sm_yellow', 'sm_orange', 'sm_red'];

				$.each(colors, function(index, val) {
					if (module.hasClass(val)) { module.removeClass(val) };
				});
			},

			_saveSemesterplan : function() {
				var dfd = $.Deferred();

				var self = this;
				// semester run var
				var i = 1;

				var count = this.config.semesterplanspalten.length;
				// if there is a zero semester, count -1 !
				if (this.config.semesterplanspalten.first().attr("id") == 0) {
					i = 0;
				}

				var successfulDbWritings = 1;

				// trigger manually, cause of _resetModule()
				this.config.semesterplanspalten.sortable( "refreshPositions" );

				// for each semester
				(this.config.semesterplanspalten).each(function(index, semester) {

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
					    console.log("count: "+count+" | i: "+successfulDbWritings);
					    if (count === successfulDbWritings) dfd.resolve();
					    successfulDbWritings++;
					  },
					  error: function(xhr, textStatus, errorThrown) {
					    //called when there is an error
					  }
					});

					i++;
				});

				return dfd.promise();
			}
		};


		
		Studienplan.init({
			semesterplanspalten: $('.semesterplanspalte')
			, connectWithColumns: '.semesterplanspalte'
			, sendButton : $('#sendButton')
			, resetButton : $('#resetStudienPlan')
			, pruefenButtons : $('a.b_pruefen')
			, hoerenButtons : $('a.b_hoeren')
			, modalContent : $('#myModal') //existiert an dieser stelle noch nicht!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! deswegen funzt oben nicht
			, modalWrapper : $('#modalcontent')
			, infoButtonsWrapper : $('ul.dropdown-menu')
			, modulNotenInputs : $('.semestermodul input.modulnote')
			, semesterplanEditCtxMenu : $('#studienplan-einstellungen')
			, progressBar : $('#study-percent')
			, averageMark : $('#average-mark')
			, swsBadges : $('span.sws-badge')
			, cpBadges : $('span.cp-badge')

		});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>