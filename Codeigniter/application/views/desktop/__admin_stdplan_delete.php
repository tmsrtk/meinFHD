<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Studiengangverwaltung<?php endblock(); ?>

<?php
# general form setup
?>

<?php startblock('content'); # additional markup before content ?>

<div class="well">
	<table> 
		<!-- tablehead -->
		<thead><tr>
			<th>StudiengangName:</th>
			<th>Kurzbezeichnung:</th>
			<th>Löschen</th>
		</tr></thead>
	
		<tbody>
		<?php 
		    $btn_attributes = 'class = "btn-danger"';
		    
		    foreach($delete_view_data as $sp) :  
			echo form_open('admin/delete_stdplan'); ?>
			<tr>
			    <td style="width:200px;"><?php echo $sp->StudiengangName; ?></td>
			    <td><?php echo $sp->StudiengangAbkuerzung.'_'.$sp->Semester.'_'.$sp->Pruefungsordnung; ?></td>
			    <td>
				<?php
				    $attributes = array('class' => 'listform', 'id' => 'stdgngform');
				    echo form_submit('delete_sdtplan', 'Stundenplan loeschen', $btn_attributes);
				?>
			    </td>
			</tr>
			<?php 
			    // put some static data into post - CreditpointsMin (actually not needed) and FachbereichID (final = 5)
			    $hiddenData = array(
//				'stdgng_id' => $sp->StudiengangID,
				'stdplan_abk' => $sp->StudiengangAbkuerzung,
				'stdplan_semester' => $sp->Semester,
				'stdplan_po' => $sp->Pruefungsordnung
			    );
			    echo form_hidden($hiddenData);
			    echo form_close();
			?>

		    <?php endforeach; ?>
		</tbody>
	</table>	
</div>

<?php endblock(); ?>

<?php end_extend(); ?>