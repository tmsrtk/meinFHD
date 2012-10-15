<?php 
	/**
	 * Building custom-tableheader.
	 * Depending on lab- and tutor-flag a button is added to activate the application.
	 * Only LABS get the button when the user is NO tutor.
	 */
?>
<div class="clearfix">
	<div class="span1"></div>
	<div class="span7"><h3><?php echo $headline.':'; ?></h3></div>
	<div class="span2" class="activation-status-<?php echo $course_id;?>"><b>
		<?php 
//			if($is_lab && !$is_tut){
//				echo 'Anmeldung deaktiviert';
//			}
		?>
	</b></div>
	<div class="span2">
		<?php
			// Print button only for labs AND if user is NO Tutor
			// TODO - $lecutre_details come from the row before
			// if lab-row is the first row >> ERROR because those lecture-details are not known
//			if($is_lab && !$is_tut){
//				$switch_button_label = '<i class="icon-ok"></i> Anmeldung aktivieren';
//				$anchor_attrs = array(
//					'class' => 'btn btn-mini btn-success activation-buttons-'.$course_id,
//					'id' => 'activation-button-'.$lab_details->SPKursID,
//					'data-id' => $lab_details->SPKursID,
//					'data-status' => 'disabled'
//				);
//				echo anchor('kursverwaltung/show_coursemgt#', $switch_button_label, $anchor_attrs);
//			}
		?>
	</div>
</div>
<br />
<div class="clearfix">
    <div class="span1 bold">Email</div>
    <div class="span2 bold">Kursname</div>
    <div class="span1 bold">Raum</div>
    <div class="span2 bold">Startzeit</div>
    <div class="span2 bold">Endzeit</div>
    <div class="span2 bold">Tag</div>
    <div class="span1 bold" id="course-mgt-tn">max. TN</div>
    <div class="span1 bold">Teilnehmer</div>
</div>

