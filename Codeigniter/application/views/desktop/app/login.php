<?php extend('app/index.php'); ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Login<?php endblock();?>

<?php
# general form setup: login-form
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
$submitLoginButtonAttributes = array(
	'name'			=> 'submit',
	'type'			=> 'submit',
	'id'			=> 'submitLoginForm',
	'content'			=> 'lokal Anmelden',
	'class'			=> 'btn btn-primary btn-medium pull-right'
);



# general form setup: request-account-form
$requestAccountFormAttributes = array(
	'class' 		=> 'request-account-form'
);

// prepare attributes for forgot-password email input field 
$requestAccountInputAttributes = array(
	'name'			=> 'request-account',
	'id'			=> 'request-account',
	'placeholder'	=> 'E-Mail',
	'class'			=> 'input-large'
);

// prepare attributes for submit button 
$submitRequestAccountButtonAttributes = array(
	'name'			=> 'submit',
	'type'			=> 'submit',
	'id'			=> 'submitRequestAccountForm',
	'content'			=> 'Anfordern',
	'class'			=> 'btn btn-primary btn-small'
);



# general form setup: forgot-password-form
$forgotPasswordFormAttributes = array(
	'class' 		=> 'forgot-password-form'
);

// prepare attributes for forgot-password email input field 
$forgotInputAttributes = array(
	'name'			=> 'forgot-email',
	'id'			=> 'forgot-email',
	'placeholder'	=> 'E-Mail',
	'class'			=> 'input-large'
);

// prepare attributes for submit button 
$submitForgotPasswordButtonAttributes = array(
	'name'			=> 'submit',
	'type'			=> 'submit',
	'id'			=> 'submitForgotPasswordForm',
	'content'			=> 'Senden',
	'class'			=> 'btn btn-primary btn-small'
);
?>

<?php startblock('content'); # content for this view ?>
				<div class="well well-small clearfix">
					<h1>Login</h1>
					<hr>
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
							<hr>
							<?php echo form_button($submitLoginButtonAttributes); // render the submit button ?>
						<?php echo form_fieldset_close(); // close the fieldset ?>
					<?php echo form_close(); // close the whole login form ?>
                    <div class="row-fluid">
                        <a href="<?php echo site_url(); ?>sso/authenticate" class="btn btn-primary btn-medium pull-right">Login &uuml;ber Shibboleth</a>
                    </div>
                    <hr>
				</div>
				<div id="accordion-app" class="accordion">
					<div class="accordion-group">
						<div class="accordion-heading">
							<h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#request-content">Zugang anfordern  <i class="icon-plus pull-right"></i></h4>
						</div>
						<div id="request-content" class="accordion-body collapse">
							<div class="accordion-inner">
								<div class="row-fluid">
								<p>Kein Problem! Bitte trage deine E-Mail Adresse ein, das Passwort wird anschließend zugestellt.</p>
								<?php echo form_open('app/request_account', $requestAccountFormAttributes ); // create opening tag of login form ?>
									<div class="control-group">
										<div class="controls">
											<div class="input-prepend input-append">
												<span class="add-on">@</span><?php echo form_input($requestAccountInputAttributes); // render the forgot-password email field ?><?php echo form_button($submitRequestAccountButtonAttributes); ?>
											</div>
										</div>
									</div>
								<?php echo form_close(); // close the whole login form ?>
								</div>
							</div>
						</div>
					</div>
					<div class="accordion-group">
						<div class="accordion-heading">
							<h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#forgot-content">Passwort vergessen <i class="icon-plus pull-right"></i></h4>
						</div>
						<div id="forgot-content" class="accordion-body collapse">
							<div class="accordion-inner">
								<p>Kein Problem! Bitte trage deine E-Mail Adresse ein, das Passwort wird anschließend zugestellt.</p>
								<?php echo form_open('app/forgot', $forgotPasswordFormAttributes ); // create opening tag of login form ?>
									<div class="control-group">
										<div class="controls">
											<div class="input-prepend input-append">
												<span class="add-on">@</span><?php echo form_input($forgotInputAttributes); // render the forgot-password email field ?><?php echo form_button($submitForgotPasswordButtonAttributes); ?>
											</div>
										</div>
									</div>
								<?php echo form_close(); // close the whole password forgot form ?>
							</div>
						</div>
					</div>
				</div>
<?php endblock(); ?>

<?php #startblock('postCodeFooter'); # use for hidden markup like modals ?>

<?php #endblock(); ?>

<?php # startblock('headJSfiles'); ?>

<?php # endblock(); ?>

<?php end_extend(); ?>