<?php
include ('header.php');
?>

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

		<!-- begin : pop window of modification for the class-->
		<div id="backdroptrue" class="modal hide" >
			
			<div class="modal-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3><small>bearbeiten zum</small> PMIA</h3>
			</div>
			
			<div class="modal-body">
				<h4>
					zum:
					<button class="btn btn-mini pull-right" data-toggle="button">
						<i class="icon-headphones"></i> Prüfen
					</button>
					<button class="btn btn-mini pull-right" data-toggle="button">
						<i class="icon-pencil"></i> Hören
					</button>
				</h4>
				<br />
				<h4>
					teilnehmen am:
					<select class="span1">
						<option>0. Semester</option>
						<option>1. Semester</option>
						<option>2. Semester</option>
						<option>3. Semester</option>
						<option>4. Semester</option>
						<option>5. Semester</option>
					</select>
				</h4>
			</div>
			
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">schließen</a>
				<a href="#" class="btn btn-primary">speichern</a>
			</div>

		</div><!-- end : pop window of modification for the class -->


		<div class="accordion" id="accordion1">
			<div class="accordion-group">
				
				<!-- begin : the title of Semester -->
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse1">0.Semester 30 CP / 26 SWS</a>
				</div><!-- end : the title of Semester -->

				
				<!-- begin : the title of classes -->
				<div id="collapse1" class="accordion-body collapse">
					<div class="accordion-inner">
						<div class="alert alert-info clearfix">
							PMIA
							<a class="btn btn-mini pull-right" data-toggle="modal" href="#backdroptrue" >bearbeiten</a>
						</div>
					</div>
				</div><!-- begin : the title of classes -->

			</div>
		</div>

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

<?php
include ('footer.php');
?>