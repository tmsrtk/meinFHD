<?php include('header.php'); ?>
<!-- CONTENT -->
		<!-- CONTENT -->
	<div class="container container-fluid">
		<div class="row">
			<div class="span8">
				<div class="well well-small clearfix">
					<!--Titel-->
					<h1 class="maintitle">meinFHD<span>mobile</span></h1>
					<hr />
					
					<!--Loginform-->
					<?php print form_open('app/login', array('class' => 'form-inline' )); // create the login form ?>
				<!--	<form class="form-inline"> -->
						
						<input name="user" type="text" class="span2" placeholder="Benutzername" /><br />
					
						<input name="pw" type="password" class="span2" placeholder="Passwort" /><br />
						<hr class="hidden-phone" />
						
						<div class="well well-small clearfix">	
							
							<!--stay logged in-->
							<label class="checkbox">
								<input name="staylogged" type="checkbox" />
								&nbsp;eingeloggt bleiben
							</label>
							
							<!--Login btn-->
							<a href="dashboard" class="btn btn-inverse pull-right">
								<i class="icon-arrow-right icon-white"></i>
								 anmelden
							</a>
						</div>
					</form>
				</div>
			
			</div><!-- /.span8-->
			
			<!--DESKTOP AND TABLET ONLY-->
			<div class="span4">
			
				<div class="well well-small clearfix">
					<!--Modal trigger Zugang-->
					<a  class="btn pull-left" data-toggle="modal" href="#accountdata">neuer Zugang</a>
				
					<!--Modal trigger pw-->
					<a class="btn pull-right" data-toggle="modal" href="#accountdata">Passwort vergessen</a>
				</div>
			
			</div><!-- /.span4 -->						
			
		</div><!--first row ends here-->
	</div>
		<!-- CONTENT ENDE-->
<?php
	// create the login form
	print form_open('app/login');
	print form_input('name');
	print form_password('pass');
	print form_submit('button', 'Anmelden');
	print form_close();
?>

<?php include('footer.php'); ?>