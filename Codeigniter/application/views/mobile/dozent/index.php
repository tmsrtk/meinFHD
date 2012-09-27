<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title');?><?php get_extended_block();?> - Dozent<?php endblock(); ?>
<?php startblock('content'); # content for this view ?>


	<!- ------------------------------------------------ -->
	<!-- DOZENTÜBERSICHT -------------------------------- -->
	<!- ------------------------------------------------ -->

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>meinFHDmobile PROTOTYPE</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="HandheldFriendly" content="true" />
	
		<!--apple meta tags-->
		<meta name="apple-mobile-web-app-capable" content="yes" /> 
		
		<!-- Styles -->		
		<link rel="stylesheet/less" type="text/css" href="bootstrap/less/bootstrap.less">
		<style type="text/css">
			body {
				padding-top: 80px;
				padding-bottom: 40px;
			}
		</style>
		<link rel="less/stylesheet" type="text/css" href="bootstrap/less/responsive.less" />
				
		<!--LESS compiler-->
		<script src="bootstrap/js/less.js" type="text/javascript"></script>
		
	</head><!-- /head -->
	<body>	
	<!--NAVIGATION-->
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#">meinFHD<span>mobile</span></a>
				
				<div class="nav-collapse">
					<ul class="nav">
						<li><a href="index.html">Dashboard</a></li>
						<li class="active"><a href="#">Studienplanung</a></li>
						<li><a href="#contact">Persönlich Daten</a></li>
						<li><a href="#contact">Hilfe</a></li>              	              
						<li><a href="#contact">Impressum</a></li>
						
						<!--LOGOUT-->
						<li><a href="#contact">Logout</a></li>						              	              
					</ul>
				</div><!--/.nav-collapse -->
				
			</div>
		</div>
	</div><!--NAVIGATION ENDE-->
	
	
	<!- ------------------------------------------------ -->
	<!-- DOZENT ---------------------------------------- -->
	<!- ------------------------------------------------ -->
	
	<!-- CONTENT -->
	<div class="container container-fluid">
		<div class="row">
			<div class="span4">
				<div class="well well-small clearfix">
			
					<h6>Dozent</h6>
			
					<!--Titel-->
	    			<h3><?php echo $dozentinfo[0]['Titel']; ?></h3>
	    			<h1><?php echo $dozentinfo[0]['Vorname']; ?> <?php echo $dozentinfo[0]['Nachname']; ?></h1>
					<hr />
					<!--Optionen-->
					<img src="<?php echo base_url('resources\img\dozent_standart.png'); ?>" alt="dozent">

		    		<a href="mailto: <?php echo $dozentinfo[0]['Email']; ?> " class="btn btn-large pull-right">
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
		
		<div class="row">
			
			<!--optionbox at the end of page-->
			<div class="span12">
				<div class="alert alert-info clearfix">
					<a href="<?php echo base_url('stundenplan'); ?>" class="btn btn-large btn-primary" href="#">
						<i class="icon-arrow-left icon-white"></i>
						 Modulübersicht
					</a>
				</div>
			</div><!-- /.span12-->
			
		</div><!-- /.row-->
		
   	</div><!-- /.fluid container-->
   	
	<!-- CONTENT ENDE-->
	
	<!-- Scripts -->
	<script src="bootstrap/js/jquery.js" type="text/javascript"></script>		
	<script src="bootstrap/js/bootstrap.js" type="text/javascript"></script>
	
	</body> <!-- /body -->
</html> <!-- /html -->

	<!-- CONTENT ENDE-->
<?php endblock(); ?>
<?php end_extend(); ?>