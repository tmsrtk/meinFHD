<?php extend('base/template.php'); # extend main template ?>
<?php startblock('title'); # extend the site's title ?>
    <?php get_extended_block(); ?> - &Uuml;bersicht
<?php endblock();?>

<?php startblock('content'); # content for this view ?>

		<div class="span4 offset4 well well-small">
            <?php if($this->authentication->has_permissions('hat_stundenplan')): ?>
            <a href="<?php print base_url('stundenplan'); ?>" class="mfhd-btn-block-primary">Stundenplan</a>
            <?php endif; ?>
            <?php if($this->authentication->has_permissions('hat_semesterplan')): ?>
            <a href="<?php print base_url('studienplan'); ?>" class="mfhd-btn-block-primary">Studienplan</a>
		    <?php endif; ?>
            <?php if($this->authentication->has_permissions('hat_kurse')): ?>
            <a href="<?php print base_url('kursverwaltung/show_coursemgt'); ?>" class="mfhd-btn-block-primary">Kursverwaltung</a>
            <?php endif; ?>
            <?php if ( $this->authentication->has_permissions('hat_logbuch') ) : ?>
<!--                <a href="--><?php //print base_url('logbuch/index'); ?><!--" class="mfhd-btn-block-primary">Logbuch</a>-->
            <?php endif; ?>
            <?php if ( $this->authentication->is_logged_in() ) : ?>
                <a href="<?php print base_url('einstellungen'); ?>" class="mfhd-btn-block-primary">Einstellungen</a>
            <?php endif; ?>
            <?php if ( $this->authentication->is_logged_in() ) : ?>
            <a href="<?php print base_url('logout'); ?>" class="mfhd-btn-block-inverse">Logout</a>
            <?php endif; ?>
		</div>
					
	<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>