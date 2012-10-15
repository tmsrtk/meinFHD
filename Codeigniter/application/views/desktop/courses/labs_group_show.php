<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Praktikumsverwaltung - Gruppen<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
	// general form setup
	// TODO
	// to change number of shown labs >> there must be an additional field in db to store this value
	// at first static in this view
	$number_of_events = 15;
	
?>

<?php startblock('content'); # additional markup before content ?>
		<!-- TODO: run through array of courses and groups and print as table -->
		
		<div>
			tabview with a single group
			<pre>
				<?
//					print_r($active_group);
//					print_r($sp_course_details);
				?>
			</pre>
			
			<table class="table">
				<thead></thead>
				<tbody>
					<?
						foreach($sp_course_details as $details_type){
							foreach($details_type as $participants){
								foreach($participants as $index => $one_participant){
									// build row from data
									echo '<tr><td>';
									
									// print labels - name
									echo form_label($one_participant->Vorname, 'first_name'.$one_participant->BenutzerID);
									echo '</td><td>';
									echo form_label($one_participant->Nachname, 'last_name'.$one_participant->BenutzerID);
									echo '</td><td>';
									
									// print two lines of checkboxes (1. presence, 2. testat)
									for($i = 0; $i < $number_of_events; $i++){
										if(substr($one_participant->anwesenheit, $i, 1) == '1'){
											// check
											echo form_checkbox('presence'.$i, 'accept', TRUE);
										} else {
											// uncheck
											echo form_checkbox('presence'.$i, 'accept', FALSE);
										}
										
										if(substr($one_participant->testat, $i, 1) == '1'){
											// check
											echo form_checkbox('testat'.$i, 'accept', TRUE);
										} else {
											// uncheck
											echo form_checkbox('testat'.$i, 'accept', FALSE);
										}
										echo '</td><td>';
									}
									echo '</td><td>';
									
									// final testat
									echo form_checkbox('final_testat', 'accept', ($one_participant->gesamttestat ? TRUE:FALSE));
									echo '</td><td>';
									
									// print  notes
									echo form_textarea();
									echo '</td><td>';
									
									// print disable participant
									echo form_checkbox('final_testat', 'accept', ($one_participant->ende ? TRUE:FALSE));
									echo '</td></tr>';
								}
							}
						}
					?>
				</tbody>
			</table>
			
		</div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
<?php endblock(); ?>

<?php end_extend(); ?>