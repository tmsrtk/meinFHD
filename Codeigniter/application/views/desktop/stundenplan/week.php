<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Woche<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<?php $event_height = 50; # variable that defines the height of an simple event box in the table ?>

<!-- CONTENT -->
<div class="well well-small">

	<div class="row-fluid">
		<div class="span4">
            <h1>Stundenplan</h1>
		</div>
		<div class="span8">
    		<h4>Legende</h4>
    		<span class="label btn-info">Vorlesung</span>
			<span class="label btn-primary">&Uuml;bung</span>
			<span class="label btn-warning">Tutorium</span>
			<span class="label btn-success">Pratikum</span>
			<span class="label btn-inverse">Seminar</span>
		</div>
	</div>
	<hr/>

	<!-- tab-navigation -->
	<ul class="nav nav-tabs" id="tt-tab-navi">
	<?php 
		// print navigation depending on roles this user has
		foreach ($stundenplaene as $r_id => $content){
			$role_name = '';

            // find out the right name of the role that should be displayed
			switch ($r_id){
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
					break;
			}

			echo '<li id="tt-tab-'.$r_id.'">';
			echo '<a href="#tt-'.$r_id.'" data-toggle="tab">'.$role_name.'</a>';
			echo '</li>';
		}
	?>
    </ul>

	<!-- tab-content -->
	<div class="tab-content">
		<?php 
		// print div for each timetable -> one tab for each role specific timetable
		foreach($stundenplaene as $r_id => $content) : ?>
		<div class="tab-pane fade in " id="tt-<?php echo "{$r_id}" ?>">
			
			<div class="row-fluid">
				<!-- New Stundenplan Content -->
				<div class="stundenplan span12">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th style="width: 100px;"></th>
								<?php $i = 1; $wochentag = date("N"); ?>
								<?php foreach ($content as $dayname => $day) : ?>
									<th style="<?php ($i == $wochentag) ? print 'background-color: #dee4c5;' : print 'background-color: #eeeeee;';?>">
										<?php echo $dayname;?>
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
									<div style="height:<?php print $event_height; ?>px;" class="std-time-cell">
                                        <strong><?php print $zeit['StundeID']; ?></strong><br/>
										<?php print $zeit['Beginn']; ?> - <?php print $zeit['Ende']; ?>
									</div>
								<?php endforeach; ?>
								</td>
								<?php foreach ($content as $dayname => $day) : ?>
									<td class="std-time-cell" <?php if($i == $wochentag) print 'style="background-color: #dee4c5;"'; ?> >
										<div class="std-rel">
											<?php foreach ($day as $event) : ?>
												
												<?php // colors depending on the eventtype
													switch ($event['VeranstaltungsformID']){
														case 1: $class = 'btn-info'; break; // Vorlesung - blau
														case 2: $class = 'btn-primary'; break; // Uebung - rot
														case 3: $class = 'btn-warning'; break; // Seminar - gelb
														case 4: $class = 'btn-success'; break; // Praktikum - grün
														case 5: $class = 'btn-inverse'; break; // Seminaristischer Unterricht
														case 6: $class = 'btn-warning'; break; // Tutorium - grau
													}

                                                    // define the css code
													$css = 'width:' . $event['display_data']['width'] * 100 . '%;';
													$css .= 'height:' . $event_height * $event['display_data']['duration'] . 'px;';
													$css .= 'margin-top:' . $event_height * ($event['display_data']['start']-1) . 'px;';
													$css .= 'margin-left:' . 100 * (1 / $event['display_data']['max_cols']) * $event['display_data']['column'] . '%;';
													$css .= 'z-index:' . (100 - $event['display_data']['column']);
												?>
												
												<?php 
												$url = '';

                                                // if the user is an dozent, tutor or advisor the course management should be displayed (in the desired timetable) if an timetable entry is clicked
												if ( (in_array(Roles::DOZENT, $this->user_model->get_all_roles()) && Roles::DOZENT == $r_id ) ||
                                                     (in_array(Roles::TUTOR, $this->user_model->get_all_roles()) && Roles::TUTOR == $r_id) ||
                                                     (in_array(Roles::BETREUER, $this->user_model->get_all_roles()) && Roles::BETREUER == $r_id) ){

													$url = 'kursverwaltung/call_coursemgt_from_view/'.$event['KursID'];
												}
												else{ // otherwise the modul overview should be displayed
													$url = 'modul/show/' . $event['KursID'];
												}
												?>
												<a href="<?php print base_url($url) ?>" class="std-abs std-event <?php print $class; ?>" style="<?php print $css; ?>">
													<div class="std-event-container">
														<h4><?php print $event['kurs_kurz'] ?> </h4>
														<h5><?php print $event['VeranstaltungsformName'] ?> <span> <?php print $event['VeranstaltungsformAlternative']; ?> </span></h5>
														<h6><?php ( ! empty($event['Raum'])) ? print '('.$event['Raum'].')' : '' ?></h6>
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
