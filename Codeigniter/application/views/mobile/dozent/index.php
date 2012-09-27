<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Dozent<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>

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
				
			</div><!-- /.span4-->
			
			<div class="span4">
				
				<!--Vorlesung-->
				<div class="well well-small clearfix">
					<h2>Informationen</h2>
					<hr />
					<h4>Email: <?php echo $dozentinfo[0]['Email']; ?></h4>
					<h4>Fachbereich: <?php echo $dozentinfo[0]['FachbereichID']; ?></h4>						
					<h4>Büro: <?php echo $dozentinfo[0]['Raum']; ?></h4>
				</div>

			</div>	
													
		</div><!--first row ends here-->		
		
		<div class="row-fluid">
			
			<!--optionbox at the end of page-->
			<div class="span12">
				<div class="alert alert-info clearfix">
					<a href="<?php echo base_url('stundenplan'); ?>" class="btn btn-large btn-primary" href="#">
						<i class="icon-arrow-left icon-white"></i>
						 Modulübersicht
					</a>
				</div>
			</div><!-- /.span12-->
	
<?php endblock(); ?>
<?php end_extend(); ?>