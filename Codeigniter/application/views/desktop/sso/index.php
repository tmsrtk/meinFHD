<?php extend('base/template.php'); # extend main template ?>

<?php startblock('preCodeContent'); # additional markup before content ?>
        <div class="span1"></div>
        <div class="span1"></div>
        <div class="span8">
<?php endblock(); ?>

<?php startblock('postCodeContent'); # additional markup after content ?>
         </div><!-- /.span8-->
         <div class="span1"></div>
         <div class="span1"></div>
<?php endblock(); ?>

<?php end_extend(); # end extend main template ?>