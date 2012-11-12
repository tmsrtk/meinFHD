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
			echo '<p class="pull-right text-warning">'.$status_label.'</>';
		?>
	</div>
	<div class="span3">
		<?php
			// Print button 
			$switch_button_label = $button_label;
			$anchor_attrs = array(
				'class' => 'pull-right '.$btn_class.' activation-buttons-'.$course_id,
				'id' => 'activation-button-'.$course_id,
				'data-id' => $course_id,
				'data-status' => $status_css
			);
			echo anchor('kursverwaltung/show_coursemgt#', $switch_button_label, $anchor_attrs);
		?>
	</div>
</div>