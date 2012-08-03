<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Kursverwaltung<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span1"></div>
		<div class="span10 well well-small">
<?php endblock(); ?>
	
<?php
	//general form setup
	
    // prepare array with ids for json
    $course_ids_jq = array();
    // associative arrays can be handled easier in jquery
    $course_ids = array_keys($course_details);
    // contains both kursids and spkursids
    foreach ($course_ids as $id) {
		$course_ids_jq['KursID'.$id] = $id;
    }
?>

<?php startblock('content'); # additional markup before content ?>

		<div class="row-fluid">
			<h2>Meine Kurse</h2>
		</div>
		<hr>
		<div class="row-fluid">

			<ul class="nav nav-tabs" id="course-details-navi">
			<?php 
				// print navigation depending on courses this user has
				foreach ($course_names_ids as $key => $value) {
					echo '<li id="course-tab-'.$key.'">';
					echo '<a href="#'.$value->kurs_kurz.'-'.$key.'" data-toggle="tab">'.$value->kurs_kurz.'</a>';
					echo '</li>';
				}
			?>
			</ul>




			<div class="tab-content">
			<?php 
				// print div for each course
				foreach($course_names_ids as $c_id => $value) :
					echo '<div class="tab-pane" id="'.$value->kurs_kurz.'-'.$c_id.'"> ';

					// checkbox data - has to be generate each time because of course_id!
					$cb_data = array(
						'name' => '',
						'class' => 'email-checkbox',
						'id' => 'email-checkbox-all-id-'.$c_id,
						'value' => '',
						'checked' => 'checked',
					);

					$submit_data_send_email = array(
						'name' => $c_id,
						'value' => 'Email senden',
						'id' => 'send-email-to-cb-'.$c_id,
						'class' => 'btn btn-warning'
					);

					$submit_data_save_all = array(
			//		    'name' => $c_id,
						'value' => 'Kursinformationen speichern',
						'id' => 'save-all'.$c_id,
						'class' => 'btn btn-warning'
					);

					$overall_label_attrs = array(
						'id' => 'course-mgt-label-overall-'.$c_id,
						'class' => 'label label-info',
					);
			?>

				<h3>Emailversand:</h3>
				<!-- print email-line -->
				<div id="staff-send-email" class="well well-small clearfix">
					<?php echo form_open(''); ?>
					<div class="span1">
						<?php echo form_checkbox($cb_data); ?>
					</div>
					<div class="span5">
						<?php echo form_label('Email senden an alle Personen und Kursteilnehmer', '', $overall_label_attrs); ?>
					</div>
					<div class="span2">
						<?php 
							echo form_submit($submit_data_send_email);
							echo form_close();
						?>
					</div>
				</div>

			<?php
				// print staff-table
				echo '<div class="well well-small">';
				print $staff[$c_id];
				echo '</div>';

				// place for general information
				echo form_open('kursverwaltung/save_course_details_all_at_once'); 


				echo '<div class="well well-small">';
				// $course_details contains mapped details on course_ids
				foreach ($course_details[$c_id] as $c_details) {
					// necessary because pr, übung, sem come withing nested array
					if(!is_array($c_details)){
						print($c_details);
					} else {
						foreach($c_details as $v){
							print($v);
						}
					}
				}
				echo '<hr>';
				echo form_submit($submit_data_save_all);
				echo '</div>';


				// course-description
				$course_description_textarea_data = array(
					'name' => $c_id.'_description',
					'id' => 'input-course-description',
					'class' => 'input-xlarge',
					'value' => $value->Beschreibung,
					'rows' => 7,
					'cols' => 40
				);
			?>

				<div>
					<h3>Beschreibung </h3>
					<div>
					<?php echo form_textarea($course_description_textarea_data); ?>
					</div>
					<?php echo form_close(); // end of form ?>
				</div>
				</div><!-- end of tab -->
			<?php endforeach; ?>    
			</div>
		</div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span1"></div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
	
    // initialize active tab
    $('.tab-content div:first-child').addClass("active");
    $('#course-details-navi li:first-child').addClass("active");
    
    // contains all sp_course_ids in that view
    var courseIdsInView = <?php echo json_encode($course_ids_jq); ?>;
    
    // run through all ids and assign functions
    // - un/check all boxes if overall cb changes
    // - uncheck overall cb if ONE or more of the single cb is NOT checked
    // - check overall cb if all single cb are checked
    // - click on email-button
    $.each(courseIdsInView, function(indexAll, courseId){

		// save checkboxes for that course to array
		var checkboxesOnSite = $('.email-checkbox-'+courseId);
		var overallCbId = '#email-checkbox-all-id-'+courseId;
		// save id for email-button
		var sendEmailButtonId = '#send-email-to-cb-'+courseId;
		// base of label-id
		var labelIdBase = '#course-mgt-label-';
		var labelIdOverall = $('#course-mgt-label-overall-'+courseId);

		// find out how many checkboxes there are on course-site
		var numberCbs = 0;
		$.each(checkboxesOnSite, function(index, value){
			numberCbs++;
		});

		// change of overall cb - uncheck >> uncheck all | check >> check all
		$(overallCbId).change(function(){
			var cbAll = $(this);
			// run through all elements and set un/checked
			$.each(checkboxesOnSite, function(i, v){
			var cbSelf = $(this);
			var cbId = cbSelf.attr('id');
			var cbName = cbSelf.attr('name');
			var labelId = labelIdBase+cbName;

			// toggle checked/unchecked + color
			if(cbAll.is(':checked')){
				$('#'+cbId).attr('checked', true);
				labelIdOverall.text('Email senden an alle Personen und Kursteilnehmer');
				$(labelIdOverall).addClass('label-info');
				$(labelIdOverall).removeClass('label-default');
				$(labelId).addClass('label-info');
				$(labelId).removeClass('label-default');
			} else {
				$('#'+cbId).attr('checked', false);
				labelIdOverall.text('keine Auswahl für Email-Versand');
				$(labelIdOverall).addClass('label-default');
				$(labelIdOverall).removeClass('label-info');
				$(labelId).addClass('label-default');
				$(labelId).removeClass('label-info');
			}
			});
		});

		// change of any of the single checkboxes - uncheck one >> uncheck overall | check all >> check overall
		$.each(checkboxesOnSite, function(index, value){
			// init counter to detect if there are un/checked checkboxes
			var self = $(this);
			var cbId = self.attr('id');
			var cbName = self.attr('name');

			// build correct label-id
			var labelId = labelIdBase+cbName;

			// if checkbox changes
			$('#'+cbId).change(function(){
			var counter = 0;

			// affect label-color
			if($(this).is(':checked')){
				$(labelId).addClass('label-info');
				$(labelId).removeClass('label-default');
			} else {
				$(labelId).addClass('label-default');
				$(labelId).removeClass('label-info');
			}

			// count unchecked checkboxes
			$.each(checkboxesOnSite, function(i, v){
				if($(this).is(':checked')){
				counter++;
				}
			});
			// if all checkboxes are checked >> check overall checkbox
			if(counter >= 1){
				$(overallCbId).attr('checked', true);
				labelIdOverall.addClass('label-info');
				labelIdOverall.removeClass('label-default');
	//		    labelIdOverall.text('Email senden an alle Personen und Kursteilnehmer');
				labelIdOverall.text('Email senden an Auswahl');
			// otherwise uncheck overall checkbox
			} else if(counter == 0) {
				$(overallCbId).attr('checked', false);
				labelIdOverall.text('keine Auswahl für Email-Versand');
				labelIdOverall.addClass('label-default');
				labelIdOverall.removeClass('label-info');
			}
	//		} else {
			if(counter == numberCbs){
				$(overallCbId).attr('checked', true);
	//		    labelIdOverall.addClass('label-info');
	//		    labelIdOverall.removeClass('label-default');
	//		    labelIdOverall.text('Email senden an Auswahl');
				labelIdOverall.text('Email senden an alle Personen und Kursteilnehmer');
			}
			}); // end checkbox-change
		}); // end run through checkboxes

		// get staff and course checkboxes separatly
		// and put into array to run through easier
		var staffCbElements = $('.email-checkbox-staff-'+courseId);
		var courseCbElements = $('.email-checkbox-courses-'+courseId);
		var bothCbElements = [staffCbElements, courseCbElements];

		// init arrays to save recipients
		var staffRecipients = new Array();
		var courseRecipients = new Array();

		// click on email-button
		$(sendEmailButtonId).click(function(){
			// detect chosen checkboxes - 
			$.each(bothCbElements, function(index, checkboxes){
				$.each(checkboxes, function(i, v){
					var self = $(this);
					var cbName = self.attr('name');
					if(self.is(':checked')){
					// differ between staff and courses
					if(index == 0){
							staffRecipients.push(cbName);
						} else if(index == 1) {
							courseRecipients.push(cbName);
						}
					}
				});
			});
			alert(
			'TODO \n\
			Emailversand an Personen: ' + staffRecipients + '\n\
			Emailversand an Teilnehmer: ' + courseRecipients
			);
		});

		// handle PANELS
		// ids of sliders
		var buttonId = ['#labings-slider-'+courseId, '#tuts-slider-'+courseId];
		var panelId = ['#labings-panel-'+courseId, '#tuts-panel-'+courseId]; 
		
		// PANLES - activate buttons for both - labings and tuts
		$.each(buttonId, function(index, value){
			// slide-toggle
			$(value).click(function() { 
				console.log($(this).attr('id'));
				// !!usage of index: first buttonId >> first Panel || second buttonId >> second Panel
				$(panelId[index]).slideToggle('slow', function () {
				// 
				});
			});

			/*// converting plus into minus-buttons and back again
			$(value).toggle(
				function() { 
					$(this).text('-');
				},

				function() { 
					$(this).text('Bearbeiten');
				}

			);*/
		});
		
		// ids/texts of name-spans and cells
		var spanText = ['#labing-label-', '#tut-label-'];
		var spanIdText = ['added-labings-', 'added-tuts-'];
		var spanId = ['#added-labings-', '#added-tuts-'];
		var cellId = ['#current-labings-', '#current-tuts-'];

		// saving checkboxes into var
		var cb = $('#labings-panel-'+courseId).children('input');

		console.log(cb);

		// activate each panel
		$.each(panelId, function(index, value){
			// show labings in table when clicked - NOT saved yet!
			$(value + ' input').change(function () {
				var self = $(this);
				var id = self.attr("id");
				console.log(self);
				if(self.is(":checked")) {
					$('<span></span>', {
						text: $(spanText[index] + id).text()+', ',
						id: spanIdText[index] + id
					}).appendTo(cellId[index] + courseId);
				};
				if(!self.is(":checked")){
					$(spanId[index] + id).remove();
					console.log(spanId[index]+id);
				};
			});
		});


    }); // end tab-views - all elements has to be prepared for all ids
    
//    
//    // handle button to add tuts to benutzer_mm_rolle
//    $('#tutor-button').click(function(){
//	
//    });
//    
<?php endblock(); ?>

<?php end_extend(); ?>