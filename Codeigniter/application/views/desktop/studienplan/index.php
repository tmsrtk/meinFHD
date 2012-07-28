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
	<?php echo form_open('', $data_formopen); ?>
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
			</tbody>
		</table>
	<!-- Test für speichern der Modulreihenfolge -->
	<?php $fs_attrs = array(
		'id'	=>	'sBu',
		'name'	=>	'sendButton',
		'class' =>	'btn btn-success'
		); ?>
	<?php # echo form_submit($fs_attrs, 'Los'); ?>
	<?php echo form_close(); ?>
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


	$(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });


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

				this.config.sortableColumns.sortable({
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
						semester = ui.item.parent().attr('id');
						$selfBtnHoeren = ui.item.find('.b_hoeren');

						self._getSemesterForModule(kursId).done(function(result) {
							regEven = false;
							actEven = false;

							(result%2 == 0) ? regEven = true : regEven = false;
							(semester%2 == 0) ? actEven = true : actEven = false;

							if ( regEven === actEven ) {
								// console.log("ist im richtigen Semester");
							} else {
								// console.log("ist im falschen Semester");

								if ( $selfBtnHoeren.hasClass('b_active') ) {
									$selfBtnHoeren.removeClass('b_active');
								}
							}

							// ich muss wissen ob das reguläre semester gerade oder ungerade ist
							// wenns es gerade ist, schaltet sich beim modul auf ungeraden semestern das T aus

							// if ( semester == result  ) {
								
							// 	console.log("ist im richtigen Semester");

							// } else {
							// 	console.log("ist im falschen Semester");

								// if ( $selfBtnHoeren.hasClass('b_active') ) {
								// 	$selfBtnHoeren.removeClass('b_active');

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
				(this.config.sortableColumns).each(function(index, semester) {
					// console.log($(semester));

					$semester = $(semester);

					// modules per semester
					semestermodule = $semester.find('.semestermodul');

					// for each module in a semester
					(semestermodule).each(function(index, modul) {
						$modul = $(modul);

						// get mark
						mark = $modul.find('.modulnote').val();
						// get kursid
						kursId = $modul.attr('data-kursid');

						// check mark, add styles
						$modul.addClass(self._addMarkColor(mark));

						// check mark, if avaliable, hide hoeren/pruefen
						hoerenAndPruefen = $modul.find('a.b_hoeren, a.b_pruefen');
						if (mark) {
							hoerenAndPruefen.hide();
						}

						// check hoeren/pruefen, add styles
						var hoeren = $modul.find('a.b_hoeren');
						var pruefen = $modul.find('a.b_pruefen');

						self._checkStatusHoerenPruefen(kursId, 'hoeren').done(function(status) {
							if (status == '1') {
								hoeren.addClass('b_active');
							}
						});

						self._checkStatusHoerenPruefen(kursId, 'pruefen').done(function(status) {
							if (status == '1') {
								pruefen.addClass('b_active');
							}
						});
					});
				});
			},

			_checkStatusHoerenPruefen : function(kursId, what) {
				value = 'kursid='+kursId;

				return $.ajax({
					url: "<?php echo site_url();?>ajax/check_status_"+what,
					data: value
				}).promise();
			},

			_addMarkColor : function( mark ) {

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

			initModulNotenInputs : function() {
				var self = this;

				// validate function & .blur() -> add classes
				this.config.modulNotenInputs.blur(function() {

					// get input
					$this = $(this);
					// get module
					module = $this.parent();
					// get mark
					mark = $this.val();
					// hoeren&pruefen
					hoerenAndPruefen = module.find('a.b_hoeren, a.b_pruefen');

					colors = ['sm_green', 'sm_yellow-green', 'sm_yellow', 'sm_orange', 'sm_red'];
					// if there is a mark and the mark is validated, remove old colors
					if ( mark && self._validateUserInput(mark) == true ) {
						$.each(colors, function(index, val) {
							if (module.hasClass(val)) { module.removeClass(val) };
						});
						// hide hoeren/pruefen
						hoerenAndPruefen.hide();

					} else if ( mark && self._validateUserInput(mark) == false ) {
						$this.val('');

						hoerenAndPruefen.show();

						$.each(colors, function(index, val) {
							if (module.hasClass(val)) { module.removeClass(val) };
						});

						mm = self.createModalDialog('Falscher Wert!', 'Tragen Sie bitte in das Feld einen Wert zwischen 1 - 5 ein.');
						self.config.moduleModalWrapper.html(mm);

						$('#myModal').modal({
							keyboard: false
						}).on('hide', function () {
							// console.log("hidden");
						}).modal('show');
					} else {
						hoerenAndPruefen.show();

						$.each(colors, function(index, val) {
							if (module.hasClass(val)) { module.removeClass(val) };
						});
					}

					// get color for the mark
					module.addClass(self._addMarkColor(mark));
				});

			},

			_validateUserInput : function(mark) {
				if ( mark == '1' || mark == '1-' || mark == '2+'
					|| mark == '2' || mark == '2-' || mark == '3+'
					|| mark == '3' || mark == '3-' || mark == '4+'
					|| mark == '4' || mark == '4-' || mark == '5' ) { 
				return true;
			}
				else return false;
			},

			initSendButton : function() {
				var self = this;

				this.config.sendButton.click(function() {

					var v = self.config.sortableColumns;

					// semester run var
					var i = 1;
					// for each semester
					(v).each(function(index, elem) {
						// get in each semester for each module values, if the h/t buttons are klicked or not
						($(elem).find('.semestermodul')).each(function(index, elem) {
							console.log('kursid='+$(elem).attr('data-kursid')+'&status='+$(elem).find('a.b_hoeren').hasClass('b_active'));
						});
						// serialize module order values
						console.log($(elem).sortable("serialize")+'&semester='+i);
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

					self.config.sortableColumns.sortable( "cancel" );
					self.config.changedModulesHistory = [];

					return false;
				});
			},

			initPruefenButtons : function() {
				// // get the status for this button | on / off ?
				// $(this.config.btnPruefen).each(function(index, elem) {

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
				$(this.config.btnPruefen).click(function() {

					value = 'kursid='+$(this).parent().attr('data-kursid');

					// if actual button has the b_active class, turn in off
					if ( $(this).hasClass('b_active') ) {
						$(this).removeClass('b_active');

						// $.ajax({
						//   url: "<?php echo site_url();?>ajax/deactivate_status_pruefung/",
						//   data: value,
						//   success: function(data, textStatus, xhr) {
						//     console.log("deactivated");
						//   }
						// });
					} else { // otherwise, turn it on
						$(this).addClass('b_active');

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
				// $(this.config.btnHoeren).each(function(index, elem) {

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
				$(this.config.btnHoeren).click(function() {
					// cache actual button
					var $selfBtnHoeren = $(this);

					kursId = 'kursid='+$(this).parent().attr('data-kursid');

					// in which semester is our module at the moment?
					var actSemester = $(this).parent().parent().parent().attr('id');

					// check if this module has vl for this semester
					self._getSemesterForModule(kursId).done(function(regSem) {
						regEven = false;
						actEven = false;

						(regSem%2 == 0) ? regEven = true : regEven = false;
						(actSemester%2 == 0) ? actEven = true : actEven = false;

						if ( regEven === actEven ) {
							// console.log("ist im richtigen Semester");

							if ( $selfBtnHoeren.hasClass('b_active') ) {
								$selfBtnHoeren.removeClass('b_active');
							} else { // otherwise, turn it on
								$selfBtnHoeren.addClass('b_active');
							}
						} else {
							// console.log("ist im falschen Semester");

							// no way to turn it on again
							mm = self.createModalDialog('Keine VL!', 'Für dieses Modul wird keine Vorlesung in diesem Semester angeboten!');
							self.config.moduleModalWrapper.html(mm);

							$('#myModal').modal({
								keyboard: false
							}).on('hide', function () {
								// console.log("hidden");
							}).modal('show');
						}

						// if ( actSemester == semester  ) {
						// 	// console.log("ist im richtigen Semester");

						// 	// if actual button has the b_active class, turn it off
						// 	if ( $selfBtnHoeren.hasClass('b_active') ) {
						// 		$selfBtnHoeren.removeClass('b_active');

						// 		// $.ajax({
						// 		//   url: "<?php echo site_url();?>ajax/deactivate_status_hoeren/",
						// 		//   data: value,
						// 		//   success: function(data, textStatus, xhr) {
						// 		//     console.log("deactivated");
						// 		//   }
						// 		// });
						// 	} else { // otherwise, turn it on
						// 		$selfBtnHoeren.addClass('b_active');

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

			_getSemesterForModule : function(moduleId) {
				return $.ajax({
					url: "<?php echo site_url();?>ajax/check_status_hoeren_vl/",
					data: moduleId
				}).promise();
			},

			initInfoModalButtons : function() {
				var self = this;

				this.config.infoButtonsWrapper.on('click', 'li.kursinfo', function() {

					kursId = $(this).parent().parent().attr('data-kursid');

					var mm = self.createModalDialog(
						self.getTitleForModule(kursId).done(function(result) {
							self.setModuleTitle(result);
					}), 
						self.getTextForModule(kursId).done(function(result) {
							self.setModuleText(result);
						}));

					self.config.moduleModalWrapper.html(mm);

					$('#myModal').modal({ // self.config.modalContent.modal({ ... not working, why??  s. u. Kommentar!!!
						keyboard: false
					}).on('hide', function () {
						// console.log("hidden");
					}).modal('show');

				});



				this.config.infoButtonsWrapper.on('click', 'li.reset-kurs', function() {

					$this = $(this);
					module = $this.parents('.semestermodul');

					self._resetModule(module);

					// determine in which semester this this modulee should be regulary
					// self._getSemesterForModule(kursId).done(function( regSem ) {
					// 	// if different from actual semester, move baby
					// 	if ( actSem !== regSem ) {
					// 		$detachedModulee = $this.parent().parent().parent().detach();
					// 		$detachedModulee.appendTo($('.semesterplanspalte#'+regSem));
					// 	}
					// });

				});
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
				kursId = 'kursid='+module.attr('data-kursid');
				actSem = $this.parents('.semesterplanspalte ').attr('id');
				// determine in which semester this this modulee should be regulary
				this._getSemesterForModule(kursId).done(function( regSem ) {
					// if different from actual semester, move baby
					if ( actSem !== regSem ) {
						$detachedModulee = $this.parent().parent().parent().detach();
						$detachedModulee.appendTo($('.semesterplanspalte#'+regSem));
					}
				});
			},

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
			}


		};


		
		Studienplan.init({
			sortableColumns: $(".semesterplanspalte"),
			connectWithColumns: '.semesterplanspalte',
			sendButton : $('#sB'),
			resetButton : $('#resetStudienPlan'),
			btnPruefen : $('a.b_pruefen'),
			btnHoeren : $('a.b_hoeren'),
			modalContent : $('#myModal'), //existiert an dieser stelle noch nicht!! !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! deswegen funzt oben nicht
			moduleModalWrapper : $('#modalcontent'),
			infoButtonsWrapper : $('ul.dropdown-menu'),
			modulNotenInputs : $('.semestermodul input.modulnote')
		});

<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>