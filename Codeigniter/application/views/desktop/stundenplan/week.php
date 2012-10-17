<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Woche<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
<style>
	.std-rel { position: relative; }
	.std-abs { position: absolute; }
	
	.std-col {
		width: 18%;
/* 		margin-right: 20px; */
	}
	
	.std-col-legend {
		width: 10%;
	}
	
	.std-event {
		overflow: hidden;
		/*border: 1px #FAFAFA solid;*/
		color: #fff;
	}
	
	.std-event-container {
		padding: 4px;
		word-break: break-word;
	}

	#stundenplan .table-condensed td.std-time-cell {
		padding: 0;
		background-image: url('../resources/img/std-time-stripes-50.png');
	}
	
</style>



<?php $event_height = 50; ?>



<?php # FB::log($stundenplan); ?>



<!-- CONTENT -->
<div class="well well-small">

	<div class="row-fluid">
		<div class="span4">
			<h1 class="headline">Stundenplan</h1>
		</div>
		<div class="span4">
			<!--Optionen-->
    		<h4>Legende</h4>
    		<span class="label btn-info">Vorlesung</span>
			<span class="label btn-primary">Übung</span>
			<span class="label btn-warning">Tutorium</span>
			<span class="label btn-success">Pratikum</span>
			<span class="label btn-inverse">Seminar</span>											    		    	   									  
		</div>
		<div class="span4">
			<div id="studienplan-einstellungen" class="btn-group pull-right">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					Einstellungen
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li class="sp-info"><a href="#">Info</a></li>
					<li class="divider"></li>
					<li class="sp-..."><a href="#">...</a></li>
					<li class="sp-..."><a href="#">...</a></li>
					<li class="divider"></li>
					<li class="sp-reset"><a href="#">Stundenplan resetten</a></li>
				</ul>
			</div>
		</div>
	</div>
	<hr>

	<!-- tab-navigation -->
	<ul class="nav nav-tabs" id="tt-tab-navi">
	<?php 
		// print navigation depending on roles this user has
		foreach ($stundenplaene as $r_id => $content)
		{
			$role_name = '';

			switch ($r_id)
			{
				case 1:
					$role_name = 'Admin';
					break;
				case 2:
					$role_name = 'Dozent';
					break;
				case 3:
					$role_name = 'Betreuer';
					break;
				case 4:
					$role_name = 'Tutor';
					break;
				case 5:
					$role_name = 'Student';
					break;
				default:
					# code...
					break;
			}

			echo '<li id="tt-tab-'.$r_id.'">';
			echo '<a href="#'.$r_id.'-tt" data-toggle="tab">'.$role_name.'</a>';
			echo '</li>';
		}
	?>
    </ul>

	<!-- tab-content -->
	<div class="tab-content">
		<?php 
		// print div for each timetable
		foreach($stundenplaene as $r_id => $content) : ?>
		<div class="tab-pane fade in active" id="<?php echo "{$r_id}" ?>-tt">

			<?php // print role-specific timetable ?>
			
			<div class="row-fluid">
				<!-- New Stundenplan Content -->
				<div id="stundenplan" class="span12">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th style="width: 100px;"></th>
								<?php $i = 1; $wochentag = date("N"); ?>
								<?php foreach ($content as $dayname => $day) : ?>
									<th <?php ($i == $wochentag) ? print 'style="background-color: #dee4c5;"' : print 'style="background-color: #eee;"';?> >
										<h3 style="font-weight:normal;"><?php print substr($dayname, 0, 2); ?>.</h3>
									</th>
									<?php $i++; ?>
								<?php endforeach; ?>
								<?php $i = 1; ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="std-time-cell">
								<?php foreach ($zeiten as $zeit) : ?>
									<div  style="height:<?php print $event_height; ?>px;">
										<?php print $zeit['Beginn']; ?>
									</div>
								<?php endforeach; ?>
								</td>
								<?php foreach ($content as $dayname => $day) : ?>
									<td class="std-time-cell" <?php if($i == $wochentag) print 'style="background-color: #dee4c5;"'; ?> >
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
													
													//$css  = 'background-color:'	. $event['display_data']['color'] . ';';
													//$css  = 'background-color:#'	. $color . ';';
													//$css .= 'width:'			. $event_width * $event['display_data']['width'] . 'px;';
													$css = 'width:'			. $event['display_data']['width'] * 100 . '%;';
													$css .= 'height:'			. $event_height * $event['display_data']['duration'] . 'px;';
													$css .= 'margin-top:'		. $event_height * ($event['display_data']['start']-1) . 'px;';								
													$css .= 'margin-left:'		. 100 * (1 / $event['display_data']['max_cols']) * $event['display_data']['column'] . '%;';
													$css .= 'z-index:'			. (100 - $event['display_data']['column']);
												?>
												
												<?php 
												$url = '';
												if ( in_array(Roles::DOZENT, $this->user_model->get_all_roles()))
												{
													$url = 'kursverwaltung/call_coursemgt_from_view/'.$event['KursID']; // show_coursemgt
													log_message('error', $url);
												}
												else
												{
													$url = 'modul/show/' . $event['KursID'];
												}
												?>
												<a href="<?php print base_url($url) ?>" class="std-abs std-event <?php print $class; ?>" style="<?php print $css; ?>">
													<div class="std-event-container">
														<h5><?php print $event['kurs_kurz']; ?> <?php print $event['VeranstaltungsformName']; ?></h5>
														<p><?php print $event['VeranstaltungsformAlternative']; ?></p>
													</div>
												</a>
											<?php endforeach; ?>
										</div>
									</td>
									<?php $i++; ?>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			

			<?php
			// implementation-detail
			// 
			// if there are : 
			// role-specific buttons or submit-forms can be build directly inside this loop
			// otherwise (passed via controller) they got to be stuffed with 
			// the right role-id to provide role-specific function
			?>



		</div>
		<?php endforeach; ?>
	</div>

	

	

</div>
<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>

$(function() {
	$('#tt-tab-navi a:last').tab('show');
});

<?php endblock(); ?>

<?php end_extend(); ?>
