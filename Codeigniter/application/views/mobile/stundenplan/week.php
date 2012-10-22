<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Woche<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php $event_height = 30; ?>

<!-- CONTENT -->
<div class="row-fluid">
	<div class="span12 well">
		<h6>Stundenplan</h6>
		<h1>Wochenansicht</h1>

		<!--Optionen-->
		<span class="label btn-info">Vorlesung</span>
		<span class="label btn-primary">Übung</span>
		<span class="label btn-warning">Tutorium</span>
		<span class="label btn-success">Pratikum</span>
		<span class="label btn-inverse">Seminar</span>											    		    	   									  

		<!-- New Stundenplan Content -->
		<div id="stundenplan">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th style="width: 40px;">&nbsp;</th>
						<?php foreach ($stundenplan as $dayname => $day) : ?>
							<th>
								<?php print substr($dayname, 0, 2); ?>.
							</th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="std-time-cell">
						<?php foreach ($zeiten as $zeit) : ?>							
							<div style="height:<?php print $event_height; ?>px;">
								<?php print $zeit['Beginn']; ?>
							</div>
						<?php endforeach; ?>
						</td>
						
						<?php foreach ($stundenplan as $dayname => $day) : ?>
							<td class="std-time-cell">
								<div class="std-rel">
									<?php foreach ($day as $event) : ?>
										
										<?php
											switch ($event['VeranstaltungsformID'])
											{
												case 1: $color = '3a87ad'; break; // Vorlesung - blau
												case 2: $color = 'b94a48'; break; // Übung - rot
												case 3: $color = 'f89406'; break; // Seminar - gelb
												case 4: $color = '468847'; break; // Praktikum - grün
												case 5: $color = '999999'; break; // 
												case 6: $color = '999999'; break; // Tutorium - grau
											}
											
											switch ($event['VeranstaltungsformID'])
											{
												case 1: $class = 'btn-info'; break; // Vorlesung - blau
												case 2: $class = 'btn-primary'; break; // Übung - rot
												case 3: $class = 'btn-warning'; break; // Seminar - gelb
												case 4: $class = 'btn-success'; break; // Praktikum - grün
												case 5: $class = 'btn-inverse'; break; // 
												case 6: $class = 'btn-warning'; break; // Tutorium - grau
											}

											$css = 'width:'			. $event['display_data']['width'] * 100 . '%;';
											$css .= 'height:'			. $event_height * $event['display_data']['duration'] . 'px;';
											$css .= 'margin-top:'		. $event_height * ($event['display_data']['start']-1) . 'px;';								
											$css .= 'margin-left:'		. 100 * (1 / $event['display_data']['max_cols']) * $event['display_data']['column'] . '%;';
											$css .= 'z-index:'			. (100 - $event['display_data']['column']);
										?>

										<a href="<?php print base_url('modul/show/' . $event['KursID']); ?>" class="std-abs std-event <?php print $class; ?>" style="<?php print $css; ?>">
											<div class="std-event-container">
												<h5><?php print $event['kurs_kurz']; ?> <?php print $event['VeranstaltungsformName']; ?></h5>
											</div>
										</a>
									<?php endforeach; ?>
								</div>
							</td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box clearfix">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary">Übersicht</a>
			<a href="<?php print base_url('stundenplan'); ?>" class="btn btn-large pull-right">Tag</a>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<?php endblock(); ?>
<?php end_extend(); ?>
