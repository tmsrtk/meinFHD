<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<div class="row-fluid">

<div id="carousel" class="carousel slide">
	<!-- Carousel items -->
	<?php echo validation_errors(); ?>
	
	<div class="carousel-inner">
		<?php
			//--------------------Loop for one day--------------------
			$day_number = 0 ; //Incremented after a day
			$no_courses = 1 ; //Flag, set 0 if theres at least one course today
			foreach ($stundenplan as $dayname => $day) :
		?>
		<div class="item <?php if ($tage[$day_number]['IstHeute']) echo "active"; ?>">
			<!--Tag-->
			<div class="day" id= "<?php echo $dayname ?>">
				<!--Tagestitel-->
				<div class="span6 well">
					
					<h6>Stundenplan</h6>
					<h1><?php echo $dayname ?>&nbsp;<small><?php echo $tage[$day_number]['Datum'] ?></small></h1>
					
					<table class="table table-condensed" width="100%">
						<thead>
							<tr>
								<th width="70px">Uhrzeit</th>
								<th>Veranstaltung</th>
								<th width="40px">Details</th>
							</tr>
						</thead>
						<tbody>
						<?php
							# Loop for one course
							foreach ($day as $hourID => $hour) :
								
								# If entry at this hour exists
								if ($hour) :
									
									# Loop for one hour (if many courses)
									foreach ($hour as $courseID => $course) :
										
										# Only if course is active
										if ($course['Aktiv'] == 1) :
											$no_courses = 0; //courses!
						?>
						
						
											<tr>
												<td>
													<small><?php echo $course['Beginn']; ?> - <?php echo $course['Ende']; ?></small>
												</td>
												<td><?php echo $course['kurs_kurz']; ?>&nbsp;<?php echo $course['VeranstaltungsformName']; ?></td>
												<td>
													<a href="<?php print base_url('modul/show/' . $course['KursID']); ?>" class="btn btn-primary pull-right">
													<i class="icon-arrow-right icon-white"></i>
													</a>
												</td>
											</tr>
						

						
						<?php
											
											endif;
										
										# End Loop for one hour (if many courses)
										endforeach;
										
									# End If entry at this hour exists
									endif;
								# End Loop for one course
								endforeach;
						?>
						<?php if($no_courses == 1) echo "Heute von zu Hause lernen!"; ?>
						<?php $no_courses = 1 //Flag reset ?>
						</tbody>
					</table>
				</div><!-- /.span8,Tagesinhalte -->
			</div><!-- /.row, Tag-->
		</div>
		<?php
			# increment day_number
			$day_number++;
			
			# End Loop for one day
			endforeach;
		?>
	</div>
</div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="pagination pagination-centered">
			<ul>
				<li><a href="#carousel" data-slide="prev">&lsaquo;</a></li>
				<li><a href="#Montag">M</a></li>
				<li><a href="#Dienstag">D</a></li>
				<li><a href="#Mittwoch">M</a></li>
				<li><a href="#Donnerstag">D</a></li>
				<li><a href="#Freitag">F</a></li>	
				<li><a href="#carousel" data-slide="next">&rsaquo;</a></li>
			</ul>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<div class="row-fluid">
	<div class="span12">
		<div class="fhd-box clearfix">
			<a href="<?php print base_url('dashboard/mobile'); ?>" class="btn btn-large btn-primary pull-left">
				<i class="icon-arrow-left icon-white"></i>
				zurück
			</a>
			<a href="<?php print base_url('stundenplan/woche'); ?>" class="btn btn-large pull-right">Woche</a>
		</div>
	</div><!-- /.span12-->
</div><!-- /.row-fluid -->

<?php endblock(); ?>
<?php end_extend(); ?>