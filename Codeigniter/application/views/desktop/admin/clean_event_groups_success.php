<?php extend('admin/index.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Veranstaltungsgruppen erfolgreich bereinigt<?php endblock(); ?>

<?php startblock('content'); # main content ?>
    <div class="row-fluid">
        <div class="span12">
            <h3>Veranstaltungsgruppen bereinigen</h3>
            <hr/>
            <p>
                Alle Veranstaltungsgruppen wurden erfolgreich bereinigt.
            </p>
        </div>
    </div>

<?php endblock(); ?>

<?php end_extend(); ?>