<?php 
	/**
	 * Building custom-tableheader.
	 * Depending on lab- and tutor-flag a button is added to activate the application.
	 * Only LABS get the button when the user is NO tutor.
	 */
?>
<div class="clearfix">
	<div class="span1"></div>
	<div class="span5"><h3><?php echo $headline; ?></h3></div>
	<div class="span3" id="activation-status-<?php echo $course_id;?>">
		<?php 
//			if($is_lab && !$is_tut){
				echo '<p class="pull-right text-warning">'.$status_label.'</>';
//			}
		?>
	</div>
	<div class="span3">
		<?php
			// Print button only for labs AND if user is NO Tutor
			// TODO - $lecutre_details come from the row before
			// if lab-row is the first row >> ERROR because those lecture-details are not known
//			if($is_lab && !$is_tut){
				$switch_button_label = $button_label;
				$anchor_attrs = array(
					'class' => 'pull-right btn btn-success activation-buttons-'.$course_id,
					'id' => 'activation-button-'.$course_id,
					'data-id' => $course_id,
					'data-status' => $status_css
				);
				echo anchor('kursverwaltung/show_coursemgt#', $switch_button_label, $anchor_attrs);
//			}
		?>
	</div>
</div>