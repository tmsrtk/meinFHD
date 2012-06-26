<?php include('header.php'); ?>

<!-- CONTENT -->
<div class="container container-fluid">
	<div class="row">
		<div class="span4">
			<div class="well well-small clearfix">		
				<h6>Semesterplannung</h6>
			</div>
			
			<div class="well well-small clearfix hidden-phone">
				<h2>Informationen</h2>
				<h3>oder Widgets</h3>
				<hr />
				<p>Cupcake ipsum dolor sit amet brownie I love. Marshmallow fruitcake cotton candy marshmallow. Jujubes brownie I love wafer.</p>
				<h4>widget content</h4>
				<div class="progress progress-striped progress-warning active">
				  <div class="bar" style="width: 40%;"></div>
				</div>
			</div>
			
		</div><!-- /.span4-->
		
		<div class="span8">
			<div class="well well-small clearfix">
<?php for ($n = 1; $n < 8; $n++) {?>
                <table  class="table table-bordered">
                  <thead>
                    <tr>
                      <th>
                        <?php echo $n;?>.Semester
                        30 CP / 26 SWS
                      	<a class="btn btn-mini pull-right" data-toggle="collapse" data-target="#first-collapse">
							<i class="icon-chevron-down"></i>
                        </a>
                      </th>
                    </tr>
                  </thead>
                </table>                
					<div id="first-collapse" class="collapse">
						<div class="alert alert-info clearfix">
                            PMIA
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>
                        <div class="alert alert-info clearfix">
                            Netz
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>
                        <div class="alert alert-info clearfix">
                            MedProjB
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>
                        <div class="alert alert-info clearfix">
                            KommDes
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>

                        <div class="alert alert-info clearfix">
                            BWL
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>

                        <div class="alert alert-info clearfix">
                            Profstud
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-headphones"></i> hören</button>
                            <button class="btn btn-mini pull-right" data-toggle="button"><i class="icon-pencil"></i> schreiben</button>
						</div>                                                                                                
					</div>                  
					<?php 
                    		if ($n == 7){
                    		echo '
		                    	<a class="btn pull-right" data-toggle="collapse">
									<i class="icon-plus"></i>
									ein neuer Semester einfügen
            	            	</a>
							';
							}
						}
					?>
            </div>
		</div><!-- /. span8-->
												
	</div><!--first row ends here-->						
	<div class="row">
		
		<!--optionbox at the end of page-->
		<div class="span12">
			<div class="alert alert-info clearfix">
				<a href="dashboard" class="btn btn-large btn-primary" href="#">
					<i class="icon-arrow-left icon-white"></i>
					 Dashboard
				</a>
				<a href="#" class="btn btn-large pull-right" href="#">speichern</a>
			</div>
		</div><!-- /.span12-->
		
	</div><!-- /.row-->
	
	</div><!-- /.fluid container-->
	
<!-- CONTENT ENDE-->

<?php include('footer.php'); ?>