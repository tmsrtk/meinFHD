<?php extend('admin/index.php'); # extend main template ?>

<?php
// general form setup
    $title = $button_text = 'Studiengang löschen';
    $modal_text = 'Soll der Studiengang wirklich gelöscht werden?';
    $modal_text_progress = 'Studiengang wird gelöscht?';
    $controller_function = 'admin/delete_degree_program';
    
    
    // switch view delete or copy - same view
    if(!$delete){
	$title = 'Studiengang kopieren';
	$controller_function = 'admin/copy_degree_program';
	$modal_text = 'Soll der Studiengang wirklich kopiert werden?';
	$modal_text_progress = 'Studiengang wird kopiert?';
    }

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
		    <?php foreach($all_degree_programs as $sg) : ?>
			<tr>
			    <td>
				<div class="span3"><?php echo $sg->StudiengangName; ?></div>
				<div class="span1"><?php echo $sg->Pruefungsordnung; ?></div>
				<div class="span3">
				    <?php 
					echo form_open($controller_function);
				        $btn_attributes = array(
					    'class' => 'btn btn-danger submit-copy-delete-button',
					    'id' => 'submit-'.$sg->StudiengangID,
					);
					echo form_submit('copy_delete_degree_program_id', $title, $btn_attributes);
					// put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
					$hidden_data = array(
						'degree_program_id' => $sg->StudiengangID
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
		
	    <div class="modal hide" id="confirmation-dialog">
		<div class="modal-header">
		    <button type="button" data-dismiss="modal">×</button>
		    <h3><?php echo $title; ?></h3>
		</div>
		<div class="modal-body">
		    <p><?php echo $modal_text; ?></p>
		</div>
		<div class="modal-footer">
		    <a href="#" class="btn" id="confirmation-dialog-dismiss" data-dismiss="modal">Abbrechen</a>
		    <a href="#" class="btn btn-primary" data-id="0" id="confirmation-dialog-confirm">OK</a>
		</div>
	    </div>
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		    </div>
	    </div>
	    <div class="span2"></div>
<?php endblock(); ?>
	    
<?php startblock('customFooterJQueryCode');?>

	// get all copy-delete buttons on site
	$copy_delete_buttons = $('.submit-copy-delete-button').attr('id');

	// show dialog when submit-button ist clicked
	$.each($copy_delete_buttons, function(index, value){
	    console.log(value);
	    $(value).click(function (){
		$('#confirmation-dialog')..modal({
		    keyboard: false
		}).on('hide', function(){
		    ;
		}).modal('show');
		
		return false;
	    });
	});
	    
	    
	    
<?php endblock(); ?>
	    
<?php end_extend(); ?>