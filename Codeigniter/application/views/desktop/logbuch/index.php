<?php extend('base/template.php'); # extend main template ?>

<?php startblock('title');?><?php get_extended_block();?> - Logbuch<?php endblock(); ?>

<?php startblock('preCodeContent'); # additional markup before content ?>

<?php endblock(); ?>

<?php startblock('content'); # additional markup before content ?>
<div class="well well-small admin">
    <div class="row-fluid">
        <div class="span12">
            <h1>Logbuch</h1>
            <hr/>
            <p>Logbuch steht erstmal nur in der mobilen Version von meinFHD zur Verf&uuml;gung</p>
        </div>
    </div>
</div>
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>