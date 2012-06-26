<?php include('header.php'); ?>
	
	<?php echo $title; ?>
    <!-- CONTENT -->
	<div class="container container-fluid">
		<div class="row">
			<div class="span4">
			
				<!--title-->
				<div class="well well-small well-first">
		    		<h6>FAQ und Hilfe</h6>
	    		</div>				
	    		
			</div><!-- /.span4-->						
			<div class="span8">
			
				<!--table-->
				<div class="well well-small">				
					<table class="table table-condensed centered"> 
					<!--Tablehead for all lower tables-->
					<thead>
						<tr>
							<td><strong>Häufige Fragen / FAQ</strong></td>
						</tr>
					</thead>
					
					<!--collapsible-->
					<tbody>
						<tr>
							<td>Warum soll ich mein Studium jetzt schon planen?</td>
							<td>
								<a class="btn pull-right" data-toggle="collapse" data-target="#first-collapse">
									<i class="icon-plus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
					<!--collapsible content-->
					<div id="first-collapse" class="collapse">
						<div class="alert alert-info clearfix">
                        	<!--collapsible content-->
							<p>Damit Du direkt einen guten Überblick über die Kurse und Prüfungen, die auf Dich zukommen, hast.</p>
						</div>
					</div>
					
					<!--collapsible-->
					<table class="table table-condensed centered"> 
					<tbody>
						<tr>
							<td>Wie kann ich mein Studium jetzt schon planen?</td>
							<td>
								<a class="btn pull-right" data-toggle="collapse" data-target="#second-collapse">
									<i class="icon-minus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
                    
					<!--collapsible content-->
					<div id="second-collapse" class="collapse in">
						<div class="alert alert-info clearfix">
							<!--collapsible content-->
							<p>Indem Du einen Semesterplan anlegst und ihn individuell einrichtest. Das kannst du über den Hauptmenüpunkt "Studium planen" tun.</p>				
						</div>
					</div>
					
					<!--collapsible-->
					<table class="table table-condensed centered"> 
					<tbody>
						<tr>
							<td>Wie kann ich mich für ein Praktikum anmelden?</td>
							<td>
								<a class="btn pull-right" data-toggle="collapse" data-target="#third-collapse">
									<i class="icon-plus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
                    
					<!--collapsible content-->
					<div id="third-collapse" class="collapse">
						<div class="alert alert-info clearfix">
							<!--collapsible content-->
							<p>Indem du im "<strong><a href="stundenplan_simple.html">Stundenplan</a></strong>" auf den Link "anmelden" der jeweiligen Praktikumsgruppe klickst und anschließend Deine nderungen speicherst. Voraussetzung dafür ist aber, dass Du vorher einen Studiengang und Deinen Studienbeginn in Deinen persönlichen Daten angegeben hast.</p>				
						</div>
					</div>
						
				</div><!--Element ends here-->
							
			</div><!-- /.span8 -->

			<div class="span8">
			
				<!--table-->
				<div class="well well-small">				
					<table class="table table-condensed centered"> 
					<!--Tablehead for all lower tables-->
					<thead>
						<tr>
							<td><strong>Hilfe</strong></td>
						</tr>
					</thead>
					
					<!--collapsible-->
					<tbody>
						<tr>
							<td>Über "Persönliche Daten"</td>
							<td>
								<a class="btn pull-right" data-toggle="collapse" data-target="#fourth-collapse">
									<i class="icon-plus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
					<!--collapsible content-->
					<div id="fourth-collapse" class="collapse">
						<div class="alert alert-info clearfix">
                        	<!--collapsible content-->
							<p>Nähere Informationen zu diesem Thema findest Du auf der zugehörigen <strong><a href="Informationseite.php">Informationsseite</a></strong>.</p>
						</div>
					</div>
					
					<!--collapsible-->
					<table class="table table-condensed centered"> 
					<tbody>
						<tr>
							<td>Über "Studienplanung"</td>
							<td>
								<a class="btn pull-right" data-toggle="collapse" data-target="#fifth-collapse">
									<i class="icon-minus"></i>
								</a>
							</td>
						</tr>
					</tbody>
					</table>
                    
					<!--collapsible content-->
					<div id="fifth-collapse" class="collapse in">
						<div class="alert alert-info clearfix">
							<!--collapsible content-->
							<p>Nähere Informationen zu diesem Thema findest Du auf der zugehörigen <strong><a href="#">Informationsseite</a></strong>.</p>			
						</div>
					</div>			
				</div><!--Element ends here-->							
			</div><!-- /.span8 -->				
			
		</div><!--second row ends here-->
		<div class="row">
			
			<!--optionbox at the end of page-->
			<div class="span12">
				<div class="alert alert-info clearfix">
					<a href="dashboard" class="btn btn-large btn-primary pull-left">
						<i class="icon-arrow-left icon-white"></i>
						 Dashboard
					</a>
				</div>
			</div><!-- /.span12-->
			
		</div><!-- /.row-->
	</div>
    <!-- CONTENT END-->

<?php include('footer.php'); ?>