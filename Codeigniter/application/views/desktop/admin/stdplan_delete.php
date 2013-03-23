<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan l&ouml;schen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>
	
<?php
# general form setup
?>

<?php startblock('content'); # additional markup before content ?>

		<div class="row-fluid">
			<h2>Stundenplan l&ouml;schen</h2>
		</div>
		<hr>

		<div class="row-fluid">
            <?php

            /*
            * Display all imported timetables, to be able to delete them. The table will only be displayed, if there is an imported timetable.
            * The timetables are passed as an array, so the variable needs to be an array and the count needs to be grater than 1
            */
            if (is_array($delete_view_data)):

            ?>

                <table class="table table-striped">
				<!-- tablehead -->
                <thead>
                <tr>
                    <th>Studiengang:</th>
                    <th>Kurzbezeichnung:</th>
                    <th>L&ouml;schen</th>
                </tr>
                </thead>

				<tbody>
            <?php

                foreach($delete_view_data as $sp):
                    $btn_attributes = array(
                        'name' => 'delete_sdtplan',
                        'class' => 'btn-danger delete-stdplan',
                        'id' => $sp->StudiengangAbkuerzung.'_'.$sp->Pruefungsordnung.'_'.$sp->Semester,
                        'data-name' => $sp->StudiengangAbkuerzung.' - '.$sp->Pruefungsordnung.' - '.$sp->Semester.'. Semester'
                    );
            ?>
					<tr>
						<td><?php echo $sp->StudiengangName; ?></td>
						<td><?php echo $sp->StudiengangAbkuerzung.'_'.$sp->Pruefungsordnung.'_'.$sp->Semester; ?></td>
						<td>
							<?php
								$form_attrs = 'id="submit-delete-'.$sp->StudiengangAbkuerzung.'_'.$sp->Pruefungsordnung.'_'.$sp->Semester.'"'; // unique id for every button
                                echo form_open('admin/delete_stdplan', $form_attrs);

                                echo form_submit($btn_attributes, 'Stundenplan löschen');
								$hiddenData = array(
									'stdplan_abk' => $sp->StudiengangAbkuerzung,
									'stdplan_semester' => $sp->Semester,
									'stdplan_po' => $sp->Pruefungsordnung
								);
								echo form_hidden($hiddenData);
								echo form_close();
							?>
						</td>
					</tr>

            <?php
                endforeach; // end of the foreach loop
            ?>
                </tbody>
                </table>
            <?php
                else: // display a message if there is no timetable imported
            ?>
            <p>Es ist kein Stundenplan in der Datenbank vorhanden.</p>
            <?php endif; ?>
		</div>
		<div id="delete-confirmation-container"></div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>

	var title = 'Stundenplan l&ouml;schen';
	var modalText = 'modal-text';
	var modalTextProgress = 'Stundenplan wird gel&ouml;scht';

	$('td').on('click', '.delete-stdplan', function(){
	    var buttonId = $(this).attr('id');
	    var buttonName = $(this).data('name');
		
	    var dialog = createModal(title, modalText, buttonName);
	    $('#delete-confirmation-container').html(dialog);
	    
	    // modal for each button
	    // function of dialog
	    $('#delete-dialog').modal({
			keyboard: false,
			backdrop: 'static'
	    }).on('show', function(){
			$('#confirm-delete-dialog').attr('data-id', buttonId);

	    }).on('hide', function(){

	    }).modal('show');
	    
	    return false;
	});
	
	// creates modal
	function createModal(title, text, stdPlan){
	    var confirmationModal = $('<div class="modal hide" id="delete-dialog"></div>')
		.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
		.append('<div class="modal-body"><p>Soll der Stundenplan ('+stdPlan+') wirklich gel&ouml;scht werden?</p></div>')
		.append('<div class="modal-footer"><a href="#" class="btn" id="dismiss-delete-dialog" data-dismiss="modal">Nein</a>\n\
		    <a href="#" class="btn btn-primary" id="confirm-delete-dialog">Ja</a></div>');
	    
	    return confirmationModal;
	}
	
	
	// when clicking on ok
	$('#delete-confirmation-container').on('click', '#confirm-delete-dialog', function(){
	    var submitId = $(this).attr('data-id');


	    // change modal to progress
	    $('.modal-body').html(modalTextProgress);
	    $('.modal-footer').hide();
	    
	    $('#'+submitId).parents('form#submit-delete-'+submitId).submit();

        return false;
	});
	
<?php endblock(); ?>

<?php end_extend(); ?>