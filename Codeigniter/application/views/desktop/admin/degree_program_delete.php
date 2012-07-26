<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengang anlegen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	    <div class="span2"></div>
	    <div class="span8">
		    <div class="well well-small">
<?php endblock(); ?>

<?php
// general form setup
    $title = $button_text = 'Studiengang löschen';
    $controller_function = 'admin/delete_degree_program';
    
    
    $btn_attributes = 'class = "btn-danger"';
    
    // switch view delete or copy - same view
    if(!$delete){
	$title = 'Studiengang kopieren';
	$controller_function = 'admin/copy_degree_program';
    }

?>
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
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup before content ?>
		    </div>
	    </div>
	    <div class="span2"></div>
<?php endblock(); ?>
<?php end_extend(); ?>