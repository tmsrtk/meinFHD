<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan löschen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span3"></div>
	<div class="span6 well well-small">
<?php endblock(); ?>
	
<?php
# general form setup
?>

<?php startblock('content'); # additional markup before content ?>

		<div class="row-fluid">
			<h2>Stundenplan löschen</h2>
		</div>
		<hr>

		<div class="row-fluid">
			<table class="table table-striped"> 
				<!-- tablehead -->
				<thead>
					<tr>
						<th>Studiengang:</th>
						<th>Kurzbezeichnung:</th>
						<th>Löschen</th>
					</tr>
				</thead>

				<tbody>
				<?php 

					foreach($delete_view_data as $sp) :
						$btn_attributes = array(
							'name' => 'delete_sdtplan',
							'class' => 'btn-danger delete-stdplan',
							'id' => $sp->StudiengangAbkuerzung.'-'.$sp->Pruefungsordnung.'-'.$sp->Semester,
							'data-name' => $sp->StudiengangAbkuerzung.' - '.$sp->Pruefungsordnung.' - '.$sp->Semester.'. Semester'
						);
					?>

					<tr>
						<td><?php echo $sp->StudiengangName; ?></td>
						<td><?php echo $sp->StudiengangAbkuerzung.'_'.$sp->Semester.'_'.$sp->Pruefungsordnung; ?></td>
						<td>
							<?php
								$form_attrs = 'id="submit-delete"';
								echo form_open('admin/delete_stdplan', $form_attrs);
								echo form_submit($btn_attributes, 'Stundenplan loeschen');
								// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
								$hiddenData = array(
			//						'stdgng_id' => $sp->StudiengangID,
									'stdplan_abk' => $sp->StudiengangAbkuerzung,
									'stdplan_semester' => $sp->Semester,
									'stdplan_po' => $sp->Pruefungsordnung
								);
								echo form_hidden($hiddenData);
								echo form_close();
							?>
						</td>
					</tr>

					<?php endforeach; ?>
				</tbody>
			</table>	
		</div>
		<div id="delete-confirmation-container"></div>

<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
	<div class="span3"></div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>

	<!--<script>-->
	var title = 'Stundenplan löschen';
	var modalText = 'modal-text';
	var modalTextProgress = 'Stundenplan wird gelöscht';

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
			console.log(buttonId);
		
	    }).on('hide', function(){
			console.log('hidden');
		
	    }).modal('show');
	    
	    return false;
	});
	
	// creates modal
	function createModal(title, text, stdPlan){
	    var confirmationModal = $('<div class="modal hide" id="delete-dialog"></div>')
		.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
		.append('<div class="modal-body"><p>Soll der Stundenplan ('+stdPlan+') wirklich gelöscht werden?</p></div>')
		.append('<div class="modal-footer"><a href="#" class="btn" id="dismiss-delete-dialog" data-dismiss="modal">Abbrechen</a>\n\
		    <a href="#" class="btn btn-primary" id="confirm-delete-dialog">OK</a></div>');
	    
	    return confirmationModal;
	}
	
	
	// when clicking on ok
	$('#delete-confirmation-container').on('click', '#confirm-delete-dialog', function(){
	    var submitId = $(this).attr('data-id');
	    
	    // change modal to progress
	    $('.modal-body').html(modalTextProgress);
	    $('.modal-footer').hide();
	    
	    $('#'+submitId).parents('form#submit-delete').submit();
	    
	    return false;
	});
	
	    
	    
	    
<?php endblock(); ?>

<?php end_extend(); ?>