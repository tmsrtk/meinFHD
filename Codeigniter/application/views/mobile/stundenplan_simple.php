<?php include('header.php'); ?>
	
	<!-- CONTENT -->
	<div class="container container-fluid">

		<?php //Loop for one day ?>
		<?php foreach ($stundenplan as $dayname => $day) : ?>
			
		<div class="row">
			<div class="span4">
			
				<!--title-->
				<div class="well well-small well-first">
		    		<h6>Stundenplan</h6>
		    		<h1>
		    			<?php echo $dayname; ?> &nbsp;<small>12.02.2013</small>	    		
		    		</h1>
	    		</div>				
	    		
			</div><!-- /.span4-->						
			<div class="span8">
			
				<!--table-->
				<div class="well well-small">				
					<table class="table table-condensed centered"> 
					<!--Tablehead for all lower tables-->
					<thead>
						<tr>
							<td width="45%">Zeit</td>
							<td width="55%">Veranstaltung</td>
							<td>&nbsp;</td>
						</tr>
					</thead>
					
					<!--collapsible-->
					<tbody>
						<tr>
							<td>8:00 - 9:45</td>
							<td>DBS 1 Praktikum</td>
							<td>
								<a class="btn" data-toggle="collapse" data-target="#first-collapse">
									<i class="icon-plus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
					<!--collapsible content-->
					<div id="first-collapse" class="collapse">
						<div class="alert alert-info clearfix">
							<a class="btn pull-left">
								<i class="icon-ok"></i>
								anwesend
							</a>
							<a href="modul.html" class="btn btn-primary pull-right">
								Details
								<i class="icon-arrow-right icon-white"></i>
							</a>					
						</div>
					</div>
					
					<!--collapsible-->
					<table class="table table-condensed centered">
					<tbody>
						<tr>
							<td width="45%">10:00 - 12:45</td>
							<td width="55%">OOP 1 Praktikum</td>
							<td>
								<a class="btn" data-toggle="collapse" data-target="#second-collapse">
									<i class="icon-minus"></i>									
								</a>
							</td>
						</tr>
					</tbody>
					</table>
					<!--collapsible content-->
					<div id="second-collapse" class="collapse in">
						<div class="alert alert-info clearfix">
							<a class="btn pull-left">
								<i class="icon-ok"></i>
								anwesend
							</a>
							<a href="modul.html" class="btn btn-primary pull-right">
								Details
								<i class="icon-arrow-right icon-white"></i>
							</a>					
						</div>
					</div>
					
					<!--collapsible-->
					<table class="table table-condensed centered">
					<tbody>
						<tr>
							<td width="45%">14:00 - 16:45</td>
							<td width="55%">Mathe2 Vorlesung</td>
							<td>
								<span class="btn" data-toggle="collapse" data-target="#third-collapse">
									<i class="icon-plus"></i>
								</span>
							</td>
						</tr>
					</tbody>
					</table>
					<!--collapsible content-->
					<div id="third-collapse" class="collapse">
						<div class="alert alert-info clearfix">
							<a class="btn btn-small pull-left">
								<i class="icon-ok"></i>
								anwesend
							</a>
							<a href="modul.html" class="btn btn-large btn-primary pull-right">
								Details
								<i class="icon-arrow-right icon-white"></i>
							</a>					
						</div>
					</div>
						
				</div><!--Element ends here-->
							
			</div><!-- /.span8 -->						
			
		<?php endforeach; ?>
		<?php //Loop for one day ?>

		</div><!--first row ends here-->
		<div class="row">
		
			<!--optionbox at the end of page-->
			<div class="span12">

				<div class="pagination pagination-centered">
				  <ul>
				    <li class="active">
				      <a href="#">M</a>
				    </li>
				    <li><a href="#">D</a></li>
				    <li><a href="#">M</a></li>
				    <li><a href="#">D</a></li>
				    <li><a href="#">F</a></li>					    
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