<?php include('header.php'); ?>
<?php
	$loginFormAttributes = array(
		'class' 		=> 'login-form'
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
		'content'		=> 'Anmelden <i class="icon-arrow-right icon-white"></i>',
		'class'			=> 'btn btn-large btn-primary btn-fullwidth'
	);
?>
<!-- CONTENT -->
	<div class="container container-fluid">
		<div class="row-fluid">
			<div class="span6">
				<div class="well well-small">				
					<h1>Login</h1>
				</div>				
				<?php echo form_open('app/login', $loginFormAttributes ); // create opening tag of login form ?>
					<?php echo form_fieldset(); // wrap elements ina a fieldset due to semantics ?>
						<div class="well well-small clearfix">					
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

							<label class="checkbox">
								<?php echo form_checkbox($permanentLoginAttributes); // render the permaLogin field ?>
								Angemeldet bleiben
							</label>																								
						</div>				
						<?php echo form_button($submitButtonAttributes); // render the submit button ?>
						<ul class="unstyled">
							<li><a href="">Zugang anfordern</a></li>
							<li><a href="">Passwort vergessen</a></li>
						</ul>
						
												
					<?php echo form_fieldset_close(); // close the fieldset ?>
				<?php echo form_close(); // close the whole login form ?>
			</div><!-- /.span4-->
		</div><!--first row ends here -->
	</div>
	<!-- CONTENT ENDE-->

<?php include('footer.php'); ?>