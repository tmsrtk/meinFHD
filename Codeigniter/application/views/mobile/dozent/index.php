<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Dozent<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

<<<<<<< HEAD
			<div class="span4">
				<div class="well well-small clearfix">
			
					<h6>Dozent</h6>
			
					<!--Titel-->
	    			<h3><?php echo $dozentinfo[0]['Titel']; ?></h3>
	    			<h1><?php echo $dozentinfo[0]['Vorname']; ?> <?php echo $dozentinfo[0]['Nachname']; ?></h1>
					<hr />
					<!--Optionen-->

					<?php //--------------------If there is a URL in the dozentinfo Table --------------------?>

					<?php if ($dozentinfo[0]['BildURL'] == '') { ?>

						<img src="<?php echo base_url('resources\img\dozent_standart.png'); ?>" alt="dozent">

					<?php }else { ?>

						<img src="<?php echo $dozentinfo[0]['BildURL']; ?>" alt="dozent">

					<?php //--------------------EndIf there is a URL in the dozentinfo Table --------------------?>
					<?php } ?>

		    		<a href="mailto: <?php echo $dozentinfo[0]['Email'] ;  ?>" class="btn btn-large pull-right">
		   				<i class="icon-envelope"></i>
		   				 Mail
		   			</a>
				    		    	   									  
				</div>
=======
			<div class="span6 well">
					<h6><?php echo $dozentinfo[0]['Titel']; ?></h6>	    			
	    			<h1><?php echo $dozentinfo[0]['Vorname']; ?> <?php echo $dozentinfo[0]['Nachname']; ?></h1>				
					<table class="table table-condensed" width="100%">
						<thead>
							<tr>
								<th width="100px">Informationen</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>								
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Büro</td>
								<td colspan="2"><?php echo $dozentinfo[0]['Raum']; ?></td>
							</tr>
							<tr>
								<td>Fachbereich</td>
								<td colspan="2"><?php echo $dozentinfo[0]['FachbereichID']; ?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td><?php echo $dozentinfo[0]['Email']; ?></td>
								<td>
									<a href="mailto: <?php echo $dozentinfo[0]['Email']; ?>" class="btn pull-right">
										<i class="icon-envelope"></i>
									</a>
		   						</td>
							</tr>
						</tbody>
					</table>				    		    	   									  

>>>>>>> view-design added
				
			</div><!-- /.span4-->
													
		</div><!--first row ends here-->		
		
		<div class="row-fluid">
			
			<!--optionbox at the end of page-->
			<div class="span12">
				<div class="fhd-box clearfix">
					<a href="<?php echo base_url('stundenplan'); ?>" class="btn btn-large btn-primary" href="#">
						<i class="icon-arrow-left icon-white"></i>
						 Modulübersicht
					</a>
				</div>
			</div><!-- /.span12-->
	
<?php endblock(); ?>
<?php end_extend(); ?>