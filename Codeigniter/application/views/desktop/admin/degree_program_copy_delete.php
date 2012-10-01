<?php extend('admin/index.php'); # extend main template ?>

<?php
// general form setup
    $title = $button_text = 'Studiengang löschen';
    $modal_text = 'Soll der Studiengang wirklich gelöscht werden?';
    $modal_text_progress = 'Studiengang wird gelöscht.';
    $controller_function = 'admin/delete_degree_program';
    
    
    // switch view delete or copy - same view
    if(!$delete){
	$title = 'Studiengang kopieren';
	$controller_function = 'admin/copy_degree_program';
	$modal_text = 'Soll der Studiengang wirklich kopiert werden?';
	$modal_text_progress = 'Studiengang wird kopiert.';
    }
    
    $form_attrs = 'id="submit-copy-delete"';

?>
<?php startblock('title');?><?php get_extended_block();?> - <?php echo $title;?><?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	    <div class="span2"></div>
	    <div class="span8">
		    <div class="well well-small">
<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
	    <div class="row-fluid">
		    <h2><?php echo $title; ?></h2>
	    </div>
	    <hr>
	    <div class="row-fluid">
		
	    <table class="table table-striped"> 
		<thead>
		    <th>
			<div class="span3 bold">Studiengang:</div>
			<div class="span1 bold">PO:</div>
			<div class="span3 bold">Aktion</div>
		    </th>
		</thead>

		<tbody>
		    <?php foreach($all_degree_programs as $dp) : ?>
			<tr>
			    <td>
				<div class="span3"><?php echo $dp->StudiengangName; ?></div>
				<div class="span1"><?php echo $dp->Pruefungsordnung; ?></div>
				<div class="span3">
				    <?php 
					echo form_open($controller_function, $form_attrs);
				        $btn_attributes = array(
					    'class' => 'btn btn-danger submit-copy-delete-button',
					    'id' => 'submit-'.$dp->StudiengangID,
					    'name' => 'copy_delete_degree_program_id'
					);
					echo form_submit($btn_attributes, $title);
					// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
					$hidden_data = array(
						'degree_program_id' => $dp->StudiengangID
					);
					echo form_hidden($hidden_data);
					echo form_close();
				    ?>
				</div>
			    </td>
			</tr>
			<?php 
			?>

		    <?php endforeach; ?>
		</tbody>
	    </table>
		
	    <div id="copy-delete-confirmation"></div>
		
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		    </div>
	    </div>
	    <div class="span2"></div>
<?php endblock(); ?>
	    
<?php startblock('customFooterJQueryCode');?>

	var title = '<?php echo $title; ?>';
	var modalText = '<?php echo $modal_text; ?>';
	var modalTextProgress = '<?php echo $modal_text_progress; ?>';

	$('td').on('click', '.submit-copy-delete-button', function(){
	    var dialog = createModal(title, modalText);
	    $('#copy-delete-confirmation').html(dialog);
	    
	    var buttonId = $(this).attr('id');
	    // modal for each button
	    // function of dialog
	    $('#cdc-dialog').modal({
		keyboard: false
	    }).on('show', function(){
		$('#confirm-copy-delete-confirmation-dialog').attr('data-id', buttonId);
		console.log(buttonId);
		
	    }).on('hide', function(){
		console.log('hidden');
		
	    }).modal('show');
	    
	    return false;
	});
	
	function createModal(title, text){
	    var confirmationModal = $('<div class="modal hide" id="cdc-dialog"></div>')
		.html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
		.append('<div class="modal-body"><p>'+text+'</p></div>')
		.append('<div class="modal-footer"><a href="#" class="btn" id="dismiss-copy-delete-confirmation-dialog" data-dismiss="modal">Abbrechen</a>\n\
		    <a href="#" class="btn btn-primary" id="confirm-copy-delete-confirmation-dialog">OK</a></div>');
	    
	    return confirmationModal;
	}
	
	$('#copy-delete-confirmation').on('click', '#confirm-copy-delete-confirmation-dialog', function(){
	    var submitId = $(this).attr('data-id');
	    
	    // change modal to progress
	    $('.modal-body').html(modalTextProgress);
	    $('.modal-footer').hide();
	    
	    $('#'+submitId).parents('form#submit-copy-delete').submit();
	    
	    return false;
	});
	
	    
	    
	    
<?php endblock(); ?>
	    
<?php end_extend(); ?>