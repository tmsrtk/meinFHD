<?php extend('app/index.php'); ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Login<?php endblock();?>

<?php startblock('content'); # content for this view ?>

<?php
					#	$this->load->library('message');
						
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
							'content'			=> 'Anmelden',
							'class'			=> 'btn btn-primary pull-right'
						);
?>
				<?php print $messages; ?>
				<div class="well well-small clearfix">
					<h1>Login</h1>
					<hr />
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
							
							<hr/>
							<div class="control-group">
								<div class="controls">
									<div class="btn-group dropdown pull-left">
										
										<button class="btn"><i class="icon-question-sign"></i> Hilfe</button>
										<button class="btn dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<!--Modal trigger Zugang-->
											<li><a  class="" data-toggle="modal" href="#accountdata"><i class="icon-user"></i> Zugang anfordern</a></li>
											<!--Modal trigger pw-->
											<li><a class="" data-toggle="modal" href="#request-password"><i class="icon-envelope"></i> Passwort vergessen</a></li>
										</ul>
									</div>
								</div>
							</div>
								
							<?php echo form_button($submitButtonAttributes); // render the submit button ?>
						<?php echo form_fieldset_close(); // close the fieldset ?>
					<?php echo form_close(); // close the whole login form ?>
				</div>
<?php endblock(); ?>

<?php startblock('postCodeFooter'); # use for hidden markup like modals ?>
		<!-- REQUEST PASSWORD MODAL OVERLAY-->
		<div class="modal fade" id="request-password">
			<div class="modal-header">
				<h3>Passwort vergessen?</h3>
			</div>
			<div class="modal-body">
				<p>Kein Problem! Bitte trage deine E-Mail Adresse ein, das Passwort wird anschließend zugestellt.</p>
				<label for="email">E-mail-Adresse</label>
				<input name="email" type="text" />
			</div>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn btn-small">schließen</a>
				<a href="#" class="btn btn-primary btn-small">absenden</a>
			</div>
		</div>
		<!-- REQUEST PASSWORD MODAL OVERLAY ends here-->
<?php endblock(); ?>

<?php startblock('headJSfiles'); ?>
		
<?php endblock(); ?>
<?php end_extend(); ?>