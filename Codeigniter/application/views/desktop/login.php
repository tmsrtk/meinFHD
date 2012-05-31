<?php include('header.php'); ?>

<!-- CONTENT -->
	<div class="container container-fluid app-login">
		<div class="row-fluid">
			<div class="span4">
				
			</div>
			<div class="span4">
				<div class="well well-small clearfix">
					<h1 class="maintitle">Login</h1>
					<hr />
					<?php
						$loginFormAttributes = array(
							'class' 		=> ''
						);
						
						// prepare attributes for username input field 
						$usernameInputAttributes = array(
							'name'			=> 'username',
							'id'			=> 'username',
							'placeholder'	=> 'Benutzername',
							'class'			=> 'input-xxlarge'
						);
						
						// prepare attributes for username input field 
						$passwordInputAttributes = array(
							'name'			=> 'password',
							'id'			=> 'password',
							'placeholder'	=> 'Passwort',
							'class'			=> 'input-xxlarge'
						);
						
						// prepare attributes for permanent login checkbox 
						$permanentLoginAttributes = array(
							'name'			=> 'permanentLogin',
							'id'			=> 'permanentLogin',
							'class'			=> ''
						);
						
						// prepare attributes for submit button 
						$submitButtonAttributes = array(
							'name'			=> 'submit',
							'type'			=> 'submit',
							'id'			=> 'submitLoginForm',
							'content'			=> 'Anmelden',
							'class'			=> 'btn btn-primary pull-right'
						);
					?>
					<?php echo form_open('app/login', $loginFormAttributes ); // create opening tag of login form ?>
						<?php echo form_fieldset(); // wrap elements ina a fieldset due to semantics ?>
							<div class="control-group">
								<div class="controls">
									<div class="input-prepend">
										<span class="add-on"><i class="icon-user"></i></span><?php echo form_input($usernameInputAttributes); // render the username field ?>
									</div>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<div class="input-prepend">
										<span class="add-on"><i class="icon-lock"></i></span><?php echo form_password($passwordInputAttributes); // render the password field ?>
									</div>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label class="checkbox">
										<?php echo form_checkbox($permanentLoginAttributes); // render the permaLogin field ?>
										Angemeldet bleiben
									</label>
								</div>
							</div>
							
							<hr />
							
							<?php echo form_button($submitButtonAttributes); // render the submit button ?>
						<?php echo form_fieldset_close(); // close the fieldset ?>
					<?php echo form_close(); // close the whole login form ?>
				</div>
			
			</div><!-- /.span4-->
			<div class="span4"></div>
		</div><!--first row ends here-->
		<div class="row-fluid">
			<div class="span4"></div>
			<!--DESKTOP AND TABLET ONLY-->
			<div class="span4">
				<div class="well well-small clearfix">
					<!--Modal trigger Zugang-->
					<a  class="btn pull-left" data-toggle="modal" href="#accountdata">neuer Zugang</a>
				
					<!--Modal trigger pw-->
					<a class="btn pull-right" data-toggle="modal" href="#accountdata">Passwort vergessen</a>
				</div>
			
			</div><!-- /.span4 -->
			<div class="span4"></div>
		</div>
		
	</div>
		<!-- CONTENT ENDE-->

<?php include('footer.php'); ?>