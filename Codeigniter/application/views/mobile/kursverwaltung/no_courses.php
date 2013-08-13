<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Kursverwaltung<?php endblock(); ?>

<?php startblock('content'); ?>

<div class="span12 well well-small">
    <div class="row-fluid">
        <h2>Kursverwaltung</h2>
    </div>
    <hr/>
    <div class="row-fluid">
	    <p>
            Sie wurden bei keinem Kurs als Betreuer oder Verantwortlicher hinterlegt. Vermutlich ist aktuell kein
            Stundenplan in der Datenbank hinterlegt.<br/>
            Kontaktieren Sie bitte einen Kurs-Verantwortlichen, oder einen Administrator.
        </p>
    </div>
</div>
<?php endblock(); ?>
<?php end_extend(); ?>
