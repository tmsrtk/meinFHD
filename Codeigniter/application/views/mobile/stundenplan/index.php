<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Stundenplan - Tag<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>
<!-- CONTENT -->
<div class="container container-fluid">
	<div id="carousel" class="carousel slide">
		<!-- Carousel items -->
		<?php echo validation_errors(); ?>
		<div class="carousel-inner">
			<?php
				//--------------------Loop for one day--------------------
				$day_number = 0 ; //Incermented after a day
				$no_courses = 1 ; //Flag, set 0 if theres at least one course today
				foreach ($stundenplan as $dayname => $day) :
			?>
			<div class="item <?php if ($tage[$day_number]['IstHeute']) echo "active"; ?>">
				<!--Tag-->
				<div class="row-fluid day" id= "<?php echo $dayname ?>">
					<!--Tagestitel-->
					<div class="span4">
						<!--title-->
						<div class="well well-small well-first">
							<h6>Stundenplan</h6>
							<h1><?php echo $dayname ?>&nbsp;<small><?php echo $tage[$day_number]['Datum'] ?></small></h1>
						</div>
					</div><!-- /.span4, Tagestitel-->
					<!--Tagesinhalte-->
					<div class="span8">
						<div class="accordion well well-small" id="accordion<?php echo $dayname;?>">
							<?php
								# Loop for one course
								foreach ($day as $hourID => $hour) :
									
									# If entry at this hour exists
									if ($hour) {
										
										# Loop for one hour (if many courses)
										foreach ($hour as $courseID => $course) :
											
											# Only if course is active
											if ($course['Aktiv'] == 1) {
												$no_courses = 0; //courses!
							?>
							<!--accordion-group-->
							<div class="accordion-heading">
								<table>
									<tbody>
										<tr>
											<td width="45%"><p><small><?php echo $course['Beginn']; ?> - <?php echo $course['Ende']; ?></small></p></td>
											<td width="55%"><?php echo $course['kurs_kurz']; ?>&nbsp;<?php echo $course['VeranstaltungsformName']; ?></td>
											<td>
												<a class="btn accordion-toggle pull-right" data-toggle="collapse" data-parent="#accordion<?php echo $dayname;?>" href="#target<?php echo $course['SPKursID']; ?>">
														<i class="icon-plus"></i>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div id="target<?php echo $course['SPKursID']; ?>" class="accordion-body collapse">
								<hr>
								<div class="fhd-box clearfix">
									<button class="btn pull-left attendant attendant<?php echo $course['SPKursID']; ?>">
										<i class="icon-ok"></i> anwesend
									</button>
									<a href="<?php print base_url(); ?>modul/show/<?php echo $course['KursID']; ?>" class="btn btn-primary pull-right">
										Details <i class="icon-arrow-right icon-white"></i>
									</a>
								</div>
							</div>
							<!--/accordion-group-->
							<?php
												# End If course is active
												}
											
											# End Loop for one hour (if many courses)
											endforeach;
											
										# End If entry at this hour exists
										}
									# End Loop for one course
									endforeach;
							?>
							<?php if($no_courses == 1) echo "Heute von zu Hause lernen!"; ?>
							<?php $no_courses = 1 //Flag reset ?>
						</div><!--/#accordion-->
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
		<!-- Carousel nav -->
		<!--<a class="carousel-control left" href="#carousel" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#carousel" data-slide="next">&rsaquo;</a>-->
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="pagination pagination-centered">
				<ul>
					<li><a class="" href="#carousel" data-slide="prev">&lsaquo;</a></li>
					<li><a class="slide-montag" href="#Montag">M</a></li>
					<li><a class="slide-dienstag" href="#Dienstag">D</a></li>
					<li><a class="slide-mittwoch" href="#Mittwoch">M</a></li>
					<li><a class="slide-donnerstag" href="#Donnerstag">D</a></li>
					<li><a class="slide-freitag" href="#Freitag">F</a></li>	
					<li><a class="" href="#carousel" data-slide="next">&rsaquo;</a></li>
				</ul>
			</div>
		</div><!-- /.span12-->
	</div><!-- /.row-fluid -->
	<div class="row-fluid">
		<div class="span12">
			<div class="fhd-box clearfix">
				<a href="<?php print base_url('dashboard/index'); ?>" class="btn btn-large btn-primary pull-left">
					<i class="icon-arrow-left icon-white"></i>
					zurück
				</a>
				<a href="<?php print base_url('stundenplan/woche'); ?>" class="btn btn-large pull-right">Woche</a>
			</div>
		</div><!-- /.span12-->
	</div><!-- /.row-fluid -->
	
	
</div>
<?php endblock(); ?>
<?php end_extend(); ?>
