<?php include ('header.php'); ?>

<!-- begin : pop window (one-to-one relation between class and it's pop window) -->
<?php foreach($studienplan as $semester) : ?>
	<?php $semesterNum = 0; ?>
		<?php foreach($semester as $modul) : ?>
			<?php foreach($modul as $data) : ?>

			  	<!-- begin : html for pop window -->
			  	<form id="form1" name="form1" method="post" action="/meinFHD/Codeigniter/studienplan/speichern">
              	<?php echo '<div id="popWindow' . $data['KursID'] . '" class="modal hide" >'; ?>
                  
                	<div class="modal-header">
                      	<a class="close" data-dismiss="modal">×</a>
                      	<h3><?php echo $data['Kurzname']; ?></h3>
                  	</div>
                  
                  	<div class="modal-body">
                  		  <?php echo '<input id="hoeren_' . $data['KursID'] . '" name="hoeren_' . $data['KursID'] . '" type="hidden" value="" />'; ?>
    	              	  <?php echo '<input id="schreiben_' . $data['KursID'] . '" name="schreiben_' . $data['KursID'] . '" type="hidden" value="" />'; ?>
    	              	  <h4>
        	              	  <span>zum:<?php echo $data['Teilnehmen']; ?></span>
            	          	  <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> Prüfen</button>
                	      	  <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> Teilnehmen</button>
                    	  </h4>
				          <br />
    	                  <h4>
        	          	  	  <span>Note:</span>
            	      	  	  <?php echo '<input id="note_' . $data['KursID'] . '" name="note_'. $data['KursID'] .'" type="text" class="span1 input-mini pull-right" value="' . $data['Notenpunkte'] . '">';?>
						  </h4>
						  <br />
                     	 <h4>
	                          <span>teilnehmen am:</span>
    	                      <?php echo '<select id="semester_' . $data['KursID'] . '" name="semester_' . $data['KursID'] . '" class="span2">'; ?>                          
        	                  <?php for ( $n = 0 ; $n < count($semester); $n++ ) : ?>						
                                  <?php echo'<option>' . $n .'. Semester</option>'; ?>                                  
                              <?php endfor;?>               
                          </select>
                      </h4>
                  </div>
                  
                  <div class="modal-footer">
                      <a href="#" class="btn" data-dismiss="modal">schließen</a>
                      <button type="submit" class="btn btn-primary">speichern</button>
                  </div>
              
              </div>
              </form>
              <!-- end : html for pop window -->

			<?php endforeach; ?>
		<?php $semesterNum++; ?>
		<?php endforeach;	// end : for "Semester" ?>
<?php endforeach;			// end : for "Modul" ?>
<!-- end : pop window (one-to-one relation between class and it's pop window) -->



<!-- begin : CONTENT -->
<div class="container container-fluid">	
	
	<!-- begin : the first row -->
	<div class="row">
				
		<!-- begin : the title of this page -->
		<div class="span4">
			<div class="well well-small clearfix">
				<h6>Semesterplanung</h6>
			</div>
		</div>
		<!-- end : the title of this page -->

		<!-- begin : the Semester list -->
		<div class="span8">
			<?php foreach($studienplan as $semester) : ?>
				<?php $semesterNum = 0; ?>
					<?php foreach($semester as $modul) : ?>
						<div class="accordion" id="accordion">
							<div class="accordion-group">
															
								<!-- begin : Semester title -->				
								<div class="accordion-heading">
									<div class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $semesterNum; ?>">
										<?php echo $semesterNum; ?>.&nbsp;Semester
										<i class="icon-plus pull-right"></i>
									</div>
								</div>				
								<!-- end : Semester title -->		
				
								<!-- begin : Classes title -->				
								<div id="collapse_<?php echo $semesterNum; ?>" class="accordion-body collapse">
									<div class="accordion-inner">
										<?php foreach($modul as $data) : ?>
											<div class="alert alert-info clearfix">										
												<?php echo $data['Kurzname'];?>				
												<a class="btn btn-mini pull-right" data-toggle="modal" data-backdrop="static" href="#popWindow<?php echo $data['KursID']; ?>" >bearbeiten</a>							
											</div>
										<?php endforeach;	// end : for "classes" ?>
									</div>
								</div>	
								<!-- begin : Classes title -->
								
							</div>
						</div>
		
        			<?php $semesterNum++; ?>
        			<?php endforeach;	// end : for "Semester" ?>
        	<?php endforeach; 			// end : for "Modul"?>	
		</div>
		<!-- end : the Semester list -->
	</div>
	<!-- end : the first row -->
	
	<!-- begin : the second row -->
	<div class="row">
		<!-- begin : optionbox -->
		<div class="span12">
			<div class="alert alert-info clearfix">
				<a href="dashboard" class="btn btn-large btn-primary" href="#"> <i class="icon-arrow-left icon-white"></i> Dashboard </a>
				<a href="#" class="btn btn-large pull-right" href="#"><i class="icon-plus"></i>&nbsp;Semester</a>
			</div>
		</div>
		<!-- end : optionbox -->
	</div>
	<!-- begin : the second row -->

</div>
<!-- end : CONTENT-->

<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="http://twitter.github.com/bootstrap/assets/js/bootstrap-collapse.js"></script>
<script type="text/javascript">

    /* icon-switch for accordions*/
    $(function() {
        $('.collapse').on('shown', function(e) {
            	$(e.target).parent().find('i.icon-plus').removeClass('icon-plus').addClass('icon-minus');
       		}).on('hidden', function(e) {
            	$(e.target).parent().find('i.icon-minus').removeClass('icon-minus').addClass('icon-plus');
        	});
    });

</script>

<?php include ('footer.php'); ?>