<?php include('header.php'); ?>

<div class="container container-fluid">
	<div class="row">
		<div class="span8">
			<!--title-->
			<div class="well well-small well-first">
	    		<h6>Willkommen</h6>
	    		<h1>meinFHDmobile</h1>
    		</div>			
		</div>
		<div class="span4">
			<a class="btn btn-large btn-fullwidth btn-primary" href="<?php print base_url('stundenplan'); ?>">
				Stundenplan
				<i class="icon-arrow-right icon-white"></i>
			</a>
			<a class="btn btn-large btn-fullwidth btn-primary" href="<?php print base_url('studienplan'); ?>">
				Studienplanung
				<i class="icon-arrow-right icon-white"></i>
			</a>
			<a class="btn btn-large btn-fullwidth btn-primary" href="<?php print base_url('einstellungen'); ?>">
				Einstellungen
				<i class="icon-arrow-right icon-white"></i>
			</a>
		</div>
	</div>	
</div>

<?php include('footer.php'); ?>