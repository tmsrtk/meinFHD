<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Kursverwaltung<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
<!--	<div class="span1"></div>-->
		<div class="span12 well well-small">
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
				<div class="span1"></div>
				<div class="span9"><h3>Emailversand:</h3></div>
				<div class="span2"></div>
				<!-- print email-line -->
<!--				<div id="staff-send-email" class="well well-small clearfix">-->
				<div id="staff-send-email" class="clearfix">
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
				<hr>
<!--				<hr>-->

			<?php
				// print staff-table
//				echo '<div class="well well-small">';
				echo '<div class="">';
				print $staff[$c_id];
				echo '</div>';
				echo '<hr>';
//				echo '<hr>';

				// place for general information
				echo form_open('kursverwaltung/save_course_details_all_at_once'); 


//				echo '<div class="well well-small">';
				echo '<div class="">';
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
					echo '<hr>';
//					echo '<hr>';
				}

			?>
				<div>
					<?php
//						echo '<hr>';
						echo $description[$c_id];
                        echo '<hr>'; # modification by CK to
                        echo $topics[$c_id]; # modification by CK to show topics
                        echo '<hr>';
						echo form_submit($submit_data_save_all);
						echo '</div>';
						echo form_close(); // end of form 
					?></div>
				</div><!-- end of tab -->
			<?php endforeach; ?>    
			</div>
			<div id="testing"></div>
		</div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<!--	<div class="span1"></div>-->
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

		//console.log(cb);

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
					//console.log(spanId[index]+id);
				};
			});
		});
		
		
		// ################ handle download-tn-buttons
		var downloadTnButtonsLab = $('.download-tn-button-'+courseId);
		var downloadTnButtonCourse = $('.download-tn-button-course-'+courseId);
		
		$.each(downloadTnButtonsLab, function(index, value){
			$(value).click(function(){
			console.log('test');
				var spCourseId = $(this).data('id');
				$.ajax({
					type: "POST",
					url: "<?php echo site_url();?>kursverwaltung/ajax_create_participants_file_sp_course/",
					dataType: 'html',
					data : {sp_course_id : spCourseId},
					success: function (data){
						// TODO ??
					}
				});
			});
		});
		
		$(downloadTnButtonCourse).click(function(){
			var spCourseId = $(this).data('id');
			$.ajax({
				type: "POST",
				url: "<?php echo site_url();?>kursverwaltung/ajax_create_participants_file_course/",
				dataType: 'html',
				data : {sp_course_id : spCourseId},
				success: function (data){
					// TODO ??
				}
			});
		});
		
		// ################ handle activate-application buttons
		var switchActivationButtons = $('.activation-buttons-'+courseId);
		
		// run through all buttons on site
		$.each(switchActivationButtons, function(index, value) {
			var buttonId = '#'+$(value).attr('id');
			// click behaviour
			$(buttonId).click(function(){
				var buttonText = '';
				var buttonStatus = $(this).data('status');
				var spCourseId = $(this).data('id');
				var courseIdStatus = [spCourseId, buttonStatus];

				// alter text and status depending on former status
				if(buttonStatus == 'disabled'){
					buttonText = 'Anmeldung deaktivieren';
					buttonStatus = 'enabled';
				} else {
					buttonText = 'Anmeldung aktivieren';
					buttonStatus = 'disabled';
				}

				// de/acitvate sp_course
				$.ajax({
					type: "POST",
					url: "<?php echo site_url();?>kursverwaltung/ajax_toggle_activation_of_spcourse/",
					dataType: 'html',
					data : {course_id_status : courseIdStatus},
					success: function (data){
						$(buttonId).data('status', buttonStatus);
						$(buttonId).text(buttonText);
<!--						$('#testing').text(data);-->
						
					}
				});
			});
		});


    }); // end tab-views - all elements has to be prepared for all ids
	
	
	// create dialog element
	function createModal(title, text) {
		var myDialog = 
			$('<div class="modal hide" id="participants-modal"></div>')
			.html('<div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
			.append('<div class="modal-body" id="modal-body"><p>'+text+'</p></div>')
			.append('<div class="modal-footer"><a href="#" class="btn" id="part-modal-cancel" data-dismiss="modal">Abbrechen</a>\n\
			<a href="" class="btn btn-primary" data-id="0" data-delete="0" id="part-modal-confirm" data-accept="modal">Herunterladen</a></div>');

		return myDialog;
    };
    
//    
//    // handle button to add tuts to benutzer_mm_rolle
//    $('#tutor-button').click(function(){
//	
//    });
//    
<?php endblock(); ?>

<?php end_extend(); ?>