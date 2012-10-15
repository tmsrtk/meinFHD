<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Praktikumsverwaltung - Überblick<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
	<div class="span12 well well-small">
<?php endblock(); ?>
	
<?php
	//general form setup
	
?>

<?php startblock('content'); # additional markup before content ?>
		
<!--		<div>
			<pre>
				<?php // print_r($sp_course_details); ?>
			</pre>
		</div>-->
		
		<div class="row-fluid">
			<h2>Meine Praktikumsgruppen</h2>
		</div>

		<div>
			<?php
				// helper var to save the course before
				// used to start new well and print headline
				$course_before = -1;
				
				// run through all details and print them + button
				foreach($sp_course_details as $key => $details){
					$group = 1;
					// if there are details for that course-eventtype-combination print them
					if($details){
						foreach($details as $d){
							// print headline if the course changes
							if($course_before === -1){
								echo '<div class="well-small"><h4>'.$d->Kursname.'</h4>';
							} else if($course_before != substr($key, 0, 3)){
								echo '</div><div class="well-small"><h4>'.$d->Kursname.'</h4>';
							}
							// print lab-groups + buttons
							echo form_open('kursverwaltung/show_labmgt');
//							echo 'Gruppe '.$group;
//							echo $d->TagName;
//							echo $d->Beginn;
							echo form_hidden('sp_course_id', $d->SPKursID);
							echo form_hidden('course_id', substr($key, 0, 3));
							echo form_submit(array('class' => 'span btn btn-info', 'name' => 'show_group'), 'Gruppe '.$group);
							echo form_close();
							$group++;
							// save the course before
							$course_before = substr($key, 0, 3);
						}
					}
				}
			
			?>
		</div>
		
<?php endblock(); ?>
<?php startblock('postCodeContent'); # additional markup before content ?>
	</div>
<?php endblock(); ?>
	
<?php startblock('customFooterJQueryCode');?>
<?php endblock(); ?>

<?php end_extend(); ?>