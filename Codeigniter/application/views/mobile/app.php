<?php include('header.php'); ?>

<div class="container container-fluid">
	<div class="row">
		<div class="span8">
			<!--title-->
			<div class="well well-small well-first">
	    		<h6>Wilkommen</h6>
	    		<h1>meinFHDmobile</h1>
    		</div>			
		</div>
		<div class="span4">
			<a class="btn btn-large btn-startpage btn-primary" href="<?php print base_url('stundenplan'); ?>">
				<strong>Stundenplan</strong>
				<i class="icon-arrow-right icon-white pull-right"></i>
			</a>
			<a class="btn btn-large btn-startpage btn-primary" href="<?php print base_url('studienplan'); ?>">
				<strong>Studienplanung</strong>
				<i class="icon-arrow-right icon-white pull-right"></i>
			</a>
			<a class="btn btn-large btn-startpage btn-primary" href="<?php print base_url('einstellungen'); ?>">
				<strong>Einstellungen</strong>
				<i class="icon-arrow-right icon-white pull-right"></i>
			</a>
		</div>
	</div>	
</div>

<?php include('footer.php'); ?>