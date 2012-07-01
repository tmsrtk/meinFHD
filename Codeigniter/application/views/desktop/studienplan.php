<?php include ('header.php'); ?>

<!-- begin : pop window (one-to-one relation between class and it's pop window) -->
<?php

	foreach($studienplan as $semester) :
		$semesterNum = 0;
		foreach($semester as $modul) :
			foreach($modul as $data) :

?>

			  <!-- begin : html for pop window -->
              <?php echo '<div id="popWindow' . $data['KursID'] . '" class="modal hide" >'; ?>
                  
                  <div class="modal-header">
                      <a class="close" data-dismiss="modal">×</a>
                      <h3><?php echo $data['Kurzname']; ?></h3>
                  </div>
                  
                  <div class="modal-body">
                  	  <?php echo '<input id="hoeren' . $data['KursID'] . '" name="hoeren' . $data['KursID'] . '" type="hidden" value="" />'; ?>
                  	  <?php echo '<input id="schreiben' . $data['KursID'] . '" name="hoeren' . $data['KursID'] . '" type="hidden" value="" />'; ?>
                  	  <h4>
                      	  <span>zum:<?php echo $data['Hoeren']; ?></span>
                      	  <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> Prüfen</button>
                      	  <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> Teilnehmen</button>
                      </h4>
                      <br />
                      <h4>
                  	  	  <span>Note:</span>
                  	  	  <?php echo '<input id="note' . $data['KursID'] . '" type="text" class="span1 input-mini pull-right" value="' . $data['Notenpunkte'] . '">';?>
					  </h4>
					  <br />
                      <h4>
                          <span>teilnehmen am:</span>
                          <?php echo '<select id="semester' . $data['KursID'] . '" class="span2">'; ?>                          
                          <?php 
                          
                              for ( $n = 0 ; $n < count($semester); $n++ ) {						
                                  echo'<option>' . $n .'. Semester</option>';
                                  
                              }				
                          
                          ?>                              
                          </select>
                      </h4>
                  </div>
                  
                  <div class="modal-footer">
                      <a href="#" class="btn" data-dismiss="modal">schließen</a>
                      <a href="#" class="btn btn-primary">speichern</a>
                  </div>
              
              </div>
              <!-- end : html for pop window -->

<?php

			endforeach;
			$semesterNum++;
		endforeach;				// end : for "Semester"
	endforeach;					// end : for "Modul"

?>
<!-- end : pop window (one-to-one relation between class and it's pop window) -->

<!-- CONTENT -->
<div class="container container-fluid">
	<div class="row">
		<div class="span4">
			<div class="well well-small clearfix">
				<h6>Semesterplanung</h6>
			</div>

			<div class="well well-small clearfix hidden-phone">
				<h2>Informationen</h2>
				<h3>oder Widgets</h3>
				<hr />
				<p>
					Cupcake ipsum dolor sit amet brownie I love. Marshmallow fruitcake cotton candy marshmallow. Jujubes brownie I love wafer.
				</p>
				<h4>widget content</h4>
				<div class="progress progress-striped progress-warning active">
					<div class="bar" style="width: 40%;"></div>
				</div>
			</div>

		</div><!-- /.span4-->


		<?php 
		
			foreach($studienplan as $semester) :
				$semesterNum = 0;				
        		foreach($semester as $modul) :

		?>

		<div class="accordion" id="accordion1">
			<div class="accordion-group">
			
							
				<!-- begin : Semester title -->				
				<div class="accordion-heading">
					<?php echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse' . $semesterNum .'">' . $semesterNum . '. Semester  </a>'; ?>
				</div>				
				<!-- end : Semester title -->
				
				
				<!-- begin : Classes title -->				
				<?php echo '<div id="collapse'.$semesterNum.'" class="accordion-body collapse">'; ?>
				<?php foreach($modul as $data) : ?>
				
					<div class="accordion-inner">
						<div class="alert alert-info clearfix">										
							<?php echo $data['Kurzname'];?>				
							<a class="btn btn-mini pull-right" data-toggle="modal" data-backdrop="static" href="#popWindow<?php echo $data['KursID']; ?>" >bearbeiten</a>							
						</div>
					</div>
					
				<?php endforeach;	// end : for "classes" ?>
				
				</div>
				<!-- begin : Classes title -->

			</div>
		</div>
		
        <?php
        
        		$semesterNum++;        		        	
				endforeach;				// end : for "Semester"
			endforeach;					// end : for "Modul"
		
		?>		

	</div><!--first row ends here-->
	
	<div class="row">
		<!--optionbox at the end of page-->
		<div class="span12">
			<div class="alert alert-info clearfix">
				<a href="dashboard" class="btn btn-large btn-primary" href="#"> <i class="icon-arrow-left icon-white"></i> Dashboard </a>
				<a href="#" class="btn btn-large pull-right" href="#">speichern</a>
			</div>
		</div><!-- /.span12-->
	</div><!-- /.row-->

</div><!-- /.fluid container-->

<!-- CONTENT ENDE-->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="http://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js"></script>
<script type="text/javascript" src="http://www.stevefenton.co.uk/cmsfiles/assets/File/jquery.mobiledragdrop.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $(".drag").mobiledraganddrop({
            targets : ".drop"
        });
    });
</script>

<?php include ('footer.php'); ?>