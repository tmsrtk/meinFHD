<?php extend('sso/index.php'); ?>

<?php startblock('title'); # extend the site's title ?><?php get_extended_block(); ?> - Account verkn&uuml;pfen<?php endblock();?>

<?php
# general form setup: login-form
$linkAccountFormAttributes = array(
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

// prepare attributes for link account button
$submitLinkAccountButtonAttributes = array(
    'name'			=> 'submit',
    'type'			=> 'submit',
    'id'			=> 'submitLoginForm',
    'content'			=> 'Account verkn&uuml;pfen',
    'class'			=> 'btn btn-primary btn-medium pull-right'
);

?>

<?php startblock('content'); # content for this view ?>
                <div class="well well-small clearfix">
                    <h2>Sorry kein Account gefunden</h2>
                    <hr>
                    <p>
                        Hallo, <br/>
                        vielen Dank f&uuml;r deine Anmeldung. Du hast dich &uuml;ber den Shibboleth IdP der FH D&uuml;sseldorf angemeldet, aber leider konnte keine passende
                        lokale Identit&auml;t gefunden werden.<br/>
                        Wenn du bereits eine lokale Identit&auml;t besitzt kannst du diese jetzt verkn&uuml;pfen, oder fordere dir einen Zugang an.
                    </p>
                    <p>
                </div>

                <!-- Account linking Accordion -->
                <div id="accordion-app" class="accordion">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <h4 class="accordion-toggle" data-parent="#accordion-app" data-toggle="collapse" data-target="#request-content">Ja, ich besitze einen Accound und m&ouml;chte diesen jetzt verkn&uuml;pfen<i class="icon-plus pull-right"></i></h4>
                        </div>
                        <div id="request-content" class="accordion-body collapse">
                            <div class="accordion-inner">
                                <div class="row-fluid">
                                    <p>Bitte trage deinen Loginnamen und dein Passwort ein. Anschließend wird der angegebene lokale Zugang mit deiner globalen Identit&auml;t verkn&uuml;pft und du wirst eingeloggt.</p>
                                    <?php echo form_open('sso/link_account', $linkAccountFormAttributes); // create opening tag of login form ?>
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
                                            <hr>
                                            <?php echo form_button($submitLinkAccountButtonAttributes); // render the submit button ?>
                                        <?php echo form_fieldset_close(); // close the fieldset ?>
                                    <?php echo form_close(); // close the whole login form ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Account linking Accordion -->

                <!-- Account anfordern als Accordion, oder was anderes...-->

<?php endblock(); ?>

<?php startblock('customFooterJQueryCode');?>


<?php endblock(); ?>

<?php end_extend(); ?>