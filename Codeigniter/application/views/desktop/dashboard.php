<?php include('header.php'); ?>
		
		<!-- CONTENT -->
		<div class="container container-fluid">
			<div class="row">
				<div class="span4">
					<div class="well widget">
			            <i class="icon icon-question-sign pull-right"></i>
			            <h5><i class="icon icon-tasks"></i>Deine Credit Points</h5>
			            <div class="widget-content">
			                <div id="leistungsContainer" style="width: 278px; height: 70px;"></div>
			            </div>
	            	</div>
				</div><!-- /.span4-->
				
				<div class="span4">
				<div class="well widget ">
		            <i class="icon icon-question-sign pull-right"></i>
		            <h5><i class="icon icon-tasks"></i>N&auml;chste Veranstaltung</h5>
		            <div class="widget-content">
		                <div class="row-fluid">
		                    <div class="span3">
		                        <span class="bold">18.05.2012</span>
		                        <span class="bold date">8:00</span>
		                    </div>
		                    <div class="span8">    
		                        <div class="bold">Mathematik 2</div>
		                        <span class="grey">Bei: </span>Prof. Dr. rer. nat D&ouml;rries
		                        <div class="grey">Raum: H 1.11 </div>
		                    </div>
		                </div>
		            </div>
		        </div>
				</div><!-- /. span4-->
				
				<div class="span4">
					 <div class="well widget">
			            <i class="icon icon-question-sign pull-right"></i>
			            <h5><i class="icon icon-tasks"></i>Ziel</h5>
			            <div class="widget-content">
			                <div id="fehlendeKurse">
			                    Dir fehlen noch <span class="badge badge-important">6</span> Kurse. Darunter befinden sich <span class="badge badge-important">3</span> Praktika.
			                </div>
			            </div>
		        </div>
				</div><!-- /.span4-->
				
			</div><!--first row ends here-->
			<div class="row"><!--second row starts here-->
				<div class="span4">
				
					<div class="well widget">
					
		            	 <i class="icon icon-question-sign pull-right" title="Willst Du es wissen wile lange noch in diesen Semester hast?" rel="tooltip"></i>
		           		 <h5><i class="icon icon-tasks"></i>Semestercountdown</h5>
		           		 
		           		 <div id="widgetContainer"class="widget-content">
		           		 
		            	 	<div id="frontSide">
		            	 	
			            	 	<div id="counter1">
			            	 		<div class="bold" style="text-align:center;">Semesterende in</div>
				            	 	<div id="countdown" class="countdownHolder"> </div>
				            	 	<p id="note"></p> 
				            	</div>
				            	 
				            	<div id="counter2">
				            	 	<div class="bold" style="text-align:center;">Klausuren in</div>
				            	 	<div id="countdown2" class="countdownHolder"> </div>
				                	<p id="note2"></p>
				                	
			            	 	</div>
			            	 	<a href="#" id="flipToSettings" class="flipLink"><i class="icon icon-info-sign pull-right"></i></a>
							</div> 
							
			                <div  id="backSide" >
			                
			                	<div class="widget-edit ">Edit Counter
			                		<form >
										<label>Suche Deine Counter </label>
										<input type="checkbox" id ="semesterende" onclick="toggle_counter($(this))"> Semester Ende
										<input type="checkbox" id ="klausurstart" onclick="toggle_counter($(this))" > Klausur Start
									</form>
			                		<a href="#" id="flipToFront" class="flipLink"><button>ok</button></a>
			                	</div>
	
			          	</div>  
			          	    		
		            	 </div> 
		            	   	
		       		</div>
		       </div><!-- /.span4-->
				
				 <div  class="span4">
					<div  class="well widget">
			          	  <i class="icon icon-question-sign pull-right"></i>
			          	  <h5><i class="icon icon-tasks"></i>Noten</h5>
			                <div class="widget-content"">Hier kommt Noten widget</div>
			           
			         
	            	</div>


				</div><!-- /. span4-->	
							
				<div class="span4">
				 <div class="well widget">
		            <i class="icon icon-question-sign pull-right"></i>
		            <h5><i class="icon icon-tasks"></i>Dein Studienverlauf</h5>
		            <div class="widget-content">
		                <div id="studienverlaufContainer" style="height: 200px;"></div>
		            </div>
		         </div>
				</div><!-- /.span4-->
				
			</div><!--second row ends here-->
			<div class="row"><!--third row starts here-->
				<div class="span4">
					<div class="well widget">
			            <i class="icon icon-question-sign pull-right"></i>
			            <h5><i class="icon icon-tasks"></i>FAQ</h5>
			            <div class="widget-content">
			                <div id="muss noch">Hilfe - MeinFHD</div>
			                <div id="muss noch">Faq</div>
			            </div>
	            	</div>
				</div><!-- /.span4-->
				
				<div class="span4">
					<div class="well widget">
			            <i class="icon icon-question-sign pull-right"></i>
			            <h5><i class="icon icon-tasks"></i>Ein Box</h5>
			            <div class="widget-content">
			                
			            </div>
	            	</div>
				</div><!-- /. span4-->
				
				<div class="span4">
					<div class="well widget">
			            <i class="icon icon-question-sign pull-right"></i>
			            <h5><i class="icon icon-tasks"></i> Noch Ein Box</h5>
			            <div class="widget-content">
			                
			            </div>
	            	</div>
				</div><!-- /.span4-->
				
			</div><!--third row ends here-->
		</div><!-- /.fluid container-->
		<!-- CONTENT ENDE-->
<?php include('footer.php'); ?>