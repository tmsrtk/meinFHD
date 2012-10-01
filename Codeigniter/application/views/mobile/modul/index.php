<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Modul<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php
	
	$course_to_enroll = array();
	
	foreach ($courseinfo['Kurse'] as $key => $courselist) {
		if ( ! empty($courselist)) {
			if ($courselist[0]['VeranstaltungsformAlternative'] != '') {
				$course_to_enroll[$key] = $courselist;
			}
		}
	}
	
?>
			

<!-- MODULÜBERSICHT -->

<div class="row-fluid">
	<div class="span6 well">
	
		<h6>Modulübersicht</h6>
		<h1><?php echo $courseinfo['Modulinfo']['kurs_kurz']; ?></h1>
		
		<!-- Dozent -->
	    <table class="table table-condensed" width="100%">
			<thead>
				<tr>
					<th>Dozent</th>
					<th width="40px">Details</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $courseinfo['Modulinfo']['DozentTitel'] . " " . $courseinfo['Modulinfo']['DozentVorname'] . " " . $courseinfo['Modulinfo']['DozentNachname'];?></td>
					<td>
						<a href=<?php echo base_url('dozent/'. $courseinfo['Modulinfo']['DozentID'] ); ?> class="btn btn-primary pull-right">
		    				<i class="icon-arrow-right icon-white"></i>		    				 
		    			</a>
					</td>
				</tr>
			</tbody>
		</table>
		
		<!-- Termine -->
		<table class="table table-condensed" width="100%">
			<thead>
				<tr>
					<th width="70px">Termin</th>
					<th>Zeit</th>					
					<th width="40px">Raum</th>
				</tr>
			</thead>
			<tbody>
				<!-- Without enroll -->
				<?php foreach ($courseinfo['Kurse'] as $key => $courselist) : ?>
					<?php if ( ! empty($courselist)) : ?>
						<?php if ($courselist[0]['VeranstaltungsformAlternative'] == '') : ?>													
							<?php foreach ($courselist as $veranstaltung) : ?>
							
								<tr>
									<td><?php echo $key; ?></td>
									<td><?php echo substr($veranstaltung['TagName'],0,2). '. ' . $veranstaltung['Beginn'] . ' - ' . $veranstaltung['Ende']; ?></td>	
									<td><?php echo $courselist[0]['Raum']; ?></td>
								</tr>
								
							<?php endforeach; ?>							
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				
			</tbody>
		</table>		    	   		    										  
		
	</div>
		
<!-- With enroll -->
<?php if ( ! empty($course_to_enroll)) : ?>
	<div class="span6 well">
		<?php foreach ($course_to_enroll as $key => $courselist) : ?>
			
			<h3><?php echo $key; ?> <small><?php echo $courselist[0]['Raum']; ?></small></h3>
			<table class="table table-condensed"> 
				<thead>
					<tr>
						<th width="70px">Gruppe</th>
						<th>Zeit</th>
						<th>teilnehmen</th>
					</tr>
				</thead>
				<tbody>
	
					<?php if ($courselist[0]['aktiv'] == 1) : ?>
						<!-vom User belegter Termin--->
						<tr class="alert alert-success">
							<td><?php echo $courselist[0]['VeranstaltungsformAlternative'] ?></td>
							<td><?php echo substr($veranstaltung['TagName'],0,2); ?>. <?php echo $courselist[0]['Beginn'] ?> - <?php echo $courselist[0]['Ende'] ?> </td>
							<td>

							<?php if ($courselist[0]['Anmeldung_zulassen'] == 1): ?>
								<a href="<?php echo base_url('modul/withdraw_from_course/'. $courselist[0]['KursID'].'/'. $courselist[0]['SPKursID'].'/'.  $courselist[0]['GruppeID'] ); ?>" class="btn btn-primary pull-right">
									<i class="icon-remove icon-white"></i>
								</a>
							<?php endif ?>

							</td>
						</tr><!--belegeter Termin Ende-->
					<?php endif; ?>
	
					<?php if ($courselist[0]['aktiv'] == 0)  : ?>
						<?php foreach ($courselist as $veranstaltung) : ?>
							<tr>
								<td><?php echo $veranstaltung['VeranstaltungsformAlternative'] ?></td>
								<td><?php echo substr($veranstaltung['TagName'],0,2); ?>. <?php echo $veranstaltung['Beginn']; ?> - <?php echo $veranstaltung['Ende']; ?></td>
								<td>

								<?php if ($courselist[0]['Anmeldung_zulassen'] == 1): ?>
									<a href="<?php echo base_url('modul/enroll_to_course/'. $veranstaltung['KursID'].'/'. $veranstaltung['SPKursID'].'/'.  $veranstaltung['GruppeID'] ); ?>" class="btn pull-right">
										<i class="icon-ok"></i>
									</a>		
								<?php endif ?>	

								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
						
				</tbody>
			</table>
			
		<?php endforeach; ?>
	</div>
<?php endif; ?>	

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">Übersicht</a>
			<a href="<?php print base_url('stundenplan'); ?>" class="btn btn-large pull-right">Stundenplan</a>
		</div>
	</div>
</div>
	
<!-- CONTENT ENDE-->
<?php endblock(); ?>
<?php end_extend(); ?>
