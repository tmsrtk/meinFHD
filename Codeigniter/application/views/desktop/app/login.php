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
	'name'			=> 'permanent_login',
	'id'			=> 'permanent_login',
);

// prepare attributes for submit button 
$submitLoginButtonAttributes = array(
	'name'			=> 'submit',
	'type'			=> 'submit',
	'id'			=> 'submitLoginForm',
	'content'		=> 'lokal Anmelden',
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
										<?php echo form_checkbox($permanentLoginAttributes, 'yes', false);?>
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
				</div>
				<div id="accordion-app" class="accordion">
					<div class="accordion-group">
						<div class="accordion-heading">
							<h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#request-content">Zugang anfordern  <i class="icon-plus pull-right"></i></h4>
						</div>
						<div id="request-content" class="accordion-body collapse">
							<div class="accordion-inner">
								<div class="row-fluid">
								    <?php echo $request_account_mask; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="accordion-group">
						<div class="accordion-heading">
							<h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#forgot-content">Passwort vergessen? <i class="icon-plus pull-right"></i></h4>
						</div>
						<div id="forgot-content" class="accordion-body collapse">
							<div class="accordion-inner">
								<p>Kein Problem! Bitte trage deine E-Mail Adresse ein, anschlie&szlig;end wird dir ein neues Passwort zugestellt.</p>
								<?php echo form_open('app/forgot_password', $forgotPasswordFormAttributes ); // create opening tag of login form ?>
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

<?php startblock('customFooterJQueryCode');?>

    /**
    * Object which handles the UI functions.
    * @type {Object}
    */
    var AdditionalInfo = {
        init : function(config) {
        this.config = config;
        this.bindEvents();
        this.build();

        this.changeToStudent();
    },
    bindEvents : function() {
        context = this;
        this.config.roleInput.change(function() {
        context.toggle_studentdata($(this));
        });
    this.config.erstsemestler.change(function() {
        context.toggle_erstsemestlerdata($(this));
        });
    },
    build : function() {
        this.toggle_studentdata($("label[for='role']").parent().find("input:checked"));
        this.toggle_erstsemestlerdata($("label[for='erstsemestler']").parent().find("input"));
    },
    toggle_studentdata : function(c) {
        additional_student_data = $("div#studentendaten");

        // c jQuery object of toggle button
        if (c.val() === '5') {
            // show additional student da
            additional_student_data.slideDown('slow');
            this.changeToStudent();
        }
        else {
            additional_student_data.slideUp('slow');
            this.changeToDozent();
        }
    },
    toggle_erstsemestlerdata : function(c) {
        var erstsemestler_data = $("div#erstsemestler_daten");

        if (c.attr('checked')) {
            erstsemestler_data.slideUp('slow');
        }
        else {
            erstsemestler_data.slideDown('slow');
        }
    },
    changeToStudent : function() {
        // this.config.additionalInfoContent = this.studentenInfo;
        this.config.additionalInfoContent.html(this.studentenInfo);
    },
    changeToDozent : function() {
        this.config.additionalInfoContent.html(this.dozentenInfo);
    },
    studentenInfo : "Gib bitte die folgenden Daten an, damit wir feststellen können, dass Du ein Student an diesem Fachbereich bist. Die Emailadresse wird für die Kommunikation mit meinFHD, den Dozenten und Studierenden verwendet.",
    dozentenInfo : "Geben Sie bitte hier Ihren vollen Namen an, da dieser für die Kommunikation mit den Studierenden gebraucht wird. Die Emailadresse wird für die Kommunikation mit meinFHD und den Studierenden verwendet. "
    };

    // initialise the object
    AdditionalInfo.init({
        additionalInfoContent : $("div#additional-info"),
        roleInput : $("input[name='role']"),
        erstsemestler : $("input[name='erstsemestler']")
    });
<?php endblock(); ?>

<?php end_extend(); ?>