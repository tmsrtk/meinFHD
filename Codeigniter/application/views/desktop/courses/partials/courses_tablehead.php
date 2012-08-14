<!--building custom table >> header-->
<div class="clearfix">
	<div class="span1"></div>
	<div class="span9"><h3><?php echo $headline.':'; ?></h3></div>
	<div class="span2">
		<?php
			if($is_lab){
				$switch_button_label = '<i class="icon-ok"></i> Anmeldung aktivieren';
				$anchor_attrs = array(
					'class' => 'btn btn-mini btn-success activation-buttons-'.$course_id,
					'id' => 'activation-button-'.$lecture_details->SPKursID,
					'data-id' => $lecture_details->SPKursID,
					'data-status' => 'disabled'
				);
				echo anchor('kursverwaltung/show_coursemgt#', $switch_button_label, $anchor_attrs);
			}
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

