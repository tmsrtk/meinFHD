<?php include('header.php'); ?>

<!-- CONTENT -->
<div class="container container-fluid">


	<?php //--------------------Loop for one day-------------------- ?>
	<?php $day_number = 0 //Incermented after a day ?>
	<?php foreach ($stundenplan as $dayname => $day) : ?>

	<div class="row day" id= "<?php echo $dayname ?>">
		<div class="span4">
		
			<!--title-->
			<div class="well well-small well-first">
	    		<h6>Stundenplan</h6>
	    		<h1>
	    			<?php echo $dayname ?>&nbsp;<small><?php echo $tage[$day_number]['Datum'] ?></small>	    		
	    		</h1>
    		</div>				
    		
		</div><!-- /.span4-->								
		<div class="span8">
		
			<div class="accordion well well-small" id="stundenplan_accordion">
									

				<?php //--------------------Loop for one course-------------------- ?>
				<?php foreach ($day as $hourID => $hour) : ?>

				<?php //--------------------If entry at this hour exists -------------------- ?>
				<?php if ($hour) { ?>

					<?php //--------------------Loop for one hour (if many courses)-------------------- ?>
					<?php foreach ($hour as $courseID => $course) : ?>

						<?php //--------------------Only if course is active-------------------- ?>
						<?php if ($course['Aktiv'] == 1) { ?>

							<!--accordion-group-->	
							<div class="well-small">
								<div class="accordion-heading">
									<table>
										<tbody>
											<tr>
												<td width="45%"><?php echo $course['Beginn']; ?> - <?php echo $course['Ende']; ?></td>
												<td width="55%"><?php echo $course['kurs_kurz']; ?>&nbsp;<?php echo utf8_decode($course['VeranstaltungsformName']); ?></td>
												<td>
													<a class="btn accordion-toggle pull-right" data-toggle="collapse" data-parent="#stundenplan_accordion" href="#collapseThree">
														<i class="icon-plus"></i>
													</a>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div id="collapseThree" class="accordion-body collapse">
									<hr />
									<div class="alert alert-info clearfix">
									<button class="btn pull-left attendant">
										<i class="icon-ok"></i>
										anwesend
									</button>
									<a href="modul.html" class="btn btn-primary pull-right">
										Details
										<i class="icon-arrow-right icon-white"></i>
									</a>					
								</div>
								</div>
							</div><!--/accordion-group-->


						<?php //--------------------End If course is active-------------------- ?>	
						<?php }  ?>

					<?php //--------------------End Loop for one hour (if many courses)-------------------- ?>	
					<?php endforeach; ?>	

				<?php //--------------------End If entry at this hour exists -------------------- ?>
				<?php }   ?>

			<?php //--------------------End Loop for one course --------------------?>			
			<?php endforeach; ?>	
				
			</div><!--/stundenplan-accordion-->
						
		</div><!-- /.span8 -->		

	</div><!--first row ends here-->	

		<?php $day_number++ ?>	
		<?php //--------------------End Loop for one day --------------------?>			
		<?php endforeach; ?>			
			
	<div class="row">
	
		<!--optionbox at the end of page-->
		<div class="span12">

			<div class="pagination pagination-centered">
			  <ul>
			    <li id="Montag">
			      <a href="#Montag">M</a>
			    </li>
			    <li id="Dienstag"><a href="#Dienstag">D</a></li>
			    <li id="Mittwoch"><a href="#Mittwoch">M</a></li>
			    <li id="Donnerstagy"><a href="#Donnerstag">D</a></li>
			    <li id="Freitag"><a href="#Freitag">F</a></li>					    
			  </ul>
			</div>
			
		</div><!-- /.span12-->
	
	</div><!-- /.row-->
	<div class="row">
		
		<!--optionbox at the end of page-->
		<div class="span12">
			<div class="alert alert-info clearfix">
				<a href="dashboard" class="btn btn-large btn-primary pull-left">
					<i class="icon-arrow-left icon-white"></i>
					 Dashboard
				</a>
				<a href="stundenplan_table" class="btn btn-large pull-right">Woche</a>
			</div>
		</div><!-- /.span12-->
		
	</div><!-- /.row-->
</div>

<?php include('footer.php'); ?>