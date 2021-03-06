<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Modulübersicht<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

	<!- ------------------------------------------------ -->
	<!-- MODULÜBERSICHT -------------------------------- -->
	<!- ------------------------------------------------ -->
	
	<!-- CONTENT -->
	<div class="container container-fluid">
		<div class="row">
			<div class="well well-small">
				<div class="span10">
					<!--Titel-->
		    			<h1><?php echo $courseinfo['Modulinfo']['kurs_kurz'] ?> | <?php echo $courseinfo['Modulinfo']['Kursname'] ?></h1>
		    			<h4><span>bei </span><?php echo $courseinfo['Modulinfo']['DozentTitel'] . " " . $courseinfo['Modulinfo']['DozentVorname'] . " " . $courseinfo['Modulinfo']['DozentNachname'];                                                ?></h4>
				</div>
				<div class="span2">
					<a href="<?php echo base_url('dozent/show/'.$courseinfo['Modulinfo']['DozentID']) ?>" class="btn btn-large btn-primary pull-right">
						<i class="icon-arrow-right icon-white"></i>
						Dozent
					</a>
				</div>
				<div class="clearfix"></div>
				<hr />
			</div>
		</div><!--first row ends here-->

		<div class="row">
			<?php //--------------------Loop for kurse --------------------?>			
			<?php foreach ($courseinfo['Kurse'] as $key => $courselist) : ?>

				<?php //--------------------If there is any course of that kind --------------------?>
				<?php if ( ! empty($courselist))  : ?>
				
				<div class="span4">

					<div class="well well-small">
						<h2><?php echo $key; ?>&nbsp;<small></small></h2>
						<h3><?php echo $courselist[0]['Raum']; ?>

						<?php //--------------------If there are alternatives --------------------?>
						<?php if ( ! $courselist[0]['VeranstaltungsformAlternative'] == '')  { ?>
						
						</h3>
						
						<table class="table centered"> 
							<thead>
								<tr>
									<td width="25%">Gruppe</td>
									<td>Termin</td>
									<td></td>
								</tr>
							</thead>
							<tbody>
								<?php //--------------------If user already is enrolled --------------------?>
								<?php if ($courselist[0]['aktiv'] == 1)  { ?>

									<!-vom User belegter Termin--->
									<tr class="alert alert-success">
										<td><?php echo $courselist[0]['VeranstaltungsformAlternative'] ?></td>
										<td><?php echo substr($veranstaltung['TagName'],0,2); ?>/ <?php echo $courselist[0]['Beginn'] ?> - <?php echo $courselist[0]['Ende'] ?></td>
										<td>
											<a href="<?php echo base_url('modul/withdraw_from_course/'. $courselist[0]['KursID'].'/'. $courselist[0]['SPKursID'].'/'.  $courselist[0]['GruppeID'] ); ?>" class="btn btn-large btn-danger pull-right">
												<i class="icon-remove icon-white"></i>
											</a>
										</td>
									</tr><!--belegeter Termin Ende-->

								<?php //--------------------EndIf user already is enrolled -------------------- ?>
								<?php } ?>


								<?php //--------------------If user not already enrolled --------------------?>
								<?php if ($courselist[0]['aktiv'] == 0)  { ?>

									<?php //--------------------Loop for all alternatives --------------------?>	
									<?php foreach ($courselist as $veranstaltung) : ?>

										<tr>
											<td><?php echo $veranstaltung['VeranstaltungsformAlternative'] ?></td>
											<td><?php echo substr($veranstaltung['TagName'],0,2); ?>/ <?php echo $veranstaltung['Beginn']; ?> - <?php echo $veranstaltung['Ende']; ?></td>
											<td>
											<?php if ($veranstaltung['Anmeldung_zulassen'] == 0 || 
														$veranstaltung['Anzahl Teilnehmer'] >= $veranstaltung['TeilnehmerMax']) : ?>								
												<a href="#" class="btn btn-large pull-right">
													<i class="icon-minus-sign"></i>
												</a>
											<?php else: ?>
												<a href="<?php echo base_url('modul/enroll_to_course/'. $veranstaltung['KursID'].'/'. $veranstaltung['SPKursID'].'/'.  $veranstaltung['GruppeID'] ); ?>" class="btn btn-large pull-right">
													<i class="icon-ok"></i>
												</a>
											<?php endif; ?>
											</td>
											<td><?php echo $veranstaltung['Anzahl Teilnehmer'] ?> / <?php echo $veranstaltung['TeilnehmerMax'] ?></td>
										</tr>
									<?php endforeach; ?>

								<?php //--------------------EndIf user already is enrolled -------------------- ?>
								<?php } ?>


							</tbody>
						</table>

						<?php //--------------------EndIf there are alternatives , Begin Else there are none -------------------- ?>
						<?php } else { ?>

							<?php //--------------------Loop for Veranstaltung without alternatives--------------------?>			
							<?php foreach ($courselist as $veranstaltung) : ?>

								/ <?php echo $veranstaltung['Beginn']; ?> - <?php echo $veranstaltung['Ende']; ?> / <?php  echo $veranstaltung['TagName']; ?>s</h3>
						
							<?php //--------------------End Loop for Veranstaltung without alternatives--------------------?>			
							<?php endforeach; ?>

						<?php //--------------------EndElse there are no alternatives -------------------- ?>
						<?php } ?>

						<hr class="hidden-phone" />

					</div>
					<?php //--------------------EndIF there is any course of that kind  -------------------- ?>

				</div><!-- /.span4-->
				<?php endif; ?>

			<?php //--------------------End Loop for Veranstaltung --------------------?>			
			<?php endforeach; ?>

		</div><!-- /.row-->
		
   	</div><!-- /.fluid container-->
   	
	<!-- CONTENT ENDE-->
<?php endblock(); ?>
<?php end_extend(); ?>